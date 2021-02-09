<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
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

    </style>
    <body>
        <div class="container" style="width:900px;">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                  <li class="nav-item active">
                    <a class="nav-link" href="#" onclick="changeChart(1)">線圖</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">統計資訊</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">當日資訊</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#" onclick="changeChart(4)">當日成交資訊</a>
                  </li>
                </ul>
              </div>
            </nav>
            <form id="fugle_form" name="fugle_form" action="{{ route('fugle') }}" method="POST" class="sidebar-form">
                <h2 id="title" class="text-center text-black font-weight-bold" style="margin-bottom:20px;">股票圖表</h2>
                <input type="text" id="symbolId" name="symbolId" class="form-control" placeholder="請輸入股票代碼" style="margin-bottom: 20px;" value="">    
                    <div style="text-align:right">
                    <input type="submit" id="btn_insert" class="btn btn-primary" value="查詢" />
                    </div>
                <figure class="highcharts-figure">
                    <div id="chart"></div>
                    <div id="dealts"></div>
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
    <link rel="stylesheet" href="/css/stock.css">
</html>