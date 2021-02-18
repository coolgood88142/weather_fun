<template>
    <data-table 
        :data="data"
        :columns="columns"
        @on-table-props-changed="reloadTable"
    >
    </data-table>
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
        this.getData(this.url);
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
        weatherObservationUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }
}
</script>