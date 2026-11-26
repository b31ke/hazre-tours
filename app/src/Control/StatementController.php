<?php

namespace HazreTours\Control;

use HazreTours\Admin\StatementAdmin;
use HazreTours\Model\TravelerStatement;
use PageController;
use Psr\Log\LoggerInterface;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Director;
use SilverStripe\Control\Email\Email;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Middleware\HTTPCacheControlMiddleware;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Security\SecurityToken;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Versioned\Versioned;

/**
 * Javna stranica izjave putnika: /izjava/<token>.
 * Bez tačnog tokena (48 heks znakova) ne prikazuje se ništa; nevažeći i istekli link daju istu poruku.
 * Stranica se ne kešira, ne indeksira i ne šalje adresu (token) drugim sajtovima.
 */
class StatementController extends PageController
{
    private static $allowed_actions = ['index'];

    private static $url_handlers = [
        '$Token' => 'index',
    ];

    private ?TravelerStatement $statement = null;

    private string $state = 'invalid';

    private array $errors = [];

    public function index(HTTPRequest $request)
    {
        HTTPCacheControlMiddleware::singleton()->disableCache(true);
        $response = $this->getResponse();
        $response->addHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $response->addHeader('Referrer-Policy', 'no-referrer');

        $this->statement = $this->findStatement((string) $request->param('Token'));
        $s = $this->statement;
        if (!$s) {
            $this->state = 'invalid';
        } elseif ($s->IsConfirmed()) {
            $this->state = 'confirmed';
        } elseif ($s->IsExpired()) {
            $this->state = 'invalid';
        } else {
            $this->state = 'form';
            if ($request->isPOST()) {
                $this->handlePost($request, $s);
            }
        }
        if ($this->state === 'invalid') {
            $response->setStatusCode(404);
        }

        return $this->customise([
            'Title' => 'Izjava putnika',
            'MetaTitle' => 'Izjava o prihvatanju Opštih uslova putovanja | Hazre Tours',
        ])->renderWith(['StatementPage', 'Page']);
    }

    private function findStatement(string $token): ?TravelerStatement
    {
        if (!preg_match('/^[a-f0-9]{48}$/', $token)) {
            return null;
        }
        $s = TravelerStatement::get()->filter('Token', $token)->first();
        return $s && hash_equals((string) $s->Token, $token) ? $s : null;
    }

    private function handlePost(HTTPRequest $request, TravelerStatement $s): void
    {
        if (!SecurityToken::inst()->checkRequest($request)) {
            $this->errors['form'] = 'Stranica je bila dugo otvorena. Osvježite je i potvrdite ponovo.';
            return;
        }
        // Bez termina iz admina: putnik bira aranžman i termin (samo aktuelni polasci aktivnih aranžmana)
        $termId = 0;
        if (!$s->TermID) {
            $termId = (int) $request->postVar('term');
            if (!isset(TravelerStatement::selectableTerms()[$termId])) {
                $this->errors['term'] = 'Izaberite aranžman i termin na koji putujete.';
            }
        }
        $checked = (array) $request->postVar('check');
        foreach (array_keys(TravelerStatement::CHECKS) as $key) {
            if (empty($checked[$key])) {
                $this->errors['checks'] = 'Za potvrdu izjave potrebno je označiti sve potvrde.';
            }
        }
        $companions = TravelerStatement::parseCompanions(mb_substr((string) $request->postVar('companions'), 0, 6000));
        if (count($companions) > TravelerStatement::MAX_COMPANIONS) {
            $this->errors['companions'] = 'Najviše ' . TravelerStatement::MAX_COMPANIONS . ' saputnika. Za veće grupe javite se agenciji.';
        }
        if ($this->errors) {
            return;
        }

        if ($termId) {
            $s->TermID = $termId;
        }
        $s->Companions = implode("\n", $companions);
        $s->Status = 'potvrdjena';
        $s->ConfirmedAt = date('Y-m-d H:i:s');
        $s->ConfirmIP = mb_substr((string) $request->getIP(), 0, 45);
        $s->ConfirmAgent = mb_substr(strip_tags((string) $request->getHeader('User-Agent')), 0, 255);
        $s->TermsVersion = $this->pageVersion('uslovi-putovanja');
        $s->PrivacyVersion = $this->pageVersion('politika-privatnosti');
        $s->ConfirmedChecks = json_encode(array_values(TravelerStatement::CHECKS), JSON_UNESCAPED_UNICODE);
        $s->write();
        $this->state = 'done';
        $this->notify($s);
    }

