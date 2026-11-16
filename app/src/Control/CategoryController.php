<?php

namespace HazreTours\Control;

use HazreTours\Model\Trip;
use HazreTours\Model\TripCategory;
use PageController;
use SilverStripe\Control\HTTPRequest;

/**
 * Stranica kategorije: /putovanja/<naziv> (Advent, Nova godina, Zimovanje…)
 * i /putovanja (svi aktivni aranžmani).
 */
class CategoryController extends PageController
{
    private static $allowed_actions = ['index'];

    private static $url_handlers = [
        '$Slug' => 'index',
    ];

    protected ?TripCategory $category = null;

    public function index(HTTPRequest $request)
    {
        $slug = (string) $request->param('Slug');
        if ($slug !== '') {
            $this->category = TripCategory::get()->filter('URLSegment', $slug)->first();
            if (!$this->category) {
                return $this->httpError(404, 'Kategorija nije pronađena.');
            }
        }
        $c = $this->category;
        return $this->customise([
            'Title' => $c ? $c->Title : 'Sva putovanja',
            'MetaTitle' => ($c ? $c->Title . ' – putovanja' : 'Sva putovanja') . ' | Hazre Tours',
            'MetaDescriptionText' => $c?->Intro ?: 'Izleti, putovanja i ljetovanja s polaskom iz Tuzle, Srebrenika, Gračanice, Gradačca, Brčkog i Orašja.',
            'Intro' => $c?->Intro ?: 'Svi aranžmani s narednim polaskom, od najbližeg.',
            'Category' => $c,
            'Trips' => $c ? $c->LiveTrips() : Trip::liveList(),
        ])->renderWith(['CategoryPage', 'Page']);
    }

    public function Link($action = null): string
    {
        return $this->category ? $this->category->Link() : '/putovanja';
    }
}
