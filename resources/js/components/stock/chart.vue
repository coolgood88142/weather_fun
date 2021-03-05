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
		chartUrl: {
			type: String,
		},
    },
    data() {
        return {
            url: this.chartUrl,
            data: {},
            tableProps: {
                search: '',
                length: 5,
                column: 'time',
                dir: 'asc',
                pagination: {
                    showdisabled : true
                }
            },
            columns: [
                {
                    label: '時間',
                    name: 'time',
                    orderable: true,
                },
                {
                    label: '張數',
                    name: 'unit',
                    orderable: true,
                },
                {
                    label: '股數',
                    name: 'volume',
                    orderable: true,
                },
                {
                    label: '開盤價',
                    name: 'open',
                    orderable: true,
                },
                {
                    label: '收盤價',
                    name: 'close',
                    orderable: true,
                },
                {
                    label: '最高價',
                    name: 'high',
                    orderable: true,
                },
                {
                    label: '最低價',
                    name: 'low',
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
        chartUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }
}
</script>