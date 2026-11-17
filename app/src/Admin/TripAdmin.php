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

    /** Na kartici Aranžmani: pozdrav i brojke (aktivna putovanja, novi upiti, polasci ove sedmice, pretplatnici). */
    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);
        if ($this->modelClass === Trip::class && $form) {
            $form->Fields()->unshift(\SilverStripe\Forms\LiteralField::create('HazreDashboard', $this->dashboardHTML()));
        }
        return $form;
    }

    private function dashboardHTML(): string
    {
        $h = (int) date('G');
        $hello = $h < 11 ? 'Dobro jutro' : ($h < 18 ? 'Dobar dan' : 'Dobro veče');
        $member = \SilverStripe\Security\Security::getCurrentUser();
        $name = $member && $member->FirstName && $member->FirstName !== 'Default Admin' ? ', ' . $member->FirstName : '';
        $today = date('Y-m-d');
        $week = date('Y-m-d', strtotime('+7 days'));
        $live = Trip::liveList()->count();
        $new = Inquiry::get()->filter('Status', 'novo')->count();
        $soon = \HazreTours\Model\TripTerm::get()->filter(['StartDate:GreaterThanOrEqual' => $today, 'StartDate:LessThanOrEqual' => $week, 'Trip.Active' => true])->count();
        $subs = Subscriber::get()->count();
        $next = \HazreTours\Model\TripTerm::get()->filter(['StartDate:GreaterThanOrEqual' => $today, 'Trip.Active' => true])->sort('StartDate')->first();
        $base = $this->Link();
        $tab = fn (string $cls) => $base . '/' . str_replace('\\', '-', $cls);
        $e = fn ($s) => htmlspecialchars((string) $s, ENT_QUOTES);

        $card = fn (string $cls, string $icon, int|string $num, string $label, string $href) =>
            '<a class="hz-stat ' . $cls . '" href="' . $e($href) . '"><span class="hz-stat__ico font-icon-' . $icon . '" aria-hidden="true"></span>'
            . '<b>' . $e($num) . '</b><span>' . $e($label) . '</span></a>';

        $nextHtml = '';
        if ($next && $next->Trip()->exists()) {
            $nextHtml = '<div class="hz-next"><span class="hz-next__dot"></span><span>Sljedeći polazak</span><b>'
                . $e($next->obj('StartDate')->Format('dd.MM.')) . '</b>' . $e($next->Trip()->Title)
                . '<em>' . $e($next->Countdown()) . '</em></div>';
        }

        return '<section class="hz-dash">'
            . '<div class="hz-dash__head"><div><p class="hz-dash__kicker">' . $e($hello . $name) . '</p>'
            . '<h2 class="hz-dash__title">Šta je novo u agenciji</h2></div>' . $nextHtml . '</div>'
            . '<div class="hz-stats">'
            . $card('is-royal', 'p-map', $live, 'aktivnih putovanja', $tab(Trip::class))
            . $card('is-orange', 'chat', $new, $new === 1 ? 'novi upit' : 'novih upita', $tab(Inquiry::class))
            . $card('is-blue', 'clock', $soon, 'polazaka u 7 dana', \SilverStripe\Control\Director::baseURL() . 'kalendar')
            . $card('is-green', 'p-mail', $subs, 'pretplatnika', $tab(Subscriber::class))
            . '</div></section>';
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
