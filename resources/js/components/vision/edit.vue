<template>
    <div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
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
                    <div class="row">
                        <div class="col">英文關鍵字</div>
                        <div class="col">中文關鍵字</div>
                        <div class="w-100"></div>
                        <div class="col-6">
                            <table id="entable" class="table" v-sortable="{ onUpdateEnglish: onUpdateEnglish }">
                                <tr class="table-info" v-for="(en, index) in enKeywords" :key="en" >
                                    <th><button  class="navbar-toggler pull-left" type="button" data-toggle="collapse-side" data-target-sidebar=".side-collapse-left" data-target-content=".side-collapse-container-left" 
                                        @click="upEnglishKeyWord(index)">
                                    <i class="bi-arrow-up-circle"></i>
                                    </button>
                                    <button  class="navbar-toggler pull-left" type="button" data-toggle="collapse-side" data-target-sidebar=".side-collapse-left" data-target-content=".side-collapse-container-left" 
                                        @click="downEnglishKeyWord(index)">
                                    <i class="bi-arrow-down-circle"></i>
                                    </button></th>
                                    <th><input type="text" name="enkeys[]" :value="en" v-on:input="changeEnglishKeyWord(index)"></th>
                                    <th><button  class="navbar-toggler pull-left" type="button" data-toggle="collapse-side" data-target-sidebar=".side-collapse-left" data-target-content=".side-collapse-container-left" 
                                        @click="deleteEnglishKeyWord(index)">
                                    <i class="bi bi-trash"></i>
                                    </button></th>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table id="chtable" class="table" v-sortable="{ onUpdateChinese: onUpdateChinese }">
                                <tr class="table-info" v-for="(ch, index) in chKeywords" :key="ch" >
                                    <th><button  class="navbar-toggler pull-left" type="button" data-toggle="collapse-side" data-target-sidebar=".side-collapse-left" data-target-content=".side-collapse-container-left" 
                                        @click="upChineseKeyWord(index)">
                                    <i class="bi-arrow-up-circle"></i>
                                    </button>
                                    <button  class="navbar-toggler pull-left" type="button" data-toggle="collapse-side" data-target-sidebar=".side-collapse-left" data-target-content=".side-collapse-container-left" 
                                        @click="downChineseKeyWord(index)">
                                    <i class="bi-arrow-down-circle"></i>
                                    </button></th>
                                    <th><input type="text" name="chkeys[]" :value="ch" v-on:input="changeChineseKeyWord(index)"></th>
                                    <th><button  class="navbar-toggler pull-left" type="button" data-toggle="collapse-side" data-target-sidebar=".side-collapse-left" data-target-content=".side-collapse-container-left" 
                                        @click="deleteChineseKeyWord(index)">
                                    <i class="bi bi-trash"></i>
                                    </button></th>
                                </tr>
                            </table>
                        </div>
                    </div>
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

Vue.directive('sortable', {
  inserted: function (el, binding) {
    new Sortable(el, binding.value || {})
  }
})

export default {
    props: {
        englishKeywords: {
            type: Array,
        },
        chineseKeywords: {
            type: Array,
        },
        keyWordId: {
            type: Number,
        }
    },
    data() {
        return {
            'keywords': [
                this.englishKeywords,
                this.chineseKeywords,
            ],
            'enKeywords': this.englishKeywords,
            'chKeywords': this.chineseKeywords,
            'id' : this.keyWordId
        }
    },
    methods:{
        onUpdateEnglish(event) {
            let now = this.enKeywords[event.newIndex]
            let old = this.enKeywords[event.oldIndex]

            this.enKeywords[event.newIndex] = old
            this.enKeywords[event.oldIndex] = now
        },
        onUpdateChinese(event) {
            let now = this.chKeywords[event.newIndex]
            let old = this.chKeywords[event.oldIndex]

            this.chKeywords[event.newIndex] = old
            this.chKeywords[event.oldIndex] = now
        },
        addKeyWord(){
            let length1 = this.enKeywords.length
            let length2 = this.chKeywords.length

            this.enKeywords.splice(length1+1, 0, '')
            this.chKeywords.splice(length2+1, 0, '')
        },
        deleteEnglishKeyWord(index){
            this.enKeywords.splice(index, 1)
        },
        deleteChineseKeyWord(index){
            this.chKeywords.splice(index, 1)
        },
        upEnglishKeyWord(index){
            let now = this.enKeywords[index];
            let before = this.enKeywords[index-1];
            if(now != undefined && before != undefined){
                this.enKeywords.splice(index-1, 0, now)
                this.enKeywords.splice(index+1, 1)
            }
        },
        upChineseKeyWord(index){
            let now = this.chKeywords[index];
            let before = this.chKeywords[index-1];
            if(now != undefined && before != undefined){
                this.chKeywords.splice(index-1, 0, now)
                this.chKeywords.splice(index+1, 1)
            }
        },
        downEnglishKeyWord(index){
            let now = this.enKeywords[index];
            let after = this.enKeywords[index+1];
            if(now != undefined && after != undefined){
                this.enKeywords.splice(index, 0, after)
                this.enKeywords.splice(index+2, 1)
            }
        },
        downChineseKeyWord(index){
            let now = this.chKeywords[index];
            let after = this.chKeywords[index+1];
            if(now != undefined && after != undefined){
                this.chKeywords.splice(index, 0, after)
                this.chKeywords.splice(index+2, 1)
            }
        },
        saveKeyword(){
            const params = {
				id: this.id,
                english_keyword: this.getEnglishKeyWord(),
                chinese_keyword: this.getChineseKeyWord()
			}
            axios.post('/saveKeyWord', params).then((response) => {
				if (response.data.status === "success") {
					swal({
						title: response.data.message,
						confirmButtonColor: "#e6b930",
						icon: response.data.status,
						showCloseButton: true,
					}).then(() => {
						window.location.href = '/vision'
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
        getEnglishKeyWord(){
            let keyword = '';
            let ketElement = document.getElementsByName('enkeys[]');
            ketElement.forEach((e, index) => {
                if(e.value == ''){
                    swal({
                        title: '英文關鍵字第' + index + '筆資料不能為空',
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
        getChineseKeyWord(){
            let keyword = '';
            let ketElement = document.getElementsByName('chkeys[]');
            ketElement.forEach((e, index) => {
                if(e.value == ''){
                    swal({
                        title: '中文關鍵字第' + index + '筆資料不能為空',
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
        changeEnglishKeyWord(index){
            let ketElement = document.getElementsByName('enkeys[]');
            this.enKeywords[index] = ketElement[index].value
        },
        changeChineseKeyWord(index){
            let ketElement = document.getElementsByName('chkeys[]');
            this.chKeywords[index] = ketElement[index].value
        }
    },
    watch:{
        keyWordId(val){
            this.id = val
            return val
        },
        englishKeywords(val){
            this.enKeywords = val
            return val
        },
        chineseKeywords(val){
            this.chKeywords = val
            return val
        },
    }
}
</script>