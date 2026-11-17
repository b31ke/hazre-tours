<section class="page-hero">
    <div class="page-hero-glow" aria-hidden="true"></div>
    <div class="wrap">
        <nav class="crumbs" aria-label="Putanja"><a href="$BaseHref">Početna</a><span>/</span><span aria-current="page">$Title</span></nav>
        <% if $PageForm == 'pomjeri' %><p class="ph-kicker">Vaše putovanje, vaš plan</p><% else_if $PageForm == 'autobus' %><p class="ph-kicker">Vozimo vas mi</p><% end_if %>
        <h1 class="h1">$Title</h1>
        <% if $Intro %><p class="hero-lead">$Intro</p><% end_if %>
    </div>
    <svg class="ph-wave" viewBox="0 0 1440 60" preserveAspectRatio="none" aria-hidden="true"><path d="M0 60 H1440 V30 C 1180 0, 900 56, 720 32 S 260 4, 0 30 Z" fill="#fff"/></svg>
</section>
<section class="block page-body">
    <div class="wrap<% if $PageForm %> pb-grid<% end_if %><% if $PageForm == 'autobus' %> bus-feature<% end_if %>">
        <% if $PageForm == 'autobus' %>
        <figure class="bus-photo">
            <img src="images/hazre-autobusi.webp" alt="Autobusi Hazre Tours" width="792" height="990" loading="lazy">
            <span class="bp-badge"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="3"/><path d="M3 11h18"/><circle cx="7.5" cy="19" r="1.6"/><circle cx="16.5" cy="19" r="1.6"/></svg>Udobno, sigurno, na vrijeme</span>
            <figcaption>
                <b>Vlastiti autobusi turističke klase</b>
                <span class="bp-chips"><span>Klima</span><span>WC</span><span>TV i audio</span><span>Iskusni vozači</span></span>
            </figcaption>
        </figure>
        <div class="bus-side">
        <% end_if %>
        <% if $Content %><article class="typo pb-text">$Content</article><% end_if %>
        <% if $PageForm %>
        <ol class="pb-steps">
            <% if $PageForm == 'autobus' %>
            <li><span class="pbs-n">1</span><div><b>Pošaljete relaciju</b><p>Odakle, kuda, kada i koliko vas putuje.</p></div></li>
            <li><span class="pbs-n">2</span><div><b>Dobijete ponudu</b><p>Cijena prevoza s našim autobusom i vozačem.</p></div></li>
            <li><span class="pbs-n">3</span><div><b>Vozimo vas</b><p>Klimatizovan autobus, iskusni vozači, tačni polasci.</p></div></li>
            <% else %>
            <li><span class="pbs-n">1</span><div><b>Pošaljete želje</b><p>Kuda, kada, koliko vas je i kakav smještaj želite.</p></div></li>
            <li><span class="pbs-n">2</span><div><b>Mi složimo ponudu</b><p>Prevoz našim autobusom, smještaj, program i vodič.</p></div></li>
            <li><span class="pbs-n">3</span><div><b>Putujete bez brige</b><p>Vi uživate, a mi vodimo računa o svemu ostalom.</p></div></li>
            <% end_if %>
        </ol>
        <% end_if %>
        <% if $PageForm == 'autobus' %>
        <a class="btn btn-sun btn-lg bus-cta" href="#kontakt">Zatražite ponudu za prevoz</a>
        </div>
        <% end_if %>
        $Form
    </div>
</section>
<% if $PageForm %>
    <% include ContactSection FormType=$PageForm %>
<% else %>
    <% include ContactSection FormType='rezervacija' %>
<% end_if %>
