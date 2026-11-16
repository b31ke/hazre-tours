<section class="hero" aria-labelledby="hero-h">
    <% if $HeroImage.exists %><div class="hero-photo" style="background-image:url('$HeroImage.ScaleMaxWidth(2000).Convert('webp').URL')" aria-hidden="true"></div><% end_if %>
    <canvas class="routes" id="routes" aria-hidden="true"></canvas>
    <script type="application/json" id="map-data">$MapJSON.RAW</script>
    <div class="wrap hero-in">
        <div class="hero-copy">
            <p class="eyebrow hero-eyebrow"><span class="dot-live"></span>$HeroEyebrow</p>
            <h1 id="hero-h" class="hero-h">$HeroTitleHTML</h1>
            <% if $HeroLead %><p class="hero-lead">$HeroLead</p><% end_if %>
        </div>

        <form class="finder glass" id="finder" role="search" action="putovanja">
            <label class="f-cell f-q"><span>Kuda putujete?</span><input id="s-q" type="search" placeholder="Istanbul, advent, Zlatibor…" autocomplete="off"></label>
            <label class="f-cell"><span>Vrsta</span>
                <select id="s-type"><option value="sve">Sva putovanja</option><option value="jednodnevni">Jednodnevni</option><option value="visednevni">Višednevni</option><option value="ljetovanje">Ljetovanja</option></select>
            </label>
            <label class="f-cell"><span>Mjesec</span><select id="s-month"><option value="">Bilo kada</option><% loop $SearchMonths %><option value="$Value">$Label</option><% end_loop %></select></label>
            <label class="f-cell"><span>Polazak iz</span>
                <select id="s-from"><option value="">Bilo gdje</option><% loop $SearchCities %><option>$Name</option><% end_loop %></select>
            </label>
            <button class="btn btn-sun f-go" type="submit"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>Pretraži</button>
        </form>

        <ul class="stats" aria-label="Hazre Tours u brojevima">
            <li><b data-count="$YearsOnRoad">$YearsOnRoad</b><span>godina na putu</span></li>
            <li><b data-count="$UpcomingTrips.Count">$UpcomingTrips.Count</b><span>aktivnih aranžmana</span></li>
            <li><b data-count="$DestinationCount">$DestinationCount</b><span>destinacija</span></li>
            <li><b data-count="$CityCount">$CityCount</b><span>mjesta polaska</span></li>
        </ul>
    </div>
    <div class="hero-legend mono" aria-hidden="true"><span class="lg-home"></span>SREBRENIK 44.71°N 18.49°E</div>
</section>

<% if $NextDepartures.Count %>
<section class="block board-sec" aria-labelledby="board-h">
    <div class="wrap">
        <div class="sec-head">
            <div><p class="eyebrow">Uživo</p><h2 id="board-h" class="h2">Najbliži polasci</h2></div>
            <a class="btn btn-ghost" href="kalendar">Kalendar polazaka →</a>
        </div>
        <% include DepartureBoard Departures=$NextDepartures %>
    </div>
</section>
<% end_if %>

<% if $HomeCategories.Count %>
<section class="block cats-sec" aria-labelledby="cats-h">
    <div class="wrap">
        <div class="sec-head"><div><p class="eyebrow">Teme</p><h2 id="cats-h" class="h2">Šta vas vuče na put?</h2></div></div>
        <div class="cats">
            <% loop $HomeCategories %>
            <a class="cat" href="$Link" data-reveal data-tilt>
                <% with $CoverTrip %><img src="$CardImageURL" alt="" loading="lazy" class="<% if $PosterIsFlyer %>flyer<% end_if %>"><% end_with %>
                <span class="cat-n mono">$TripCount</span>
                <span class="cat-t">$Title</span>
                <span class="cat-go" aria-hidden="true">→</span>
            </a>
            <% end_loop %>
        </div>
    </div>
</section>
<% end_if %>

<section class="block" id="aranzmani" aria-labelledby="ar-h">
    <div class="wrap">
        <div class="sec-head">
            <div><p class="eyebrow">Za vas izdvajamo</p><h2 id="ar-h" class="h2">$TripsTitle</h2></div>
            <div class="chips" role="group" aria-label="Vrsta putovanja">
                <button class="chip" type="button" data-type="sve" aria-pressed="true">Sve</button>
                <button class="chip" type="button" data-type="jednodnevni" aria-pressed="false">Jednodnevni</button>
                <button class="chip" type="button" data-type="visednevni" aria-pressed="false">Višednevni</button>
            </div>
        </div>
        <p class="result-note" id="result-note" hidden></p>
        <div class="grid" id="grid">
            <% loop $UpcomingTrips %><% include TripCard %><% end_loop %>
            <div class="empty" id="empty"<% if $UpcomingTrips.Count %> hidden<% end_if %>>Za ovu pretragu trenutno nemamo polazak. <a href="putovanje-po-mjeri">Pošaljite upit za putovanje po mjeri →</a></div>
        </div>
        <% if $Content %><div class="typo home-text">$Content</div><% end_if %>
    </div>
</section>

