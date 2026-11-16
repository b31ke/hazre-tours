<?php

namespace {

    use SilverStripe\CMS\Model\SiteTree;
    use SilverStripe\Forms\DropdownField;

    /**
     * Obična stranica iz CMS-a (O nama, Uslovi putovanja…). Ispod teksta može imati formu.
     */
    class Page extends SiteTree
    {
        public const FORMS = [
            '' => 'Bez forme (samo kontakt na dnu)',
            'pomjeri' => 'Putovanje po mjeri',
            'autobus' => 'Najam autobusa / ekskurzija',
        ];

        private static $db = [
            'PageForm' => "Enum(',pomjeri,autobus','')",
            'Intro' => 'Varchar(255)',
        ];

        private static $has_one = [];

        public function getCMSFields()
        {
            $fields = parent::getCMSFields();
            $fields->addFieldToTab('Root.Main', \SilverStripe\Forms\TextField::create('Intro', 'Podnaslov')
                ->setDescription('Jedna rečenica ispod naslova. Nije obavezno.'), 'Content');
            $fields->addFieldToTab('Root.Main', DropdownField::create('PageForm', 'Forma ispod teksta', self::FORMS), 'Metadata');
            return $fields;
        }
    }
}
