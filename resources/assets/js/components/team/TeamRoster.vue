<template>
    <div id="team-roster" class="container-fluid">
        <h3 class="text-capitalize">
            <small>Team Roster</small>
            <br>
            {{ team.name }}
        </h3>
    </div>
</template>
<script>
export default {
    name: 'TeamRoster',
    data: function(){
        return {
            
        }
    },
    computed: {
        authenticatedUser(){
            return this.$store.state.authenticatedUser;
        },
        team() {
            return this.authenticatedUser.team;
        },
        isOwner(){
            return this.team && ! this.authenticatedUser.team_id;
        },
        isMember(){
            return this.team && ! this.isOwner;
        }
    },
    methods: {
        
    }
}
</script>