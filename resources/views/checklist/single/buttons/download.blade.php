<!-- Download -->
<a :href="'/' + file.uploads[0].path" :alt="file.name + 'download link'" :download="file.name + '_v' + file.version + '_' + getUploadDate(file.uploads[0])"
   v-if="file.uploads[0]">
    <button type="button" class="btn btn-unstyled button-download"><i class="fa fa-download"></i>
    </button>
</a>
<button type="button" class="btn btn-unstyled button-download" v-else disabled><i
            class="fa fa-download"></i></button>