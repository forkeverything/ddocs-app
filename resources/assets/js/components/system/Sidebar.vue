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
</div>
</template>
<script>
export default {
    data: function(){
        return {}
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
        }, 150)
    },
    created() {
        window.addEventListener('resize', this.checkShowSidebar);
    },
    mounted() {
        this.$nextTick(this.checkShowSidebar);
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.checkShowSidebar)
    }
}
</script>