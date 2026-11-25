<% if $SiteConfig.PromoVisible %>
<section class="promo-hero">
    <div class="wrap promo-hero-in">
        <p class="promo-kicker">Akcija</p>
        <h2>$SiteConfig.PromoTitle</h2>
        <% if $SiteConfig.PromoText %><p class="promo-sub">$SiteConfig.PromoText</p><% end_if %>
        <% if $SiteConfig.PromoUntil %><p class="countdown" data-until="$SiteConfig.PromoUntilISO">Akcija traje još <b data-cd="d">-</b> d <b data-cd="h">--</b> h <b data-cd="m">--</b> min <b data-cd="s">--</b> s</p><% end_if %>
        <a class="btn btn-white" href="<% if $SiteConfig.PromoLink %>$SiteConfig.PromoLink<% else %>#aranzmani<% end_if %>">Više informacija →</a>
    </div>
</section>
<% end_if %>

<% if $HeroMode == 'klasicni' %>
    <% include HeroClassic %>
<% else %>
    <% include HeroSlider %>
<% end_if %>

<section class="block" id="aranzmani" aria-labelledby="ar-h">
    <div class="wrap">
        <div class="finder">
            <p class="script-title" aria-hidden="true">Pronađi, rezerviši, spakuj kofer</p>
            <h2 id="ar-h" class="sr">Aktuelni aranžmani</h2>
            <div class="sb-wrap">
                <form class="search-bar wide" id="search" role="search" action="putovanja">
                    <label class="sr" for="s-q">Pronađi svoje putovanje</label>
                    <input id="s-q" type="search" placeholder="Kuda putujete? Istanbul, advent, Zlatibor…" data-ph-short="Kuda putujete?" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="sb-res" aria-autocomplete="list">
                    <button class="sb-filter" type="button" id="filter-btn" aria-expanded="false" aria-controls="filter-panel" aria-label="Još filtera: mjesec i mjesto polaska" title="Mjesec i mjesto polaska"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg><span>Filter</span></button>
                    <button class="btn btn-royal" type="submit" aria-label="Traži"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></button>
                </form>
                <div class="sb-drop" id="sb-drop" hidden>
                    <ul class="sb-results" id="sb-res" role="listbox" aria-label="Putovanja"></ul>
                    <p class="sb-foot">Nema onoga što tražite? <a href="putovanje-po-mjeri">Pošaljite upit za putovanje po mjeri →</a></p>
                </div>
            </div>
            <div class="cat-tabs" role="group" aria-label="Kategorije putovanja">
                <button type="button" class="ct" data-cat="" aria-pressed="true">Sve ponude</button>
                <button type="button" class="ct" data-type="jednodnevni" aria-pressed="false">Jednodnevni izleti</button>
                <% loop $MenuCategories %><button type="button" class="ct" data-cat="$URLSegment" aria-pressed="false">$Title</button><% end_loop %>
            </div>
        </div>
        <div class="filter-panel" id="filter-panel" hidden>
            <label><span>Vrsta putovanja</span>
                <select id="s-type"><option value="sve">Sva putovanja</option><option value="jednodnevni">Jednodnevni izleti</option><option value="visednevni">Višednevna putovanja</option><option value="ljetovanje">Ljetovanja</option></select>
            </label>
            <label><span>Mjesec</span><select id="s-month"><option value="">Bilo kada</option><% loop $SearchMonths %><option value="$Value">$Label</option><% end_loop %></select></label>
            <label><span>Polazak iz</span><select id="s-from"><option value="">Bilo gdje</option><% loop $SearchCities %><option>$Name</option><% end_loop %></select></label>
        </div>
        <p class="result-note" id="result-note" hidden></p>
        <div class="grid home-grid" id="grid">
            <% loop $GridTrips %><% include TripCard %><% end_loop %>
            <div class="empty" id="empty"<% if $UpcomingTrips.Count %> hidden<% end_if %>>Za ovaj izbor trenutno nemamo polazak. <a href="putovanje-po-mjeri">Pošaljite upit za putovanje po mjeri →</a></div>
        </div>
        <% if $AllTripsCount > 4 %><div class="all-trips"><a class="btn btn-ghost btn-lg" href="putovanja">Pogledaj sva putovanja ($AllTripsCount)</a></div><% end_if %>
        <% if $Content %><div class="typo home-text">$Content</div><% end_if %>
    </div>
</section>

