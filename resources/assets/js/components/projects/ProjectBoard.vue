<template>
    <div id="project-board">
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
            return {}
        },
        props: ['project'],
        methods: {
            showNewRootItemField(){
                this.$set('project.newItemField', true);
            },
            contains(a, b) {
                return a.contains ?
                a != b && a.contains(b) :
                        !!(a.compareDocumentPosition(b) & 16);
            },
            updateItemPosition(el, target, sibling) {

                let data = {
                    'parent_type': target.dataset.parentType,
                    'parent_id': target.dataset.parentId,
                    'position': sibling.dataset.position,
                    'type': el.dataset.type,
                    'id': el.dataset.id
                };

                console.log(data);
            }
        },
        ready(){
            let drake = dragula(Array.prototype.slice.call(document.querySelectorAll('.list-board-items')), {
                moves: (el, source, handle, sibling) => {
                    return true;
                    return el.classList.contains('single-board-item');
                },
                accepts: (el, target, source, sibling) => {
                    // prevent dragged containers from trying to drop inside itself
                    return !this.contains(el, target) && sibling;
                }
            });

            drake.on('drop', (el, target, source, sibling) => this.updateItemPosition(el, target, sibling));
        }
    }
</script>               