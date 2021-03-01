<template>
    <button
        type="button"
        @click="click(data)"
        class="btn btn-primary btn-sm">
        刪除
    </button>
</template>
<script>
export default {
    props: {
        data: {},
        name: {},
        click: {},
        meta: {},
        classes: {},
    },
}
</script>