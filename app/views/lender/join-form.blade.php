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

    {{ BootstrapForm::password('password_confirmation', [
        'label'         => false,
        'placeholder'   => \Lang::get('lender.join.form.password-confirmation'),
        'sr-only'       => \Lang::get('lender.join.form.password-confirmation'),
        'feedback-icon' => 'fa-lock',
    ]) }}

    {{ BootstrapForm::select('countryId', $form->getCountries()->toKeyValue('id', 'name'), $country['id'], [
        'label' => false,
        'sr-only' => \Lang::get('lender.join.form.country-id'),
    ]) }}

    I agree to Zidisha's <a target="_blank" href="#">Terms of Use</a>
    and <a target="_blank" href="http://www.iubenda.com/privacy-policy/629677/legal">Privacy Policy</a>.


    {{ BootstrapForm::submit('submit', ['class' => 'btn btn-primary btn-block']) }}

    {{ BootstrapForm::close() }}
</div>

<hr/>

Already a member?  <strong>{{ link_to_route('login', 'Log in' ) }}</strong>