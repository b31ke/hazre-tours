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

- **Aranžmani i upiti** → Aranžmani: naziv, cijena, plakat, program (Word/PDF), šta obuhvata, termini (datum, cijena, popunjenost). Prošli termini sami nestaju sa sajta.
- **Aranžmani i upiti** → Upiti: rezervacije iz forme sa sajta (status, bilješka agencije). Svaki upit stiže i mailom.
- **Pages**: Početna (naslov, tekst, slika u vrhu), O nama, Uslovi putovanja, Politika privatnosti.
- **Settings → Kontakt**: adresa, telefoni, e-mail, e-mail za upite, Facebook/Instagram.

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
