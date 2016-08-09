<!-- History -->
<button type="button" class="btn btn-unstyled button-history" @click="expandFileSection(file, 'history')" :disabled="! file.uploads[0]">
<i class="fa fa-clock-o"></i>
</button>