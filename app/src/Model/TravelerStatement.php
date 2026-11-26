<?php

namespace HazreTours\Model;

use HazreTours\Admin\StatementAdmin;
use SilverStripe\Control\Director;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

/**
 * Izjava putnika o upoznavanju i prihvatanju Opštih uslova putovanja i Politike privatnosti.
 * Agencija je napravi u adminu za putnika koji je već rezervisao, pošalje mu lični link (/izjava/<token>),
 * a putnik je potvrdi na sajtu. Potvrđena izjava se više ne može mijenjati (dokaz), a briše je samo glavni admin.
 *
 * @property string $Token
 * @property string $HolderName
 * @property string $Phone
 * @property string $Companions
 * @property string $Status
 * @property string $ConfirmedAt
 * @method Trip Trip()
 * @method TripTerm Term()
 */
class TravelerStatement extends DataObject
{
    private static string $table_name = 'HazreTravelerStatement';

    private static string $singular_name = 'Izjava putnika';

    private static string $plural_name = 'Izjave putnika';

    /** Tekstovi potvrda koje putnik označava (u izjavu se spremaju tačno ovi tekstovi). */
    public const CHECKS = [
        'read' => 'Potvrđujem da sam imao/la mogućnost pročitati Opšte uslove putovanja HAZRE TOURS, da sam ih razumio/la i da sam upoznat/a s njihovim sadržajem.',
        'accept' => 'Potvrđujem da prihvatam Opšte uslove putovanja HAZRE TOURS koji se primjenjuju na moj turistički aranžman.',
        'trip' => 'Potvrđujem da sam upoznat/a s programom putovanja, uključenim uslugama, cijenom, uslovima plaćanja i pravilima otkazivanja koja se odnose na moj aranžman.',
        'privacy' => 'Potvrđujem da sam upoznat/a s Politikom privatnosti HAZRE TOURS i da mi je omogućeno da pročitam informacije o obradi i zaštiti svojih ličnih podataka.',
        'true' => 'Izjavljujem da su svi podaci koje sam naveo/la u ovom obrascu tačni i potpuni.',
    ];

    public const STATUSES = [
        'poslana' => 'Čeka potvrdu',
        'potvrdjena' => 'Potvrđena',
    ];

    /** Najviše saputnika na jednoj izjavi i najduže ime. */
    public const MAX_COMPANIONS = 40;

    public const MAX_NAME = 100;

    private static array $db = [
        'Token' => 'Varchar(64)',
        'HolderName' => 'Varchar(120)',
        'Phone' => 'Varchar(40)',
        'Companions' => 'Text',
        'Note' => 'Varchar(255)',
        'Status' => "Enum('poslana,potvrdjena','poslana')",
        'ConfirmedAt' => 'Datetime',
        'ConfirmIP' => 'Varchar(45)',
        'ConfirmAgent' => 'Varchar(255)',
        'TermsVersion' => 'Varchar(120)',
        'PrivacyVersion' => 'Varchar(120)',
        'ConfirmedChecks' => 'Text',
    ];

    private static array $has_one = [
        'Trip' => Trip::class,
        'Term' => TripTerm::class,
        'CreatedBy' => Member::class,
    ];

    private static array $indexes = [
        'Token' => ['type' => 'unique', 'columns' => ['Token']],
        'Status' => true,
    ];

    private static string $default_sort = 'Created DESC';

    private static array $summary_fields = [
        'HolderName' => 'Nosilac rezervacije',
        'TripTitle' => 'Aranžman',
        'TermLabel' => 'Termin',
        'CompanionCount' => 'Saputnika',
        'StatusLabel' => 'Status',
        'ConfirmedLabel' => 'Potvrđeno',
    ];

    private static array $searchable_fields = [
        'HolderName' => ['title' => 'Ime i prezime'],
        'Phone' => ['title' => 'Telefon'],
        'Status' => ['title' => 'Status'],
        'Trip.Title' => ['title' => 'Aranžman', 'filter' => 'PartialMatchFilter'],
    ];

    // ---------- Pristup: samo ko ima "Izjave putnika" u adminu; potvrđenu izjavu niko ne mijenja, briše samo glavni admin

