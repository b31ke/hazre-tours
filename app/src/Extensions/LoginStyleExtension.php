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
  f.innerHTML = '<a class="hz-created" href="https://beshirt.ba" target="_blank" rel="noopener" aria-label="Created by BE SHIRT d.o.o.">'
    + '<span class="hz-created__by">Created by</span><span class="hz-created__sep" aria-hidden="true"></span>'
    + '<img src=' + JSON.stringify($logo) + ' alt="BE SHIRT d.o.o." width="92" height="25"></a>';
  f.classList.add('hz-brand');
});
JS, 'hazre-login-brand');
    }
}
