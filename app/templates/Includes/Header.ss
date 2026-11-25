<div class="topbar">
    <div class="wrap topbar-in">
        <% with $TopbarTrip %><% with $NextTerm %>
        <a class="tb-next" href="$Up.Link">
            <span class="tb-live" aria-hidden="true"></span>
            <span class="tb-lbl">Sljedeći polazak</span>
            <b>$StartDate.Format('dd.MM.')</b> <span class="tb-trip">$Up.Title</span>
            <span class="tb-cd">$Countdown</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <% end_with %><% end_with %>
        <span class="tb-right">
            <% if $SiteConfig.Address %><a class="tb-item tb-addr" href="$SiteConfig.MapsLink" target="_blank" rel="noopener" title="Otvori na Google mapi"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>$SiteConfig.Address</a><% end_if %>
            <% if $SiteConfig.ContactEmail %><a class="tb-item tb-mail" href="mailto:$SiteConfig.ContactEmail"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>$SiteConfig.ContactEmail</a><% end_if %>
            <% if $SiteConfig.FacebookURL %><a class="tb-soc" href="$SiteConfig.FacebookURL" rel="noopener" aria-label="Facebook"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H7v4h3v8h4v-8h3l1-4h-4V8z"/></svg></a><% end_if %>
            <% if $SiteConfig.InstagramURL %><a class="tb-soc" href="$SiteConfig.InstagramURL" rel="noopener" aria-label="Instagram"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg></a><% end_if %>
            <% with $SiteConfig.MainPhone %><a class="tb-phone" href="tel:$Tel"><span class="tb-ring" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2"/></svg></span>$Number</a><% end_with %>
        </span>
    </div>
</div>
<header class="site-head" id="top">
    <div class="wrap head-row">
        <a class="logo" href="$BaseHref" aria-label="$SiteConfig.Title, početna">
            <img src="images/hazre-logo-sm.png" alt="$SiteConfig.Title" width="171" height="70">
        </a>
        <nav class="main-nav" id="main-nav" aria-label="Glavni meni">
            <div class="nav-head">
                <a class="nav-logo" href="$BaseHref" aria-label="$SiteConfig.Title, početna"><img src="images/hazre-logo-sm.png" alt="" width="137" height="56"></a>
                <button class="nav-close" type="button" aria-label="Zatvori meni"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
            </div>
            <ul>
                <li><a class="nav-link<% if $URLSegment == 'home' %> on<% end_if %>" href="$BaseHref">Početna</a></li>
                <li class="has-drop">
                    <a href="putovanja" class="nav-link<% if $BodyClass == 'CategoryController' || $BodyClass == 'TripController' %> on<% end_if %>" aria-haspopup="true">Putovanja <svg width="10" height="10" viewBox="0 0 10 10" aria-hidden="true"><path d="M1 3l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.6"/></svg></a>
                    <button class="drop-toggle" type="button" aria-expanded="false" aria-label="Prikaži vrste putovanja"><svg width="14" height="14" viewBox="0 0 10 10" aria-hidden="true"><path d="M1 3l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.6"/></svg></button>
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
            <div class="nav-foot">
                <% with $SiteConfig.MainPhone %><a class="nav-call" href="tel:$Tel"><span aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2"/></svg></span><b>$Number</b></a><% end_with %>
                <p class="nav-motto">Gdje god krenete, <em>mi vozimo</em></p>
            </div>
        </nav>
        <div class="nav-backdrop" aria-hidden="true"></div>
        <div class="head-actions">
            <button class="icon-btn menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Meni">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
