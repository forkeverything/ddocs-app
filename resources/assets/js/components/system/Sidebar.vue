<template>
<div id="sidebar" v-show="showSidebar">
    <button type="button" class="close btn-close-sidebar"
            aria-label="Close" @click="toggleSidebar"
    >
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="sidebar-logo">
        <router-link to="/">
            <img alt="Brand" src="/images/logo/logo-white.svg">
        </router-link>
    </div>
    <div id="side-checklists" class="links-section">
        <h5 class="header">Checklists</h5>
        <router-link to="/checklists/make" class="link-add"><button type="button" class="btn btn-text">+</button></router-link>
        <ul id="list-side-checklist" class="list-unstyled list-links">
            <li><router-link to="/checklists">View All</router-link></li>
        </ul>
    </div>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            recentChecklists: ''
        }
    },
    computed: {
      showSidebar () {
          return this.$store.state.showSidebar;
        }
    },
    methods: {
        toggleSidebar() {
            this.$store.commit('toggleSidebar');
            this.showOnResize = false;
        },
        checkShowSidebar: _.debounce(function() {
            let windowWidth = $(window).width();
            if(windowWidth < 768 &&   this.showSidebar) this.toggleSidebar();
            if(windowWidth >= 768 && ! this.showSidebar) this.toggleSidebar();
        }, 150),
        fetchRecentChecklists() {
            this.$http.get('/api/checklists/recent', {
                before(xhr) {
                    RequestsMonitor.pushOntoQueue(xhr);
                }
            }).then((response) => {
                // success
                this.recentChecklists = response.json();
            }, (response) => {
                // error
                console.log('Error fetching from: /api/checklists/recent');
            });
        }
    },
    created() {
        window.addEventListener('resize', this.checkShowSidebar);
    },
    mounted() {
        this.fetchRecentChecklists();
        this.$nextTick(this.checkShowSidebar);
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.checkShowSidebar)
    }
}
</script>