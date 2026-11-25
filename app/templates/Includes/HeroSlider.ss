<section class="hs" aria-roledescription="carousel" aria-label="Izdvojena putovanja">
    <div class="hs-slides">
        <% loop $HeroSlides %>
        <article class="hs-slide<% if $IsFirst %> is-active<% end_if %>" data-i="$Pos(0)" aria-roledescription="slide" aria-label="$Pos od $TotalItems"<% if not $IsFirst %> aria-hidden="true"<% end_if %>>
            <div class="hs-bg<% if not $PhotoID %> is-poster<% end_if %>">
                <img src="<% if $PhotoID %>$HeroPhotoURL<% else %>$CardImageURL<% end_if %>"<% if $PhotoID %> srcset="$PhotoURL 1200w, $HeroPhotoURL 1920w" sizes="100vw"<% end_if %> alt="" decoding="async" <% if $IsFirst %>fetchpriority="high"<% else %>loading="lazy"<% end_if %>>
            </div>
            <p class="hs-dest" aria-hidden="true"><% if $DestinationName %>$DestinationName<% else %>$Title<% end_if %></p>
            <div class="wrap hs-in">
                <div class="hs-copy">
                    <% with $NextTerm %>
                    <p class="hs-kicker"><span class="hs-pulse" aria-hidden="true"></span>Polazak $Label <i>·</i> <b>$Countdown</b></p>
                    <% end_with %>
                    <h2 class="hs-title"><a href="$Link">$Title</a></h2>
                    <% if $Highlight %><p class="hs-lead">$Highlight</p><% else_if $Summary %><p class="hs-lead">$Summary.LimitCharacters(150)</p><% end_if %>
                    <div class="hs-cta">
                        <a class="btn btn-orange btn-lg" href="$Link">Pogledaj aranžman <span aria-hidden="true">→</span></a>
                        <p class="hs-price"><small><% if $HasPriceFrom %>Već od<% else %>Cijena<% end_if %></small><span class="hs-pv"><b>$MinPrice</b><sup>KM</sup></span></p>
                    </div>
                </div>
            </div>
        </article>
        <% end_loop %>
    </div>

    <div class="wrap hs-top">
        <h1 class="hs-brand">$HeroTitleHTML</h1>
        <p class="hs-count" aria-hidden="true"><b class="hs-cur">01</b><span>/ 0$HeroSlides.Count</span></p>
    </div>

    <div class="wrap hs-nav">
        <div class="hs-thumbs" role="tablist" aria-label="Izaberite putovanje">
            <% loop $HeroSlides %>
            <button type="button" class="hs-thumb<% if $IsFirst %> is-active<% end_if %>" data-go="$Pos(0)" role="tab" aria-selected="<% if $IsFirst %>true<% else %>false<% end_if %>" aria-label="<% if $DestinationName %>$DestinationName<% else %>$Title<% end_if %>">
                <span class="hs-ph"><% if $PhotoID %><img src="$Photo.Fill(168,120).Convert('webp').URL" alt="" loading="lazy"><% else_if $PosterID %><img src="$Poster.Fill(168,120).Convert('webp').URL" alt="" loading="lazy"><% end_if %></span>
                <span class="hs-tt"><small>0$Pos<% with $NextTerm %> · $StartDate.Format('dd.MM.')<% end_with %></small><b><% if $DestinationName %>$DestinationName<% else %>$Title<% end_if %></b></span>
                <span class="hs-bar"><i></i></span>
            </button>
            <% end_loop %>
        </div>
        <div class="hs-arrows">
            <button type="button" class="hs-arrow" data-dir="-1" aria-label="Prethodno putovanje"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button>
            <button type="button" class="hs-arrow" data-dir="1" aria-label="Sljedeće putovanje"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button>
        </div>
    </div>

    <svg class="hs-wave" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0 60 C 240 20, 480 20, 720 50 S 1200 90, 1440 40 L1440 90 L0 90 Z" fill="#fff"/>
        <path d="M0 58 C 240 18, 480 18, 720 48 S 1200 88, 1440 38" fill="none" stroke="#17AEDF" stroke-width="3" opacity=".7"/>
        <path d="M0 70 C 260 36, 500 40, 740 62 S 1180 92, 1440 56" fill="none" stroke="#F7A11A" stroke-width="3" opacity=".85"/>
    </svg>
</section>
