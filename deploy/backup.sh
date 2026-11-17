#!/bin/bash
#
# Noćni backup hazre-tours na Google Drive (isti postupak i ista rclone veza "gdrive" kao za shop.beshirt.ba).
#
#   1. baza          -> jedan komprimovan fajl po noći (aranžmani, termini, upiti, stranice)
#   2. public/assets -> plakati, programi putovanja i slike iz CMS-a
#   3. .env          -> podešavanja servera (lozinka baze, admin pristup), potrebna za vraćanje
#
# Na serveru ostaje kopija baze 14 dana (~/backups/hazre/db), na Google Driveu 30 dana baza i ogledalo
# fajlova. Fajl koji je na sajtu izmijenjen ili obrisan na Driveu se ne gubi: prethodna verzija ide u
# "assets-stare/<datum>".
#
# Pokreće ga cron jednom noću (deploy/UPUTSTVO.txt, dio BACKUP). Kad je sve u redu ne ispisuje ništa,
# a kad nešto ne uspije ispiše razlog, pa mail od crona stiže SAMO kad treba nešto pogledati.

set -u
set -o pipefail

SITE="${SITE:-$HOME/hazre-tours}"
DEST="${DEST:-$HOME/backups/hazre}"
REMOTE="${REMOTE:-gdrive:HAZRE-backup}"
KEEP_LOCAL_DAYS="${KEEP_LOCAL_DAYS:-14}"
KEEP_REMOTE_DAYS="${KEEP_REMOTE_DAYS:-30}"
RCLONE="${RCLONE:-$(command -v rclone 2>/dev/null || echo "$HOME/bin/rclone")}"

STAMP="$(date +%Y-%m-%d_%H%M)"
LOG="$DEST/backup.log"
DUMP="$DEST/db/hazre-baza-$STAMP.sql.gz"
RUNNING="$DEST/u-toku"

mkdir -p "$DEST/db" || { echo "BACKUP hazre-tours NIJE USPIO: ne mogu napraviti folder $DEST" >&2; exit 1; }
chmod 700 "$DEST"

# jedan backup u isto vrijeme (oznaka starija od 3 sata je od backupa koji je pukao)
if [ -f "$RUNNING" ] && [ -z "$(find "$RUNNING" -mmin +180 2>/dev/null)" ]; then
    exit 0
fi
date '+%Y-%m-%d %H:%M' > "$RUNNING"

CREDENTIALS=""
trap 'rm -f "$RUNNING" "$CREDENTIALS"' EXIT

fail() {
    echo "$(date '+%Y-%m-%d %H:%M') FAILED: $*" >> "$LOG"
    echo "BACKUP hazre-tours NIJE USPIO ($(date '+%d.%m.%Y %H:%M')): $*" >&2
    exit 1
}

# jedna vrijednost iz .env, bez navodnika
env_value() {
    grep -E "^$1=" "$SITE/.env" | tail -n 1 | cut -d= -f2- | sed -e 's/\r$//' -e 's/^"\(.*\)"$/\1/' -e "s/^'\(.*\)'\$/\1/"
}

[ -f "$SITE/.env" ] || fail "nema fajla .env u $SITE"

DB_NAME="$(env_value SS_DATABASE_NAME)"
DB_USER="$(env_value SS_DATABASE_USERNAME)"
DB_PASS="$(env_value SS_DATABASE_PASSWORD)"
DB_HOST="$(env_value SS_DATABASE_SERVER)"
DB_PORT="$(env_value SS_DATABASE_PORT)"
[ -n "$DB_NAME" ] && [ -n "$DB_USER" ] || fail "u .env nedostaje naziv baze ili korisnik"

# ---- 1. baza -----------------------------------------------------------------------------------

# lozinka ide kroz privatni fajl, nikad u komandnu liniju (tamo je mogu vidjeti drugi nalozi)
CREDENTIALS="$(mktemp "$DEST/.my.XXXXXX")" || fail "ne mogu napraviti privremeni fajl"
chmod 600 "$CREDENTIALS"
{
    echo "[client]"
    echo "user=\"$(printf '%s' "$DB_USER" | sed -e 's/\\/\\\\/g' -e 's/"/\\"/g')\""
    echo "password=\"$(printf '%s' "$DB_PASS" | sed -e 's/\\/\\\\/g' -e 's/"/\\"/g')\""
    echo "host=\"${DB_HOST:-localhost}\""
    [ -n "$DB_PORT" ] && echo "port=$DB_PORT"
} > "$CREDENTIALS"

DUMPER="$(command -v mariadb-dump 2>/dev/null || command -v mysqldump 2>/dev/null)"
[ -n "$DUMPER" ] || fail "na serveru nema programa mysqldump"

# --single-transaction: tačna slika baze bez zaustavljanja sajta dok traje
"$DUMPER" --defaults-extra-file="$CREDENTIALS" --single-transaction --quick --routines --triggers \
    --default-character-set=utf8mb4 "$DB_NAME" 2>> "$LOG" | gzip -9 > "$DUMP" \
    || { rm -f "$DUMP"; fail "baza se nije mogla izvesti (detalji u $LOG)"; }

