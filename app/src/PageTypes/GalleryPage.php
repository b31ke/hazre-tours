<?php

namespace HazreTours\PageTypes;

use Page;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\ManyManyList;

/**
 * Galerija: fotografije s putovanja i vozni park. Pages → Galerija.
 *
 * @method ManyManyList<Image> Images()
 */
class GalleryPage extends Page
{
    private static string $table_name = 'HazreGalleryPage';

    private static string $singular_name = 'Galerija';

    private static string $description = 'Fotografije s putovanja i vozni park';

    private static string $icon_class = 'font-icon-p-gallery';

    private static array $many_many = [
        'Images' => Image::class,
    ];

    private static array $many_many_extraFields = [
        'Images' => ['SortOrder' => 'Int'],
    ];

    private static array $owns = ['Images'];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Main', UploadField::create('Images', 'Fotografije')
            ->setFolderName('galerija')->setAllowedFileCategories('image/supported')
            ->setDescription('Možete označiti i prevući više fotografija odjednom. Naslov fotografije se vidi ispod nje.'), 'Content');
        return $fields;
    }

    public function SortedImages()
    {
        return $this->Images()->sort('SortOrder');
    }
}
