<section class="page-hero">
    <div class="page-hero-glow" aria-hidden="true"></div>
    <div class="wrap">
        <p class="ph-kicker">Hazre Tours</p>
        <h1 class="h1">Izjava putnika</h1>
        <p class="hero-lead">Izjava o upoznavanju i prihvatanju Opštih uslova putovanja</p>
    </div>
    <svg class="ph-wave" viewBox="0 0 1440 60" preserveAspectRatio="none" aria-hidden="true"><path d="M0 60 H1440 V30 C 1180 0, 900 56, 720 32 S 260 4, 0 30 Z" fill="#fff"/></svg>
</section>
<section class="block stmt-page">
    <div class="wrap stmt-wrap">
        <% if $State == 'form' %>
            <% with $Stmt %>
            <div class="stmt-card">
                <p class="stmt-hello">Poštovani/a <b>$HolderName</b>,</p>
                <p>prije potvrde ove izjave molimo vas da <b>pažljivo pročitate</b> Opšte uslove putovanja i Politiku privatnosti Hazre Toursa (linkovi su ispod) i provjerite tačnost svojih podataka.</p>
                <dl class="stmt-data">
                    <div><dt>Nosilac rezervacije</dt><dd>$HolderName</dd></div>
                    <% if not $NeedsTerm %>
                    <div><dt>Aranžman</dt><dd>$Trip</dd></div>
                    <div><dt>Termin</dt><dd>$Term</dd></div>
                    <% end_if %>
                    <% if $Phone %><div><dt>Telefon</dt><dd>$Phone</dd></div><% end_if %>
                </dl>
            </div>
            <% end_with %>

            <div class="stmt-important">
                <p class="stmt-important-h"><span aria-hidden="true">!</span>Važno: pročitajte prije potvrde</p>
                <p>Prije potvrde izjave molimo vas da pažljivo pročitate sljedeće dokumente:</p>
                <div class="stmt-docs">
                    <a class="stmt-doc" href="/uslovi-putovanja" target="_blank" rel="noopener noreferrer"><em>1</em><b>Opšti uslovi putovanja Hazre Tours</b><span>Otvara se u novoj kartici</span></a>
                    <a class="stmt-doc" href="/politika-privatnosti" target="_blank" rel="noopener noreferrer"><em>2</em><b>Politika privatnosti Hazre Tours</b><span>Otvara se u novoj kartici</span></a>
                </div>
                <p>Potvrdom ove izjave potvrđujete da ste imali mogućnost upoznati se s Opštim uslovima putovanja, da ih prihvatate i da ste upoznati s informacijama o obradi i zaštiti ličnih podataka. Izjava važi i za saputnike koje navedete.</p>
                <p><b>Molimo vas da pažljivo pročitate navedene dokumente i provjerite tačnost unesenih podataka.</b></p>
            </div>

            <form class="stmt-form" method="post" action="$Link" novalidate>
                <input type="hidden" name="SecurityID" value="$SecurityID">
                <% if $FormError %><p class="stmt-err" role="alert">$FormError</p><% end_if %>

                <% with $Stmt %><% if $NeedsTerm %>
                <fieldset class="stmt-fs">
                    <legend>Aranžman i termin</legend>
                    <p class="stmt-help">Izaberite putovanje i datum polaska na koji ste prijavljeni.</p>
                    <select name="term" required>
                        <option value="">Izaberite aranžman i termin</option>
                        <% loop $TermOptions %><option value="$ID">$Label</option><% end_loop %>
                    </select>
                </fieldset>
                <% end_if %><% end_with %>

                <fieldset class="stmt-fs">
                    <legend>Saputnici</legend>
                    <p class="stmt-help">Ako putujete s drugim osobama, upišite ime i prezime svake osobe u novi red (najviše $MaxCompanions). Ova izjava važi i za njih. Ne upisujte JMBG, broj pasoša ni druge podatke.</p>
                    <textarea name="companions" rows="4" maxlength="6000" placeholder="Npr.&#10;Amra Hodžić&#10;Emir Hodžić"><% with $Stmt %>$Companions<% end_with %></textarea>
                </fieldset>

                <fieldset class="stmt-fs">
                    <legend>Potvrde</legend>
                    <% loop $Checks %>
                    <label class="stmt-check"><input type="checkbox" name="check[$Key]" value="1" required><span>$Text</span></label>
                    <% end_loop %>
                </fieldset>

                <p class="stmt-help">Potvrdom izjave vaša saglasnost se elektronski evidentira u sistemu Hazre Toursa (datum i vrijeme potvrde, IP adresa i uređaj), u svrhu dokumentovanja da ste upoznati s Opštim uslovima putovanja i obradom ličnih podataka.</p>
                <button class="btn btn-sun btn-lg stmt-btn" type="submit">Potvrđujem izjavu</button>
            </form>
        <% else_if $State == 'done' %>
            <div class="stmt-card stmt-ok">
                <span class="stmt-ok-ico" aria-hidden="true">✓</span>
                <h2>Hvala<% with $Stmt %>, $FirstName<% end_with %>!</h2>
                <p>Vaša izjava je potvrđena i evidentirana. Ovu stranicu možete zatvoriti.</p>
                <p class="stmt-help">Sretan put i vidimo se na polasku!</p>
            </div>
        <% else_if $State == 'confirmed' %>
            <div class="stmt-card stmt-ok">
                <span class="stmt-ok-ico" aria-hidden="true">✓</span>
                <h2>Izjava je već potvrđena</h2>
                <p>Ova izjava je potvrđena $ConfirmedOn. Nije potrebno ništa više raditi.</p>
            </div>
        <% else %>
            <div class="stmt-card">
                <h2>Link nije ispravan ili je istekao</h2>
                <p>Provjerite da ste otvorili cijeli link iz poruke. Ako problem ostane, javite se agenciji na <a href="tel:$SiteConfig.MainPhone.Tel">$SiteConfig.MainPhone.Number</a>.</p>
            </div>
        <% end_if %>
    </div>
</section>
