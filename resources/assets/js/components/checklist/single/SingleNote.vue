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
                          v-model="note.body"
                          :style="{ height: textAreaHeight }"
                          @focus="setFocus"
                          @blur="blurInput"
                          @keydown.enter.prevent="addNoteAfterThisOne"
                          @keydown.delete="removeThisNote($event)"
                          @keydown.up.prevent.stop="pressedArrow('prev')"
                          @keydown.down.prevent.stop="pressedArrow('next')"
                ></textarea>
                <div class="sizer" v-el:sizer>{{ note.body }}</div>
            </div>
        </td>
    </tr>
</template>
<script>
export default {
    data: function(){
        return {
            ajaxReady: true,
            textAreaHeight: '25px',
            previousBody: ''
        }
    },
    computed: {
      focused() {
          return this.focusedIndex === this.index;
      }
    },
    watch: {
        index() {
            this.updateExistingNote();
        },
        'note.body'() {
            this.setTextAreaHeight();
        },
        'note.checked'() {
            this.updateExistingNote();
        }
    },
    props: ['index', 'focused-index', 'note', 'file-request-hash'],
    methods: {
        toggleChecked() {
            this.note.checked = !this.note.checked;
        },
        setTextAreaHeight() {
            this.textAreaHeight = $(this.$els.sizer).height() + 'px';
        },
        setFocus() {
            this.focusedIndex = this.index;
        },
        blurInput() {
            this.focusedIndex = '';
            this.updateExistingNote();
        },
        addNoteAfterThisOne() {
            this.updateExistingNote();
            vueGlobalEventBus.$emit('add-new-note', this.index);
        },
        removeThisNote(event) {
            if(this.note.body) return;
            vueGlobalEventBus.$emit('delete-note', {
                index: this.index,
                event: event
            });
        },
        updateExistingNote(){
            if (!this.ajaxReady) return;
            this.ajaxReady = false;
            this.$http.put('/note/' + this.note.id, {
                'position': this.index,
                'body': this.note.body,
                'checked': this.note.checked
            }).then((response) => {
                // success
                console.log('updated note!');
                this.previousBody = this.note.body;
                this.ajaxReady = true;
            }, (response) => {
                // error
                console.log(response);
                this.ajaxReady = true;
            });
        },
        pressedArrow: function (direction) {
            let indexToFocus = (direction === 'prev') ? this.index - 1 : this.index + 1;
            vueGlobalEventBus.$emit('focus-note', indexToFocus);
        }
    },
    ready() {
        this.previousBody = this.note.body;
        this.setTextAreaHeight();
    }
}
</script>