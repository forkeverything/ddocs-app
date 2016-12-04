<template>
    <div id="add-file-request">
        <div class="text-right">
            <a v-show="! showForm" href="#" @click.prevent="toggleShow">Add File</a>
        </div>
        <div class="form-wrap" v-show="showForm">
            <h5>Add File</h5>
            <form @submit.prevent="save">
                <div class="fields form-group">
                    <div class="field-name">
                        <input type="text" ref="input" v-model="name" class="form-control" placeholder="File Name">
                    </div>
                    <div class="field-due">
                        <date-picker v-model="due"
                                     :carbon="true"
                                     :formatted="true"
                                     :button-only="true"
                        >
                        </date-picker>
                    </div>
                </div>
                <ul class="list-unstyled list-inline">
                    <li>
                        <a href="#" @click.prevent="reset">Cancel</a>
                    </li>
                    <li class="pr-0">
                        <a href="#" @click.prevent="save" :disabled="! canSave">Add</a>
                    </li>
                </ul>
            </form>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                showForm: false,
                name: '',
                due: ''
            }
        },
        props: ['visible'],
        computed: {
            checklist() {
                return this.$store.state.checklist.data;
            },
            canSave() {
                return this.name;
            }
        },
        watch: {
            visible(showing) {
                if (showing) this.$nextTick(() => $(this.$refs.input).focus());
            }
        },
        methods: {
            toggleShow() {
                this.showForm = !this.showForm;
            },
            hide() {
                this.$emit('hide');
            },
            reset() {
                this.name = '';
                this.due = '';
                this.toggleShow();
            },
            save(){
                if (!this.ajaxReady) return;
                this.ajaxReady = false;
                this.$http.post(`/api/c/${ this.checklist.hash }/add_file`, {
                    name: this.name,
                    due: this.due
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.$emit('added', response.json());
                    this.reset();
                    this.$nextTick(() => this.ajaxReady = true);
                }, (response) => {
                    // error
                    console.log("error adding new to file to checklist.");
                    this.ajaxReady = true;
                });
            }
        }
    }
</script>