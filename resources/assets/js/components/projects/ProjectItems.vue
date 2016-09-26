<template>
    <div id="project-items">

        <div id="new-item-buttons-wrap">
            <button id="btn-new-project-file"
                    type="button"
                    class="btn btn-primary btn-sm"
                    @click="showNewItemField('files')"
            >
                <i class="fa fa-plus"></i> File
            </button>
            <button id="btn-new-project-category"
                    type="button"
                    class="btn btn-primary btn-sm"
                    @click="showNewItemField('categories')"
            >
                <i class="fa fa-plus"></i> Category
            </button>
        </div>

        <ul id="list-board-items" class="list-unstyled">
            <new-project-item :parent.sync="parent" :new-field.sync="newField"></new-project-item>
            <!--<single-board-item v-for="item in parent.items" :item="item"></single-board-item>-->
            <template v-for="(index, item) in parent.items">
                <project-file v-if="item.type === 'App\\ProjectFile'" :file.sync="item" :index="index"></project-file>
                <project-category v-if="item.type === 'App\\ProjectCategory'" :category.sync="item" :index="index"></project-category>
            </template>
        </ul>


        <delete-item-modal></delete-item-modal>
    </div>
</template>
<script>
export default {
    data: function(){
        return {
            newField: ''
        }
    },
    props: ['parent'],
    methods: {
        showNewItemField(type) {
            this.newField = type;
        }
    }
}
</script>