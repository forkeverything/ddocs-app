<template>
    <li class="project-file board-item">
        <board-item-name :item.sync="file" :update-function="updateItem"></board-item-name>
    </li>
</template>
<script>
    const projectItem = require('../../mixins/ProjectItem');
    export default {
        data: function () {
            return {
                itemName: 'file'
            }
        },
        props: ['file'],
        mixins: [projectItem],
        methods: {}
    }
</script>