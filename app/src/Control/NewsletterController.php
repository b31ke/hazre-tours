<?php

namespace HazreTours\Control;

use HazreTours\Model\Subscriber;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Security\SecurityToken;

/**
 * Prijava na novosti (POST /newsletter). Adrese su u admin → Newsletter (izvoz u CSV za slanje).
 */
class NewsletterController extends Controller
{
    private static $allowed_actions = ['index'];

    public function index(HTTPRequest $request): HTTPResponse
    {
        $ajax = $request->isAjax() || str_contains((string) $request->getHeader('Accept'), 'application/json');
        if (!$request->isPOST() || !SecurityToken::inst()->checkRequest($request)) {
            return $this->reply($ajax, 400, 'Osvježite stranicu i pokušajte ponovo.');
        }
        if (trim((string) $request->postVar('website')) !== '') {
            return $this->reply($ajax, 200, 'Hvala!', true);
        }
        $email = mb_strtolower(trim((string) $request->postVar('email')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->reply($ajax, 422, 'Upišite ispravnu e-mail adresu.');
        }
        if (!$request->postVar('consent')) {
            return $this->reply($ajax, 422, 'Potvrdite da želite primati obavijesti.');
        }
        if (!Subscriber::get()->filter('Email', $email)->exists()) {
            Subscriber::create(['Email' => mb_substr($email, 0, 150)])->write();
        }
        return $this->reply($ajax, 200, 'Prijavljeni ste. Javljamo nova putovanja i akcije prvo vama.', true);
    }

    private function reply(bool $ajax, int $code, string $message, bool $ok = false): HTTPResponse
    {
        if ($ajax) {
            return HTTPResponse::create(json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE), $code)
                ->addHeader('Content-Type', 'application/json; charset=utf-8');
        }
        return $this->redirect('/?novosti=' . ($ok ? 'ok' : 'greska') . '#novosti');
    }
}
