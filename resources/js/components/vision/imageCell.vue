<template>
    <img :src="data.image" />
</template>

<script>
export default {
    props: {
        data: {
            type: Object,
        }
    },
}
</script>