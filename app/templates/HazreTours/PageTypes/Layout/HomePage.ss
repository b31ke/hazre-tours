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

<% if $HeroSlides.Count %>
<section class="slider" aria-roledescription="carousel" aria-label="Izdvojena putovanja">
    <% loop $HeroSlides %>
    <div class="slide<% if $IsFirst %> on<% end_if %>" aria-roledescription="slide" aria-label="$Pos od $TotalItems">
        <% if $SlideImageURL %><img src="$SlideImageURL" alt="" <% if not $IsFirst %>loading="lazy"<% end_if %>><% end_if %>
        <div class="wrap slide-txt">
            <p class="slide-date">$TabLabel</p>
            <h2>$Title</h2>
            <p class="slide-price"><% if $HasPriceFrom %>od <% end_if %><b>$MinPrice KM</b> po osobi</p>
            <a class="btn btn-orange" href="$Link">Pogledaj putovanje</a>
        </div>
    </div>
    <% end_loop %>
    <% if $HeroSlides.Count > 1 %>
    <button class="sl-arrow sl-prev" type="button" aria-label="Prethodno putovanje">‹</button>
    <button class="sl-arrow sl-next" type="button" aria-label="Sljedeće putovanje">›</button>
    <div class="dots"><% loop $HeroSlides %><button class="dot" type="button" aria-label="Putovanje $Pos"<% if $IsFirst %> aria-current="true"<% end_if %>></button><% end_loop %></div>
    <% end_if %>
</section>
<% end_if %>

<section class="intro" aria-labelledby="intro-h">
    <div class="wrap intro-in">
        <h1 id="intro-h">Hazre Tours, putnička agencija i prevoznik iz Srebrenika</h1>
        <% if $HeroLead %><p>$HeroLead</p><% end_if %>
        <form class="search-bar" id="search" role="search" action="putovanja">
            <label class="sr" for="s-q">Pronađi svoje putovanje</label>
            <input id="s-q" type="search" placeholder="Pronađi svoje putovanje… (npr. Istanbul, advent, Zlatibor)" autocomplete="off">
            <button class="btn btn-blue" type="submit"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><span>Traži</span></button>
        </form>
    </div>
</section>

<section class="block" id="aranzmani" aria-labelledby="ar-h">
    <div class="wrap">
        <div class="center-head">
            <h2 id="ar-h">Pogledajte aktuelne aranžmane i rezervišite svoje sljedeće putovanje</h2>
            <button class="btn btn-orange btn-sm" type="button" id="filter-btn" aria-expanded="false" aria-controls="filter-panel">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M3 5h18l-7 8v6l-4 2v-8z"/></svg>Filtriraj
            </button>
        </div>
        <div class="filter-panel" id="filter-panel" hidden>
            <label><span>Vrsta putovanja</span>
                <select id="s-type"><option value="sve">Sva putovanja</option><option value="jednodnevni">Jednodnevni izleti</option><option value="visednevni">Višednevna putovanja</option><option value="ljetovanje">Ljetovanja</option></select>
            </label>
            <label><span>Mjesec</span><select id="s-month"><option value="">Bilo kada</option><% loop $SearchMonths %><option value="$Value">$Label</option><% end_loop %></select></label>
            <label><span>Polazak iz</span><select id="s-from"><option value="">Bilo gdje</option><% loop $SearchCities %><option>$Name</option><% end_loop %></select></label>
        </div>
        <p class="result-note" id="result-note" hidden></p>
        <div class="grid" id="grid">
            <% loop $UpcomingTrips %><% include TripCard %><% end_loop %>
            <div class="empty" id="empty"<% if $UpcomingTrips.Count %> hidden<% end_if %>>Za ovaj izbor trenutno nemamo polazak. <a href="putovanje-po-mjeri">Pošaljite upit za putovanje po mjeri →</a></div>
        </div>
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
        <div class="cats">
            <% loop $HomeCategories %>
            <a class="cat" href="$Link">
                <% with $CoverTrip %><img src="$CardImageURL" alt="" loading="lazy" class="<% if $PosterIsFlyer %>flyer<% end_if %>"><% end_with %>
                <span class="cat-t">$Title</span>
                <span class="cat-n">$TripCount <% if $TripCount == 1 %>putovanje<% else %>putovanja<% end_if %></span>
            </a>
            <% end_loop %>
        </div>
    </div>
