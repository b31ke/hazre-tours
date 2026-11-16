#!/bin/bash
#
# Postavlja novu verziju Hazre Tours sajta na serveru (cPanel), isto kao shop i Minter.
# Pokreće se na serveru nakon što je paket (hazre-paket.zip) poslan u home folder:
#
#   bash ~/hazre-tours/deploy/postavi.sh            (svaki sljedeći put)
#   unzip -oq ~/hazre-paket.zip -d ~/hazre-tours && bash ~/hazre-tours/deploy/postavi.sh   (prvi put)
#
# Baza i slike (public/assets) se ne diraju. .env na serveru se ne mijenja.

set -euo pipefail

SITE="${SITE:-$HOME/hazre-tours}"
PAKET="${PAKET:-$HOME/hazre-paket.zip}"
PHP="${PHP:-/opt/cpanel/ea-php84/root/usr/bin/php}"
COMPOSER="${COMPOSER:-$HOME/composer.phar}"

cd "$SITE"

HTACCESS="$SITE/public/.htaccess"
# cPanel (MultiPHP) upisuje u .htaccess koju verziju PHP-a sajt koristi; paket bi to prepisao
# i sajt bi pao na stari PHP. Taj blok se sačuva i vrati.
CPANEL_BLOCK=""
if [ -f "$HTACCESS" ]; then
  CPANEL_BLOCK="$(sed -n '/# php -- BEGIN cPanel-generated handler/,/# php -- END cPanel-generated handler/p' "$HTACCESS")"
fi

if [ -f "$PAKET" ]; then
  unzip -oq "$PAKET" -d "$SITE"
  rm -f "$PAKET"
  echo "Paket raspakovan."
fi

if [ -n "$CPANEL_BLOCK" ] && ! grep -q "BEGIN cPanel-generated handler" "$HTACCESS"; then
  printf '\n%s\n' "$CPANEL_BLOCK" >> "$HTACCESS"
fi
if ! grep -q "BEGIN cPanel-generated handler" "$HTACCESS"; then
  echo "PAŽNJA: u public/.htaccess nema cPanel PHP podešavanja. cPanel -> MultiPHP Manager -> domena sajta -> PHP 8.4 -> Apply." >&2
fi

if [ ! -f "$SITE/.env" ]; then
  echo "NEMA .env: napravi ga po deploy/env-produkcija.primjer pa pokreni ponovo." >&2
  exit 1
fi
touch "$SITE/hazre-greske.log"
chmod 600 "$SITE/hazre-greske.log"

# Ovaj server nema mod_env: linija SetEnv ruši .htaccess (greška 500 na cijelom sajtu)
sed -i 's/^\(\s*\)SetEnv HTTP_MOD_REWRITE/\1# SetEnv HTTP_MOD_REWRITE/' "$SITE/public/.htaccess"

"$PHP" -d allow_url_fopen=On "$COMPOSER" install --no-dev --optimize-autoloader --no-interaction --no-progress
"$PHP" "$COMPOSER" vendor-expose --no-interaction

# Prava: folderi 755, fajlovi 644 (server odbija fajlove u koje grupa smije pisati), .env samo vlasnik
find "$SITE" -path "$SITE/public/assets" -prune -o -type d -exec chmod 755 {} +
find "$SITE" -path "$SITE/public/assets" -prune -o -type f -exec chmod 644 {} +
chmod -R u+rwX "$SITE/public/assets" 2>/dev/null || true
chmod 600 "$SITE/.env"

"$PHP" vendor/bin/sake db:build --flush
# Prvi put prebaci sadržaj sa starog sajta; kasnije ne dira ništa što je agencija izmijenila
"$PHP" -d allow_url_fopen=On vendor/bin/sake tasks:hazre-import

echo "Gotovo. Admin: <adresa sajta>/admin"
