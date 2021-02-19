<template>
    <div>
        <figure class="highcharts-figure">
            <div id="acidRainPhChart"></div>
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
		acidRainPhUrl: {
			type: String,
		},
    },
    data() {
        return {
            url:  this.acidRainUrl,
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
                    label: '平均值',
                    name: 'mean',
                    orderable: true,
                },
                {
                    label: '最大值',
                    name: 'max',
                    orderable: true,
                },
                {
                    label: '最小值',
                    name: 'min',
                    orderable: true,
                }
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
                let mean = [];
                let max = [];
                let min = [];
                this.data.data.forEach((el, index) => {
                    city.push(el.city)
                    mean.push(parseFloat(el.mean))
                    max.push(parseFloat(el.max))
                    min.push(parseFloat(el.min))
                })

                let forecastArray = {
                    city: city,
                    mean: mean,
                    max: max,
                    min: min,
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
            Highcharts.chart('acidRainPhChart', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: '酸雨PH值',
                    align: 'left'
                },
                xAxis: [{
                    categories: data.city,
                    crosshair: true
                }],
                yAxis: [{ // Secondary yAxis
                    gridLineWidth: 0,
                    title: {
                        text: '平均值',
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
                        text: '最大值',
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
                        text: '最小值',
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
                    name: '平均值',
                    type: 'column',
                    yAxis: 0,
                    data: data.mean,
                    dashStyle: 'shortdot',
                    tooltip: {
                        valueSuffix: ' %'
                    },
                    color: '#AAAAAA',

                }, {
                    name: '最大值',
                    type: 'spline',
                    yAxis: 1,
                    data: data.max,
                    tooltip: {
                        valueSuffix: ' °'
                    },
                    color: '#fd8307',

                },{
                    name: '最小值',
                    type: 'spline',
                    yAxis: 2,
                    data: data.min,
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
        acidRainPhUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }
}
</script>