<?php

namespace HazreTours\Model;

use SilverStripe\Control\Director;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\View\Parsers\URLSegmentFilter;

/**
 * Kategorija putovanja: Advent, Nova godina, Shopping, Zimovanje, Ljetovanje Turska…
 * Ima svoju stranicu /putovanja/<naziv> i pločicu na početnoj.
 *
 * @property string $Title
 * @property string $URLSegment
 * @method ManyManyList<Trip> Trips()
 */
class TripCategory extends DataObject
{
    private static string $table_name = 'HazreTripCategory';

    private static string $singular_name = 'Kategorija';

    private static string $plural_name = 'Kategorije';

    private static array $db = [
        'Title' => 'Varchar(80)',
        'URLSegment' => 'Varchar(80)',
        'Intro' => 'Text',
        'ShowOnHome' => 'Boolean(1)',
        'Sort' => 'Int',
    ];

    private static array $belongs_many_many = [
        'Trips' => Trip::class,
    ];

    private static string $default_sort = 'Sort ASC, Title ASC';

    private static array $summary_fields = [
        'Title' => 'Naziv',
        'TripCount' => 'Aktivnih aranžmana',
        'ShowOnHome.Nice' => 'Na početnoj',
        'Sort' => 'Redoslijed',
    ];

    public function getCMSFields(): FieldList
    {
        return FieldList::create(
            TextField::create('Title', 'Naziv')->setDescription('Npr. "Advent", "Nova godina", "Ljetovanje Turska".'),
            TextareaField::create('Intro', 'Kratak tekst na stranici kategorije')->setRows(3),
            CheckboxField::create('ShowOnHome', 'Prikaži pločicu na početnoj'),
            NumericField::create('Sort', 'Redoslijed')->setDescription('Manji broj ide prvi.'),
            TextField::create('URLSegment', 'Adresa')->setDescription('hazretours.ba/putovanja/… Prazno = iz naziva.'),
        );
    }

    protected function onBeforeWrite(): void
    {
        parent::onBeforeWrite();
        $slug = URLSegmentFilter::create()->filter((string) ($this->URLSegment ?: $this->Title)) ?: 'kategorija';
        $base = $slug;
        $i = 2;
        while (self::get()->filter('URLSegment', $slug)->exclude('ID', (int) $this->ID)->exists()) {
            $slug = $base . '-' . $i++;
        }
        $this->URLSegment = $slug;
    }

    public function Link(): string
    {
        return Director::baseURL() . 'putovanja/' . $this->URLSegment;
    }

    /** Aktivni aranžmani s budućim terminom, po prvom polasku. */
    public function LiveTrips(): \SilverStripe\Model\List\ArrayList
    {
        return Trip::liveList($this->Trips());
    }

    public function getTripCount(): int
    {
        return $this->LiveTrips()->count();
    }

    /** Slika pločice: plakat prvog aranžmana. */
    public function CoverTrip(): ?Trip
    {
        return $this->LiveTrips()->first();
    }
}
