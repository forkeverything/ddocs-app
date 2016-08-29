<template>
    <div id="modal-register" class="modal" role="dialog" v-el:modal-register>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h2 class="modal-title text-center">Create Account</h2>
                </div>
                <div class="modal-body">
                    <form-errors :event-bus="bus"></form-errors>
                    <form @submit.prevent="createAccount">
                        <div class="form-group row">
                            <label class="col-xs-4 control-label text-right">Name</label>
                            <div class="col-xs-6">
                                <input type="text" class="form-control" name="name" v-model="registerName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-xs-4 control-label text-right">Email</label>
                            <div class="col-xs-6">
                                <input type="email" class="form-control" name="email" v-model="registerEmail">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-xs-4 control-label text-right">Password</label>
                            <div class="col-xs-6">
                                <input type="password" class="form-control" name="password" v-model="registerPassword">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-xs-4 control-label text-right">Confirm Password</label>
                            <div class="col-xs-6">
                                <input type="password" class="form-control" name="password_confirmation"
                                       v-model="registerPasswordConfirmation">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-grey btn-space" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-solid-green" @click="createAccount">{{ registerButtonText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    var vueRegistrationEventBus = new Vue();
    export default {

        data: function () {
            return {
                ajaxReady: true,
                bus: vueRegistrationEventBus,
                registerName: '',
                registerEmail: '',
                registerPassword: '',
                registerPasswordConfirmation: ''
            };
        },
        props: [],
        computed: {
            registerButtonText: function() {
                if(this.ajaxReady) return 'Save';
                return 'Saving...';
            }
        },
        methods: {
            createAccount: function () {
                var self = this;
                self.registerButtonText = 'Saving...';
                vueClearValidationErrors(vueRegistrationEventBus);
                if (!self.ajaxReady) return;
                self.ajaxReady = false;

                self.$http.post('/register', {
                    "name": self.registerName,
                    "email": self.registerEmail,
                    "password": self.registerPassword,
                    "password_confirmation": self.registerPasswordConfirmation
                }).then((response) => {
                    // success
                    $(self.$els.modalRegister).modal('hide');
                    vueGlobalEventBus.$emit('created-account', response);
                }, (response) => {
                    // error
                    console.log('GET REQ Error!');
                    console.log(response);
                    self.ajaxReady = true;
                    vueValidation(response, vueRegistrationEventBus);
                });
            }
        },
        ready: function () {
            $(this.$els.modalRegister).modal({
                backdrop: 'static',
                show: false
            });

            vueGlobalEventBus.$on('show-registration-modal', function () {
                $(this.$els.modalRegister).modal('show');
            }.bind(this));
        }
    }
</script>