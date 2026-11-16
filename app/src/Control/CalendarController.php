<?php

namespace HazreTours\Control;

use HazreTours\Model\TripTerm;
use PageController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;

/**
 * Kalendar polazaka: /kalendar. Svi budući termini po mjesecima, kao ploča polazaka.
 */
class CalendarController extends PageController
{
    private static $allowed_actions = ['index'];

    private const MONTHS = ['Januar', 'Februar', 'Mart', 'April', 'Maj', 'Juni', 'Juli', 'August', 'Septembar', 'Oktobar', 'Novembar', 'Decembar'];

    public function index(HTTPRequest $request)
    {
        return $this->customise([
            'Title' => 'Kalendar polazaka',
            'MetaTitle' => 'Kalendar polazaka | Hazre Tours',
            'MetaDescriptionText' => 'Svi polasci Hazre Toursa po mjesecima: datum, trajanje, cijena i slobodna mjesta.',
            'Intro' => 'Svi naredni polasci po mjesecima. Kliknite na putovanje za program i rezervaciju.',
        ])->renderWith(['CalendarPage', 'Page']);
    }

    public function Link($action = null): string
    {
        return '/kalendar';
    }

    public function Months(): ArrayList
    {
        $groups = [];
        $terms = TripTerm::get()->filter(['StartDate:GreaterThanOrEqual' => date('Y-m-d'), 'Trip.Active' => true])->sort('StartDate');
        foreach ($terms as $term) {
            $key = substr((string) $term->StartDate, 0, 7);
            $groups[$key][] = $term;
        }
        $out = [];
        foreach ($groups as $key => $list) {
            $out[] = ArrayData::create([
                'Label' => self::MONTHS[(int) substr($key, 5, 2) - 1] . ' ' . substr($key, 0, 4) . '.',
                'Anchor' => 'm' . str_replace('-', '', $key),
                'Terms' => ArrayList::create($list),
            ]);
        }
        return ArrayList::create($out);
    }
}
