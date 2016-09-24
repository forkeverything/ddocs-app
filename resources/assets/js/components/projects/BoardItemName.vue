<template>
    <span class="name">
        <span class="current_name"
              :class="typeClassName"
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
               @blur="sendUpdateItemEvent"
               @keydown.enter.prevent="blurInput"
        >
    </span>
</template>
<script>
export default {
    data: function(){
        return {
            editing: false
        }
    },
    computed: {
        typeClassName() {
            switch(this.item.type) {
                case 'App\\ProjectCategory':
                    return 'category';
                case 'App\\ProjectFile':
                    return 'file';
            }
        }
    },
    props: ['item'],
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
        sendUpdateItemEvent() {
            this.editing = false;
            vueGlobalEventBus.$emit('update-board-item', this.item);
        }
    }
}
</script>