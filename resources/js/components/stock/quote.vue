<template>
    <data-table 
        :data="data"
        :columns="columns"
        :per-page="perpage"
        @on-table-props-changed="reloadTable"
    >
    </data-table>
</template>


<script>
import DataTable from 'laravel-vue-datatable';
Vue.use(DataTable);

export default {
    props: {
		quoteUrl: {
			type: String,
		},
    },
    data() {
        return {
            url: this.quoteUrl,
            perpage : ['10', '15' ,'20', '25', '30'],
            data: {},
            tableProps: {
                search: '',
                length: 10,
                column: 'type',
                dir: 'asc'
            },
            translate: { nextButton: 'Next', previousButton: 'Previous', placeholderSearch: 'Search Table'},
            columns: [
                {
                    label: '資料類別',
                    name: 'type',
                    orderable: true,
                },
                {
                    label: '總成交價',
                    name: 'price',
                    orderable: true,
                },
                {
                    label: '最新一筆成交時間',
                    name: 'at',
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
        quoteUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }
}
</script>