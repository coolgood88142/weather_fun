<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="/css/stock.css">
        <link rel="stylesheet" href="/css/datepicker.css"/>
        <link rel="stylesheet" href="/css/datepicker.css"/>
    </head>
    <style>
        #container {
            height: 400px; 
        }

        .highcharts-figure, .highcharts-data-table table {
            min-width: 310px; 
            max-width: 800px;
            margin: 1em auto;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #EBEBEB;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }
        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }
        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }
        .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
            padding: 0.5em;
        }
        .highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }
        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }
        
        .navbar-light .navbar-nav .nav-link {
            color: #fff;
        }
    </style>
    <body>
        <div class="container" style="width:900px;">
            <nav class="navbar navbar-expand-md navbar-light navbar-dark bg-dark fixed-top navbar-inverse">
                <button  class="navbar-toggler pull-left" type="button" data-toggle="collapse-side" data-target-sidebar=".side-collapse-left" data-target-content=".side-collapse-container-left" >
                    <span class="navbar-toggler-icon"></span>
                </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                  <li class="nav-item ">
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
              </div>
            </nav>
            <form id="fugle_form" name="fugle_form" action="{{ route('fugle') }}" method="POST" class="sidebar-form" style="margin-top:70px;">
               
                    <div class="row justify-content-center align-items-center">
                        <h2 id="title" class="text-center text-black font-weight-bold" style="margin-bottom:20px;">股票圖表</h2>
                        <input type="text" id="symbolId" name="symbolId" class="form-control" placeholder="請輸入股票代碼" style="margin-bottom: 10px;" value="{{ $symbolId }}">    
                            
                        <label>24hr Date-Time:</label>
                        <div class="input-group date" id="id_1">
                            <input type="text" value="05/16/2018 11:31:00" class="form-control" required/>
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            </span>
                        </div>

                        <label class="col-min-text col-md-3 col-lg-2 datetext control-label">起始時間:</label>
                        <div class=" col-md-3 col-lg-2 col-xl-2">
                          <input type="text" class="form-control" name="begin_date" data-provide="datepicker">
                        </div>
                        <label class="col-min-text col-md-3 col-lg-2 datetext control-label">截止時間:</label>
                        <div class="col-md-3 col-lg-2 col-xl-2">
                          <input type="text" class="form-control" name="end_date" data-provide="datepicker">
                        </div>
                        <div id="query" class="col-min-btn col-md-3 col-lg-2" style="text-align:right;">
                          <input type="submit" name="query_data" class="btn btn-primary" value="查詢">
                        </div>
                    </div>
                <figure class="highcharts-figure">
                    <div id="chart"></div>
                    <div id="dealts" style="display: none;"></div>
                </figure>
            </form>
        </div>
        
        <input type="hidden" id="chartArray" value="{{ json_encode($chart) }}"/>
        <input type="hidden" id="dealtsArray" value="{{ json_encode($dealts) }}"/>
    </body>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="{{mix('js/app.js')}}"></script>
    <script src="{{mix('js/stock.js')}}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/bootstrap.datepicker-fork/1.3.0/js/bootstrap-datepicker.js"></script> --}}
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css">
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
        function changeChart(num){
            if(num == 1){
                $("#dealts").hide();
                $("#chart").show();
            }else if(num == 4){
                $("#chart").hide();
                $("#dealts").show();

            }
        }
    </script>
</html>