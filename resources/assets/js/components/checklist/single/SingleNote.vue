<template>
    <tr class="row-single-note">
        <td class="col-check fit-to-content"
            :class="{
                'is-focused': focused
            }"
        >
            <button type="button"
                    class="btn btn-check"
                    :class="{
                        checked: note.checked
                    }"
                    @click="toggleChecked"
            >
                <i class="fa fa-check"></i>
            </button>
        </td>
        <td class="col-body"
            :class="{
                'is-focused': focused
            }"
        >
            <div class="note-body"
                 :class="{
                        checked: note.checked
                    }"
            >
                <textarea class="form-control input-note-body"
                          rows="1"
                          v-model="body"
                          :style="{ height: textAreaHeight }"
                          @focus="setFocus"
                          @blur="blurInput"
                          @keydown.enter.prevent="addNoteAfterThisOne"
                          @keydown.delete="removeThisNote($event)"
                          @keydown.up.prevent.stop="pressedArrow('prev')"
                          @keydown.down.prevent.stop="pressedArrow('next')"
                >
                </textarea>
                <div class="sizer" ref="sizer">{{ body }}</div>
            </div>
        </td>
    </tr>
</template>
<script>
export default {
    data: function(){
        return {
            body: '',
            ajaxReady: true,
            textAreaHeight: '25px'
        }
    },
    computed: {
      focused() {
          return this.focusedIndex === this.index;
      }
    },
    watch: {
        index() {
            this.$emit('update-note-position', this.index);
        },
        body() {
            this.setTextAreaHeight();
        },
    },
    props: ['index', 'focused-index', 'note', 'file-request-hash'],
    methods: {
        toggleChecked() {
            this.$emit('toggle-check-note', index);
        },
        setTextAreaHeight() {
            this.textAreaHeight = $(this.$refs.sizer).height() + 'px';
        },
        setFocus() {
            this.$emit('focus-note', this.index);
        },
        blurInput() {
            this.$emit('focus-note');
            this.$emit('update-note-body', this.body, this.index);
        },
        addNoteAfterThisOne() {
            vueGlobalEventBus.$emit('add-new-note', this.index);
        },
        removeThisNote(event) {
            if(this.body) return;
            vueGlobalEventBus.$emit('remove-note', {
                index: this.index,
                event: event
            });
        },
        pressedArrow: function (direction) {
            let indexToFocus = (direction === 'prev') ? this.index - 1 : this.index + 1;
            vueGlobalEventBus.$emit('focus-note', indexToFocus);
        }
    },
    mounted() {
        this.body = this.note.body;
        if(this.note.position !== this.index) {
            this.$emit('update-note-position', this.index);
        }
        this.$nextTick(this.setTextAreaHeight);
    }
}
</script>