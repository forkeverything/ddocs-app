<!-- Reject -->
<button type="button" class="btn btn-unstyled button-reject" @click="expandFileSection(file, 'reject')" :disabled="file.status !== 'received'">
<i class="fa fa-close"></i>
</button>