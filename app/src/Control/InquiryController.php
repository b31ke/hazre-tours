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
use SilverStripe\Core\Environment;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\SecurityToken;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Prima sve forme za upit (POST /upit): rezervacija, putovanje po mjeri, izmjena aranžmana, najam autobusa.
 * Upit se sprema u admin (Upiti) i šalje mailom agenciji. Radi i bez JavaScripta.
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

        $type = (string) $request->postVar('type');
        if (!isset(Inquiry::TYPES[$type])) {
            $type = 'rezervacija';
        }
        $v = fn (string $name, int $max = 150) => mb_substr(trim((string) $request->postVar($name)), 0, $max);

        $name = $v('name', 120);
        $phone = $v('phone', 40);
        $email = $v('email');
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
        if ($type === 'pomjeri' && $v('destination') === '') {
            $errors['destination'] = 'Napišite kuda biste željeli putovati.';
        }
        if (!$request->postVar('terms')) {
            $errors['terms'] = 'Potvrdite saglasnost da bismo vam mogli poslati ponudu.';
        }
        if ($errors) {
            return $this->reply($ajax, 422, ['error' => 'Provjerite označena polja.', 'fields' => $errors]);
        }

        $trip = Trip::get()->byID((int) $request->postVar('trip'));
        $changes = (array) $request->postVar('changes');
        $inquiry = Inquiry::create([
            'Type' => $type,
            'Name' => $name,
            'Phone' => $phone,
            'Email' => $email,
            'TripID' => $trip && $trip->Active ? $trip->ID : 0,
            'TermLabel' => $v('term', 80),
            'Departure' => $v('departure', 60),
            'Adults' => max(0, min(99, (int) $request->postVar('adults'))),
            'Kids' => max(0, min(99, (int) $request->postVar('kids'))),
            'KidsAges' => $v('kids_ages', 80),
            'Destination' => $v('destination'),
            'Period' => $v('period'),
            'Duration' => $v('duration', 40),
            'Budget' => $v('budget', 60),
            'AccommodationWish' => $v('accommodation', 60),
            'Changes' => mb_substr(implode(', ', array_map(fn ($c) => trim((string) $c), $changes)), 0, 255),
            'Message' => $v('message', 3000),
        ]);
        $inquiry->write();
        $this->notify($inquiry);

        $first = explode(' ', $inquiry->Name)[0];
        $message = match ($type) {
            'pomjeri' => "Hvala, {$first}! Pripremamo ponudu po vašoj mjeri i javljamo se na {$inquiry->Phone}.",
            'autobus' => "Hvala, {$first}! Šaljemo ponudu za prevoz, javljamo se na {$inquiry->Phone}.",
            'izmjena' => "Hvala, {$first}! Pogledat ćemo šta možemo promijeniti i javiti se na {$inquiry->Phone}.",
            default => "Hvala, {$first}! Upit je zaprimljen. Javit ćemo vam se na {$inquiry->Phone} radi potvrde i uplate avansa.",
        };
        return $this->reply($ajax, 200, ['ok' => true, 'message' => $message]);
    }

    private function notify(Inquiry $inquiry): void
    {
        $to = SiteConfig::current_site_config()->inquiryRecipient();
        if (!$to) {
            return;
        }
        $html = '<h2 style="font-family:sans-serif">' . htmlspecialchars($inquiry->getTypeLabel()) . ' sa sajta</h2><table cellpadding="6" style="border-collapse:collapse;font-family:sans-serif">';
        foreach ($inquiry->rows() as $label => $value) {
            $html .= '<tr><td style="color:#5B6E7C;vertical-align:top">' . $label . '</td><td><b>' . nl2br(htmlspecialchars((string) $value)) . '</b></td></tr>';
        }
        $model = str_replace('\\', '-', Inquiry::class);
        $admin = Director::absoluteURL("/admin/aranzmani/{$model}/EditForm/field/{$model}/item/{$inquiry->ID}/edit");
        $html .= '</table><p style="font-family:sans-serif"><a href="' . htmlspecialchars($admin) . '">Otvori upit u adminu</a></p>';

        try {
            $mail = Email::create()
                ->setTo($to)
                ->setSubject($inquiry->getTypeLabel() . ': ' . $inquiry->getTripTitle() . ' – ' . $inquiry->Name)
                ->html($html);
            // Pošiljalac mora biti adresa s domene servera s kojeg mail ide (inače završava u spamu)
            if ($from = Environment::getEnv('HAZRE_MAIL_FROM')) {
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
            return HTTPResponse::create(json_encode($data, JSON_UNESCAPED_UNICODE), $code)
                ->addHeader('Content-Type', 'application/json; charset=utf-8');
        }
        $back = strtok($this->getBackURL() ?: '/', '#');
        $back .= (str_contains($back, '?') ? '&' : '?') . (!empty($data['ok']) ? 'upit=poslan' : 'upit=greska') . '#kontakt';
        return $this->redirect($back);
    }
}
