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
            <div class="chat-box">
                <p class="chat-lbl">Piši nam</p>
                <div class="chat-btns">
                    <% if $SiteConfig.ViberLink %><a class="btn btn-viber" href="$SiteConfig.ViberLink"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M11.4 0C9.473.028 5.333.344 3.02 2.467 1.302 4.187.696 6.7.633 9.817.57 12.933.488 18.776 6.12 20.36h.003l-.004 2.416s-.037.977.61 1.177c.777.242 1.234-.5 1.98-1.302.407-.44.972-1.084 1.397-1.58 3.85.326 6.812-.416 7.15-.525.776-.252 5.176-.816 5.892-6.657.74-6.02-.36-9.83-2.34-11.546-.596-.55-3.006-2.3-8.375-2.323 0 0-.395-.025-1.037-.017zm.058 1.693c.545-.004.88.017.88.017 4.542.02 6.717 1.388 7.222 1.846 1.675 1.435 2.53 4.868 1.906 9.897v.002c-.604 4.878-4.174 5.184-4.832 5.395-.28.09-2.882.737-6.153.524 0 0-2.436 2.94-3.197 3.704-.12.12-.26.167-.352.144-.13-.033-.166-.188-.165-.414l.02-4.018c-4.762-1.32-4.485-6.292-4.43-8.895.054-2.604.543-4.738 1.996-6.173 1.96-1.773 5.474-2.018 7.11-2.03zm.38 2.602c-.167 0-.303.135-.304.302 0 .167.133.303.3.305 1.624.01 2.946.537 4.028 1.592 1.073 1.046 1.62 2.468 1.633 4.334.002.167.14.3.307.3.166-.002.3-.138.3-.304-.014-1.984-.618-3.596-1.816-4.764-1.19-1.16-2.692-1.753-4.447-1.765zm-3.96.695c-.19-.032-.4.005-.616.117l-.01.002c-.43.247-.816.562-1.146.932-.002.004-.006.004-.008.008-.267.323-.42.638-.46.948-.008.046-.01.093-.007.14 0 .136.022.27.065.4l.013.01c.135.48.473 1.276 1.205 2.604.42.768.903 1.5 1.446 2.186.27.344.56.673.87.984l.132.132c.31.308.64.6.984.87.686.543 1.418 1.027 2.186 1.447 1.328.733 2.126 1.07 2.604 1.206l.01.014c.13.042.265.064.402.063.046.002.092 0 .138-.008.31-.036.627-.19.948-.46.004 0 .003-.002.008-.005.37-.33.683-.72.93-1.148l.003-.01c.225-.432.15-.842-.18-1.12-.004 0-.698-.58-1.037-.83-.36-.255-.73-.492-1.113-.71-.51-.285-1.032-.106-1.248.174l-.447.564c-.23.283-.657.246-.657.246-3.12-.796-3.955-3.955-3.955-3.955s-.037-.426.248-.656l.563-.448c.277-.215.456-.737.17-1.248-.217-.383-.454-.756-.71-1.115-.25-.34-.826-1.033-.83-1.035-.137-.165-.31-.265-.502-.297zm4.49.88c-.158.002-.29.124-.3.282-.01.167.115.312.282.324 1.16.085 2.017.466 2.645 1.15.63.688.93 1.524.906 2.57-.002.168.13.306.3.31.166.003.305-.13.31-.297.025-1.175-.334-2.193-1.067-2.994-.74-.81-1.777-1.253-3.05-1.346h-.024zm.463 1.63c-.16.002-.29.127-.3.287-.008.167.12.31.288.32.523.028.875.175 1.113.422.24.245.388.62.416 1.164.01.167.15.295.318.287.167-.008.295-.15.287-.317-.03-.644-.215-1.178-.58-1.557-.367-.378-.893-.574-1.52-.607h-.018z"/></svg>Viber</a><% end_if %>
                    <% if $SiteConfig.WhatsAppLink %><a class="btn btn-wa" href="$SiteConfig.WhatsAppLink" rel="noopener"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.5-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .5l-.3.5-.4.4c-.1.1-.3.3-.1.6.2.3.7 1.2 1.6 2 1.1 1 2 1.3 2.3 1.4.3.1.4.1.6-.1l.8-1c.2-.3.4-.2.6-.1l1.9.9c.3.1.4.2.5.3.1.1.1.7-.2 1.2z"/></svg>WhatsApp</a><% end_if %>
                </div>
            </div>
            <% end_if %>
        </div>
        <div class="glass form-card" data-reveal>
            <% include InquiryForm FormType=$FormType %>
        </div>
    </div>
    <script type="application/json" class="form-terms">$FormTermsJSON.RAW</script>
</section>