<% if $HomeCategories.Count %>
<section class="block alt" aria-labelledby="cats-h">
    <div class="wrap">
        <div class="center-head">
            <h2 id="cats-h">Pronađite svoju sljedeću destinaciju</h2>
            <p>Izaberite temu i pogledajte sva putovanja iz te ponude.</p>
        </div>
        <div class="bento">
            <% loop $HomeCategories %>
            <a class="tile t-$Pos<% if not $TileImageURL %> no-img<% end_if %>" href="$Link">
                <% if $TileImageURL %><img class="tile-bg" src="$TileImageURL" alt="" loading="lazy"><% end_if %>
                <span class="tile-count">$TripCount <% if $TripCount == 1 %>polazak<% else_if $TripCount < 5 %>polaska<% else %>polazaka<% end_if %></span>
                <span class="tile-body">
                    <span class="tile-title">$Title</span>
                    <% if $Pos == 1 %><span class="tile-list"><% loop $TopTrips %><span>$Title</span><% end_loop %></span><% end_if %>
                    <span class="tile-row">
                        <span class="tile-from">od <b>$MinPrice KM</b></span>
                        <span class="tile-go">Pogledaj <span aria-hidden="true">→</span></span>
                    </span>
                </span>
            </a>
            <% end_loop %>
        </div>
    </div>
</section>
<% end_if %>

<section class="why" aria-labelledby="why-h">
    <svg class="why-wave top" viewBox="0 0 1440 70" preserveAspectRatio="none" aria-hidden="true"><path d="M0 0 H1440 V30 C 1200 70, 960 70, 720 40 S 240 0, 0 40 Z" fill="#F3F7FC"/></svg>
    <div class="wrap why-grid">
        <div class="why-copy">
            <p class="why-kicker">Zašto baš Hazre Tours?</p>
            <h2 id="why-h">Prvo prevoznik,<br><em>onda agencija</em></h2>
            <p class="why-lead">Od 1996. vozimo putnike, a od 2016. pravimo i putovanja. Autobus je naš, vozač je naš i plan puta je naš, pa ne zavisimo od tuđeg prevoza i znamo svaki kilometar rute.</p>
            <ul class="why-stats">
                <li><b>$YearsOnRoad</b><span>godina na putu</span></li>
                <li><b>$CityCount</b><span>mjesta polaska</span></li>
                <li><b>$AllTripsCount</b><span>aktivnih putovanja</span></li>
            </ul>
            <a class="btn btn-orange btn-lg" href="o-nama">Upoznajte nas</a>
        </div>
        <div class="why-cards">
            <div class="wcard"><span class="wc-ico o"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="3" width="16" height="15" rx="3"/><path d="M4 11h16M8 21v-3M16 21v-3"/><circle cx="8" cy="14.5" r="1"/><circle cx="16" cy="14.5" r="1"/></svg></span><h3>Vlastiti autobusi</h3><p>Turistički autobusi s klimom, TV-om i WC-om, tehnički pregledani prije svakog puta.</p></div>
            <div class="wcard"><span class="wc-ico b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="2.2"/><path d="M12 14.2V21M9.9 11.4 3.4 9.8M14.1 11.4l6.5-1.6"/></svg></span><h3>Iskusni vozači</h3><p>Vozači s dugogodišnjim iskustvom, koji ove rute znaju napamet.</p></div>
            <div class="wcard"><span class="wc-ico b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l7 3v5.5c0 4.4-3 8-7 9.5-4-1.5-7-5.1-7-9.5V6z"/><path d="M8.8 12.2l2.2 2.2 4.4-4.6"/></svg></span><h3>Sigurnost i pouzdanost</h3><p>Vozimo od 1996. Polazimo na vrijeme i držimo se dogovorenog plana puta.</p></div>
            <div class="wcard"><span class="wc-ico o"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 4.5-6 8-6s7 2 8 6"/></svg></span><h3>Vodič i pratilac</h3><p>Na grupnim putovanjima s vama je naš pratilac, od polaska do povratka.</p></div>
            <div class="wcard"><span class="wc-ico o"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="6" width="18" height="13" rx="2.5"/><path d="M3 10.5h18M7 15h4"/></svg></span><h3>Fleksibilno plaćanje</h3><p>Pri prijavi uplaćujete akontaciju, a ostatak najkasnije 21 dan prije polaska.</p></div>
            <div class="wcard"><span class="wc-ico b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 14v-2a8 8 0 0 1 16 0v2"/><rect x="3" y="13.5" width="4" height="6" rx="1.6"/><rect x="17" y="13.5" width="4" height="6" rx="1.6"/><path d="M19 19.5c0 1.2-1.6 2-4 2h-2"/></svg></span><h3>Podrška i brza rezervacija</h3><p>Rezervišite telefonom, e-mailom ili preko sajta. Odgovaramo istog radnog dana.</p></div>
        </div>
    </div>
    <div class="road" aria-hidden="true">
        <div class="road-line"></div>
        <svg class="road-bus" viewBox="0 0 120 52"><rect x="4" y="6" width="104" height="34" rx="9" fill="#F7A11A"/><rect x="12" y="12" width="16" height="12" rx="2" fill="#E3F6FC"/><rect x="32" y="12" width="16" height="12" rx="2" fill="#E3F6FC"/><rect x="52" y="12" width="16" height="12" rx="2" fill="#E3F6FC"/><rect x="72" y="12" width="16" height="12" rx="2" fill="#E3F6FC"/><path d="M92 12h8a6 6 0 0 1 6 6v6H92z" fill="#E3F6FC"/><rect x="4" y="29" width="104" height="4" fill="#fff" opacity=".85"/><circle cx="28" cy="42" r="7" fill="#132E7E"/><circle cx="28" cy="42" r="3" fill="#fff"/><circle cx="86" cy="42" r="7" fill="#132E7E"/><circle cx="86" cy="42" r="3" fill="#fff"/></svg>
    </div>
    <svg class="why-wave bottom" viewBox="0 0 1440 70" preserveAspectRatio="none" aria-hidden="true"><path d="M0 70 H1440 V40 C 1200 0, 960 0, 720 30 S 240 70, 0 30 Z" fill="#F3F7FC"/></svg>
