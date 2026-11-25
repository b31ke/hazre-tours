<section class="hero" aria-labelledby="hero-h">
    <div class="hero-sun" aria-hidden="true"></div>
    <div class="wrap hero-grid">
        <div class="hero-copy">
            <% if $HeroEyebrow %><p class="hero-kicker">$HeroEyebrowHTML</p><% end_if %>
            <h1 id="hero-h">$HeroTitleHTML</h1>
            <% if $HeroLead %><p class="hero-lead">$HeroLeadHTML</p><% end_if %>
            <div class="hero-btns">
                <a class="btn btn-orange btn-lg" href="#aranzmani">Pogledaj ponudu</a>
                <a class="btn btn-ghost btn-lg" href="putovanje-po-mjeri"><span><span class="hb-long">Putovanje </span>po mjeri</span></a>
            </div>
            <ul class="trust">
                <li><b>Od 1996.</b> na putu</li>
                <li><b>Vlastiti</b> autobusi</li>
                <li><b>$CityCount</b> mjesta polaska</li>
            </ul>
        </div>

        <div class="hero-art">
            <% if $HeroImage.exists %>
                <img class="hero-photo" src="$HeroImage.FillMax(900,700).Convert('webp').URL" alt="" width="900" height="700">
            <% else %>
                <% loop $HeroTrips %>
                <a class="postcard pc-$Pos<% if not $PhotoURL %><% if $PosterIsFlyer %> flyer<% end_if %><% end_if %>" href="$Link" aria-label="$Title">
                    <img src="<% if $PhotoURL %>$PhotoURL<% else %>$CardImageURL<% end_if %>" alt="" fetchpriority="<% if $Pos == 1 %>high<% else %>auto<% end_if %>">
                    <span class="pc-name"><% if $DestinationName %>$DestinationName<% else %>$Title<% end_if %></span>
                </a>
                <% end_loop %>
            <% end_if %>
        </div>
    </div>

    <% if $NextDepartures.Count > 1 %>
    <div class="wrap next-strip">
        <span class="ns-h">Sljedeći polasci</span>
        <% loop $NextDepartures.Limit(4, 1) %>
        <a href="$Trip.Link"><b>$StartDate.Format('dd.MM.')</b> $Trip.Title <span>$EffectivePrice KM</span></a>
        <% end_loop %>
        <a class="ns-all" href="kalendar">Svi polasci →</a>
    </div>
    <% end_if %>

    <svg class="hero-wave" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0 60 C 240 20, 480 20, 720 50 S 1200 90, 1440 40 L1440 90 L0 90 Z" fill="#fff"/>
        <path d="M0 58 C 240 18, 480 18, 720 48 S 1200 88, 1440 38" fill="none" stroke="#17AEDF" stroke-width="3" opacity=".55"/>
        <path d="M0 70 C 260 36, 500 40, 740 62 S 1180 92, 1440 56" fill="none" stroke="#F7A11A" stroke-width="3" opacity=".7"/>
    </svg>
</section>
