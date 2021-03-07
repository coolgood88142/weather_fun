<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
        <link rel="stylesheet" href="/css/stock.css">
        <link rel="stylesheet" href="/css/datetimepicker.css">
    </head>
    <body>
        <div id="app" class="container" style="width:900px;">
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
            <ul class="nav nav-tabs" style="margin-top:70px;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(1)">線圖</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(2)">統計資訊</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(3)">當日資訊</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(4)">當日成交資訊</a>
                </li>
              </ul>
            <form id="fugle_form" name="fugle_form" action="{{ route('fugle') }}" method="POST" class="sidebar-form" style="margin-top:10px;">
                <h2 id="title" class="text-center text-black font-weight-bold" style="margin-bottom:20px;">股票圖表</h2>
                <div class="form-group row">
                    <label for="symbolId" class="col-sm-2 col-form-label">股票代號：</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="symbolId" name="symbolId" v-model="symbolId" value="">
                    </div>
                </div>
                <div class="row justify-content-center align-items-center">   
                    <label class="col-md-3 col-lg-2 col-xl-2">起始時間：</label>
                    <div class="input-group date col-md-3 col-lg-2 col-xl-3" id="time1">
                        <input type="text" class="form-control" id="begintime" name="begintime" v-model="begintime" value="" >
                        <div class="input-group-addon input-group-append">
                            <div class="input-group-text">
                                <i class="glyphicon glyphicon-time fa fa-clock-o"></i>
                            </div>
                        </div>
                    </div>

                    <label class="col-md-3 col-lg-2 col-xl-2">截止時間：</label>
                        <div class="input-group date col-md-3 col-lg-2 col-xl-3" id="time2">
                            <input type="text" class="form-control" id="endtime" name="endtime" v-model="endtime" value="">
                            <div class="input-group-addon input-group-append">
                                <div class="input-group-text">
                                    <i class="glyphicon glyphicon-time fa fa-clock-o"></i>
                                </div>
                            </div>
                        </div>
                        
                    <div id="query" class="col-min-btn col-md-3 col-lg-2" style="text-align:right;">
                        <input type="button" name="query_data" class="btn btn-primary" value="查詢" v-on:click="checkTime()">
                    </div>
                    <input type="hidden" id="nowChart" name="nowChart" v-model="nowChart" value="">
                </div>
                <figure class="highcharts-figure">
                    <div id="chart" v-show="showChart"></div>
                    <div id="dealts" v-show="showDealts"></div>
                </figure>
            </form>
            <chart v-show="showChart" :chart-url="chartUrl"></chart>
            <quote v-show="showQuote" :quote-url="quoteUrl"></quote>
            <metas v-show="showMetas" :metas-url="metasUrl"></metas>
            <dealts v-show="showDealts" :dealts-url="dealtsUrl"></dealts>
        </div>
    </body>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="{{mix('js/app.js')}}"></script>
    <script src="{{mix('js/stock.js')}}"></script>
    <script crossorigin="anonymous" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css">
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://monim67.github.io/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#time1').datetimepicker({
                "allowInputToggle": true,
                "showClose": true,
                "showClear": true,
                "showTodayButton": true,
                "format": "HH:mm",
            });

            $('#time2').datetimepicker({
                "allowInputToggle": true,
                "showClose": true,
                "showClear": true,
                "showTodayButton": true,
                "format": "HH:mm",
            });
        });
    </script>    
</html>