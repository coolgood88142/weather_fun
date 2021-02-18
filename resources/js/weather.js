import forecast from "./components/weather/forecast.vue"
import weekForecast from "./components/weather/weekForecast.vue"
import weatherObservation from "./components/weather/weatherObservation.vue"
import rainObservation from "./components/weather/rainObservation.vue"
import weatherObservationReport from "./components/weather/weatherObservationReport.vue"
import acidRainPh from "./components/weather/acidRainPH.vue"
import ultraviolet from "./components/weather/ultraviolet.vue"
import ozoneYear from "./components/weather/ozoneYear.vue"
import seismi from "./components/weather/seismi.vue"
import smallSeiSmi from "./components/weather/smallSeiSmi.vue"
import alarm from "./components/weather/alarm.vue"
import sunrise from "./components/weather/sunrise.vue"
import moonrise from "./components/weather/moonrise.vue"

let app = new Vue({
	el: "#app",
    data: {
        showForecast: true,
        showWeekForecast: false,
        showWeatherObservation: false,
        showRainObservation: false,
        showWeatherObservationReport: false,
        showAcidRainPh: false,
        showUltraviolet: false,
        showOzoneYear: false,
        showSeismi: false,
        showSmallSeiSmi: false,
        showAlarm: false,
        showSunrise: false,
        showMoonrise: false,
        forecastUrl: '',
        url: 'http://127.0.0.1:8000/getWeather/',
        symbolId: '',
        weekForecastUrl: '',
        weatherObservationUrl: '',
        rainObservationUrl: '',
        weatherObservationReportUrl: '',
        acidRainPhUrl: '',
        ultravioletUrl: '',
        ozoneYearUrl: '',
        seismiUrl: '',
        smallSeiSmiUrl: '',
        alarmUrl: '',
        sunriseUrl: '',
        moonriseUrl: '',
        nowChart: 0,
        status: "success",
    },
    components: {
        'forecast': forecast,
        'week-forecast': weekForecast,
        'weather-observation': weatherObservation,
        'rain-observation': rainObservation,
        'weather-observation-report': weatherObservationReport,
        'acid-rain-ph': acidRainPh,
        'ultraviolet': ultraviolet,
        'ozone-year': ozoneYear,
        'seismi': seismi,
        'small-sei-smi': smallSeiSmi,
        'alarm': alarm,
        'sunrise': sunrise,
        'moonrise': moonrise,
    },
    mounted() {
        this.changeChart(this.nowChart);
    },
    methods:{
        changeChart(now){
            this.nowChart = now

            if(now == 0){
                this.showForecast = true
                this.showWeekForecast = false
                this.howWeatherObservation =  false
                this.showRainObservation =  false
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  false
                this.showUltraviolet =  false
                this.showOzoneYear =  false
                this.showSeismi =  false
                this.showSmallSeiSmi =  false
                this.showAlarm =  false
                this.showSunrise =  false
                this.showMoonrise =  false
                this.forecastUrl = this.url + this.nowChart;
            }else if(now == 1){
                this.showForecast = false
                this.showWeekForecast = true
                this.howWeatherObservation =  false
                this.showRainObservation =  false
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  false
                this.showUltraviolet =  false
                this.showOzoneYear =  false
                this.showSeismi =  false
                this.showSmallSeiSmi =  false
                this.showAlarm =  false
                this.showSunrise =  false
                this.showMoonrise =  false
                this.weekForecastUrl = this.url + this.nowChart;
            }else if(now == 2){
                this.showForecast = false
                this.showWeekForecast = false
                this.showWeatherObservation = true
                this.showRainObservation =  false
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  false
                this.showUltraviolet =  false
                this.showOzoneYear =  false
                this.showSeismi =  false
                this.showSmallSeiSmi =  false
                this.showAlarm =  false
                this.showSunrise =  false
                this.showMoonrise =  false
                this.weatherObservationUrl = this.url + this.nowChart;
            }else if(now == 3){
                this.showForecast = false
                this.showWeekForecast = false
                this.showWeatherObservation = false
                this.showRainObservation = true 
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  false
                this.showUltraviolet =  false
                this.showOzoneYear =  false
                this.showSeismi =  false
                this.showSmallSeiSmi =  false
                this.showAlarm =  false
                this.showSunrise =  false
                this.showMoonrise =  false
                this.rainObservationUrl = this.url + this.nowChart;
            }else if(now == 4){
                this.showForecast = false
                this.showWeekForecast = false
                this.showWeatherObservation = false
                this.showRainObservation = false 
                this.showWeatherObservationReport =  true
                this.showAcidRainPh =  false
                this.showUltraviolet =  false
                this.showOzoneYear =  false
                this.showSeismi =  false
                this.showSmallSeiSmi =  false
                this.showAlarm =  false
                this.showSunrise =  false
                this.showMoonrise =  false
                this.weatherObservationReportUrl = this.url + this.nowChart;
            }else if(now == 5){
                this.showForecast = false
                this.showWeekForecast = false
                this.showWeatherObservation = false
                this.showRainObservation = false 
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  true
                this.showUltraviolet =  false
                this.showOzoneYear =  false
                this.showSeismi =  false
                this.showSmallSeiSmi =  false
                this.showAlarm =  false
                this.showSunrise =  false
                this.showMoonrise =  false
                this.acidRainPhUrl = this.url + this.nowChart;
            }else if(now == 6){
                this.showForecast = false
                this.showWeekForecast = false
                this.showWeatherObservation = false
                this.showRainObservation = false 
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  false
                this.showUltraviolet =  true
                this.showOzoneYear =  false
                this.showSeismi =  false
                this.showSmallSeiSmi =  false
                this.showAlarm =  false
                this.showSunrise =  false
                this.showMoonrise =  false
                this.ultravioletUrl = this.url + this.nowChart;
            }else if(now == 7){
                this.showForecast = false
                this.showWeekForecast = false
                this.showWeatherObservation = false
                this.showRainObservation = false 
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  false
                this.showUltraviolet =  false
                this.showOzoneYear =  true
                this.showSeismi =  false
                this.showSmallSeiSmi =  false
                this.showAlarm =  false
                this.showSunrise =  false
                this.showMoonrise =  false
                this.ozoneYearUrl = this.url + this.nowChart;
            }else if(now == 8){
                this.showForecast = false
                this.showWeekForecast = false
                this.showWeatherObservation = false
                this.showRainObservation = false 
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  false
                this.showUltraviolet =  false
                this.showOzoneYear =  false
                this.showSeismi =  true
                this.showSmallSeiSmi =  false
                this.showAlarm =  false
                this.showSunrise =  false
                this.showMoonrise =  false
                this.SeismiUrl = this.url + this.nowChart;
            }else if(now == 9){
                this.showForecast = false
                this.showWeekForecast = false
                this.showWeatherObservation = false
                this.showRainObservation = false 
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  false
                this.showUltraviolet =  false
                this.showOzoneYear =  false
                this.showSeismi =  false
                this.showSmallSeiSmi =  true
                this.showAlarm =  false
                this.showSunrise =  false
                this.showMoonrise =  false
                this.smallSeiSmiUrl = this.url + this.nowChart;
            }else if(now == 10){
                this.showForecast = false
                this.showWeekForecast = false
                this.showWeatherObservation = false
                this.showRainObservation = false 
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  false
                this.showUltraviolet =  false
                this.showOzoneYear =  false
                this.showSeismi =  false
                this.showSmallSeiSmi =  false
                this.showAlarm =  true
                this.showSunrise =  false
                this.showMoonrise =  false
                this.alarmUrl = this.url + this.nowChart;
            }else if(now == 11){
                this.showForecast = false
                this.showWeekForecast = false
                this.showWeatherObservation = false
                this.showRainObservation = false 
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  false
                this.showUltraviolet =  false
                this.showOzoneYear =  false
                this.showSeismi =  false
                this.showSmallSeiSmi =  false
                this.showAlarm =  false
                this.showSunrise =  true
                this.showMoonrise =  false
                this.sunriseUrl = this.url + this.nowChart;
            }else if(now == 12){
                this.showForecast = false
                this.showWeekForecast = false
                this.showWeatherObservation = false
                this.showRainObservation = false 
                this.showWeatherObservationReport =  false
                this.showAcidRainPh =  false
                this.showUltraviolet =  false
                this.showOzoneYear =  false
                this.showSeismi =  false
                this.showSmallSeiSmi =  false
                this.showAlarm =  false
                this.showSunrise =  false
                this.showMoonrise =  true
                this.moonriseUrl = this.url + this.nowChart;
            }
        }
    }
})

