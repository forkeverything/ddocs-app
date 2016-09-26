module.exports = {
    props: ['index'],
    methods: {
        clearQueue() {
            for (var i = 0; i < this[this.itemName].requests_queue.length; i++) {
                this[this.itemName].requests_queue.shift().abort();
            }
        },
        updateItem(){
            this.$http.put('/projects/' + this[this.itemName].project_id + '/item', this[this.itemName], {
                before(xhr){
                    this.clearQueue();
                    this[this.itemName].requests_queue.push(xhr);
                }
            }).then((res) => {
            }, (res) => {
                console.log('update item error');
                console.log(res);
            });
        },
        showDeleteProjectItemModal(item) {
            vueGlobalEventBus.$emit('delete-project-item', item);
        }
    },
    ready() {
        this[this.itemName].requests_queue = [];
    }
};