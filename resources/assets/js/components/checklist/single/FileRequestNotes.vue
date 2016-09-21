<template>
    <div class="notes">
        <button type="button" class="btn btn-primary btn-add-note btn-small" @click="addNewNote()"><i class="fa fa-sticky-note-o"></i> Add Note</button>
        <!-- Notes Table -->
        <table class="table table-notes">
            <tbody>
            <template v-for="(index, note) in fileRequest.notes">
                <single-note :note.sync="note" :file-request-hash="fileRequest.hash" :index="index" :focused-index.sync="focusedIndex"></single-note>
            </template>
            </tbody>
        </table>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                focusedIndex: '',
                newNote: {
                    body: ''
                }
            }
        },
        props: ['file-request'],
        computed: {},
        methods: {
            focusNote(position) {
                if(position < 0 || position > this.fileRequest.notes.length - 1) return;
                $($('.row-single-note')[position]).find('textarea').focus();
            },
            addNewNote(position = this.fileRequest.notes.length) {
                if (!this.ajaxReady) return;
                this.ajaxReady = false;
                this.$http.post('/note', {
                    'file_request_hash': this.fileRequest.hash,
                    'position': position,
                    'body': ''
                }).then((response) => {
                    // success
                    this.fileRequest.notes.splice(position, 0, response.json());
                    this.$nextTick(() => {
                        this.focusNote(position);
                    });
                    this.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log(response);
                    this.ajaxReady = true;
                });
            },
            deleteNote(index, keyboardEvent) {
                let note = this.fileRequest.notes[index];
                if (!this.ajaxReady) return;
                this.ajaxReady = false;
                this.$http.delete('/note/' + note.id).then((response) => {
                    // success

                    this.fileRequest.notes.splice(index, 1);
                    this.$nextTick(() => {
                        keyboardEvent.preventDefault();
                        this.focusNote(index - 1);
                    });

                    this.ajaxReady = true;
                }, (response) => {
                    console.log(response);
                    this.ajaxReady = true;
                })
            }
        },
        ready() {
            vueGlobalEventBus.$on('add-new-note', (index) => this.addNewNote(index + 1));
            vueGlobalEventBus.$on('delete-note', (args) => this.deleteNote(args.index, args.event));
            vueGlobalEventBus.$on('focus-note', (position) => this.focusNote(position));
        }
    }
</script>