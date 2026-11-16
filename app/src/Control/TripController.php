<?php

namespace HazreTours\Control;

use HazreTours\Model\Trip;
use PageController;
use SilverStripe\Control\HTTPRequest;

/**
 * Stranica jednog aranžmana: /aranzman/<naziv>
 */
class TripController extends PageController
{
    private static $allowed_actions = ['index'];

    private static $url_handlers = [
        '$Slug' => 'index',
    ];

    protected ?Trip $trip = null;

    public function index(HTTPRequest $request)
    {
        $slug = (string) $request->param('Slug');
        $trip = $slug ? Trip::get()->filter('URLSegment', $slug)->first() : null;
        if (!$trip || !$trip->canView()) {
            return $this->httpError(404, 'Aranžman nije pronađen.');
        }
        $this->trip = $trip;
        return $this->customise([
            'Trip' => $trip,
            'Title' => $trip->Title,
            'MetaTitle' => $trip->Title . ' | Hazre Tours',
            'MetaDescriptionText' => $trip->Summary ?: $trip->Highlight,
            'OgImage' => $trip->Poster()->exists() ? $trip->Poster()->ScaleMaxWidth(1200)?->getAbsoluteURL() : '',
        ])->renderWith(['TripPage', 'Page']);
    }

    public function Link($action = null): string
    {
        return $this->trip ? $this->trip->Link() : '/';
    }

    /** Ostali aranžmani na dnu stranice (najbliži polasci, bez ovog). */
    public function OtherTrips()
    {
        $list = $this->FormTrips();
        if ($this->trip) {
            $list = $list->exclude('ID', $this->trip->ID);
        }
        return $list->limit(3);
    }
}
