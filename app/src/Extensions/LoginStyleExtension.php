<?php

namespace HazreTours\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Stranica za prijavu u admin (/Security/login) u bojama sajta: plava pozadina, logo, narandžasto dugme.
 */
class LoginStyleExtension extends Extension
{
    protected function onAfterInit(): void
    {
        Requirements::css('css/admin-login.css');
    }
}
