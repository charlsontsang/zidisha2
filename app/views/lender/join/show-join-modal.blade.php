<?php
    $showJoinModal = Session::get('showJoinModal');
?>

<div class="modal {{ $showJoinModal ? '' : 'fade' }}" id="join-modal" tabindex="0" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            @if($showJoinModal)
                @include('lender.join-modal')
            @endif
        </div>
    </div>
</div>

@if($showJoinModal)
    @section('script-footer')
    <script type="text/javascript">
        $(function() {
            $('#join-modal').modal();
        });
    </script>
    @append
@endif
