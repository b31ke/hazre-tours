<div class="fab" data-fab>
    <div class="fab-items" id="fab-items">
        <% if $SiteConfig.ViberLink %><a class="fab-item viber" href="$SiteConfig.ViberLink"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2C7 2 3.5 4.6 3.5 10c0 3 .9 5 2.5 6.3V21l3.4-2.5c.8.2 1.7.3 2.6.3 5 0 8.5-2.6 8.5-8.8S17 2 12 2zm3.6 12.3c-.4.6-1.4 1-2 .8-2.6-.8-4.6-2.7-5.5-5.3-.2-.6.2-1.6.8-2 .3-.2.7-.2.9.1l1 1.4c.2.3.2.7-.1 1l-.4.4c.5 1.2 1.4 2.1 2.6 2.6l.4-.4c.3-.3.7-.3 1-.1l1.4 1c.3.2.3.6-.1.5z"/></svg>Viber</a><% end_if %>
        <% if $SiteConfig.WhatsAppLink %><a class="fab-item wa" href="$SiteConfig.WhatsAppLink" rel="noopener"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.5-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .5l-.3.5-.4.4c-.1.1-.3.3-.1.6.2.3.7 1.2 1.6 2 1.1 1 2 1.3 2.3 1.4.3.1.4.1.6-.1l.8-1c.2-.3.4-.2.6-.1l1.9.9c.3.1.4.2.5.3.1.1.1.7-.2 1.2z"/></svg>WhatsApp</a><% end_if %>
        <% with $SiteConfig.MainPhone %><a class="fab-item call" href="tel:$Tel"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2"/></svg>$Number</a><% end_with %>
    </div>
    <button class="fab-main" type="button" aria-expanded="false" aria-controls="fab-items" aria-label="Kontakt: poziv, Viber, WhatsApp">
        <svg class="i-open" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M4 5h16v11H8l-4 4z"/></svg>
        <svg class="i-close" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg>
    </button>
</div>
