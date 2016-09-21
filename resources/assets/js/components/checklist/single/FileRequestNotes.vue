<template>
    <div class="notes">
        <div class="loader" v-if="loading">
            <cube-loader></cube-loader>
        </div>
        <div v-else>
            <button type="button" class="btn btn-primary btn-add-note btn-small" @click="addNewNote()"><i
                    class="fa fa-sticky-note-o"></i> Add Note
            </button>
            <!-- Notes Table -->
            <table class="table table-notes">
                <tbody>
                <template v-for="(index, note) in notes">
                    <single-note :note.sync="note" :file-request-hash="fileRequest.hash" :index="index"
                                 :focused-index.sync="focusedIndex"></single-note>
                </template>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                loading: true,
                fetchRequests: [],
                notes: [],
                focusedIndex: ''
            }
        },
        props: ['file-request'],
        computed: {},
        watch: {
            fileRequest(){
                this.getNotes();
            }
        },
        methods: {
            getNotes(){
                this.loading = true;
                this.notes = [];
                this.$http.get('/fr/' + this.fileRequest.hash + '/notes', {
                    before(xhr) {
                        for (var i = 0; i < this.fetchRequests.length; i++) {
                            this.fetchRequests.shift().abort();
                        }
                        this.fetchRequests.push(xhr);
                    }
                }).then((response) => {
                    this.notes = _.map(response.json(), (note) => {
                        note.queue = {
                            updating: [],
                            deleting: []
                        };
                        return note;
                    });
                    this.loading = false;
                }, (response) => {
                    console.log("Couldn't fetch notes.");
                    console.log(response);
                });
            },
            focusNote(position) {
                if (position < 0 || position > this.notes.length - 1) return;
                $($('.row-single-note')[position]).find('textarea').focus();
            },
            addNewNote(position = this.notes.length) {
                let newNote = {
                    body: '',
                    checked: false,
                    position: position,
                    file_request_hash: this.fileRequest.hash,
                    queue: {
                        updating: [],
                        deleting: []
                    }
                };
                this.notes.splice(position, 0, newNote);
                this.$nextTick(() => {
                    this.focusNote(position);
                });
            },
            saveChanges(index) {
                let note = this.notes[index];
                if (!note.id && !note.saving) {
                    this.saveNewNote(note, index)
                } else {
                    if (note.saving) {
                        note.needs_update = true;
                    } else {
                        this.updateNote(note, index);
                    }
                }
            },
            saveNewNote(note, index){
                note.saving = true;
                this.$http.post('/note', note).then((response) => {
                    // success
                    console.log('saved new note');
                    note.id = response.json().id;
                    note.saving = false;
                    if (note.needs_delete) {
                        this.deleteNote(note);
                        return;
                    }
                    if (note.needs_update) {
                        console.log('updated note after saving!');
                        this.updateNote(note, index);
                    }
                }, (response) => {
                    // error
                    console.log(response);
                    note.saving = false;
                });
            },
            clearQueue(note, action) {
                for (var i = 0; i < note.queue[action].length; i++) {
                    note.queue[action].shift().abort();
                }
            },
            updateNote(note, index) {
                if (note.deleting) return;
                this.$http.put('/note/' + note.id, {
                    'position': index,
                    'body': note.body,
                    'checked': note.checked
                }, {
                    before(xhr){
                        this.clearQueue(note, 'updating');
                        note.queue.updating.push(xhr);
                    }
                }).then((response) => {
                    // success
                    console.log('updated note!');
                }, (response) => {
                    // error
                    console.log(response);
                });
            },
            removeNote(index, keyboardEvent) {
                let note = this.notes[index];
                // success
                this.notes.splice(index, 1);
                this.$nextTick(() => {
                    keyboardEvent.preventDefault();
                    this.focusNote(index - 1);
                });
                // If we're removing an unsaved note
                if (!note.id) {
                    note.needs_delete = true;
                } else {
                    this.deleteNote(note);
                }
            },
            deleteNote(note) {
                note.deleting = true;
                this.$http.delete('/note/' + note.id, {
                    before(xhr) {
                        this.clearQueue(note, 'updating');
                        this.clearQueue(note, 'deleting');
                        note.queue.deleting.push(xhr);
                    }
                }).then((response) => {
                    console.log('deleted note');
                }, (response) => {
                    console.log(response);
                })
            }
        },
        ready() {
            this.getNotes();
            vueGlobalEventBus.$on('add-new-note', (index) => this.addNewNote(index + 1));
            vueGlobalEventBus.$on('remove-note', (args) => this.removeNote(args.index, args.event));
            vueGlobalEventBus.$on('focus-note', (position) => this.focusNote(position));
            vueGlobalEventBus.$on('save-changes-note', (index) => this.saveChanges(index));
        }
    }
</script>