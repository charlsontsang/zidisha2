<div class="modal fade {{ $modalClass or '' }}" id="{{ $id or ''}}">
    <div class="modal-dialog">
        <div class="modal-content">
            @if($title)
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{ $title }}</h4>
            </div>
            @endif
            <div class="modal-body" @if(!empty($scrollable))style="height: 400px;overflow-y: scroll"@endif>
                @if(isset($body))
                    <p>{{ $body }}</p>
                @else
                    @include($template)
                @endif
            </div>
            @if (isset($footer))
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            @endif
        </div>
    </div>
</div>