<template>
    <div class="user-avatar">
        <div class="initials">{{ initials }}</div>
    </div>
</template>
<script>
    export default {
        data: function(){
            return {

            };
        },
        props: ['user'],
        computed: {
            initials() {
                let initials = '';
                let arr = this.user.name.split(' ');
                initials += arr[0].charAt(0);
                if(arr[1]) initials += arr[1].charAt(0);
                return initials.toUpperCase();
            }
        },
        methods: {}
    }
</script>