    public static function canManage($member = null): bool
    {
        return Permission::checkMember($member, 'CMS_ACCESS_' . StatementAdmin::class);
    }

    public function canView($member = null)
    {
        return self::canManage($member);
    }

    public function canCreate($member = null, $context = [])
    {
        return self::canManage($member);
    }

    public function canEdit($member = null)
    {
        return self::canManage($member) && !$this->IsConfirmed();
    }

    public function canDelete($member = null)
    {
        return Permission::checkMember($member, 'ADMIN');
    }

    // ---------- Admin

    public function getCMSFields(): FieldList
    {
        $terms = [];
        $from = date('Y-m-d', strtotime('-60 days'));
        foreach (TripTerm::get()->filter('StartDate:GreaterThanOrEqual', $from)->sort('StartDate') as $term) {
            if ($term->Trip()->exists()) {
                $terms[$term->ID] = $term->Trip()->Title . ' – ' . $term->Label();
            }
        }
        if ($this->TermID && !isset($terms[$this->TermID]) && $this->Term()->exists()) {
            $terms[$this->TermID] = $this->Term()->Trip()->Title . ' – ' . $this->Term()->Label();
        }

        $fields = FieldList::create();
        if ($this->exists()) {
            $fields->push(LiteralField::create('StmtLink', $this->linkPanelHTML()));
        } else {
            $fields->push(LiteralField::create('StmtInfo', '<p class="message notice">Nakon snimanja dobićete lični link koji šaljete putniku (Viber, WhatsApp ili SMS). Putnik otvori link, provjeri podatke i potvrdi izjavu.</p>'));
        }
        $fields->push(HeaderField::create('StmtH1', 'Nosilac rezervacije i putovanje', 3));
        $fields->push(TextField::create('HolderName', 'Ime i prezime nosioca rezervacije')->setAttribute('maxlength', 120));
        $fields->push(TextField::create('Phone', 'Broj telefona')->setAttribute('maxlength', 40)
            ->setDescription('Na ovaj broj šaljete link. Putniku se na stranici prikazuje samo djelimično (npr. 061 *** 529).'));
        $fields->push(DropdownField::create('TermID', 'Aranžman i termin', $terms)->setEmptyString('Putnik bira sam (u izjavi)')
            ->setDescription('Ako ostavite prazno, putnik pri potvrdi izabere aranžman i termin s liste aktuelnih polazaka.'));
        $fields->push(TextareaField::create('Companions', 'Saputnici (nije obavezno)')->setRows(5)
            ->setDescription('Jedno ime i prezime u jednom redu, najviše ' . self::MAX_COMPANIONS . '. Putnik može dopuniti spisak pri potvrdi. Upisujte samo ime i prezime, bez JMBG-a, broja pasoša i sličnih podataka.'));
        $fields->push(TextField::create('Note', 'Interna napomena (putnik je ne vidi)')->setAttribute('maxlength', 255));

        if ($this->IsConfirmed()) {
            $fields = $fields->makeReadonly();
            $fields->push(LiteralField::create('StmtProof', $this->proofHTML()));
        }
        return $fields;
    }

    public function validate(): \SilverStripe\Core\Validation\ValidationResult
    {
        $result = parent::validate();
        if (mb_strlen(trim((string) $this->HolderName)) < 3) {
            $result->addFieldError('HolderName', 'Upišite ime i prezime nosioca rezervacije.');
        }
        if (strlen(preg_replace('/\D/', '', (string) $this->Phone)) < 6) {
            $result->addFieldError('Phone', 'Upišite broj telefona.');
        }
        if (count(self::parseCompanions((string) $this->Companions)) > self::MAX_COMPANIONS) {
            $result->addFieldError('Companions', 'Najviše ' . self::MAX_COMPANIONS . ' saputnika na jednoj izjavi.');
        }
        return $result;
    }

