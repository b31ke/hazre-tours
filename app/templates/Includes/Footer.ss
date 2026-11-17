<% include Newsletter %>
<footer class="site-foot">
    <div class="foot-scene" aria-hidden="true">
        <svg class="foot-wave" viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0 0 H1440 V20 C 1180 60, 900 0, 720 24 S 260 60, 0 18 Z" fill="#F3F7FC"/></svg>
        <svg class="foot-skyline" viewBox="0 0 1440 120" preserveAspectRatio="xMidYMax slice">
            <g fill="rgba(255,255,255,.07)">
                <!-- Istanbul: džamija s kupolom i minaretima -->
                <rect x="60" y="40" width="4" height="80"/><path d="M58 40 62 26 66 40z"/>
                <path d="M80 120V84a34 30 0 0 1 68 0v36z"/><rect x="108" y="44" width="12" height="12" rx="6"/>
                <rect x="164" y="40" width="4" height="80"/><path d="M162 40 166 26 170 40z"/>
                <!-- Graz: sahat-kula -->
                <rect x="250" y="62" width="34" height="58"/><path d="M244 62 267 30 290 62z"/><circle cx="267" cy="78" r="7" fill="rgba(255,255,255,.12)"/>
                <!-- niske kuće -->
                <path d="M300 120V92l16-12 16 12v28zM336 120V98l14-10 14 10v22z"/>
                <!-- Budimpešta: parlament -->
                <path d="M420 120V86h40V70a22 22 0 0 1 44 0v16h40v34z"/><rect x="478" y="34" width="8" height="16"/><path d="M476 34 482 18 488 34z"/>
                <rect x="428" y="72" width="6" height="14"/><rect x="530" y="72" width="6" height="14"/>
                <!-- Beč: katedrala -->
                <path d="M640 120V70l14-12 14 12v50z"/><path d="M700 120V48l8-30 8 30v72z"/><path d="M730 120V80h30v40z"/>
                <!-- Ljubljana: dvorac na brdu -->
                <path d="M820 120c30-26 70-34 110-34s70 10 100 34z"/><rect x="890" y="66" width="22" height="26"/><rect x="896" y="54" width="10" height="14"/>
                <!-- Zlatibor: planine i jele -->
                <path d="M1060 120l60-56 40 34 50-44 70 66z"/><path d="M1240 120l14-34 14 34zM1262 120l12-28 12 28z"/>
                <!-- sahat-kula -->
                <rect x="1330" y="52" width="20" height="68"/><path d="M1326 52 1340 32 1354 52z"/>
            </g>
        </svg>
        <div class="foot-road"></div>
        <svg class="foot-bus" viewBox="0 0 120 52"><rect x="4" y="6" width="104" height="34" rx="9" fill="#F7A11A"/><rect x="12" y="12" width="16" height="12" rx="2" fill="#E3F6FC"/><rect x="32" y="12" width="16" height="12" rx="2" fill="#E3F6FC"/><rect x="52" y="12" width="16" height="12" rx="2" fill="#E3F6FC"/><rect x="72" y="12" width="16" height="12" rx="2" fill="#E3F6FC"/><path d="M92 12h8a6 6 0 0 1 6 6v6H92z" fill="#E3F6FC"/><rect x="4" y="29" width="104" height="4" fill="#fff" opacity=".85"/><circle cx="28" cy="42" r="7" fill="#0E2466"/><circle cx="28" cy="42" r="3" fill="#fff"/><circle cx="86" cy="42" r="7" fill="#0E2466"/><circle cx="86" cy="42" r="3" fill="#fff"/></svg>
    </div>

    <div class="wrap foot-grid">
        <div class="foot-brand">
            <img src="images/hazre-logo-sm.png" alt="$SiteConfig.Title" width="171" height="70" loading="lazy">
            <p class="foot-motto">Gdje god krenete,<br><em>mi vozimo</em></p>
            <p class="foot-from"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="3"/><path d="M3 11h18"/><circle cx="7.5" cy="19" r="1.6"/><circle cx="16.5" cy="19" r="1.6"/></svg><span><b>Polasci iz:</b> <% loop $FooterCities %>$Name<% if not $IsLast %> · <% end_if %><% end_loop %></span></p>
            <div class="socials">
                <% if $SiteConfig.FacebookURL %><a href="$SiteConfig.FacebookURL" rel="noopener" aria-label="Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M14 8h3V4h-3c-2.8 0-4 1.7-4 4.4V11H7v4h3v9h4v-9h3l1-4h-4V8.6c0-.4.3-.6.6-.6z"/></svg></a><% end_if %>
                <% if $SiteConfig.InstagramURL %><a href="$SiteConfig.InstagramURL" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.3" fill="currentColor"/></svg></a><% end_if %>
                <% if $SiteConfig.TikTokURL %><a href="$SiteConfig.TikTokURL" rel="noopener" aria-label="TikTok"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M16 3c.3 2.3 1.8 3.9 4 4.1v3.3c-1.5 0-2.8-.4-4-1.2v6.3A5.6 5.6 0 1 1 10.4 10v3.4a2.3 2.3 0 1 0 2.3 2.3V3z"/></svg></a><% end_if %>
                <% if $SiteConfig.ViberCommunityURL %><a href="$SiteConfig.ViberCommunityURL" rel="noopener" aria-label="Viber zajednica"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2C7 2 3.5 4.6 3.5 10c0 3 .9 5 2.5 6.3V21l3.4-2.5c.8.2 1.7.3 2.6.3 5 0 8.5-2.6 8.5-8.8S17 2 12 2zm3.6 12.3c-.4.6-1.4 1-2 .8-2.6-.8-4.6-2.7-5.5-5.3-.2-.6.2-1.6.8-2 .3-.2.7-.2.9.1l1 1.4c.2.3.2.7-.1 1l-.4.4c.5 1.2 1.4 2.1 2.6 2.6l.4-.4c.3-.3.7-.3 1-.1l1.4 1c.3.2.3.6-.1.5z"/></svg></a><% end_if %>
            </div>
        </div>
        <nav class="foot-col" aria-label="Putovanja">
            <p class="foot-h">Putovanja</p>
            <a href="putovanja">Sva putovanja</a>
            <a href="kalendar">Kalendar polazaka</a>
            <% loop $MenuCategories.Limit(5) %><a href="$Link">$Title</a><% end_loop %>
        </nav>
        <nav class="foot-col" aria-label="Agencija">
            <p class="foot-h">Agencija</p>
            <% loop $Menu(1) %><% if $URLSegment != 'home' %><a href="$Link">$MenuTitle</a><% end_if %><% end_loop %>
            <a href="uslovi-putovanja">Uslovi putovanja</a>
            <a href="politika-privatnosti">Politika privatnosti</a>
            <a href="#" data-consent-open>Postavke kolačića</a>
        </nav>
        <div class="foot-col foot-contact">
            <p class="foot-h">Kontakt</p>
            <% with $SiteConfig.MainPhone %><a class="foot-call" href="tel:$Tel"><span class="fc-ico" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2"/></svg></span><b>$Number</b></a><% end_with %>
            <div class="foot-phones"><% loop $SiteConfig.OtherPhones %><a href="tel:$Tel">$Number</a><% end_loop %></div>
            <% if $SiteConfig.ContactEmail %><a class="foot-line" href="mailto:$SiteConfig.ContactEmail"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>$SiteConfig.ContactEmail</a><% end_if %>
            <% if $SiteConfig.Address %><a class="foot-line" href="$SiteConfig.MapsLink" rel="noopener" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>$SiteConfig.Address</a><% end_if %>
            <% if $SiteConfig.WorkingHours %><p class="foot-line muted">$SiteConfig.WorkingHours</p><% end_if %>
        </div>
    </div>

    <p class="foot-mark" aria-hidden="true">HAZRE TOURS</p>

    <div class="wrap foot-bottom">
        <span>© $CurrentYear <% if $SiteConfig.CompanyName %>$SiteConfig.CompanyName<% else %>$SiteConfig.Title<% end_if %><% if $SiteConfig.LicenseNumber %> · Licenca $SiteConfig.LicenseNumber<% end_if %></span>
        <a href="#top" class="to-top">Na vrh <span aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M12 19V5M6 11l6-6 6 6"/></svg></span></a>
    </div>
</footer>
