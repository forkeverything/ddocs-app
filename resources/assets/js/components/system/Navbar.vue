<template>
    <nav id="navbar">

        <button type="button" class="btn-toggle-sidebar"
                v-show="! showSidebar"
                @click="toggleSidebar"
        >
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

            <div class="navbar-title" v-html="navTitle"></div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <!--<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">-->
                <!--<ul class="nav navbar-nav navbar-right">-->

                    <!--<li class="dropdown" v-if="authenticatedUser">-->
                        <!--<a href="#" class="dropdown-toggle text-capitalize" data-toggle="dropdown" role="button"-->
                           <!--aria-haspopup="true"-->
                           <!--aria-expanded="false">{{ authenticatedUser.name }}</a>-->
                        <!--<ul class="dropdown-menu">-->
                            <!--<li><router-link to="/checklists">Checklists</router-link></li>-->
                            <!--<li><router-link to="/projects">Projects</router-link></li>-->
                            <!--<li><router-link to="/account">Account</router-link></li>-->
                            <!--<li>-->
                                <!--<a class="clickable" @click.prevent="logout">-->
                                    <!--Logout-->
                                <!--</a>-->
                            <!--</li>-->
                        <!--</ul>-->
                    <!--</li>-->

                    <!--<template v-if="! authenticatedUser">-->
                        <!--<li>-->
                            <!--<router-link to="/login" class="navbar-link">Login</router-link>-->
                        <!--</li>-->
                        <!--<li>-->
                            <!--<router-link to="/register" class="navbar-link">Sign Up</router-link>-->
                        <!--</li>-->
                    <!--</template>-->
                <!--</ul>-->
            <!--</div>&lt;!&ndash; /.navbar-collapse &ndash;&gt;-->
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
            },
            navTitle() {
                return this.$store.state.navTitle;
            },
            showSidebar () {
                return this.$store.state.showSidebar;
            }
        },
        methods: {
            logout(){
                Authenticator.logout();
            },
            toggleSidebar() {
                this.$store.commit('toggleSidebar');
            }
        }
    }
</script>