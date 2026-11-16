<?php

namespace HazreTours\Admin;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;

/**
 * Admin → Settings → kartica "Kontakt": podaci koji se vide u zaglavlju, podnožju i na kontaktu.
 */
class HazreSettings extends Extension
{
    private static array $db = [
        'CompanyName' => 'Varchar(120)',
        'Address' => 'Varchar(200)',
        'Phones' => 'Text',
        'ContactEmail' => 'Varchar(150)',
        'InquiryEmail' => 'Varchar(150)',
        'WorkingHours' => 'Varchar(150)',
        'FacebookURL' => 'Varchar(255)',
        'InstagramURL' => 'Varchar(255)',
    ];

    protected function updateCMSFields(FieldList $fields): void
    {
        $fields->addFieldsToTab('Root.Kontakt', [
            TextField::create('CompanyName', 'Naziv firme'),
            TextField::create('Address', 'Adresa'),
            TextareaField::create('Phones', 'Telefoni')->setRows(4)
                ->setDescription('Jedan broj u jednom redu. Prvi broj je glavni (dugme za poziv na telefonu).'),
            EmailField::create('ContactEmail', 'E-mail na sajtu'),
            EmailField::create('InquiryEmail', 'E-mail za upite sa sajta')
                ->setDescription('Na ovu adresu stiže svaki upit iz forme. Prazno = e-mail na sajtu.'),
            TextField::create('WorkingHours', 'Radno vrijeme'),
            TextField::create('FacebookURL', 'Facebook stranica (link)'),
            TextField::create('InstagramURL', 'Instagram (link)'),
        ]);
    }

    public function PhoneList(): ArrayList
    {
        $rows = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->getOwner()->Phones))));
        return ArrayList::create(array_map(fn ($p) => ArrayData::create([
            'Number' => $p,
            'Tel' => self::tel($p),
        ]), $rows));
    }

    public function MainPhone(): ?ArrayData
    {
        return $this->PhoneList()->first();
    }

    /** "035 644 155" → "+38735644155" */
    public static function tel(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '387')) {
            return '+' . $digits;
        }
        return '+387' . ltrim($digits, '0');
    }

    public function inquiryRecipient(): string
    {
        $o = $this->getOwner();
        return (string) ($o->InquiryEmail ?: $o->ContactEmail);
    }
}
