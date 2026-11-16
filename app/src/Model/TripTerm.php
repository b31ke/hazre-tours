<?php

namespace HazreTours\Model;

use SilverStripe\Forms\DateField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;

/**
 * Jedan polazak aranžmana: datum polaska i povratka, cijena (ako se razlikuje) i popunjenost.
 *
 * @property string $StartDate
 * @property string $EndDate
 * @property int $Price
 * @property string $Status
 * @method Trip Trip()
 */
class TripTerm extends DataObject
{
    private static string $table_name = 'HazreTripTerm';

    private static string $singular_name = 'Termin';

    private static string $plural_name = 'Termini';

    public const STATUSES = [
        'slobodno' => 'Slobodna mjesta',
        'zadnja' => 'Zadnja mjesta',
        'popunjeno' => 'Popunjena grupa',
    ];

    private static array $db = [
        'StartDate' => 'Date',
        'EndDate' => 'Date',
        'Price' => 'Int',
        'Status' => "Enum('slobodno,zadnja,popunjeno','slobodno')",
        'Note' => 'Varchar(150)',
    ];

    private static array $has_one = [
        'Trip' => Trip::class,
    ];

    private static string $default_sort = 'StartDate ASC';

    private static array $summary_fields = [
        'Label' => 'Termin',
        'DaysLabel' => 'Trajanje',
        'EffectivePriceLabel' => 'Cijena',
        'StatusLabel' => 'Popunjenost',
        'Note' => 'Napomena',
    ];

    public function getCMSFields(): FieldList
    {
        return FieldList::create(
            DateField::create('StartDate', 'Datum polaska'),
            DateField::create('EndDate', 'Datum povratka')->setDescription('Prazno za jednodnevni izlet.'),
            NumericField::create('Price', 'Cijena za ovaj termin (KM)')->setDescription('Prazno ili 0 = cijena iz aranžmana.'),
            DropdownField::create('Status', 'Popunjenost', self::STATUSES),
            TextField::create('Note', 'Napomena')->setDescription('Nije obavezno, npr. "Nova godina na brodu".'),
        );
    }

    public function validate(): \SilverStripe\Core\Validation\ValidationResult
    {
        $result = parent::validate();
        if (!$this->StartDate) {
            $result->addFieldError('StartDate', 'Upišite datum polaska.');
        } elseif ($this->EndDate && $this->EndDate < $this->StartDate) {
            $result->addFieldError('EndDate', 'Povratak ne može biti prije polaska.');
        }
        return $result;
    }

    public function Days(): int
    {
        if (!$this->StartDate || !$this->EndDate) {
            return 1;
        }
        return (int) (new \DateTime($this->StartDate))->diff(new \DateTime($this->EndDate))->days + 1;
    }

    public function getDaysLabel(): string
    {
        $d = $this->Days();
        return $d . ($d === 1 ? ' dan' : ' dana');
    }

    /** "28.11.2026." ili "12.12. – 17.12.2026." */
    public function Label(): string
    {
        if (!$this->StartDate) {
            return '';
        }
        $start = $this->obj('StartDate');
        if (!$this->EndDate || $this->EndDate === $this->StartDate) {
            return $start->Format('dd.MM.y.');
        }
        return $start->Format('dd.MM.') . ' – ' . $this->obj('EndDate')->Format('dd.MM.y.');
    }

    public function getLabel(): string
    {
        return $this->Label();
    }

    public function EffectivePrice(): int
    {
        return (int) ($this->Price ?: $this->Trip()->Price);
    }

    public function getEffectivePriceLabel(): string
    {
        return $this->EffectivePrice() . ' KM';
    }

    public function getStatusLabel(): string
    {
        return self::STATUSES[$this->Status] ?? '';
    }

    public function IsFull(): bool
    {
        return $this->Status === 'popunjeno';
    }

    /** Broj dana do polaska ("za 5 dana", "sutra"). */
    public function Countdown(): string
    {
        $n = (int) (new \DateTime('today'))->diff(new \DateTime($this->StartDate))->format('%r%a');
        return match (true) {
            $n <= 0 => 'danas',
            $n === 1 => 'sutra',
            default => 'za ' . $n . ' dana',
        };
    }

    public function getTitle(): string
    {
        return $this->Label();
    }
}