<section class="block why" aria-labelledby="why-h">
    <div class="wrap why-grid">
        <div class="why-copy" data-reveal>
            <p class="eyebrow">Zašto Hazre Tours</p>
            <h2 id="why-h" class="h2">Prvo prevoznik, onda agencija</h2>
            <p class="muted">Od 2008. vozimo putnike, a od 2016. pravimo i putovanja. Zato je autobus naš, vozač naš i plan puta naš: ne zavisimo od tuđeg prevoza i znamo svaki kilometar rute.</p>
            <a class="btn btn-ghost" href="o-nama">Više o nama →</a>
        </div>
        <div class="why-list">
            <div class="why-item glass" data-reveal><span class="why-ico">01</span><h3>Vlastiti vozni park</h3><p>Turistički autobusi s klimom, TV-om i WC-om, tehnički pregledani prije svakog puta.</p></div>
            <div class="why-item glass" data-reveal><span class="why-ico">02</span><h3>Polasci iz vašeg grada</h3><p>Tuzla, Srebrenik, Gračanica, Gradačac, Brčko, Orašje i Bijeljina, bez presjedanja.</p></div>
            <div class="why-item glass" data-reveal><span class="why-ico">03</span><h3>Vodič i pratilac</h3><p>Na grupnim putovanjima s vama je naš pratilac od polaska do povratka.</p></div>
            <div class="why-item glass" data-reveal><span class="why-ico">04</span><h3>Jasne cijene</h3><p>U programu piše šta je uključeno, a šta se plaća posebno. Bez iznenađenja na putu.</p></div>
        </div>
    </div>
</section>

<section class="block services" aria-labelledby="svc-h">
    <div class="wrap">
        <div class="sec-head"><div><p class="eyebrow">Usluge</p><h2 id="svc-h" class="h2">Sve za put na jednom mjestu</h2></div></div>
        <div class="svc-grid">
            <a class="svc glass" href="putovanja" data-reveal data-tilt><span class="svc-k mono">IZLETI</span><h3>Izleti i putovanja</h3><p>Advent, šoping, evropske metropole i vikend ture s vodičem.</p><span class="go">→</span></a>
            <a class="svc glass" href="putovanje-po-mjeri" data-reveal data-tilt><span class="svc-k mono">PO MJERI</span><h3>Putovanje po mjeri</h3><p>Vaš termin, vaš hotel, vaš program. Za porodice, firme i udruženja.</p><span class="go">→</span></a>
            <a class="svc glass" href="najam-autobusa" data-reveal data-tilt><span class="svc-k mono">AUTOBUS</span><h3>Najam autobusa</h3><p>Ekskurzije, sportski klubovi, svadbe i transferi.</p><span class="go">→</span></a>
            <a class="svc glass" href="putovanje-po-mjeri" data-reveal data-tilt><span class="svc-k mono">HOTELI</span><h3>Hoteli i ljetovanja</h3><p>Više od 200.000 hotela u 140 zemalja, uz vaučer iz agencije.</p><span class="go">→</span></a>
        </div>
    </div>
</section>

<% if $PublishedReviews.Count %>
<section class="block reviews" aria-labelledby="rev-h">
    <div class="wrap">
        <div class="sec-head">
            <div><p class="eyebrow">Utisci putnika</p><h2 id="rev-h" class="h2">Šta kažu oni koji su putovali</h2></div>
            <% if $SiteConfig.GoogleReviewsURL %><a class="btn btn-ghost" href="$SiteConfig.GoogleReviewsURL" rel="noopener">Sve recenzije na Googleu →</a><% end_if %>
        </div>
        <div class="rev-track">
            <% loop $PublishedReviews %>
            <figure class="rev glass" data-reveal>
                <div class="stars" aria-label="Ocjena $Rating od 5">$Stars</div>
                <blockquote>$Text</blockquote>
                <figcaption><span class="av">$Initial</span><span><b>$Name</b><% if $City %>, $City<% end_if %><% if $TripTitle %><small>$TripTitle</small><% end_if %></span></figcaption>
            </figure>
            <% end_loop %>
        </div>
    </div>
</section>
<% end_if %>

<% if $GalleryPreview.Count %>
<section class="block gallery-sec" aria-labelledby="gal-h">
    <div class="wrap">
        <div class="sec-head"><div><p class="eyebrow">Galerija</p><h2 id="gal-h" class="h2">S naših putovanja</h2></div><a class="btn btn-ghost" href="$GalleryLink">Cijela galerija →</a></div>
        <div class="gallery">
            <% loop $GalleryPreview %><a href="$ScaleMaxWidth(1800).URL" class="g-item" data-lightbox="home"><img src="$Fill(600,600).Convert('webp').URL" alt="$Title.ATT" loading="lazy"></a><% end_loop %>
        </div>
    </div>
</section>
<% end_if %>

<section class="block faq-sec" aria-labelledby="faq-h">
    <div class="wrap faq-grid">
        <div data-reveal>
            <p class="eyebrow">Prije puta</p>
            <h2 id="faq-h" class="h2">Česta pitanja</h2>
            <p class="muted">Sve što trebate znati o prijavi, osiguranju, sjedištima i dokumentima.</p>
            <a class="btn btn-ghost" href="uslovi-putovanja">Opšti uslovi putovanja →</a>
        </div>
        <% include FaqList Items=$GeneralFaqs %>
    </div>
</section>

<% include ContactSection FormType='rezervacija' %>
