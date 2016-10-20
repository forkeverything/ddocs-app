<template>
    <div class="notes">
        <rectangle-loader :loading="loading" size="small"></rectangle-loader>
        <div v-if="! loading">
            <button type="button" class="btn btn-default btn-sm btn-add-note" @click="addNewNote()">
                <i class="fa fa-sticky-note-o"></i> Add Note
            </button>
            <!-- Notes Table -->
            <table class="table table-notes">
                <tbody>
                <template v-for="(note, index) in notes">
                    <single-note :note="note"
                                 :key="note.hash"
                                 :focused-index="focusedIndex"
                                 :file-request-hash="fileRequest.hash"
                                 :index="index"
                                 @set-focused-index="setFocusIndex"
                                 @toggle-check-note="toggleCheckNote"
                                 @update-note-body="updateNoteBody"
                                 @update-note-position="updateNotePosition"
                                 @add-new-note="addNewNote"
                                 @remove-note="removeNote"
                                 @focus-note="focusNote"
                    >
                    </single-note>

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
                fetchNotesRequest: '',
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
            updateNotePosition(index){
                this.saveChangesForNoteAtIndex(index);
            },
            updateNoteBody(body, index){
                let note = this.notes[index];
                if (!note) return;  // notes been deleted
                note.body = body;
                this.saveChangesForNoteAtIndex(index);
            },
            toggleCheckNote(index) {
                this.notes[index].checked = !this.notes[index].checked;
                this.saveChangesForNoteAtIndex(index);
            },
            setFocusIndex(index) {
                if (index === undefined) {
                    this.focusedIndex = '';
                } else {
                    this.focusedIndex = index;
                }
            },
            getNotes(){
                this.loading = true;
                this.notes = [];
                this.$http.get('/api/file_requests/' + this.fileRequest.hash + '/notes', {
                    before(xhr) {
                        if (this.fetchNotesRequest) RequestsMonitor.abortRequest(this.fetchNotesRequest);
                        this.fetchNotesRequest = xhr;
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    this.notes = _.map(response.json(), (note) => {
                        note.pending_requests = {
                            updating: '',
                            deleting: ''
                        };
                        return note;
                    });
                    this.loading = false;
                }, (response) => {
                    console.log(response);
                });
            },
            focusNote(position) {
                if (position < 0 || position > this.notes.length - 1) return;
                $($('.row-single-note')[position]).find('textarea').focus();
            },
            addNewNote(position = this.notes.length) {
                let newNote = {
                    hash: randomString(12), // we create a fake-hash first so vue can keep track of v-for
                    body: '',
                    saved: false,
                    checked: false,
                    position: position,
                    file_request_hash: this.fileRequest.hash,
                    pending_requests: {
                        updating: '',
                        deleting: ''
                    }
                };
                this.notes.splice(position, 0, newNote);
                this.$nextTick(() => {
                    this.focusNote(position);
                });
            },
            saveChangesForNoteAtIndex(index) {
                let note = this.notes[index];
                if (note.saved === false && !note.saving) {
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
                this.$http.post('/api/note', note, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    note.hash = response.json().hash;
                    note.saved = true;
                    note.saving = false;
                    if (note.needs_delete) {
                        this.deleteNote(note);
                        return;
                    }
                    if (note.needs_update) {
                        this.updateNote(note, index);
                    }
                }, (response) => {
                    // error
                    console.log(response);
                    note.saving = false;
                });
            },
            abortRequest(note, action) {
                if (action) {
                    if (note.pending_requests[action]) RequestsMonitor.abortRequest(note.pending_requests[action]);
                } else {
                    // No action specified, abort all actions
                    for (let action in note.pending_requests) {
                        if (note.pending_requests.hasOwnProperty(action)) {
                            if (note.pending_requests[action]) RequestsMonitor.abortRequest(note.pending_requests[action]);
                        }
                    }
                }

            },
            updateNote(note, index) {
                if (note.deleting) return;
                this.$http.put('/api/note/' + note.hash, {
                    'position': index,
                    'body': note.body,
                    'checked': note.checked
                }, {
                    before(xhr){
                        this.abortRequest(note, 'updating');
                        note.pending_requests.updating = xhr;
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
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
                if (note.saved === false) {
                    note.needs_delete = true;
                } else {
                    this.deleteNote(note);
                }
            },
            deleteNote(note) {
                note.deleting = true;
                this.$http.delete('/api/note/' + note.hash, {
                    before(xhr) {
                        this.abortRequest(note);
                        note.pending_requests.deleting = xhr;
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {

                }, (response) => {
                    console.log(response);
                })
            }
        },
        created() {
            vueGlobalEventBus.$on('focus-note', (position) => this.focusNote(position));
        },
        mounted() {
            this.getNotes();
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('focus-note');
        }
    }
</script>