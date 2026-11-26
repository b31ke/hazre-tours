<?php

namespace HazreTours\Admin;

use HazreTours\Model\TravelerStatement;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldImportButton;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Security\Permission;

/**
 * Admin → Izjave putnika: izjave o prihvatanju Opštih uslova putovanja za putnike koji su već rezervisali.
 * Pristup ima glavni admin i grupa Urednici aranžmana; izvoz u CSV samo glavni admin.
 */
class StatementAdmin extends ModelAdmin
{
    private static string $url_segment = 'izjave';

    private static string $menu_title = 'Izjave putnika';

    private static string $menu_icon_class = 'font-icon-checklist';

    private static int $menu_priority = 9;

    private static array $managed_models = [
        TravelerStatement::class => ['title' => 'Izjave putnika'],
    ];

    public $showImportForm = false;

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();
        $config->removeComponentsByType([GridFieldImportButton::class, GridFieldPrintButton::class]);
        if (!Permission::check('ADMIN')) {
            $config->removeComponentsByType(\SilverStripe\Forms\GridField\GridFieldExportButton::class);
        }
        return $config;
    }
}
