<!DOCTYPE html>
<html lang="zh-TW">
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>d3、vue 畫一個台灣地圖 - Augustus - Let's Write</title>
    <link rel="canonical" href="https://letswritetw.github.io/letswrite-taiwan-map-basic/"/>
    <meta property="fb:app_id" content="2435108729902508"/>
    <meta property="og:url" content="https://letswritetw.github.io/letswrite-taiwan-map-basic/"/>
    <meta property="og:type" content="website"/>
    <meta property="og:site_name" content="Let's Write"/>
    <meta property="og:title" content="d3、vue 畫一個台灣地圖 - Let's Write"/>
    <meta property="og:description" content="d3、vue 畫一個台灣地圖 - Let's Write"/>
    <meta property="og:image" content="https://letswritetw.github.io/letswrite-taiwan-map-basic/fb.png"/>
    <meta property="og:image:width" content="1200"/>
    <meta property="og:image:height" content="630"/>
    <link rel="shortcut icon" href="https://letswritetw.github.io/letswritetw/dist/img/logo_512.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css"/>
    <link rel="stylesheet" href="css/taiwan.min.css"/>
    <!-- Google Tag Manager-->
    <script>
      (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
      'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer','GTM-PGQ9WQT');
      
    </script>
  </head>
  <body>
    <!-- Google Tag Manager (noscript)-->
    <noscript>
      <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PGQ9WQT" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <div class="container" id="app">
      <div class="taiwan-map" ref="map">
        <div id="map">
          <svg id="svg" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet"></svg>
        </div>
      </div>
      <div class="shop-list">
        {{-- <h1>{{ h1 }}</h1>
        <h2>{{ h2 }}</h2> --}}
      </div>
    </div>
    <script src="//cdnjs.cloudflare.com/ajax/libs/babel-polyfill/7.6.0/polyfill.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.js"></script>
    <script src="//d3js.org/d3.v3.min.js"></script>
    <script src="{{mix('js/taiwan.min.js')}}"></script>
    <script src="{{mix('js/taiwan.js')}}"></script>
  </body>
</html>