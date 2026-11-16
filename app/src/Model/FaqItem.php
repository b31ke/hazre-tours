<?php

namespace HazreTours\Model;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;

/**
 * Često pitanje. Bez aranžmana = opšte (početna i svaki aranžman), s aranžmanom = samo za taj aranžman.
 *
 * @property string $Question
 * @property string $Answer
 * @method Trip Trip()
 */
class FaqItem extends DataObject
{
    private static string $table_name = 'HazreFaqItem';

    private static string $singular_name = 'Pitanje';

    private static string $plural_name = 'Česta pitanja';

    private static array $db = [
        'Question' => 'Varchar(255)',
        'Answer' => 'Text',
        'Sort' => 'Int',
    ];

    private static array $has_one = [
        'Trip' => Trip::class,
    ];

    private static string $default_sort = 'Sort ASC, ID ASC';

    private static array $summary_fields = [
        'Question' => 'Pitanje',
        'Scope' => 'Gdje se vidi',
        'Sort' => 'Redoslijed',
    ];

    public function getCMSFields(): FieldList
    {
        $trips = Trip::get()->sort('Title')->map('ID', 'Title')->toArray();
        return FieldList::create(
            TextField::create('Question', 'Pitanje'),
            TextareaField::create('Answer', 'Odgovor')->setRows(5),
            DropdownField::create('TripID', 'Samo za aranžman', $trips)->setEmptyString('Opšte pitanje (svuda)'),
            NumericField::create('Sort', 'Redoslijed'),
        );
    }

    public function getScope(): string
    {
        return $this->TripID ? $this->Trip()->Title : 'Svuda';
    }

    public function getTitle(): string
    {
        return (string) $this->Question;
    }

    public function AnswerHTML(): \SilverStripe\ORM\FieldType\DBHTMLText
    {
        return \SilverStripe\ORM\FieldType\DBField::create_field('HTMLFragment', nl2br(htmlspecialchars((string) $this->Answer)));
    }
}
