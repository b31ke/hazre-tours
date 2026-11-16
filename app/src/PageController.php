<?php

namespace {

    use HazreTours\Control\SeoController;
    use HazreTours\Model\Trip;
    use HazreTours\Model\TripCategory;
    use SilverStripe\CMS\Controllers\ContentController;
    use SilverStripe\Control\Director;
    use SilverStripe\Model\List\ArrayList;
    use SilverStripe\ORM\FieldType\DBField;
    use SilverStripe\Security\SecurityToken;
    use SilverStripe\View\Requirements;

    /**
     * Zajednička osnova svih javnih stranica (CMS stranice, aranžman, kategorija, kalendar).
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

        /** Najbliži polazak za gornju traku (na svim stranicama). */
        public function TopbarTrip(): ?Trip
        {
            return Trip::liveList()->first();
        }

        /** Aranžmani u formi i pretrazi (aktivni s budućim terminom). */
        public function FormTrips(): ArrayList
        {
            return Trip::liveList();
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

        /** Pretraga u zaglavlju: svi aktivni aranžmani (naziv, slika, cijena, link). */
        public function SearchJSON(): string
        {
            $rows = array_map(fn (Trip $t) => $t->toPublicArray(), $this->FormTrips()->toArray());
            return json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
        }

        private ?ArrayList $menuCategories = null;

        /** Kategorije s bar jednim polaskom (meni, podnožje), izračunate jednom po stranici. */
        public function MenuCategories()
        {
            return $this->menuCategories ??= ArrayList::create(array_values(array_filter(TripCategory::get()->toArray(), fn ($c) => $c->getTripCount() > 0)));
        }

        public function InquiryToken(): string
        {
            return SecurityToken::inst()->getValue();
        }

        public function InquiryTokenName(): string
        {
            return SecurityToken::inst()->getName();
        }

        public function InquirySent(): bool
        {
            return $this->getRequest()->getVar('upit') === 'poslan';
        }

        /** Klasa na <body>: npr. HomePageController, TripController (za CSS). */
        public function BodyClass(): string
        {
            return (new \ReflectionClass($this))->getShortName();
        }

        public function Indexable(): bool
        {
            return SeoController::indexable();
        }

        public function CurrentYear(): string
        {
            return date('Y');
        }

        public function CurrentURL(): string
        {
            return Director::absoluteURL($this->getRequest()->getURL() === '' ? '/' : '/' . $this->getRequest()->getURL());
        }

        /** Agencija za Google (schema.org TravelAgency) na svakoj stranici. */
        public function AgencyJsonLD(): DBField
        {
            $c = $this->SiteConfig();
            $data = [
                '@context' => 'https://schema.org',
                '@type' => 'TravelAgency',
                'name' => $c->Title ?: 'Hazre Tours',
                'url' => Director::absoluteBaseURL(),
                'logo' => Director::absoluteURL('images/hazre-logo.png'),
                'image' => Director::absoluteURL('images/hazre-logo.png'),
                'telephone' => ($p = $c->MainPhone()) ? $p->Tel : null,
                'email' => $c->ContactEmail ?: null,
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => preg_replace('/,?\s*\d{5}.*$/', '', (string) $c->Address),
                    'postalCode' => '75350',
                    'addressLocality' => 'Srebrenik',
                    'addressCountry' => 'BA',
                ],
                'foundingDate' => '2008',
                'sameAs' => array_values(array_filter([$c->FacebookURL, $c->InstagramURL, $c->TikTokURL])),
            ];
            return DBField::create_field('HTMLFragment', '<script type="application/ld+json">'
                . json_encode(array_filter($data, fn ($v) => $v !== null && $v !== []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) . '</script>');
        }

        /** Slika za pregled linka kad stranica nema svoju. */
        public function DefaultOgImage(): string
        {
            return Director::absoluteURL('images/hazre-og.jpg');
        }
    }
}
