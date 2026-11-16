<% with $Trip %>
$JsonLD
<section class="trip-hero">
    <div class="trip-bg<% if $PosterIsFlyer %> flyer<% end_if %>" data-parallax aria-hidden="true"<% if $HeroImageURL %> style="background-image:url('$HeroImageURL')"<% end_if %>></div>
    <div class="wrap trip-hero-in">
        <nav class="crumbs" aria-label="Putanja"><a href="$BaseHref">Početna</a><span>/</span><a href="putovanja">Putovanja</a><% with $Categories.First %><span>/</span><a href="$Link">$Title</a><% end_with %></nav>
        <p class="eyebrow">$TypeLabel<% if $DestinationName %> · $DestinationName<% end_if %></p>
        <h1 class="h1 trip-title">$Title</h1>
        <div class="facts">
            <span class="fact fact-price"><% if $OnPromo %><s>$OldPrice KM</s> <% end_if %><% if $HasPriceFrom %>od <% end_if %><b>$MinPrice KM</b> po osobi</span>
            <span class="fact"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>$DurationLabel</span>
            <span class="fact"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="4" y="3" width="16" height="15" rx="3"/><path d="M4 11h16M8 21v-3M16 21v-3"/></svg>$TransportLabel</span>
            <% if $DistanceKm %><span class="fact mono">≈ $DistanceKm km od Srebrenika</span><% end_if %>
        </div>
        <% if $OnPromo && $PromoUntil %>
        <div class="trip-promo glass">
            <span><% if $PromoNote %>$PromoNote<% else %>Akcijska cijena<% end_if %></span>
            <span class="countdown" data-until="$PromoUntilISO"><b data-cd="d">--</b><small>d</small><b data-cd="h">--</b><small>h</small><b data-cd="m">--</b><small>m</small><b data-cd="s">--</b><small>s</small></span>
        </div>
        <% end_if %>
    </div>
</section>