    /** Verzija stranice u trenutku potvrde: datum zadnje izmjene i otisak sadržaja (dokaz šta je putnik prihvatio). */
    private function pageVersion(string $segment): string
    {
        $page = Versioned::get_by_stage(SiteTree::class, Versioned::LIVE)->filter('URLSegment', $segment)->first();
        if (!$page) {
            return 'nije pronađena';
        }
        return 'izmjena ' . $page->obj('LastEdited')->Format('dd.MM.y. HH:mm') . ', otisak ' . substr(hash('sha256', (string) $page->Content), 0, 16);
    }

    private function notify(TravelerStatement $s): void
    {
        $to = SiteConfig::current_site_config()->inquiryRecipient();
        if (!$to) {
            return;
        }
        $e = fn ($v) => htmlspecialchars((string) $v, ENT_QUOTES);
        $model = str_replace('\\', '-', TravelerStatement::class);
        $admin = Director::absoluteURL(StatementAdmin::singleton()->Link() . "/{$model}/EditForm/field/{$model}/item/{$s->ID}/edit");
        $html = '<h2 style="font-family:sans-serif">Putnik je potvrdio izjavu</h2><p style="font-family:sans-serif">'
            . '<b>' . $e($s->HolderName) . '</b><br>' . $e($s->getTripTitle()) . ' – ' . $e($s->getTermLabel())
            . '<br>Saputnika: ' . $s->getCompanionCount() . '<br>Potvrđeno: ' . $e($s->getConfirmedLabel())
            . '</p><p style="font-family:sans-serif"><a href="' . $e($admin) . '">Otvori izjavu u adminu</a></p>';
        try {
            $mail = Email::create()->setTo($to)
                ->setSubject('Izjava potvrđena: ' . $s->getTripTitle() . ' – ' . $s->HolderName)
                ->html($html);
            if ($from = Environment::getEnv('HAZRE_MAIL_FROM')) {
                $mail->setFrom($from, 'Hazre Tours');
            }
            $mail->send();
        } catch (\Throwable $ex) {
            Injector::inst()->get(LoggerInterface::class)->warning('Mail za izjavu nije poslan: ' . $ex->getMessage());
        }
    }

    // ---------- Za šablon (lični podaci samo u stanju "form")

    public function Indexable(): bool
    {
        return false;
    }

    public function Link($action = null): string
    {
        return $this->statement ? '/izjava/' . $this->statement->Token : '/izjava';
    }

    public function State(): string
    {
        return $this->state;
    }

    public function Stmt(): ?ArrayData
    {
        $s = $this->statement;
        if (!$s || !in_array($this->state, ['form', 'done'], true)) {
            return null;
        }
        return ArrayData::create([
            'HolderName' => $s->HolderName,
            'FirstName' => explode(' ', trim((string) $s->HolderName))[0],
            'Phone' => $s->MaskedPhone(),
            'Trip' => $s->getTripTitle(),
            'TripLink' => $s->Trip()->exists() ? $s->Trip()->Link() : '',
            'Term' => $s->getTermLabel(),
            'NeedsTerm' => !$s->TermID,
            'TermOptions' => ArrayList::create(array_map(
                fn ($id, $label) => ArrayData::create(['ID' => $id, 'Label' => $label]),
                array_keys(TravelerStatement::selectableTerms()),
                TravelerStatement::selectableTerms()
            )),
            // za <textarea>: već escapirano, bez pretvaranja novih redova u <br>
            'Companions' => \SilverStripe\ORM\FieldType\DBField::create_field('HTMLFragment', htmlspecialchars(implode("\n", $s->CompanionList()), ENT_QUOTES)),
            'CompanionList' => ArrayList::create(array_map(fn ($n) => ArrayData::create(['Name' => $n]), $s->CompanionList())),
        ]);
    }

    public function ConfirmedOn(): string
    {
        return $this->statement && $this->state === 'confirmed' ? $this->statement->obj('ConfirmedAt')->Format('dd.MM.y.') : '';
    }

    public function Checks(): ArrayList
    {
        return ArrayList::create(array_map(
            fn ($k, $t) => ArrayData::create(['Key' => $k, 'Text' => $t]),
            array_keys(TravelerStatement::CHECKS),
            TravelerStatement::CHECKS
        ));
    }

    public function FormError(): string
    {
        return $this->errors['form'] ?? ($this->errors['term'] ?? ($this->errors['checks'] ?? ($this->errors['companions'] ?? '')));
    }

    public function MaxCompanions(): int
    {
        return TravelerStatement::MAX_COMPANIONS;
    }
}
