<template>
    <div>
        <figure class="highcharts-figure">
            <div id="chart"></div>
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
		weatherObservationReportUrl: {
			type: String,
		},
    },
    data() {
        return {
            url: this.weatherObservationReportUrl,
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
                    name: '24r',
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
                    label: '每小時最大10分鐘平均風速，',
                    name: 'h_f10',
                    orderable: true,
                },
                {
                    label: '每小時最大10分鐘平均風向',
                    name: 'h_10d',
                    orderable: true,
                },
                {
                    label: '每小時最大10分鐘平均風速發生時間',
                    name: 'h_f10t',
                    orderable: true,
                },
                {
                    label: '每小時紫外線指數',
                    name: 'h_uvi',
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
                {
                    label: '本日總日照時數',
                    name: 'd_ts',
                    orderable: true,
                },
                {
                    label: '十分鐘盛行能見度',
                    name: 'vis',
                    orderable: true,
                },
                {
                    label: '十分鐘天氣現象描述',
                    name: 'weather',
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
            })
            // eslint-disable-next-line
            .catch(errors => {
                //Handle Errors
            })
        },
        reloadTable(tableProps) {
            this.getData(this.url, tableProps);
        }
    },
    watch:{
        weatherObservationReportUrl(val){
            this.url = val
            this.getData(this.url);
        }
    } 
}
</script>