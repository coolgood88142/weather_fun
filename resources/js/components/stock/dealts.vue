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
		dealtsUrl: {
			type: String,
		},
    },
    data() {
        return {
            url: this.dealtsUrl,
            data: {},
            tableProps: {
                search: '',
                length: 10,
                column: 'serial',
                dir: 'asc'
            },
            columns: [
                {
                    label: '交易編號',
                    name: 'serial',
                    orderable: true,
                },
                {
                    label: '時間',
                    name: 'at',
                    orderable: true,
                },
                {
                    label: '張數',
                    name: 'unit',
                    orderable: true,
                },
                {
                    label: '價錢',
                    name: 'price',
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
        dealtsUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }
}
</script>