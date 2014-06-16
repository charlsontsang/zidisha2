<ul class="list-unstyled">
    @foreach($comments as $comment)
        @include("partials.comments.root", ['comment' => $comment])
    @endforeach
</ul>
