<template>
    <div>
        <figure class="highcharts-figure">
            <div id="weatherObservationChart"></div>
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
		weatherObservationUrl: {
			type: String,
		},
    },
    data() {
        return {
            url: this.weatherObservationUrl,
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
                    label: '高度',
                    name: 'elev',
                    orderable: true,
                },
                {
                    label: '風向',
                    name: 'wdir',
                    orderable: true,
                },
                {
                    label: '風速',
                    name: 'wdsd',
                    orderable: true,
                },
                {
                    label: '溫度',
                    name: 'temp',
                    orderable: true,
                },
                {
                    label: '相對濕度',
                    name: 'humd',
                    orderable: true,
                },
                {
                    label: '氣壓',
                    name: 'pres',
                    orderable: true,
                },
                {
                    label: '日累積雨量',
                    name: 'h_24r',
                    orderable: true,
                },
                {
                    label: '每小時最大陣風風速',
                    name: 'h_fx',
                    orderable: true,
                },
                {
                    label: '每小時最大陣風風向',
                    name: 'h_xd',
                    orderable: true,
                },
                {
                    label: '每小時最大陣風時間',
                    name: 'h_fxt',
                    orderable: true,
                },
                {
                    label: '本日最高溫',
                    name: 'd_tx',
                    orderable: true,
                },
                {
                    label: '本日最高溫發生時間',
                    name: 'd_txt',
                    orderable: true,
                },
                {
                    label: '本日最低溫',
                    name: 'd_tn',
                    orderable: true,
                },
                {
                    label: '本日最低溫發生時間',
                    name: 'd_tnt',
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
                let wdir = [];
                let wdsd = [];
                let pres = [];
                this.data.data.forEach((el, index) => {
                    city.push(el.city)
                    wdir.push(parseInt(el.wdir))
                    wdsd.push(parseFloat(el.wdsd))
                    pres.push(parseFloat(el.pres))
                })

                let forecastArray = {
                    city: city,
                    wdir: wdir,
                    wdsd: wdsd,
                    pres: pres,
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
            Highcharts.chart('weatherObservationChart', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: '氣象觀測',
                    align: 'left'
                },
                xAxis: [{
                    categories: data.city,
                    crosshair: true
                }],
                yAxis: [{ // Secondary yAxis
                    gridLineWidth: 0,
                    title: {
                        text: '氣壓',
                        style: {
                            color: '#CCEEFF'
                        }
                    },
                    labels: {
                        format: '{value} ',
                        style: {
                            color: '#CCEEFF'
                        }
                    },

                }, { // Primary yAxis
                    title: {
                        text: '風向',
                        style: {
                            color: '#fd8307'
                        }
                    },
                    labels: {
                        format: '{value} ',
                        style: {
                            color: '#fd8307'
                        }
                    },
                    opposite: true
                },
                { // Tertiary yAxis
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
                    name: '氣壓',
                    type: 'column',
                    yAxis: 0,
                    data: data.pres,
                    dashStyle: 'shortdot',
                    tooltip: {
                        valueSuffix: ' %'
                    },
                    color: '#CCEEFF',

                }, {
                    name: '風向',
                    type: 'spline',
                    yAxis: 1,
                    data: data.wdir,
                    tooltip: {
                        valueSuffix: ' °'
                    },
                    color: '#fd8307',

                },{
                    name: '風速',
                    type: 'spline',
                    yAxis: 2,
                    data: data.wdsd,
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
        weatherObservationUrl(val){
            this.url = val
            this.getData(this.url);
        }
    } 
}
</script>