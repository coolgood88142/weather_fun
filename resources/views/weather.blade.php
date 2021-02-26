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
      <nav class="navbar navbar-expand-md navbar-light navbar-dark bg-dark fixed-top navbar-inverse">
        <button  class="navbar-toggler pull-left" type="button" data-toggle="collapse-side" data-target-sidebar=".side-collapse-left" data-target-content=".side-collapse-container-left" >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item ">
              <a class="nav-link" href="{{ $token }}">首頁</a>
            </li>
            <li class="nav-item ">
              <a class="nav-link" href="/weather">台灣地圖</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/weatherChart">氣象圖表</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/fugle">股票圖表</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/vision">圖片解析</a>
            </li>
          </ul>
        </div>
      </nav>
      <div class="taiwan-map" ref="map" style="margin-top: 80px;">
        <div id="map">
          <taiwan :taiwan-data="{{ json_encode($taiwanData) }}"  @save-city-data="saveCityData" />
        </div>
      </div>
      <div class="shop-list">
        <h1>@{{ cityCh }}</h1><br/>
        {{-- <h2>@{{ cityEn }}</h2><br/> --}}
        <div class="row">
          <div class="col">
            <h3>溫度：@{{ temperature }}</h3>
          </div>
          <div class="col">
            <h3>降雨機率：@{{ rain }}%</h3>
          </div>
          <div class="w-100"></div>
          <div class="col">
            <h3>風向：@{{ wdir }}</h3>
          </div>
          <div class="col">
            <h3>風速：@{{ wdsd }}</h3>
          </div>
          <div class="w-100"></div>
          <div class="col">
            <h3>氣壓：@{{ pres }}</h3>
          </div>
          <div class="col">
            <h3>相對溼度：@{{ humd }}</h3>
          </div>
          <div class="w-100"></div>
          <div class="col">
            <h3>紫外線指數：@{{ uv }}</h3>
          </div>
          <div class="col">
            <h3>臭氧累年平均值：@{{ ozone }}</h3>
          </div>
          <div class="w-100"></div>
          <div class="col">
            <h3>地震報告：@{{ earthquake }}</h3>
          </div>
          <div class="w-100"></div>
          <div class="col">
          <h3>未來1週氣候：@{{ climate }}</h3>
          </div>
        </div>
        {{-- <h3>當月最高酸雨 pH 值：@{{ rainPH }}</h3> --}}
        
      </div>
    </div>     
  
    <link rel="stylesheet" href="dist/augurio-taiwan.min.css"/>
    <link rel="stylesheet" href="{{mix('css/app.css')}}"/>
    <script src="{{mix('js/d3.v3.min.js')}}"></script>
    <script src="{{mix('js/app.js')}}"></script>
    <script src="{{mix('js/taiwan.js')}}"></script>
  </body>
</html>