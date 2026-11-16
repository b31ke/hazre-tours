<header class="site-head" id="top">
    <div class="wrap head-row">
        <a class="logo" href="$BaseHref" aria-label="$SiteConfig.Title, početna">
            <img src="images/hazre-logo-sm.png" alt="$SiteConfig.Title" width="96" height="64">
        </a>
        <nav class="main-nav" id="main-nav" aria-label="Glavni meni">
            <ul>
                <li class="has-mega">
                    <a href="putovanja" class="nav-link<% if $BodyClass == 'CategoryController' || $BodyClass == 'TripController' %> on<% end_if %>" aria-haspopup="true">Putovanja <svg width="10" height="10" viewBox="0 0 10 10" aria-hidden="true"><path d="M1 3l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.6"/></svg></a>
                    <div class="mega">
                        <div class="mega-cols">
                            <div>
                                <p class="mega-h">Vrsta</p>
                                <a href="{$BaseHref}?vrsta=jednodnevni#aranzmani">Jednodnevni izleti</a>
                                <a href="{$BaseHref}?vrsta=visednevni#aranzmani">Višednevna putovanja</a>
                                <a href="putovanja">Sva putovanja</a>
                                <a href="kalendar">Kalendar polazaka</a>
                            </div>
                            <div>
                                <p class="mega-h">Teme</p>
                                <% loop $MenuCategories %><a href="$Link">$Title <span class="count">$TripCount</span></a><% end_loop %>
                            </div>
                        </div>
                    </div>
                </li>
                <li><a class="nav-link" href="kalendar">Kalendar</a></li>
                <% loop $Menu(1) %><% if $URLSegment != 'home' %>
                <li><a class="nav-link<% if $isCurrent || $isSection %> on<% end_if %>" href="$Link">$MenuTitle</a></li>
                <% end_if %><% end_loop %>
            </ul>
        </nav>
        <div class="head-actions">
            <button class="icon-btn" type="button" data-open-search aria-label="Pretraži putovanja (Ctrl+K)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <kbd>Ctrl K</kbd>
            </button>
            <% with $SiteConfig.MainPhone %><a class="head-phone" href="tel:$Tel"><span class="dot-live"></span>$Number</a><% end_with %>
            <a class="btn btn-sun" href="#kontakt">Rezerviši</a>
            <button class="icon-btn menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Meni">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
