import keyword from "./components/vision/keyword.vue"
import upload from "./components/vision/upload.vue"
import edit from "./components/vision/edit.vue"

let user = new Vue({
    el: "#app",
    data: {
        url: './getVision',
        saveVisionUrl: './upload',
        showModal: false,
    },
    components: {
        'keyword': keyword,
        'upload': upload,
        'edit': edit,
    },
    
})

// $('#myModal').on('shown.bs.modal', function () {
//     $('#myInput').trigger('focus')
//   })

