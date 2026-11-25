<section class="page-hero">
    <div class="page-hero-glow" aria-hidden="true"></div>
    <div class="wrap">
        <nav class="crumbs" aria-label="Putanja"><a href="$BaseHref">Početna</a><span>/</span><span aria-current="page">$Title</span></nav>
        <% if $HeroKicker %><p class="ph-kicker">$HeroKicker</p><% end_if %>
        <h1 class="h1">$Title</h1>
        <% if $Intro %><p class="hero-lead">$Intro</p><% end_if %>
    </div>
    <svg class="ph-wave" viewBox="0 0 1440 60" preserveAspectRatio="none" aria-hidden="true"><path d="M0 60 H1440 V30 C 1180 0, 900 56, 720 32 S 260 4, 0 30 Z" fill="#fff"/></svg>
</section>

<section class="block faq-block faq-page" aria-labelledby="faq-h">
    <div class="wrap faq-grid">
        <div class="faq-side">
            <% if $SideKicker %><p class="script-kicker">$SideKicker</p><% end_if %>
            <h2 id="faq-h"><% if $SideTitle %>$SideTitle<% else %>$Title<% end_if %></h2>
            <% if $Content %><div class="muted">$Content</div><% else %>
            <p class="muted">Prijava, osiguranje, sjedišta i dokumenti. Sve ostalo je u <a href="uslovi-putovanja">opštim uslovima putovanja</a>.</p>
            <% end_if %>
            <% with $SiteConfig.MainPhone %>
            <a class="faq-help" href="tel:$Tel">
                <span class="faq-help-ico" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2"/></svg></span>
                <span><small><% if $Top.HelpLabel %>$Top.HelpLabel<% else %>Niste našli odgovor? Pozovite<% end_if %></small><b>$Number</b></span>
            </a>
            <% end_with %>
        </div>
        <% if $GeneralFaqs.Count %>
            <% include FaqList Items=$GeneralFaqs %>
        <% else %>
            <p class="empty">Pitanja uskoro.</p>
        <% end_if %>
    </div>
</section>

<% include ContactSection FormType='rezervacija' %>
