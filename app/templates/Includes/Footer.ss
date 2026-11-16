<footer>
    <div class="wrap">
        <img src="images/hazre-logo.png" alt="$SiteConfig.Title" width="81" height="54" loading="lazy">
        <nav aria-label="Podnožje">
            <a href="{$BaseHref}#aranzmani">Aranžmani</a>
            <% loop $Menu(1) %><% if $URLSegment != 'home' %><a href="$Link">$MenuTitle</a><% end_if %><% end_loop %>
            <a href="politika-privatnosti/">Politika privatnosti</a>
            <% if $SiteConfig.FacebookURL %><a href="$SiteConfig.FacebookURL" rel="noopener">Facebook</a><% end_if %>
            <% if $SiteConfig.InstagramURL %><a href="$SiteConfig.InstagramURL" rel="noopener">Instagram</a><% end_if %>
        </nav>
        <span>© $CurrentYear <% if $SiteConfig.CompanyName %>$SiteConfig.CompanyName<% else %>$SiteConfig.Title<% end_if %></span>
    </div>
</footer>

<% with $SiteConfig.MainPhone %>
<a class="float-call" href="tel:$Tel" aria-label="Pozovite $Number"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2"/></svg></a>
<% end_with %>
