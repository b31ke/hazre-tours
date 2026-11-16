<% if $SiteConfig.GA4Id || $SiteConfig.MetaPixelId %>
<div class="consent" id="consent" role="dialog" aria-label="Kolačići" hidden>
    <p>Koristimo kolačiće za mjerenje posjeta i oglasa, da bismo vam pokazivali putovanja koja vas zanimaju. Sajt radi i bez njih.</p>
    <div class="consent-btns">
        <button type="button" class="btn btn-ghost" data-consent="no">Samo neophodni</button>
        <button type="button" class="btn btn-sun" data-consent="yes">Prihvatam</button>
    </div>
</div>
<% end_if %>
