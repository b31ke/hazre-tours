<section class="page-head">
    <div class="wrap">
        <% if $Parent %><p class="eyebrow sun"><a href="$Parent.Link">$Parent.MenuTitle</a></p><% end_if %>
        <h1>$Title</h1>
    </div>
</section>
<section class="block">
    <div class="wrap">
        <article class="typography">$Content</article>
        $Form
    </div>
</section>
<% include InquiryForm %>
