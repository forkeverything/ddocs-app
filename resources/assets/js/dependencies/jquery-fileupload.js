$(document).ready(function () {

    // Initialize jQuery File Upload
    $('.input-file-upload').fileupload({
        dataType: 'json',
        done: function (e, data) {
            console.log(data);
            // $.each(data.result.files, function (index, file) {
            //     $('<p/>').text(file.name).appendTo(document.body);
            // });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);

            $(this).siblings('.progress').children('.progress-bar').css(
                'width',
                progress + '%'
            ).attr('aria-valuenow', progress).children('.sr-only').html(progress + '%');
        }
    });

    // Bind buttons to trigger file input
    $(document).on('click', '.button-upload-file', function(e) {
        e.preventDefault();
        $('#input-file-' + $(this).data('file')).click();
    });
});