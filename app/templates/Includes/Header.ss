<div class="topbar">
    <div class="wrap topbar-in">
        <% if $SiteConfig.Address %><span class="tb-addr"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>$SiteConfig.Address</span><% end_if %>
        <span class="tb-right">
            <% with $SiteConfig.MainPhone %><a href="tel:$Tel"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2"/></svg>$Number</a><% end_with %>
            <% if $SiteConfig.ContactEmail %><a href="mailto:$SiteConfig.ContactEmail" class="tb-mail">$SiteConfig.ContactEmail</a><% end_if %>
            <% if $SiteConfig.FacebookURL %><a href="$SiteConfig.FacebookURL" rel="noopener" aria-label="Facebook">f</a><% end_if %>
            <% if $SiteConfig.InstagramURL %><a href="$SiteConfig.InstagramURL" rel="noopener" aria-label="Instagram">ig</a><% end_if %>
        </span>
    </div>
</div>
<header class="site-head" id="top">
    <div class="wrap head-row">
        <a class="logo" href="$BaseHref" aria-label="$SiteConfig.Title, početna">
            <img src="images/hazre-logo-sm.png" alt="$SiteConfig.Title" width="171" height="70">
        </a>
        <nav class="main-nav" id="main-nav" aria-label="Glavni meni">
            <ul>
                <li><a class="nav-link<% if $URLSegment == 'home' %> on<% end_if %>" href="$BaseHref">Početna</a></li>
                <li class="has-drop">
                    <a href="putovanja" class="nav-link<% if $BodyClass == 'CategoryController' || $BodyClass == 'TripController' %> on<% end_if %>" aria-haspopup="true">Putovanja <svg width="10" height="10" viewBox="0 0 10 10" aria-hidden="true"><path d="M1 3l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.6"/></svg></a>
                    <div class="drop">
                        <a href="{$BaseHref}?vrsta=jednodnevni#aranzmani">Jednodnevni izleti</a>
                        <a href="{$BaseHref}?vrsta=visednevni#aranzmani">Višednevna putovanja</a>
                        <% loop $MenuCategories %><a href="$Link">$Title <span class="count">$TripCount</span></a><% end_loop %>
                        <a href="putovanja">Sva putovanja</a>
                    </div>
                </li>
                <li><a class="nav-link<% if $BodyClass == 'CalendarController' %> on<% end_if %>" href="kalendar">Kalendar</a></li>
                <% loop $Menu(1) %><% if $URLSegment != 'home' %>
                <li><a class="nav-link<% if $isCurrent || $isSection %> on<% end_if %>" href="$Link">$MenuTitle</a></li>
                <% end_if %><% end_loop %>
                <li><a class="nav-link" href="#kontakt">Kontakt</a></li>
            </ul>
        </nav>
        <div class="head-actions">
            <a class="btn btn-orange hide-md" href="#kontakt">Rezerviši</a>
            <button class="icon-btn menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Meni">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
