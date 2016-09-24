<template>
    <li class="single-board-item" :data-position="item.position" :data-type="item.type" :data-id="item.id" :class="{'without-nested': ! hasNestedItems}">
        <div class="main">
            <board-item-name :item.sync="item"></board-item-name>
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
            :data-parent-type="item.type"
            :data-parent-id="item.id"
        >
            <single-board-item v-for="nestedItem in item.items" :item.sync="nestedItem"></single-board-item>
            <li class="drag-space" v-if="! hasNestedItems"></li>
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
            }
        },
        props: ['item'],
        methods: {
            showNewSubItemField() {
                this.$set('item.newItemField', true);
            },
            isThisItem(item) {
                return item.type === this.item.type && item.id === this.item.id;
            },
            clearQueue() {
                for (var i = 0; i < this.item.requests_queue.length; i++) {
                    this.item.requests_queue.shift().abort();
                }
            },
            updateItem(){
                this.$http.put('/projects/' + this.item.project_id + '/item', this.item, {
                    before(xhr){
                        this.clearQueue();
                        this.item.requests_queue.push(xhr);
                    }
                }).then((res) => {
                }, (res) => {
                    console.log('update item error');
                    console.log(res);
                });
            }
        },
        ready() {
            // initialize a queue
            this.$set('item.requests_queue', []);

            vueGlobalEventBus.$on('update-board-item', (item) => {
                if(this.isThisItem(item)) this.updateItem();
            });
        }
    }
</script>