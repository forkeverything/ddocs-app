<template>
    <nav id="navbar" v-show="authenticatedUser">

        <button type="button" class="btn-toggle-sidebar"
                v-show="! showSidebar"
                @click="toggleSidebar"
        >
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        <div class="navbar-title truncate" v-html="navTitle"></div>

        <div class="user-account dropdown">
            <a href="#"
               class="dropdown-toggle link-settings"
               data-toggle="dropdown"
               role="button"
               aria-haspopup="true"
               aria-expanded="false"
            >
                <user-avatar :user="authenticatedUser"></user-avatar>
            </a>
            <ul class="dropdown-menu dropdown-menu-right ">
                <li>
                    <router-link to="/account">Account</router-link>
                </li>
                <li>
                    <a class="clickable" @click.prevent="logout">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
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