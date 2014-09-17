<div class="panel-default login-form">

    <?php $loginForm = App::make('\Zidisha\Auth\Form\LoginForm') ?>

    {{ BootstrapForm::open(array('action' => 'AuthController@postLogin')) }}
    {{ BootstrapForm::populate($loginForm) }}

    <a href="{{ $loginForm->getFacebookLoginUrl() }}" class="btn btn-facebook btn-block btn-icon">
        <span class="icon-container">
            <span class="fa fa-facebook fa-lg fa-fw"></span>
        </span>
        <span class="text-container">
             @lang('borrower.login.facebook-login')
        </span>
    </a>

    <a href="{{ $loginForm->getGoogleLoginUrl() }}" class="btn btn-default btn-google btn-block btn-icon">
        <span class="icon-container">
            <span class="fa fa-google-plus fa-lg fa-fw"></span>
        </span>
        <span class="text-container">
             @lang('borrower.login.google-login')
        </span>
    </a>

    <div class="hr-text">
        <hr>
        <span>or</span>
    </div>

    <div class="form-group has-feedback">
        <div class="sr-only">@lang('borrower.login.form.email')</div>
        {{ BootstrapForm::text('email', null, [
            'class'       => 'form-control',
            'placeholder' => Lang::get('borrower.login.form.email'),
            'label' => false,
        ]) }}
        <span class="fa-lg form-control-feedback text-muted" style="top: 0;">@</span>
    </div>

    <div class="form-group has-feedback">
        <div class="sr-only">@lang('borrower.login.form.password')</div>
        {{ BootstrapForm::password('password', [
            'class'       => 'form-control',
            'placeholder' => Lang::get('borrower.login.form.password'),
            'label' => false,
        ]) }}
        <span class="fa fa-lock fa-lg form-control-feedback text-muted" style="top: 0;"></span>
    </div>

    <div class="row">
        <div class="col-xs-6">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember_me">
                    @lang('borrower.login.form.remember-me')
                </label>
            </div>
        </div>
        <div class="col-xs-6">
            <p class="pull-right">
                <a href="{{ action('RemindersController@getRemind') }}">
                    @lang('borrower.login.form.forget-password')
                </a>
            </p>
        </div>
    </div>

    @if(isset($modal) && $modal)
        {{ BootstrapForm::hidden('modal', 1) }}
    @endif

    <input class="btn btn-primary btn-block" type="submit" value="Log in"/>
    {{ BootstrapForm::close() }}
    @lang('borrower.login.not-a-member')  <strong><a href="{{ route('join') }}" data-toggle="modal" data-target="#join-modal" data-dismiss="modal">@lang('borrower.login.join')</a></strong>

</div>
