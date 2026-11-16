<article class="card<% if $SoldOut %> is-full<% end_if %><% if $PosterIsFlyer %> is-flyer<% end_if %>" <% if $HomeRank %>data-pick="$HomeRank" <% end_if %>data-type="$Type" data-months="$MonthsData" data-from="$DeparturesData" data-cats="$CategoriesData" data-search="$Title.LowerCase $Highlight.LowerCase $DestinationName.LowerCase">
    <%-- mekani sjaj iza kartice u bojama plakata (vidi se pri prelazu mišem) --%>
    <% if $CardImageURL %><img class="card-glow" src="$CardImageURL" alt="" aria-hidden="true" loading="lazy" decoding="async"><% end_if %>
    <a class="card-photo" href="$Link" tabindex="-1" aria-hidden="true">
        <% if $CardImageURL %><img src="$CardImageURL" alt="" loading="lazy" decoding="async"><% end_if %>
        <% if not $PosterIsFlyer %><span class="card-seal"><% if $HasPriceFrom %><small>od</small><% end_if %><b>$MinPrice</b><small>KM</small></span><% end_if %>
        <% if $SoldOut %><span class="stamp">Popunjena grupa</span><% end_if %>
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
                <strong><% if $HasPriceFrom %><small>od </small><% end_if %>$MinPrice <small>KM</small></strong>
                <span>po osobi</span>
            </div>
            <a class="card-go" href="$Link" aria-label="Više informacija: $Title.ATT"><span>Rezerviši</span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        </div>
        <% if $OnPromo && $PromoNote %><p class="card-promo">$PromoNote</p><% end_if %>
    </div>
</article>
