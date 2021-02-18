<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
                    <a class="nav-link" href="https://fierce-headland-21046.herokuapp.com/weather/">台灣地圖</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="/weatherChart">氣象圖表</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="/fugle">股票圖表</a>
                  </li>
                </ul>
              </div>
            </nav>
            <ul class="nav nav-tabs" style="margin-top:70px;">
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(0)">天氣預報</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(1)">未來天氣預報</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(2)">氣象觀測</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(3)">雨量觀測</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(4)">天氣觀測報告</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(5)">酸雨 pH 值</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(6)">紫外線指數</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(7)">臭氧量觀測</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(8)">有感地震報告</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(9)">小區域有感地震報告</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(10)">天氣警報</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(11)">日出日沒時刻</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" v-on:click="changeChart(12)">月出日沒時刻</a>
                </li>
              </ul>
            <form id="fugle_form" name="fugle_form" action="{{ route('fugle') }}" method="POST" class="sidebar-form" style="margin-top:10px;">
                <h2 id="title" class="text-center text-black font-weight-bold" style="margin-bottom:20px;">氣象圖表</h2>
                <figure class="highcharts-figure">
                    <div id="forecast"></div>
                    <div id="observation" style="display: none;"></div>
                    <div id="total" style="display: none;"></div>
                </figure>
            </form>
            <forecast v-show="showForecast" :forecast-url="forecastUrl"></forecast>
            <week-forecast v-show="showWeekForecast" :week-forecast-url="weekForecastUrl"></week-forecast>
            <weather-observation v-show="showWeatherObservation" :weather-observation-url="weatherObservationUrl"></weather-observation>
            <rain-observation v-show="showRainObservation" :rain-observation-url="rainObservationUrl"></rain-observation>
            <weather-observation-report v-show="showWeatherObservationReport" :weather-observation-report-url="weatherObservationReportUrl"></weather-observation-report>
            <acid-rain-ph v-show="showAcidRainPh" :acid-rain-ph-url="acidRainPhUrl"></acid-rain-ph>
            <ultraviolet v-show="showUltraviolet" :ultraviolet-url="ultravioletUrl"></ultraviolet>
            <ozone-year v-show="showOzoneYear" :ozone-year-url="ozoneYearUrl"></ozone-year>
            <seismi v-show="showSeismi" :seismi-url="seismiUrl"></seismi>
            <small-sei-smi v-show="showSmallSeiSmi" :small-sei-smi-url="smallSeiSmiUrl"></small-sei-smi>
            <alarm v-show="showAlarm" :alarm-url="alarmUrl"></alarm>
            <sunrise v-show="showSunrise" :sunrise-url="sunriseUrl"></sunrise>
            <moonrise v-show="showMoonrise" :moonrise-url="moonriseUrl"></moonrise>
        </div>
        
        <input type="hidden" id="forecastArray" value="{{ json_encode($forecast) }}"/>
        <input type="hidden" id="observationArray" value="{{ json_encode($observation) }}"/>
        <input type="hidden" id="totalArray" value="{{ json_encode($total) }}"/>

        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/series-label.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="{{mix('js/app.js')}}"></script>
        <script src="{{mix('js/weather.js')}}"></script>
        <script crossorigin="anonymous" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css">
        {{-- <link rel="stylesheet" href="https://monim67.github.io/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" type="text/css" media="all" /> --}}
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

            function changeChart(num){
                if(num == 1){
                    $("#forecast").show();
                    $("#observation").hide();
                    $("#total").hide();
                }else if(num == 2){
                    $("#forecast").hide();
                    $("#observation").show();
                    $("#total").hide();
                }else if(num == 3){
                    $("#forecast").hide();
                    $("#observation").hide();
                    $("#total").show();
                }
            }

            function checkTime(){
                let isSuccess = true;
                let begintime = $("#begintime").val();
                let endtime = $("#endtime").val();

                if(begintime != "" && endtime != ""){
                    if(begintime > endtime){
                        showMessage(true, "截止日期不能大於起始日期!");
                        isSuccess = false;
                    }
                }

                if(isSuccess == true){
                    $("#fugle_form").submit();
                }
            }

            function showMessage(isError, message){
                let status = "success";
                if(isError){
                    status = "error";
                }

                swal({
                    title: message,
                    confirmButtonColor: "#e6b930",
                    icon: status,
                    showCloseButton: true,
                })
            }
        </script>
    </body>
</html>