<section class="block trip-body">
    <div class="wrap trip-grid">
        <div class="trip-main">
            <div class="tabs" role="tablist" aria-label="O putovanju">
                <button role="tab" aria-selected="true" aria-controls="t-opis" id="tb-opis">Opis</button>
                <% if $ProgramDays.Count %><button role="tab" aria-selected="false" aria-controls="t-program" id="tb-program">Program</button><% end_if %>
                <button role="tab" aria-selected="false" aria-controls="t-cijena" id="tb-cijena">Cijena i uslovi</button>
                <% if $AllFaqs.Count %><button role="tab" aria-selected="false" aria-controls="t-pitanja" id="tb-pitanja">Pitanja</button><% end_if %>
                <span class="tab-ink" aria-hidden="true"></span>
            </div>

            <div class="tab-panel" role="tabpanel" id="t-opis" aria-labelledby="tb-opis">
                <h2 class="panel-h">Opis putovanja</h2>
                <% if $Summary %><p class="trip-lead">$Summary</p><% end_if %>
                <% if $IncludesList.Count || $ExcludesList.Count %>
                <div class="two">
                    <% if $IncludesList.Count %><div class="box glass"><h3>Aranžman obuhvata</h3><ul class="ticks"><% loop $IncludesList %><li>$Text</li><% end_loop %></ul></div><% end_if %>
                    <% if $ExcludesList.Count %><div class="box glass"><h3>Ne obuhvata</h3><ul class="dashes"><% loop $ExcludesList %><li>$Text</li><% end_loop %></ul></div><% end_if %>
                </div>
                <% end_if %>
                <% if $Accommodation %><div class="box glass"><h3>Smještaj i prevoz</h3><p>$Accommodation</p></div><% end_if %>
                <% if $Departures %><p class="trip-deps"><b>Polasci iz:</b> $Departures. <span class="muted">Tačno vrijeme i mjesto polaska su u programu.</span></p><% end_if %>
            </div>

            <% if $ProgramDays.Count %>
            <div class="tab-panel" role="tabpanel" id="t-program" aria-labelledby="tb-program">
                <h2 class="panel-h">Program putovanja</h2>
                <ol class="days">
                    <% loop $ProgramDays %>
                    <li class="day<% if $IsIntro %> day-intro<% end_if %>">
                        <details<% if $IsFirst || $Up.ProgramDays.Count < 3 %> open<% end_if %>>
                            <summary><span class="day-n mono"><% if $IsIntro %>!<% else %>$Num<% end_if %></span><span class="day-t">$Title</span><i aria-hidden="true"></i></summary>
                            <div class="day-b typo">$Body</div>
                        </details>
                    </li>
                    <% end_loop %>
                </ol>
            </div>
            <% end_if %>

            <div class="tab-panel" role="tabpanel" id="t-cijena" aria-labelledby="tb-cijena">
                <h2 class="panel-h">Cijena i uslovi</h2>
                <table class="price-table">
                    <thead><tr><th>Termin</th><th>Trajanje</th><th>Cijena</th><th>Status</th></tr></thead>
                    <tbody>
                    <% loop $UpcomingTerms %>
                    <tr class="<% if $IsFull %>full<% end_if %>"><td class="mono">$Label</td><td>$DaysLabel</td><td class="mono"><b>$EffectivePrice KM</b></td><td><span class="st st-$StatusKey">$StatusLabel</span></td></tr>
                    <% end_loop %>
                    </tbody>
                </table>
                <% if $KidsNote %><p class="kids glass"><b>Popusti za djecu:</b> $KidsNote</p><% end_if %>
                <% if $ProgramNotes %><div class="typo notes">$ProgramNotes</div><% end_if %>
                <p class="muted small">Za sve aranžmane važe <a href="uslovi-putovanja">opšti uslovi putovanja</a> agencije Hazre Tours.</p>
            </div>

            <% if $AllFaqs.Count %>
            <div class="tab-panel" role="tabpanel" id="t-pitanja" aria-labelledby="tb-pitanja">
                <h2 class="panel-h">Česta pitanja</h2>
                <% include FaqList Items=$AllFaqs %>
            </div>
            <% end_if %>

            <% if $GallerySorted.Count %>
            <div class="trip-gallery">
                <h2 class="panel-h">Fotografije</h2>
                <div class="gallery"><% loop $GallerySorted %><a href="$ScaleMaxWidth(1800).URL" class="g-item" data-lightbox="trip"><img src="$Fill(600,600).Convert('webp').URL" alt="$Title.ATT" loading="lazy"></a><% end_loop %></div>
            </div>
            <% end_if %>

            <%-- forma je izvan "with $Trip" da vidi podatke stranice (token, link) --%>
            <% end_with %>
            <details class="change glass">
                <summary><span><b>Sviđa vam se putovanje, ali biste nešto promijenili?</b><small>Termin, hotel, trajanje ili putujete kao grupa: pošaljite želje pa pravimo ponudu.</small></span><i aria-hidden="true"></i></summary>
                <% include InquiryForm FormType='izmjena' %>
            </details>
            <% with $Trip %>
        </div>

        <aside class="trip-side">
            <div class="book glass" id="book">
                <p class="book-h">Izaberite termin</p>
                <div class="term-pick">
                    <% loop $UpcomingTerms %>
                    <label class="term<% if $IsFull %> full<% end_if %>">
                        <input type="radio" name="pick" value="$Label · $EffectivePrice KM"<% if $IsFull %> disabled<% end_if %><% if $Up.NextOpenTerm.ID == $ID %> checked<% end_if %>>
                        <span class="term-d"><b class="mono">$Label</b><small>$WeekdayShort · $DaysLabel</small></span>
                        <span class="term-p"><b class="mono">$EffectivePrice KM</b><small class="st st-$StatusKey">$StatusLabel</small></span>
                    </label>
                    <% end_loop %>
                </div>
                <% if $SoldOut %>
                <a class="btn btn-ghost btn-lg wide" href="#kontakt">Javite mi za novi termin</a>
                <% else %>
                <a class="btn btn-sun btn-lg wide" href="#kontakt" data-book="$ID">Rezerviši mjesto</a>
                <% end_if %>
                <% if $ProgramFile.exists %><a class="btn btn-ghost wide" href="$ProgramFile.URL" download><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12m0 0-4-4m4 4 4-4M5 21h14"/></svg>Program ($ProgramFile.Extension.UpperCase)</a><% end_if %>
                <div class="share">
                    <span class="muted small">Podijeli:</span>
                    <a href="viber://forward?text=$Top.ShareText" aria-label="Podijeli na Viberu" class="sh viber">V</a>
                    <a href="https://wa.me/?text=$Top.ShareText" rel="noopener" aria-label="Podijeli na WhatsAppu" class="sh wa">W</a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=$Top.ShareURL" rel="noopener" aria-label="Podijeli na Facebooku" class="sh fb">f</a>
                    <button type="button" class="sh" data-copy="$AbsoluteLink" aria-label="Kopiraj link">⧉</button>
                </div>
                <% if $Poster.exists %><a class="poster-link" href="$Poster.URL" data-lightbox="poster">Pogledaj plakat</a><% end_if %>
            </div>
        </aside>
    </div>
</section>

<% if $Reviews.Count %>
<section class="block reviews">
    <div class="wrap">
        <div class="sec-head"><div><p class="eyebrow">Utisci</p><h2 class="h2">Putnici o ovom putovanju</h2></div></div>
        <div class="rev-track"><% loop $Reviews %><figure class="rev glass"><div class="stars">$Stars</div><blockquote>$Text</blockquote><figcaption><span class="av">$Initial</span><span><b>$Name</b><% if $City %>, $City<% end_if %></span></figcaption></figure><% end_loop %></div>
    </div>
</section>
<% end_if %>
<% end_with %>

<% if $OtherTrips.Count %>
<section class="block">
    <div class="wrap">
        <div class="sec-head"><div><p class="eyebrow">Možda vas zanima</p><h2 class="h2">Slična putovanja</h2></div><a class="btn btn-ghost" href="putovanja">Sva putovanja →</a></div>
        <div class="grid"><% loop $OtherTrips %><% include TripCard %><% end_loop %></div>
    </div>
</section>
<% end_if %>

<% include ContactSection FormType='rezervacija' %>

<div class="mobile-book">
    <span><small><% if $Trip.HasPriceFrom %>od <% end_if %></small><b class="mono">$Trip.MinPrice KM</b><small>po osobi</small></span>
    <a class="btn btn-sun" href="#kontakt" data-book="$Trip.ID">Rezerviši</a>
</div>
