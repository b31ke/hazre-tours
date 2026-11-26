<?php

namespace HazreTours\Extensions;

use HazreTours\Admin\TripAdmin;
use SilverStripe\Core\Extension;
use SilverStripe\Security\Group;
use SilverStripe\Security\Permission;

/**
 * Pri izgradnji baze napravi grupu "Urednici aranžmana" koja vidi samo Aranžmani i upiti.
 * Korisnike u grupu dodaje glavni admin (Korisnici → korisnik → Grupe).
 */
class EditorGroupExtension extends Extension
{
    public const CODE = 'urednici-aranzmana';

    protected function onRequireDefaultRecords(): void
    {
        $group = Group::get()->filter('Code', self::CODE)->first();
        if (!$group) {
            $group = Group::create([
                'Title' => 'Urednici aranžmana',
                'Code' => self::CODE,
                'Description' => 'Vide i uređuju samo "Aranžmani i upiti": aranžmane, termine, upite, kategorije, česta pitanja, utiske i newsletter.',
            ]);
            $group->write();
        }
        // Aranžmani i upiti + Izjave putnika (agencija ih pravi za putnike koji su rezervisali)
        foreach (['CMS_ACCESS_' . TripAdmin::class, 'CMS_ACCESS_' . \HazreTours\Admin\StatementAdmin::class] as $code) {
            if (!Permission::get()->filter(['GroupID' => $group->ID, 'Code' => $code])->exists()) {
                Permission::grant($group->ID, $code);
            }
        }
    }
}
