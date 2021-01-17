<!DOCTYPE html>
<html lang="zh-TW">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Access-Control-Allow-Origin" content="*" />
    <meta property="og:image:width" content="1200"/>
    <meta property="og:image:height" content="630"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css"/>
  </head>
  <body>
    <div class="container" id="app">
      <div class="taiwan-map" ref="map">
        <div id="map">
          <taiwan :taiwan-data="{{ json_encode($taiwanData) }}"  @save-city-data="saveCityData" />
        </div>
      </div>
      <div class="shop-list">
        <h1>@{{ cityCh }}</h1>
        <h2>@{{ cityEn }}</h2>
        <h2>溫度：@{{ temperature }}</h2>
        <h2>降雨機率：@{{ rain }}%</h2>
        <h2>未來1週氣候：@{{ climate }}</h2>
      </div>
    </div>     
  
    <link rel="stylesheet" href="dist/augurio-taiwan.min.css"/>
    <link rel="stylesheet" href="{{mix('css/app.css')}}"/>
    <script src="//d3js.org/d3.v3.min.js"></script>
    <script src="{{mix('js/app.js')}}"></script>
    <script src="{{mix('js/taiwan.js')}}"></script>
  </body>
</html>