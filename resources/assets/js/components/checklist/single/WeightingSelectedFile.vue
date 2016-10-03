<template>
    <div id="selected-file-weighting">

        <button v-if="user"
                type="button"
                class="btn btn-weighting"
                @click="showInput"
                :class="{
                    filled: this.fileRequest.weighting
                }"
        >
            <span class="icon">%</span>
            <span class="weighting" v-show="! inputVisible">
                <span v-if="fileRequest.weighting">{{ fileRequest.weighting }}</span>
                <span v-else>Weighting</span>
            </span>
            <input v-else
                   type="number"
                   ref="input"
                   step="0.01"
                   v-model="input"
                   @blur="blurInput"
            >
        </button>

        <div class="uneditable" v-else>
            <span class="icon">%</span>
            <div class="weighting">
                <span v-if="fileRequest.weighting">{{ fileRequest.weighting }}</span>
                <span v-else>--</span>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                inputVisible: false,
                input: ''
            }
        },
        props: ['user', 'file-request', 'index'],
        methods: {
            showInput() {
                this.inputVisible = true;
                this.$nextTick(() => $(this.$refs.input).focus());
            },
            hideInput() {
                this.inputVisible = false;
            },
            blurInput() {
                this.input = this.input || null;
                if (this.input !== this.fileRequest.weighting) {
                    this.saveWeighting();
                } else {
                    this.hideInput();
                }
            },
            saveWeighting(){

                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.put('/fr/' + this.fileRequest.hash, {
                    weighting: this.input
                }).then((response) => {
                    this.$emit('update-file-request', response.json(), this.index);
                    this.hideInput();
                    this.ajaxReady = true;
                    vueGlobalEventBus.$emit('updated-weighting');
                }, (response) => {
                    console.log('Got request error');
                    console.log(response);
                    this.ajaxReady = true;
                });
            }
        },
        watch: {
            fileRequest(val) {
                this.input = val.weighting;
            }
        },
        mounted(){
            this.input = this.fileRequest.weighting;
        }
    }
</script>