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
        rainObservationUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }
}
</script>