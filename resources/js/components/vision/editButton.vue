<template>
    <button
        type="button"
        data-toggle="modal"
        @click="click(data)"
        class="btn btn-primary btn-sm"
        data-target="#exampleModal">
        編輯
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