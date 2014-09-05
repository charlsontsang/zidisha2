@extends('layouts.master')

@section('page-title')
Gift Cards
@stop

@section('content')
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 text-center">
        <div class="page-header">
            <h1>Gift Cards</h1>
        </div>
        <p>
            Use a gift card to give a friend or loved one the freedom to choose whom they want to fund, plus the feel-good bonus that comes with being able to follow their chosen entrepreneur’s story and watch the business they seeded grow.  When the loan gets paid back, they can reinvest the funds in another small business and do it all over again.
        </p>

        {{ BootstrapForm::open(array('controller' => 'LenderController@postGiftCards')) }}
        {{ BootstrapForm::populate($form) }}
    </div>
</div>

</div> <!-- /container -->
<div class="container-fluid home-grey giftcard">

<div class="row">
    <div class="col-sm-6">
        <h4>Step One: Choose An Image</h4>

        <div class="row templates">
            <div class="col-sm-6">
                <div id="giftcard-4-1" class="giftcard-thumbnail">
                    <label for="giftcard_template_radio-4-1" ><img src="{{ asset('assets/images/gift-card/image4.png'); }}"></label>
                    <input name="template" id="giftcard_template_radio-4-1" value="4" checked="checked" type="radio">
                </div>
                <div id="giftcard-3-1" class="giftcard-thumbnail">
                    <label for="giftcard_template_radio-3-1" ><img src="{{ asset('assets/images/gift-card/image3.png'); }}"></label>
                    <input name="template" id="giftcard_template_radio-3-1" value="3" type="radio">
                </div>
                <div id="giftcard-2-1" class="giftcard-thumbnail">
                    <label for="giftcard_template_radio-2-1" ><img src="{{ asset('assets/images/gift-card/image2.png'); }}"/></label>
                    <input name="template" id="giftcard_template_radio-2-1" value="2" type="radio">
                </div>
                <div id="giftcard-8-1" class="giftcard-thumbnail">
                    <label for="giftcard_template_radio-8-1" ><img src="{{ asset('assets/images/gift-card/image8.png'); }}"/></label>
                    <input name="template" id="giftcard_template_radio-8-1" value="8" type="radio">
                </div>
            </div>
            <div class="col-sm-6">
                <div id="giftcard-6-1" class="giftcard-thumbnail">
                    <label for="giftcard_template_radio-6-1" ><img src="{{ asset('assets/images/gift-card/image6.png'); }}"/></label>
                    <input name="template" id="giftcard_template_radio-6-1" value="6" type="radio">
                </div>
                <div id="giftcard-7-1" class="giftcard-thumbnail">
                    <label for="giftcard_template_radio-7-1" ><img src="{{ asset('assets/images/gift-card/image7.png'); }}"/></label>
                    <input name="template" id="giftcard_template_radio-7-1" value="7" type="radio">
                </div>
                <div id="giftcard-5-1" class="giftcard-thumbnail">
                    <label for="giftcard_template_radio-5-1" ><img src="{{ asset('assets/images/gift-card/image5.png'); }}"/></label>
                    <input name="template" id="giftcard_template_radio-5-1" value="5" type="radio">
                </div>
                <div id="giftcard-9-1" class="giftcard-thumbnail">
                    <label for="giftcard_template_radio-9-1" ><img src="{{ asset('assets/images/gift-card/image9.png'); }}"/></label>
                    <input name="template" id="giftcard_template_radio-9-1" value="9" type="radio">
                </div>
            </div>
        </div>

        <!--
        {{ BootstrapForm::select('template', $form->getTemplates()) }}
        -->
    </div>
    <div class="col-sm-6">

        <h4>Step Two: Customize Your Card</h4>

        {{ BootstrapForm::select('amount', $form->getAmounts(), null, ['label' => 'Amount']) }}

        {{ BootstrapForm::select( 'orderType', $form->getOrderTypes(), null, ['label' => 'Delivery Method']) }}

        {{ BootstrapForm::text('recipientEmail', null, ['label' => 'Recipient Email']) }}

        {{ BootstrapForm::label('Optional Fields') }}
      
        {{ BootstrapForm::text('recipientName', null, ['label' => 'To']) }}

        {{ BootstrapForm::text('fromName', null, ['label' => 'From']) }}

        {{ BootstrapForm::textarea('message', null, ['label' => 'Add a Note', 'style' => 'height: 150px;']) }}

        {{ BootstrapForm::text('confirmationEmail', null, ['label' => 'Your Email (for purchase confirmation)']) }}

        {{ BootstrapForm::submit('Next >>') }}

        {{ BootstrapForm::close() }}
    </div>
</div>
@stop

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function() {
        $('.giftcard-thumbnail').on('click', function () {
            $(this).parents().parents('.templates').find('div.giftcard-thumbnail').removeClass('selected');
            $(this).addClass('selected');
        });
        $('div.giftcard-thumbnail:first').addClass('selected');
    });
</script>
@stop