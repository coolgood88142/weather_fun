import keyword from "./components/vision/keyword.vue"
import upload from "./components/vision/upload.vue"

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
    },
    
})

// $('#myModal').on('shown.bs.modal', function () {
//     $('#myInput').trigger('focus')
//   })

