<article class="card<% if $SoldOut %> is-full<% end_if %><% if $PosterIsFlyer %> is-flyer<% end_if %>" data-type="$Type" data-months="$MonthsData" data-from="$DeparturesData" data-cats="$CategoriesData" data-search="$Title.LowerCase $Highlight.LowerCase $DestinationName.LowerCase">
    <a class="card-photo" href="$Link" tabindex="-1" aria-hidden="true">
        <% if $CardImageURL %><img src="$CardImageURL" alt="" loading="lazy" decoding="async"><% end_if %>
        <%-- plakat već ima cijenu i datum na sebi, pa pečat ide samo na obične fotografije --%>
        <% if not $PosterIsFlyer %><span class="card-seal"><% if $HasPriceFrom %><small>od</small><% end_if %><b>$MinPrice</b><small>KM</small></span><% end_if %>
        <% if $SoldOut %><span class="stamp">Popunjena grupa</span><% end_if %>
        <span class="card-peek">Pogledaj program <span aria-hidden="true">→</span></span>
    </a>
    <div class="card-body">
        <div class="card-tags">
            <span class="card-date"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>$TabLabel</span>
            <% if $OnPromo %><span class="tag tag-red">−$DiscountPercent%</span><% else_if $Badge %><span class="tag tag-blue">$Badge</span><% end_if %>
            <% if not $SoldOut && $FewSeatsLeft %><span class="tag tag-warn">Još $FewSeatsLeft mjesta</span><% end_if %>
        </div>
        <h3><a href="$Link">$Title</a></h3>
        <% if $Highlight %><p class="card-hl">$Highlight</p><% end_if %>
        <div class="card-foot">
            <div class="card-price">
                <% if $OnPromo %><s>$OldPrice KM</s><% end_if %>
                <strong><% if $HasPriceFrom %><small>od </small><% end_if %>$MinPrice KM</strong>
                <span>po osobi</span>
            </div>
            <a class="btn btn-orange btn-sm" href="$Link">Više informacija</a>
        </div>
        <% if $OnPromo && $PromoNote %><p class="card-promo">$PromoNote</p><% end_if %>
    </div>
</article>
