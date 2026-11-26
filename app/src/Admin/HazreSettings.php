<?php

namespace HazreTours\Admin;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;

/**
 * Admin → Settings: kartice Kontakt, Akcija (traka na vrhu sajta) i Analitika.
 */
class HazreSettings extends Extension
{
    private static array $db = [
        'CompanyName' => 'Varchar(120)',
        'Address' => 'Varchar(200)',
        'Phones' => 'Text',
        'ContactEmail' => 'Varchar(150)',
        'InquiryEmail' => 'Varchar(150)',
        'WorkingHours' => 'Varchar(150)',
        'LicenseNumber' => 'Varchar(80)',
        'ViberNumber' => 'Varchar(40)',
        'WhatsAppNumber' => 'Varchar(40)',
        'ViberCommunityURL' => 'Varchar(255)',
        'FacebookURL' => 'Varchar(255)',
        'InstagramURL' => 'Varchar(255)',
        'TikTokURL' => 'Varchar(255)',
        'GoogleReviewsURL' => 'Varchar(255)',
        'MapURL' => 'Varchar(255)',
        'PromoActive' => 'Boolean',
        'PromoTitle' => 'Varchar(80)',
        'PromoText' => 'Varchar(200)',
        'PromoUntil' => 'Date',
        'PromoLink' => 'Varchar(255)',
        'GA4Id' => 'Varchar(30)',
        'MetaPixelId' => 'Varchar(30)',
        'FestiveMode' => "Enum('auto,ukljuceno,iskljuceno','auto')",
        'FestiveGreetingOff' => 'Boolean',
        'FestiveGreeting' => 'Varchar(80)',
    ];

    /**
     * Tekst praznične čestitke u slideru; prazno = "Sretni praznici i sretna <iduća godina>.", a {godina} se zamijeni godinom.
     * Prazan rezultat znači da je čestitka isključena.
     */
    public function FestiveGreetingText(): string
    {
        if ($this->owner->FestiveGreetingOff) {
            return '';
        }
        $year = (int) date('n') === 1 ? (int) date('Y') : (int) date('Y') + 1;
        $text = trim((string) $this->owner->FestiveGreeting) ?: 'Sretni praznici i sretna {godina}.';
        return str_replace('{godina}', (string) $year, $text);
    }

    /**
     * Praznična (božićno-novogodišnja) tema: lampice ispod menija, snijeg u zaglavlju stranica i Djed Mraz pored loga.
     * Automatski je uključena od 1. decembra do 10. januara, a u Settings se može stalno uključiti ili isključiti.
     */
    public function IsFestive(): bool
    {
        $mode = $this->owner->FestiveMode ?: 'auto';
        if ($mode !== 'auto') {
            return $mode === 'ukljuceno';
        }
        $md = (int) date('nd');
        return $md >= 1201 || $md <= 110;
    }

