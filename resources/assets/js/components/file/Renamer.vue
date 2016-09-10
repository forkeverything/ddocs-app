<template>
    <div class="file-renamer">
        <input type="text"
               v-el:input
               v-model="name"
               class="form-control"
               v-show="file.renaming"
               @blur="rename"
        >
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                name: '',
                error: ''
            }
        },
        props: ['file'],
        computed: {
            validName: function () {
                return this.name && this.name !== this.file.name;
            }
        },
        methods: {
            setName: function () {
                this.name = this.file.name;
            },
            rename: function () {
                var self = this;

                if (!this.validName) {
                    this.setName();
                    this.file.renaming = false;
                    return;
                }

                if (!self.ajaxReady) return;
                self.ajaxReady = false;

                self.$http.put('/fr/' + this.file.hash, {
                    name: self.name
                }).then((response) => {
                    // success
                    let updatedFile = JSON.parse(response.data);
                    updatedFile.renaming = false;
                    self.file = updatedFile;
                    self.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log('GET REQ Error!');
                    console.log(response);
                    this.$set('file.renaming', false);
                })
            }
        },
        ready: function () {
            this.setName();

            this.$watch('file.renaming', () => {
                this.$nextTick(() => $(this.$els.input).focus());
            });
        }
    }
</script>