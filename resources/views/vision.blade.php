<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="/css/stock.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
        <!-- SortableJS -->
  <script src="https://unpkg.com/sortablejs@1.4.2"></script>
  <!-- VueSortable -->
  <script src="https://unpkg.com/vue-sortable@0.1.3"></script>
      </head>
    <body>
        <div id="app" class="container">
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
            <form id="fugle_form" name="fugle_form" action="" method="POST" class="sidebar-form" style="margin-top:70px;">    
                <h2 id="title" class="text-center text-black font-weight-bold" style="margin-bottom:20px;">圖片解析</h2>
                <upload :upload-url="saveVisionUrl"></upload>
                <keyword :key-word-url="url"></keyword>
            </form>
        </div>
    </body>
    <script src="{{mix('js/app.js')}}"></script>
    <script src="{{mix('js/vision.js')}}"></script>
</html>