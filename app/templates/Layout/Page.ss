<section class="page-hero">
    <div class="page-hero-glow" aria-hidden="true"></div>
    <div class="wrap">
        <nav class="crumbs" aria-label="Putanja"><a href="$BaseHref">Početna</a><span>/</span><span aria-current="page">$Title</span></nav>
        <h1 class="h1">$Title</h1>
        <% if $Intro %><p class="hero-lead">$Intro</p><% end_if %>
    </div>
</section>
<section class="block page-body">
    <div class="wrap">
        <article class="typo glass-soft" data-reveal>$Content</article>
        $Form
    </div>
</section>
<% if $PageForm %>
    <% include ContactSection FormType=$PageForm %>
<% else %>
    <% include ContactSection FormType='rezervacija' %>
<% end_if %>
