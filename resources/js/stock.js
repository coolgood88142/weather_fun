import chart from "./components/stock/chart.vue"
import dealts from "./components/stock/dealts.vue"
import metas from "./components/stock/metas.vue"
import quote from "./components/stock/quote.vue"
import datetime from 'vuejs-datetimepicker';

let app = new Vue({
	el: "#app",
    data: {
        showChart: true,
        showQuote: false,
		showMetas: false,
        showDealts: false,
        showTime: false,
        url: './getFugle',
        chartUrl: '',
        quoteUrl: '',
        metasUrl: '',
        dealtsUrl: '',
        symbolId: '',
        begintime: '',
        endtime: '',
        chartMessage: '',
        dealtsMessage: '',
        nowChart: 1,
        status: "success",
    },
    components: {
        'chart': chart,
        'dealts': dealts,
        'metas': metas,
        'quote': quote,
        'datetime': datetime 
    },
    methods: {
        showMessage(isError, message){
            if(isError){
                this.status = "error";
            }

            swal({
                title: message,
                confirmButtonColor: "#e6b930",
                icon: this.status,
                showCloseButton: true,
            })
        },
        changeChart(now){
            const begintime = document.getElementById("begintime").value
            const endtime = document.getElementById("endtime").value
            this.begintime = begintime
            this.endtime = endtime
            this.nowChart = now

            if(now == 1){
                this.showChart = true
                this.showQuote = false
                this.showMetas = false
                this.showDealts = false
                this.showTime = true

                if(this.chartMessage != ''){
                    this.showMessage(true, this.chartMessage);
                    this.chartUrl = ''
                }else if(this.symbolId != ''){
                    this.chartUrl = this.url + '/chart/' + this.symbolId;
                    if(begintime != '' && endtime != ''){
                        this.chartUrl = this.chartUrl + '?begintime=' + begintime + '&endtime=' + endtime;
                    }else if(begintime != ''){
                        this.chartUrl = this.chartUrl + '?begintime=' + begintime;
                    }else if(endtime != ''){
                        this.chartUrl = this.chartUrl + '?endtime=' + endtime
                    }
                }

                
            }else if(now == 2){
                this.showChart = false
                this.showQuote = true
                this.showMetas = false
                this.showDealts = false
                this.showTime = false

                if(this.symbolId != ''){
                    this.quoteUrl = this.url + '/quote/' + this.symbolId;
                }
            }else if(now == 3){
                this.showChart = false
                this.showQuote = false
                this.showMetas = true
                this.showDealts = false
                this.showTime = false
                
                if(this.symbolId != ''){
                    this.metasUrl = this.url + '/meta/' + this.symbolId;
                }
            }else if(now == 4){
                this.showChart = false
                this.showQuote = false
                this.showMetas = false
                this.showDealts = true
                this.showTime = true
                
                if(this.dealtsMessage != ''){
                    this.showMessage(true, this.dealtsMessage);
                    this.dealtsUrl = ''
                }else if(this.symbolId != ''){
                    this.dealtsUrl = this.url + '/dealts/' + this.symbolId;
                    if(begintime != '' && endtime != ''){
                        this.dealtsUrl = this.dealtsUrl + '?begintime=' + begintime + '&endtime=' + endtime;
                    }else if(begintime != ''){
                        this.dealtsUrl = this.dealtsUrl + '?begintime=' + begintime;
                    }else if(endtime != ''){
                        this.dealtsUrl = this.dealtsUrl + '?endtime=' + endtime
                    }
                }
            }

        },
        checkTime(){
            let isSuccess = true;
            this.begintime = document.getElementById("begintime").value;
            this.endtime = document.getElementById("endtime").value;

            if(this.begintime  != "" && this.endtime  != ""){
                if(this.begintime  > this.endtime){
                    this.showMessage(true, "截止時間不能大於起始時間!");
                    isSuccess = false;
                }
            }

            if(isSuccess == true){
                const url = document.getElementById("fugle_form").action

                axios.post(url, {
                    symbolId: this.symbolId,
                    begintime: this.begintime,
                    endtime: this.endtime
                }).then((response) => {
                    if (response.data.symbolIdMessage == '') {
                        let chart = response.data.chart
                        let dealts = response.data.dealts
                        this.updateChart(chart, dealts)
                    }else{
                        this.showMessage(true, response.data.symbolIdMessage)
                    }

                    this.chartMessage = response.data.chartMessage
                    this.dealtsMessage = response.data.dealtsMessage
                    if(this.nowChart != ''){
                        this.changeChart(this.nowChart)
                    }
                    
                }).catch((error) => {
                    if (error.response) {
                        console.log(error.response.data)
                        console.log(error.response.status)
                        console.log(error.response.headers)
                    } else {
                        console.log("Error", error.message)
                    }
                })
            }
        },
        updateChart(chartArray, dealtsArray){
            Highcharts.chart('chart', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: '富果API-線圖',
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
        }
    },
})
