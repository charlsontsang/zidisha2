@if (Session::has('flash_notifications'))
    @foreach(Session::get('flash_notifications') as $notification)
        @if ($notification['type'] == 'modal')
            @include('partials/modal', ['modalClass' => 'flash-modal', 'title' => $notification['title'], 'body' => $notification['message']])
        @else
            @include('partials/alert', ['level' => $notification['level'], 'message' => $notification['message']])
        @endif
    @endforeach
@endif