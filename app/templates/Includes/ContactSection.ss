<section class="block contact" id="kontakt" aria-labelledby="kontakt-h">
    <div class="wrap contact-grid">
        <div class="contact-card" data-reveal>
            <svg class="cc-route" viewBox="0 0 400 600" preserveAspectRatio="none" aria-hidden="true"><path d="M-20 560 C 120 480, 60 360, 200 320 S 360 160, 430 40" fill="none" stroke="rgba(255,255,255,.18)" stroke-width="3" stroke-dasharray="2 12" stroke-linecap="round"/></svg>
            <p class="cc-kicker">Javite nam se</p>
            <h2 id="kontakt-h" class="cc-title"><% if $FormType == 'pomjeri' %>Vaše putovanje, <em>vaš plan</em><% else_if $FormType == 'autobus' %>Zatražite ponudu <em>za prevoz</em><% else %>Rezervišite mjesto <em>za tren</em><% end_if %></h2>
            <p class="cc-lead">Pozovite, pišite ili popunite formu. Odgovaramo brzo, a mjesto vam čuvamo dok ne potvrdite.</p>

            <% with $SiteConfig.MainPhone %>
            <a class="cc-call" href="tel:$Tel">
                <span class="cc-call-ico" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2"/></svg></span>
                <span class="cc-call-txt"><small>Pozovite odmah</small><b>$Number</b></span>
                <svg class="cc-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <% end_with %>

            <div class="cc-tiles">
                <% loop $SiteConfig.OtherPhones %>
                <a class="cc-tile" href="tel:$Tel">
                    <span class="cc-ico" aria-hidden="true"><% if $IsMobile %><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="2" width="12" height="20" rx="3"/><path d="M11 18h2"/></svg><% else %><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2"/></svg><% end_if %></span>
                    <span><small><% if $IsMobile %>Mobilni<% else %>Fiksni<% end_if %></small><b>$Number</b></span>
                </a>
                <% end_loop %>
                <% if $SiteConfig.ContactEmail %>
                <a class="cc-tile wide" href="mailto:$SiteConfig.ContactEmail">
                    <span class="cc-ico" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
                    <span><small>E-mail</small><b>$SiteConfig.ContactEmail</b></span>
                </a>
                <% end_if %>
                <% if $SiteConfig.Address %>
                <a class="cc-tile wide" href="$SiteConfig.MapsLink" rel="noopener" target="_blank">
                    <span class="cc-ico" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
                    <span><small>Posjetite nas<% if $SiteConfig.WorkingHours %> · $SiteConfig.WorkingHours<% end_if %></small><b>$SiteConfig.Address</b></span>
                    <span class="cc-map">Mapa <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg></span>
                </a>
                <% end_if %>
            </div>

            <% if $SiteConfig.ViberLink || $SiteConfig.WhatsAppLink %>
            <p class="chat-lbl">Piši nam</p>
            <div class="chat-btns">
                <% if $SiteConfig.ViberLink %><a class="btn btn-viber" href="$SiteConfig.ViberLink">Viber</a><% end_if %>
                <% if $SiteConfig.WhatsAppLink %><a class="btn btn-wa" href="$SiteConfig.WhatsAppLink" rel="noopener">WhatsApp</a><% end_if %>
            </div>
            <% end_if %>
        </div>
        <div class="glass form-card" data-reveal>
            <% include InquiryForm FormType=$FormType %>
        </div>
    </div>
    <script type="application/json" class="form-terms">$FormTermsJSON.RAW</script>
</section>
