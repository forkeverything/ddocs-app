<template>
    <div class="onoffswitch">
        <input type="checkbox" v-model="model" class="onoffswitch-checkbox" :id="'myonoffswitch' + id">
        <label class="onoffswitch-label" :for="'myonoffswitch' + id">
            <span class="onoffswitch-inner"></span>
            <span class="onoffswitch-switch"></span>
        </label>
    </div>
</template>
<script>
export default {
    data: function(){
        return {
            id: this._uid
        }
    },
    props: ['model'],
    methods: {
        
    }
}
</script>