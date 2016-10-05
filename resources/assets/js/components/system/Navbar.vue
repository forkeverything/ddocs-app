<template>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>


                <router-link to="/" class="navbar-brand"><img alt="Brand" src="/images/logo/fc_logo_v1.svg"
                                                              class="img-logo"></router-link>

            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">

                    <li class="dropdown" v-if="authenticatedUser">
                        <a href="#" class="dropdown-toggle text-capitalize" data-toggle="dropdown" role="button"
                           aria-haspopup="true"
                           aria-expanded="false">{{ authenticatedUser.name }}</a>
                        <ul class="dropdown-menu">
                            <li><router-link to="/checklists">Checklists</router-link></li>
                            <li><router-link to="/projects">Projects</router-link></li>
                            <li><router-link to="/account">Account</router-link></li>
                            <li>
                                <a class="clickable" @click.prevent="logout">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>

                    <template v-if="! authenticatedUser">
                        <li>
                            <a href="/login" class="navbar-link">
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="/register" class="navbar-link">
                                Sign Up
                            </a>
                        </li>
                    </template>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</template>
<script>
    export default {
        data: function () {
            return {}
        },
        computed: {
            authenticatedUser(){
                return this.$store.state.authenticatedUser;
            }
        },
        methods: {
            logout(){
                auth.removeCookie();
                this.$store.commit('setUser', '');
                this.$router.push('/login');
            }
        }
    }
</script>