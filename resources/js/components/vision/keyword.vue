<template>
    <div>
        <data-table 
            :data="data"
            :columns="columns">
            
        </data-table>
        <edit
            :row="selectedRow" :key-word-id="id">
        </edit>
    </div>
    <!-- <edit></edit> -->
</template>

<script>
import imageCell from './imageCell';
import edit from './edit';
import editButton from './editButton';
import deleteButton from './deleteButton';
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
        editButton,
        deleteButton,
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
                    component: editButton,
                    event: "click",
                    handler: this.updateSelectedModal,
                }, 
                // {
                //     label: '刪除',
                //     name: '',
                //     orderable: false,
                //     classes: { 
                //         'btn': true,
                //         'btn-primary': true,
                //         'btn-sm': true,
                //     },
                //     component: deleteButton,
                //     event: "click",
                //     handler: this.deleteKeyWord,
                // }, 
            ],
            selectedRow: {},
            id: '',
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
            this.id = data["editId"];
        },
        deleteKeyWord(data){
            const params = {
				id: data["editId"],
			}
            axios.post('/deleteKeyWord', params).then((response) => {
				if (response.data.status === "success") {
					swal({
						title: response.data.message,
						confirmButtonColor: "#e6b930",
						icon: response.data.status,
						showCloseButton: true,
					}).then(() => {
						window.location.reload()
					});
				}
			}).catch((error) => {
				if (error.response) {
					console.log(error.response.data)
					console.log(error.response.status)
					console.log(error.response.headers)
				} else {
					console.log("Error", error.message)
				}
			})
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