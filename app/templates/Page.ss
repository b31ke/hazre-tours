<!doctype html>
<html lang="bs" class="no-js">
<head>
    <% base_tag %>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><% if $MetaTitle %>$MetaTitle<% else_if $URLSegment == 'home' %>$SiteConfig.Title – putnička agencija i prevoznik, Srebrenik<% else %>$Title | $SiteConfig.Title<% end_if %></title>
    <% if $MetaDescriptionText %><meta name="description" content="$MetaDescriptionText.ATT"><% else_if $MetaDescription %><meta name="description" content="$MetaDescription.ATT"><% else %><meta name="description" content="Putnička agencija i prevoznik iz Srebrenika od 1996. Putovanja, izleti i najam autobusa, s polascima iz gradova sjeveroistočne BiH."><% end_if %>
    <link rel="canonical" href="$CurrentURL">
    <%-- Naslov i opis kod dijeljenja linka (WhatsApp, Viber, Facebook): isti kao naslov u tabu preglednika --%>
    <meta property="og:title" content="<% if $MetaTitle %>$MetaTitle.ATT<% else_if $URLSegment == 'home' %>$SiteConfig.Title.ATT – putnička agencija i prevoznik, Srebrenik<% else %>$Title.ATT | $SiteConfig.Title.ATT<% end_if %>">
    <meta property="og:description" content="<% if $MetaDescriptionText %>$MetaDescriptionText.ATT<% else_if $MetaDescription %>$MetaDescription.ATT<% else %>Putnička agencija i prevoznik iz Srebrenika od 1996. Putovanja, izleti i najam autobusa, s polascima iz gradova sjeveroistočne BiH.<% end_if %>">
    <meta property="og:site_name" content="$SiteConfig.Title.ATT">
    <meta property="og:type" content="website">
    <meta property="og:url" content="$CurrentURL">
    <meta property="og:image" content="<% if $OgImage %>$OgImage.ATT<% else %>$DefaultOgImage<% end_if %>">
    <meta property="og:locale" content="bs_BA">
    <meta name="theme-color" content="#F4F8FB">
    <% if not $Indexable %><meta name="robots" content="noindex, nofollow"><% end_if %>
    <link rel="icon" href="favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="images/favicon-192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Poppins:wght@300;500;600;700&family=Open+Sans:wght@400;600;700&family=Kaushan+Script&display=swap">
    <script>document.documentElement.classList.replace('no-js', 'js');</script>
    $AgencyJsonLD
</head>
<body class="$BodyClass<% if $SiteConfig.IsFestive %> is-festive<% end_if %>"<% if $SiteConfig.IsFestive %> data-greet="$SiteConfig.FestiveGreetingText.ATT"<% end_if %> data-ga="$SiteConfig.GA4Id.ATT" data-pixel="$SiteConfig.MetaPixelId.ATT">
    <a class="skip" href="#sadrzaj">Preskoči na sadržaj</a>
    <% if $SiteConfig.PromoVisible %><% if $BodyClass != 'HomePageController' %><% include PromoBar %><% end_if %><% end_if %>
    <% include Header %>
    <main id="sadrzaj">
        $Layout
    </main>
    <% include Footer %>
    <% include FloatContact %>
    <% include Consent %>
    <script type="application/json" id="search-data">$SearchJSON.RAW</script>
</body>
</html>
