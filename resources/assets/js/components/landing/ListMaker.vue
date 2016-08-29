<template>
<div>
    <h3 class="text-center">For hardcore lists with plenty of files and options, check out our <a href="#" @click.prevent="showListMaker">list maker</a>.</h3>
    <div id="list-maker" v-show="listMaker">
        <checklist-maker :user="user"></checklist-maker>
    </div>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            listMaker: false
        }
    },
    props: ['user'],
    methods: {
        showListMaker: function() {
            this.listMaker = true;
        }
    }
}
</script>