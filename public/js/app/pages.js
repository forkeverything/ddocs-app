Vue.component('checklist-make', {
    name: 'checklistMake',
    el: function() {
        return '#checklist-make'
    },
    data: function() {
        return {
            ajaxReady: true,
            checklistRecipient: '',
            checklistName: '',
            checklistDescription: '',
            editingName: false,
            files: [
                {
                    name: '',
                    description: '',
                    due: '',
                    required: 1
                }
            ]
        };
    },
    props: [],
    computed: {
        checklistNameText: function() {
            // Used to display a default when a name isn't given
            if(this.checklistName) return this.checklistName;
            return 'List Name';
        },
        validFiles: function() {
            // Only return files with names
            return _.filter(this.files, function (file) {
                return file.name;
            });
        },
        fileCount: function() {
           return this.validFiles.length;
        },
        canSendChecklist: function() {
            // Required fields...
            return this.checklistRecipient && this.checklistName && this.fileCount > 0;
        }
    },
    methods: {
        toggleEditingName: function() {
            this.editingName = ! this.editingName;
            if(this.editingName) {
                this.$nextTick(function() {
                    $('#input-checklist-name').focus();
                });
            }
        },
        addAnotherFileAfter: function(file) {
            var fileIndex = _.indexOf(this.files, file);
            var newFile = {
                name: '',
                description: '',
                due: '',
                required: 1
            };
            this.files.splice(fileIndex + 1, 0, newFile);
            this.$nextTick(function () {
                $($('.single-file')[fileIndex + 1]).find('.input-file-name').focus();
            });
        },
        removeFile: function(file) {
            var fileIndex = _.indexOf(this.files, file);
            if(! file.name && fileIndex !== 0) {
                this.files.splice(fileIndex, 1);
                this.$nextTick(function () {
                    $($('.single-file')[fileIndex - 1]).find('.input-file-name').focus();
                });
            }
        },
        goTo: function(direction, file) {
            var fileIndex = _.indexOf(this.files, file);
            if(direction === 'prev') {
                $($('.single-file')[fileIndex - 1]).find('.input-file-name').focus();
            } else {
                $($('.single-file')[fileIndex + 1]).find('.input-file-name').focus();
            }
        },
        toggleRequired: function(file) {
            file.required = file.required ? 0 : 1;
        },
        sendChecklist: function() {
            var self = this;
            if(!self.ajaxReady) return;
            self.ajaxReady = false;
            $.ajax({
                url: '/checklist/make',
                method: 'POST',
                data: {
                    recipient: self.checklistRecipient,
                    name: self.checklistName,
                    description: self.checklistDescription,
                    requested_files: self.validFiles
                },
                success: function(data) {
                   location.href = "/checklist/" + data;
                },
                error: function(response) {
                    console.log(response);
                    self.ajaxReady = true;
                }
            });
        }
    },
    events: {

    },
    ready: function() {
        $(document).on('focus', '.input-file-name', function(){
            $(this).parent().addClass('is-focused');
        });

        $(document).on('blur', '.input-file-name', function(){
            $(this).parent().removeClass('is-focused');
        });
    }
});
Vue.component('checklist-single', fetchesFromEloquentRepository.extend({
    name: 'checklistSingle',
    el: function () {
        return '#checklist-single'
    },
    data: function () {
        return {
            ajaxReady: true,
            hasFilters: true,
            filterOptions: [
                {
                    value: 'required',
                    label: 'Requirement'
                },
                {
                    value: 'version',
                    label: 'Version'
                },
                {
                    value: 'due',
                    label: 'Due Date'
                },
                {
                    value: 'status',
                    label: 'Status'
                }
            ],
            files: []
        };
    },
    props: ['checklist-hash'],
    computed: {
        requestUrl: function () {
            return '/checklist/' + this.checklistHash + '/files';
        }
    },
    methods: {
        uploadFile: function (file, $event) {
            var fd = new FormData();
            var uploadedFile = $event.srcElement.files[0];

            fd.append('file', uploadedFile);

            var self = this;
            if (!self.ajaxReady) return;
            self.ajaxReady = false;
            $.ajax({
                url: '/checklist/' + self.checklistHash + '/file/' + file.id,
                method: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                xhr: function () {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) myXhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            var max = e.total;
                            var current = e.loaded;
                            var progress = Math.round((current * 100) / max);
                            $('#input-file-' + file.id).siblings('.progress').children('.progress-bar').css(
                                'width',
                                progress + '%'
                            ).attr('aria-valuenow', progress).children('.sr-only').html(progress + '%');
                        }
                    }, false);
                    return myXhr;
                },
                success: function (updatedFileModel) {
                    var index = _.indexOf(self.files, _.find(self.files, {id: file.id}));
                    self.files.splice(index, 1, updatedFileModel);
                    self.ajaxReady = true;
                },
                error: function (response) {
                    console.log(response);
                    self.ajaxReady = true;
                }
            });
        },
        updateProgress: function (e) {
            if (e.lengthComputable) {
                var max = e.total;
                var current = e.loaded;

                var Percentage = Math.round((current * 100) / max);
                console.log(Percentage);


                if (Percentage >= 100) {
                    // process completed
                }
            }
        }
    },
    events: {},
    ready: function () {

        this.$watch('response', function (response) {
            this.files = $.map(_.omit(response.data, 'query_parameters'), function (value, index) {
                return [value];
            });

        }.bind(this));

        $(document).on('click', '.button-upload-file', function (e) {
            e.preventDefault();
            $('#input-file-' + $(this).data('file')).click();
        });
    }
}));
//# sourceMappingURL=pages.js.map
