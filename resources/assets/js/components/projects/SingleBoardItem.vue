<template>
<li class="single-board-item">
    <div class="name" :class="item.type">{{ item.name }}</div>
    <ul v-if="hasNestedItems" class="list-unstyled list-nested-items">
        <single-boad-item v-for="nestedItem in item.items" :item="nestedItem"></single-boad-item>
        <new-board-item :parent.sync="item"></new-board-item>
    </ul>
</li>
</template>
<script>
export default {
    data: function(){
        return {

        }
    },
    computed: {
      hasNestedItems(){
          return this.item.items.length > 0;
      }
    },
    props: ['item'],
    methods: {

    }
}
</script>