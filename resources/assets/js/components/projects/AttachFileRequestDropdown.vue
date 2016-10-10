<template>
    <li class="dropdown attach-fr-dropdown"
        :class="{
            'open': show
        }"
        @click.stop=""
    >
        <a class="btn btn-primary btn-sm"
           data-toggle="dropdown"
           aria-haspopup="true"
           aria-expanded="false"
           @click.prevent.stop="toggleDropdown"
           :disabled="attached"
        >
            <i class="fa fa-link"></i>Attach
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <h4>Attach File Request</h4>
            <p>Link an existing file request from a checklist to share with team members.</p>
            <input type="text" class="form-control" placeholder="File name, recipient email, checklist...">
        </div>
    </li>
</template>
<script>
export default {
    data: function(){
        return {
            show: false,
        }
    },
    computed: {
        attached(){
            return this.file.file_request_id
        }
    },
    props: ['file'],
    watch: {
        file(newFile){
            if(newFile) {
                console.log('search');
            }
        }
    },
    methods: {
        toggleDropdown(){
            if(this.attached) return;
            this.show = ! this.show;
        }
    },
    mounted(){

    }
}
</script>