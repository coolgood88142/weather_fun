<template>
    <div>
        <figure class="highcharts-figure">
            <div id="forecastChart"></div>
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
		forecastUrl: {
			type: String,
		},
    },
    data() {
        return {
            url:  this.forecastUrl,
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
                    label: '天氣現象',
                    name: 'wx',
                    orderable: true,
                },
                {
                    label: '最高溫度',
                    name: 'maxt',
                    orderable: true,
                },
                {
                    label: '最低溫度',
                    name: 'mint',
                    orderable: true,
                },
                {
                    label: '舒適度',
                    name: 'ci',
                    orderable: true,
                },
                {
                    label: '降雨機率',
                    name: 'pop',
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
                let mint = [];
                let pop = [];
                this.data.data.forEach((el, index) => {
                    city.push(el.city)
                    maxt.push(parseInt(el.maxt))
                    mint.push(parseInt(el.mint))
                    pop.push(parseInt(el.pop))
                })

                let forecastArray = {
                    city: city,
                    maxt: maxt,
                    mint: mint,
                    pop: pop,
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
            Highcharts.chart('forecastChart', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: '天氣預報',
                    align: 'left'
                },
                xAxis: [{
                    categories: data.city,
                    crosshair: true
                }],
                yAxis: [{ // Secondary yAxis
                    title: {
                        text: '降雨機率',
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
                    dashStyle: 'shortdot',
                    tooltip: {
                        valueSuffix: ' °'
                    },
                    color: '#fd8307',

                },{
                    name: '最低溫度',
                    type: 'column',
                    yAxis: 1,
                    data: data.mint,
                    dashStyle: 'shortdot',
                    tooltip: {
                        valueSuffix: ' °'
                    },
                    color: Highcharts.getOptions().colors[0]

                },
                {
                    name: '降雨機率',
                    type: 'spline',
                    yAxis: 0,
                    data: data.pop,
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
        }
    },
    watch:{
        forecastUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }
}
</script>