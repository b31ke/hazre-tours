<section class="page-hero">
    <div class="page-hero-glow" aria-hidden="true"></div>
    <div class="wrap">
        <nav class="crumbs" aria-label="Putanja"><a href="$BaseHref">Početna</a><span>/</span><span aria-current="page">$Title</span></nav>
        <p class="ph-kicker">Uspomene s naših putovanja</p>
        <h1 class="h1">$Title</h1>
        <% if $Intro %><p class="hero-lead">$Intro</p><% end_if %>
    </div>
    <svg class="ph-wave" viewBox="0 0 1440 60" preserveAspectRatio="none" aria-hidden="true"><path d="M0 60 H1440 V30 C 1180 0, 900 56, 720 32 S 260 4, 0 30 Z" fill="#fff"/></svg>
</section>
<section class="block">
    <div class="wrap">
        <% if $Content %><div class="typo" data-reveal>$Content</div><% end_if %>
        <% if $SortedImages.Count %>
        <div class="gallery gallery-full">
            <% loop $SortedImages %><a href="$ScaleMaxWidth(1800).URL" class="g-item" data-lightbox="page" data-reveal><img src="$Fill(700,700).Convert('webp').URL" alt="$Title.ATT" loading="lazy"><% if $Title %><span class="g-cap">$Title</span><% end_if %></a><% end_loop %>
        </div>
        <% else %>
        <p class="empty">Fotografije uskoro.</p>
        <% end_if %>
    </div>
</section>
<% include ContactSection FormType='rezervacija' %>