</section>
<% end_if %>

<section class="block" aria-labelledby="why-h">
    <div class="wrap">
        <div class="center-head">
            <h2 id="why-h">Zašto putovati s nama</h2>
            <p>Od 2008. vozimo putnike, a od 2016. organizujemo i putovanja.</p>
        </div>
        <div class="features">
            <div class="feature"><span class="f-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="4" y="3" width="16" height="15" rx="3"/><path d="M4 11h16M8 21v-3M16 21v-3"/></svg></span><h3>Vlastiti autobusi</h3><p>Turistički autobusi s klimom, TV-om i WC-om i naši iskusni vozači.</p></div>
            <div class="feature"><span class="f-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg></span><h3>Polasci iz vašeg grada</h3><p>Tuzla, Srebrenik, Gračanica, Gradačac, Brčko, Orašje i Bijeljina.</p></div>
            <div class="feature"><span class="f-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 4.5-6 8-6s7 2 8 6"/></svg></span><h3>Vodič i pratilac</h3><p>Na grupnim putovanjima s vama je naš pratilac od polaska do povratka.</p></div>
            <div class="feature"><span class="f-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 12V7H4v5m16 0v7H4v-7m16 0H4"/><path d="M12 7v14"/></svg></span><h3>Jasne cijene</h3><p>U programu piše šta je uključeno, a šta se plaća posebno.</p></div>
        </div>
    </div>
</section>

<section class="block alt" aria-labelledby="svc-h">
    <div class="wrap">
        <div class="center-head"><h2 id="svc-h">Naše usluge</h2></div>
        <div class="services">
            <a class="service" href="putovanja"><h3>Izleti i putovanja</h3><p>Advent, šoping, evropski gradovi i vikend ture s vodičem.</p><span>Pogledaj ponudu →</span></a>
            <a class="service" href="putovanje-po-mjeri"><h3>Putovanje po mjeri</h3><p>Vaš termin, vaš hotel, vaš program. Za porodice, firme i udruženja.</p><span>Pošalji upit →</span></a>
            <a class="service" href="najam-autobusa"><h3>Najam autobusa</h3><p>Ekskurzije, sportski klubovi, svadbe i transferi.</p><span>Zatraži ponudu →</span></a>
            <a class="service" href="putovanje-po-mjeri"><h3>Hoteli i ljetovanja</h3><p>Više od 200.000 hotela u 140 zemalja, uz vaučer iz agencije.</p><span>Pošalji upit →</span></a>
        </div>
    </div>
</section>

<% if $PublishedReviews.Count %>
<section class="block" aria-labelledby="rev-h">
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
<section class="block" aria-labelledby="gal-h">
    <div class="wrap">
        <div class="center-head"><h2 id="gal-h">Galerija</h2><a class="link-more" href="$GalleryLink">Cijela galerija →</a></div>
        <div class="gallery">
            <% loop $GalleryPreview %><a href="$ScaleMaxWidth(1800).URL" class="g-item" data-lightbox="home"><img src="$Fill(600,600).Convert('webp').URL" alt="$Title.ATT" loading="lazy"></a><% end_loop %>
        </div>
    </div>
</section>
<% end_if %>

<section class="block alt" aria-labelledby="faq-h">
    <div class="wrap faq-wrap">
        <div class="center-head">
            <h2 id="faq-h">Česta pitanja</h2>
            <p>Prijava, osiguranje, sjedišta i dokumenti. Sve ostalo je u <a href="uslovi-putovanja">opštim uslovima putovanja</a>.</p>
        </div>
        <% include FaqList Items=$GeneralFaqs %>
    </div>
</section>

<% include ContactSection FormType='rezervacija' %>
