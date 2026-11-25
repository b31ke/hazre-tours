<?php

namespace HazreTours\PageTypes;

use HazreTours\Model\FaqItem;
use Page;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataList;
use SilverStripe\Versioned\Versioned;

/**
 * Česta pitanja: sva opšta pitanja (ona koja nisu vezana za aranžman).
 * Sve se uređuje u adminu: Stranice → Česta pitanja (tekstovi lijevo i kartica "Pitanja i odgovori"),
 * a pitanja i u Aranžmani i upiti → Česta pitanja.
 */
class FaqPage extends Page
{
    private static string $table_name = 'HazreFaqPage';

    private static string $singular_name = 'Česta pitanja';

    private static string $description = 'Stranica sa svim opštim pitanjima i odgovorima';

    private static string $icon_class = 'font-icon-help-circled';

    private static array $db = [
        'HeroKicker' => 'Varchar(120)',
        'SideKicker' => 'Varchar(120)',
        'SideTitle' => 'Varchar(120)',
        'HelpLabel' => 'Varchar(120)',
    ];

    private static array $defaults = [
        'HeroKicker' => 'Imate pitanje? Imamo odgovor',
        'SideKicker' => 'Imate pitanje?',
        'SideTitle' => 'Česta pitanja',
        'HelpLabel' => 'Niste našli odgovor? Pozovite',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab('Root.Main', [
            HeaderField::create('FaqTextsH', 'Tekstovi na stranici', 3),
            TextField::create('HeroKicker', 'Natpis iznad naslova (u zaglavlju)'),
            TextField::create('SideKicker', 'Mali natpis lijevo (rukopis)'),
            TextField::create('SideTitle', 'Naslov lijevo'),
            TextField::create('HelpLabel', 'Tekst na dugmetu za poziv')->setDescription('Broj telefona se uzima iz Postavke → Kontakt.'),
        ], 'Content');
        $fields->dataFieldByName('Content')?->setTitle('Tekst lijevo, ispod naslova')
            ->setDescription('Ako je prazno, piše: "Prijava, osiguranje, sjedišta i dokumenti. Sve ostalo je u opštim uslovima putovanja."');
        $fields->addFieldToTab('Root.Pitanja', GridField::create(
            'FaqItems',
            'Pitanja i odgovori',
            $this->GeneralFaqs(),
            GridFieldConfig_RecordEditor::create(30)
        )->setDescription('Redoslijed se mijenja poljem "Redoslijed" u pitanju (manji broj ide prije).'));
        $fields->fieldByName('Root.Pitanja')?->setTitle('Pitanja i odgovori');
        return $fields;
    }

    /** Za meni: ova stranica ide iza Kontakta. */
    public function IsFaqPage(): bool
    {
        return true;
    }

    /** @return DataList<FaqItem> */
    public function GeneralFaqs(): DataList
    {
        return FaqItem::get()->filter('TripID', 0);
    }

    /** Ako stranica ne postoji, napravi je i objavi, odmah iza zadnje stranice u meniju (pored Kontakta). */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        if (static::class !== self::class) {
            return;
        }
        $page = self::get()->first();
        if ($page) {
            // stranica napravljena prije ovih polja: upiši zadane tekstove ako su prazni
            if (!$page->SideTitle) {
                foreach (self::config()->get('defaults') as $k => $v) {
                    $page->$k = $page->$k ?: $v;
                }
                $page->write();
                if ($page->isPublished()) {
                    $page->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
                }
            }
            return;
        }
        $last = Page::get()->filter(['ParentID' => 0, 'ShowInMenus' => true])->sort('Sort', 'DESC')->first();
        $page = self::create();
        $page->Title = 'Česta pitanja';
        $page->URLSegment = 'cesta-pitanja';
        $page->ShowInMenus = true;
        $page->Sort = $last ? $last->Sort + 1 : 10;
        $page->write();
        $page->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
    }
}
