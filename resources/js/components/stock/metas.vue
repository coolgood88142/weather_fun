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
		metasUrl: {
			type: String,
		},
    },
    data() {
        return {
            url: this.metasUrl,
            data: {},
            tableProps: {
                search: '',
                length: 10,
                column: 'item',
                dir: 'asc'
            },
            columns: [
                {
                    label: '項目',
                    name: 'item',
                    orderable: true,
                },
                {
                    label: '現況',
                    name: 'value',
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
        metasUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }
}
</script>