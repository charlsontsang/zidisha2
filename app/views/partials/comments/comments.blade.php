<div class="comments">
    @include('partials.comments.partial.post')
    <ul class="list-unstyled">
        @foreach($comments as $comment)
        @include("partials.comments.root", ['comment' => $comment, 'commentType'=> $commentType ])
        @endforeach
    </ul>
</div>

<script type="text/html" id="comment-upload-input-template">
    {{ BootstrapForm::file('file[]', ['label' => 'comments.upload-file']) }}
</script>