gzip -t "$DUMP" 2>/dev/null || fail "fajl s bazom je oštećen"
# potpun izvoz završava ovim redom, prekinut ne
gunzip -c "$DUMP" | tail -n 5 | grep -q "Dump completed" || fail "izvoz baze nije potpun"
chmod 600 "$DUMP"
DUMP_SIZE="$(du -h "$DUMP" | cut -f1)"

find "$DEST/db" -name 'hazre-baza-*.sql.gz' -mtime +"$KEEP_LOCAL_DAYS" -delete

# ---- 2. Google Drive ---------------------------------------------------------------------------

if [ ! -x "$RCLONE" ] || ! "$RCLONE" listremotes 2>/dev/null | grep -q "^${REMOTE%%:*}:"; then
    fail "baza je sačuvana na serveru ($(basename "$DUMP"), $DUMP_SIZE), ali NIŠTA nije otišlo na Google Drive: rclone ili njegova veza \"${REMOTE%%:*}\" nije podešena"
fi

# Google dozvoljava ograničen broj zahtjeva u minuti, pa polako: par zahtjeva u sekundi, dva fajla odjednom
RC=("$RCLONE" --log-file "$LOG" --log-level NOTICE --retries 3 --low-level-retries 10
    --tpslimit 4 --transfers 2 --checkers 4 --drive-pacer-min-sleep 250ms)

# prvo ono malo i nezamjenjivo: mora stići i u noći kad slike ne stignu
# Na Google Drive idu samo ŠIFROVANE kopije baze i .env (ključ ostaje na serveru i kod vlasnika).
KEYFILE="${KEYFILE:-$HOME/.hazre-backup-kljuc}"
if [ ! -s "$KEYFILE" ]; then
    (umask 077 && openssl rand -base64 32 > "$KEYFILE") || fail "ne mogu napraviti ključ za šifrovanje"
    echo "NOVI KLJUČ ZA BACKUP hazre-tours (bez njega se kopije s Google Drivea ne mogu otvoriti)." >&2
    echo "Sačuvaj ga na sigurnom mjestu (npr. menadžer lozinki), van servera:" >&2
    cat "$KEYFILE" >&2
fi
ENC=(openssl enc -aes-256-cbc -pbkdf2 -iter 200000 -salt -pass "file:$KEYFILE")
DUMP_ENC="$DUMP.enc"
ENV_ENC="$DEST/env-produkcija.enc"
CREDENTIALS_EXTRA="$DUMP_ENC $ENV_ENC"
trap 'rm -f "$RUNNING" "$CREDENTIALS" $CREDENTIALS_EXTRA' EXIT
"${ENC[@]}" -in "$DUMP" -out "$DUMP_ENC" || fail "baza se nije mogla šifrovati"
"${ENC[@]}" -in "$SITE/.env" -out "$ENV_ENC" || fail ".env se nije mogao šifrovati"

"${RC[@]}" copyto "$DUMP_ENC" "$REMOTE/baza/$(basename "$DUMP_ENC")" || fail "baza je sačuvana na serveru, ali nije stigla na Google Drive"
"${RC[@]}" delete "$REMOTE/baza" --min-age "${KEEP_REMOTE_DAYS}d" || echo "$(date '+%Y-%m-%d %H:%M') napomena: stare baze na Driveu nisu očišćene" >> "$LOG"
"${RC[@]}" copyto "$ENV_ENC" "$REMOTE/postavke/env-produkcija.enc" || fail "baza je na Google Driveu, ali fajl .env nije stigao"
# stara nešifrovana kopija .env s Drivea (ako je ostala od ranije)
"${RC[@]}" deletefile "$REMOTE/postavke/env-produkcija.txt" 2>/dev/null || true
"${RC[@]}" delete "$REMOTE/baza" --include "*.sql.gz" 2>/dev/null || true

# Ogledalo slika; ono što je na sajtu izmijenjeno ili nestalo čuva se sa strane, ne briše.
# Bez umanjenih kopija ("ime__Fit....jpg"): sajt ih sam napravi ponovo.
mkdir -p "$SITE/public/assets"
FILES_OK=0
for attempt in 1 2 3 4; do
    if "${RC[@]}" sync "$SITE/public/assets" "$REMOTE/assets" \
        --backup-dir "$REMOTE/assets-stare/$STAMP" \
        --exclude "error-*.html" --exclude "*__*" --exclude ".htaccess" --exclude "web.config"; then
        FILES_OK=1
        break
    fi
    sleep 120
done
[ "$FILES_OK" = 1 ] || fail "baza i postavke su na Google Driveu, ali slike nisu sve stigle (Google je ograničio brzinu); nastavlja se pri sljedećem backupu"

echo "$(date '+%Y-%m-%d %H:%M') ok: baza $DUMP_SIZE, slike i postavke na $REMOTE" >> "$LOG"

# dnevnik ne raste unedogled
tail -n 500 "$LOG" > "$LOG.tmp" && mv "$LOG.tmp" "$LOG"
exit 0
