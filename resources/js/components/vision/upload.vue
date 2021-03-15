<template>
    <form method="POST" :action="uploadUrl" enctype="multipart/form-data" class="form-inline">
        <div class="form-group">
        <label for="po_image" class="block text-sm leading-5 font-medium text-gray-700">
            圖片上傳
            
        </label>
        <input type="file" class="form-control-file" name="po_image">
        </div>
        <div class="form-group">
            <div id="query" class="col-min-btn col-md-3 col-lg-2" style="text-align:right;">
                <input type="submit" name="query_data" class="btn btn-primary" value="上傳" >
            </div>
        </div>
        
    </form>
</template>

<script>
export default {
     props: {
		uploadUrl: {
			type: String,
		},
    },
}
</script>