<?php $joinForm = App::make('\Zidisha\Lender\Form\JoinForm') ?>

<a href="{{ $joinForm->getFacebookJoinUrl() }}" class="btn btn-facebook btn-block btn-icon">
    <span class="icon-container">
        <span class="fa fa-facebook fa-lg fa-fw"></span>
    </span>
    <span class="text-container">
         Join with Facebook
    </span>
</a>

<a href="{{ $joinForm->getGoogleJoinUrl() }}" class="btn btn-default btn-google btn-block btn-icon">
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
        'id' => 'joinForm']
    ) }}
    {{ BootstrapForm::populate($joinForm) }}

    {{ BootstrapForm::text('username', null, [
        'label'         => false,
        'placeholder'   => 'Your name',
        'sr-only'       => 'Your name',
        'feedback-icon' => 'fa-user',
    ]) }}

    {{ BootstrapForm::text('email', null, [
        'label'         => false,
        'placeholder'   => 'Your email',
        'sr-only'       => 'Your email',
        'feedback-text' => '@',
    ]) }}

    {{ BootstrapForm::password('password', [
        'label'         => false,
        'placeholder'   => 'Create password',
        'sr-only'       => 'Create password',
        'feedback-icon' => 'fa-lock',
    ]) }}

    {{ BootstrapForm::select('countryId', $joinForm->getCountries()->toKeyValue('id', 'name'), null, [
        'label' => false,
        'sr-only' => 'Your country',
    ]) }}

    <p>By signing up, I agree to Zidisha's <a target="_blank" href="{{ route('page:terms-of-use') }}">Terms of Use
    and Privacy Policy</a>.</p>

    @if(isset($modal) && $modal)
        {{ BootstrapForm::hidden('modal', 1) }}
    @endif

    {{ BootstrapForm::submit('Join', ['class' => 'btn btn-primary btn-block']) }}

    {{ BootstrapForm::close() }}
</div>

<hr/>

Already a member?&nbsp;&nbsp;

<strong>     

    @if(isset($modal) && $modal)
        <a href="{{ route('login') }}" data-toggle="modal" data-target="#login-modal" data-dismiss="modal">Log In</a>
    @else
        <a href="{{ route('login') }}">Log In</a>
    @endif

</strong>
