@extends('layouts.master')

@section('page-title')
Gift Cards
@stop

@section('content')
</div> <!-- /container -->
<div class="container-fluid highlight highlight-top">
    <div class="container">
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
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-6">

            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Step One: Choose An Image
                    </h3>
                </div>
                <div class="panel-body">
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
                            <div id="giftcard-5-1" class="giftcard-thumbnail">
                                <label for="giftcard_template_radio-5-1" ><img src="{{ asset('assets/images/gift-card/image5.png'); }}"/></label>
                                <input name="template" id="giftcard_template_radio-5-1" value="5" type="radio">
                            </div>
                            <div id="giftcard-7-1" class="giftcard-thumbnail">
                                <label for="giftcard_template_radio-7-1" ><img src="{{ asset('assets/images/gift-card/image7.png'); }}"/></label>
                                <input name="template" id="giftcard_template_radio-7-1" value="7" type="radio">
                            </div>
                            <div id="giftcard-6-1" class="giftcard-thumbnail">
                                <label for="giftcard_template_radio-6-1" ><img src="{{ asset('assets/images/gift-card/image6.png'); }}"/></label>
                                <input name="template" id="giftcard_template_radio-6-1" value="6" type="radio">
                            </div>
                            <div id="giftcard-9-1" class="giftcard-thumbnail">
                                <label for="giftcard_template_radio-9-1" ><img src="{{ asset('assets/images/gift-card/image9.png'); }}"/></label>
                                <input name="template" id="giftcard_template_radio-9-1" value="9" type="radio">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">

            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Step Two: Customize Your Card
                    </h3>
                </div>
                <div class="panel-body">
                    {{ BootstrapForm::select('amount', $form->getAmounts(), null, ['label' => 'Amount']) }}

                    {{ BootstrapForm::label('Delivery Method:') }}

                    {{ BootstrapForm::radio('orderType', 'Email', null, [
                        'label' => 'Email', 'id' => 'email'
                    ]) }}

                    {{ BootstrapForm::radio('orderType', 'Self-Print', null, [
                        'label' => 'Self-Print', 'id' => 'print'
                    ]) }}

                    {{ BootstrapForm::text('recipientEmail', null, ['label' => 'Recipient Email', 'id' => 'recipient']) }}

                    <br/>
                    <strong>Optional Fields</strong>
                  
                    {{ BootstrapForm::text('recipientName', null, ['label' => 'To']) }}

                    {{ BootstrapForm::text('fromName', null, ['label' => 'From']) }}

                    {{ BootstrapForm::textarea('message', null, ['label' => 'Add a Note', 'style' => 'height: 150px;']) }}

                    {{ BootstrapForm::text('confirmationEmail', null, ['label' => 'Your Email (for purchase confirmation)']) }}

                    {{ BootstrapForm::submit('Next >>') }}

                    {{ BootstrapForm::close() }}
                </div>
            </div>
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
<script type="text/javascript">
    $(document).ready(function() {
        $('#print').click(function () {
            $('#recipient').parents('.form-group').hide();
        });
        $('#email').click(function () {
            $('#recipient').parents('.form-group').show();
        });
    });
</script>
@stop