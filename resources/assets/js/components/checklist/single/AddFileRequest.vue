<template>
    <div id="add-file-request">
        <div class="text-right">
            <button type="button" class="btn btn-sm btn-info" v-show="! showForm" @click="toggleShow"><i class="fa fa-plus"></i> File</button>
        </div>
        <div class="form-wrap" v-show="showForm">
            <h4>Add File</h4>
            <form @submit.prevent="save">
                <div class="fields">
                    <div class="form-group field-name">
                        <label for="">File Name</label>
                        <input type="text" ref="input" v-model="name" class="form-control">
                    </div>
                    <div class="form-group field-due">
                        <label for="">Due</label>
                        <date-picker v-model="due"
                                     :carbon="true"
                                     :formatted="true"
                                     :button-only="true"
                        >
                        </date-picker>
                    </div>
                </div>
                <div class="form-buttons">
                    <button class="btn btn-sm btn-default" type="button" @click="reset">Cancel</button>
                    <button class="btn btn-sm btn-info" type="submit" :disabled="! canSave">Save</button>
                </div>
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
                this.$http.post(`/api/c/${ this.checklist.hash }/files`, {
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