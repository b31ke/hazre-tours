<div class="topbar">
    <div class="wrap">
        <% if $SiteConfig.Address %><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>$SiteConfig.Address</span><% end_if %>
        <span class="phones">
            <% loop $SiteConfig.PhoneList.Limit(3) %>
                <span><% if $IsFirst %><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2"/></svg><% end_if %><a href="tel:$Tel">$Number</a></span>
            <% end_loop %>
            <% if $SiteConfig.ContactEmail %><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><a href="mailto:$SiteConfig.ContactEmail">$SiteConfig.ContactEmail</a></span><% end_if %>
        </span>
    </div>
</div>

<header class="nav">
    <div class="wrap">
        <a class="logo" href="$BaseHref" aria-label="$SiteConfig.Title, početna"><img src="images/hazre-logo.png" alt="$SiteConfig.Title putnička agencija" width="87" height="58"></a>
        <button class="menu-btn" id="menu-btn" type="button" aria-expanded="false" aria-controls="menu">Meni ☰</button>
        <nav aria-label="Glavni meni">
            <ul id="menu">
                <li><a href="{$BaseHref}?vrsta=jednodnevni#aranzmani" data-filter="jednodnevni">Jednodnevni izleti</a></li>
                <li><a href="{$BaseHref}?vrsta=visednevni#aranzmani" data-filter="visednevni">Višednevna putovanja</a></li>
                <% loop $Menu(1) %><% if $URLSegment != 'home' %>
                <li><a href="$Link" class="$LinkingMode">$MenuTitle</a></li>
                <% end_if %><% end_loop %>
                <li><a class="btn" href="#kontakt">Pošalji upit</a></li>
            </ul>
        </nav>
    </div>
</header>
