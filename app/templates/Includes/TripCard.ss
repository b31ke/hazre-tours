<article class="card<% if $SoldOut %> is-full<% end_if %>" data-type="$Type" data-months="$MonthsData" data-from="$DeparturesData" data-cats="$CategoriesData" data-search="$Title.LowerCase $Highlight.LowerCase $DestinationName.LowerCase" data-tilt data-reveal>
    <a class="card-photo<% if $PosterIsFlyer %> flyer<% end_if %>" href="$Link" tabindex="-1" aria-hidden="true">
        <% if $CardImageURL %><img src="$CardImageURL" alt="" loading="lazy" decoding="async"><% end_if %>
        <span class="card-date">$TabLabel</span>
        <% if $OnPromo %><span class="card-badge promo">−$DiscountPercent%</span><% else %><span class="card-badge<% if $Badge %> hot<% end_if %>">$BadgeLabel</span><% end_if %>
        <% if $SoldOut %><span class="stamp">Popunjena grupa</span><% else_if $FewSeatsLeft %><span class="seats">Još $FewSeatsLeft mjesta</span><% end_if %>
        <% if $DistanceKm %><span class="card-km mono">$DistanceKm km</span><% end_if %>
    </a>
    <div class="card-body">
        <h3><a href="$Link">$Title</a></h3>
        <div class="card-price">
            <% if $OnPromo %><s>$OldPrice KM</s><% end_if %>
            <strong><% if $HasPriceFrom %><small>od </small><% end_if %>$MinPrice <small>KM</small></strong>
            <span>po osobi</span>
        </div>
        <% if $OnPromo && $PromoNote %><p class="card-promo">$PromoNote</p><% end_if %>
        <% if $Highlight %><p class="card-hl">$Highlight</p><% end_if %>
        <div class="card-foot">
            <% if $Departures %><span class="card-from" title="Polasci: $Departures.ATT"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>$DeparturesShort</span><% end_if %>
            <a class="btn btn-line" href="$Link">Više informacija</a>
        </div>
    </div>
</article>
