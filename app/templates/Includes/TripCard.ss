<article class="card" data-type="$Type" data-months="$MonthsData" data-from="$DeparturesData" data-search="$Title.LowerCase $Highlight.LowerCase">
    <a class="photo<% if $PosterIsFlyer %> flyer<% end_if %>" href="$Link" tabindex="-1" aria-hidden="true">
        <% if $CardImageURL %><img src="$CardImageURL" alt="" loading="lazy"><% end_if %>
        <span class="tab">$TabLabel</span>
        <span class="badge<% if $Badge %> hot<% end_if %>">$BadgeLabel</span>
        <% if $NextTerm.IsFull %><span class="stamp">Popunjena grupa</span><% end_if %>
    </a>
    <div class="body">
        <h3><a href="$Link">$Title</a></h3>
        <div class="price"><% if $HasPriceFrom %><small>od </small><% end_if %>$MinPrice KM <small>po osobi</small></div>
        <% if $Highlight %><p class="hl">$Highlight</p><% end_if %>
        <% if $Departures %><p class="from">Polasci: $DeparturesShort</p><% end_if %>
        <a class="btn" href="$Link">Više informacija</a>
    </div>
</article>
