module.exports = {
    name: 'replacesFileFromFilesArray',
    methods: {
        replaceFile: function (updatedFileModel) {
            var self = this;
            var index = _.indexOf(self.files, _.find(self.files, {id: updatedFileModel.id}));
            self.$set('files[' + index + ']', updatedFileModel);
        }
    }
};