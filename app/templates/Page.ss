<!doctype html>
<html lang="bs">
<head>
    <% base_tag %>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><% if $MetaTitle %>$MetaTitle<% else_if $URLSegment == 'home' %>$SiteConfig.Title – putnička agencija Srebrenik<% else %>$Title | $SiteConfig.Title<% end_if %></title>
    <% if $MetaDescriptionText %><meta name="description" content="$MetaDescriptionText.ATT"><% else_if $MetaDescription %><meta name="description" content="$MetaDescription.ATT"><% end_if %>
    <meta property="og:title" content="<% if $MetaTitle %>$MetaTitle.ATT<% else %>$Title.ATT<% end_if %>">
    <meta property="og:site_name" content="$SiteConfig.Title.ATT">
    <meta property="og:type" content="website">
    <% if $OgImage %><meta property="og:image" content="$OgImage.ATT"><% end_if %>
    <meta name="theme-color" content="#0E5E8A">
    <% if not $Indexable %><meta name="robots" content="noindex, nofollow"><% end_if %>
    <link rel="icon" href="images/hazre-logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito+Sans:opsz,wght@6..12,400;6..12,600;6..12,700&display=swap">
</head>
<body class="$ClassName.ShortName">
    <% include Header %>
    <main id="pocetna">
        $Layout
    </main>
    <% include Footer %>
</body>
</html>
