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

    /** Mali naslov: dio iza "·" je u spanu koji se na telefonu krije (desktop isti). */
    public function HeroEyebrowHTML(): DBField
    {
        $safe = htmlspecialchars((string) $this->HeroEyebrow, ENT_QUOTES);
        $html = preg_replace('/^(.+?)(\s*·\s*.+)$/u', '$1<span class="hk-rest">$2</span>', $safe, 1);
        return DBField::create_field('HTMLFragment', $html);
    }

    /** Tekst ispod naslova: sve iza prve rečenice je u spanu koji se na telefonu krije (desktop isti). */
    public function HeroLeadHTML(): DBField
    {
        $safe = htmlspecialchars((string) $this->HeroLead, ENT_QUOTES);
        $html = preg_replace('/^(.+?[.!?])(\s+\S.*)$/us', '$1<span class="hl-rest">$2</span>', $safe, 1);
        return DBField::create_field('HTMLFragment', $html);
    }

    public function UpcomingTrips(): ArrayList
    {
        return Trip::liveList();
    }

    /** Koliko aranžmana stane u dva reda na početnoj (4 u redu na velikom ekranu). */
    public const HOME_LIMIT = 8;

    /**
     * Svi aktivni aranžmani za mrežu na početnoj: prvo izabrani u adminu ("Prikaži na početnoj", po redoslijedu),
     * pa ostali. Prvih 8 dobije mjesto (homeRank) i vidljivo je; ostali se pojave tek kad posjetilac filtrira ili traži.
     */
    public function GridTrips(): ArrayList
    {
        $all = Trip::liveList()->toArray();
        $picked = array_values(array_filter($all, fn (Trip $t) => $t->ShowOnHome));
        if ($picked) {
            usort($picked, fn (Trip $a, Trip $b) => [$a->HomeSort ?: 999, $a->NextTerm()->StartDate] <=> [$b->HomeSort ?: 999, $b->NextTerm()->StartDate]);
        } else {
            $picked = $all;
        }
        $picked = array_slice($picked, 0, self::HOME_LIMIT);
        $ids = [];
        foreach ($picked as $i => $t) {
            $t->homeRank = $i + 1;
            $ids[$t->ID] = true;
        }
        $rest = array_values(array_filter($all, fn (Trip $t) => !isset($ids[$t->ID])));
        foreach ($rest as $t) {
            $t->homeRank = 0;
        }
        return ArrayList::create(array_merge($picked, $rest));
    }

    public function AllTripsCount(): int
    {
        return Trip::liveList()->count();
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
        $cats = array_values(array_filter(TripCategory::get()->filter('ShowOnHome', true)->toArray(), fn (TripCategory $c) => $c->getTripCount() > 0));
        // svaka tema dobija drugu sliku (ne tri puta isti Graz): prvi aranžman teme čija destinacija još nije iskorištena
        $usedTrips = [];
        $usedDest = [];
        foreach ($cats as $cat) {
            $pick = null;
            foreach ([true, false] as $strict) {
                foreach ($cat->LiveTrips() as $trip) {
                    $dest = $trip->DestinationName ?: $trip->Title;
                    if (!$trip->PosterID || isset($usedTrips[$trip->ID]) || ($strict && isset($usedDest[$dest]))) {
                        continue;
                    }
                    $pick = $trip;
                    break 2;
                }
            }
            if ($pick) {
                $cat->coverOverride = $pick;
                $usedTrips[$pick->ID] = true;
                $usedDest[$pick->DestinationName ?: $pick->Title] = true;
            }
        }
        return ArrayList::create($cats);
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
        return (int) date('Y') - 1996;
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
