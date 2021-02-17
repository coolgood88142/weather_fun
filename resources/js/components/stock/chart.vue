<template>
    <data-table
        :columns="columns"
        :per-page="perPage"
        url="./getFugle/chart/3515"
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
                    label: '時間',
                    name: 'time',
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
                {
                    label: '開盤價',
                    name: 'open',
                    orderable: true,
                },
                {
                    label: '收盤價',
                    name: 'close',
                    orderable: true,
                },
                {
                    label: '最高價',
                    name: 'high',
                    orderable: true,
                },
                {
                    label: '最低價',
                    name: 'low',
                    orderable: true,
                },
            ]
        }
    },
}
</script>