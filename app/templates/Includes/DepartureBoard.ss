<div class="dep">
    <table class="dep-table">
        <thead><tr><th>Datum</th><th>Putovanje</th><th class="hide-sm">Trajanje</th><th class="hide-sm">Prevoz</th><th>Cijena</th><th>Status</th></tr></thead>
        <tbody>
        <% loop $Departures %>
            <tr>
                <td class="nowrap"><b>$StartDate.Format('dd.MM.y.')</b> <small>$WeekdayShort</small></td>
                <td><a href="$Trip.Link">$Trip.Title</a></td>
                <td class="hide-sm nowrap">$DaysLabel</td>
                <td class="hide-sm">$Trip.TransportLabel</td>
                <td class="nowrap price-cell">$EffectivePrice KM</td>
                <td><span class="st st-$StatusKey">$StatusLabel</span></td>
            </tr>
        <% end_loop %>
        </tbody>
    </table>
</div>
