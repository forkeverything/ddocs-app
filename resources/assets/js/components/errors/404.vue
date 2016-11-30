<template>
<div id="error">
    <h1 class="code">404</h1>
    <p class="explanation">
        Sorry! It looks like the <strong>{{ assetType }}</strong> you're looking for can't be found.
    </p>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            allowedTypes: [
                'checklist'
            ]
        }
    },
    computed: {
        assetType() {
            let type = this.$route.params.type;
            if(type && this.allowedTypes.indexOf(type) !== -1) return this.$route.params.type;
            return 'page';
        }
    },
    methods: {
        
    }
}
</script>