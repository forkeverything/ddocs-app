<!-- Upload -->
<button type="button" class="btn btn-unstyled button-upload"
        data-file="@{{ file.id }}"
        :disabled="file.status === 'received'"
>
    <i class="fa fa-upload"></i>
</button>