<?php

namespace HazreTours\Control;

use HazreTours\Model\Inquiry;
use HazreTours\Model\Trip;
use Psr\Log\LoggerInterface;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\Email\Email;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\SecurityToken;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Prima formu za upit/rezervaciju (POST /upit): sprema upit u admin (Upiti) i šalje mail agenciji.
 * Forma radi i bez JavaScripta (preusmjerenje nazad s ?upit=poslan).
 */
class InquiryController extends Controller
{
    private static $allowed_actions = ['index'];

    public function index(HTTPRequest $request): HTTPResponse
    {
        if (!$request->isPOST()) {
            return $this->redirect('/#kontakt');
        }
        $ajax = $request->isAjax() || str_contains((string) $request->getHeader('Accept'), 'application/json');

        if (!SecurityToken::inst()->checkRequest($request)) {
            return $this->reply($ajax, 400, ['error' => 'Stranica je bila dugo otvorena. Osvježite je i pošaljite ponovo.']);
        }
        // Zamka za botove: ovo polje ljudi ne vide
        if (trim((string) $request->postVar('website')) !== '') {
            return $this->reply($ajax, 200, ['ok' => true, 'message' => 'Hvala!']);
        }

        $name = trim((string) $request->postVar('name'));
        $phone = trim((string) $request->postVar('phone'));
        $email = trim((string) $request->postVar('email'));
        $errors = [];
        if (mb_strlen($name) < 3) {
            $errors['name'] = 'Upišite ime i prezime.';
        }
        if (strlen(preg_replace('/\D/', '', $phone)) < 6) {
            $errors['phone'] = 'Upišite broj telefona da vas možemo nazvati.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'E-mail adresa nije ispravna.';
        }
        if (!$request->postVar('terms')) {
            $errors['terms'] = 'Potvrdite da ste upoznati s uslovima putovanja.';
        }
        if ($errors) {
            return $this->reply($ajax, 422, ['error' => 'Provjerite označena polja.', 'fields' => $errors]);
        }

        $trip = Trip::get()->byID((int) $request->postVar('trip'));
        $inquiry = Inquiry::create([
            'Name' => mb_substr($name, 0, 120),
            'Phone' => mb_substr($phone, 0, 40),
            'Email' => mb_substr($email, 0, 150),
            'TripID' => $trip && $trip->Active ? $trip->ID : 0,
            'TermLabel' => mb_substr(trim((string) $request->postVar('term')), 0, 80),
            'Departure' => mb_substr(trim((string) $request->postVar('departure')), 0, 60),
            'Adults' => max(1, min(99, (int) $request->postVar('adults'))),
            'Kids' => max(0, min(99, (int) $request->postVar('kids'))),
            'Message' => mb_substr(trim((string) $request->postVar('message')), 0, 3000),
        ]);
        $inquiry->write();
        $this->notify($inquiry);

        $first = explode(' ', $inquiry->Name)[0];
        return $this->reply($ajax, 200, [
            'ok' => true,
            'message' => "Hvala, {$first}! Upit je zaprimljen. Javit ćemo vam se na broj {$inquiry->Phone} radi potvrde i uplate avansa.",
        ]);
    }

    private function notify(Inquiry $inquiry): void
    {
        $config = SiteConfig::current_site_config();
        $to = $config->inquiryRecipient();
        if (!$to) {
            return;
        }
        $rows = [
            'Aranžman' => $inquiry->getTripTitle(),
            'Termin' => $inquiry->TermLabel,
            'Mjesto polaska' => $inquiry->Departure,
            'Putnika' => $inquiry->getPeopleLabel(),
            'Ime i prezime' => $inquiry->Name,
            'Telefon' => $inquiry->Phone,
            'E-mail' => $inquiry->Email,
            'Napomena' => $inquiry->Message,
        ];
        $html = '<h2>Novi upit sa sajta</h2><table cellpadding="6" style="border-collapse:collapse">';
        foreach ($rows as $label => $value) {
            if ($value !== '' && $value !== null) {
                $html .= '<tr><td style="color:#5B6E7C">' . $label . '</td><td><b>' . nl2br(htmlspecialchars((string) $value)) . '</b></td></tr>';
            }
        }
        $admin = Director::absoluteURL('/admin/aranzmani/' . str_replace('\\', '-', Inquiry::class) . '/EditForm/field/' . str_replace('\\', '-', Inquiry::class) . '/item/' . $inquiry->ID . '/edit');
        $html .= '</table><p><a href="' . htmlspecialchars($admin) . '">Otvori upit u adminu</a></p>';

        try {
            $mail = Email::create()
                ->setTo($to)
                ->setSubject('Novi upit: ' . $inquiry->getTripTitle() . ' – ' . $inquiry->Name)
                ->html($html);
            // Pošiljalac mora biti adresa s domene servera s kojeg mail ide (inače završava u spamu):
            // na privremenom hostingu se postavlja u .env (HAZRE_MAIL_FROM)
            if ($from = \SilverStripe\Core\Environment::getEnv('HAZRE_MAIL_FROM')) {
                $mail->setFrom($from, 'Hazre Tours');
            }
            if ($inquiry->Email) {
                $mail->setReplyTo($inquiry->Email, $inquiry->Name);
            }
            $mail->send();
        } catch (\Throwable $e) {
            // Upit je sačuvan u adminu i kad mail ne prođe
            Injector::inst()->get(LoggerInterface::class)->warning('Mail za upit nije poslan: ' . $e->getMessage());
        }
    }

    private function reply(bool $ajax, int $code, array $data): HTTPResponse
    {
        if ($ajax) {
            $response = HTTPResponse::create(json_encode($data, JSON_UNESCAPED_UNICODE), $code);
            $response->addHeader('Content-Type', 'application/json; charset=utf-8');
            return $response;
        }
        // Bez JavaScripta: nazad na stranicu s koje je poslano
        $back = $this->getBackURL() ?: '/';
        $back = strtok($back, '#');
        $back .= (str_contains($back, '?') ? '&' : '?') . (!empty($data['ok']) ? 'upit=poslan' : 'upit=greska') . '#kontakt';
        return $this->redirect($back);
    }
}