</section>

<% if $PublishedReviews.Count %>
<section class="block alt" aria-labelledby="rev-h">
    <div class="wrap">
        <div class="center-head">
            <h2 id="rev-h">Utisci naših putnika</h2>
            <% if $SiteConfig.GoogleReviewsURL %><a class="link-more" href="$SiteConfig.GoogleReviewsURL" rel="noopener">Sve recenzije na Googleu →</a><% end_if %>
        </div>
        <div class="reviews">
            <% loop $PublishedReviews %>
            <figure class="review">
                <div class="stars" aria-label="Ocjena $Rating od 5">$Stars</div>
                <blockquote>$Text</blockquote>
                <figcaption><b>$Name</b><% if $City %>, $City<% end_if %><% if $TripTitle %><small>$TripTitle</small><% end_if %></figcaption>
            </figure>
            <% end_loop %>
        </div>
    </div>
</section>
<% end_if %>

<% if $GalleryPreview.Count %>
<section class="block alt" aria-labelledby="gal-h">
    <div class="wrap">
        <div class="center-head"><h2 id="gal-h">Galerija</h2><a class="link-more" href="$GalleryLink">Cijela galerija →</a></div>
        <div class="gallery">
            <% loop $GalleryPreview %><a href="$ScaleMaxWidth(1800).URL" class="g-item" data-lightbox="home"><img src="$Fill(600,600).Convert('webp').URL" alt="$Title.ATT" loading="lazy"></a><% end_loop %>
        </div>
    </div>
</section>
<% end_if %>

<section class="block alt svc-block" aria-labelledby="svc-h">
    <div class="wrap">
        <div class="center-head"><p class="script-kicker">Šta sve radimo</p><h2 id="svc-h">Naše usluge</h2></div>
        <div class="services">
            <a class="service s-o" href="putovanja">
                <span class="svc-no">01</span>
                <span class="svc-ico" aria-hidden="true"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3z"/></svg></span>
                <h3>Izleti i putovanja</h3><p>Advent, šoping, evropski gradovi i vikend ture s vodičem.</p>
                <span class="svc-go">Pogledaj ponudu <i aria-hidden="true">→</i></span>
            </a>
            <a class="service s-b" href="putovanje-po-mjeri">
                <span class="svc-no">02</span>
                <span class="svc-ico" aria-hidden="true"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-5.6-7-11a7 7 0 0 1 14 0c0 5.4-7 11-7 11z"/><path d="m9.5 10 1.8 1.8L15 8.2"/></svg></span>
                <h3>Putovanje po mjeri</h3><p>Vaš termin, vaš hotel, vaš program. Za porodice, firme i udruženja.</p>
                <span class="svc-go">Pošalji upit <i aria-hidden="true">→</i></span>
            </a>
            <a class="service s-r" href="najam-autobusa">
                <span class="svc-no">03</span>
                <span class="svc-ico" aria-hidden="true"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="13" rx="3"/><path d="M3 11h18M8 4v7M16 4v7"/><circle cx="7.5" cy="19" r="1.6"/><circle cx="16.5" cy="19" r="1.6"/></svg></span>
                <h3>Najam autobusa</h3><p>Ekskurzije, sportski klubovi, svadbe i transferi.</p>
                <span class="svc-go">Zatraži ponudu <i aria-hidden="true">→</i></span>
            </a>
            <a class="service s-t" href="putovanje-po-mjeri">
                <span class="svc-no">04</span>
                <span class="svc-ico" aria-hidden="true"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="17" cy="6" r="3"/><path d="M2 20c3-2 5-2 7 0s4 2 7 0 4-2 6 0"/><path d="M3 16c3-2 5-2 7 0s4 2 7 0"/></svg></span>
                <h3>Hoteli i ljetovanja</h3><p>Više od 200.000 hotela u 140 zemalja, uz vaučer iz agencije.</p>
                <span class="svc-go">Pošalji upit <i aria-hidden="true">→</i></span>
            </a>
        </div>
    </div>
</section>

<% include ContactSection FormType='rezervacija' %>
