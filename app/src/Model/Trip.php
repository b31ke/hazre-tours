<?php

namespace HazreTours\Model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Director;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\View\Parsers\URLSegmentFilter;

/**
 * Aranžman (izlet, putovanje). Vodi se u adminu → Aranžmani.
 * Termini su posebno (TripTerm), da jedan aranžman može imati više polazaka.
 *
 * @property string $Title
 * @property string $URLSegment
 * @property string $Type
 * @property int $Price
 * @method Image Poster()
 * @method File ProgramFile()
 * @method HasManyList<TripTerm> Terms()
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

    /** Mjesta polaska koja agencija najčešće koristi (za pretragu na sajtu). */
    public const CITIES = ['Tuzla', 'Srebrenik', 'Gračanica', 'Gradačac', 'Brčko', 'Orašje', 'Bijeljina', 'Kalesija', 'Šićki Brod'];

    private static array $db = [
        'Title' => 'Varchar(150)',
        'URLSegment' => 'Varchar(150)',
        'Type' => "Enum('jednodnevni,visednevni,ljetovanje','jednodnevni')",
        'Price' => 'Int',
        'Highlight' => 'Varchar(150)',
        'Summary' => 'Text',
        'Program' => 'HTMLText',
        'Includes' => 'Text',
        'Excludes' => 'Text',
        'KidsNote' => 'Text',
        'Badge' => 'Varchar(40)',
        'Departures' => 'Varchar(255)',
        'PosterIsFlyer' => 'Boolean(1)',
        'Active' => 'Boolean(1)',
    ];

    private static array $has_one = [
        'Poster' => Image::class,
        'ProgramFile' => File::class,
    ];

    private static array $has_many = [
        'Terms' => TripTerm::class,
    ];

    private static array $owns = ['Poster', 'ProgramFile'];

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
        $fields = FieldList::create();
        $fields->push(TextField::create('Title', 'Naziv')->setDescription('Npr. "Advent u Budimpešti". Datumi se ne pišu u naziv, oni su u terminima.'));
        $fields->push(DropdownField::create('Type', 'Vrsta', self::TYPES));
        $fields->push(NumericField::create('Price', 'Cijena (KM po osobi)')->setDescription('Osnovna cijena. Termin može imati svoju cijenu (npr. 2 ili 3 dana).'));
        $fields->push(TextField::create('Highlight', 'Kratko ispod cijene')->setDescription('Jedna rečenica na kartici, npr. "Božićni sajam, Ribarska kula, Budimski dvorac".'));
        $fields->push(TextField::create('Badge', 'Oznaka na slici')->setDescription('Nije obavezno. Npr. "Avans do 08.12." ili "Novo". Prazno = vrsta putovanja.'));
        $fields->push(TextField::create('Departures', 'Mjesta polaska')->setDescription('Odvojena zarezom: ' . implode(', ', self::CITIES)));
        $fields->push(CheckboxField::create('Active', 'Prikaži na sajtu'));

        if ($this->isInDB()) {
            $config = GridFieldConfig_RecordEditor::create();
            $fields->push(GridField::create('Terms', 'Termini (polasci)', $this->Terms()->sort('StartDate'), $config)
                ->setDescription('Prošli termini se sami sklanjaju sa sajta.'));
        } else {
            $fields->push(\SilverStripe\Forms\LiteralField::create('TermsNote', '<p class="alert alert-info">Termine (datume polazaka) dodajete nakon što sačuvate aranžman.</p>'));
        }

        $fields->push(UploadField::create('Poster', 'Plakat ili fotografija')
            ->setFolderName('aranzmani')->setAllowedFileCategories('image/supported'));
        $fields->push(CheckboxField::create('PosterIsFlyer', 'Slika je plakat s tekstom')
            ->setDescription('Uključeno: na kartici se prikazuje samo srednji dio plakata (fotografija), bez teksta. Za običnu fotografiju isključite.'));
        $fields->push(UploadField::create('ProgramFile', 'Program putovanja (Word ili PDF)')
            ->setFolderName('programi')->setAllowedExtensions(['doc', 'docx', 'pdf']));

        $fields->push(TextareaField::create('Summary', 'Kratak opis')->setRows(3)->setDescription('2–3 rečenice na vrhu stranice aranžmana.'));
        $fields->push(TextareaField::create('Includes', 'Aranžman obuhvata')->setRows(5)->setDescription('Jedna stavka u jednom redu.'));
        $fields->push(TextareaField::create('Excludes', 'Aranžman ne obuhvata')->setRows(5)->setDescription('Jedna stavka u jednom redu.'));
        $fields->push(TextareaField::create('KidsNote', 'Popusti za djecu')->setRows(2));
        $fields->push(HTMLEditorField::create('Program', 'Program putovanja (po danima)')->setRows(18));
        $fields->push(TextField::create('URLSegment', 'Adresa na sajtu')->setDescription('hazretours.ba/aranzman/… Prazno = napravi se iz naziva.'));

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public function validate(): \SilverStripe\Core\Validation\ValidationResult
    {
        $result = parent::validate();
        if (!trim((string) $this->Title)) {
            $result->addFieldError('Title', 'Upišite naziv aranžmana.');
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

    /** Termini od danas nadalje, po datumu. */
    public function UpcomingTerms()
    {
        return $this->Terms()->filter('StartDate:GreaterThanOrEqual', date('Y-m-d'))->sort('StartDate');
    }

    public function NextTerm(): ?TripTerm
    {
        return $this->UpcomingTerms()->first();
    }

    public function getNextTermLabel(): string
    {
        return ($t = $this->NextTerm()) ? $t->Label() : '—';
    }

    /** Najniža cijena među budućim terminima (ili osnovna cijena). */
    public function MinPrice(): int
    {
        $prices = array_map(fn (TripTerm $t) => $t->EffectivePrice(), $this->UpcomingTerms()->toArray());
        return $prices ? min($prices) : (int) $this->Price;
    }

    /** Cijene se razlikuju po terminima, pa se piše "od". */
    public function HasPriceFrom(): bool
    {
        $prices = array_unique(array_map(fn (TripTerm $t) => $t->EffectivePrice(), $this->UpcomingTerms()->toArray()));
        return count($prices) > 1;
    }

    public function getPriceLabel(): string
    {
        return ($this->HasPriceFrom() ? 'od ' : '') . $this->MinPrice() . ' KM';
    }

    /** Tekst na jezičku kartice: "28.11. | 1 dan", "05.12. / 19.12. | 1 dan", "5 termina od 08.01." */
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

    /** "1 dan", "7 dana" ili "2 ili 3 dana" kad termini traju različito. */
    public function DurationLabel(): string
    {
        $days = array_values(array_unique(array_map(fn (TripTerm $t) => $t->Days(), $this->UpcomingTerms()->toArray())));
        sort($days);
        if (!$days) {
            return $this->Type === 'jednodnevni' ? '1 dan' : '';
        }
        return implode(' ili ', $days) . (end($days) === 1 ? ' dan' : ' dana');
    }

    public function DepartureList(): ArrayList
    {
        return ArrayList::create(array_map(fn ($c) => ArrayData::create(['Name' => $c]), $this->departureArray()));
    }

    public function departureArray(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->Departures))));
    }

    /** Prva 4 mjesta polaska za karticu. */
    public function DeparturesShort(): string
    {
        $c = $this->departureArray();
        return implode(', ', array_slice($c, 0, 4)) . (count($c) > 4 ? '…' : '');
    }

    public function DeparturesData(): string
    {
        return implode('|', $this->departureArray());
    }

    /** Mjeseci polazaka za pretragu na početnoj, npr. "2026-12|2027-01". */
    public function MonthsData(): string
    {
        return implode('|', array_unique(array_map(fn (TripTerm $t) => substr((string) $t->StartDate, 0, 7), $this->UpcomingTerms()->toArray())));
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

    /** Za karticu: plakat smanjen na 900 px širine. */
    public function CardImageURL(): string
    {
        $img = $this->Poster();
        return $img->exists() ? (string) $img->ScaleMaxWidth(900)?->getURL() : '';
    }

    public function canView($member = null): bool
    {
        return $this->Active || parent::canView($member);
    }
}
