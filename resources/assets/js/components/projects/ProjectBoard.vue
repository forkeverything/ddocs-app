<template>
    <div id="project-board">
        <button id="btn-new-item"
                type="button"
                class="btn btn-sm"
                @click="showNewRootItemField"
        >
            New Item
        </button>
        <ul class="list-board-items list-unstyled">
            <single-board-item v-for="item in project.items" :item="item"></single-board-item>
            <new-board-item :parent.sync="project"></new-board-item>
        </ul>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {}
        },
        props: ['project'],
        methods: {
            showNewRootItemField(){
                this.$set('project.newItemField', true);
            }
        }
    }
</script>               