<template>
    <li class="project-category board-item">
        <board-item-name :item.sync="category" :update-function="updateItem"></board-item-name>
    </li>
</template>
<script>
    const projectItem = require('../../mixins/ProjectItem');
    export default {
        data: function () {
            return {
                itemName: 'category'
            }
        },
        props: ['category'],
        mixins: [projectItem],
        methods: {}
    }
</script>