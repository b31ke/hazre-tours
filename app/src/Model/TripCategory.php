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

    private static array $has_one = [
        'Image' => \SilverStripe\Assets\Image::class,
    ];

    private static array $owns = ['Image'];

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
            \SilverStripe\AssetAdmin\Forms\UploadField::create('Image', 'Slika pločice')
                ->setFolderName('teme')->setAllowedFileCategories('image/supported')
                ->setDescription('Fotografija koja predstavlja temu (vodoravna, najmanje 1600 px širine). Prazno = plakat jednog od aranžmana.'),
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
    private ?\SilverStripe\Model\List\ArrayList $liveCache = null;

    /** Izračuna se jednom po stranici (meni, pločica, podnožje). */
    public function LiveTrips(): \SilverStripe\Model\List\ArrayList
    {
        return $this->liveCache ??= Trip::liveList($this->Trips());
    }

    public function getTripCount(): int
    {
        return $this->LiveTrips()->count();
    }

    /** Aranžman čija slika ide na pločicu (početna bira različite za svaku temu). */
    public ?Trip $coverOverride = null;

    /** Slika pločice: izabrani aranžman ili plakat prvog aranžmana. */
    public function CoverTrip(): ?Trip
    {
        return $this->coverOverride ?? $this->LiveTrips()->first();
    }

    /** Fotografija teme za pločicu (webp, do 1400 px), pamti se u kešu kao i plakati. */
    public function TileImageURL(): string
    {
        if (!$this->ImageID || !($img = $this->Image())->exists()) {
            return '';
        }
        $cache = \SilverStripe\Core\Injector\Injector::inst()->get(\Psr\SimpleCache\CacheInterface::class . '.hazreImages');
        $key = 'cat' . $img->ID . '_' . strtotime((string) $img->LastEdited) . '_' . substr((string) $img->FileHash, 0, 10);
        $url = $cache->get($key);
        if (!is_string($url) || $url === '') {
            $scaled = $img->ScaleMaxWidth(1400);
            $url = (string) (($scaled?->Convert('webp') ?: $scaled)?->getURL() ?? '');
            if ($url !== '') {
                $cache->set($key, $url);
            }
        }
        return $url;
    }

    /** Najniža cijena u temi (za "od … KM" na pločici). */
    public function MinPrice(): int
    {
        $prices = array_map(fn (Trip $t) => $t->MinPrice(), $this->LiveTrips()->toArray());
        return $prices ? min($prices) : 0;
    }

    /** Nazivi prva tri putovanja (za veliku pločicu). */
    public function TopTrips()
    {
        return $this->LiveTrips()->limit(3);
    }
}
