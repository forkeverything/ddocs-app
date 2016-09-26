<template>
    <span class="name">
        <span class="current"
              v-show="! editing"
        >
            {{ item.name }}
        </span>
        <button type="button"
                class="btn btn-edit-name"
                @click="enterEditMode"
                v-show="! editing"
        >
            <i class="fa fa-pencil"></i>
        </button>
        <input type="text"
               v-model="item.name"
               class="input-name"
               v-el:input
               v-show="editing"
               @blur="updateName"
               @keydown.enter.prevent="blurInput"
        >
    </span>
</template>
<script>
export default {
    data: function(){
        return {
            originalName: '',
            editing: false
        }
    },
    computed: {
        validName() {
            return this.item.name;
        }
    },
    props: ['item', 'update-function'],
    methods: {
        blurInput() {
            $(this.$els.input).blur();
        },
        enterEditMode() {
            this.editing = true;
            this.$nextTick(() => {
                $(this.$els.input).focus();
            });
        },
        updateName() {
            if(! this.validName) this.item.name = this.originalName;
            this.editing = false;
            this.updateFunction();
            this.originalName = this.item.name;
        }
    },
    ready() {
        this.originalName = this.item.name;
    }
}
</script>