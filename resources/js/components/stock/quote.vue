<template>
    <data-table
        :columns="columns"
        :per-page="perPage"
        url="./getFugle/quote/3515"
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
                    label: '資料類別',
                    name: 'type',
                    orderable: true,
                },
                {
                    label: '總成交價',
                    name: 'price',
                    orderable: true,
                },
                {
                    label: '最新一筆成交時間',
                    name: 'at',
                    orderable: true,
                },
                {
                    label: '張數',
                    name: 'unit',
                    orderable: true,
                },
                {
                    label: '股數',
                    name: 'volume',
                    orderable: true,
                },
            ]
        }
    },
}
</script>