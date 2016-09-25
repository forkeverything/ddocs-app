<template>
    <div id="project-board"
         :class="{
            'dragging': dragging
         }"
    >
        <delete-item-modal></delete-item-modal>
        <div id="updating-position-overlay" v-show="updatingPosition">
            <div class="loader">
                <cube-loader></cube-loader>
            </div>
        </div>
        <button id="btn-new-item"
                type="button"
                class="btn btn-sm"
                @click="showNewRootItemField"
        >
            New Item
        </button>
        <ul class="list-board-items list-unstyled" data-parent-type="App\Project" :data-parent-id="project.id">
            <single-board-item v-for="item in project.items" :item="item"></single-board-item>
            <new-board-item :parent.sync="project"></new-board-item>
        </ul>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                dragging: false,
                drake: '',
                updatingPosition: true
            }
        },
        props: ['project'],
        methods: {
            initDrag(){
                // if we're re-initializing
                if(this.drake) this.drake.destroy();
                // create our drake instance
                this.drake = dragula(Array.prototype.slice.call(document.querySelectorAll('.list-board-items')), {
                    moves: (el, source, handle, sibling) => {
                        // Don't want to make the new board item field draggable
                        return !el.classList.contains('field-new-item');
                    },
                    accepts: (el, target, source, sibling) => {
                        // prevent dragged containers from trying to drop inside itself and
                        // prevent dropping as last item (needs sibling)
                        return !this.contains(el, target) && sibling && ! sibling.classList.contains('drag-space');
                    }
                });
                // 'dragging' class
                this.drake.on('drag', () => {
                    this.dragging = true;
                });
                this.drake.on('cancel', () => {
                    this.dragging = false;
                });
                // Handle drop
                this.drake.on('drop', (el, target, source, sibling) => {
                    this.updateItemPosition(el, target, source, sibling);
                });

                this.updatingPosition = false;
            },
            showNewRootItemField(){
                this.$set('project.newItemField', true);
            },
            contains(a, b) {
                return a.contains ?
                a != b && a.contains(b) :
                        !!(a.compareDocumentPosition(b) & 16);
            },
            updateItemPosition(el, target, source, sibling) {
                this.updatingPosition = true;
                let currentPosition = el.dataset.position;
                let siblingPosition = sibling.dataset.position;
                let differentParent = source.dataset.parentType !== target.dataset.parentType || source.dataset.parentId !== target.dataset.parentId;
                // if we moved up or came from different parent
                let newPosition = (currentPosition > siblingPosition || differentParent ) ? siblingPosition : siblingPosition - 1;
                let data = {
                    'parent_type': target.dataset.parentType,
                    'parent_id': target.dataset.parentId,
                    'position': newPosition,
                    'type': el.dataset.type,
                    'id': el.dataset.id
                };
                this.$http.put('/projects/' + this.project.id + '/positions', data)
                        .then((response) => {
                            this.project = response.json();
                            this.$nextTick(() => {
                                // Remove the element that dragula made so our DOM doesn't conflict.
                                el.remove();
                                this.initDrag();
                            });
                        }, (response) => {
                            console.log("Couldn't update item positions");
                            console.log(response);
                        });
            }
        },
        ready(){
            this.initDrag();

            vueGlobalEventBus.$on('init-drag', () => {
                this.updatingPosition = true;
                this.initDrag();
            });

            vueGlobalEventBus.$on('set-project', (project) => {
                this.updatingPosition = true;
                this.project = project;
                this.$nextTick(() => {
                    this.initDrag();
                    this.updatingPosition = false;
                });
            });
        }
    }
</script>               