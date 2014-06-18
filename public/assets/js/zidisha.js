$(function() {

    var $comments = $('.comments');

    $comments.on('click', '.comment-action', function(){
        var $this = $(this),
            $forms = $this.closest('.comment').find('.comment-forms');

        $forms.find('.comment-form').hide();
        $forms.find('[data-comment-action=' + $this.attr('target') + ']').show();

        return false;
    });

    $comments.on('click', '.comment-share', function() {
        $(this).next().toggle();
        return false;
    });

    $comments.on('click', '.comment-original-message', function() {
        $(this).closest('p').next().toggle();
        return false;
    });

    var commentUploadTemplate = $('#comment-upload-input-template').html();

    $comments.on('click', '.comment-upload-add-more', function(){
        $(this).closest('.comment').find('.comment-upload-inputs').append($(commentUploadTemplate));
        return false;
    });

    var $borrowerEditForm = $('.borrower-edit-form');

    var borrowerUploadTemplate = $('#borrower-upload-input-template').html();
    $borrowerEditForm.on('click', '.borrower-upload-add-more', function(){
        $borrowerEditForm.find('.borrower-upload-inputs').prepend($(borrowerUploadTemplate));
        return false;
    });
});
