<form class="inq js-form" method="post" action="upit" novalidate data-type="$FormType">
    <input type="hidden" name="$Top.InquiryTokenName" value="$Top.InquiryToken">
    <input type="hidden" name="BackURL" value="$Top.Link">
    <input type="hidden" name="type" value="$FormType">
    <div class="hp" aria-hidden="true"><label>Web stranica<input name="website" tabindex="-1" autocomplete="off"></label></div>

    <% if $FormType == 'rezervacija' %>
        <div class="field full"><label for="f-trip-$FormType">Aranžman</label>
            <select id="f-trip-$FormType" name="trip" class="js-trip">
                <option value="">Opšti upit</option>
                <% loop $Top.FormTrips %><option value="$ID"<% if $Top.Trip.ID == $ID %> selected<% end_if %>>$Title · <% if $HasPriceFrom %>od <% end_if %>$MinPrice KM</option><% end_loop %>
            </select>
        </div>
        <div class="field"><label for="f-term-$FormType">Termin</label><select id="f-term-$FormType" name="term" class="js-term"><option value="">Izaberite aranžman</option></select></div>
        <div class="field"><label for="f-from-$FormType">Mjesto polaska</label>
            <select id="f-from-$FormType" name="departure">
                <option>Tuzla</option><option selected>Srebrenik</option><option>Gračanica</option><option>Gradačac</option><option>Brčko</option><option>Orašje</option><option>Bijeljina</option><option>Kalesija</option><option>Šićki Brod</option>
            </select>
        </div>
    <% end_if %>

    <% if $FormType == 'pomjeri' %>
        <div class="field full"><label for="f-dest">Kuda biste željeli putovati?</label><input id="f-dest" name="destination" placeholder="Npr. Rim i Firenca, ljetovanje u Turskoj, ekskurzija u Beč"><span class="err" data-err="destination" hidden></span></div>
    <% end_if %>

    <% if $FormType == 'izmjena' %>
        <input type="hidden" name="trip" value="$Top.Trip.ID">
        <fieldset class="field full chips-field">
            <legend>Šta biste željeli promijeniti?</legend>
            <label class="pick"><input type="checkbox" name="changes[]" value="Termin"><span>Termin</span></label>
            <label class="pick"><input type="checkbox" name="changes[]" value="Trajanje"><span>Trajanje</span></label>
            <label class="pick"><input type="checkbox" name="changes[]" value="Cijena ili plaćanje"><span>Cijena ili plaćanje</span></label>
            <label class="pick"><input type="checkbox" name="changes[]" value="Prevoz"><span>Prevoz</span></label>
            <label class="pick"><input type="checkbox" name="changes[]" value="Smještaj"><span>Smještaj</span></label>
            <label class="pick"><input type="checkbox" name="changes[]" value="Program i izleti"><span>Program i izleti</span></label>
            <label class="pick"><input type="checkbox" name="changes[]" value="Broj putnika"><span>Broj putnika (grupa)</span></label>
            <label class="pick"><input type="checkbox" name="changes[]" value="Nešto drugo"><span>Nešto drugo</span></label>
        </fieldset>
    <% end_if %>

    <% if $FormType == 'pomjeri' || $FormType == 'izmjena' %>
        <div class="field"><label for="f-period-$FormType">Koji termin ili period vam odgovara?</label><input id="f-period-$FormType" name="period" placeholder="Npr. druga polovina maja"></div>
        <div class="field"><label for="f-dur-$FormType">Koliko dana?</label>
            <select id="f-dur-$FormType" name="duration"><option value="">Izaberite</option><option>1 dan</option><option>2–3 dana</option><option>4–7 dana</option><option>8–10 dana</option><option>Više od 10 dana</option><option>Nisam siguran/na</option></select>
        </div>
    <% end_if %>

    <% if $FormType == 'autobus' %>
        <div class="field"><label for="f-bfrom">Polazak iz</label><input id="f-bfrom" name="departure" placeholder="Npr. Srebrenik"></div>
        <div class="field"><label for="f-bto">Odredište</label><input id="f-bto" name="destination" placeholder="Npr. Beč, aerodrom Sarajevo"></div>
        <div class="field"><label for="f-bdate">Datum ili period</label><input id="f-bdate" name="period" placeholder="Npr. 15.05. – 17.05."></div>
        <div class="field"><label for="f-bpax">Broj putnika</label><input id="f-bpax" name="adults" type="number" min="1" value="40" inputmode="numeric"></div>
    <% else %>
        <div class="field"><label for="f-adults-$FormType">Odraslih</label><input id="f-adults-$FormType" name="adults" type="number" min="1" value="2" inputmode="numeric"></div>
        <div class="field"><label for="f-kids-$FormType">Djece do 12 god.</label><input id="f-kids-$FormType" name="kids" type="number" min="0" value="0" inputmode="numeric" class="js-kids"></div>
        <% if $FormType != 'rezervacija' %>
        <div class="field js-kids-ages" hidden><label for="f-ages-$FormType">Uzrast djece</label><input id="f-ages-$FormType" name="kids_ages" placeholder="Npr. 5 i 11 godina"></div>
        <div class="field"><label for="f-budget-$FormType">Okvirni budžet po osobi</label><input id="f-budget-$FormType" name="budget" placeholder="Npr. do 500 KM"></div>
        <fieldset class="field full chips-field">
            <legend>Smještaj</legend>
            <label class="pick"><input type="radio" name="accommodation" value="Hotel 3*"><span>Hotel 3*</span></label>
            <label class="pick"><input type="radio" name="accommodation" value="Hotel 4*"><span>Hotel 4*</span></label>
            <label class="pick"><input type="radio" name="accommodation" value="Hotel 5*"><span>Hotel 5*</span></label>
            <label class="pick"><input type="radio" name="accommodation" value="Apartman"><span>Apartman</span></label>
            <label class="pick"><input type="radio" name="accommodation" value="Svejedno" checked><span>Nemam posebnu želju</span></label>
        </fieldset>
        <% end_if %>
    <% end_if %>

    <div class="field"><label for="f-name-$FormType">Ime i prezime</label><input id="f-name-$FormType" name="name" autocomplete="name" required><span class="err" data-err="name" hidden></span></div>
    <div class="field"><label for="f-phone-$FormType">Telefon (Viber)</label><input id="f-phone-$FormType" name="phone" type="tel" autocomplete="tel" placeholder="06x xxx xxx" required><span class="err" data-err="phone" hidden></span></div>
    <div class="field full"><label for="f-mail-$FormType">E-mail <small>(nije obavezno)</small></label><input id="f-mail-$FormType" name="email" type="email" autocomplete="email"><span class="err" data-err="email" hidden></span></div>
    <div class="field full"><label for="f-msg-$FormType"><% if $FormType == 'rezervacija' %>Napomena<% else %>Opišite svoje idealno putovanje<% end_if %></label>
        <textarea id="f-msg-$FormType" name="message" placeholder="<% if $FormType == 'rezervacija' %>Jednokrevetna soba, željeno sjedište, PZO preko agencije…<% else_if $FormType == 'autobus' %>Povratak isti dan ili s noćenjem, usputna stajališta…<% else %>Tempo putovanja, hotel, izleti, posebne želje…<% end_if %>"></textarea>
    </div>
    <label class="check full"><input type="checkbox" name="terms" value="1"> <span>Saglasan/na sam da Hazre Tours koristi ove podatke za pripremu ponude i kontakt, i upoznat/a sam s <a href="uslovi-putovanja">uslovima putovanja</a>.</span></label>
    <span class="err full" data-err="terms" hidden></span>
    <div class="full form-actions">
        <button class="btn btn-sun btn-lg" type="submit"><span><% if $FormType == 'rezervacija' %>Pošalji rezervaciju<% else_if $FormType == 'autobus' %>Zatraži ponudu za prevoz<% else %>Pošalji upit<% end_if %></span></button>
        <span class="muted small">Odgovaramo istog radnog dana.</span>
    </div>
    <p class="form-msg full" role="status"<% if not $Top.InquirySent %> hidden<% end_if %>>Hvala! Upit je zaprimljen, javit ćemo vam se.</p>
</form>
