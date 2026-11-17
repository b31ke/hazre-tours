<section class="page-hero">
    <div class="page-hero-glow" aria-hidden="true"></div>
    <div class="wrap">
        <nav class="crumbs" aria-label="Putanja"><a href="$BaseHref">Početna</a><span>/</span><span aria-current="page">$Title</span></nav>
        <p class="ph-kicker">Planirajte putovanje na vrijeme</p>
        <h1 class="h1">$Title</h1>
        <p class="hero-lead">$Intro</p>
        <div class="chips cat-chips">
            <% loop $Months %><a class="chip" href="#$Anchor">$Label</a><% end_loop %>
        </div>
    </div>
    <svg class="ph-wave" viewBox="0 0 1440 60" preserveAspectRatio="none" aria-hidden="true"><path d="M0 60 H1440 V30 C 1180 0, 900 56, 720 32 S 260 4, 0 30 Z" fill="#fff"/></svg>
</section>
<section class="block">
    <div class="wrap cal">
        <% loop $Months %>
        <div class="cal-month" id="$Anchor">
            <h2 class="cal-h mono">$Label</h2>
            <% include DepartureBoard Departures=$Terms %>
        </div>
        <% end_loop %>
        <% if not $Months.Count %><p class="empty">Novi polasci uskoro.</p><% end_if %>
    </div>
</section>
<% include ContactSection FormType='rezervacija' %>
