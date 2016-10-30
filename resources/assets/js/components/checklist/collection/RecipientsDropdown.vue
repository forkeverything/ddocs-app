<template>
    <li class="recipients">
        <span @mouseover="show"
              @mouseleave="hide"
        >
            <i class="fa fa-users"></i>{{ checklist.meta.num_recipients }}
        </span>
        <ul class="list-recipients list-unstyled" v-show="showList">
            <li v-for="recipient in checklist.meta.recipients">
                {{ recipient }}
            </li>
        </ul>
    </li>
</template>
<script>
    export default {
        data: function () {
            return {
                showList: false
            }
        },
        props: ['checklist'],
        methods: {
            show() {
                this.showList = true;
            },
            hide() {
                this.showList = false;
            }
        }
    }
</script>