<?php

namespace HazreTours\Model;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataObject;

/**
 * Upit sa sajta: rezervacija, putovanje po mjeri, izmjena aranžmana ili najam autobusa. Admin → Upiti.
 *
 * @property string $Name
 * @property string $Phone
 * @property string $Email
 * @property string $Type
 * @property string $Status
 * @method Trip Trip()
 */
class Inquiry extends DataObject
{
    private static string $table_name = 'HazreInquiry';

    private static string $singular_name = 'Upit';

    private static string $plural_name = 'Upiti';

    public const TYPES = [
        'rezervacija' => 'Rezervacija',
        'pomjeri' => 'Putovanje po mjeri',
        'izmjena' => 'Izmjena aranžmana',
        'autobus' => 'Najam autobusa / ekskurzija',
    ];

    public const STATUSES = [
        'novo' => 'Novo',
        'obrada' => 'U obradi',
        'potvrdjeno' => 'Potvrđeno',
        'otkazano' => 'Otkazano',
    ];

    private static array $db = [
        'Type' => "Enum('rezervacija,pomjeri,izmjena,autobus','rezervacija')",
        'Name' => 'Varchar(120)',
        'Phone' => 'Varchar(40)',
        'Email' => 'Varchar(150)',
        'TermLabel' => 'Varchar(80)',
        'Departure' => 'Varchar(60)',
        'Adults' => 'Int',
        'Kids' => 'Int',
        'KidsAges' => 'Varchar(80)',
        'Destination' => 'Varchar(150)',
        'Period' => 'Varchar(150)',
        'Duration' => 'Varchar(40)',
        'Budget' => 'Varchar(60)',
        'AccommodationWish' => 'Varchar(60)',
        'Changes' => 'Varchar(255)',
        'Message' => 'Text',
        'Status' => "Enum('novo,obrada,potvrdjeno,otkazano','novo')",
        'StaffNote' => 'Text',
    ];

    private static array $has_one = [
        'Trip' => Trip::class,
    ];

    private static string $default_sort = 'Created DESC';

    private static array $summary_fields = [
        'Created.Nice' => 'Poslano',
        'TypeLabel' => 'Vrsta',
        'Name' => 'Ime i prezime',
        'Phone' => 'Telefon',
        'TripTitle' => 'Aranžman / destinacija',
        'TermLabel' => 'Termin',
        'PeopleLabel' => 'Putnika',
        'StatusLabel' => 'Status',
    ];

    private static array $searchable_fields = ['Name', 'Phone', 'Type', 'Status'];

    public function getCMSFields(): FieldList
    {
        $fields = FieldList::create(
            DropdownField::create('Status', 'Status', self::STATUSES),
            TextareaField::create('StaffNote', 'Bilješka agencije')->setRows(3)->setDescription('Vidi samo agencija.'),
            ReadonlyField::create('CreatedNice', 'Poslano', $this->obj('Created')->Nice()),
            ReadonlyField::create('TypeLabel', 'Vrsta upita', $this->getTypeLabel()),
        );
        foreach ($this->rows() as $label => $value) {
            $fields->push(ReadonlyField::create('R' . md5($label), $label, $value));
        }
        return $fields;
    }

    /** Popunjena polja za prikaz u adminu i u mailu. */
    public function rows(): array
    {
        $rows = [
            'Aranžman' => $this->Trip()->exists() ? $this->Trip()->Title : '',
            'Termin' => $this->TermLabel,
            'Destinacija' => $this->Destination,
            'Period' => $this->Period,
            'Trajanje' => $this->Duration,
            'Mjesto polaska' => $this->Departure,
            'Putnika' => $this->getPeopleLabel(),
            'Uzrast djece' => $this->KidsAges,
            'Budžet po osobi' => $this->Budget,
            'Smještaj' => $this->AccommodationWish,
            'Šta bi promijenili' => $this->Changes,
            'Ime i prezime' => $this->Name,
            'Telefon' => $this->Phone,
            'E-mail' => $this->Email,
            'Poruka' => $this->Message,
        ];
        return array_filter($rows, fn ($v) => $v !== '' && $v !== null);
    }

    public function getTypeLabel(): string
    {
        return self::TYPES[$this->Type] ?? '';
    }

    public function getTripTitle(): string
    {
        if ($this->Trip()->exists()) {
            return $this->Trip()->Title;
        }
        return $this->Destination ?: 'Opšti upit';
    }

    public function getPeopleLabel(): string
    {
        if (!$this->Adults && !$this->Kids) {
            return '';
        }
        return $this->Adults . ' odr.' . ($this->Kids ? ' + ' . $this->Kids . ' djece' : '');
    }

    public function getStatusLabel(): string
    {
        return self::STATUSES[$this->Status] ?? '';
    }

    public function getTitle(): string
    {
        return $this->Name . ' – ' . $this->getTripTitle();
    }

    public function canCreate($member = null, $context = []): bool
    {
        return false;
    }
}
