<template>
    <data-table
        :columns="columns"
        url="./getFugle"
    >
    </data-table>
</template>

<script>
export default {
    data() {
        return {
            tableProps: {
                search: '',
                length: '',
                column: 'open',
            },
            columns: [
                {
                    label: 'Open',
                    name: 'open',
                    orderable: true,
                },
                {
                    label: 'Close',
                    name: 'close',
                    orderable: true,
                },
                {
                    label: 'High',
                    name: 'high',
                    orderable: true,
                },
            ]
        }
    },
}
</script>