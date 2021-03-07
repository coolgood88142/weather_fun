<template>
    <div>
        <figure class="highcharts-figure">
            <div id="ultravioletChart"></div>
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
		ultravioletUrl: {
			type: String,
		},
    },
    data() {
        return {
            url: this.ultravioletUrl,
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
                    label: '紫外線指數',
                    name: 'uvi',
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
                let uvi = [];
                this.data.data.forEach((el, index) => {
                    city.push(el.city)
                    uvi.push(parseFloat(el.uvi))
                })

                let forecastArray = {
                    city: city,
                    uvi: uvi,
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
            Highcharts.chart('ultravioletChart', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: '紫外線',
                    align: 'left'
                },
                xAxis: [{
                    categories: data.city,
                    crosshair: true
                }],
                yAxis: [{ // Secondary yAxis
                    gridLineWidth: 0,
                    title: {
                        text: '紫外線指數',
                        style: {
                            color: '#5500FF'
                        }
                    },
                    labels: {
                        format: '{value} ',
                        style: {
                            color: '#5500FF'
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
                    name: '紫外線指數',
                    type: 'column',
                    yAxis: 0,
                    data: data.uvi,
                    dashStyle: 'shortdot',
                    tooltip: {
                        valueSuffix: ' %'
                    },
                    color: '#5500FF',

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
        ultravioletUrl(val){
            this.url = val
            this.getData(this.url);
        }
    } 
}
</script>