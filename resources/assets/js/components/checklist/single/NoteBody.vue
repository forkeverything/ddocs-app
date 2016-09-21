<template>
</template>
<script>
    export default {
        data: function () {
            return {
                textAreaHeight: '25px'
            }
        },
        props: ['model'],
        watch: {
            model() {
                this.textAreaHeight = $(this.$els.sizer).height() + 'px';
            }
        },
        methods: {}
    }
</script>