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
      <div id="container"></div>
    </figure>
   
    <script>
      Highcharts.chart('container', {
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
        categories: ['12:32', '12:33', '12:41', '12:47', '12:49', '12:50',
            '12:54', '13:01', '13:03', '13:04', '13:11', '13:12',
            '12:54', '13:01', '13:03', '13:04', '13:11', '13:12','12:54','13:12'],
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
        data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 
              135.6, 148.5, 216.4, 194.1, 95.6, 54.4,
               49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 95.6, 54.4,],
        tooltip: {
            valueSuffix: ' 張'
        }

    }, {
        name: '卷數',
        type: 'column',
        yAxis: 1,
        data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 
              135.6, 148.5, 216.4, 194.1, 95.6, 54.4,
               49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 95.6, 54.4,],
        tooltip: {
            valueSuffix: ' 卷'
        }

    },{
        name: '開盤價',
        type: 'spline',
        yAxis: 2,
        data: [1016, 1016, 1015.9, 1015.5, 1012.3, 1009.5, 
               1009.6, 1010.2, 1013.1, 1016.9, 1018.2, 1016.7,
               1016, 1016, 1015.9, 1015.5, 1012.3, 1009.5, 1013.1, 1016.9,],
        marker: {
            enabled: false
        },
        dashStyle: 'shortdot',
        tooltip: {
            valueSuffix: ' mb'
        }

    }, {
        name: '最高價',
        type: 'spline',
        data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5,
               23.3, 18.3, 13.9, 9.6, 7.0, 6.9, 9.5, 14.5, 
               18.2, 21.5, 25.2, 26.5,],
        tooltip: {
            valueSuffix: ' °C'
        }
    },{
        name: '最低價',
        type: 'spline',
        data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5,
               23.3, 18.3, 13.9, 9.6, 7.0, 6.9, 9.5, 14.5, 
               18.2, 21.5, 25.2, 26.5,],
        tooltip: {
            valueSuffix: ' °C'
        }
    },{
        name: '收盤價',
        type: 'spline',
        data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5,
               23.3, 18.3, 13.9, 9.6, 7.0, 6.9, 9.5, 14.5, 
               18.2, 21.5, 25.2, 26.5,],
        tooltip: {
            valueSuffix: ' °C'
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