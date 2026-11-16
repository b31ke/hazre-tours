<?php

namespace HazreTours\Model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Director;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\HasManyList;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\Model\List\SS_List;
use SilverStripe\View\Parsers\URLSegmentFilter;

/**
 * Aranžman (izlet, putovanje, ljetovanje). Admin → Aranžmani.
 *
 * @property string $Title
 * @property string $URLSegment
 * @property string $Type
 * @property int $Price
 * @property int $OldPrice
 * @method Image Poster()
 * @method File ProgramFile()
 * @method HasManyList<TripTerm> Terms()
 * @method HasManyList<FaqItem> Faqs()
 * @method ManyManyList<TripCategory> Categories()
 * @method ManyManyList<Image> Gallery()
 */
class Trip extends DataObject
{
    private static string $table_name = 'HazreTrip';

    private static string $singular_name = 'Aranžman';

    private static string $plural_name = 'Aranžmani';

    public const TYPES = [
        'jednodnevni' => 'Jednodnevni izlet',
        'visednevni' => 'Višednevno putovanje',
        'ljetovanje' => 'Ljetovanje',
    ];

    public const TRANSPORT = [
        'autobus' => 'Autobus',
        'avion' => 'Avion',
        'vlastiti' => 'Vlastiti prevoz',
    ];

    /** Mjesta polaska koja agencija najčešće koristi (pretraga na sajtu). */
    public const CITIES = ['Tuzla', 'Srebrenik', 'Gračanica', 'Gradačac', 'Brčko', 'Orašje', 'Bijeljina', 'Kalesija', 'Šićki Brod'];

    /** Srebrenik: odavde se računa udaljenost do destinacije. */
    public const HOME_LAT = 44.7065;
    public const HOME_LNG = 18.4893;

    private static array $db = [
        'Title' => 'Varchar(150)',
        'URLSegment' => 'Varchar(150)',
        'Type' => "Enum('jednodnevni,visednevni,ljetovanje','jednodnevni')",
        'Transport' => "Enum('autobus,avion,vlastiti','autobus')",
        'Price' => 'Int',
        'OldPrice' => 'Int',
        'PromoNote' => 'Varchar(150)',
        'PromoUntil' => 'Date',
        'Highlight' => 'Varchar(150)',
        'Summary' => 'Text',
        'Program' => 'HTMLText',
        'Includes' => 'Text',
        'Excludes' => 'Text',
        'Accommodation' => 'Text',
        'KidsNote' => 'Text',
        'Badge' => 'Varchar(40)',
        'Departures' => 'Varchar(255)',
        'DestinationName' => 'Varchar(60)',
        'DestinationLat' => 'Decimal(9,5)',
        'DestinationLng' => 'Decimal(9,5)',
        'PosterIsFlyer' => 'Boolean(1)',
        'Featured' => 'Boolean',
        'ShowOnHome' => 'Boolean',
        'HomeSort' => 'Int',
        'Active' => 'Boolean(1)',
    ];

    private static array $has_one = [
        'Poster' => Image::class,
        'Photo' => Image::class,
        'ProgramFile' => File::class,
    ];

    private static array $has_many = [
        'Terms' => TripTerm::class,
        'Faqs' => FaqItem::class,
    ];

    private static array $many_many = [
        'Categories' => TripCategory::class,
        'Gallery' => Image::class,
    ];

    private static array $many_many_extraFields = [
        'Gallery' => ['SortOrder' => 'Int'],
    ];

    private static array $owns = ['Poster', 'Photo', 'ProgramFile', 'Gallery'];

    private static array $cascade_deletes = ['Terms'];

    private static array $indexes = [
        'URLSegment' => ['type' => 'unique', 'columns' => ['URLSegment']],
    ];

    private static string $default_sort = 'Title ASC';

    private static array $summary_fields = [
        'Poster.CMSThumbnail' => 'Plakat',
        'Title' => 'Naziv',
        'TypeLabel' => 'Vrsta',
        'NextTermLabel' => 'Sljedeći polazak',
        'PriceLabel' => 'Cijena',
        'Active.Nice' => 'Na sajtu',
        'HomeLabel' => 'Na početnoj',
    ];

    private static array $searchable_fields = ['Title', 'Type', 'Active'];

    private static array $field_labels = [
        'Title' => 'Naziv',
        'Type' => 'Vrsta',
        'Price' => 'Cijena (KM po osobi)',
        'Active' => 'Prikaži na sajtu',
    ];

