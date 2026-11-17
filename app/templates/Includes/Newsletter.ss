<section class="news" id="novosti" aria-labelledby="news-h">
    <div class="wrap">
        <div class="news-card" data-reveal>
            <svg class="news-trail" viewBox="0 0 600 260" preserveAspectRatio="none" aria-hidden="true"><path d="M-10 230 C 120 220, 160 120, 280 140 S 470 200, 560 60" fill="none" stroke="rgba(255,255,255,.35)" stroke-width="2.5" stroke-dasharray="3 11" stroke-linecap="round"/></svg>
            <svg class="news-plane" viewBox="0 0 64 64" aria-hidden="true"><path d="M60 6 4 28l20 7 4 21 9-13 13 9z" fill="#fff"/><path d="m24 35 36-29-23 37z" fill="#F7A11A"/><path d="m28 56 2-14 7 1z" fill="#D9E6FF"/></svg>
            <div class="news-copy">
                <p class="news-kicker">Ne propustite akciju</p>
                <h2 id="news-h">Prvi saznajte za nova putovanja i <em>first minute</em> cijene</h2>
                <ul class="news-perks">
                    <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>Novi polasci</li>
                    <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>Akcijske cijene</li>
                    <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>Bez spama</li>
                </ul>
            </div>
            <form class="news-form js-form" method="post" action="newsletter">
                <input type="hidden" name="$Top.InquiryTokenName" value="$Top.InquiryToken">
                <div class="hp" aria-hidden="true"><input name="website" tabindex="-1" autocomplete="off"></div>
                <div class="news-row">
                    <span class="news-at" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
                    <label class="sr" for="nl-email">E-mail</label>
                    <input id="nl-email" name="email" type="email" placeholder="Vaša e-mail adresa" autocomplete="email" required>
                    <button class="news-btn" type="submit">Prijavi me <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4z"/></svg></button>
                </div>
                <label class="check"><input type="checkbox" name="consent" value="1" required> <span>Želim primati obavijesti o putovanjima Hazre Toursa. Odjava jednim klikom.</span></label>
                <% if $SiteConfig.ViberCommunityURL %><a class="viber-cta" href="$SiteConfig.ViberCommunityURL" rel="noopener">Ili se pridružite našoj Viber zajednici →</a><% end_if %>
                <p class="form-msg" role="status" hidden></p>
            </form>
        </div>
    </div>
</section>
