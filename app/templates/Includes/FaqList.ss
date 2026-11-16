<div class="faq" data-reveal>
    <% loop $Items %>
    <details class="faq-item"<% if $IsFirst %> open<% end_if %>>
        <summary><span>$Question</span><i aria-hidden="true"></i></summary>
        <div class="faq-a">$AnswerHTML</div>
    </details>
    <% end_loop %>
</div>
