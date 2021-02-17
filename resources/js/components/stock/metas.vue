<template>
    <data-table
        :columns="columns"
        :per-page="perPage"
        url="http://127.0.0.1:8000/getFugle/meta/3515"
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
                    label: '股票出示',
                    name: 'abnormal',
                    orderable: true,
                },
                {
                    label: '股票中文名稱',
                    name: 'nameZhTw',
                    orderable: true,
                },
                {
                    label: '產業別',
                    name: 'industryZhTw',
                    orderable: true,
                },
                {
                    label: '交易幣別代碼',
                    name: 'currency',
                    orderable: true,
                },
                {
                    label: '股票類別',
                    name: 'typeZhTw',
                    orderable: true,
                },
                {
                    label: '交易單位 股/張',
                    name: 'volumePerUnit',
                    orderable: true,
                },
                {
                    label: '是否為指數',
                    name: 'isIndex',
                    orderable: true,
                },
                {
                    label: '是否為權證',
                    name: 'isWarrant',
                    orderable: true,
                },
                {
                    label: '今日是否暫停買賣',
                    name: 'isSuspended',
                    orderable: true,
                },
                {
                    label: '今日是否已終止上市',
                    name: 'isTerminated',
                    orderable: true,
                },
                {
                    label: '今日參考價',
                    name: 'priceReference',
                    orderable: true,
                },
                {
                    label: '漲停價',
                    name: 'priceHighLimit',
                    orderable: true,
                },
                {
                    label: '跌停價',
                    name: 'priceLowLimit',
                    orderable: true,
                },
                {
                    label: '是否可先買後賣現股當沖',
                    name: 'canDayBuySell',
                    orderable: true,
                },
                {
                    label: '是否可先賣後買現股當沖',
                    name: 'canDaySellBuy',
                    orderable: true,
                },
                {
                    label: '是否豁免平盤下借券賣出',
                    name: 'canShortLend',
                    orderable: true,
                },
                {
                    label: '是否豁免平盤下融券賣出',
                    name: 'canShortMargin',
                    orderable: true,
                },
            ]
        }
    },
}
</script>