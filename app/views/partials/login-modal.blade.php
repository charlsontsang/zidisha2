<?php
    $showLoginModal = Session::get('showLoginModal');
?>

<div class="modal {{ $showLoginModal ? '' : 'fade' }}" id="login-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            @if($showLoginModal)
                @include('auth.login-modal')
            @endif
        </div>
    </div>
</div>

@if($showLoginModal)
    @section('script-footer')
    <script type="text/javascript">
        $(function() {
            console.log('pol');
            $('#login-modal').modal();
        });
    </script>
    @append
@endif
