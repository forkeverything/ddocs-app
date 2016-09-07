module.exports = {
    name: 'replacesFileFromFilesArray',
    methods: {
        replaceFile: function (updatedFileModel) {
            if(typeof updatedFileModel !== 'object') throw new Error('Invalid object when trying to update file model.');
            var self = this;
            var index = _.indexOf(self.files, _.find(self.files, {id: updatedFileModel.id}));
            self.$set('files[' + index + ']', updatedFileModel);
        }
    }
};