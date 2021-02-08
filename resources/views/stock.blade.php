<!DOCTYPE html>
<html lang="zh-TW">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
  </head>
  <script src="{{mix('js/app.js')}}"></script>
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/series-label.js"></script>
  <script src="https://code.highcharts.com/modules/exporting.js"></script>
  <script src="https://code.highcharts.com/modules/export-data.js"></script>
  <script src="https://code.highcharts.com/modules/accessibility.js"></script>
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
    <figure class="highcharts-figure">
      <div id="chart"></div>
    </figure>
    <input type="hidden" id="time" value="{{ json_encode($chart['time']) }}"/>
    <input type="hidden" id="open" value="{{ json_encode($chart['open']) }}"/>
    <input type="hidden" id="close" value="{{ json_encode($chart['close']) }}"/>
    <input type="hidden" id="high" value="{{ json_encode($chart['high']) }}"/>
    <input type="hidden" id="low" value="{{ json_encode($chart['low']) }}"/>
    <input type="hidden" id="unit" value="{{ json_encode($chart['unit']) }}"/>
    <input type="hidden" id="volume" value="{{ json_encode($chart['volume']) }}"/>
   
    <script>
      Highcharts.chart('chart', {
    chart: {
        zoomType: 'xy'
    },
    title: {
        text: '富果API-線圖',
        align: 'left'
    },
    subtitle: {
        text: 'Source: https://developer.fugle.tw/realtime/v0/intraday/chart',
        align: 'left'
    },
    xAxis: [{
        categories: JSON.parse(document.getElementById("time").value),
        crosshair: true
    }],
    yAxis: [{ // Primary yAxis
        labels: {
            format: '{value}卷',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        title: {
            text: '卷數',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        opposite: true

    }, { // Secondary yAxis
        gridLineWidth: 0,
        title: {
            text: '張數',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },
        labels: {
            format: '{value} 張',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        }

    }, { // Tertiary yAxis
        gridLineWidth: 0,
        title: {
            text: '價位',
            style: {
                color: Highcharts.getOptions().colors[2]
            }
        },
        labels: {
            format: '{value} 元',
            style: {
                color: Highcharts.getOptions().colors[2]
            }
        },
        opposite: true
    }],
    tooltip: {
        shared: true
    },
    legend: {
        layout: 'vertical',
        align: 'left',
        x: 80,
        verticalAlign: 'top',
        y: 55,
        floating: true,
        backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || // theme
            'rgba(255,255,255,0.25)'
    },
    series: [{
        name: '張數',
        type: 'column',
        yAxis: 1,
        data: JSON.parse(document.getElementById("unit").value),
        tooltip: {
            valueSuffix: ' 張'
        }

    }, {
        name: '卷數',
        type: 'column',
        yAxis: 1,
        data: JSON.parse(document.getElementById("volume").value),
        tooltip: {
            valueSuffix: ' 卷'
        }

    },{
        name: '開盤價',
        type: 'spline',
        yAxis: 2,
        data: JSON.parse(document.getElementById("open").value),
        marker: {
            enabled: false
        },
        dashStyle: 'shortdot',
        tooltip: {
            valueSuffix: ' 元'
        }

    }, {
        name: '最高價',
        type: 'spline',
        data: JSON.parse(document.getElementById("high").value),
        tooltip: {
            valueSuffix: ' 元'
        }
    },{
        name: '最低價',
        type: 'spline',
        data: JSON.parse(document.getElementById("low").value),
        tooltip: {
            valueSuffix: ' 元'
        }
    },{
        name: '收盤價',
        type: 'spline',
        data: JSON.parse(document.getElementById("close").value),
        tooltip: {
            valueSuffix: ' 元'
        }
    }],
    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    floating: false,
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    x: 0,
                    y: 0
                },
                yAxis: [{
                    labels: {
                        align: 'right',
                        x: 0,
                        y: -6
                    },
                    showLastLabel: false
                }, {
                    labels: {
                        align: 'left',
                        x: 0,
                        y: -6
                    },
                    showLastLabel: false
                }, {
                    visible: false
                }]
            }
        }]
    }
});

    </script>
  </body>
</html>