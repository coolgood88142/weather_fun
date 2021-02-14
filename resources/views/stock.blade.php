<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="/css/stock.css">
        <link rel="stylesheet" href="/css/datepicker.css"/>
        {{-- <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css">
        <link rel="stylesheet" href="/css/bootstrap-datetimepicker.min.css" type="text/css" media="all" /> --}}
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
                            
                        <div class="form-group">
                            <label class="col-min-text col-md-3 col-lg-2 datetext control-label">起始時間:</label>
                            <div class="input-group date" id="time1">
                                <input type="text" name="end_time1" value="" class="form-control" placeholder="End time" title="" required="" id="id_end_time1">
                                <div class="input-group-addon input-group-append">
                                    <div class="input-group-text">
                                        <i class="glyphicon glyphicon-time fa fa-clock-o"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-min-text col-md-3 col-lg-2 datetext control-label">截止時間:</label>
                            <div class="input-group date" id="time2">
                                <input type="text" name="end_time2" value="" class="form-control" placeholder="End time" title="" required="" id="id_end_time2">
                                <div class="input-group-addon input-group-append">
                                    <div class="input-group-text">
                                        <i class="glyphicon glyphicon-time fa fa-clock-o"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="query" class="col-min-btn col-md-3 col-lg-2" style="text-align:right;">
                          <input type="submit" name="query_data" class="btn btn-primary" value="查詢" oncLick="checkTime()">
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
    {{-- <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script> --}}
    

    
    {{-- <script src="{{mix('js/bootstrap-datetimepicker.min.js')}}"></script> --}}
    <link crossorigin="anonymous" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
    rel="stylesheet">
{{-- <script crossorigin="anonymous" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" src="https://code.jquery.com/jquery-3.2.1.min.js"></script> --}}
<script crossorigin="anonymous" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css">
<link rel="stylesheet" href="./css/bootstrap-datetimepicker.min.css" type="text/css" media="all" />
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>

{{-- <script type="text/javascript" src="./js/demo.js"></script> --}}
<script src="{{mix('js/app.js')}}"></script>
<script src="{{mix('js/stock.js')}}"></script>
<script crossorigin="anonymous" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    {{-- <script type="text/javascript" src="./js/demo.js"></script> --}}
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

        function checkTime(){
            // axios.post("/checkFugleDataTime", {
            //     id: [
            //          'bTime':,$('#id_end_time1').val(),
            //          'eTime':,$('#id_end_time2').val(),
            //         ]
            // }).then((response) => {
            //     if (response.data.status === "success") {
            //         swal({
            //             title: response.data.message,
            //             confirmButtonColor: "#e6b930",
            //             icon: response.data.status,
            //             showCloseButton: true,
            //          })
            //     }
            // }).catch((error) => {
            //     if (error.response) {
            //         console.log(error.response.data)
            //         console.log(error.response.status)
            //         console.log(error.response.headers)
            //     } else {
            //          console.log("Error", error.message)
            //     }
            // })
        }
    </script>
</html>