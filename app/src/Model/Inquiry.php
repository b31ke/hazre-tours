<?php

namespace HazreTours\Model;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataObject;

/**
 * Upit ili rezervacija poslana formom sa sajta. Admin → Upiti.
 *
 * @property string $Name
 * @property string $Phone
 * @property string $Email
 * @property string $Status
 * @method Trip Trip()
 */
class Inquiry extends DataObject
{
    private static string $table_name = 'HazreInquiry';

    private static string $singular_name = 'Upit';

    private static string $plural_name = 'Upiti';

    public const STATUSES = [
        'novo' => 'Novo',
        'obrada' => 'U obradi',
        'potvrdjeno' => 'Potvrđeno',
        'otkazano' => 'Otkazano',
    ];

    private static array $db = [
        'Name' => 'Varchar(120)',
        'Phone' => 'Varchar(40)',
        'Email' => 'Varchar(150)',
        'TermLabel' => 'Varchar(80)',
        'Departure' => 'Varchar(60)',
        'Adults' => 'Int',
        'Kids' => 'Int',
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
        'Name' => 'Ime i prezime',
        'Phone' => 'Telefon',
        'TripTitle' => 'Aranžman',
        'TermLabel' => 'Termin',
        'PeopleLabel' => 'Putnika',
        'StatusLabel' => 'Status',
    ];

    private static array $searchable_fields = ['Name', 'Phone', 'Status'];

    public function getCMSFields(): FieldList
    {
        return FieldList::create(
            DropdownField::create('Status', 'Status', self::STATUSES),
            TextareaField::create('StaffNote', 'Bilješka agencije')->setRows(3)->setDescription('Vidi samo agencija.'),
            ReadonlyField::create('CreatedNice', 'Poslano', $this->obj('Created')->Nice()),
            ReadonlyField::create('Name', 'Ime i prezime'),
            ReadonlyField::create('Phone', 'Telefon'),
            ReadonlyField::create('Email', 'E-mail'),
            ReadonlyField::create('TripTitle', 'Aranžman', $this->getTripTitle()),
            ReadonlyField::create('TermLabel', 'Termin'),
            ReadonlyField::create('Departure', 'Mjesto polaska'),
            ReadonlyField::create('PeopleLabel', 'Putnika', $this->getPeopleLabel()),
            ReadonlyField::create('Message', 'Napomena'),
        );
    }

    public function getTripTitle(): string
    {
        return $this->Trip()->exists() ? $this->Trip()->Title : 'Opšti upit';
    }

    public function getPeopleLabel(): string
    {
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
