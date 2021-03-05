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
        this.getData(this.url);
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
                    gridLineWidth: 0,
                    title: {
                        text: '相對溼度',
                        style: {
                            color: '#CCEEFF'
                        }
                    },
                    labels: {
                        format: '{value} %',
                        style: {
                            color: '#CCEEFF'
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
                    name: '相對溼度',
                    type: 'column',
                    yAxis: 0,
                    data: data.rh,
                    dashStyle: 'shortdot',
                    tooltip: {
                        valueSuffix: ' %'
                    },
                    color: '#CCEEFF',

                }, {
                    name: '最高溫度',
                    type: 'spline',
                    yAxis: 1,
                    data: data.maxt,
                    tooltip: {
                        valueSuffix: ' °'
                    },
                    color: '#fd8307',

                },{
                    name: '最低溫度',
                    type: 'spline',
                    yAxis: 2,
                    data: data.mini,
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