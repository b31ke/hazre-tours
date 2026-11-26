<?php

namespace HazreTours\Control;

use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\Middleware\HTTPMiddleware;

/**
 * Sigurnosna zaglavlja za sve odgovore sajta i admina:
 * zabrana ugrađivanja u tuđe stranice (clickjacking), bez pogađanja tipa sadržaja, ograničen Referer,
 * isključene funkcije preglednika koje sajt ne koristi i (na HTTPS) obavezan HTTPS za ubuduće.
 * Content-Security-Policy dolazi nakon sigurnosnog pregleda (treba uskladiti s fontovima, mapama i analitikom).
 */
class SecurityHeadersMiddleware implements HTTPMiddleware
{
    public function process(HTTPRequest $request, callable $delegate)
    {
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }
        /** @var HTTPResponse $response */
        $response = $delegate($request);
        if (!$response instanceof HTTPResponse) {
            return $response;
        }
        $set = function (string $name, string $value) use ($response) {
            if (!$response->getHeader($name)) {
                $response->addHeader($name, $value);
            }
        };
        $set('X-Content-Type-Options', 'nosniff');
        $set('X-Frame-Options', 'SAMEORIGIN');
        $set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        if (Director::is_https($request) && Director::isLive()) {
            $set('Strict-Transport-Security', 'max-age=31536000');
        }
        return $response;
    }
}
