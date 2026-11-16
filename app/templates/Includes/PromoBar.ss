<div class="promo" role="region" aria-label="Akcija">
    <div class="wrap">
        <span class="promo-tag">$SiteConfig.PromoTitle</span>
        <% if $SiteConfig.PromoText %><span class="promo-text">$SiteConfig.PromoText</span><% end_if %>
        <% if $SiteConfig.PromoUntil %>
        <span class="countdown" data-until="$SiteConfig.PromoUntilISO" aria-label="Akcija traje do $SiteConfig.PromoUntil.Format('dd.MM.y.')">
            <b data-cd="d">--</b><small>d</small><b data-cd="h">--</b><small>h</small><b data-cd="m">--</b><small>m</small><b data-cd="s">--</b><small>s</small>
        </span>
        <% end_if %>
        <a class="promo-link" href="<% if $SiteConfig.PromoLink %>$SiteConfig.PromoLink<% else %>{$BaseHref}#aranzmani<% end_if %>">Pogledaj ponudu →</a>
    </div>
</div>
