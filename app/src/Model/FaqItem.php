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
            TextareaField::create('Answer', 'Odgovor')->setRows(8)
                ->setDescription('Svaki red je novi pasus. Red koji počinje s "• " ili "- " postaje stavka liste; "Naziv: vrijednost" (npr. "Do 45 dana prije polaska: 5%") se prikaže kao red s istaknutom vrijednošću desno.'),
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

    /**
     * Odgovor u preglednom obliku: svaki red je pasus, redovi koji počinju s "•", "-" ili "*" postaju lista,
     * a stavka "naziv: vrijednost" se prikazuje kao red sa istaknutom vrijednošću desno.
     * Procenti, broj dana i iznosi u KM se podebljaju.
     */
    public function AnswerHTML(): \SilverStripe\ORM\FieldType\DBHTMLText
    {
        $html = '';
        $list = [];
        $flush = function () use (&$html, &$list) {
            if ($list) {
                $html .= '<ul class="faq-list">' . implode('', $list) . '</ul>';
                $list = [];
            }
        };
        foreach (preg_split('/\R/u', trim((string) $this->Answer)) as $line) {
            $line = trim($line);
            if ($line === '') {
                $flush();
                continue;
            }
            if (preg_match('/^[•\-\*–]\s*(.+)$/u', $line, $m)) {
                $item = $m[1];
                if (preg_match('/^(.{2,80}?):\s+(.{1,40})$/u', $item, $kv)) {
                    $list[] = '<li class="kv"><span>' . htmlspecialchars($kv[1]) . '</span><b>' . htmlspecialchars($kv[2]) . '</b></li>';
                } else {
                    $list[] = '<li>' . self::emph($item) . '</li>';
                }
                continue;
            }
            $flush();
            $html .= '<p>' . self::emph($line) . '</p>';
        }
        $flush();
        return \SilverStripe\ORM\FieldType\DBField::create_field('HTMLFragment', $html);
    }

    private static function emph(string $text): string
    {
        $t = htmlspecialchars($text);
        return preg_replace('/(\d+(?:[.,]\d+)?\s?%|\d+\s(?:dan|dana|sat|sati|KM)\b)/u', '<strong>$1</strong>', $t);
    }
}
