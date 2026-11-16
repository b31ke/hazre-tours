<?php

namespace HazreTours\Tasks;

use HazreTours\Model\Trip;
use HazreTours\Model\TripTerm;
use HazreTours\PageTypes\HomePage;
use Page;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use SilverStripe\SiteConfig\SiteConfig;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Prebacuje sadržaj sa starog sajta hazretours.ba u CMS: kontakt podatke, stranice
 * (Početna, O nama, Uslovi putovanja, Politika privatnosti) i aranžmane s terminima,
 * plakatima, programima (Word) i programom po danima.
 *
 * Pravi samo ono što još ne postoji, pa se može pokrenuti više puta i na serveru:
 * ništa što je agencija izmijenila u adminu se ne prepisuje.
 *
 *   ddev exec vendor/bin/sake tasks:hazre-import
 */
class ImportTask extends BuildTask
{
    protected static string $commandName = 'hazre-import';

    protected string $title = 'Hazre Tours: uvoz sa starog sajta';

    protected static string $description = 'Kontakt, stranice i aranžmani sa hazretours.ba (pravi samo ono što još ne postoji).';

    private const OLD = 'https://www.hazretours.ba';

    private const UP = self::OLD . '/wp-content/uploads/';

    /**
     * Aranžmani sa starog sajta (novembar 2026). Cijene su s plakata, jer ih na stranicama aranžmana nema.
     * Termin: [polazak, povratak|null, cijena|null]
     */
    private const TRIPS = [
        ['slug' => 'graz-maribor-zagreb-novembar', 'title' => 'Graz – Maribor – Zagreb', 'type' => 'visednevni', 'price' => 189,
            'terms' => [['2026-11-21', '2026-11-22']],
            'poster' => '2026/09/GRAZ-MARIBOR-ZAGREB-2111.jpg', 'doc' => '2026/09/GRAZ-MARIBOR-ZAGREB-2111-1.doc',
            'old' => '/aranzman/izleti/visednevni/graz-maribor-zagreb-21-11-22-11-2026/',
            'hl' => 'Seiersberg, Maribor i Zagreb s vodičem', 'from' => 'Tuzla, Šićki Brod, Srebrenik, Gradačac, Brčko, Orašje',
            'summary' => 'Noćna vožnja do šoping centra Seiersberg, fakultativni obilazak Graza, noćenje u centru Maribora i obilazak Zagreba s vodičem.',
            'inc' => "Prevoz autobusom turističke klase (klima, TV, video, WC)\nHotel 3* u Mariboru, 1 noćenje s doručkom\nPosjeta šoping centru Seiersberg\nObilazak Maribora i Zagreba s vodičem\nPratilac tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "Putničko zdravstveno osiguranje 6 KM (preko 60 godina dupla polica)\nOdlazak u Graz i obilazak s vodičem 10 €\nDoplata za jednokrevetnu sobu 50 KM\nDodatno sjedište u autobusu 50 KM\nHotelska taksa 3 €\nIndividualni troškovi"],
        ['slug' => 'novi-pazar-zlatibor', 'title' => 'Novi Pazar & Zlatibor', 'type' => 'visednevni', 'price' => 149,
            'terms' => [['2026-11-21', '2026-11-22']],
            'poster' => '2026/09/NOVI-PAZAR-2111.jpg', 'doc' => '2026/09/NOVI-PAZAR-2111.doc',
            'old' => '/aranzman/izleti/jednodnevni/novi-pazar-21-11-22-11-2026/',
            'hl' => 'Hotel Vrbak 3* i slobodno vrijeme na Zlatiboru', 'from' => 'Brčko, Gradačac, Gračanica, Srebrenik, Tuzla, Kalesija',
            'summary' => 'Preko Bajine Bašte i Zlatibora do Novog Pazara, obilazak grada s lokalnim vodičem, a u povratku dva sata na Zlatiboru.',
            'inc' => "Prevoz autobusom turističke klase (klima, TV, video, WC)\nHotel Vrbak 3*, 1 noćenje s doručkom\nPosjeta Zlatiboru\nPratilac tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "PZO 5 KM (preko 60 godina 10 KM)\nDoplata za jednokrevetnu sobu 35 KM\nObilazak grada s turističkim vodičem 10 KM\nVečera uz muziku uživo\nIndividualni troškovi"],
        ['slug' => 'advent-budimpesta', 'title' => 'Advent u Budimpešti', 'type' => 'jednodnevni', 'price' => 79,
            'terms' => [['2026-11-28', null]],
            'poster' => '2026/10/BUDIMPE%C5%A0TA-2811-1.jpg', 'doc' => '2026/10/BUDIMPE%C5%A0TA-2811-1.docx',
            'old' => '/aranzman/izleti/jednodnevni/budimpesta-28-11-2026/',
            'hl' => 'Božićni sajam, Ribarska kula, Budimski dvorac', 'from' => 'Tuzla, Srebrenik, Gračanica, Gradačac, Brčko, Orašje',
            'summary' => 'Noćna vožnja i dolazak oko 9 sati. Fakultativni obilazak grada s vodičem (Trg heroja, Bazilika sv. Stjepana, Lančani most), zatim božićni sajam.',
            'inc' => "Prevoz autobusom turističke klase (audio i video oprema)\nVodič-pratilac tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "Fakultativni obilazak grada 10 €\nPZO 5 KM\nIndividualni troškovi"],
        ['slug' => 'advent-shopping-ljubljana', 'title' => 'Advent & shopping Ljubljana', 'type' => 'jednodnevni', 'price' => 79,
            'terms' => [['2026-11-28', null]],
            'poster' => '2026/10/LJUBLJANA-2811.jpg', 'doc' => '2026/10/LJUBLJANA-2811.doc',
            'old' => '/aranzman/izleti/jednodnevni/ljubljana-28-11-2026/',
            'hl' => 'CityPark i božićni market', 'from' => 'Tuzla, Srebrenik, Gradačac, Brčko, Orašje',
            'summary' => 'Šoping u CityParku do 13 sati, zatim fakultativni obilazak Ljubljane s vodičem (Prešernov trg, Tromostovlje) i božićni market.',
            'inc' => "Prevoz autobusom turističke klase (klima, TV, video, WC)\nPosjeta tržnom centru CityPark\nUsluge turističkog vodiča\nPratilac tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "PZO 4 KM\nOdlazak u Ljubljanu i obilazak s vodičem 10 €\nUlaznice za historijske spomenike"],
        ['slug' => 'advent-shopping-graz', 'title' => 'Advent & shopping Graz', 'type' => 'jednodnevni', 'price' => 79,
            'terms' => [['2026-12-05', null], ['2026-12-19', null]],
            'poster' => '2026/10/GRAZ-2-POLASKA.jpg', 'doc' => '2026/10/GRAZ-2-POLASKA.doc',
            'old' => '/aranzman/izleti/jednodnevni/graz-05-12-2026-19-12-2026/',
            'hl' => 'Lidl, Seiersberg i stari grad pod zaštitom UNESCO-a', 'from' => 'Tuzla, Gračanica, Srebrenik, Gradačac, Brčko, Orašje',
            'summary' => 'Ujutro Lidl i Seiersberg do 12:30, zatim obilazak Graza s vodičem: Landhaus, Gradska vijećnica, Schlossberg. Polazak iz Graza u 17 sati.',
            'inc' => "Prevoz autobusom turističke klase (klima, TV, video, WC)\nPosjeta Seiersbergu i Lidlu\nPratilac/vodič tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "PZO 4 KM (preko 60 godina dupla polica)\nOdlazak u Graz i obilazak s vodičem 10 €\nIndividualni troškovi"],
        ['slug' => 'graz-maribor-zagreb-decembar', 'title' => 'Graz – Maribor – Zagreb', 'type' => 'visednevni', 'price' => 199,
            'terms' => [['2026-12-12', '2026-12-13']],
            'poster' => '2026/10/GRAZ-MARIBOR-ZAGREB-1212.jpg', 'doc' => '2026/10/GRAZ-MARIBOR-ZAGREB-1212.doc',
            'old' => '/aranzman/izleti/visednevni/graz-maribor-zagreb-12-12-13-12-2026/',
            'hl' => 'Tri adventa za jedan vikend', 'from' => 'Tuzla, Šićki Brod, Srebrenik, Gradačac, Brčko, Orašje',
            'summary' => 'Seiersberg i advent u Grazu, noćenje i advent u Mariboru, a u nedjelju adventski Zagreb s vodičem.',
            'inc' => "Prevoz autobusom turističke klase (klima, TV, video, WC)\nHotel 3* u Mariboru, 1 noćenje s doručkom\nPosjeta šoping centru Seiersberg\nObilazak Maribora i Zagreba s vodičem\nPratilac tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "PZO\nOdlazak u Graz i obilazak s vodičem 10 €\nDoplata za jednokrevetnu sobu\nHotelska taksa\nIndividualni troškovi"],
        ['slug' => 'edirne-istanbul', 'title' => 'Edirne & Istanbul', 'type' => 'visednevni', 'price' => 319,
            'terms' => [['2026-12-12', '2026-12-17']],
            'poster' => '2026/10/ISTANBUL-1212.jpg', 'doc' => '2026/10/ISTANBUL-1212.doc',
            'old' => '/aranzman/izleti/visednevni/istanbul-12-12-17-12-2026/',
            'hl' => 'Sultanahmet, krstarenje Bosforom, tursko veče', 'from' => 'Tuzla, Šićki Brod, Gračanica, Srebrenik, Gradačac, Brčko, Bijeljina',
            'summary' => 'Hotel Nanda 3* blizu Kapali čaršije. Sultanahmet, Čamlidža, Ortakoy, krstarenje Bosforom, Ejup i Pierre Loti, Sulejmanija i Egipatska pijaca.',
            'inc' => "Prevoz autobusom turističke klase\nHotel 3* u Istanbulu s doručkom\nObilazak kvarta Sultanahmet\nPratilac tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "PZO\nFakultativni izleti i krstarenje Bosforom\nTursko veče\nDoplata za jednokrevetnu sobu\nIndividualni troškovi"],
        ['slug' => 'budimpesta-decembar', 'title' => 'Budimpešta', 'type' => 'jednodnevni', 'price' => 79,
            'terms' => [['2026-12-13', null], ['2026-12-26', null]],
            'poster' => '2026/10/BUDIMPE%C5%A0TA-2-POLASKA.jpg', 'doc' => '2026/10/BUDIMPE%C5%A0TA-2-POLASKA.docx',
            'old' => '/aranzman/izleti/jednodnevni/budimpesta-13-12-2026-26-12-2026/',
            'hl' => 'Dva decembarska polaska', 'from' => 'Tuzla, Srebrenik, Gračanica, Gradačac, Brčko, Orašje',
            'summary' => 'Obilazak Budimpešte s vodičem i slobodno vrijeme na božićnom sajmu.',
            'inc' => "Prevoz autobusom turističke klase (audio i video oprema)\nVodič-pratilac tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "Fakultativni obilazak grada 10 €\nPZO 5 KM\nIndividualni troškovi"],
        ['slug' => 'graz-klagenfurt', 'title' => 'Graz & Klagenfurt', 'type' => 'visednevni', 'price' => 199,
            'terms' => [['2026-12-19', '2026-12-20']],
            'poster' => '2026/10/HAZREPOSTER.jpg', 'doc' => '2026/10/GRAZ-KLAGENFURT-1912.doc',
            'old' => '/aranzman/izleti/visednevni/graz-klagenfurt-19-12-20-12-2026/',
            'hl' => 'Seiersberg, hotel u Grazu, zmaj Lindwurm', 'from' => 'Tuzla, Šićki Brod, Srebrenik, Gradačac, Brčko, Orašje',
            'summary' => 'Šoping i advent u Grazu, noćenje u hotelu 3*, pa u nedjelju Klagenfurt: stara vijećnica, zmaj Lindwurm i božićni sajam.',
            'inc' => "Prevoz autobusom turističke klase (klima, TV, video, WC)\nHotel 3* u Grazu, 1 noćenje s doručkom\nPosjeta šoping centru Seiersberg\nPratilac i vodič tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "PZO 6 KM (preko 60 godina dupla polica)\nFakultativni izleti\nDoplata za jednokrevetnu sobu\nIndividualni troškovi"],
        ['slug' => 'nova-godina-istanbul', 'title' => 'Nova godina u Istanbulu', 'type' => 'visednevni', 'price' => 419,
            'terms' => [['2026-12-28', '2027-01-03']],
            'poster' => '2026/10/ISTANBUL-NOVA-GODINA.jpg', 'doc' => '2026/10/ISTANBUL-NOVA-GODINA-1.doc',
            'old' => '/aranzman/izleti/visednevni/nova-godina-istanbul-28-12-03-01-2027/',
            'hl' => 'Doček na Bosforu, Prinčeva ostrva', 'from' => 'Tuzla, Šićki Brod, Gračanica, Srebrenik, Gradačac, Brčko, Bijeljina',
            'summary' => 'Sedam dana u Istanbulu: Sultanahmet, Ortakoy, Čamlidža, krstarenje Bosforom, Prinčeva ostrva i mogućnost dočeka Nove godine na brodu.',
            'inc' => "Prevoz autobusom turističke klase\nHotel Nanda 3* s doručkom\nObilazak kvarta Sultanahmet\nPratilac tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "PZO\nFakultativni izleti\nSvečani doček Nove godine na brodu uz večeru (cijena naknadno)\nDoplata za jednokrevetnu sobu\nIndividualni troškovi"],
        ['slug' => 'zima-2027-zlatibor', 'title' => 'Zima 2027 – Zlatibor', 'type' => 'visednevni', 'price' => 139, 'badge' => 'Avans do 08.12.',
            'terms' => [['2027-01-08', '2027-01-10', 209], ['2027-01-15', '2027-01-17', 209], ['2027-01-22', '2027-01-24', 209], ['2027-01-30', '2027-01-31', 139], ['2027-02-06', '2027-02-07', 139]],
            'poster' => '2026/11/ZLATIBOR-2-i-3-DANA.jpg', 'doc' => '2026/11/ZLATIBOR-2-i-3-DANA-1.doc',
            'old' => '/aranzman/izleti/visednevni/zima-2027-zlatibor/',
            'hl' => 'Hotel 4*, wellness & spa, El Paso City', 'from' => 'Brčko, Gradačac, Gračanica, Srebrenik, Tuzla, Kalesija',
            'summary' => 'Vikend na Zlatiboru u hotelu 4* s wellness centrom, 2 dana / 1 noć ili 3 dana / 2 noći. Cijene važe za prijave i avans do 08.12.2026.',
            'inc' => "Prevoz autobusom turističke klase\nHotel 4* na Zlatiboru, 1 ili 2 noćenja s doručkom\nKorištenje wellness & spa centra i teretane\nPratilac tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "PZO 5 KM za 2 dana, 7,50 KM za 3 dana (preko 60 godina dupla polica)\nDoplata za jednokrevetnu sobu 60 KM po noćenju\nFakultativni izlet El Paso City\nBoravišna taksa (plaća se na recepciji)\nIndividualni troškovi",
            'kids' => 'Djeca do 6,99 god. uz dvije odrasle osobe 50% popusta, od 7 do 11,99 god. 30%. Dijete do 11,99 god. u sobi s jednom odraslom osobom 10% popusta.'],
        ['slug' => 'istanbul-princeva-ostrva-edirne', 'title' => 'Istanbul, Prinčeva ostrva & Edirne', 'type' => 'visednevni', 'price' => 369,
            'terms' => [['2027-01-14', '2027-01-20']],
            'poster' => '2026/11/ISTANBUL-1401.jpg', 'doc' => '2026/11/ISTANBUL-1401.doc',
            'old' => '/aranzman/izleti/visednevni/istanbul-14-01-20-01-2027/',
            'hl' => 'Galata, Taksim, Bosfor i tursko veče', 'from' => 'Tuzla, Srebrenik, Gračanica, Gradačac, Brčko, Bijeljina',
            'summary' => 'Hotel Nanda 3*, Sultanahmet, Sulejmanija, Egipatska pijaca, Galata kula i Istiklal, krstarenje Bosforom, Prinčeva ostrva i Edirne.',
            'inc' => "Prevoz autobusom turističke klase\nHotel 3* u Istanbulu s doručkom\nObilazak kvarta Sultanahmet\nPratilac tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "PZO\nFakultativni izleti\nTursko veče\nDoplata za jednokrevetnu sobu\nIndividualni troškovi"],
        ['slug' => 'bec-bratislava', 'title' => 'Beč & Bratislava', 'type' => 'visednevni', 'price' => 179,
            'terms' => [['2027-01-22', '2027-01-24']],
            'poster' => '2026/11/BE%C4%8C-2201.jpg', 'doc' => '2026/11/BE%C4%8C-2201.docx',
            'old' => '/aranzman/izleti/visednevni/bec-bratislava-22-01-24-01-2027/',
            'hl' => 'Dvorac Belvedere, Bratislavski grad, Eurovea', 'from' => 'Tuzla, Srebrenik, Gračanica, Gradačac, Brčko, Orašje',
            'summary' => 'Belvedere i panoramsko razgledanje Beča, noćenje u hotelu 3*, zatim Bratislava s vodičem i dva sata šopinga u Eurovei.',
            'inc' => "Prevoz autobusom turističke klase (klima, TV, video, WC)\nHotel 3* u Beču, 1 noćenje s doručkom\nPosjeta dvorcu Belvedere\nPratilac tokom putovanja\nTroškovi organizacije i PDV",
            'exc' => "PZO\nFakultativni obilazak s vodičem\nDoplata za jednokrevetnu sobu\nIndividualni troškovi"],
    ];

    /** Destinacija na mapi ruta (naziv, širina, dužina) po adresi aranžmana. */
    private const DESTINATIONS = [
        'graz-maribor-zagreb-novembar' => ['Graz', 47.07070, 15.43950],
        'novi-pazar-zlatibor' => ['Novi Pazar', 43.13670, 20.51220],
        'advent-budimpesta' => ['Budimpešta', 47.49790, 19.04020],
        'advent-shopping-ljubljana' => ['Ljubljana', 46.05690, 14.50580],
        'advent-shopping-graz' => ['Graz', 47.07070, 15.43950],
        'graz-maribor-zagreb-decembar' => ['Maribor', 46.55470, 15.64590],
        'edirne-istanbul' => ['Istanbul', 41.00820, 28.97840],
        'budimpesta-decembar' => ['Budimpešta', 47.49790, 19.04020],
        'graz-klagenfurt' => ['Klagenfurt', 46.62470, 14.30530],
        'nova-godina-istanbul' => ['Istanbul', 41.00820, 28.97840],
        'zima-2027-zlatibor' => ['Zlatibor', 43.72900, 19.70060],
        'istanbul-princeva-ostrva-edirne' => ['Istanbul', 41.00820, 28.97840],
        'bec-bratislava' => ['Beč', 48.20820, 16.37380],
    ];

    /** Kategorije: naziv => [redoslijed, uvod, aranžmani] */
    private const CATEGORIES = [
        'Advent' => [1, 'Božićni sajmovi u Grazu, Budimpešti, Ljubljani, Mariboru i Zagrebu. Jednodnevni i vikend polasci.',
            ['advent-budimpesta', 'advent-shopping-ljubljana', 'advent-shopping-graz', 'graz-maribor-zagreb-novembar', 'graz-maribor-zagreb-decembar', 'budimpesta-decembar', 'graz-klagenfurt']],
        'Shopping' => [2, 'Seiersberg, CityPark, Eurovea i outleti: jednodnevni šoping izleti s polaskom u noći.',
            ['advent-shopping-graz', 'advent-shopping-ljubljana', 'graz-maribor-zagreb-novembar', 'graz-maribor-zagreb-decembar', 'graz-klagenfurt', 'bec-bratislava']],
        'Istanbul' => [3, 'Istanbul, Edirne i Prinčeva ostrva autobusom, s hotelom blizu Kapali čaršije.',
            ['edirne-istanbul', 'nova-godina-istanbul', 'istanbul-princeva-ostrva-edirne']],
        'Nova godina' => [4, 'Dočekajte Novu godinu na putovanju.', ['nova-godina-istanbul']],
        'Zimovanje' => [5, 'Vikendi na snijegu s hotelom i wellnessom.', ['zima-2027-zlatibor']],
        'Vikend putovanja' => [6, 'Dva ili tri dana, polazak u petak ili subotu, povratak u nedjelju.',
            ['graz-maribor-zagreb-novembar', 'graz-maribor-zagreb-decembar', 'graz-klagenfurt', 'novi-pazar-zlatibor', 'bec-bratislava', 'zima-2027-zlatibor']],
        'Ljetovanja' => [7, 'Turska, Jadran, Albanija, Grčka i Tunis. Ponuda za ljeto 2027 uskoro.', []],
    ];

    /** Opšta česta pitanja, iz opštih uslova putovanja i napomena u programima agencije. */
    private const FAQ = [
        ['Kako se prijavljujem za putovanje?', "Telefonom, e-mailom, formom na sajtu ili lično u agenciji u Srebreniku. Prijava je važeća kad je potvrdi agencija i kad zaključimo ugovor (potvrdu) o putovanju, a može biti i elektronski."],
        ['Da li je putničko zdravstveno osiguranje obavezno?', "Da. Svaki putnik mora imati uplaćeno putničko zdravstveno osiguranje, vlastito ili putem agencije. Cijena je navedena u programu svakog putovanja, a osobe starije od 60 godina plaćaju duplu policu."],
        ['Kako se raspoređuju sjedišta u autobusu?', "Redoslijed prijava određuje raspored sjedišta, s tim da agencija pravi raspored po strukturi i broju putnika. Željeno sjedište možete uzeti uz doplatu."],
        ['Odakle su polasci i u koliko sati?', "Polasci su iz Tuzle, Srebrenika, Gračanice, Gradačca, Brčkog i Orašja, a za Istanbul i iz Bijeljine. Tačno vrijeme i mjesto polaska za svaki grad su u programu putovanja."],
        ['Šta ako se ne prijavi dovoljno putnika?', "Cijene su računate na 40 do 45 prijavljenih putnika. S manjim brojem agencija zadržava pravo otkaza putovanja ili korekcije cijene, o čemu vas obavještavamo na vrijeme. Fakultativni izleti se realizuju uz najmanje 25 prijavljenih."],
        ['Koje dokumente trebam?', "Svaki putnik je dužan sam provjeriti važnost svojih putnih isprava i pravila za ulazak u zemlje Evropske unije prije polaska. Ako niste sigurni, pitajte nas prije prijave."],
        ['Ko odgovara za prtljag i lične stvari?', "Putnici sami vode računa o putnim ispravama, novcu i ličnim stvarima tokom putovanja. Prtljag možete osigurati kod osiguravajuće kuće po njihovim uslovima."],
    ];

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $this->siteConfig($output);
        $this->pages($output);
        foreach (self::TRIPS as $data) {
            $this->trip($data, $output);
        }
        $this->destinations($output);
        $this->categories($output);
        $this->faqs($output);
        $this->cleanPrograms($output);
        $this->categoryImages($output);
        $output->writeln('Gotovo.');
        return 0;
    }

    /**
     * Program sa starog sajta: bez podebljanog kurziva i bez tabele usluga (ide u Obuhvata/Ne obuhvata).
     * Dira samo programe koji još imaju oznake starog sajta, ne ono što je agencija uredila u adminu.
     */
    private function cleanPrograms(PolyOutput $output): void
    {
        foreach (Trip::get() as $trip) {
            $html = (string) $trip->Program;
            $fromOldSite = str_contains($html, '<strong><em>') || preg_match('/<table\b[^>]*>.*obuh/isu', $html);
            if (!$fromOldSite) {
                continue;
            }
            $clean = ProgramCleaner::clean($html);
            $trip->Program = $clean['html'];
            if ($clean['includes'] !== '') {
                $trip->Includes = $clean['includes'];
            }
            if ($clean['excludes'] !== '') {
                $trip->Excludes = $clean['excludes'];
            }
            $trip->write();
            $output->writeln('Očišćen program: ' . $trip->Title);
        }
    }

    /** Koordinate za mapu ruta (samo gdje još nisu upisane). */
    private function destinations(PolyOutput $output): void
    {
        foreach (self::DESTINATIONS as $slug => [$name, $lat, $lng]) {
            $trip = Trip::get()->filter('URLSegment', $slug)->first();
            if ($trip && !$trip->DestinationLat) {
                $trip->DestinationName = $name;
                $trip->DestinationLat = $lat;
                $trip->DestinationLng = $lng;
                $trip->write();
            }
        }
    }

    private function categories(PolyOutput $output): void
    {
        foreach (self::CATEGORIES as $title => [$sort, $intro, $slugs]) {
            if (\HazreTours\Model\TripCategory::get()->filter('Title', $title)->exists()) {
                continue;
            }
            $cat = \HazreTours\Model\TripCategory::create(['Title' => $title, 'Sort' => $sort, 'Intro' => $intro, 'ShowOnHome' => true]);
            $cat->write();
            foreach ($slugs as $slug) {
                if ($trip = Trip::get()->filter('URLSegment', $slug)->first()) {
                    $trip->Categories()->add($cat);
                }
            }
            $output->writeln("Kategorija: $title");
        }
    }

    /**
     * Pozadinske fotografije tema sa Unsplasha (adrese slika s njihovog CDN-a) (besplatna licenca, dozvoljeno i za komercijalnu upotrebu).
     * Postavlja se samo tema koja još nema sliku, pa ne dira ono što agencija sama postavi.
     */
    private const CATEGORY_IMAGES = [
        'Advent' => ['photo-1766086893303-1494a76d1980', 'advent-bozicni-sajam'],
        'Shopping' => ['photo-1758274251589-fb70a3654a1b', 'shopping-kese'],
        'Istanbul' => ['photo-1636537511494-c3e558e0702b', 'istanbul-djevojacka-kula'],
        'Nova godina' => ['photo-1783231754573-b5f6672c70ac', 'nova-godina-vatromet'],
        'Zimovanje' => ['photo-1762746445663-67887f9183d0', 'zimovanje-skijaliste'],
        'Vikend putovanja' => ['photo-1541849546-216549ae216d', 'vikend-prag'],
    ];

    private function categoryImages(PolyOutput $output): void
    {
        foreach (self::CATEGORY_IMAGES as $title => [$photoId, $fileName]) {
            $cat = \HazreTours\Model\TripCategory::get()->filter('Title', $title)->first();
            if (!$cat || $cat->ImageID) {
                continue;
            }
            $existing = Image::get()->filter('Name', $fileName . '.jpg')->first();
            if (!$existing) {
                $bytes = $this->fetch('https://images.unsplash.com/' . $photoId . '?w=1920&q=80&fm=jpg&fit=max');
                if ($bytes === null || @getimagesizefromstring($bytes) === false) {
                    $output->writeln("Slika za temu $title nije preuzeta");
                    continue;
                }
                $existing = Image::create();
                $existing->setFromString($bytes, 'teme/' . $fileName . '.jpg');
                $existing->Title = $title . ' (Unsplash)';
                $existing->write();
                $existing->publishSingle();
            }
            $cat->ImageID = $existing->ID;
            $cat->write();
            $output->writeln("Slika teme: $title");
        }
    }

    private function faqs(PolyOutput $output): void
    {
        if (\HazreTours\Model\FaqItem::get()->exists()) {
            return;
        }
        foreach (self::FAQ as $i => [$q, $a]) {
            \HazreTours\Model\FaqItem::create(['Question' => $q, 'Answer' => $a, 'Sort' => $i + 1])->write();
        }
        $output->writeln('Česta pitanja: ' . count(self::FAQ));
    }

    private function siteConfig(PolyOutput $output): void
    {
        $c = SiteConfig::current_site_config();
        $defaults = [
            'Title' => 'Hazre Tours',
            'Tagline' => 'Putnička agencija i prevoznik, Srebrenik',
            'CompanyName' => '„Hazre Tours“ d.o.o. Srebrenik',
            'Address' => 'Bosanskih branilaca b.b., 75350 Srebrenik',
            'Phones' => "035 644 155\n061 938 529\n062 384 484\n035 645 053",
            'ContactEmail' => 'hazretoursagencija@gmail.com',
            'InquiryEmail' => 'hazretoursagencija@gmail.com',
        ];
        $changed = false;
        foreach ($defaults as $field => $value) {
            if (!$c->$field || ($field === 'Title' && $c->Title === 'Your Site Name') || ($field === 'Tagline' && $c->Tagline === 'your tagline here')) {
                $c->$field = $value;
                $changed = true;
            }
        }
        if ($changed) {
            $c->write();
            $output->writeln('Kontakt podaci upisani (Settings → Kontakt).');
        }
    }

    private function pages(PolyOutput $output): void
    {
        if (!HomePage::get()->exists()) {
            $home = HomePage::create([
                'Title' => 'Početna',
                'MenuTitle' => 'Početna',
                'URLSegment' => 'home',
                'Sort' => 1,
                'HeroEyebrow' => 'Putnička agencija i prevoznik · Srebrenik',
                'HeroTitle' => 'Gdje god krenete, *mi vozimo*',
                'HeroLead' => 'Advent u Grazu i Budimpešti, Istanbul, Beč i zimovanje na Zlatiboru. Polasci iz Tuzle, Srebrenika, Gračanice, Gradačca, Brčkog, Orašja i Bijeljine, našim autobusima.',
                'TripsTitle' => 'Aktuelni aranžmani',
            ]);
            $home->write();
            $home->publishRecursive();
            $output->writeln('Stranica: Početna');
        }

        // [adresa, naslov, redoslijed, u meniju, stari sajt, tekst, forma, podnaslov]
        $pages = [
            ['putovanje-po-mjeri', 'Putovanje po mjeri', 2, true, null, $this->customContent(), 'pomjeri', 'Recite nam kuda, kada i s kim, a mi složimo program, prevoz i smještaj.'],
            ['najam-autobusa', 'Najam autobusa', 3, true, null, $this->busContent(), 'autobus', 'Ekskurzije, sportski klubovi, firme, svadbe i transferi, našim autobusima i našim vozačima.'],
            ['o-nama', 'O nama', 4, true, '/o-nama/', $this->aboutContent(), '', 'Prevoznik od 2008., putnička agencija od 2016.'],
            ['uslovi-putovanja', 'Uslovi putovanja', 5, false, '/uslovi-putovanja/', null, '', ''],
            ['politika-privatnosti', 'Politika privatnosti', 6, false, '/politika-privatnositi/', null, '', ''],
        ];
        foreach ($pages as [$slug, $title, $sort, $inMenu, $old, $content, $form, $intro]) {
            if (Page::get()->filter('URLSegment', $slug)->exists()) {
                continue;
            }
            $content ??= $this->oldContent(self::OLD . $old, ['.entry-content', 'article', 'main']);
            $page = Page::create([
                'Title' => $title,
                'URLSegment' => $slug,
                'Sort' => $sort,
                'ShowInMenus' => $inMenu,
                'PageForm' => $form,
                'Intro' => $intro,
                'Content' => $content ?: '<p>Tekst stranice upišite u adminu (Pages).</p>',
            ]);
            $page->write();
            $page->publishRecursive();
            $output->writeln("Stranica: $title" . ($content ? '' : ' (bez teksta, stari sajt nije odgovorio)'));
        }
        // Stranice za greške na bosanskom, u izgledu sajta (statične kopije u assets/error-*.html)
        $errors = [
            404 => ['Stranica nije pronađena', '<p>Link je možda zastario ili je putovanje završeno. Pogledajte <a href="/putovanja">aktuelna putovanja</a> ili <a href="/kalendar">kalendar polazaka</a>.</p>'],
            500 => ['Nešto nije u redu', '<p>Desila se greška na našoj strani. Pokušajte ponovo za minut ili nas pozovite na 035 644 155.</p>'],
        ];
        foreach ($errors as $code => [$title, $content]) {
            if (\SilverStripe\ErrorPage\ErrorPage::get()->filter('ErrorCode', $code)->exists()) {
                continue;
            }
            $page = \SilverStripe\ErrorPage\ErrorPage::create(['Title' => $title, 'ErrorCode' => $code, 'Content' => $content, 'ShowInMenus' => false, 'ShowInSearch' => false, 'Sort' => 90 + intdiv($code, 100)]);
            $page->write();
            $page->publishSingle();
            $output->writeln("Stranica za grešku $code");
        }

        // Galerija: skrivena iz menija dok agencija ne doda fotografije (Pages → Galerija)
        if (!\HazreTours\PageTypes\GalleryPage::get()->exists()) {
            $g = \HazreTours\PageTypes\GalleryPage::create([
                'Title' => 'Galerija',
                'URLSegment' => 'galerija',
                'Sort' => 7,
                'ShowInMenus' => false,
                'Intro' => 'Naša putovanja i naš vozni park.',
                'Content' => '',
            ]);
            $g->write();
            $g->publishRecursive();
            $output->writeln('Stranica: Galerija (dodajte fotografije u Pages → Galerija)');
        }
    }

    private function customContent(): string
    {
        return <<<HTML
<p>Svidio vam se neki od naših aranžmana, ali biste promijenili termin, trajanje ili hotel? Putujete s porodicom, firmom ili udruženjem? Popunite formu ispod i pripremićemo ponudu po vašoj mjeri: prevoz našim autobusom, smještaj, program i vodič.</p>
HTML;
    }

    private function busContent(): string
    {
        return <<<HTML
<p>Hazre Tours je osnovan 2008. godine kao prevoznička firma. Vlastiti vozni park i iskusni vozači su temelj svega što radimo, pa autobus iznajmljujemo i za vaša putovanja.</p>
<h2>Za koga vozimo</h2>
<ul>
<li><strong>Škole:</strong> ekskurzije i izleti, po programu škole ili našem.</li>
<li><strong>Sportski klubovi:</strong> utakmice, turniri i pripreme u BiH i Evropi.</li>
<li><strong>Firme i udruženja:</strong> službena putovanja, team building, sajmovi.</li>
<li><strong>Privatne grupe:</strong> svadbe, proslave, transferi do aerodroma.</li>
</ul>
<p>Pošaljite nam relaciju, datum i broj putnika, a mi vam šaljemo ponudu.</p>
HTML;
    }

    private function trip(array $d, PolyOutput $output): void
    {
        if (Trip::get()->filter('URLSegment', $d['slug'])->exists()) {
            return;
        }
        $trip = Trip::create([
            'Title' => $d['title'],
            'URLSegment' => $d['slug'],
            'Type' => $d['type'],
            'Price' => $d['price'],
            'Highlight' => $d['hl'],
            'Summary' => $d['summary'],
            'Includes' => $d['inc'],
            'Excludes' => $d['exc'],
            'KidsNote' => $d['kids'] ?? '',
            'Badge' => $d['badge'] ?? '',
            'Departures' => $d['from'],
            'PosterIsFlyer' => true,
            'Active' => true,
            'Program' => $this->oldContent(self::OLD . $d['old'], ['#tab-description', '.woocommerce-Tabs-panel--description']),
        ]);
        if ($poster = $this->download(self::UP . $d['poster'], 'aranzmani', Image::class)) {
            $trip->PosterID = $poster->ID;
        }
        if ($doc = $this->download(self::UP . $d['doc'], 'programi', File::class)) {
            $trip->ProgramFileID = $doc->ID;
        }
        $trip->write();
        foreach ($d['terms'] as $t) {
            TripTerm::create([
                'TripID' => $trip->ID,
                'StartDate' => $t[0],
                'EndDate' => $t[1] ?? null,
                'Price' => $t[2] ?? 0,
                'Status' => 'slobodno',
            ])->write();
        }
        $output->writeln('Aranžman: ' . $d['title'] . ($trip->PosterID ? '' : ' (bez plakata)') . ($trip->Program ? '' : ' (bez programa)'));
    }

    /** Preuzme fajl sa starog sajta u assets/<folder>/ i objavi ga. */
    private function download(string $url, string $folder, string $class): ?File
    {
        $name = rawurldecode(basename(parse_url($url, PHP_URL_PATH)));
        $name = \SilverStripe\Assets\FileNameFilter::create()->filter($name);
        if ($existing = File::get()->filter('Name', $name)->first()) {
            return $existing;
        }
        $bytes = $this->fetch($url);
        if ($bytes === null) {
            return null;
        }
        /** @var File $file */
        $file = $class::create();
        $file->setFromString($bytes, $folder . '/' . $name);
        $file->Title = pathinfo($name, PATHINFO_FILENAME);
        $file->write();
        $file->publishSingle();
        return $file;
    }

    /** Sadržaj (HTML) sa stare stranice, očišćen od skripti, stilova i WordPress dodataka. */
    private function oldContent(string $url, array $selectors): string
    {
        $html = $this->fetch($url);
        if (!$html) {
            return '';
        }
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        $xp = new \DOMXPath($doc);
        $node = null;
        foreach ($selectors as $sel) {
            $q = str_starts_with($sel, '#')
                ? "//*[@id='" . substr($sel, 1) . "']"
                : (str_starts_with($sel, '.') ? "//*[contains(concat(' ', normalize-space(@class), ' '), ' " . substr($sel, 1) . " ')]" : '//' . $sel);
            if ($found = $xp->query($q)->item(0)) {
                $node = $found;
                break;
            }
        }
        if (!$node) {
            return '';
        }
        // Skripte (stari sajt ima i ubačen zlonamjerni kod), stilovi, forme i link za preuzimanje se ne prenose
        foreach (['script', 'style', 'iframe', 'form', 'noscript', 'svg', 'button', 'input'] as $tag) {
            foreach (iterator_to_array($node->getElementsByTagName($tag)) as $el) {
                $el->parentNode->removeChild($el);
            }
        }
        $inner = '';
        foreach ($node->childNodes as $child) {
            $inner .= $doc->saveHTML($child);
        }
        $inner = strip_tags($inner, '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4><table><thead><tbody><tr><td><th><a>');
        $inner = preg_replace('/\s(?:style|class|id|data-[\w-]+|width|height|align)="[^"]*"/i', '', $inner);
        $inner = preg_replace('/<a[^>]*\.(?:docx?|pdf)"[^>]*>.*?<\/a>/is', '', $inner);
        $inner = preg_replace('/Download\s*\([^)]*\)/i', '', $inner);
        $inner = preg_replace('/<h2>\s*(Opis|Description)\s*<\/h2>/i', '', $inner);
        $inner = preg_replace('/<(p|li|h\d)>\s*(&nbsp;|\xc2\xa0)?\s*<\/\1>/u', '', $inner);
        return trim($inner);
    }

    private function fetch(string $url): ?string
    {
        $ctx = stream_context_create(['http' => ['timeout' => 30, 'user_agent' => 'Mozilla/5.0 (HazreTours import)', 'follow_location' => 1]]);
        $data = @file_get_contents($url, false, $ctx);
        return $data === false || $data === '' ? null : $data;
    }

    private function aboutContent(): string
    {
        return <<<HTML
<p>Dobrodošli u Hazre Tours, vašeg pouzdanog partnera za nezaboravna putovanja i siguran prevoz.</p>
<p>Priča o nama počinje 2008. godine, kada je Hazre Tours osnovan kao prevoznička firma s ciljem da putnicima pruži udobnost i sigurnost na putu. Prateći potrebe naših vjernih klijenata i želju da ponudimo kompletnu uslugu, od 2016. godine uspješno poslujemo i kao putnička agencija. Spajanjem dugogodišnjeg prevoznog iskustva i ljubavi prema turizmu danas kreiramo aranžmane koji se pamte.</p>
<h2>Naša ponuda</h2>
<ul>
<li><strong>Jednodnevna i višednevna putovanja:</strong> obilasci evropskih metropola i skrivenih dragulja regije.</li>
<li><strong>Ljetovanja:</strong> odmor na najpopularnijim destinacijama.</li>
<li><strong>Hoteli:</strong> rezervacija smještaja u više od 200.000 hotela u 140 zemalja svijeta, od odabira i plaćanja do vaučera.</li>
<li><strong>Iznajmljivanje autobusa:</strong> moderna i tehnički opremljena flota za grupna putovanja, ekskurzije i transfere.</li>
</ul>
<h2>Zašto Hazre Tours?</h2>
<ul>
<li><strong>Iskustvo u prevozu i turizmu:</strong> pouzdanost prevoznika i usluga moderne turističke agencije.</li>
<li><strong>Vlastiti vozni park:</strong> tehnička ispravnost, udobnost i sigurnost na svakom kilometru.</li>
<li><strong>Profesionalan tim:</strong> iskusni vozači, licencirani vodiči i ljubazno osoblje.</li>
<li><strong>Dobar omjer cijene i kvaliteta:</strong> aranžmani dostupni svima, uz visok nivo usluge.</li>
</ul>
HTML;
    }
}
