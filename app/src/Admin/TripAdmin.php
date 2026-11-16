<?php

namespace HazreTours\Admin;

use HazreTours\Model\FaqItem;
use HazreTours\Model\Inquiry;
use HazreTours\Model\Review;
use HazreTours\Model\Subscriber;
use HazreTours\Model\Trip;
use HazreTours\Model\TripCategory;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldImportButton;
use SilverStripe\Forms\GridField\GridFieldPrintButton;

/**
 * Admin → Aranžmani i upiti: sve što agencija svakodnevno mijenja.
 */
class TripAdmin extends ModelAdmin
{
    private static string $url_segment = 'aranzmani';

    private static string $menu_title = 'Aranžmani i upiti';

    private static string $menu_icon_class = 'font-icon-p-map';

    private static int $menu_priority = 10;

    private static array $managed_models = [
        Trip::class => ['title' => 'Aranžmani'],
        Inquiry::class => ['title' => 'Upiti'],
        TripCategory::class => ['title' => 'Kategorije'],
        FaqItem::class => ['title' => 'Česta pitanja'],
        Review::class => ['title' => 'Utisci putnika'],
        Subscriber::class => ['title' => 'Newsletter'],
    ];

    public $showImportForm = false;

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();
        $config->removeComponentsByType([GridFieldImportButton::class, GridFieldPrintButton::class]);
        // Izvoz u Excel/CSV samo za upite i newsletter
        if (!in_array($this->modelClass, [Inquiry::class, Subscriber::class], true)) {
            $config->removeComponentsByType(GridFieldExportButton::class);
        }
        return $config;
    }

    public function getList(): \SilverStripe\ORM\DataList
    {
        $list = parent::getList();
        if ($this->modelClass === Trip::class) {
            $list = $list->sort(['Active' => 'DESC', 'Title' => 'ASC']);
        }
        return $list;
    }
}
