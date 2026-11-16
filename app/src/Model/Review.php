<?php

namespace HazreTours\Model;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;

/**
 * Utisak putnika. Unosi ga agencija (npr. prepisan s Facebooka ili Googlea uz dozvolu putnika).
 * Sekcija se na sajtu ne vidi dok nema bar jednog objavljenog utiska.
 *
 * @method Trip Trip()
 */
class Review extends DataObject
{
    private static string $table_name = 'HazreReview';

    private static string $singular_name = 'Utisak putnika';

    private static string $plural_name = 'Utisci putnika';

    private static array $db = [
        'Name' => 'Varchar(80)',
        'City' => 'Varchar(80)',
        'Text' => 'Text',
        'Rating' => 'Int',
        'Source' => 'Varchar(40)',
        'Published' => 'Boolean(1)',
    ];

    private static array $has_one = [
        'Trip' => Trip::class,
    ];

    private static array $defaults = ['Rating' => 5, 'Published' => true];

    private static string $default_sort = 'Created DESC';

    private static array $summary_fields = [
        'Name' => 'Ime',
        'Stars' => 'Ocjena',
        'TripTitle' => 'Putovanje',
        'Published.Nice' => 'Objavljeno',
    ];

    public function getCMSFields(): FieldList
    {
        return FieldList::create(
            TextField::create('Name', 'Ime (npr. "Amra H.")'),
            TextField::create('City', 'Grad'),
            TextareaField::create('Text', 'Utisak')->setRows(5),
            DropdownField::create('Rating', 'Ocjena', [5 => '★★★★★', 4 => '★★★★', 3 => '★★★']),
            DropdownField::create('TripID', 'Putovanje', Trip::get()->sort('Title')->map('ID', 'Title')->toArray())->setEmptyString('—'),
            TextField::create('Source', 'Izvor')->setDescription('Npr. Google, Facebook, lično.'),
            CheckboxField::create('Published', 'Prikaži na sajtu'),
        );
    }

    public function getStars(): string
    {
        return str_repeat('★', max(1, min(5, (int) $this->Rating)));
    }

    public function getTripTitle(): string
    {
        return $this->Trip()->exists() ? $this->Trip()->Title : '';
    }

    public function getTitle(): string
    {
        return (string) $this->Name;
    }

    public function Initial(): string
    {
        return mb_strtoupper(mb_substr((string) $this->Name, 0, 1));
    }
}
