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
                    <a class="nav-link" href="javascript:void(0)" onclick="changeChart(1)">線圖</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">統計資訊</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">當日資訊</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="changeChart(4)">當日成交資訊</a>
                </li>
              </ul>
            <form id="fugle_form" name="fugle_form" action="{{ route('fugle') }}" method="POST" class="sidebar-form" style="margin-top:10px;">
                <h2 id="title" class="text-center text-black font-weight-bold" style="margin-bottom:20px;">股票圖表</h2>
                <div class="form-group row">
                    <label for="symbolId" class="col-sm-2 col-form-label">股票代號：</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="symbolId" name="symbolId" value="">
                    </div>
                </div>
                <div class="row justify-content-center align-items-center">   
                    <label class="col-md-3 col-lg-2 col-xl-2">起始時間:</label>
                    <div class="input-group date col-md-3 col-lg-2 col-xl-3" id="time1">
                        <input type="text" id="begintime" name="begintime" value="" class="form-control">
                        <div class="input-group-addon input-group-append">
                            <div class="input-group-text">
                                <i class="glyphicon glyphicon-time fa fa-clock-o"></i>
                            </div>
                        </div>
                    </div>

                    <label class="col-md-3 col-lg-2 col-xl-2">截止時間:</label>
                        <div class="input-group date col-md-3 col-lg-2 col-xl-3" id="time2">
                            <input type="text" id="endtime" name="endtime" value="" class="form-control">
                            <div class="input-group-addon input-group-append">
                                <div class="input-group-text">
                                    <i class="glyphicon glyphicon-time fa fa-clock-o"></i>
                                </div>
                            </div>
                        </div>
                        
                    <div id="query" class="col-min-btn col-md-3 col-lg-2" style="text-align:right;">
                        <input type="button" name="query_data" class="btn btn-primary" value="查詢" onclick="checkTime()">
                    </div>
                    <input type="hidden" id="nowChart" name="nowChart" value="{{ $nowChart }}">
                </div>
                <figure class="highcharts-figure">
                    <div id="chart"></div>
                    <div id="dealts" style="display: none;"></div>
                </figure>
            </form>
            <metas></metas>
            <dealts></dealts>
            <quote></quote>
        </div>
        
        <input type="hidden" id="symbolIdMessage" value="{{ $symbolIdMessage }}"/>
        <input type="hidden" id="chartMessage" value="{{ $chartMessage }}"/>
        <input type="hidden" id="dealtsMessage" value="{{ $dealtsMessage }}"/>
        <input type="hidden" id="symbol_Id" value="{{ $symbolId }}"/>
        <input type="hidden" id="bTime" value="{{ $bTime }}"/>
        <input type="hidden" id="eTime" value="{{ $eTime }}"/>
        <input type="hidden" id="chartArray" value="{{ json_encode($chart) }}"/>
        <input type="hidden" id="dealtsArray" value="{{ json_encode($dealts) }}"/>
        <input type="hidden" id="datatable" value="{{ json_encode($datatable) }}"/>

        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/series-label.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="{{mix('js/app.js')}}"></script>
        <script src="{{mix('js/stock.js')}}"></script>
        <script crossorigin="anonymous" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css">
        {{-- <link rel="stylesheet" href="https://monim67.github.io/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" type="text/css" media="all" /> --}}
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
        <script type="text/javascript" src="https://monim67.github.io/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
        <script>
            $(document).ready(function() {
                let symbolIdMessage = $("#symbolIdMessage").val();
                let symbol_Id = $("#symbol_Id").val();
                let bTime = $("#bTime").val();
                let eTime = $("#eTime").val();
                let chartMessage = $("#chartMessage").val();
                let dealtsMessage = $("#dealtsMessage").val();
                let nowChart = $("#nowChart").val();

                if(symbolIdMessage != ''){
                    showMessage(true, symbolIdMessage)
                }

                if(symbol_Id == ''){
                    $("#symbolId").val('');
                }else{
                    $("#symbolId").val(symbol_Id);
                }

                if(bTime == ''){
                    $("#begintime").val('');
                }else{
                    $("#begintime").val(bTime);
                }


                if(eTime == ''){
                    $("#endtime").val('');
                }else{
                    $("#endtime").val(eTime);
                }

                if(nowChart != ''){
                    changeChart(nowChart);
                }

                if(nowChart == '1' && chartMessage != ''){
                    showMessage(true, chartMessage)
                }else if(nowChart == '4' && dealtsMessage != ''){
                    showMessage(true, dealtsMessage)
                }

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

            function changeChart(now){
                if(now == 1){
                    $("#dealts").hide();
                    $("#chart").show();

                    if($("#chartMessage").val() != ''){
                        showMessage(true, $("#chartMessage").val());
                    }
                }else if(now == 4){
                    $("#chart").hide();
                    $("#dealts").show();

                    if($("#dealtsMessage").val() != ''){
                        showMessage(true, $("#dealtsMessage").val());
                    }
                }

                $("#nowChart").val(now);
            }

            function checkTime(){
                let isSuccess = true;
                let begintime = $("#begintime").val();
                let endtime = $("#endtime").val();

                if(begintime != "" && endtime != ""){
                    if(begintime > endtime){
                        showMessage(true, "截止時間不能大於起始時間!");
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