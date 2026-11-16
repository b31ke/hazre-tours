<section class="hero">
    <div class="bg<% if not $HeroImage.exists %> flyer<% end_if %>" aria-hidden="true" style="background-image:url('<% if $HeroImage.exists %>$HeroImage.ScaleMaxWidth(1800).URL<% else %>$UpcomingTrips.First.CardImageURL<% end_if %>')"></div>
    <div class="wrap">
        <% if $HeroEyebrow %><p class="eyebrow sun">$HeroEyebrow</p><% end_if %>
        <h1>$HeroTitleHTML</h1>
        <% if $HeroLead %><p class="lead">$HeroLead</p><% end_if %>
        <form class="search" id="search" role="search" action="#aranzmani">
            <label for="s-q"><span>Kuda putujete?</span><input id="s-q" type="search" placeholder="Istanbul, Graz, Zlatibor…" autocomplete="off"></label>
            <label for="s-type"><span>Vrsta</span>
                <select id="s-type"><option value="sve">Sva putovanja</option><option value="jednodnevni">Jednodnevni</option><option value="visednevni">Višednevni</option><option value="ljetovanje">Ljetovanja</option></select>
            </label>
            <label for="s-month"><span>Mjesec</span><select id="s-month"><option value="">Bilo kada</option><% loop $SearchMonths %><option value="$Value">$Label</option><% end_loop %></select></label>
            <label for="s-from"><span>Polazak iz</span>
                <select id="s-from"><option value="">Bilo gdje</option><% loop $SearchCities %><option>$Name</option><% end_loop %></select>
            </label>
            <button class="btn" type="submit">Pretraži</button>
        </form>
        <div class="since">
            <div><b>2008.</b>prevoznik</div>
            <div><b>2016.</b>putnička agencija</div>
            <div><b>Vlastiti</b>vozni park</div>
        </div>
    </div>
</section>

<% if $NextDepartures.Count %>
<section class="next" aria-label="Najbliži polasci">
    <div class="wrap">
        <h2>Najbliži polasci</h2>
        <% loop $NextDepartures %>
            <a class="pill" href="$Trip.Link"><span class="d">$StartDate.Format('dd.MM.')</span>$Trip.Title <span class="countdown">· $Countdown</span></a>
        <% end_loop %>
    </div>
</section>
<% end_if %>

<section class="block" id="aranzmani" aria-labelledby="ar-title">
    <div class="wrap">
        <div class="sec-head">
            <div>
                <p class="eyebrow">Za vas izdvajamo</p>
                <h2 id="ar-title">$TripsTitle</h2>
            </div>
            <div class="chips" role="group" aria-label="Vrsta putovanja">
                <button class="chip" type="button" data-type="sve" aria-pressed="true">Sve</button>
                <button class="chip" type="button" data-type="jednodnevni" aria-pressed="false">Jednodnevni</button>
                <button class="chip" type="button" data-type="visednevni" aria-pressed="false">Višednevni</button>
            </div>
        </div>
        <p class="result-note" id="result-note" hidden></p>
        <div class="grid" id="grid">
            <% loop $UpcomingTrips %><% include TripCard %><% end_loop %>
            <div class="empty" id="empty"<% if $UpcomingTrips.Count %> hidden<% end_if %>>Za ovu pretragu trenutno nemamo polazak. Pošaljite nam upit pa ćemo vam javiti kad bude novi termin.</div>
        </div>
        <% if $Content %><div class="typography home-text">$Content</div><% end_if %>
    </div>
</section>

<section class="block services" id="usluge" aria-labelledby="svc-title">
    <div class="wrap">
        <div class="sec-head"><div><p class="eyebrow">Usluge</p><h2 id="svc-title">Sve za vaše putovanje na jednom mjestu</h2></div></div>
        <div class="svc-grid">
            <div class="svc">
                <div class="ico"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="4" y="3" width="16" height="15" rx="3"/><path d="M4 11h16M8 21v-3M16 21v-3"/><circle cx="8" cy="14.5" r="1"/><circle cx="16" cy="14.5" r="1"/></svg></div>
                <h3>Izleti i putovanja</h3>
                <p>Jednodnevni i višednevni izleti u evropske metropole i gradove regije, s vodičem i pratiocem.</p>
                <a href="#aranzmani">Pogledaj aranžmane →</a>
            </div>
            <div class="svc">
                <div class="ico"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M2 20c2-2 4-2 6 0s4 2 6 0 4-2 6 0"/></svg></div>
                <h3>Ljetovanja</h3>
                <p>Turska, Jadran, Albanija, Grčka i Tunis. Za ponudu i rani upit nas kontaktirajte.</p>
                <a href="#kontakt" data-msg="Zanima me ljetovanje.">Pošalji upit za ljeto →</a>
            </div>
            <div class="svc">
                <div class="ico"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 21V8l9-5 9 5v13"/><path d="M9 21v-6h6v6"/></svg></div>
                <h3>Hoteli</h3>
                <p>Rezervacija smještaja u više od 200.000 hotela u 140 zemalja, od izbora i plaćanja do vaučera.</p>
                <a href="#kontakt" data-msg="Trebam rezervaciju hotela.">Rezerviši hotel →</a>
            </div>
            <div class="svc">
                <div class="ico"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 17V7a2 2 0 0 1 2-2h11l5 5v7"/><path d="M3 12h18"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg></div>
                <h3>Iznajmljivanje autobusa</h3>
                <p>Moderna i tehnički ispravna flota za ekskurzije, grupna putovanja i transfere.</p>
                <a href="#kontakt" data-msg="Zanima me najam autobusa.">Zatraži ponudu →</a>
            </div>
        </div>
    </div>
</section>

<section class="block info" id="vazno" aria-labelledby="info-title">
    <div class="wrap">
        <div class="sec-head">
            <div><p class="eyebrow sun">Prije puta</p><h2 id="info-title">Važno za sve aranžmane</h2></div>
            <a class="btn ghost light" href="uslovi-putovanja/">Opšti uslovi putovanja</a>
        </div>
        <div class="info-grid">
            <div><b>Putničko zdravstveno osiguranje</b><p>Svaki putnik mora imati uplaćeno PZO, vlastito ili putem agencije. Osobe starije od 60 godina plaćaju duplu policu.</p></div>
            <div><b>Raspored sjedišta</b><p>Redoslijed prijava određuje raspored u autobusu. Željeno sjedište možete uzeti uz doplatu.</p></div>
            <div><b>Minimalan broj putnika</b><p>Cijene su računate na 40 do 45 prijavljenih putnika. Fakultativni izleti se realizuju uz najmanje 25 prijavljenih.</p></div>
            <div><b>Putne isprave</b><p>Provjerite važnost dokumenata i pravila ulaska u zemlje Evropske unije prije polaska.</p></div>
        </div>
    </div>
</section>

<% include InquiryForm %>