    protected function onBeforeWrite(): void
    {
        parent::onBeforeWrite();
        if (!$this->Token) {
            // 48 heksadecimalnih znakova (192 bita) iz kriptografski sigurnog generatora
            $this->Token = bin2hex(random_bytes(24));
        }
        if (!$this->CreatedByID && ($member = Security::getCurrentUser())) {
            $this->CreatedByID = $member->ID;
        }
        if ($this->TermID && $this->Term()->exists()) {
            $this->TripID = $this->Term()->TripID;
        }
        $this->HolderName = mb_substr(trim(strip_tags((string) $this->HolderName)), 0, 120);
        $this->Phone = mb_substr(trim(strip_tags((string) $this->Phone)), 0, 40);
        $this->Companions = implode("\n", self::parseCompanions((string) $this->Companions));
    }

    // ---------- Pomoćne metode

    /** Spisak saputnika: jedno ime u redu, bez HTML-a i praznih redova, najviše MAX_COMPANIONS. */
    public static function parseCompanions(string $text): array
    {
        $out = [];
        foreach (preg_split('/\R/u', $text) as $line) {
            $line = trim(preg_replace('/\s+/u', ' ', strip_tags($line)));
            $line = trim($line, " \t-•*0123456789.)");
            if ($line !== '') {
                $out[] = mb_substr($line, 0, self::MAX_NAME);
            }
        }
        return array_slice(array_values(array_unique($out)), 0, self::MAX_COMPANIONS + 1);
    }

    public function CompanionList(): array
    {
        return self::parseCompanions((string) $this->Companions);
    }

    public function getCompanionCount(): int
    {
        return count($this->CompanionList());
    }

    public function IsConfirmed(): bool
    {
        return $this->Status === 'potvrdjena';
    }

    /** Link istekne 30 dana nakon polaska, a bez izabranog termina 90 dana nakon pravljenja izjave. */
    public function IsExpired(): bool
    {
        if ($this->Term()->exists()) {
            return strtotime($this->Term()->StartDate . ' +30 days') < time();
        }
        return $this->Created && strtotime($this->Created . ' +90 days') < time();
    }

    /** Termini koje putnik može izabrati sam: aktivni aranžmani, polasci od danas nadalje. */
    public static function selectableTerms(): array
    {
        $out = [];
        foreach (TripTerm::get()->filter(['StartDate:GreaterThanOrEqual' => date('Y-m-d'), 'Trip.Active' => true])->sort('StartDate') as $term) {
            $out[$term->ID] = $term->Trip()->Title . ' – ' . $term->Label();
        }
        return $out;
    }

    public function Link(): string
    {
        return Director::absoluteURL('/izjava/' . $this->Token);
    }

    public function getTripTitle(): string
    {
        return $this->Trip()->exists() ? (string) $this->Trip()->Title : ($this->IsConfirmed() ? '' : 'Bira putnik');
    }

    public function getTermLabel(): string
    {
        return $this->Term()->exists() ? $this->Term()->Label() : '';
    }

    public function getStatusLabel(): string
    {
        return self::STATUSES[$this->Status] ?? '';
    }

    public function getConfirmedLabel(): string
    {
        return $this->ConfirmedAt ? $this->obj('ConfirmedAt')->Format('dd.MM.y. HH:mm') : '';
    }

    public function getTitle(): string
    {
        return trim($this->HolderName . ' – ' . $this->getTripTitle(), ' –');
    }

    /** Broj telefona za prikaz putniku: samo početak i kraj (061 *** 529). */
    public function MaskedPhone(): string
    {
        $d = preg_replace('/\D/', '', (string) $this->Phone);
        if (strlen($d) < 6) {
            return '';
        }
        return substr($d, 0, 3) . ' *** ' . substr($d, -3);
    }

