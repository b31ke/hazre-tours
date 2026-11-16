<?php

namespace HazreTours\Admin;

use HazreTours\Model\Inquiry;
use HazreTours\Model\Trip;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldImportButton;
use SilverStripe\Forms\GridField\GridFieldPrintButton;

/**
 * Admin → Aranžmani: aranžmani s terminima i upiti sa sajta.
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
    ];

    public $showImportForm = false;

    protected function getGridFieldConfig(): \SilverStripe\Forms\GridField\GridFieldConfig
    {
        $config = parent::getGridFieldConfig();
        $config->removeComponentsByType([GridFieldImportButton::class, GridFieldPrintButton::class]);
        if ($this->modelClass !== Inquiry::class) {
            $config->removeComponentsByType(GridFieldExportButton::class);
        }
        return $config;
    }

    public function getList(): \SilverStripe\ORM\DataList
    {
        $list = parent::getList();
        if ($this->modelClass === Trip::class) {
            // Aktivni prvo, pa po nazivu
            $list = $list->sort(['Active' => 'DESC', 'Title' => 'ASC']);
        }
        return $list;
    }
}
