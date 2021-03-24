<template>
    <div>
        <figure class="highcharts-figure">
            <div id="tidalChart"></div>
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
		tidalUrl: {
			type: Array,
		},
    },
    data() {
        return {
            url: this.tidalUrl,
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
                    label: '潮汐',
                    name: 'tidal',
                    orderable: true,
                },
                {
                    label: '潮高(高程基準)',
                    name: 'twvd',
                    orderable: true,
                },
                {
                    label: '潮高(當地)',
                    name: 'local',
                    orderable: true,
                },
                {
                    label: '潮高(相對海圖)',
                    name: 'relative',
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
                let tidal = [];
                let twvd = [];
                let local = [];
                let relative = [];
                this.data.data.forEach((el, index) => {
                    city.push(el.city)
                    tidal.push(parseInt(el.tidal))
                    twvd.push(parseInt(el.twvd))
                    local.push(parseInt(el.local))
                    relative.push(parseInt(el.relative))
                })

                let forecastArray = {
                    city: city,
                    tidal: tidal,
                    twvd: twvd,
                    local: local,
                    relative: relative,
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
            Highcharts.chart('tidalChart', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: '潮汐預報',
                    align: 'left'
                },
                xAxis: [{
                    categories: data.city,
                    crosshair: true
                }],
                yAxis: [
                { // Tertiary yAxis
                    gridLineWidth: 0,
                    title: {
                        text: '潮高(當地)',
                        style: {
                            color: '#FF0000'
                        }
                    },
                    labels: {
                        format: '{value} ',
                        style: {
                            color: '#FF0000'
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
                    name: '潮高(高程基準)',
                    type: 'column',
                    yAxis: 0,
                    data: data.twvd,
                    dashStyle: 'shortdot',
                    tooltip: {
                        valueSuffix: ' '
                    },
                    color: '#000000',

                }, {
                    name: '潮高(當地)',
                    type: 'column',
                    yAxis: 0,
                    data: data.local,
                    dashStyle: 'shortdot',
                    tooltip: {
                        valueSuffix: ' '
                    },
                    color: '#FF0000',

                },{
                    name: '潮高(相對海圖)',
                    type: 'column',
                    yAxis: 0,
                    data: data.relative,
                    dashStyle: 'shortdot',
                    tooltip: {
                        valueSuffix: ' '
                    },
                    color: '#0000C6'

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
        tidalUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }  
}
</script>