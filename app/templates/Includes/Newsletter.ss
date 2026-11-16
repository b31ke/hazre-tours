<section class="news" id="novosti" aria-labelledby="news-h">
    <div class="wrap news-in" data-reveal>
        <div>
            <p class="eyebrow">Novosti</p>
            <h2 id="news-h">Prvi saznajte za nova putovanja i akcije</h2>
            <p class="muted">Jedan mail kad objavimo nove polaske ili first minute cijene. Bez spama, odjava jednim klikom.</p>
        </div>
        <form class="news-form js-form" method="post" action="newsletter">
            <input type="hidden" name="$Top.InquiryTokenName" value="$Top.InquiryToken">
            <div class="hp" aria-hidden="true"><input name="website" tabindex="-1" autocomplete="off"></div>
            <div class="news-row">
                <label class="sr" for="nl-email">E-mail</label>
                <input id="nl-email" name="email" type="email" placeholder="vas@email.com" autocomplete="email" required>
                <button class="btn btn-sun" type="submit">Prijavi me</button>
            </div>
            <label class="check"><input type="checkbox" name="consent" value="1" required> <span>Želim primati obavijesti o putovanjima Hazre Toursa.</span></label>
            <% if $SiteConfig.ViberCommunityURL %><a class="viber-cta" href="$SiteConfig.ViberCommunityURL" rel="noopener">Ili se pridružite našoj Viber zajednici →</a><% end_if %>
            <p class="form-msg" role="status" hidden></p>
        </form>
    </div>
</section>
