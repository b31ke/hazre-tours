<div class="board" data-reveal>
    <div class="board-head">
        <span class="board-title"><span class="dot-live"></span>Polasci</span>
        <span class="board-clock mono" data-clock>--:--:--</span>
    </div>
    <div class="board-scroll">
        <table class="board-table">
            <thead><tr><th>Datum</th><th>Destinacija</th><th class="hide-sm">Trajanje</th><th class="hide-sm">Prevoz</th><th>Cijena</th><th>Status</th></tr></thead>
            <tbody>
            <% loop $Departures %>
                <tr>
                    <td class="mono"><span class="flap" data-flap="$StartDate.Format('dd.MM.')">$StartDate.Format('dd.MM.')</span> <small>$WeekdayShort</small></td>
                    <td><a href="$Trip.Link" class="board-dest"><span class="flap" data-flap="$Trip.Title.UpperCase">$Trip.Title.UpperCase</span></a></td>
                    <td class="mono hide-sm">$DaysLabel</td>
                    <td class="hide-sm">$Trip.TransportLabel</td>
                    <td class="mono board-price">$EffectivePrice KM</td>
                    <td><span class="st st-$StatusKey"><% if $IsFull %>POPUNJENO<% else_if $StatusKey == 'zadnja' %>$StatusLabel.UpperCase<% else %>$Countdown.UpperCase<% end_if %></span></td>
                </tr>
            <% end_loop %>
            </tbody>
        </table>
    </div>
</div>
