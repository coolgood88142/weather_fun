<template>
    <data-table
        :columns="columns"
        :per-page="perPage"
        url="./getFugle/dealts/3515"
    >
    </data-table>
</template>

<script>
import DataTable from 'laravel-vue-datatable';
Vue.use(DataTable);

export default {
    name: 'app',
    data() {
        return {
            perPage: ['10', '25', '50'],
            columns: [
                {
                    label: '交易編號',
                    name: 'serial',
                    orderable: true,
                },
                {
                    label: '時間',
                    name: 'at',
                    orderable: true,
                },
                {
                    label: '張數',
                    name: 'unit',
                    orderable: true,
                },
                {
                    label: '價錢',
                    name: 'price',
                    orderable: true,
                },
            ]
        }
    },
}
</script>