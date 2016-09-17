<template>
    <div id="selected-file-weighting">


        <button type="button"
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
                   v-el:input
                   step="0.01"
                   v-model="input"
                   @blur="blurInput"
            >
        </button>


        <!--<button type="button"-->
        <!--class="btn"-->
        <!--:class="{-->
        <!--filled: this.fileRequest.weighting-->
        <!--}"-->
        <!--v-show="! inputVisible"-->
        <!--@click="showInput"-->
        <!--&gt;-->
        <!--<span v-if="fileRequest.weighting">{{ fileRequest.weighting }}</span>-->
        <!--<span v-else>%</span>-->
        <!--</button>-->
        <!--<input v-else-->
        <!--type="number"-->
        <!--v-el:input-->
        <!--step="0.01"-->
        <!--v-model="input"-->
        <!--@blur="blurInput"-->
        <!--&gt;-->
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
        props: ['file-request'],
        methods: {
            showInput() {
                this.inputVisible = true;
                this.$nextTick(() => $(this.$els.input).focus());
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
                    this.fileRequest = JSON.parse(response.data);
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
        ready() {
            this.input = this.fileRequest.weighting;
        }
    }
</script>