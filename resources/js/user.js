import userdatatable from "./components/userdatatable.vue"

let user = new Vue({
	el: "#app",
    components: {
        'userdatatable': userdatatable,
    },
})