<template>
    <ul id="maker-files" class="list-files list-unstyled">
        <li v-for="(index, file) in files" class="single-file" :class="{ 'is-focused': focusedFileIndex === index }">

                    <span class="icon-file line-el">
                    <i class="fa fa-file"></i>
                    </span>

            <input type="text"
                   class="form-control input-file-name line-el"
                   v-model="file.name"
                   placeholder="File name"
                   :name="'files[' + index + '][name]'"
                   @keyup.enter="addFile"
                   @keydown.delete="removeFile($event)"
                   @keydown.up.prevent.stop="pressedArrow('prev')"
                   @keyup="searchForFileName($event, file, index)"
                   @keydown.down.prevent.stop="pressedArrow('next')"
                   @focus="focusNameInput(index, file)"
                   v-el:name-input
            >

            <ul class="list-name-options list-unstyled"
                v-show="focusedFileIndex === index && fileNameOptions.length > 0">
                <li v-for="(index, name) in fileNameOptions"
                    track-by="$index"
                    tabIndex="-1"
                    class="single-name"
                    @mouseover="selectOption(index)"
                    @click="addFile"
                    :class="{
                                    'is-selected': selectedOptionIndex === index
                                }"
                >
                    {{ name }}
                </li>
            </ul>

            <input type="text"
                   class="input-due line-el"
                   v-model="file.due"
                   v-datepicker
                   placeholder="Due date"
                   tabindex="-1"
                   :class="{'filled': file.due}"
                   :name="'files[' + index + '][due]'"
            >
        </li>
    </ul>
</template>
<script>
    let xhr_requests = [];

    export default {
        data: function () {
            return {
                ajaxReady: true,
                focusedFileIndex: '',
                selectedOptionIndex: '',
                fileNameOptions: []
            }
        },
        props: ['files'],
        methods: {
            selectOption: function (index) {
                this.selectedOptionIndex = index;
            },
            focusNameInput: function (index, file) {
                this.clearSearchRequests();
                this.focusedFileIndex = index;
            },
            blurInput: function () {
                this.clearSearchRequests();
                this.focusedFileIndex = '';
            },
            searchForFileName: function (event, file, index) {
                if (event.key.length === 1 || event.key === "Backspace") {
                    // Abort old unfinished requests
                    this.clearSearchRequests();
                    // input a character or backspaced
                    this.fetchFileNames(file.name, index);
                }
            },
            clearSearchRequests: function () {
                this.fileNameOptions = [];
                for (var i = 0; i < xhr_requests.length; i++) {
                    xhr_requests.shift().abort();
                }
            },
            fetchFileNames: _.debounce(function (searchString, index) {
                if (!searchString) return;
                this.$http.get('/files?name_only=1&limit=5&search=' + searchString, {
                    before: (xhr) => {
                        // Add current request to the queue
                        xhr_requests.push(xhr);
                    }
                })
                        .then((response) => {
                            // If we've moved on to a diff file, results useless
                            if (index !== this.focusedFileIndex) return;
                            // Success
                            let results = JSON.parse(response.data);
                            // If our search string isn't in results, we'll make it the first option
                            if (results.map((name) => name.toLowerCase()).indexOf(searchString.toLowerCase()) === -1) results.unshift(searchString);
                            // select first option
                            this.selectedOptionIndex = 0;
                            // append results
                            this.fileNameOptions = results;
                        });
            }, 50),
            addFile: function () {
                let selectedOption = this.fileNameOptions[this.selectedOptionIndex];
                let currentFileName = this.files[this.focusedFileIndex].name;

                if (!selectedOption && !currentFileName) return;

                if (selectedOption) this.files[this.focusedFileIndex].name = selectedOption;

                var newFile = {
                    name: '',
                    description: '',
                    due: ''
                };

                this.files.splice(this.focusedFileIndex + 1, 0, newFile);
                this.$nextTick(function () {
                    this.goTo('next');
                });
            },
            removeFile: function (event) {
                if (!this.files[this.focusedFileIndex].name && this.focusedFileIndex !== 0) {
                    this.files.splice(this.focusedFileIndex, 1);
                    this.$nextTick(function () {
                        event.preventDefault();
                        $($('.single-file')[this.focusedFileIndex - 1]).find('.input-file-name').focus();
                    });
                }
            },
            pressedArrow: function (direction) {
                let optionIndexToSelect = (direction === 'prev') ? this.selectedOptionIndex - 1 : this.selectedOptionIndex + 1;
                if (this.fileNameOptions && this.fileNameOptions[optionIndexToSelect]) {
                    this.selectOption(optionIndexToSelect);
                } else {
                    this.goTo(direction);
                }
            },
            pressedArrowUp: function () {
                if (this.fileNameOptions && this.fileNameOptions[this.selectedOptionIndex - 1]) {
                    // Have options and there is a next option - select it
                    this.selectOption(this.selectedOptionIndex - 1);
                } else {
                    this.goTo('prev');
                }
            },
            pressedArrowDown: function () {
                if (this.fileNameOptions && this.fileNameOptions[this.selectedOptionIndex + 1]) {
                    // Have options and there is a next option - select it
                    this.selectOption(this.selectedOptionIndex + 1);
                } else {
                    this.goTo('next');
                }
            },
            goTo: function (direction) {
                if (direction === 'prev') {
                    $($('.single-file')[this.focusedFileIndex - 1]).find('.input-file-name').focus();
                } else {
                    $($('.single-file')[this.focusedFileIndex + 1]).find('.input-file-name').focus();
                }
            }
        },
        ready: function () {

            // If we click outside the list of files, blur our input
            $(document).on('click', (e) => {
                let container = $('#maker-files');
                if (!$(container).is(e.target) && $(container).has(e.target).length === 0) {
                    this.blurInput();
                }
            });
        }
    };
</script>