<?php

namespace HazreTours\Extensions;

use HazreTours\Admin\TripAdmin;
use SilverStripe\Core\Extension;
use SilverStripe\Security\Permission;

/**
 * Ko ima pristup "Aranžmani i upiti" (grupa Urednici aranžmana) smije pregledati i uređivati
 * aranžmane, termine, upite, kategorije, pitanja, utiske i newsletter, te dodavati fotografije za njih.
 * Glavni admin i dalje vidi sve; modeli koji nešto izričito zabranjuju (npr. ručno dodavanje upita) to zadržavaju.
 */
class TripAdminAccessExtension extends Extension
{
    public static function allowed($member = null): bool
    {
        return Permission::checkMember($member, 'CMS_ACCESS_' . TripAdmin::class);
    }

    protected function canView($member = null)
    {
        return self::allowed($member) ? true : null;
    }

    protected function canEdit($member = null)
    {
        return self::allowed($member) ? true : null;
    }

    protected function canDelete($member = null)
    {
        return self::allowed($member) ? true : null;
    }

    protected function canCreate($member = null, $context = [])
    {
        return self::allowed($member) ? true : null;
    }
}
