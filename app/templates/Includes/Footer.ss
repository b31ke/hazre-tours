<% include Newsletter %>
<footer class="site-foot">
    <div class="wrap foot-grid">
        <div class="foot-brand">
            <img src="images/hazre-logo-sm.png" alt="$SiteConfig.Title" width="171" height="70" loading="lazy">
            <p>Putnička agencija i prevoznik iz Srebrenika. Na putu od 2008.</p>
            <div class="socials">
                <% if $SiteConfig.FacebookURL %><a href="$SiteConfig.FacebookURL" rel="noopener" aria-label="Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M14 8h3V4h-3c-2.8 0-4 1.7-4 4.4V11H7v4h3v9h4v-9h3l1-4h-4V8.6c0-.4.3-.6.6-.6z"/></svg></a><% end_if %>
                <% if $SiteConfig.InstagramURL %><a href="$SiteConfig.InstagramURL" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.3" fill="currentColor"/></svg></a><% end_if %>
                <% if $SiteConfig.TikTokURL %><a href="$SiteConfig.TikTokURL" rel="noopener" aria-label="TikTok"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M16 3c.3 2.3 1.8 3.9 4 4.1v3.3c-1.5 0-2.8-.4-4-1.2v6.3A5.6 5.6 0 1 1 10.4 10v3.4a2.3 2.3 0 1 0 2.3 2.3V3z"/></svg></a><% end_if %>
                <% if $SiteConfig.ViberCommunityURL %><a href="$SiteConfig.ViberCommunityURL" rel="noopener" aria-label="Viber zajednica"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2C7 2 3.5 4.6 3.5 10c0 3 .9 5 2.5 6.3V21l3.4-2.5c.8.2 1.7.3 2.6.3 5 0 8.5-2.6 8.5-8.8S17 2 12 2zm3.6 12.3c-.4.6-1.4 1-2 .8-2.6-.8-4.6-2.7-5.5-5.3-.2-.6.2-1.6.8-2 .3-.2.7-.2.9.1l1 1.4c.2.3.2.7-.1 1l-.4.4c.5 1.2 1.4 2.1 2.6 2.6l.4-.4c.3-.3.7-.3 1-.1l1.4 1c.3.2.3.6-.1.5z"/></svg></a><% end_if %>
            </div>
        </div>
        <div>
            <p class="foot-h">Putovanja</p>
            <a href="putovanja">Sva putovanja</a>
            <a href="kalendar">Kalendar polazaka</a>
            <% loop $MenuCategories.Limit(5) %><a href="$Link">$Title</a><% end_loop %>
        </div>
        <div>
            <p class="foot-h">Agencija</p>
            <% loop $Menu(1) %><% if $URLSegment != 'home' %><a href="$Link">$MenuTitle</a><% end_if %><% end_loop %>
            <a href="uslovi-putovanja">Uslovi putovanja</a>
            <a href="politika-privatnosti">Politika privatnosti</a>
            <a href="#" data-consent-open>Postavke kolačića</a>
        </div>
        <div>
            <p class="foot-h">Kontakt</p>
            <% if $SiteConfig.Address %><p><% if $SiteConfig.MapURL %><a href="$SiteConfig.MapURL" rel="noopener">$SiteConfig.Address</a><% else %>$SiteConfig.Address<% end_if %></p><% end_if %>
            <% loop $SiteConfig.PhoneList %><a href="tel:$Tel" class="mono">$Number</a><% end_loop %>
            <% if $SiteConfig.ContactEmail %><a href="mailto:$SiteConfig.ContactEmail">$SiteConfig.ContactEmail</a><% end_if %>
            <% if $SiteConfig.WorkingHours %><p class="muted">$SiteConfig.WorkingHours</p><% end_if %>
        </div>
    </div>
    <div class="wrap foot-bottom">
        <span>© $CurrentYear <% if $SiteConfig.CompanyName %>$SiteConfig.CompanyName<% else %>$SiteConfig.Title<% end_if %><% if $SiteConfig.LicenseNumber %> · Licenca $SiteConfig.LicenseNumber<% end_if %></span>
        <a href="#top" class="to-top">Na vrh ↑</a>
    </div>
</footer>
