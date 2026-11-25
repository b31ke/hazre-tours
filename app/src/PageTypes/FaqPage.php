<?php

namespace HazreTours\PageTypes;

use HazreTours\Model\FaqItem;
use Page;
use SilverStripe\ORM\DataList;
use SilverStripe\Versioned\Versioned;

/**
 * Česta pitanja: sva opšta pitanja (ona koja nisu vezana za aranžman).
 * Pitanja se uređuju u adminu (Aranžmani i upiti → Česta pitanja).
 */
class FaqPage extends Page
{
    private static string $table_name = 'HazreFaqPage';

    private static string $singular_name = 'Česta pitanja';

    private static string $description = 'Stranica sa svim opštim pitanjima i odgovorima';

    private static string $icon_class = 'font-icon-help-circled';

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
        if (static::class !== self::class || self::get()->exists()) {
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
