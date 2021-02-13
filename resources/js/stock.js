$(document).ready(function () {
    var sideslider = $('[data-toggle=collapse-side]');
    var get_sidebar = sideslider.attr('data-target-sidebar');
    var get_content = sideslider.attr('data-target-content');
    sideslider.click(function(event){
        $(get_sidebar).toggleClass('in');
        $(get_content).toggleClass('out');
    });

    // $('#id_1').datetimepicker({
    //     "allowInputToggle": true,
    //     "showClose": true,
    //     "showClear": true,
    //     "showTodayButton": true,
    //     "format": "MM/DD/YYYY HH:mm:ss",
    // });

    // openDate($("input[name='begin_date']"));
    // openDate($("input[name='end_date']"));
});

// function openDate(name){
//   $(name).datepicker({
//     uiLibrary: 'bootstrap4',
//       format: "HH:MM",
//       language:"zh-TW",
//       weekStart: 1,
//       daysOfWeekHighlighted: "6,0",
//       autoclose: true,
//       todayHighlight: true,
//   });
// }

let chartArray = JSON.parse(document.getElementById("chartArray").value);
let dealtsArray = JSON.parse(document.getElementById("dealtsArray").value);

Highcharts.chart('chart', {
    chart: {
        zoomType: 'xy'
    },
    title: {
        text: '富果API-線圖',
        align: 'left'
    },
    subtitle: {
        text: 'Source: ' + chartArray.source,
        align: 'left'
    },
    xAxis: [{
        categories: chartArray.time,
        crosshair: true
    }],
    yAxis: [ { // Secondary yAxis
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
        },
        opposite: true

    },{ // Primary yAxis
        title: {
            text: '股數',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        labels: {
            format: '{value} 股',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        opposite: true
    },
    { // Tertiary yAxis
        gridLineWidth: 0,
        title: {
            text: '價格',
            style: {
                color: Highcharts.getOptions().colors[2]
            }
        },
        labels: {
            format: '{value} 元',
            style: {
                color: Highcharts.getOptions().colors[2]
            }
        }
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
        yAxis: 0,
        data: chartArray.unit,
        tooltip: {
            valueSuffix: ' 張'
        }

    }, {
        name: '股數',
        type: 'column',
        yAxis: 1,
        data: chartArray.volume,
        tooltip: {
            valueSuffix: ' 股'
        }

    },{
        name: '開盤價',
        type: 'spline',
        yAxis: 2,
        data: chartArray.open,
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
        yAxis: 2,
        data: chartArray.high,
        tooltip: {
            valueSuffix: ' 元'
        }
    },{
        name: '最低價',
        type: 'spline',
        yAxis: 2,
        data: chartArray.low,
        tooltip: {
            valueSuffix: ' 元'
        }
    },{
        name: '收盤價',
        type: 'spline',
        yAxis: 2,
        data: chartArray.close,
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

Highcharts.chart('dealts', {
    chart: {
        zoomType: 'xy'
    },
    title: {
        text: '富果API-當日成交資訊',
        align: 'left'
    },
    subtitle: {
        text: 'Source: ' + dealtsArray.source,
        align: 'left'
    },
    xAxis: [{
        categories: dealtsArray.at,
        crosshair: true
    }],
    yAxis: [ { // Secondary yAxis
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

    },{ // Tertiary yAxis
        gridLineWidth: 0,
        title: {
            text: '價格',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        labels: {
            format: '{value} 元',
            style: {
                color: Highcharts.getOptions().colors[1]
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
        yAxis: 0,
        data: dealtsArray.unit,
        tooltip: {
            valueSuffix: ' 張'
        }

    },{
        name: '價格',
        type: 'spline',
        yAxis: 1,
        data: dealtsArray.price,
        marker: {
            enabled: false
        },
        dashStyle: 'shortdot',
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