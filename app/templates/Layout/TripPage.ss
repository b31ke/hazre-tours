<% with $Trip %>
$JsonLD
<section class="trip-hero">
    <div class="trip-bg<% if $PosterIsFlyer %> flyer<% end_if %>" aria-hidden="true"<% if $HeroImageURL %> style="background-image:url('$HeroImageURL')"<% end_if %>></div>
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
                
            </div>

            <div class="tab-panel" role="tabpanel" id="t-opis" aria-labelledby="tb-opis">
                <h2 class="panel-h">Opis putovanja</h2>
                <% if $Summary %><p class="trip-lead">$Summary</p><% end_if %>

                <% if $PhotoURL %>
                <figure class="dest-photo">
                    <img src="$PhotoURL" alt="<% if $DestinationName %>$DestinationName<% else %>$Title<% end_if %>" loading="lazy">
                    <figcaption>
                        <span class="dp-place"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg><% if $DestinationName %>$DestinationName<% else %>$Title<% end_if %></span>
                        <% with $NextOpenTerm %><span class="dp-when">Polazak $Label · <b>$Countdown</b></span><% end_with %>
                    </figcaption>
                </figure>
                <% end_if %>

                <% if $IncludesList.Count || $ExcludesList.Count %>
                <div class="two">
                    <% if $IncludesList.Count %><div class="box inc"><h3><span class="bx-ico" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="m5 12 5 5 9-10"/></svg></span>Aranžman obuhvata</h3><ul class="ticks"><% loop $IncludesList %><li>$Text</li><% end_loop %></ul></div><% end_if %>
                    <% if $ExcludesList.Count %><div class="box exc"><h3><span class="bx-ico" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M7 7l10 10M17 7 7 17"/></svg></span>Ne obuhvata</h3><ul class="dashes"><% loop $ExcludesList %><li>$Text</li><% end_loop %></ul></div><% end_if %>
                </div>
                <% end_if %>
                <% if $Accommodation %><div class="box"><h3>Smještaj i prevoz</h3><p>$Accommodation</p></div><% end_if %>
                <% if $DepartureList.Count %>
                <div class="deps-card">
                    <div class="deps-h"><span class="bx-ico" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="13" rx="3"/><path d="M3 11h18"/><circle cx="7.5" cy="19" r="1.6"/><circle cx="16.5" cy="19" r="1.6"/></svg></span><div><b>Polasci iz</b><small>Tačno vrijeme i mjesto polaska su u programu.</small></div></div>
                    <ul class="deps-chips"><% loop $DepartureList %><li>$Name</li><% end_loop %></ul>
                </div>
                <% end_if %>
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

        </div>

        <aside class="trip-side">
            <% if $Poster.exists %>
            <a class="side-poster" href="$Poster.URL" data-lightbox="poster" aria-label="Pogledaj plakat: $Title">
                <img src="$CardImageURL" alt="Plakat: $Title" loading="lazy">
                <span class="sp-zoom" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4M11 8v6M8 11h6"/></svg></span>
            </a>
            <% end_if %>
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
                    <a href="viber://forward?text=$Top.ShareText" aria-label="Podijeli na Viberu" class="sh viber"><svg width="28" height="28" viewBox="0 0 48 48" aria-hidden="true"><rect width="48" height="48" rx="12" fill="#7360F2"/><path fill="#fff" d="M24 9.5c-7.6 0-13 3.8-13 11.6 0 4.1 1.3 7 3.6 8.8v6.4l4.8-3.5c1.4.3 2.9.4 4.6.4 7.6 0 13-3.7 13-12.1S31.6 9.5 24 9.5zm0 21.4c-1.6 0-3-.1-4.3-.5l-2.5 1.9v-3.1l-.6-.4c-1.8-1.3-2.8-3.6-2.8-7.7 0-6.2 4.3-9.4 10.2-9.4s10.2 3.2 10.2 9.4c0 6.6-4.3 9.8-10.2 9.8z"/><path fill="#fff" d="M27.7 26.3c-.6.9-1.9 1.4-2.8 1.1-3.6-1.1-6.4-3.8-7.6-7.4-.3-.9.2-2.2 1.1-2.8.4-.3 1-.3 1.3.1l1.4 1.9c.3.4.3 1-.1 1.3l-.6.5c.7 1.7 2 3 3.7 3.7l.5-.6c.4-.4.9-.4 1.3-.1l1.9 1.4c.4.3.4.9-.1 1.1z"/><path fill="none" stroke="#fff" stroke-width="1.6" stroke-linecap="round" d="M24.6 15.2a5.6 5.6 0 0 1 5.3 5.3M24.6 17.9a2.9 2.9 0 0 1 2.6 2.6"/></svg></a>
                    <a href="https://wa.me/?text=$Top.ShareText" rel="noopener" aria-label="Podijeli na WhatsAppu" class="sh wa"><svg width="28" height="28" viewBox="0 0 48 48" aria-hidden="true"><rect width="48" height="48" rx="12" fill="#25D366"/><g transform="translate(10.5 10.5) scale(1.125)"><path fill="#fff" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></g></svg></a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=$Top.ShareURL" rel="noopener" aria-label="Podijeli na Facebooku" class="sh fb"><svg width="28" height="28" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="12" fill="#fff"/><path fill="#0866FF" d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647z"/></svg></a>
                    <button type="button" class="sh" data-copy="$AbsoluteLink" aria-label="Kopiraj link"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/></svg></button>
                </div>
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