let forecastArray = JSON.parse(document.getElementById("forecastArray").value);
let observationArray = JSON.parse(document.getElementById("observationArray").value);
let totalArray = JSON.parse(document.getElementById("totalArray").value);

Highcharts.chart('forecast', {
    chart: {
        zoomType: 'xy'
    },
    title: {
        text: '氣象API-天氣預報',
        align: 'left'
    },
    xAxis: [{
        categories: forecastArray.city,
        crosshair: true
    }],
    yAxis: [{ // Secondary yAxis
        gridLineWidth: 0,
        title: {
            text: '降雨機率',
            style: {
                color: '#AAAAAA'
            }
        },
        labels: {
            format: '{value} %',
            style: {
                color: '#AAAAAA'
            }
        },

    }, { // Primary yAxis
        title: {
            text: '最高溫度',
            style: {
                color: '#fd8307'
            }
        },
        labels: {
            format: '{value} °',
            style: {
                color: '#fd8307'
            }
        },
        opposite: true
    },
    { // Tertiary yAxis
        title: {
            text: '最低溫度',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },
        labels: {
            format: '{value} °',
            style: {
                color: Highcharts.getOptions().colors[0]
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
        name: '降雨機率',
        type: 'column',
        yAxis: 0,
        data: forecastArray.pop,
        dashStyle: 'shortdot',
        tooltip: {
            valueSuffix: ' %'
        },
        color: '#AAAAAA',

    }, {
        name: '最高溫度',
        type: 'spline',
        yAxis: 1,
        data: forecastArray.maxT,
        tooltip: {
            valueSuffix: ' °'
        },
        color: '#fd8307',

    },{
        name: '最低溫度',
        type: 'spline',
        yAxis: 2,
        data: forecastArray.minT,
        tooltip: {
            valueSuffix: ' °'
        },
        color: Highcharts.getOptions().colors[0]

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

Highcharts.chart('observation', {
    chart: {
        zoomType: 'xy'
    },
    title: {
        text: '氣象API-氣象觀測',
        align: 'left'
    },
    subtitle: {
        text: 'Source: ',
        align: 'left'
    },
    xAxis: [{
        categories: observationArray.city,
        crosshair: true
    }],
    yAxis: [{ // Secondary yAxis
        
        title: {
            text: '風速',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },
        labels: {
            format: '{value} ',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },
        opposite: true
    }, { // Primary yAxis
        title: {
            text: '風向',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        labels: {
            format: '{value} ',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        opposite: true
    },
    { // Tertiary yAxis
        gridLineWidth: 0,
        title: {
            text: '氣壓',
            style: {
                color: Highcharts.getOptions().colors[2]
            }
        },
        labels: {
            format: '{value} ',
            style: {
                color: Highcharts.getOptions().colors[2]
            }
        },
        
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
        name: '風速',
        type: 'column',
        yAxis: 0,
        data: observationArray.wdir,
        tooltip: {
            valueSuffix: ' '
        }

    },{
        name: '風向',
        type: 'column',
        yAxis: 1,
        data: observationArray.wdsd,
        tooltip: {
            valueSuffix: ' '
        }

    },{
        name: '氣壓',
        type: 'spline',
        yAxis: 2,
        data: observationArray.pres,
        marker: {
            enabled: false
        },
        dashStyle: 'shortdot',
        tooltip: {
            valueSuffix: ' '
        }

    },{
        name: '相對溼度',
        type: 'spline',
        yAxis: 2,
        data: observationArray.humd,
        tooltip: {
            valueSuffix: ' %'
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

Highcharts.chart('total', {
    chart: {
        zoomType: 'xy'
    },
    title: {
        text: '氣象API-統計資訊',
        align: 'left'
    },
    subtitle: {
        text: 'Source: ',
        align: 'left'
    },
    xAxis: [{
        categories: totalArray.city,
        crosshair: true
    }],
    yAxis: [{ // Secondary yAxis
        gridLineWidth: 0,
        title: {
            text: '酸雨ph值',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },
        labels: {
            format: '{value} ',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },

    }, { // Primary yAxis
        title: {
            text: '紫外線指數',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        labels: {
            format: '{value} ',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        opposite: true
    },
    { // Tertiary yAxis
        title: {
            text: '臭氧累年平均值',
            style: {
                color: Highcharts.getOptions().colors[2]
            }
        },
        labels: {
            format: '{value} ',
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
        name: '酸雨ph值',
        type: 'spline',
        yAxis: 0,
        data: totalArray.ph,
        dashStyle: 'shortdot',
        tooltip: {
            valueSuffix: ' '
        }

    }, {
        name: '紫外線指數',
        type: 'column',
        yAxis: 1,
        data: totalArray.uvi,
        tooltip: {
            valueSuffix: ' '
        }

    },{
        name: '臭氧累年平均值',
        type: 'column',
        yAxis: 2,
        data: totalArray.ozoneYear,
        tooltip: {
            valueSuffix: ' '
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
