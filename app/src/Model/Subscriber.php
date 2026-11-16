<?php

namespace HazreTours\Model;

use SilverStripe\ORM\DataObject;

/**
 * Prijava na obavijesti o novim putovanjima i akcijama (newsletter). Admin → Upiti → Newsletter (izvoz u CSV).
 */
class Subscriber extends DataObject
{
    private static string $table_name = 'HazreSubscriber';

    private static string $singular_name = 'Prijava na novosti';

    private static string $plural_name = 'Newsletter';

    private static array $db = [
        'Email' => 'Varchar(150)',
    ];

    private static array $indexes = [
        'Email' => ['type' => 'unique', 'columns' => ['Email']],
    ];

    private static string $default_sort = 'Created DESC';

    private static array $summary_fields = [
        'Email' => 'E-mail',
        'Created.Nice' => 'Prijavljen',
    ];

    public function canCreate($member = null, $context = []): bool
    {
        return false;
    }

    public function canEdit($member = null): bool
    {
        return false;
    }
}
