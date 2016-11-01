<template>
<div id="project-members">
    <ul class="list-inline list-unstyled">
        <li v-for="member in members">
            {{ member.name }}
        </li>
    </ul>
</div>
</template>
<script>
export default {
    data: function(){
        return {

        }
    },
    props: ['members'],
    methods: {

    }
}
</script>