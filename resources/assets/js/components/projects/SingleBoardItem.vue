<template>
    <li class="single-board-item">
        <div class="main">
            <span class="name" :class="typeClassName">{{ item.name }}</span>
            <ul class="list-unstyled list-inline list-item-actions">
                <li><button type="button" class="btn btn-add-sub-item" @click="showNewSubItemField"><i class="fa fa-level-down"></i></button></li>
                <li><button type="button" class="btn btn-delete"><i class="fa fa-close"></i></button></li>
                <li><button type="button" class="btn btn-link"><i class="fa fa-link"></i></button></li>
            </ul>
        </div>
        <ul class="list-unstyled list-board-items nested"
            :class="{
                'has-items': hasNestedItems
            }"
        >
            <single-board-item v-for="nestedItem in item.items" :item="nestedItem"></single-board-item>
            <new-board-item :parent.sync="item"></new-board-item>
        </ul>
    </li>
</template>
<script>
    export default {
        data: function () {
            return {}
        },
        computed: {
            hasNestedItems(){
                return this.item.items.length > 0;
            },
            typeClassName() {
                switch(this.item.type) {
                    case 'App\\ProjectCategory':
                        return 'category';
                    case 'App\\ProjectFile':
                        return 'file';
                }
            }
        },
        props: ['item'],
        methods: {
            showNewSubItemField() {
                this.$set('item.newItemField', true);
            }
        }
    }
</script>