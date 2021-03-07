<template>
    <div>
        <figure class="highcharts-figure">
            <div id="rainObservationChart"></div>
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
		rainObservationUrl: {
			type: String,
		},
    },
    data() {
        return {
            url: this.rainObservationUrl,
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
                    label: '每小時累積雨量',
                    name: 'rain',
                    orderable: true,
                },
                {
                    label: '每10分鐘累積雨量',
                    name: 'min_10',
                    orderable: true,
                },
                {
                    label: '每3小時累積雨量',
                    name: 'hour_3',
                    orderable: true,
                },
                {
                    label: '每6小時累積雨量',
                    name: 'hour_6',
                    orderable: true,
                },
                {
                    label: '每12小時累積雨量',
                    name: 'hour_12',
                    orderable: true,
                },
                {
                    label: '每天累積雨量',
                    name: 'hour_24',
                    orderable: true,
                },
                {
                    label: '本日累積雨量',
                    name: 'now',
                    orderable: true,
                },
                {
                    label: '前1日0時到現在之累積雨量',
                    name: 'latest_2days',
                    orderable: true,
                },
                {
                    label: '前2日0時到現在之累積雨量',
                    name: 'latest_3days',
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
                let elev = [];
                let rain = [];
                let now = [];
                this.data.data.forEach((el, index) => {
                    city.push(el.city)
                    elev.push(parseFloat(el.elev))
                    rain.push(parseFloat(el.rain))
                    now.push(parseFloat(el.now))
                })

                let forecastArray = {
                    city: city,
                    elev: elev,
                    rain: rain,
                    now: now,
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
            Highcharts.chart('rainObservationChart', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: '雨量觀測',
                    align: 'left'
                },
                xAxis: [{
                    categories: data.city,
                    crosshair: true
                }],
                yAxis: [{ // Secondary yAxis
                    gridLineWidth: 0,
                    title: {
                        text: '高度',
                        style: {
                            color: '#AAAAAA'
                        }
                    },
                    labels: {
                        format: '{value} ',
                        style: {
                            color: '#AAAAAA'
                        }
                    },

                }, { // Primary yAxis
                    title: {
                        text: '每小時累積雨量',
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
                        text: '本日累積雨量',
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
                    name: '高度',
                    type: 'column',
                    yAxis: 0,
                    data: data.elev,
                    dashStyle: 'shortdot',
                    tooltip: {
                        valueSuffix: ' '
                    },
                    color: '#AAAAAA',

                }, {
                    name: '每小時累積雨量',
                    type: 'spline',
                    yAxis: 1,
                    data: data.rain,
                    tooltip: {
                        valueSuffix: ' °'
                    },
                    color: '#fd8307',

                },{
                    name: '本日累積雨量',
                    type: 'spline',
                    yAxis: 2,
                    data: data.now,
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
        rainObservationUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }   
}
</script>