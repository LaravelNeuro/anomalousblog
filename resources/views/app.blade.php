<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite('resources/js/app.js')
    @inertiaHead
  </head>
  <body>
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