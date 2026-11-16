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
            'OgImage' => $trip->Poster()->exists() ? $trip->Poster()->FillMax(1200, 630)?->getAbsoluteURL() : '',
        ])->renderWith(['TripPage', 'Page']);
    }

    public function Link($action = null): string
    {
        return $this->trip ? $this->trip->Link() : '/';
    }

    /** Ostali aranžmani na dnu stranice (najbliži polasci, bez ovog). */
    /** Ostali aranžmani: prvo iz iste kategorije, pa najbliži polasci. */
    public function OtherTrips()
    {
        $all = $this->FormTrips()->exclude('ID', $this->trip?->ID ?? 0)->toArray();
        $cats = $this->trip ? $this->trip->Categories()->column('ID') : [];
        usort($all, fn ($a, $b) => (int) (bool) array_intersect($b->Categories()->column('ID'), $cats) <=> (int) (bool) array_intersect($a->Categories()->column('ID'), $cats));
        return \SilverStripe\Model\List\ArrayList::create(array_slice($all, 0, 3));
    }

    public function ShareText(): string
    {
        return rawurlencode(($this->trip?->Title ?? '') . ' – ' . ($this->trip?->getPriceLabel() ?? '') . ' ' . ($this->trip?->AbsoluteLink() ?? ''));
    }

    public function ShareURL(): string
    {
        return rawurlencode($this->trip?->AbsoluteLink() ?? '');
    }
}
