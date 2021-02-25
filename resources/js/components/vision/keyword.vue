<template>
    <data-table 
        :data="data"
        :columns="columns"
        @on-table-props-changed="reloadTable">
    </data-table>
    <model
        :row="selectedRow"
    >
    </model>
</template>

<script>
import imageCell from './imageCell';
import Modal from './Modal';
import button from './button';
import DataTable from 'laravel-vue-datatable';
// import Model from '../stock/meta/model.vue';
Vue.use(DataTable);

export default {
    props: {
		keyWordUrl: {
			type: String,
		},
    },
    components: {
        imageCell,
        Modal,
        button,
    },
    data() {
        return {
            url:  this.keyWordUrl,
            data: {},
            tableProps: {
                search: '',
                length: 50,
                column: 'no',
                dir: 'asc'
            },
            columns: [
                {
                    label: '圖片',
                    name: 'image',
                    orderable: true,
                    component: imageCell,
                },
                {
                    label: 'keyword',
                    name: 'keyword',
                    orderable: true,
                },
                {
                    label: 'ediu',
                    name: '',
                    orderable: false,
                    component: button,
                    event: "click",
                    handler: this.updateSelectedModal,
                }, 
            ],
            selectedRow: {},
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
        },
        updateSelectedModal(data) {
            this.selectedRow = data;
        }
    },
    watch:{
        alarmUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }
}
</script>