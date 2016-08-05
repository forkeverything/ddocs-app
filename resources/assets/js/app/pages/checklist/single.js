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