    protected function updateCMSFields(FieldList $fields): void
    {
        $fields->addFieldsToTab('Root.Kontakt', [
            TextField::create('CompanyName', 'Naziv firme'),
            TextField::create('Address', 'Adresa'),
            TextField::create('MapURL', 'Link na Google mapu (lokacija agencije)'),
            TextareaField::create('Phones', 'Telefoni')->setRows(4)
                ->setDescription('Jedan broj u jednom redu. Prvi broj je glavni (dugme za poziv na telefonu).'),
            TextField::create('ViberNumber', 'Viber broj')->setDescription('Npr. 061 938 529. Na sajtu se pojavi dugme "Piši nam na Viber".'),
            TextField::create('WhatsAppNumber', 'WhatsApp broj'),
            EmailField::create('ContactEmail', 'E-mail na sajtu'),
            EmailField::create('InquiryEmail', 'E-mail za upite sa sajta')
                ->setDescription('Na ovu adresu stiže svaki upit iz forme. Prazno = e-mail na sajtu.'),
            TextField::create('WorkingHours', 'Radno vrijeme'),
            TextField::create('LicenseNumber', 'Broj licence agencije')->setDescription('Prikazuje se u podnožju (povjerenje kupaca).'),
            HeaderField::create('SocialH', 'Mreže', 3),
            TextField::create('FacebookURL', 'Facebook (link)'),
            TextField::create('InstagramURL', 'Instagram (link)'),
            TextField::create('TikTokURL', 'TikTok (link)'),
            TextField::create('ViberCommunityURL', 'Viber zajednica (link)')->setDescription('Link na Viber zajednicu/kanal za obavijesti o novim putovanjima.'),
            TextField::create('GoogleReviewsURL', 'Google recenzije (link)')->setDescription('Link na Google profil agencije; uz utiske putnika ide dugme "Sve recenzije na Googleu".'),
        ]);
        $fields->addFieldsToTab('Root.Akcija', [
            LiteralField::create('PromoInfo', '<p>Traka na vrhu sajta s odbrojavanjem (npr. Black Friday, First minute). Cijene na aranžmanima mijenjate u samom aranžmanu (Stara cijena).</p>'),
            CheckboxField::create('PromoActive', 'Prikaži traku s akcijom'),
            TextField::create('PromoTitle', 'Naslov')->setDescription('Npr. "Black Friday".'),
            TextField::create('PromoText', 'Tekst')->setDescription('Npr. "Do 20% popusta na zimska putovanja".'),
            DateField::create('PromoUntil', 'Traje do (odbrojavanje)'),
            TextField::create('PromoLink', 'Link')->setDescription('Npr. /putovanja/akcija ili prazno = aranžmani na početnoj.'),
        ]);
        $fields->addFieldsToTab('Root.Akcija', [
            HeaderField::create('FestiveH', 'Praznična tema', 3),
            \SilverStripe\Forms\DropdownField::create('FestiveMode', 'Božićno-novogodišnji ukrasi', [
                'auto' => 'Automatski (od 1. decembra do 10. januara)',
                'ukljuceno' => 'Uvijek uključeno',
                'iskljuceno' => 'Isključeno',
            ])->setDescription('Snježni rub s ledenicama i lampice ispod menija, snijeg i svjetla u zaglavlju, čestitka i Djed Mraz pored loga.'),
            \SilverStripe\Forms\DropdownField::create('FestiveGreetingOff', 'Čestitka u zaglavlju', [
                0 => 'Uključena',
                1 => 'Isključena',
            ])->setDescription('Zlatna poruka na sredini velikog slidera i zaglavlja stranica (vidi se na većim ekranima, dok je praznična tema uključena).'),
            TextField::create('FestiveGreeting', 'Tekst čestitke')
                ->setAttribute('placeholder', 'Sretni praznici i sretna {godina}.')
                ->setAttribute('maxlength', 80)
                ->setDescription('Prazno = "Sretni praznici i sretna 2027." {godina} se sam zamijeni godinom koja dolazi. Najbolje do 40 znakova.'),
        ]);
        $fields->addFieldsToTab('Root.Analitika', [
            LiteralField::create('AnaInfo', '<p>Mjerenje posjeta i oglasa. Pokreće se tek kad posjetilac prihvati kolačiće.</p>'),
            TextField::create('GA4Id', 'Google Analytics 4 (G-XXXXXXX)'),
            TextField::create('MetaPixelId', 'Meta (Facebook) Pixel ID'),
        ]);
    }

    public function PhoneList(): ArrayList
    {
        $rows = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->getOwner()->Phones))));
        return ArrayList::create(array_map(fn ($p) => ArrayData::create([
            'Number' => $p,
            'Tel' => self::tel($p),
            'IsMobile' => str_starts_with(ltrim(preg_replace('/\D/', '', $p), '0'), '6') || str_starts_with(preg_replace('/\D/', '', $p), '3876'),
        ]), $rows));
    }

    /** Ostali brojevi (bez glavnog) za pločice u kontaktu. */
    public function OtherPhones(): ArrayList
    {
        $list = $this->PhoneList()->toArray();
        array_shift($list);
        return ArrayList::create($list);
    }

    /** Link na mapu: uneseni link ili pretraga adrese na Google mapama. */
    public function MapsLink(): string
    {
        $o = $this->getOwner();
        if ($o->MapURL) {
            return (string) $o->MapURL;
        }
        return $o->Address ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode('Hazre Tours, ' . $o->Address) : '';
    }

    public function MainPhone(): ?ArrayData
    {
        return $this->PhoneList()->first();
    }

    /** "035 644 155" → "+38735644155" */
    public static function tel(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '387')) {
            return '+' . $digits;
        }
        return '+387' . ltrim($digits, '0');
    }

    public function ViberLink(): string
    {
        $n = (string) $this->getOwner()->ViberNumber;
        return $n ? 'viber://chat?number=' . rawurlencode(self::tel($n)) : '';
    }

    public function WhatsAppLink(): string
    {
        $n = (string) $this->getOwner()->WhatsAppNumber;
        return $n ? 'https://wa.me/' . ltrim(self::tel($n), '+') : '';
    }

    public function inquiryRecipient(): string
    {
        $o = $this->getOwner();
        return (string) ($o->InquiryEmail ?: $o->ContactEmail);
    }

    public function PromoVisible(): bool
    {
        $o = $this->getOwner();
        return $o->PromoActive && $o->PromoTitle && (!$o->PromoUntil || $o->PromoUntil >= date('Y-m-d'));
    }

    public function PromoUntilISO(): string
    {
        $o = $this->getOwner();
        return $o->PromoUntil ? $o->PromoUntil . 'T23:59:59' : '';
    }
}
