$(function() {

    $comments = $('.comments');

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

});
