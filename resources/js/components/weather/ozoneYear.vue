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
		ozoneYearUrl: {
			type: String,
		},
    },
    data() {
        return {
            url: this.ozoneYearUrl,
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
                    label: '臭氧量平均值',
                    name: 'ozoneYear',
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
        ozoneYearUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }
}
</script>