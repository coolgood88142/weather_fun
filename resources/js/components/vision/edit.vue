<template>
    <div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
        
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">keywords</h5>
                    <div id="query" class="col-min-btn col-md-3 col-lg-2" style="text-align:right;">
                        <input type="button" name="query_data" class="btn btn-primary" value="新增" v-on:click="addKeyWord()">
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    
                </div>
                <div class="modal-body">
                    <table id="item_table" class="table">
                        <tr class="table-info" v-for="(col, index) in keyWordRow" :key="index">
                            <th><button  class="navbar-toggler pull-left" type="button" data-toggle="collapse-side" data-target-sidebar=".side-collapse-left" data-target-content=".side-collapse-container-left" 
                                @click="upKeyWord(index)">
                            <i class="bi-arrow-up-circle"></i>
                            </button>
                            <button  class="navbar-toggler pull-left" type="button" data-toggle="collapse-side" data-target-sidebar=".side-collapse-left" data-target-content=".side-collapse-container-left" 
                                @click="downKeyWord(index)">
                            <i class="bi-arrow-down-circle"></i>
                            </button></th>
                            <th><input type="text" name="keys[]" :value="col" v-on:input="changeKeyWord(index)"></th>
                            <th><button  class="navbar-toggler pull-left" type="button" data-toggle="collapse-side" data-target-sidebar=".side-collapse-left" data-target-content=".side-collapse-container-left" 
                                @click="deleteKeyWord(index)">
                            <i class="bi bi-trash"></i>
                            </button></th>

						</tr>
					</table>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    <div class="form-check form-check-inline">
						<input type="button" class="btn btn-secondary" id="cancel" name="cancel"
								value="取消" data-dismiss="modal">
					</div>
					<div class="form-check form-check-inline">
						<input type="button" class="btn btn-primary" id="save" name="save"
								value="儲存" @click="saveKeyword()">
					</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        row: {
            type: Array,
        },
        keyWordId: {
            type: String,
        }
    },
    data() {
        return {
            'keyWordRow': '',
        }
    },
    methods:{
        addKeyWord(){
            let length = this.keyWordRow.length
            this.keyWordRow.splice(length+1, 0, '')
        },
        deleteKeyWord(index){
            this.keyWordRow.splice(index, 1)
        },
        upKeyWord(index){
            let now = this.keyWordRow[index];
            let before = this.keyWordRow[index-1];
            if(now != undefined && before != undefined){
                this.keyWordRow.splice(index-1, 0, now)
                this.keyWordRow.splice(index+1, 1)
            }
        },
        downKeyWord(index){
            let now = this.keyWordRow[index];
            let after = this.keyWordRow[index+1];
            if(now != undefined && after != undefined){
                this.keyWordRow.splice(index, 0, after)
                this.keyWordRow.splice(index+2, 1)
            }
        },
        saveKeyword(){
            const params = {
				id: this.keyWordId,
                keyword: this.getKeyWord()
			}
            axios.post('/saveKeyWord', params).then((response) => {
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

        },
        getKeyWord(){
            let keyword = '';
            let ketElement = document.getElementsByName('keys[]');
            ketElement.forEach((e, index) => {
                if(e.value == ''){
                    swal({
                        title: '第' + index + '筆資料不能為空',
                        confirmButtonColor: "#e6b930",
                        icon: 'error',
                        showCloseButton: true,
                    })
                    keyword = ''
                }else{
                    keyword = keyword + e.value + ','
                }
            })

            if(keyword != ''){
               keyword = keyword.substring(0,keyword.length-1)
            }

            return keyword
        },
        changeKeyWord(index){
            let ketElement = document.getElementsByName('keys[]');
            this.keyWordRow[index] = ketElement[index].value

        }
    },
    watch:{
        keyWords(val){
            console.log(val)
            return val.split(",")
        },
        row(val){
            this.keyWordRow = val
            return val
        },
    }
}
</script>