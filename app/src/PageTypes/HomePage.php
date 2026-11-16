<?php

namespace HazreTours\PageTypes;

use HazreTours\Model\Review;
use HazreTours\Model\Trip;
use HazreTours\Model\TripCategory;
use HazreTours\Model\TripTerm;
use HazreTours\Model\FaqItem;
use Page;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\FieldType\DBField;

/**
 * Početna: naslov i slika u vrhu se mijenjaju u CMS-u (Pages → Početna),
 * aranžmani, kategorije, pitanja i utisci dolaze iz admina (Aranžmani i upiti).
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

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(['PageForm', 'Intro']);
        $fields->addFieldsToTab('Root.Main', [
            TextField::create('HeroEyebrow', 'Mali naslov iznad'),
            TextField::create('HeroTitle', 'Glavni naslov')->setDescription('Riječ između *zvjezdica* se ističe bojom, npr. "Putujte *sigurno*, našim autobusima".'),
            TextareaField::create('HeroLead', 'Tekst ispod naslova')->setRows(3),
            UploadField::create('HeroImage', 'Slika u pozadini (nije obavezno)')->setFolderName('naslovna')->setAllowedFileCategories('image/supported'),
            TextField::create('TripsTitle', 'Naslov iznad aranžmana'),
        ], 'Content');
        $fields->dataFieldByName('Content')?->setTitle('Tekst ispod aranžmana (nije obavezno)');
        return $fields;
    }

    public function HeroTitleHTML(): DBField
    {
        $safe = htmlspecialchars((string) $this->HeroTitle, ENT_QUOTES);
        return DBField::create_field('HTMLFragment', preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $safe));
    }

    public function UpcomingTrips(): ArrayList
    {
        return Trip::liveList();
    }

    /**
     * Kolaž u vrhu: tri različite destinacije sa slikom (najbliži polazak prvi),
     * da se ne ponove npr. dva Istanbula.
     */
    public function HeroTrips(): ArrayList
    {
        $out = [];
        $seen = [];
        foreach (Trip::liveList() as $trip) {
            $dest = $trip->DestinationName ?: $trip->Title;
            if (!$trip->PosterID || isset($seen[$dest])) {
                continue;
            }
            $seen[$dest] = true;
            $out[] = $trip;
            if (count($out) === 3) {
                break;
            }
        }
        return ArrayList::create($out);
    }

    /** Slider u vrhu: istaknuti pa najbliži polasci, najviše 5. */
    public function HeroSlides(): ArrayList
    {
        return Trip::liveList()->limit(5);
    }

    /** Ploča polazaka: najbližih 8 polazaka. */
    public function NextDepartures(): ArrayList
    {
        return ArrayList::create(TripTerm::get()
            ->filter(['StartDate:GreaterThanOrEqual' => date('Y-m-d'), 'Trip.Active' => true])
            ->sort('StartDate')
            ->limit(8)
            ->toArray());
    }

    public function HomeCategories(): ArrayList
    {
        $cats = array_filter(TripCategory::get()->filter('ShowOnHome', true)->toArray(), fn (TripCategory $c) => $c->getTripCount() > 0);
        return ArrayList::create(array_values($cats));
    }

    public function SearchMonths(): ArrayList
    {
        $names = ['januar', 'februar', 'mart', 'april', 'maj', 'juni', 'juli', 'august', 'septembar', 'oktobar', 'novembar', 'decembar'];
        $months = array_unique(array_map(fn ($d) => substr($d, 0, 7), TripTerm::get()
            ->filter(['StartDate:GreaterThanOrEqual' => date('Y-m-d'), 'Trip.Active' => true])
            ->sort('StartDate')->column('StartDate')));
        return ArrayList::create(array_map(fn ($m) => ArrayData::create([
            'Value' => $m,
            'Label' => $names[(int) substr($m, 5, 2) - 1] . ' ' . substr($m, 0, 4) . '.',
        ]), array_values($months)));
    }

    public function SearchCities(): ArrayList
    {
        return ArrayList::create(array_map(fn ($c) => ArrayData::create(['Name' => $c]), Trip::CITIES));
    }

    public function GeneralFaqs()
    {
        return FaqItem::get()->filter('TripID', 0)->limit(8);
    }

    public function PublishedReviews()
    {
        return Review::get()->filter('Published', true)->limit(9);
    }

    /** Brojevi u traci povjerenja. */
    public function YearsOnRoad(): int
    {
        return (int) date('Y') - 2008;
    }

    public function DestinationCount(): int
    {
        return count(array_unique(array_filter(Trip::get()->filter('Active', true)->column('DestinationName'))));
    }

    public function CityCount(): int
    {
        return count(Trip::CITIES);
    }

    /** Destinacije za mapu ruta (JSON za canvas). */
    public function MapJSON(): string
    {
        $seen = [];
        foreach (Trip::liveList() as $trip) {
            $d = $trip->toPublicArray();
            if (!$d['lat'] || isset($seen[$d['dest']])) {
                continue;
            }
            $seen[$d['dest']] = $d;
        }
        return json_encode(array_values($seen), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
    }

    public function GalleryPreview()
    {
        $page = GalleryPage::get()->first();
        return $page ? $page->SortedImages()->limit(8) : null;
    }

    public function GalleryLink(): string
    {
        return ($p = GalleryPage::get()->first()) ? $p->Link() : '';
    }
}
