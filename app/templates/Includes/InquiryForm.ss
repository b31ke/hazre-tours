<section class="block" id="kontakt" aria-labelledby="kontakt-title">
    <div class="wrap contact">
        <div>
            <p class="eyebrow">Kontakt</p>
            <h2 id="kontakt-title" class="h-section">Rezervišite mjesto ili pošaljite upit</h2>
            <p class="muted" style="margin-top:12px;max-width:48ch">Prijave primamo telefonom, e-mailom, putem forme ili lično u agenciji.</p>
            <dl>
                <% if $SiteConfig.CompanyName %><dt>Firma</dt><dd>$SiteConfig.CompanyName</dd><% end_if %>
                <% if $SiteConfig.Address %><dt>Adresa</dt><dd>$SiteConfig.Address</dd><% end_if %>
                <% if $SiteConfig.PhoneList.Count %><dt>Telefon</dt><dd><% loop $SiteConfig.PhoneList %><a href="tel:$Tel">$Number</a><% if not $IsLast %><br><% end_if %><% end_loop %></dd><% end_if %>
                <% if $SiteConfig.ContactEmail %><dt>E-mail</dt><dd><a href="mailto:$SiteConfig.ContactEmail">$SiteConfig.ContactEmail</a></dd><% end_if %>
                <% if $SiteConfig.WorkingHours %><dt>Radno vrijeme</dt><dd>$SiteConfig.WorkingHours</dd><% end_if %>
            </dl>
        </div>
        <form class="inq" id="inq" method="post" action="upit" novalidate>
            <h3 class="full">Forma za rezervaciju</h3>
            <input type="hidden" name="$InquiryTokenName" value="$InquiryToken">
            <input type="hidden" name="BackURL" value="$Link">
            <div class="hp" aria-hidden="true"><label for="f-website">Web stranica</label><input id="f-website" name="website" tabindex="-1" autocomplete="off"></div>
            <div class="field full"><label for="f-trip">Aranžman</label>
                <select id="f-trip" name="trip">
                    <option value="">Opšti upit</option>
                    <% loop $FormTrips %><option value="$ID"<% if $Up.Trip.ID == $ID %> selected<% end_if %>>$Title (<% if $HasPriceFrom %>od <% end_if %>$MinPrice KM)</option><% end_loop %>
                </select>
            </div>
            <div class="field"><label for="f-term">Termin</label><select id="f-term" name="term"><option value="">Izaberite aranžman</option></select></div>
            <div class="field"><label for="f-from">Mjesto polaska</label>
                <select id="f-from" name="departure">
                    <option>Tuzla</option><option selected>Srebrenik</option><option>Gračanica</option><option>Gradačac</option><option>Brčko</option><option>Orašje</option><option>Bijeljina</option><option>Kalesija</option><option>Šićki Brod</option>
                </select>
            </div>
            <div class="field"><label for="f-adults">Odraslih</label><input id="f-adults" name="adults" type="number" min="1" value="2" inputmode="numeric"></div>
            <div class="field"><label for="f-kids">Djece do 12 god.</label><input id="f-kids" name="kids" type="number" min="0" value="0" inputmode="numeric"></div>
            <div class="field"><label for="f-name">Ime i prezime</label><input id="f-name" name="name" autocomplete="name" required><span class="err" data-err="name" hidden></span></div>
            <div class="field"><label for="f-phone">Telefon</label><input id="f-phone" name="phone" type="tel" autocomplete="tel" placeholder="06x xxx xxx" required><span class="err" data-err="phone" hidden></span></div>
            <div class="field full"><label for="f-mail">E-mail (nije obavezno)</label><input id="f-mail" name="email" type="email" autocomplete="email"><span class="err" data-err="email" hidden></span></div>
            <div class="field full"><label for="f-msg">Napomena</label><textarea id="f-msg" name="message" placeholder="Jednokrevetna soba, željeno sjedište, PZO preko agencije…"></textarea></div>
            <label class="check full"><input type="checkbox" id="f-ok" name="terms" value="1"> <span>Upoznat/a sam s <a href="uslovi-putovanja/">opštim uslovima putovanja</a>.</span></label>
            <span class="err full" data-err="terms" hidden></span>
            <div class="full"><button class="btn" type="submit">Pošalji rezervaciju</button></div>
            <p class="notice full" id="inq-msg" role="status"<% if not $InquirySent %> hidden<% end_if %>>Hvala! Upit je zaprimljen, javit ćemo vam se radi potvrde.</p>
        </form>
        <script type="application/json" id="form-terms">$FormTermsJSON.RAW</script>
    </div>
</section>
