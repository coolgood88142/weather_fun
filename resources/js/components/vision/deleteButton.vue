<template>
    <button
        type="button"
        @click="click(data)"
        class="btn btn-danger">
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