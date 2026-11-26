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
                <% if $SiteConfig.ViberCommunityURL %><a class="tb-soc is-vb" href="$SiteConfig.ViberCommunityURL" target="_blank" rel="noopener" aria-label="Viber zajednica"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M11.4 0C9.473.028 5.333.344 3.02 2.467 1.302 4.187.696 6.7.633 9.817.57 12.933.488 18.776 6.12 20.36h.003l-.004 2.416s-.037.977.61 1.177c.777.242 1.234-.5 1.98-1.302.407-.44.972-1.084 1.397-1.58 3.85.326 6.812-.416 7.15-.525.776-.252 5.176-.816 5.892-6.657.74-6.02-.36-9.83-2.34-11.546-.596-.55-3.006-2.3-8.375-2.323 0 0-.395-.025-1.037-.017zm.058 1.693c.545-.004.88.017.88.017 4.542.02 6.717 1.388 7.222 1.846 1.675 1.435 2.53 4.868 1.906 9.897v.002c-.604 4.878-4.174 5.184-4.832 5.395-.28.09-2.882.737-6.153.524 0 0-2.436 2.94-3.197 3.704-.12.12-.26.167-.352.144-.13-.033-.166-.188-.165-.414l.02-4.018c-4.762-1.32-4.485-6.292-4.43-8.895.054-2.604.543-4.738 1.996-6.173 1.96-1.773 5.474-2.018 7.11-2.03zm.38 2.602c-.167 0-.303.135-.304.302 0 .167.133.303.3.305 1.624.01 2.946.537 4.028 1.592 1.073 1.046 1.62 2.468 1.633 4.334.002.167.14.3.307.3.166-.002.3-.138.3-.304-.014-1.984-.618-3.596-1.816-4.764-1.19-1.16-2.692-1.753-4.447-1.765zm-3.96.695c-.19-.032-.4.005-.616.117l-.01.002c-.43.247-.816.562-1.146.932-.002.004-.006.004-.008.008-.267.323-.42.638-.46.948-.008.046-.01.093-.007.14 0 .136.022.27.065.4l.013.01c.135.48.473 1.276 1.205 2.604.42.768.903 1.5 1.446 2.186.27.344.56.673.87.984l.132.132c.31.308.64.6.984.87.686.543 1.418 1.027 2.186 1.447 1.328.733 2.126 1.07 2.604 1.206l.01.014c.13.042.265.064.402.063.046.002.092 0 .138-.008.31-.036.627-.19.948-.46.004 0 .003-.002.008-.005.37-.33.683-.72.93-1.148l.003-.01c.225-.432.15-.842-.18-1.12-.004 0-.698-.58-1.037-.83-.36-.255-.73-.492-1.113-.71-.51-.285-1.032-.106-1.248.174l-.447.564c-.23.283-.657.246-.657.246-3.12-.796-3.955-3.955-3.955-3.955s-.037-.426.248-.656l.563-.448c.277-.215.456-.737.17-1.248-.217-.383-.454-.756-.71-1.115-.25-.34-.826-1.033-.83-1.035-.137-.165-.31-.265-.502-.297zm4.49.88c-.158.002-.29.124-.3.282-.01.167.115.312.282.324 1.16.085 2.017.466 2.645 1.15.63.688.93 1.524.906 2.57-.002.168.13.306.3.31.166.003.305-.13.31-.297.025-1.175-.334-2.193-1.067-2.994-.74-.81-1.777-1.253-3.05-1.346h-.024zm.463 1.63c-.16.002-.29.127-.3.287-.008.167.12.31.288.32.523.028.875.175 1.113.422.24.245.388.62.416 1.164.01.167.15.295.318.287.167-.008.295-.15.287-.317-.03-.644-.215-1.178-.58-1.557-.367-.378-.893-.574-1.52-.607h-.018z"/></svg></a><% end_if %>
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
                <% loop $Menu(1) %><% if $URLSegment != 'home' %><% if not $IsFaqPage %>
                <li><a class="nav-link<% if $isCurrent || $isSection %> on<% end_if %>" href="$Link">$MenuTitle</a></li>
                <% end_if %><% end_if %><% end_loop %>
                <li><a class="nav-link" href="#kontakt">Kontakt</a></li>
                <% loop $Menu(1) %><% if $IsFaqPage %>
                <li><a class="nav-link<% if $isCurrent %> on<% end_if %>" href="$Link">$MenuTitle</a></li>
                <% end_if %><% end_loop %>
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
    <% if $SiteConfig.IsFestive %><div class="xl" aria-hidden="true"></div><% end_if %>
</header>
