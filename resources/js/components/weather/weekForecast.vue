<template>
    <div>
        <figure class="highcharts-figure">
            <div id="weekForecastChart"></div>
        </figure>
        <data-table 
            :data="data"
            :columns="columns"
            @on-table-props-changed="reloadTable"
        >
        </data-table>
    </div>
</template>

<script>
import DataTable from 'laravel-vue-datatable';
Vue.use(DataTable);

export default {
    props: {
		weekForecastUrl: {
			type: String,
		},
    },
    data() {
        return {
            url: this.weekForecastUrl,
            data: {},
            tableProps: {
                search: '',
                length: 23,
                column: 'no',
                dir: 'asc'
            },
            columns: [
                {
                    label: '序號',
                    name: 'no',
                    orderable: true,
                },
                {
                    label: '縣市',
                    name: 'city',
                    orderable: true,
                },
                {
                    label: '降雨機率',
                    name: 'pop',
                    orderable: true,
                },
                {
                    label: '溫度',
                    name: 't',
                    orderable: true,
                },
                {
                    label: '相對溼度',
                    name: 'rh',
                    orderable: true,
                },
                {
                    label: '最高舒適度',
                    name: 'minci',
                    orderable: true,
                },
                {
                    label: '最大風速',
                    name: 'ws',
                    orderable: true,
                },
                {
                    label: '最高體感溫度',
                    name: 'maxat',
                    orderable: true,
                },
                {
                    label: '天氣現象',
                    name: 'wx',
                    orderable: true,
                },
                {
                    label: '最大舒適度指數',
                    name: 'maxci',
                    orderable: true,
                },
                {
                    label: '最低溫度',
                    name: 'mini',
                    orderable: true,
                },
                {
                    label: '紫外線指數',
                    name: 'uvi',
                    orderable: true,
                },
                {
                    label: '天氣預報綜合描述',
                    name: 'weatherdescription',
                    orderable: true,
                },
                {
                    label: '最低體感溫度',
                    name: 'minat',
                    orderable: true,
                },
                {
                    label: '最高溫度',
                    name: 'maxt',
                    orderable: true,
                },
                {
                    label: '風向',
                    name: 'wd',
                    orderable: true,
                },
                {
                    label: '露點溫度',
                    name: 'td',
                    orderable: true,
                },
            ]
        }
    },
    created() {
        if(this.url != ''){
            this.getData(this.url)
        }
    },
    methods:{
        getData(url = this.url, options = this.tableProps) {
            axios.get(url, {
                params: options
            })
            .then(response => {
                this.data = response.data;
                
                let city = [];
                let maxt = [];
                let mini = [];
                let rh = [];
                this.data.data.forEach((el, index) => {
                    city.push(el.city)
                    maxt.push(parseInt(el.maxt))
                    mini.push(parseInt(el.mini))
                    rh.push(parseInt(el.rh))
                })

                let forecastArray = {
                    city: city,
                    maxt: maxt,
                    mini: mini,
                    rh: rh,
                }
                
                this.getChart(forecastArray)
            })
            // eslint-disable-next-line
            .catch(errors => {
                //Handle Errors
            })
        },
        reloadTable(tableProps) {
            this.getData(this.url, tableProps);
        },
        getChart(data){
            Highcharts.chart('weekForecastChart', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: '未來天氣預報',
                    align: 'left'
                },
                xAxis: [{
                    categories: data.city,
                    crosshair: true
                }],
                yAxis: [{ // Secondary yAxis
                    title: {
                        text: '相對溼度',
                        style: {
                            color: '#0000FF'
                        }
                    },
                    labels: {
                        format: '{value} %',
                        style: {
                            color: '#0000FF'
                        }
                    },
                    opposite: true
                }, { // Primary yAxis
                    gridLineWidth: 0,
                    title: {
                        text: '溫度',
                        style: {
                            color: '#000000'
                        }
                    },
                    labels: {
                        format: '{value} °',
                        style: {
                            color: '#000000'
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
                    name: '最高溫度',
                    type: 'column',
                    yAxis: 1,
                    data: data.maxt,
                    tooltip: {
                        valueSuffix: ' °'
                    },
                    color: '#fd8307',

                },{
                    name: '最低溫度',
                    type: 'column',
                    yAxis: 1,
                    data: data.mini,
                    tooltip: {
                        valueSuffix: ' °'
                    },
                    color: Highcharts.getOptions().colors[0]

                },
                {
                    name: '相對溼度',
                    type: 'spline',
                    yAxis: 0,
                    data: data.rh,
                    dashStyle: 'shortdot',
                    tooltip: {
                        valueSuffix: ' %'
                    },
                    color: '#0000FF',

                }, ],
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

    function Meteogram(xml, container) {
        // Parallel arrays for the chart data, these are populated as the XML/JSON file
        // is loaded
        this.symbols = [];
        this.precipitations = [];
        this.precipitationsError = []; // Only for some data sets
        this.winds = [];
        this.temperatures = [];
        this.pressures = [];

        // Initialize
        this.xml = xml;
        this.container = container;

        // Run
        this.parseYrData();
    }

    /**
    * Function to smooth the temperature line. The original data provides only whole degrees,
    * which makes the line graph look jagged. So we apply a running mean on it, but preserve
    * the unaltered value in the tooltip.
    */
    Meteogram.prototype.smoothLine = function (data) {
        var i = data.length,
            sum,
            value;

        while (i--) {
            data[i].value = value = data[i].y; // preserve value for tooltip

            // Set the smoothed value to the average of the closest points, but don't allow
            // it to differ more than 0.5 degrees from the given value
            sum = (data[i - 1] || data[i]).y + value + (data[i + 1] || data[i]).y;
            data[i].y = Math.max(value - 0.5, Math.min(sum / 3, value + 0.5));
        }
    };

    /**
    * Draw the weather symbols on top of the temperature series. The symbols are
    * fetched from yr.no's MIT licensed weather symbol collection.
    * https://github.com/YR/weather-symbols
    */
    Meteogram.prototype.drawWeatherSymbols = function (chart) {
        var meteogram = this;

        chart.series[0].data.forEach((point, i) => {
            if (meteogram.resolution > 36e5 || i % 2 === 0) {

                chart.renderer
                    .image(
                        'https://cdn.jsdelivr.net/gh/YR/weather-symbols@6.0.2/dist/svg/' +
                            meteogram.symbols[i] + '.svg',
                        point.plotX + chart.plotLeft - 8,
                        point.plotY + chart.plotTop - 30,
                        30,
                        30
                    )
                    .attr({
                        zIndex: 5
                    })
                    .add();
            }
        });
    };


    /**
    * Draw blocks around wind arrows, below the plot area
    */
    Meteogram.prototype.drawBlocksForWindArrows = function (chart) {
        var xAxis = chart.xAxis[0],
            x,
            pos,
            max,
            isLong,
            isLast,
            i;

        for (pos = xAxis.min, max = xAxis.max, i = 0; pos <= max + 36e5; pos += 36e5, i += 1) {

            // Get the X position
            isLast = pos === max + 36e5;
            x = Math.round(xAxis.toPixels(pos)) + (isLast ? 0.5 : -0.5);

            // Draw the vertical dividers and ticks
            if (this.resolution > 36e5) {
                isLong = pos % this.resolution === 0;
            } else {
                isLong = i % 2 === 0;
            }
            chart.renderer.path(['M', x, chart.plotTop + chart.plotHeight + (isLong ? 0 : 28),
                'L', x, chart.plotTop + chart.plotHeight + 32, 'Z'])
                .attr({
                    stroke: chart.options.chart.plotBorderColor,
                    'stroke-width': 1
                })
                .add();
        }

        // Center items in block
        chart.get('windbarbs').markerGroup.attr({
            translateX: chart.get('windbarbs').markerGroup.translateX + 8
        });

    };

    /**
    * Get the title based on the XML data
    */
    Meteogram.prototype.getTitle = function () {
        return 'Meteogram for ' + this.xml.querySelector('location name').textContent +
            ', ' + this.xml.querySelector('location country').textContent;
    };

    /**
    * Build and return the Highcharts options structure
    */
    Meteogram.prototype.getChartOptions = function () {
        var meteogram = this;

        return {
            chart: {
                renderTo: this.container,
                marginBottom: 70,
                marginRight: 40,
                marginTop: 50,
                plotBorderWidth: 1,
                height: 310,
                alignTicks: false,
                scrollablePlotArea: {
                    minWidth: 720
                }
            },

            defs: {
                patterns: [{
                    id: 'precipitation-error',
                    path: {
                        d: [
                            'M', 3.3, 0, 'L', -6.7, 10,
                            'M', 6.7, 0, 'L', -3.3, 10,
                            'M', 10, 0, 'L', 0, 10,
                            'M', 13.3, 0, 'L', 3.3, 10,
                            'M', 16.7, 0, 'L', 6.7, 10
                        ].join(' '),
                        stroke: '#68CFE8',
                        strokeWidth: 1
                    }
                }]
            },

            title: {
                text: this.getTitle(),
                align: 'left',
                style: {
                    whiteSpace: 'nowrap',
                    textOverflow: 'ellipsis'
                }
            },

            credits: {
                text: 'Forecast from <a href="http://yr.no">yr.no</a>',
                href: this.xml.querySelector('credit link').getAttribute('url'),
                position: {
                    x: -40
                }
            },

            tooltip: {
                shared: true,
                useHTML: true,
                headerFormat:
                    '<small>{point.x:%A, %b %e, %H:%M} - {point.point.to:%H:%M}</small><br>' +
                    '<b>{point.point.symbolName}</b><br>'

            },

            xAxis: [{ // Bottom X axis
                type: 'datetime',
                tickInterval: 2 * 36e5, // two hours
                minorTickInterval: 36e5, // one hour
                tickLength: 0,
                gridLineWidth: 1,
                gridLineColor: 'rgba(128, 128, 128, 0.1)',
                startOnTick: false,
                endOnTick: false,
                minPadding: 0,
                maxPadding: 0,
                offset: 30,
                showLastLabel: true,
                labels: {
                    format: '{value:%H}'
                },
                crosshair: true
            }, { // Top X axis
                linkedTo: 0,
                type: 'datetime',
                tickInterval: 24 * 3600 * 1000,
                labels: {
                    format: '{value:<span style="font-size: 12px; font-weight: bold">%a</span> %b %e}',
                    align: 'left',
                    x: 3,
                    y: -5
                },
                opposite: true,
                tickLength: 20,
                gridLineWidth: 1
            }],

            yAxis: [{ // temperature axis
                title: {
                    text: null
                },
                labels: {
                    format: '{value}°',
                    style: {
                        fontSize: '10px'
                    },
                    x: -3
                },
                plotLines: [{ // zero plane
                    value: 0,
                    color: '#BBBBBB',
                    width: 1,
                    zIndex: 2
                }],
                maxPadding: 0.3,
                minRange: 8,
                tickInterval: 1,
                gridLineColor: 'rgba(128, 128, 128, 0.1)'

            }, { // precipitation axis
                title: {
                    text: null
                },
                labels: {
                    enabled: false
                },
                gridLineWidth: 0,
                tickLength: 0,
                minRange: 10,
                min: 0

            }, { // Air pressure
                allowDecimals: false,
                title: { // Title on top of axis
                    text: 'hPa',
                    offset: 0,
                    align: 'high',
                    rotation: 0,
                    style: {
                        fontSize: '10px',
                        color: Highcharts.getOptions().colors[2]
                    },
                    textAlign: 'left',
                    x: 3
                },
                labels: {
                    style: {
                        fontSize: '8px',
                        color: Highcharts.getOptions().colors[2]
                    },
                    y: 2,
                    x: 3
                },
                gridLineWidth: 0,
                opposite: true,
                showLastLabel: false
            }],

            legend: {
                enabled: false
            },

            plotOptions: {
                series: {
                    pointPlacement: 'between'
                }
            },


            series: [{
                name: 'Temperature',
                data: this.temperatures,
                type: 'spline',
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: true
                        }
                    }
                },
                tooltip: {
                    pointFormat: '<span style="color:{point.color}">\u25CF</span> ' +
                        '{series.name}: <b>{point.value}°C</b><br/>'
                },
                zIndex: 1,
                color: '#FF3333',
                negativeColor: '#48AFE8'
            }, {
                name: 'Precipitation',
                data: this.precipitationsError,
                type: 'column',
                color: 'url(#precipitation-error)',
                yAxis: 1,
                groupPadding: 0,
                pointPadding: 0,
                tooltip: {
                    valueSuffix: ' mm',
                    pointFormat: '<span style="color:{point.color}">\u25CF</span> ' +
                        '{series.name}: <b>{point.minvalue} mm - {point.maxvalue} mm</b><br/>'
                },
                grouping: false,
                dataLabels: {
                    enabled: meteogram.hasPrecipitationError,
                    formatter: function () {
                        if (this.point.maxvalue > 0) {
                            return this.point.maxvalue;
                        }
                    },
                    style: {
                        fontSize: '8px',
                        color: 'gray'
                    }
                }
            }, {
                name: 'Precipitation',
                data: this.precipitations,
                type: 'column',
                color: '#68CFE8',
                yAxis: 1,
                groupPadding: 0,
                pointPadding: 0,
                grouping: false,
                dataLabels: {
                    enabled: !meteogram.hasPrecipitationError,
                    formatter: function () {
                        if (this.y > 0) {
                            return this.y;
                        }
                    },
                    style: {
                        fontSize: '8px',
                        color: 'gray'
                    }
                },
                tooltip: {
                    valueSuffix: ' mm'
                }
            }, {
                name: 'Air pressure',
                color: Highcharts.getOptions().colors[2],
                data: this.pressures,
                marker: {
                    enabled: false
                },
                shadow: false,
                tooltip: {
                    valueSuffix: ' hPa'
                },
                dashStyle: 'shortdot',
                yAxis: 2
            }, {
                name: 'Wind',
                type: 'windbarb',
                id: 'windbarbs',
                color: Highcharts.getOptions().colors[1],
                lineWidth: 1.5,
                data: this.winds,
                vectorLength: 18,
                yOffset: -15,
                tooltip: {
                    valueSuffix: ' m/s'
                }
            }]
        };
    };

    /**
    * Post-process the chart from the callback function, the second argument to Highcharts.Chart.
    */
    Meteogram.prototype.onChartLoad = function (chart) {

        this.drawWeatherSymbols(chart);
        this.drawBlocksForWindArrows(chart);

    };

    /**
    * Create the chart. This function is called async when the data file is loaded and parsed.
    */
    Meteogram.prototype.createChart = function () {
        var meteogram = this;
        this.chart = new Highcharts.Chart(this.getChartOptions(), function (chart) {
            meteogram.onChartLoad(chart);
        });
    };

    Meteogram.prototype.error = function () {
        document.getElementById('loading').innerHTML = '<i class="fa fa-frown-o"></i> Failed loading data, please try again later';
    };

    /**
    * Handle the data. This part of the code is not Highcharts specific, but deals with yr.no's
    * specific data format
    */
    Meteogram.prototype.parseYrData = function () {

        var meteogram = this,
            xml = this.xml,
            pointStart,
            forecast = xml && xml.querySelector('forecast');

        if (!forecast) {
            return this.error();
        }

        // The returned xml variable is a JavaScript representation of the provided
        // XML, generated on the server by running PHP simple_load_xml and
        // converting it to JavaScript by json_encode.
        forecast.querySelectorAll('tabular time').forEach((time, i) => {
            // Get the times - only Safari can't parse ISO8601 so we need to do
            // some replacements
            var from = time.getAttribute('from') + ' UTC',
                to = time.getAttribute('to') + ' UTC';

            from = from.replace(/-/g, '/').replace('T', ' ');
            from = Date.parse(from);
            to = to.replace(/-/g, '/').replace('T', ' ');
            to = Date.parse(to);

            if (to > pointStart + 4 * 24 * 36e5) {
                return;
            }

            // If it is more than an hour between points, show all symbols
            if (i === 0) {
                meteogram.resolution = to - from;
            }

            // Populate the parallel arrays
            meteogram.symbols.push(
                time.querySelector('symbol').getAttribute('var')
                    .match(/[0-9]{2}[dnm]?/)[0]
            );

            meteogram.temperatures.push({
                x: from,
                y: parseInt(
                    time.querySelector('temperature').getAttribute('value'),
                    10
                ),
                // custom options used in the tooltip formatter
                to: to,
                symbolName: time.querySelector('symbol').getAttribute('name')
            });

            var precipitation = time.querySelector('precipitation');
            meteogram.precipitations.push({
                x: from,
                y: parseFloat(
                    Highcharts.pick(
                        precipitation.getAttribute('minvalue'),
                        precipitation.getAttribute('value')
                    )
                )
            });

            if (precipitation.getAttribute('maxvalue')) {
                meteogram.hasPrecipitationError = true;
                meteogram.precipitationsError.push({
                    x: from,
                    y: parseFloat(precipitation.getAttribute('maxvalue')),
                    minvalue: parseFloat(precipitation.getAttribute('minvalue')),
                    maxvalue: parseFloat(precipitation.getAttribute('maxvalue')),
                    value: parseFloat(precipitation.getAttribute('value'))
                });
            }

            if (i % 2 === 0) {
                meteogram.winds.push({
                    x: from,
                    value: parseFloat(time.querySelector('windSpeed')
                        .getAttribute('mps')),
                    direction: parseFloat(time.querySelector('windDirection')
                        .getAttribute('deg'))
                });
            }

            meteogram.pressures.push({
                x: from,
                y: parseFloat(time.querySelector('pressure').getAttribute('value'))
            });

            if (i === 0) {
                pointStart = (from + to) / 2;
            }
        }
        );

        // Smooth the line
        this.smoothLine(this.temperatures);

        // Create the chart when the data is loaded
        this.createChart();
    };
    // End of the Meteogram protype


    // On DOM ready...

    // Set the hash to the yr.no URL we want to parse
    var place,
        url;
    if (!location.hash) {
        place = 'United_Kingdom/England/London';
        //place = 'France/Rhône-Alpes/Val_d\'Isère~2971074';
        //place = 'Norway/Sogn_og_Fjordane/Vik/Målset';
        //place = 'United_States/California/San_Francisco';
        //place = 'United_States/Minnesota/Minneapolis';

        location.hash = 'https://www.yr.no/place/' + place + '/forecast_hour_by_hour.xml';
    }

    // Then get the XML file through Highcharts' CORS proxy. Our proxy is limited to
    // this specific location. Useing the third party, rate limited cors.io service
    // for experimenting with other locations.
    function getXML(url, cb, cbErr) {
        const request = new XMLHttpRequest();
        request.open('GET', url, true);

        request.onload = function () {
            if (this.status >= 400) {
                return cbErr();
            }
            return cb(new DOMParser().parseFromString(this.response, 'application/xml'));
        };

        request.send();
    }

    url = location.hash.substr(1);
    getXML(url === 'https://www.yr.no/place/United_Kingdom/England/London/forecast_hour_by_hour.xml' ?
        'https://demo-live-data.highcharts.com/weather-forecast.xml' :
        'https://cors-anywhere.herokuapp.com/' + url,
    xml => {
        window.meteogram = new Meteogram(xml, 'container');
    },
    Meteogram.prototype.error
    );


        }
    },
    watch:{
        weekForecastUrl(val){
            this.url = val
            this.getData(this.url);
        }
    } 
}
</script>