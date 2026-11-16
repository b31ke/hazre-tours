<section class="block contact" id="kontakt" aria-labelledby="kontakt-h">
    <div class="wrap contact-grid">
        <div class="contact-info" data-reveal>
            <p class="eyebrow">Kontakt</p>
            <h2 id="kontakt-h" class="h2"><% if $FormType == 'pomjeri' %>Vaše putovanje, vaš plan<% else_if $FormType == 'autobus' %>Zatražite ponudu za prevoz<% else %>Rezervišite mjesto ili pošaljite upit<% end_if %></h2>
            <p class="muted">Prijave primamo telefonom, Viberom, e-mailom, putem forme ili lično u agenciji.</p>
            <ul class="contact-list">
                <% loop $SiteConfig.PhoneList %><li><span class="ci">☏</span><a href="tel:$Tel" class="mono">$Number</a></li><% end_loop %>
                <% if $SiteConfig.ContactEmail %><li><span class="ci">@</span><a href="mailto:$SiteConfig.ContactEmail">$SiteConfig.ContactEmail</a></li><% end_if %>
                <% if $SiteConfig.Address %><li><span class="ci">⌖</span><% if $SiteConfig.MapURL %><a href="$SiteConfig.MapURL" rel="noopener">$SiteConfig.Address</a><% else %>$SiteConfig.Address<% end_if %></li><% end_if %>
                <% if $SiteConfig.WorkingHours %><li><span class="ci">◷</span>$SiteConfig.WorkingHours</li><% end_if %>
            </ul>
            <div class="chat-btns">
                <% if $SiteConfig.ViberLink %><a class="btn btn-viber" href="$SiteConfig.ViberLink">Piši nam na Viber</a><% end_if %>
                <% if $SiteConfig.WhatsAppLink %><a class="btn btn-wa" href="$SiteConfig.WhatsAppLink" rel="noopener">WhatsApp</a><% end_if %>
            </div>
        </div>
        <div class="glass form-card" data-reveal>
            <% include InquiryForm FormType=$FormType %>
        </div>
    </div>
    <script type="application/json" class="form-terms">$FormTermsJSON.RAW</script>
</section>