    public function getCMSFields(): FieldList
    {
        $main = [
            TextField::create('Title', 'Naziv')->setDescription('Npr. "Advent u Budimpešti". Datumi se ne pišu u naziv, oni su u terminima.'),
            DropdownField::create('Type', 'Vrsta', self::TYPES),
            CheckboxSetField::create('Categories', 'Kategorije', TripCategory::get()->map('ID', 'Title'))
                ->setDescription('Advent, Nova godina, Shopping… Nove kategorije dodajete u Aranžmani i upiti → Kategorije.'),
            DropdownField::create('Transport', 'Prevoz', self::TRANSPORT),
            TextField::create('Highlight', 'Kratko na kartici')->setDescription('Jedna rečenica, npr. "Božićni sajam, Ribarska kula, Budimski dvorac".'),
            TextareaField::create('Summary', 'Kratak opis')->setRows(3)->setDescription('2–3 rečenice na vrhu stranice aranžmana i u pregledu linka (Viber, Facebook).'),
            TextField::create('Departures', 'Mjesta polaska')->setDescription('Odvojena zarezom: ' . implode(', ', self::CITIES)),
            TextField::create('Badge', 'Oznaka na slici')->setDescription('Nije obavezno. Npr. "Novo" ili "Avans do 08.12.".'),
            CheckboxField::create('ShowOnHome', 'Prikaži na početnoj')
                ->setDescription('Na početnoj su dva reda (najviše 8 aranžmana). Ako nijedan nije označen, prikazuje se 8 najbližih polazaka.'),
            NumericField::create('HomeSort', 'Redoslijed na početnoj')->setDescription('1 = prvi. Prazno = po datumu polaska.'),
            CheckboxField::create('Featured', 'Istakni (prvi u svim listama i u vrhu početne)'),
            CheckboxField::create('Active', 'Prikaži na sajtu'),
        ];
        if ($this->isInDB()) {
            $main[] = GridField::create('Terms', 'Termini (polasci)', $this->Terms()->sort('StartDate'), GridFieldConfig_RecordEditor::create())
                ->setDescription('Prošli termini se sami sklanjaju sa sajta. Upišite i broj slobodnih mjesta kad ih ostane malo.');
        } else {
            $main[] = LiteralField::create('TermsNote', '<p class="alert alert-info">Termine (datume polazaka) dodajete nakon što sačuvate aranžman.</p>');
        }

        $price = [
            NumericField::create('Price', 'Cijena (KM po osobi)')->setDescription('Osnovna cijena. Termin može imati svoju cijenu.'),
            NumericField::create('OldPrice', 'Stara cijena (precrtana)')->setDescription('Samo kad je akcija. Prazno = bez akcije.'),
            TextField::create('PromoNote', 'Napomena o akciji')->setDescription('Npr. "Akcijska cijena za prijave do 30.11."'),
            DateField::create('PromoUntil', 'Akcija traje do')->setDescription('Na stranici se prikazuje odbrojavanje. Nakon tog datuma stara cijena se sama sklanja.'),
            TextareaField::create('KidsNote', 'Popusti za djecu')->setRows(2),
        ];

        $content = [
            TextareaField::create('Includes', 'Aranžman obuhvata')->setRows(6)->setDescription('Jedna stavka u jednom redu.'),
            TextareaField::create('Excludes', 'Aranžman ne obuhvata')->setRows(6)->setDescription('Jedna stavka u jednom redu.'),
            TextareaField::create('Accommodation', 'Smještaj i prevoz')->setRows(4)->setDescription('Hotel, sobe, autobus. Nije obavezno.'),
            HTMLEditorField::create('Program', 'Program putovanja (po danima)')->setRows(20)
                ->setDescription('Svaki dan neka počne podebljanim redom, npr. "1. DAN – BiH – ISTANBUL". Na sajtu se dani prikazuju kao harmonika.'),
        ];

        $media = [
            UploadField::create('Poster', 'Plakat ili glavna fotografija')->setFolderName('aranzmani')->setAllowedFileCategories('image/supported'),
            UploadField::create('Photo', 'Fotografija destinacije')->setFolderName('destinacije')->setAllowedFileCategories('image/supported')
                ->setDescription('Prava fotografija grada (vodoravna, bez teksta) za razglednice u vrhu početne. Prazno = plakat.'),
            CheckboxField::create('PosterIsFlyer', 'Slika je plakat s tekstom')
                ->setDescription('Uključeno: na kartici se vidi samo srednji dio plakata (fotografija). Za običnu fotografiju isključite.'),
            UploadField::create('Gallery', 'Galerija fotografija')->setFolderName('galerija')->setAllowedFileCategories('image/supported')
                ->setDescription('Fotografije destinacije ili s ranijih putovanja (više odjednom).'),
            UploadField::create('ProgramFile', 'Program putovanja za preuzimanje (Word ili PDF)')->setFolderName('programi')->setAllowedExtensions(['doc', 'docx', 'pdf']),
        ];

        $map = [
            LiteralField::create('MapNote', '<p>Destinacija se crta na mapi ruta na početnoj (linija od Srebrenika). Koordinate: Google Maps → desni klik na grad → prvi red.</p>'),
            TextField::create('DestinationName', 'Naziv na mapi')->setDescription('Kratko, npr. "Istanbul".'),
            NumericField::create('DestinationLat', 'Geografska širina')->setScale(5),
            NumericField::create('DestinationLng', 'Geografska dužina')->setScale(5),
            TextField::create('URLSegment', 'Adresa na sajtu')->setDescription('hazretours.ba/aranzman/… Prazno = iz naziva.'),
        ];

        $tabs = [
            Tab::create('Main', 'Osnovno', ...$main),
            Tab::create('Cijena', 'Cijena i akcija', ...$price),
            Tab::create('Sadrzaj', 'Program i uslovi', ...$content),
            Tab::create('Slike', 'Slike i dokumenti', ...$media),
        ];
        if ($this->isInDB()) {
            $tabs[] = Tab::create('Pitanja', 'Česta pitanja',
                GridField::create('Faqs', 'Pitanja samo za ovaj aranžman (opšta pitanja su u Česta pitanja)', $this->Faqs(), GridFieldConfig_RecordEditor::create()));
        }
        $tabs[] = Tab::create('Mapa', 'Mapa i adresa', ...$map);

        $fields = FieldList::create(TabSet::create('Root', ...$tabs));
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public function validate(): \SilverStripe\Core\Validation\ValidationResult
    {
        $result = parent::validate();
        if (!trim((string) $this->Title)) {
            $result->addFieldError('Title', 'Upišite naziv aranžmana.');
        }
        if ($this->OldPrice && $this->OldPrice <= $this->Price) {
            $result->addFieldError('OldPrice', 'Stara cijena treba biti veća od nove.');
        }
        return $result;
    }

    protected function onBeforeWrite(): void
    {
        parent::onBeforeWrite();
        $slug = URLSegmentFilter::create()->filter((string) ($this->URLSegment ?: $this->Title)) ?: 'aranzman';
        $base = $slug;
        $i = 2;
        while (self::get()->filter('URLSegment', $slug)->exclude('ID', (int) $this->ID)->exists()) {
            $slug = $base . '-' . $i++;
        }
        $this->URLSegment = $slug;
    }

    /** Lista aktivnih aranžmana se računa jednom po zahtjevu (meni, kartice, pretraga i forma je dijele). */
    private static ?array $liveCache = null;

    /** Budući termini ovog aranžmana, učitani jednom (kartica ih koristi desetak puta). */
    private ?array $upcomingCache = null;

    /** Već izračunati linkovi slika po širini. */
    private array $imageCache = [];

    /** Mjesto na početnoj (1–8) u ovom prikazu; 0 = nije u dva reda na početnoj. Postavlja HomePage::GridTrips. */
    public int $homeRank = 0;

    public function HomeRank(): int
    {
        return $this->homeRank;
    }

    public function getHomeLabel(): string
    {
        return $this->ShowOnHome ? ('Da' . ($this->HomeSort ? ' (' . $this->HomeSort . '.)' : '')) : '';
    }

    /** Aktivni aranžmani s budućim terminom: istaknuti prvi, pa po prvom polasku. */
    public static function liveList(?SS_List $source = null): ArrayList
    {
        if (self::$liveCache === null) {
            $trips = self::get()->filter('Active', true)->toArray();
            // svi budući termini aktivnih aranžmana jednim upitom
            $ids = array_map(fn (Trip $t) => $t->ID, $trips);
            $byTrip = [];
            if ($ids) {
                foreach (TripTerm::get()->filter(['TripID' => $ids, 'StartDate:GreaterThanOrEqual' => date('Y-m-d')])->sort('StartDate') as $term) {
                    $byTrip[$term->TripID][] = $term;
                }
            }
            foreach ($trips as $t) {
                $t->setUpcoming($byTrip[$t->ID] ?? []);
            }
            $trips = array_filter($trips, fn (Trip $t) => $t->NextTerm() !== null);
            usort($trips, fn (Trip $a, Trip $b) => [$b->Featured, $a->NextTerm()->StartDate] <=> [$a->Featured, $b->NextTerm()->StartDate]);
            self::$liveCache = array_values($trips);
        }
        if ($source === null) {
            return ArrayList::create(self::$liveCache);
        }
        // podskup (npr. kategorija): isti objekti iz memorije, isti redoslijed
        $ids = array_flip($source->column('ID'));
        return ArrayList::create(array_values(array_filter(self::$liveCache, fn (Trip $t) => isset($ids[$t->ID]))));
    }

    /** Postavlja već učitane termine (iz liveList) i povezuje ih s ovim aranžmanom bez novih upita. */
    public function setUpcoming(array $terms): void
    {
        foreach ($terms as $term) {
            $term->setTripObject($this);
        }
        $this->upcomingCache = $terms;
    }

    private function upcomingArray(): array
    {
        if ($this->upcomingCache === null) {
            $this->setUpcoming($this->Terms()->filter('StartDate:GreaterThanOrEqual', date('Y-m-d'))->sort('StartDate')->toArray());
        }
        return $this->upcomingCache;
    }

    public function Link(): string
    {
        return Director::baseURL() . 'aranzman/' . $this->URLSegment;
    }

    public function AbsoluteLink(): string
    {
        return Director::absoluteURL($this->Link());
    }

    public function getTypeLabel(): string
    {
        return self::TYPES[$this->Type] ?? (string) $this->Type;
    }

    public function getShortTypeLabel(): string
    {
        return ['jednodnevni' => 'Jednodnevni', 'visednevni' => 'Višednevni', 'ljetovanje' => 'Ljetovanje'][$this->Type] ?? '';
    }

    public function getTransportLabel(): string
    {
        return self::TRANSPORT[$this->Transport] ?? '';
    }

    public function UpcomingTerms(): ArrayList
    {
        return ArrayList::create($this->upcomingArray());
    }

    public function NextTerm(): ?TripTerm
    {
        return $this->upcomingArray()[0] ?? null;
    }

    /** Prvi termin u koji se još može prijaviti. */
    public function NextOpenTerm(): ?TripTerm
    {
        foreach ($this->upcomingArray() as $t) {
            if (!$t->IsFull()) {
                return $t;
            }
        }
        return null;
    }

    public function getNextTermLabel(): string
    {
        return ($t = $this->NextTerm()) ? $t->Label() : '—';
    }

    public function MinPrice(): int
    {
        $prices = array_map(fn (TripTerm $t) => $t->EffectivePrice(), $this->UpcomingTerms()->toArray());
        return $prices ? min($prices) : (int) $this->Price;
    }

    public function HasPriceFrom(): bool
    {
        $prices = array_unique(array_map(fn (TripTerm $t) => $t->EffectivePrice(), $this->UpcomingTerms()->toArray()));
        return count($prices) > 1;
    }

    public function getPriceLabel(): string
    {
        return ($this->HasPriceFrom() ? 'od ' : '') . $this->MinPrice() . ' KM';
    }

    /** Akcija traje: stara cijena postoji i rok nije prošao. */
    public function OnPromo(): bool
    {
        return $this->OldPrice > $this->MinPrice() && (!$this->PromoUntil || $this->PromoUntil >= date('Y-m-d'));
    }

    public function DiscountPercent(): int
    {
        return $this->OnPromo() ? (int) round(100 - $this->MinPrice() / $this->OldPrice * 100) : 0;
    }

    public function PromoUntilISO(): string
    {
        return $this->PromoUntil ? $this->PromoUntil . 'T23:59:59' : '';
    }

    /** Svi termini popunjeni. */
    public function SoldOut(): bool
    {
        return $this->NextTerm() !== null && $this->NextOpenTerm() === null;
    }

    /** Najmanje slobodnih mjesta među budućim terminima (0 = nije upisano). */
    public function FewSeatsLeft(): int
    {
        $left = array_filter(array_map(fn (TripTerm $t) => $t->IsFull() ? 0 : (int) $t->SeatsLeft, $this->UpcomingTerms()->toArray()));
        $min = $left ? min($left) : 0;
        return $min > 0 && $min <= 10 ? $min : 0;
    }

    public function TabLabel(): string
    {
        $terms = $this->UpcomingTerms()->toArray();
        if (!$terms) {
            return 'Termin uskoro';
        }
        if (count($terms) > 2) {
            return count($terms) . ' termina od ' . $terms[0]->obj('StartDate')->Format('dd.MM.');
        }
        $dates = implode(' / ', array_map(fn (TripTerm $t) => $t->obj('StartDate')->Format('dd.MM.'), $terms));
        return $dates . ' | ' . $this->DurationLabel();
    }

    public function DurationLabel(): string
    {
        $days = array_values(array_unique(array_map(fn (TripTerm $t) => $t->Days(), $this->UpcomingTerms()->toArray())));
        sort($days);
        if (!$days) {
            return $this->Type === 'jednodnevni' ? '1 dan' : '';
        }
        return implode(' ili ', $days) . (end($days) === 1 ? ' dan' : ' dana');
    }

    public function departureArray(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->Departures))));
    }

    public function DepartureList(): ArrayList
    {
        return ArrayList::create(array_map(fn ($c) => ArrayData::create(['Name' => $c]), $this->departureArray()));
    }

    public function DeparturesShort(): string
    {
        $c = $this->departureArray();
        return implode(', ', array_slice($c, 0, 4)) . (count($c) > 4 ? '…' : '');
    }

    public function DeparturesData(): string
    {
        return implode('|', $this->departureArray());
    }

    public function MonthsData(): string
    {
        return implode('|', array_unique(array_map(fn (TripTerm $t) => substr((string) $t->StartDate, 0, 7), $this->UpcomingTerms()->toArray())));
    }

    public function CategoriesData(): string
    {
        return implode('|', $this->Categories()->column('URLSegment'));
    }

    public function IncludesList(): ArrayList
    {
        return $this->lines('Includes');
    }

    public function ExcludesList(): ArrayList
    {
        return $this->lines('Excludes');
    }

    private function lines(string $field): ArrayList
    {
        $rows = array_filter(array_map('trim', preg_split('/\R/', (string) $this->$field)));
        return ArrayList::create(array_map(fn ($r) => ArrayData::create(['Text' => ltrim($r, "-•* \t")]), $rows));
    }

    public function BadgeLabel(): string
    {
        return $this->Badge ?: $this->getShortTypeLabel();
    }

    /** Slika za karticu (webp, do 900 px). */
    public function CardImageURL(): string
    {
        return $this->imageURL(900);
    }

    public function HeroImageURL(): string
    {
        return $this->imageURL(1800);
    }

    /** Široka slika za slider: kod plakata se izreže srednja traka (fotografija), bez teksta plakata. */
    public function SlideImageURL(): string
    {
        $img = $this->Poster();
        if (!$img->exists()) {
            return '';
        }
        $out = $this->PosterIsFlyer ? $img->Fill(1600, 600) : $img->ScaleMaxWidth(1800);
        return (string) (($out?->Convert('webp') ?: $out)?->getURL() ?? '');
    }

    /** Fotografija destinacije za razglednicu u kolažu na početnoj (webp, do 1200 px); prazno ako nije postavljena. */
    public function PhotoURL(): string
    {
        return $this->imageURL(1200, 'Photo');
    }

    private function imageURL(int $width, string $rel = 'Poster'): string
    {
        $ck = $rel . $width;
        if (isset($this->imageCache[$ck])) {
            return $this->imageCache[$ck];
        }
        if (!$this->{$rel . 'ID'}) {
            return $this->imageCache[$ck] = '';
        }
        $cache = \SilverStripe\Core\Injector\Injector::inst()->get(\Psr\SimpleCache\CacheInterface::class . '.hazreImages');
        $img = $this->{$rel}();
        if (!$img->exists()) {
            return $this->imageCache[$ck] = '';
        }
        $key = 'p' . $img->ID . '_' . $width . '_' . strtotime((string) $img->LastEdited) . '_' . substr((string) $img->FileHash, 0, 10);
        $url = $cache->get($key);
        if (!is_string($url) || $url === '') {
            $scaled = $img->ScaleMaxWidth($width);
            $webp = $scaled?->Convert('webp');
            $url = (string) (($webp ?: $scaled)?->getURL() ?? '');
            if ($url !== '') {
                $cache->set($key, $url);
            }
        }
        return $this->imageCache[$ck] = $url;
    }

    /** Slika za pregled linka (Facebook, Viber) 1200x630, pamti se u kešu kao i ostale. */
    public function OgImageURL(): string
    {
        if (!$this->PosterID || !($img = $this->Poster())->exists()) {
            return '';
        }
        $cache = \SilverStripe\Core\Injector\Injector::inst()->get(\Psr\SimpleCache\CacheInterface::class . '.hazreImages');
        $key = 'og' . $img->ID . '_' . strtotime((string) $img->LastEdited) . '_' . substr((string) $img->FileHash, 0, 10);
        $url = $cache->get($key);
        if (!is_string($url) || $url === '') {
            // u keš ide samo putanja: puna adresa zavisi od domene (lokalno, privremena adresa, hazretours.ba)
            $url = (string) ($img->FillMax(1200, 630)?->getURL() ?? '');
            if ($url !== '') {
                $cache->set($key, $url);
            }
        }
        return $url !== '' ? Director::absoluteURL($url) : '';
    }

    public function GallerySorted(): SS_List
    {
        return $this->Gallery()->sort('SortOrder');
    }

    /** Udaljenost od Srebrenika zračnom linijom (km). */
    public function DistanceKm(): int
    {
        if (!$this->DestinationLat || !$this->DestinationLng) {
            return 0;
        }
        $r = 6371;
        $dLat = deg2rad($this->DestinationLat - self::HOME_LAT);
        $dLng = deg2rad($this->DestinationLng - self::HOME_LNG);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad(self::HOME_LAT)) * cos(deg2rad((float) $this->DestinationLat)) * sin($dLng / 2) ** 2;
        return (int) round($r * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    /**
     * Program podijeljen po danima (za harmoniku). Dan počinje paragrafom koji izgleda kao naslov dana:
     * "1. DAN – …", "1.DAN-…", "DAN: …", "22.01.2027-PETAK …". Ako se ne prepozna nijedan dan, vraća se cijeli program kao jedan dio.
     */
    public function ProgramDays(): ArrayList
    {
        $html = (string) $this->Program;
        if (trim(strip_tags($html)) === '') {
            return ArrayList::create();
        }
        // svaki red (i onaj iza <br>) postaje svoj pasus, da naslov dana ne ostane zalijepljen za prethodni tekst
        $html = preg_replace('/<br\s*\/?>/i', '</p><p>', $html);
        // naslov dana kao jedina stavka numerisane liste ("<ol><li>DAN: …</li></ol>") postaje obični red
        $html = preg_replace('/<ol[^>]*>\s*<li[^>]*>(.{0,200}?DAN.{0,200}?)<\/li>\s*<\/ol>/isu', '<p>$1</p>', $html);
        $parts = preg_split('/(?=<(?:p|ul|ol|h[2-4])[\s>])/i', $html);
        $intro = '';
        $days = [];
        foreach ($parts as $part) {
            $text = trim(html_entity_decode(strip_tags($part), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            // bez ukrasnih znakova na početku reda (emoji, strelice)
            $text = preg_replace('/^[^\p{L}\p{N}]+/u', '', $text);
            $isHead = $text !== '' && mb_strlen($text) < 160 && (
                preg_match('/^(\d{1,2}\s*\.\s*DAN\b|DAN\s*[:\d]|\d{1,2}\.\d{1,2}\.\d{4}\s*\.?\s*[-–—])/iu', $text)
                // npr. "BiH-GRAZ-MARIBOR – 12.12.2026.-SUBOTA"
                || (mb_strlen($text) < 120 && preg_match('/\d{1,2}\.\d{1,2}\.\d{4}\.?\s*[-–—]?\s*(PONEDJELJAK|UTORAK|SRIJEDA|ČETVRTAK|PETAK|SUBOTA|NEDJELJA)\b/u', $text))
            );
            if ($isHead) {
                $days[] = ['title' => $text, 'body' => ''];
            } elseif ($days) {
                $days[count($days) - 1]['body'] .= $part;
            } else {
                $intro .= $part;
            }
        }
        if (!$days) {
            return ArrayList::create([ArrayData::create(['Title' => 'Program putovanja', 'Body' => DBField::create_field('HTMLFragment', $html), 'Num' => 1, 'IsIntro' => false])]);
        }
        $out = [];
        $introText = trim(html_entity_decode(strip_tags($intro), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($introText !== '' && !preg_match('/^PROGRAM PUTOVANJA:?$/iu', $introText)) {
            $out[] = ArrayData::create(['Title' => 'Važno', 'Body' => DBField::create_field('HTMLFragment', $intro), 'Num' => 0, 'IsIntro' => true]);
        }
        foreach ($days as $i => $d) {
            // Tekst nakon zadnjeg dana (uključeno/nije uključeno, napomene) je već u posebnim karticama
            $body = preg_split('/<p[^>]*>\s*(<strong>)?\s*(Aranžman obuh|ARANŽMAN RAĐEN|Napomen)/iu', $d['body'])[0];
            $out[] = ArrayData::create([
                'Title' => preg_replace('/^(\d{1,2}\s*\.\s*DAN|DAN)\s*[-–—:]?\s*/iu', '', $d['title']) ?: $d['title'],
                'Body' => DBField::create_field('HTMLFragment', $body),
                'Num' => $i + 1,
                'IsIntro' => false,
            ]);
        }
        return ArrayList::create($out);
    }

    /** Napomene iz programa (uslovi uz aranžman): sve od "ARANŽMAN RAĐEN NA BAZI" ili "Napomene" dalje. */
    public function ProgramNotes(): ?\SilverStripe\ORM\FieldType\DBHTMLText
    {
        $html = (string) $this->Program;
        if (!preg_match('/<p[^>]*>\s*(<strong>)?\s*(ARANŽMAN RAĐEN|Napomen)/iu', $html, $m, PREG_OFFSET_CAPTURE)) {
            return null;
        }
        return DBField::create_field('HTMLFragment', substr($html, $m[0][1]));
    }

    /** Pitanja za ovaj aranžman pa opšta. */
    public function AllFaqs(): ArrayList
    {
        return ArrayList::create(array_merge(
            $this->Faqs()->toArray(),
            FaqItem::get()->filter('TripID', 0)->toArray()
        ));
    }

    public function Reviews(): SS_List
    {
        return Review::get()->filter(['TripID' => $this->ID, 'Published' => true]);
    }

    /** Podaci o putovanju za Google (schema.org). */
    public function JsonLD(): DBField
    {
        $offers = [];
        foreach ($this->UpcomingTerms() as $term) {
            $offers[] = [
                '@type' => 'Offer',
                'price' => $term->EffectivePrice(),
                'priceCurrency' => 'BAM',
                'availability' => $term->IsFull() ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
                'validFrom' => date('Y-m-d', strtotime((string) $this->Created)),
                'url' => $this->AbsoluteLink(),
                'availabilityStarts' => $term->StartDate,
            ];
        }
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => $this->Title,
            'description' => $this->Summary ?: $this->Highlight,
            'url' => $this->AbsoluteLink(),
            'touristType' => $this->getTypeLabel(),
            'provider' => ['@type' => 'TravelAgency', 'name' => 'Hazre Tours', 'url' => Director::absoluteBaseURL()],
            'offers' => $offers,
        ];
        if ($img = $this->CardImageURL()) {
            $data['image'] = Director::absoluteURL($img);
        }
        return DBField::create_field('HTMLFragment', '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) . '</script>');
    }

    /** Za mapu ruta i pretragu u zaglavlju. */
    public function toPublicArray(): array
    {
        return [
            'title' => $this->Title,
            'url' => $this->Link(),
            'img' => $this->CardImageURL(),
            'flyer' => (bool) $this->PosterIsFlyer,
            'price' => $this->MinPrice(),
            'from' => $this->HasPriceFrom(),
            'date' => ($t = $this->NextTerm()) ? $t->obj('StartDate')->Format('dd.MM.') : '',
            'dest' => $this->DestinationName ?: $this->Title,
            'lat' => (float) $this->DestinationLat,
            'lng' => (float) $this->DestinationLng,
            'km' => $this->DistanceKm(),
            'q' => mb_strtolower($this->Title . ' ' . $this->Highlight . ' ' . $this->DestinationName . ' ' . implode(' ', $this->Categories()->column('Title'))),
        ];
    }

    public function canView($member = null): bool
    {
        return $this->Active || parent::canView($member);
    }
}
