<template>
    <div class="loading-overlay" v-if="loading">
        <div class="rectangle-loader">
            <svg :width="width" :height="height" :viewbox="viewbox">
                <!-- <rect width="30" height="30" class="rect" />  <- this looks buggy in retina safari -->
                <polygon :points="points" class="rect" :class="size"/>
            </svg>
        </div>
    </div>
</template>
<script>
export default {
    data: function(){
        return {

        }
    },
    computed: {
        small() {
            return this.size === 'small';
        },
        large() {
            return this.size === 'large';
        },
        width() {
            if(this.small) return '20';
            if(this.large) return '40';
            return '30';
        },
        height() {
            if(this.small) return '20';
            if(this.large) return '40';
            return '30';
        },
        viewbox() {
            if(this.small) return '0 0 20 20';
            if(this.large) return '0 0 40 40';
            return '0 0 30 30';
        },
        points() {
            if(this.small) return '0 0 0 20 20 20 20 0';
            if(this.large) return '0 0 0 40 40 40 40 0';
            return '0 0 0 30 30 30 30 0';
        }
    },
    props: ['loading', 'size'],
    methods: {

    }
}
</script>