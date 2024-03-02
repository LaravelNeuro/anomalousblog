<!DOCTYPE html>
<html>
  <head>
    <!-- Google Tag Manager -->
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('consent', 'default', {
          'ad_storage': 'denied',
          'ad_user_data': 'denied',
          'ad_personalization': 'denied',
          'analytics_storage': 'denied'
        });

      </script>
      <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
      'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer','GTM-KHLV77ZV');</script>
    <!-- End Google Tag Manager -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    
    <link rel="canonical" href="https://anomalous.laravelneuro.org/" />
    <link rel="apple-touch-icon" href="images/fav/apple-touch-icon.png">
    <link rel="icon" sizes="512x512" href="images/fav/android-chrome-512x512.png">
    <link rel="icon" sizes="192x192" href="images/fav/android-chrome-192x192.png">
    <link rel="icon" sizes="32x32" href="images/fav/favicon-32x32.png">
    <link rel="icon" sizes="16x16" href="images/fav/favicon-16x16.png">
    <link rel="preload" href="">
    
    @vite('resources/js/app.js')
    @inertiaHead
  </head>
  <body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KHLV77ZV"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="body_filter">
      <div id="sparks"></div>
    @inertia
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const bodyFilterElement = document.getElementById('body_filter');
        const body = document.body;
        const bodyFont = body.style.fontFamily;
        
        function applyRandomFilter() {
          // Define the filters to apply
          const filters = [
            'saturate(40)',
            'contrast(80)',
            'contrast(20)',
            'saturate(20)',
          ];
          
          // Choose a random filter
          const randomFilter = filters[Math.floor(Math.random() * filters.length)];
          
          // Apply the random filter to the element
          bodyFilterElement.style.filter = randomFilter;
          body.style.fontFamily = "Xerox Malfunction";
          
          // remove the filter after a short time to make it look like a brief glitch
          setTimeout(() => {
            bodyFilterElement.style.filter = '';
            body.style.fontFamily = bodyFont;
          }, 100);
          setTimeout(() => {
            bodyFilterElement.style.filter = randomFilter;
            body.style.fontFamily = "Xerox Malfunction";
          }, 150);
          setTimeout(() => {
            bodyFilterElement.style.filter = '';
            body.style.fontFamily = bodyFont;
          }, 300);

          setTimeout(applyRandomFilter, Math.random() * 60000 + 5000);
        }

        setTimeout(applyRandomFilter, Math.random() * 60000 + 5000);

      });
      </script>

  </body>
</html>