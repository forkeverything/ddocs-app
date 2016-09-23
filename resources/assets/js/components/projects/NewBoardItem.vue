<template>
    <li class="single-board-item field-new-item" :data-position="(parent.items.length - 1)">
        <div class="wrap" v-show="parent.newItemField">
            <input type="text" class="input-name" v-model="name" v-el:input @blur="checkHideField">
            <div class="submit-buttons">
                <button type="button" class="btn btn-sm btn-primary" @click="addItem('file')">File</button>
                <button type="button" class="btn btn-sm btn-info" @click="addItem('category')">Category</button>
            </div>
        </div>
    </li>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                name: '',
            }
        },
        props: ['parent'],
        watch: {
          'parent.newItemField'(visible) {
              if(visible) {
                  this.$nextTick(() => {
                      $(this.$els.input).focus();
                  });
              }
          }
        },
        methods: {
            checkHideField() {
              if(!this.name) {
                  this.$set('parent.newItemField', false);
              }
            },
            addItem(type) {
                let projectId = (this.parent.type === "App\\Project") ? this.parent.id : this.parent.project_id;
                let url = '/projects/' + projectId + '/' + (type === 'file' ? 'files' : 'categories');

                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post(url, {
                    name: this.name,
                    parent_id: this.parent.id,
                    parent_type: this.parent.type,
                    position: this.parent.items.length,
                    project_id: projectId
                }).then((response) => {
                    // success
                    this.parent.items.push(response.json());
                    this.name = '';
                    this.ajaxReady = true;
                    this.$nextTick(() => {
                        $(this.$els.input).focus();
                    });
                }, (response) => {
                    // error
                    console.log('error adding item');
                    console.log(response);
                    this.ajaxReady = false;
                });
            }
        }
    }
</script>