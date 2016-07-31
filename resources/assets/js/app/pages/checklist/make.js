Vue.component('checklist-make', {
    name: 'checklistMake',
    el: function() {
        return '#checklist-make'
    },
    data: function() {
        return {
            checklistRecipient: '',
            checklistName: '',
            checklistDescription: '',
            editingName: false,
            files: [
                {
                    name: '',
                    description: '',
                    due: ''
                }
            ]
        };
    },
    props: [],
    computed: {
        checklistNameText: function() {
            if(this.checklistName) return this.checklistName;
            return 'List Name';
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
                due: ''
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
        addFocus: function() {

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