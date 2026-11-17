<?php

namespace HazreTours\Extensions;

use SilverStripe\Control\Director;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Stranica za prijavu u admin (/Security/login) u bojama sajta: plava pozadina, logo, narandžasto dugme.
 * Na dnu "Created by" i BE SHIRT logo (link na beshirt.ba) umjesto Silverstripe loga.
 */
class LoginStyleExtension extends Extension
{
    protected function onAfterInit(): void
    {
        Requirements::css('css/admin-login.css');
        $logo = json_encode(Director::baseURL() . 'images/be-shirt-logo-white.png');
        Requirements::customScript(<<<JS
document.addEventListener('DOMContentLoaded', function () {
  var f = document.querySelector('.silverstripe-brand');
  if (!f) return;
  f.innerHTML = '<span class="hz-created">Created by</span>'
    + '<a class="hz-created__logo" href="https://beshirt.ba" target="_blank" rel="noopener" aria-label="BE SHIRT d.o.o.">'
    + '<img src=' + JSON.stringify($logo) + ' alt="BE SHIRT d.o.o." width="92" height="25"></a>';
  f.classList.add('hz-brand');
});
JS, 'hazre-login-brand');
    }
}
