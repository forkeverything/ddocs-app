<template>
    <div class="loading-overlay" v-if="loading">
        <div class="rectangle-loader">
            <svg width="30" height="30" viewbox="0 0 30 30">
                <!-- <rect width="30" height="30" class="rect" />  <- this looks buggy in retina safari -->
                <polygon points="0 0 0 30 30 30 30 0" class="rect" />
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
    props: ['loading'],
    methods: {

    }
}
</script>