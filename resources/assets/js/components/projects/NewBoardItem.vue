<template>
    <li class="single-item field-new-item">
        <input type="text" class="input-name" v-model="name">
        <div class="submit-buttons">
            <button type="button" class="btn btn-sm btn-primary" @click="addItem('file')">File</button>
            <button type="button" class="btn btn-sm btn-info" @click="addItem('category')">Category</button>
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
        methods: {
            addItem(type) {
                let projectId = (this.parent.type === "App\\Project") ? this.parent.id : this.parent.project_id;
                let url = '/projects/' + projectId + '/' + (type === 'file' ? 'files' : 'categories');

                if(! this.ajaxReady) return;
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