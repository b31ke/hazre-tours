<?php

namespace {

    use HazreTours\Model\Trip;
    use SilverStripe\CMS\Controllers\ContentController;
    use SilverStripe\Model\List\ArrayList;
    use SilverStripe\Security\SecurityToken;
    use SilverStripe\View\Requirements;

    /**
     * Zajednička osnova svih javnih stranica (CMS stranice, aranžman).
     *
     * @template T of Page
     * @extends ContentController<T>
     */
    class PageController extends ContentController
    {
        private static $allowed_actions = [];

        protected function init()
        {
            parent::init();
            Requirements::css('css/hazre.css');
            Requirements::javascript('js/hazre.js', ['defer' => true]);
        }

        /** Aranžmani u formi za upit (aktivni s budućim terminom). */
        public function FormTrips(): ArrayList
        {
            $trips = array_filter(Trip::get()->filter('Active', true)->toArray(), fn (Trip $t) => $t->NextTerm() !== null);
            usort($trips, fn (Trip $a, Trip $b) => strcmp($a->NextTerm()->StartDate, $b->NextTerm()->StartDate));
            return ArrayList::create(array_values($trips));
        }

        /** Termini svih aranžmana za formu: {"id": ["28.11.2026. · 79 KM", ...]} */
        public function FormTermsJSON(): string
        {
            $out = [];
            foreach ($this->FormTrips() as $trip) {
                foreach ($trip->UpcomingTerms() as $term) {
                    if (!$term->IsFull()) {
                        $out[$trip->ID][] = $term->Label() . ' · ' . $term->EffectivePrice() . ' KM';
                    }
                }
            }
            return json_encode($out, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
        }

        public function InquiryToken(): string
        {
            return SecurityToken::inst()->getValue();
        }

        public function InquiryTokenName(): string
        {
            return SecurityToken::inst()->getName();
        }

        /** Upit je poslan bez JavaScripta (preusmjerenje s ?upit=poslan). */
        public function InquirySent(): bool
        {
            return $this->getRequest()->getVar('upit') === 'poslan';
        }

        public function Indexable(): bool
        {
            return \HazreTours\Control\SeoController::indexable();
        }

        public function CurrentYear(): string
        {
            return date('Y');
        }
    }
}
