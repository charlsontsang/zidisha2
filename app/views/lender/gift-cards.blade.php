@extends('layouts.master')

@section('page-title')
Gift Cards
@stop

@section('content')
<div class="page-header">
    <h1>Gift Cards</h1>
</div>
<div class="page-header">
    <p>{{ \Lang::get('text.gift-cards.gift-card-text') }}</p>
</div>

{{ BootstrapForm::open(array('controller' => 'LenderController@postGiftCards', 'translationDomain' =>
'lender.gift-cards')) }}
{{ BootstrapForm::populate($form) }}

<div style='margin-right: 20px; margin-left: 10px; margin-top:30px;font-size:20px'><strong>
        <br/><br/>Step One: Select An Image</strong>
</div>

<div id="form-1" class="row giftcard_row">
    <div class="col-sm-6">
        <div id="giftcard-4-1" class="giftcard_thumbnail">
            <label for="giftcard_template_radio-4-1" ><img src="{{ asset('assets/images/gift-card/image4.png'); }}"/></label>
            <input name="giftcard_template_radio-1" id="giftcard_template_radio-4-1" value="image4" checked="checked" type="radio">
        </div>
        <div id="giftcard-3-1" class="giftcard_thumbnail">
            <label for="giftcard_template_radio-3-1" ><img src="{{ asset('assets/images/gift-card/image3.png'); }}"/></label>
            <input name="giftcard_template_radio-1" id="giftcard_template_radio-3-1" value="image3" type="radio">
        </div>
        <div id="giftcard-2-1" class="giftcard_thumbnail">
            <label for="giftcard_template_radio-2-1" ><img src="{{ asset('assets/images/gift-card/image2.png'); }}"/></label>
            <input name="giftcard_template_radio-1" id="giftcard_template_radio-2-1" value="image2" type="radio">
        </div>
        <div id="giftcard-5-1" class="giftcard_thumbnail">
            <label for="giftcard_template_radio-5-1" ><img src="{{ asset('assets/images/gift-card/image5.png'); }}"/></label>
            <input name="giftcard_template_radio-1" id="giftcard_template_radio-5-1" value="image5" type="radio">
        </div>
    </div>
    <div class="col-sm-4">
        <div id="giftcard-6-1" class="giftcard_thumbnail">
            <label for="giftcard_template_radio-6-1" ><img src="{{ asset('assets/images/gift-card/image6.png'); }}"/></label>
            <input name="giftcard_template_radio-1" id="giftcard_template_radio-6-1" value="image6" type="radio">
        </div>
        <div id="giftcard-7-1" class="giftcard_thumbnail">
            <label for="giftcard_template_radio-7-1" ><img src="{{ asset('assets/images/gift-card/image7.png'); }}"/></label>
            <input name="giftcard_template_radio-1" id="giftcard_template_radio-7-1" value="image7" type="radio">
        </div>
        <div id="giftcard-8-1" class="giftcard_thumbnail">
            <label for="giftcard_template_radio-8-1" ><img src="{{ asset('assets/images/gift-card/image8.png'); }}"/></label>
            <input name="giftcard_template_radio-1" id="giftcard_template_radio-8-1" value="image8" type="radio">
        </div>
        <div id="giftcard-9-1" class="giftcard_thumbnail">
            <label for="giftcard_template_radio-9-1" ><img src="{{ asset('assets/images/gift-card/image9.png'); }}"/></label>
            <input name="giftcard_template_radio-1" id="giftcard_template_radio-9-1" value="image9" type="radio">
        </div>
    </div>
</div>


{{ BootstrapForm::select('template', $form->getTemplates()) }}
<br/><br/>


<div style='margin-right: 20px; margin-left: 10px; margin-top:30px;font-size:20px'><strong>
        <br/><br/>Step Two: Customize Gift Card</strong>
</div>
<br/>


{{ BootstrapForm::select('amount', $form->getAmounts()) }}

{{ BootstrapForm::select( 'orderType', $form->getOrderTypes()) }}

{{ BootstrapForm::text('recipientEmail') }}

<br/>
<br/>
{{ BootstrapForm::label('Optional Fields') }}
<br/> <br/>
{{ BootstrapForm::text('recipientName') }}

{{ BootstrapForm::text('fromName') }}

{{ BootstrapForm::textarea('message') }}

{{ BootstrapForm::text('confirmationEmail') }}

{{ BootstrapForm::submit('save') }}

{{ BootstrapForm::close() }}

@stop

@section('script-footer')
<script type="text/javascript">$(document).ready(function() {
    $(".giftcard_thumbnail label").live("click", function () {
    $(this).parents("div").parents(".giftcard_row").find(".giftcard_thumbnail").removeClass("selected");
    $(this).parents("div").toggleClass("selected");
});
    $(".giftcard_thumbnail:first").addClass("selected");
});</script>
@stop