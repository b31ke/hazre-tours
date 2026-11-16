<?php

namespace HazreTours\Control;

use HazreTours\Model\Trip;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

/**
 * robots.txt i sitemap.xml (stranice iz CMS-a i aktivni aranžmani).
 */
class SeoController extends Controller
{
    private static $allowed_actions = ['index'];

    public function index(HTTPRequest $request): HTTPResponse
    {
        if (str_ends_with($request->getURL(), 'robots.txt')) {
            $body = self::indexable()
                ? "User-agent: *\nDisallow: /admin\nDisallow: /Security\nSitemap: " . Director::absoluteURL('/sitemap.xml') . "\n"
                : "User-agent: *\nDisallow: /\n";
            return HTTPResponse::create($body)->addHeader('Content-Type', 'text/plain; charset=utf-8');
        }

        if (!self::indexable()) {
            return HTTPResponse::create('', 404);
        }
        $urls = [];
        foreach (SiteTree::get()->filter('ShowInSearch', true) as $page) {
            if ($page->canView() && !$page instanceof \SilverStripe\ErrorPage\ErrorPage) {
                $urls[] = [$page->AbsoluteLink(), $page->LastEdited];
            }
        }
        foreach (Trip::get()->filter('Active', true) as $trip) {
            if ($trip->NextTerm()) {
                $urls[] = [$trip->AbsoluteLink(), $trip->LastEdited];
            }
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as [$loc, $mod]) {
            $xml .= '  <url><loc>' . htmlspecialchars($loc) . '</loc><lastmod>' . date('Y-m-d', strtotime((string) $mod)) . "</lastmod></url>\n";
        }
        $xml .= '</urlset>';
        return HTTPResponse::create($xml)->addHeader('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Google smije indeksirati sajt tek kad je na pravoj adresi: HAZRE_ALLOW_INDEX="1" u .env.
     * Na privremenoj adresi (naš hosting) to ostaje isključeno, da ne bude duplog sadržaja sa starim sajtom.
     */
    public static function indexable(): bool
    {
        return Director::isLive() && (bool) \SilverStripe\Core\Environment::getEnv('HAZRE_ALLOW_INDEX');
    }
}
