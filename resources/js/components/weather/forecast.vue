<template>
    <data-table
        :order-by="order"
        :columns="columns"
        :per-page="perPage"
        url="http://127.0.0.1:8000/getWeather/0"
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
            order: 'no',
            perPage: ['23'],
            columns: [
                {
                    label: '序號',
                    name: 'no',
                    orderable: true,
                },
                {
                    label: '縣市',
                    name: 'city',
                    orderable: true,
                },
                {
                    label: '天氣現象',
                    name: 'wx',
                    orderable: true,
                },
                {
                    label: '最高溫度',
                    name: 'maxt',
                    orderable: true,
                },
                {
                    label: '最低溫度',
                    name: 'mint',
                    orderable: true,
                },
                {
                    label: '舒適度',
                    name: 'ci',
                    orderable: true,
                },
                {
                    label: '降雨機率',
                    name: 'pop',
                    orderable: true,
                },
            ]
        }
    },
}
</script>