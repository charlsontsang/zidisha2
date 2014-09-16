@extends('layouts.master')

@section('page-title')
{{ \Lang::get('common.faqs.heading') }}
@stop


@section('content')
<div class="row" class="bs-docs-sidebar">
    <div class="col-xs-12 page-header">
        <h1>{{ \Lang::get('common.faqs.heading') }}</h1>
    </div>

    <div class="sidebar col-sm-4 pull-right">
        <div class="sidenav hidden-print hidden-xs affix-top" role="complementary">
            <ul class="nav sidenav-first-level">
                <li class="active"><h4><a href="#general-faqs">{{ \Lang::get('common.faqs.basic') }}</a></h4>
                    <ul class="nav">
                        <li><a href="#faq-1">{{ \Lang::get('common.faqs.question1') }}</a></li>
                        <li><a href="#faq-2">{{ \Lang::get('common.faqs.question2') }}</a></li>
                        <li><a href="#faq-4">{{ \Lang::get('common.faqs.question4') }}</a></li>
                        <li><a href="#faq-5">{{ \Lang::get('common.faqs.question5') }}</a></li>
                        <li><a href="#faq-3">{{ \Lang::get('common.faqs.question3') }}</a></li>
                        <li><a href="#faq-59">{{ \Lang::get('common.faqs.question59') }}</a></li>
                        <li><a href="#faq-47">{{ \Lang::get('common.faqs.question47') }}</a></li>
                        <li><a href="#faq-6">{{ \Lang::get('common.faqs.question6') }}</a></li>
                        <li><a href="#faq-52">{{ \Lang::get('common.faqs.question52') }}</a></li>
                        <li><a href="#faq-7">{{ \Lang::get('common.faqs.question7') }}</a></li>
                        <li><a href="#faq-9">{{ \Lang::get('common.faqs.question9') }}</a></li>
                        <li><a href="#faq-10">{{ \Lang::get('common.faqs.question10') }}</a></li>
                        <li><a href="#faq-11">{{ \Lang::get('common.faqs.question11') }}</a></li>
                        <li><a href="#faq-12">{{ \Lang::get('common.faqs.question12') }}</a></li>
                        <li><a href="#faq-13">{{ \Lang::get('common.faqs.question13') }}</a></li>
                        <li><a href="#faq-15">{{ \Lang::get('common.faqs.question15') }}</a></li>
                        <li><a href="#faq-63">{{ \Lang::get('common.faqs.question63') }}</a></li>
                        <li><a href="#faq-49">{{ \Lang::get('common.faqs.question49') }}</a></li>
                        <li><a href="#faq-16">{{ \Lang::get('common.faqs.question16') }}</a></li>
                        <li><a href="#faq-17">{{ \Lang::get('common.faqs.question17') }}</a></li>
                        <li><a href="#faq-46">{{ \Lang::get('common.faqs.question46') }}</a></li>
                        <li><a href="#faq-51">{{ \Lang::get('common.faqs.question51') }}</a></li>
                    </ul>
                </li>
                <li><h4><a href="#lender-faqs">{{ \Lang::get('common.faqs.lenders') }}</a></h4>
                    <ul class="nav">
                        <li><a href="#faq-53">{{ \Lang::get('common.faqs.question53') }}</a></li>
                        <li><a href="#faq-45">{{ \Lang::get('common.faqs.question45') }}</a></li>
                        <li><a href="#faq-44">{{ \Lang::get('common.faqs.question44') }}</a></li>
                        <li><a href="#faq-43">{{ \Lang::get('common.faqs.question43') }}</a></li>
                        <li><a href="#faq-19">{{ \Lang::get('common.faqs.question19') }}</a></li>
                        <li><a href="#faq-21">{{ \Lang::get('common.faqs.question21') }}</a></li>
                        <li><a href="#faq-18">{{ \Lang::get('common.faqs.question18') }}</a></li>
                        <li><a href="#faq-60">{{ \Lang::get('common.faqs.question60') }}</a></li>
                        <li><a href="#faq-20">{{ \Lang::get('common.faqs.question20') }}</a></li>
                        <li><a href="#faq-22">{{ \Lang::get('common.faqs.question22') }}</a></li>
                        <li><a href="#faq-24">{{ \Lang::get('common.faqs.question24') }}</a></li>
                        <li><a href="#faq-61">{{ \Lang::get('common.faqs.question61') }}</a></li>
                        <li><a href="#faq-62">{{ \Lang::get('common.faqs.question62') }}</a></li>
                        <li><a href="#faq-14">{{ \Lang::get('common.faqs.question14') }}</a></li>
                        <li><a href="#faq-40">{{ \Lang::get('common.faqs.question40') }}</a></li>
                        <li><a href="#faq-41">{{ \Lang::get('common.faqs.question41') }}</a></li>
                        <li><a href="#faq-42">{{ \Lang::get('common.faqs.question42') }}</a></li>
                        <li><a href="#faq-23">{{ \Lang::get('common.faqs.question23') }}</a></li>
                        <li><a href="#faq-48">{{ \Lang::get('common.faqs.question48') }}</a></li>
                    </ul>
                </li>
                <li><h4><a href="#borrower-faqs">{{ \Lang::get('common.faqs.borrower') }}</a></h4>
                    <ul class="nav">
                        <li><a href="#faq-25">{{ \Lang::get('common.faqs.question25') }}</a></li>
                        <li><a href="#faq-50">{{ \Lang::get('common.faqs.question50') }}</a></li>
                        <li><a href="#faq-26">{{ \Lang::get('common.faqs.question26') }}</a></li>
                        <li><a href="#faq-27">{{ \Lang::get('common.faqs.question27') }}</a></li>
                        <li><a href="#faq-28">{{ \Lang::get('common.faqs.question28') }}</a></li>
                        <li><a href="#faq-29">{{ \Lang::get('common.faqs.question29') }}</a></li>
                        <li><a href="#faq-36">{{ \Lang::get('common.faqs.question36') }}</a></li>
                        <li><a href="#faq-30">{{ \Lang::get('common.faqs.question30') }}</a></li>
                        <li><a href="#faq-56">{{ \Lang::get('common.faqs.question56') }}</a></li>
                        <li><a href="#faq-38">{{ \Lang::get('common.faqs.question38') }}</a></li>
                        <li><a href="#faq-37">{{ \Lang::get('common.faqs.question37') }}</a></li>
                        <li><a href="#faq-55">{{ \Lang::get('common.faqs.question55') }}</a></li>
                        <li><a href="#faq-31">{{ \Lang::get('common.faqs.question31') }}</a></li>
                        <li><a href="#faq-32">{{ \Lang::get('common.faqs.question32') }}</a></li>
                        <li><a href="#faq-33">{{ \Lang::get('common.faqs.question33') }}</a></li>
                        <li><a href="#faq-34">{{ \Lang::get('common.faqs.question34') }}</a></li>
                        <li><a href="#faq-35">{{ \Lang::get('common.faqs.question35') }}</a></li>
                        <li><a href="#faq-58">{{ \Lang::get('common.faqs.question58') }}</a></li>
                        <li><a href="#faq-57">{{ \Lang::get('common.faqs.question57') }}</a></li>
                        <li><a href="#faq-54">{{ \Lang::get('common.faqs.question54') }}</a></li>
                        <li><a href="#faq-39">{{ \Lang::get('common.faqs.question39') }}</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-sm-8 info-page">
        <h2 id="general-faqs">{{ \Lang::get('common.faqs.basic') }}</h2>
             
        <h4 id="faq-1">{{ \Lang::get('common.faqs.question1') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer1') }}</p>

     
        <h4 id="faq-2">{{ \Lang::get('common.faqs.question2') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer2') }}</p>
 
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question4') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer4') }}</p>
    
     
        <h4 id="faq-5">{{ \Lang::get('common.faqs.question5') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer5') }}</p>
  
     
        <h4 id="faq-3">{{ \Lang::get('common.faqs.question3') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer3') }}</p>
   
     
        <h4 id="faq-59">{{ \Lang::get('common.faqs.question59') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer59') }}</p>
   
     
        <h4 id="faq-47">{{ \Lang::get('common.faqs.question47') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer47') }}</p>
     
    
     
        <h4 id="faq-6">{{ \Lang::get('common.faqs.question6') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer6') }}</p>
     
    
        <h4 id="faq-52">{{ \Lang::get('common.faqs.question52') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer52') }}</p>
    
     
        <h4 id="faq-7">{{ \Lang::get('common.faqs.question7') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer7') }}</p>    
    
     
        <h4 id="faq-9">{{ \Lang::get('common.faqs.question9') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer9') }}</p>
    
     
        <h4 id="faq-10">{{ \Lang::get('common.faqs.question10') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer10') }}</p>
     
     
        <h4 id="faq-11">{{ \Lang::get('common.faqs.question11') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer11') }}</p>
     
     
        <h4 id="faq-12">{{ \Lang::get('common.faqs.question12') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer12') }}</p>
     
     
        <h4 id="faq-13">{{ \Lang::get('common.faqs.question13') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer13') }}</p>
     
     
        <h4 id="faq-15">{{ \Lang::get('common.faqs.question15') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer15') }}</p>
     
     
        <h4 id="faq-63">{{ \Lang::get('common.faqs.question63') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer63') }}</p>
     
     
        <h4 id="faq-49">{{ \Lang::get('common.faqs.question49') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer49') }}</p>
     
     
        <h4 id="faq-16">{{ \Lang::get('common.faqs.question16') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer16') }}</p>
     
     
        <h4 id="faq-17">{{ \Lang::get('common.faqs.question17') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer17') }}</p>
     
     
        <h4 id="faq-46">{{ \Lang::get('common.faqs.question46') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer46') }}</p>
     
     
        <h4 id="faq-51">{{ \Lang::get('common.faqs.question51') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer51') }}</p>
 
        <hr/>

        <h2 id="lender-faqs">{{ \Lang::get('common.faqs.lenders') }}</h2>
 
     
        <h4 id="faq-53">{{ \Lang::get('common.faqs.question53') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer53') }}</p>
     

     
        <h4 id="faq-45">{{ \Lang::get('common.faqs.question45') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer45') }}</p>
     

     
        <h4 id="faq-44">{{ \Lang::get('common.faqs.question44') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer44') }}</p>
     

     
        <h4 id="faq-43">{{ \Lang::get('common.faqs.question43') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer43') }}</p>
     

     
        <h4 id="faq-19">{{ \Lang::get('common.faqs.question19') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer19') }}</p>
     

     
        <h4 id="faq-21">{{ \Lang::get('common.faqs.question21') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer21') }}</p>
     

     
        <h4 id="faq-18">{{ \Lang::get('common.faqs.question18') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer18') }}</p>
     

     
        <h4 id="faq-60">{{ \Lang::get('common.faqs.question60') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer60') }}</p>
     

     
        <h4 id="faq-20">{{ \Lang::get('common.faqs.question20') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer20') }}</p>
     

     
        <h4 id="faq-22">{{ \Lang::get('common.faqs.question22') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer22') }}</p>
     

     
        <h4 id="faq-24">{{ \Lang::get('common.faqs.question24') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer24') }}</p>
     

     
        <h4 id="faq-61">{{ \Lang::get('common.faqs.question61') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer61') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question62') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer62') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question14') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer14') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question40') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer40') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question41') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer41') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question42') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer42') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question23') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer23') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question48') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer48') }}</p>

        <hr/>

        <h2 id="borrower-faqs">{{ \Lang::get('common.faqs.borrower') }}</h2>
 
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question25') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer25') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question50') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer50') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question26') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer26') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question27') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer27') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question28') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer28') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question29') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer29') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question36') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer36') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question30') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer30') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question56') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer56') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question38') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer38') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question37') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer37') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question55') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer55') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question31') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer31') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question32') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer32') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question33') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer33') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question34') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer34') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question35') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer35') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question58') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer58') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question57') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer57') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question54') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer54') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('common.faqs.question39') }}</h4>
        <p>{{ \Lang::get('common.faqs.answer39') }}</p>         

    </div>
</div>
@stop