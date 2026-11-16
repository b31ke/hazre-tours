<!doctype html>
<html lang="bs" class="no-js">
<head>
    <% base_tag %>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><% if $MetaTitle %>$MetaTitle<% else_if $URLSegment == 'home' %>$SiteConfig.Title – putnička agencija i prevoznik, Srebrenik<% else %>$Title | $SiteConfig.Title<% end_if %></title>
    <% if $MetaDescriptionText %><meta name="description" content="$MetaDescriptionText.ATT"><% else_if $MetaDescription %><meta name="description" content="$MetaDescription.ATT"><% end_if %>
    <link rel="canonical" href="$CurrentURL">
    <meta property="og:title" content="<% if $MetaTitle %>$MetaTitle.ATT<% else %>$Title.ATT<% end_if %>">
    <% if $MetaDescriptionText %><meta property="og:description" content="$MetaDescriptionText.ATT"><% end_if %>
    <meta property="og:site_name" content="$SiteConfig.Title.ATT">
    <meta property="og:type" content="website">
    <meta property="og:url" content="$CurrentURL">
    <meta property="og:image" content="<% if $OgImage %>$OgImage.ATT<% else %>$DefaultOgImage<% end_if %>">
    <meta property="og:locale" content="bs_BA">
    <meta name="theme-color" content="#060E16">
    <% if not $Indexable %><meta name="robots" content="noindex, nofollow"><% end_if %>
    <link rel="icon" type="image/png" href="images/favicon-192.png">
    <link rel="apple-touch-icon" href="images/favicon-192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Unbounded:wght@500;600;700;800&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap">
    <script>document.documentElement.classList.replace('no-js', 'js');</script>
    $AgencyJsonLD
</head>
<body class="$BodyClass" data-ga="$SiteConfig.GA4Id.ATT" data-pixel="$SiteConfig.MetaPixelId.ATT">
    <a class="skip" href="#sadrzaj">Preskoči na sadržaj</a>
    <% if $SiteConfig.PromoVisible %><% include PromoBar %><% end_if %>
    <% include Header %>
    <main id="sadrzaj">
        $Layout
    </main>
    <% include Footer %>
    <% include FloatContact %>
    <% include SearchOverlay %>
    <% include Consent %>
    <script type="application/json" id="search-data">$SearchJSON.RAW</script>
</body>
</html>
