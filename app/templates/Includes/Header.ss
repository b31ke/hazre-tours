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
            <% with $SiteConfig.MainPhone %><a class="tb-phone" href="tel:$Tel"><span class="tb-ring" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2"/></svg></span>$Number</a><% end_with %>
            <% if $SiteConfig.FacebookURL || $SiteConfig.InstagramURL || $SiteConfig.TikTokURL || $SiteConfig.ViberCommunityURL %><span class="tb-socials">
                <% if $SiteConfig.FacebookURL %><a class="tb-soc is-fb" href="$SiteConfig.FacebookURL" target="_blank" rel="noopener" aria-label="Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M15.12 5.32H17V2.14A26.11 26.11 0 0 0 14.26 2c-2.72 0-4.58 1.66-4.58 4.7v2.62H6.61v3.56h3.07V22h3.68v-9.12h3.06l.46-3.56h-3.52V7.05c0-1.03.28-1.73 1.76-1.73z"/></svg></a><% end_if %>
                <% if $SiteConfig.InstagramURL %><a class="tb-soc is-ig" href="$SiteConfig.InstagramURL" target="_blank" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.1"><rect x="3" y="3" width="18" height="18" rx="5.2"/><circle cx="12" cy="12" r="4.1"/><circle cx="17.4" cy="6.6" r="1.15" fill="currentColor" stroke="none"/></svg></a><% end_if %>
                <% if $SiteConfig.TikTokURL %><a class="tb-soc is-tt" href="$SiteConfig.TikTokURL" target="_blank" rel="noopener" aria-label="TikTok"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M16 3c.3 2.3 1.8 3.9 4 4.1v3.3c-1.5 0-2.8-.4-4-1.2v6.3A5.6 5.6 0 1 1 10.4 10v3.4a2.3 2.3 0 1 0 2.3 2.3V3z"/></svg></a><% end_if %>
                <% if $SiteConfig.ViberCommunityURL %><a class="tb-soc is-vb" href="$SiteConfig.ViberCommunityURL" target="_blank" rel="noopener" aria-label="Viber zajednica"><svg viewBox="0 0 48 48" aria-hidden="true"><path fill="currentColor" d="M24 9.5c-7.6 0-13 3.8-13 11.6 0 4.1 1.3 7 3.6 8.8v6.4l4.8-3.5c1.4.3 2.9.4 4.6.4 7.6 0 13-3.7 13-12.1S31.6 9.5 24 9.5zm0 21.4c-1.6 0-3-.1-4.3-.5l-2.5 1.9v-3.1l-.6-.4c-1.8-1.3-2.8-3.6-2.8-7.7 0-6.2 4.3-9.4 10.2-9.4s10.2 3.2 10.2 9.4c0 6.6-4.3 9.8-10.2 9.8z"/><path fill="currentColor" d="M28.6 26.2c-.6.9-1.8 1.3-2.7 1-3.5-1.1-6.2-3.7-7.4-7.2-.3-.9.2-2.1 1-2.6.4-.3 1-.2 1.3.2l1.3 1.9c.3.4.2 1-.2 1.3l-.5.4c.7 1.5 1.9 2.7 3.4 3.4l.4-.5c.3-.4.9-.5 1.3-.2l1.9 1.3c.4.3.5.7.2 1z"/></svg></a><% end_if %>
            </span><% end_if %>
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
