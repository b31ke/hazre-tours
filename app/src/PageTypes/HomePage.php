<?php

namespace HazreTours\PageTypes;

use HazreTours\Model\Trip;
use HazreTours\Model\TripTerm;
use Page;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Model\List\ArrayList;

/**
 * Početna stranica: naslov i slika u vrhu se mijenjaju u CMS-u (Pages → Početna),
 * aranžmani dolaze iz admina (Aranžmani).
 */
class HomePage extends Page
{
    private static string $table_name = 'HazreHomePage';

    private static string $singular_name = 'Početna';

    private static string $description = 'Početna stranica s pretragom i aranžmanima';

    private static string $icon_class = 'font-icon-p-home';

    private static array $db = [
        'HeroEyebrow' => 'Varchar(150)',
        'HeroTitle' => 'Varchar(150)',
        'HeroLead' => 'Text',
        'TripsTitle' => 'Varchar(150)',
    ];

    private static array $has_one = [
        'HeroImage' => Image::class,
    ];

    private static array $owns = ['HeroImage'];

    private static array $defaults = [
        'HeroEyebrow' => 'Putnička agencija i prevoznik iz Srebrenika',
        'HeroTitle' => 'Putujte sigurno, našim autobusima',
        'TripsTitle' => 'Aktuelni aranžmani',
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab('Root.Main', [
            TextField::create('HeroEyebrow', 'Mali naslov iznad'),
            TextField::create('HeroTitle', 'Glavni naslov')->setDescription('Riječ između *zvjezdica* se ističe narandžastom bojom, npr. "Putujte *sigurno*, našim autobusima".'),
            TextareaField::create('HeroLead', 'Tekst ispod naslova')->setRows(3),
            UploadField::create('HeroImage', 'Slika u pozadini')->setFolderName('naslovna')->setAllowedFileCategories('image/supported'),
            TextField::create('TripsTitle', 'Naslov iznad aranžmana'),
        ], 'Content');
        $fields->dataFieldByName('Content')?->setTitle('Tekst ispod aranžmana (nije obavezno)');
        return $fields;
    }

    /** Naslov s istaknutom riječi: *riječ* → <span>riječ</span> */
    public function HeroTitleHTML(): \SilverStripe\ORM\FieldType\DBHTMLText
    {
        $safe = htmlspecialchars((string) $this->HeroTitle, ENT_QUOTES);
        $html = preg_replace('/\*([^*]+)\*/', '<span>$1</span>', $safe);
        return \SilverStripe\ORM\FieldType\DBField::create_field('HTMLFragment', $html);
    }

    /** Aktivni aranžmani koji imaju bar jedan budući termin, po prvom polasku. */
    public function UpcomingTrips(): ArrayList
    {
        $trips = Trip::get()->filter('Active', true)->toArray();
        $trips = array_filter($trips, fn (Trip $t) => $t->NextTerm() !== null);
        usort($trips, fn (Trip $a, Trip $b) => strcmp($a->NextTerm()->StartDate, $b->NextTerm()->StartDate));
        return ArrayList::create(array_values($trips));
    }

    /** Šest najbližih polazaka (traka ispod naslova). */
    public function NextDepartures(): ArrayList
    {
        return ArrayList::create(TripTerm::get()
            ->filter(['StartDate:GreaterThanOrEqual' => date('Y-m-d'), 'Trip.Active' => true])
            ->exclude('Status', 'popunjeno')
            ->sort('StartDate')
            ->limit(6)
            ->toArray());
    }

    /** Mjeseci u kojima ima polazaka (za pretragu). */
    public function SearchMonths(): ArrayList
    {
        $names = ['januar', 'februar', 'mart', 'april', 'maj', 'juni', 'juli', 'august', 'septembar', 'oktobar', 'novembar', 'decembar'];
        $months = array_unique(TripTerm::get()
            ->filter(['StartDate:GreaterThanOrEqual' => date('Y-m-d'), 'Trip.Active' => true])
            ->sort('StartDate')->column('StartDate'));
        $months = array_unique(array_map(fn ($d) => substr($d, 0, 7), $months));
        return ArrayList::create(array_map(fn ($m) => \SilverStripe\Model\ArrayData::create([
            'Value' => $m,
            'Label' => $names[(int) substr($m, 5, 2) - 1] . ' ' . substr($m, 0, 4) . '.',
        ]), array_values($months)));
    }

    public function SearchCities(): ArrayList
    {
        return ArrayList::create(array_map(fn ($c) => \SilverStripe\Model\ArrayData::create(['Name' => $c]), Trip::CITIES));
    }
}
