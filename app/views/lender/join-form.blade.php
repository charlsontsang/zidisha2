<a href="{{ $facebookJoinUrl }}" class="btn btn-facebook btn-block btn-icon">
    <span class="icon-container">
        <span class="fa fa-facebook fa-lg fa-fw"></span>
    </span>
    <span class="text-container">
         Join with Facebook
    </span>
</a>

<a href="{{ $googleJoinUrl }}" class="btn btn-default btn-google btn-block btn-icon">
    <span class="icon-container">
        <span class="fa fa-google-plus fa-lg fa-fw"></span>
    </span>
    <span class="text-container">
         Join with Google
    </span>
</a>

<div class="hr-text">
    <hr>
    <span>or</span>
</div>

<button type="button" class="btn btn-primary btn-icon btn-block" target="#join-with-email" data-display="display" {{ $errors->count() ? 'style="display:none"' : '' }}>
    <span class="icon-container">
        <span class="fa fa-envelope-o fa-lg fa-fw"></span>
    </span>
    <span class="text-container">
        Join with Email
    </span>
</button>

<div id="join-with-email" {{ $errors->count() ? ''  : 'style="display:none"' }}>
    {{ BootstrapForm::open([
        'route' => 'lender:post-join',
        'translationDomain' => 'lender.join.form',
        'id' => 'joinForm']
    ) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::text('username', null, [
        'label'         => false,
        'placeholder'   => \Lang::get('lender.join.form.username'),
        'sr-only'       => \Lang::get('lender.join.form.username'),
        'feedback-icon' => 'fa-user',
    ]) }}

    {{ BootstrapForm::text('email', null, [
        'label'         => false,
        'placeholder'   => \Lang::get('lender.join.form.email'),
        'sr-only'       => \Lang::get('lender.join.form.email'),
        'feedback-text' => '@',
    ]) }}

    {{ BootstrapForm::password('password', [
        'label'         => false,
        'placeholder'   => \Lang::get('lender.join.form.password'),
        'sr-only'       => \Lang::get('lender.join.form.password'),
        'feedback-icon' => 'fa-lock',
    ]) }}

    {{ BootstrapForm::select('countryId', $form->getCountries()->toKeyValue('id', 'name'), $country['id'], [
        'label' => false,
        'sr-only' => \Lang::get('lender.join.form.country-id'),
    ]) }}

    <p>By signing up, I agree to Zidisha's <a target="_blank" href="{{ route('page:terms-of-use') }}">Terms of Use
    and Privacy Policy</a>.</p>


    {{ BootstrapForm::submit('submit', ['class' => 'btn btn-primary btn-block']) }}

    {{ BootstrapForm::close() }}
</div>

<hr/>

Already a member?  <strong><a href="{{ route('login') }}" data-toggle="modal" data-target="#login-modal" data-dismiss="modal">Log In</a></strong>

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function () {
        $('#join-lend').on('click', function() {
            $.get("{{ route('lender:join') }}");
        });
    });
</script>
@stop
