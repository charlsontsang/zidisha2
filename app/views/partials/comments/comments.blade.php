<ul class="list-unstyled comments">
    @foreach($comments as $comment)
        @include("partials.comments.root", ['comment' => $comment])
    @endforeach
</ul>
