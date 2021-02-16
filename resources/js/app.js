require('./bootstrap')
window.Vue = require('vue');
window._ = require('lodash');
window.swal = require('sweetalert');
Vue.config.productionTip = false;

import DataTable from 'laravel-vue-datatable';
Vue.use(DataTable);