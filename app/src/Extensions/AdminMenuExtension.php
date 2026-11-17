<?php

namespace HazreTours\Extensions;

use HazreTours\Admin\TripAdmin;
use HazreTours\Model\FaqItem;
use HazreTours\Model\Inquiry;
use HazreTours\Model\Review;
use HazreTours\Model\Subscriber;
use HazreTours\Model\Trip;
use HazreTours\Model\TripCategory;
use SilverStripe\Core\Extension;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;

/**
 * Podstablo u lijevom meniju admina ispod "Aranžmani i upiti" (Aranžmani, Upiti, Kategorije…), s brojem novih upita.
 */
class AdminMenuExtension extends Extension
{
    public function HazreTripMenu(): ArrayList
    {
        $base = TripAdmin::singleton()->Link();
        $new = Inquiry::get()->filter('Status', 'novo')->count();
        $items = [
            [Trip::class, 'Aranžmani', 'font-icon-p-map', 0],
            [Inquiry::class, 'Upiti', 'font-icon-chat', $new],
            [TripCategory::class, 'Kategorije', 'font-icon-tags', 0],
            [FaqItem::class, 'Česta pitanja', 'font-icon-help-circled', 0],
            [Review::class, 'Utisci putnika', 'font-icon-happy', 0],
            [Subscriber::class, 'Newsletter', 'font-icon-p-mail', 0],
        ];
        return ArrayList::create(array_map(fn ($i) => ArrayData::create([
            'Link' => rtrim($base, '/') . '/' . str_replace('\\', '-', $i[0]),
            'Title' => $i[1],
            'Icon' => $i[2],
            'Badge' => $i[3],
            'Slug' => str_replace('\\', '-', $i[0]),
        ]), $items));
    }
}
