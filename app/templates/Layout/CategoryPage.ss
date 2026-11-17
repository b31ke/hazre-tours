<section class="page-hero">
    <div class="page-hero-glow" aria-hidden="true"></div>
    <div class="wrap">
        <nav class="crumbs" aria-label="Putanja"><a href="$BaseHref">Početna</a><span>/</span><a href="putovanja">Putovanja</a><% if $Category %><span>/</span><span aria-current="page">$Title</span><% end_if %></nav>
        <p class="ph-kicker"><% if $Category %>Izaberite svoj termin<% else %>Kuda ćemo sljedeće?<% end_if %></p>
        <h1 class="h1">$Title</h1>
        <p class="hero-lead">$Intro</p>
        <div class="chips cat-chips">
            <a class="chip<% if not $Category %> on<% end_if %>" href="putovanja">Sve</a>
            <% loop $MenuCategories %><a class="chip<% if $Up.Category.ID == $ID %> on<% end_if %>" href="$Link">$Title <span class="count">$TripCount</span></a><% end_loop %>
        </div>
    </div>
    <svg class="ph-wave" viewBox="0 0 1440 60" preserveAspectRatio="none" aria-hidden="true"><path d="M0 60 H1440 V30 C 1180 0, 900 56, 720 32 S 260 4, 0 30 Z" fill="#fff"/></svg>
</section>
<section class="block">
    <div class="wrap">
        <% if $Trips.Count %>
        <div class="grid">
            <% loop $Trips %><% include TripCard %><% end_loop %>
        </div>
        <% else %>
        <div class="empty">Trenutno nema objavljenih polazaka u ovoj kategoriji. <a href="putovanje-po-mjeri">Pošaljite upit pa vam javljamo prve termine →</a></div>
        <% end_if %>
    </div>
</section>
<% include ContactSection FormType='rezervacija' %>