    private function shareText(): string
    {
        // Poruka za Viber/WhatsApp/SMS/e-mail: običan tekst (bez oznaka za podebljano, jer ih neke aplikacije prikažu pogrešno),
        // link u posebnom redu da bude klikabilan
        $first = explode(' ', trim((string) $this->HolderName))[0];
        $phone = \SilverStripe\SiteConfig\SiteConfig::current_site_config()->MainPhone();
        $lines = ['Poštovani/a ' . $first . ',', '', 'zahvaljujemo vam na rezervaciji putovanja s agencijom Hazre Tours.'];
        if ($this->Term()->exists()) {
            $lines[] = '';
            $lines[] = 'Aranžman: ' . $this->getTripTitle();
            $lines[] = 'Termin: ' . $this->getTermLabel();
        }
        $lines[] = '';
        $lines[] = 'Prije polaska potrebno je da potvrdite izjavu o upoznavanju i prihvatanju Opštih uslova putovanja.';
        $lines[] = '';
        $lines[] = 'Molimo vas da prije potvrde pažljivo pročitate Opšte uslove putovanja i Politiku privatnosti. Oba dokumenta nalaze se na stranici izjave, a potvrdom izjave potvrđujete da ste s njima upoznati i da ih prihvatate.';
        $lines[] = '';
        $lines[] = 'Izjavu možete potvrditi putem sljedećeg linka:';
        $lines[] = $this->Link();
        $lines[] = '';
        $lines[] = ($phone && $phone->Number ? 'Za sva pitanja možete nas kontaktirati na broj telefona ' . $phone->Number . '.' : 'Za sva pitanja stojimo vam na raspolaganju.');
        $lines[] = '';
        $lines[] = 'Srdačan pozdrav,';
        $lines[] = 'Hazre Tours';
        return implode("\n", $lines);
    }

    private function linkPanelHTML(): string
    {
        $e = fn ($s) => htmlspecialchars((string) $s, ENT_QUOTES);
        $text = $this->shareText();
        $digits = preg_replace('/\D/', '', (string) $this->Phone);
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = '387' . substr($digits, 1);
        }
        $wa = 'https://wa.me/' . $digits . '?text=' . rawurlencode($text);
        $viber = 'viber://forward?text=' . rawurlencode($text);
        $status = $this->IsConfirmed()
            ? '<span class="hz-stmt__badge is-ok">Potvrđena ' . $e($this->getConfirmedLabel()) . '</span>'
            : '<span class="hz-stmt__badge">Čeka potvrdu putnika</span>';
        $actions = $this->IsConfirmed() ? '' :
            '<div class="hz-stmt__actions">'
            . '<button type="button" class="btn btn-primary hz-copy" data-copy="' . $e($this->Link()) . '">Kopiraj link</button>'
            . '<a class="btn btn-outline-secondary" href="' . $e($viber) . '">Pošalji na Viber</a>'
            . '<a class="btn btn-outline-secondary" href="' . $e($wa) . '" target="_blank" rel="noopener noreferrer">Pošalji na WhatsApp</a>'
            . '<button type="button" class="btn btn-outline-secondary hz-copy" data-copy="' . $e($text) . '">Kopiraj poruku (SMS)</button>'
            . '</div>';
        return '<div class="hz-stmt">' . $status
            . '<p class="hz-stmt__lbl">Lični link za putnika</p>'
            . '<input class="hz-stmt__link form-control" type="text" readonly value="' . $e($this->Link()) . '" onclick="this.select()">'
            . $actions
            . '<p class="hz-stmt__hint">Link je jedinstven za ovu izjavu. Ne objavljujte ga javno; ko ima link, može potvrditi izjavu. Ističe 30 dana nakon polaska.</p>'
            . '</div>';
    }

    private function proofHTML(): string
    {
        $e = fn ($s) => htmlspecialchars((string) $s, ENT_QUOTES);
        $checks = json_decode((string) $this->ConfirmedChecks, true) ?: [];
        $list = '';
        foreach ($checks as $text) {
            $list .= '<li>' . $e($text) . '</li>';
        }
        return '<div class="hz-stmt hz-stmt--proof"><p class="hz-stmt__lbl">Evidencija potvrde</p><table>'
            . '<tr><td>Potvrđeno</td><td><b>' . $e($this->getConfirmedLabel()) . '</b></td></tr>'
            . '<tr><td>IP adresa</td><td>' . $e($this->ConfirmIP) . '</td></tr>'
            . '<tr><td>Uređaj / preglednik</td><td>' . $e($this->ConfirmAgent) . '</td></tr>'
            . '<tr><td>Opšti uslovi putovanja</td><td>' . $e($this->TermsVersion) . '</td></tr>'
            . '<tr><td>Politika privatnosti</td><td>' . $e($this->PrivacyVersion) . '</td></tr>'
            . '</table><p class="hz-stmt__lbl">Označene potvrde</p><ul>' . $list . '</ul></div>';
    }
}
