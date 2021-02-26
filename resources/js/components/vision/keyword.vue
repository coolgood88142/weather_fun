<template>
    <div>
        <data-table 
            :data="data"
            :columns="columns">
            
        </data-table>
        <edit
            :row="selectedRow">
        </edit>
    </div>
    <!-- <edit></edit> -->
</template>

<script>
import imageCell from './imageCell';
import edit from './edit';
import button from './button';
import DataTable from 'laravel-vue-datatable';
Vue.use(DataTable);

export default {
    props: {
		keyWordUrl: {
			type: String,
		},
    },
    components: {
        imageCell,
        edit,
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
                    label: '操作',
                    name: '',
                    orderable: false,
                    component: button,
                    event: "click",
                    handler: this.updateSelectedModal,
                }, 
            ],
            selectedRow: {},
            getkeywords: '',
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
            this.selectedRow = data['keyword'].split(","); 
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