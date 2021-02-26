<template>
    <div class="modal" id="exampleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">keywords</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>This rows email address is: {{ row.keyword }}</p>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    <div class="form-check form-check-inline">
						<input type="button" class="btn btn-primary" id="cancel" name="cancel"
								value="取消" data-dismiss="modal">
					</div>
					<div class="form-check form-check-inline">
						<input type="button" class="btn btn-primary" id="save" name="save"
								value="儲存" @click="checkCardData()">
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
            type: Object,
        }
    },
    watch:{
        keyWords(val){
            console.log(val)
            return val.split(",")
        }
    }
}
</script>