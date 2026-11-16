<% with $Trip %>
<section class="trip-hero">
    <div class="bg<% if $PosterIsFlyer %> flyer<% end_if %>" aria-hidden="true"<% if $CardImageURL %> style="background-image:url('$CardImageURL')"<% end_if %>></div>
    <div class="wrap">
        <p class="eyebrow sun"><a href="{$BaseHref}#aranzmani">Aranžmani</a> · $TypeLabel</p>
        <h1>$Title</h1>
        <div class="facts">
            <span class="p"><% if $HasPriceFrom %>od <% end_if %>$MinPrice KM po osobi</span>
            <span>$DurationLabel</span>
            <% if $Badge %><span>$Badge</span><% end_if %>
        </div>
    </div>
</section>

<section class="block trip">
    <div class="wrap trip-grid">
        <div class="trip-main">
            <% if $Summary %><p class="trip-lead">$Summary</p><% end_if %>

            <% if $IncludesList.Count || $ExcludesList.Count %>
            <div class="two">
                <% if $IncludesList.Count %><div class="box"><h2>Aranžman obuhvata</h2><ul class="ticks"><% loop $IncludesList %><li>$Text</li><% end_loop %></ul></div><% end_if %>
                <% if $ExcludesList.Count %><div class="box"><h2>Ne obuhvata</h2><ul class="dashes"><% loop $ExcludesList %><li>$Text</li><% end_loop %></ul></div><% end_if %>
            </div>
            <% end_if %>
            <% if $KidsNote %><p class="kids"><b>Popusti za djecu:</b> $KidsNote</p><% end_if %>

            <% if $Program %>
            <h2 class="h-sub">Program putovanja</h2>
            <div class="typography program">$Program</div>
            <% end_if %>
        </div>

        <aside class="trip-side">
            <div class="side-card">
                <h2>Termini</h2>
                <ul class="terms">
                    <% loop $UpcomingTerms %>
                    <li class="<% if $IsFull %>full<% end_if %>">
                        <span><b>$Label</b><small>$DaysLabel<% if $Note %> · $Note<% end_if %></small></span>
                        <span class="tp">$EffectivePrice KM<small class="st st-$Status">$StatusLabel</small></span>
                    </li>
                    <% end_loop %>
                </ul>
                <a class="btn wide" href="#kontakt">Rezerviši mjesto</a>
                <% if $ProgramFile.exists %><a class="btn ghost wide" href="$ProgramFile.URL" download>Preuzmi program ($ProgramFile.Extension.UpperCase)</a><% end_if %>
                <% if $Poster.exists %><a class="link" href="$Poster.URL" target="_blank" rel="noopener">Pogledaj plakat</a><% end_if %>
            </div>
            <% if $Departures %>
            <div class="side-card">
                <h2>Polasci iz</h2>
                <p>$Departures</p>
                <p class="muted small">Tačno vrijeme i mjesto polaska su u programu putovanja.</p>
            </div>
            <% end_if %>
        </aside>
    </div>
</section>
<% end_with %>

<% if $OtherTrips.Count %>
<section class="block services">
    <div class="wrap">
        <div class="sec-head"><div><p class="eyebrow">Možda vas zanima</p><h2>Ostali aranžmani</h2></div></div>
        <div class="grid"><% loop $OtherTrips %><% include TripCard %><% end_loop %></div>
    </div>
</section>
<% end_if %>

<% include InquiryForm %>
