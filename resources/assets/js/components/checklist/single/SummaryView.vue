<template>
    <div id="summary-view" class="content">
        <h4>Checklist Summary</h4>
        <div id="summary-recipients" class="summary-section" v-if="checklist">
            <div class="section-header expandable">
                <i class="fa fa-user icon"></i><span class="title">{{ checklist.recipients.length }} Recipients</span>
                <div class="triangle">
                    <i class="fa fa-caret-down"></i>
                    <i class="fa fa-caret-right"></i>
                </div>
            </div>
            <div class="section-content">
                <ul class="list-recipients list-unstyled" v-if="! editingRecipients">
                    <li v-for="recipient in checklist.recipients">
                        {{ recipient.email }}
                    </li>
                </ul>
                <a v-if="checklistBelongsToUser && ! editingRecipients" href="#" @click.prevent="toggleEditRecipients">Edit</a>
                <recipients-editor v-if="checklistBelongsToUser && editingRecipients"
                @hide="toggleEditRecipients"
                ></recipients-editor>
            </div>
        </div>
        <div id="summary-description" class="summary-section" v-if="checklistBelongsToUser || checklist.description">
            <div class="section-header expandable">
                <i class="fa fa-info-circle icon"></i><span class="title">Description</span>
                <div class="triangle">
                    <i class="fa fa-caret-down"></i>
                    <i class="fa fa-caret-right"></i>
                </div>
            </div>
            <div class="section-content">
                <p v-if="! checklistBelongsToUser">
                    {{ checklist.description }}
                </p>
                <editable-text-area v-if="checklistBelongsToUser" :value="checklist.description" :allow-null="true" placeholder="What these files are about..." @on-change="updateDescription"></editable-text-area>
            </div>
        </div>
        <div id="summary-file-count" class="summary-section" v-if="checklist">
            <div class="section-header">
                <i class="fa fa-file-o icon"></i><span class="title">Files {{ checklist.meta.num_received }} / {{ checklist.meta.num_total }}</span>
            </div>
        </div>
        <div id="summary-password" class="summary-section" v-if="checklistBelongsToUser">
            <div class="section-header expandable">
                <i class="fa fa-lock icon"
                   :class="{
                        'active': checklist.secure
                   }"
                ></i><span class="title">Password</span>
                <div class="triangle">
                    <i class="fa fa-caret-down"></i>
                    <i class="fa fa-caret-right"></i>
                </div>
            </div>
            <div class="section-content">
                <p class="text-muted">Recipients with an account won't need to enter this password.</p>
                <p v-show="checklist.secure && ! passwordForm">********</p>
                <ul class="list-unstyled list-inline" v-show="! passwordForm">
                    <li>
                        <a href="#" @click.prevent="togglePasswordForm">
                            <span v-show="checklist.secure">Change</span><span v-show="! checklist.secure">Set</span>
                        </a>
                    </li>
                    <li v-show="checklist.secure">
                        <a href="#" @click.prevent="setNewPassword(null)">
                            Remove
                        </a>
                    </li>
                </ul>
                <form v-show="passwordForm" @submit.prevent="setNewPassword">
                    <div class="form-group">
                        <input type="password" v-model="newPassword" class="form-control" placeholder="Password">
                    </div>
                    <div class="text-right">
                        <ul class="list-unstyled list-inline">
                            <li>
                                <a href="#" @click.prevent="togglePasswordForm">Cancel</a>
                            </li>
                            <li class="pr-0">
                                <a href="#" :disabled="! newPassword" @click.prevent="setNewPassword">Save</a>
                            </li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
        <div id="sign-up-offer" v-if="checklist && ! authenticatedUser">
            <h3>Help {{ checklist.user.name }} out!</h3>
            <div class="dino-joe">
                <img src="/images/dino_joe-01@0,5x.png" alt="Dinosaur pic">
            </div>
            <p>
                <a :href="'/register?invite_key=' + checklist.hash">Sign up</a> for an account and score free credits for both of you.
            </p>
        </div>
        <button v-if="checklistBelongsToUser"
                id="btn-delete-checklist"
                type="button"
                class="btn btn-danger"
                @click="$emit('delete-checklist')"
        >
            Permanently Delete Checklist
        </button>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                editingRecipients: false,
                passwordForm: false,
                newPassword: ''
            }
        },
        props: ['checklist-belongs-to-user'],
        computed: {
            authenticatedUser(){
                return this.$store.state.authenticatedUser;
            },
            checklist() {
                return this.$store.state.checklist.data;
            }
        },
        methods: {
            toggleEditRecipients() {
                this.editingRecipients = !this.editingRecipients;
            },
            updateDescription(newDescription){
                this.$store.commit('checklist/UPDATE_DESCRIPTION', newDescription);
                this.$store.dispatch('checklist/SAVE_CHANGES', {
                    description: newDescription
                });
            },
            togglePasswordForm(){
                this.passwordForm = ! this.passwordForm;
            },
            setNewPassword(newPassword){
                if(newPassword === null) this.newPassword = '';
                if(!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post(`/api/c/${ this.checklist.hash }/password`, {
                  "new_password": this.newPassword
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.$store.commit('checklist/UPDATE_SECURE', !! this.newPassword);
                    this.passwordForm = false;
                    this.newPassword = '';
                    this.ajaxReady = true;
                },(response) => {
                    // error
                    console.log("error updating checklist password.");
                    this.ajaxReady = true;
                });
            }
        },
        created() {
            $(document).on('click', '.summary-section .section-header.expandable', function() {
                $(this).parent('.summary-section').toggleClass('expanded');
            });
        },
        mounted() {

        },
        beforeDestroy(){
            $(document).off('click', '.summary-section .section-header.expandable');
        }
    }
</script>