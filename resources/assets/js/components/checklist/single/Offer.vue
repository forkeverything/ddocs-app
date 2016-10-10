<template>
    <div id="join-offer" v-if="! checklistBelongsToUser && ! checklist.invitation_claimed">
        <h3 class="text-center">Help {{ checklist.user.name }} Out!</h3>
        <p class="text-center">Join Files Collector and give {{ checklist.user.name }} 10 free
            credits.
            You'll also get 5 bonus credits for being an awesome friend.
            <br>
            Think of it as our way of saying thanks for trying us out!
        </p>
        <a :href="'/register?invite_key=' + checklistHash"><p class="text-center">Sign Me Up</p></a>
    </div>
</template>
<script>
export default {
    data: function(){
        return {
            
        }
    },
    methods: {
        
    }
}
</script>