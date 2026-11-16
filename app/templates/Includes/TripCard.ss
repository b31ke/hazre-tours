<article class="card<% if $SoldOut %> is-full<% end_if %>" data-type="$Type" data-months="$MonthsData" data-from="$DeparturesData" data-cats="$CategoriesData" data-search="$Title.LowerCase $Highlight.LowerCase $DestinationName.LowerCase">
    <span class="card-date">$TabLabel</span>
    <a class="card-photo<% if $PosterIsFlyer %> flyer<% end_if %>" href="$Link" tabindex="-1" aria-hidden="true">
        <% if $CardImageURL %><img src="$CardImageURL" alt="" loading="lazy" decoding="async"><% end_if %>
        <% if $OnPromo %><span class="card-badge promo">−$DiscountPercent%</span><% else_if $Badge %><span class="card-badge">$Badge</span><% end_if %>
        <% if $SoldOut %><span class="stamp">Popunjena grupa</span><% else_if $FewSeatsLeft %><span class="seats">Još $FewSeatsLeft mjesta</span><% end_if %>
    </a>
    <div class="card-body">
        <h3><a href="$Link">$Title</a></h3>
        <div class="card-price">
            <% if $OnPromo %><s>$OldPrice KM</s><% end_if %>
            <strong><% if $HasPriceFrom %><small>od </small><% end_if %>$MinPrice KM</strong>
        </div>
        <% if $OnPromo && $PromoNote %><p class="card-promo">$PromoNote</p><% end_if %>
        <% if $Highlight %><p class="card-hl">$Highlight</p><% end_if %>
        <a class="btn btn-orange" href="$Link"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7.5v.5"/></svg>Više informacija</a>
    </div>
</article>
