# Hazre Tours (hazretours.ba)

Novi sajt putničke agencije Hazre Tours d.o.o. Srebrenik: aranžmani s terminima, forma za rezervaciju i CMS u kojem agencija sama mijenja sadržaj.

## Lokalni razvoj

Potrebno: Docker Desktop i [DDEV](https://ddev.com).

```bash
ddev start
ddev composer install
ddev exec vendor/bin/sake db:build --flush
ddev exec vendor/bin/sake tasks:hazre-import
```

- Sajt: http://localhost:8097 (ili http://hazre-tours.ddev.site:33000)
- Admin: http://localhost:8097/admin (pristup iz `.env`: `SS_DEFAULT_ADMIN_USERNAME` / `SS_DEFAULT_ADMIN_PASSWORD`)
- Mailpit (lokalni mailovi, npr. upiti iz forme): `ddev mailpit`

`tasks:hazre-import` prebaci sa starog sajta kontakt podatke, stranice i aranžmane (plakati, programi u Wordu, program po danima). Pravi samo ono što još ne postoji, pa ne prepisuje izmjene iz admina.

## Šta se gdje uređuje (admin)

- **Aranžmani i upiti** → Aranžmani: naziv, kategorije, cijena i akcija (stara cijena, odbrojavanje), plakat i galerija, program (Word/PDF i po danima), termini (datum, cijena, slobodna mjesta, popunjenost), pitanja za taj aranžman, tačka na mapi ruta. Prošli termini sami nestaju sa sajta.
- **Aranžmani i upiti** → Upiti: rezervacije, putovanja po mjeri, izmjene aranžmana i najam autobusa (status, bilješka agencije, izvoz u Excel). Svaki upit stiže i mailom.
- **Aranžmani i upiti** → Kategorije (Advent, Shopping…), Česta pitanja, Utisci putnika, Newsletter (izvoz adresa).
- **Pages**: Početna (naslov, tekst, slika u vrhu), Putovanje po mjeri, Najam autobusa, O nama, Galerija, Uslovi putovanja, Politika privatnosti. Svaka stranica može imati formu ispod teksta.
- **Settings → Kontakt**: adresa, telefoni, Viber/WhatsApp, e-mail za upite, licenca, mreže, Google recenzije.
- **Settings → Akcija**: traka na vrhu sajta s odbrojavanjem (Black Friday, First minute).
- **Settings → Analitika**: Google Analytics 4 i Meta Pixel (pokreću se tek kad posjetilac prihvati kolačiće).

## Na sajtu

- Mapa ruta u vrhu početne: stvarne koordinate destinacija i udaljenost od Srebrenika.
- Ploča polazaka (kao na stanici) i kalendar polazaka (`/kalendar`).
- Pretraga preko cijelog ekrana (Ctrl+K), filter po vrsti, mjesecu i mjestu polaska.
- Stranica aranžmana: termini sa slobodnim mjestima, program po danima, česta pitanja, dijeljenje na Viber/WhatsApp/Facebook, schema.org podaci za Google.

## Struktura

- `app/src/Model`: Aranžman (`Trip`), termin (`TripTerm`), upit (`Inquiry`)
- `app/src/Control`: stranica aranžmana (`/aranzman/<naziv>`), prijem forme (`/upit`), `robots.txt` i `sitemap.xml`
- `app/src/PageTypes/HomePage.php`: početna
- `app/src/Tasks/ImportTask.php`: uvoz sa starog sajta
- `app/templates`: izgled (Silverstripe šabloni), `public/css` i `public/js`
- `deploy/`: postavljanje na hosting i backup na Google Drive (vidi `deploy/UPUTSTVO.txt`)
- `docs/prototip`: prvi statični nacrti dizajna

## Način rada

Radi se direktno na `main`.
