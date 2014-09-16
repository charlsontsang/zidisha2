@extends('layouts.master')

@section('page-title')
{{ \Lang::get('borrower.faqs.heading') }}
@stop


@section('content')
<div class="row" class="bs-docs-sidebar">
    <div class="col-xs-12 page-header">
        <h1>{{ \Lang::get('borrower.faqs.heading') }}</h1>
    </div>

    <div class="sidebar col-sm-4 pull-right">
        <div class="sidenav hidden-print hidden-xs affix-top" role="complementary">
            <ul class="nav sidenav-first-level">
                <li class="active"><h4><a href="#general-faqs">{{ \Lang::get('borrower.faqs.basic') }}</a></h4>
                    <ul class="nav">
                        <li><a href="#faq-1">{{ \Lang::get('borrower.faqs.question1') }}</a></li>
                        <li><a href="#faq-2">{{ \Lang::get('borrower.faqs.question2') }}</a></li>
                        <li><a href="#faq-4">{{ \Lang::get('borrower.faqs.question4') }}</a></li>
                        <li><a href="#faq-5">{{ \Lang::get('borrower.faqs.question5') }}</a></li>
                        <li><a href="#faq-3">{{ \Lang::get('borrower.faqs.question3') }}</a></li>
                        <li><a href="#faq-59">{{ \Lang::get('borrower.faqs.question59') }}</a></li>
                        <li><a href="#faq-47">{{ \Lang::get('borrower.faqs.question47') }}</a></li>
                        <li><a href="#faq-6">{{ \Lang::get('borrower.faqs.question6') }}</a></li>
                        <li><a href="#faq-52">{{ \Lang::get('borrower.faqs.question52') }}</a></li>
                        <li><a href="#faq-7">{{ \Lang::get('borrower.faqs.question7') }}</a></li>
                        <li><a href="#faq-9">{{ \Lang::get('borrower.faqs.question9') }}</a></li>
                        <li><a href="#faq-10">{{ \Lang::get('borrower.faqs.question10') }}</a></li>
                        <li><a href="#faq-11">{{ \Lang::get('borrower.faqs.question11') }}</a></li>
                        <li><a href="#faq-12">{{ \Lang::get('borrower.faqs.question12') }}</a></li>
                        <li><a href="#faq-13">{{ \Lang::get('borrower.faqs.question13') }}</a></li>
                        <li><a href="#faq-15">{{ \Lang::get('borrower.faqs.question15') }}</a></li>
                        <li><a href="#faq-63">{{ \Lang::get('borrower.faqs.question63') }}</a></li>
                        <li><a href="#faq-49">{{ \Lang::get('borrower.faqs.question49') }}</a></li>
                        <li><a href="#faq-16">{{ \Lang::get('borrower.faqs.question16') }}</a></li>
                        <li><a href="#faq-17">{{ \Lang::get('borrower.faqs.question17') }}</a></li>
                        <li><a href="#faq-46">{{ \Lang::get('borrower.faqs.question46') }}</a></li>
                        <li><a href="#faq-51">{{ \Lang::get('borrower.faqs.question51') }}</a></li>
                    </ul>
                </li>
                <li><h4><a href="#lender-faqs">{{ \Lang::get('borrower.faqs.lenders') }}</a></h4>
                    <ul class="nav">
                        <li><a href="#faq-53">{{ \Lang::get('borrower.faqs.question53') }}</a></li>
                        <li><a href="#faq-45">{{ \Lang::get('borrower.faqs.question45') }}</a></li>
                        <li><a href="#faq-44">{{ \Lang::get('borrower.faqs.question44') }}</a></li>
                        <li><a href="#faq-43">{{ \Lang::get('borrower.faqs.question43') }}</a></li>
                        <li><a href="#faq-19">{{ \Lang::get('borrower.faqs.question19') }}</a></li>
                        <li><a href="#faq-21">{{ \Lang::get('borrower.faqs.question21') }}</a></li>
                        <li><a href="#faq-18">{{ \Lang::get('borrower.faqs.question18') }}</a></li>
                        <li><a href="#faq-60">{{ \Lang::get('borrower.faqs.question60') }}</a></li>
                        <li><a href="#faq-20">{{ \Lang::get('borrower.faqs.question20') }}</a></li>
                        <li><a href="#faq-22">{{ \Lang::get('borrower.faqs.question22') }}</a></li>
                        <li><a href="#faq-24">{{ \Lang::get('borrower.faqs.question24') }}</a></li>
                        <li><a href="#faq-61">{{ \Lang::get('borrower.faqs.question61') }}</a></li>
                        <li><a href="#faq-62">{{ \Lang::get('borrower.faqs.question62') }}</a></li>
                        <li><a href="#faq-14">{{ \Lang::get('borrower.faqs.question14') }}</a></li>
                        <li><a href="#faq-40">{{ \Lang::get('borrower.faqs.question40') }}</a></li>
                        <li><a href="#faq-41">{{ \Lang::get('borrower.faqs.question41') }}</a></li>
                        <li><a href="#faq-42">{{ \Lang::get('borrower.faqs.question42') }}</a></li>
                        <li><a href="#faq-23">{{ \Lang::get('borrower.faqs.question23') }}</a></li>
                        <li><a href="#faq-48">{{ \Lang::get('borrower.faqs.question48') }}</a></li>
                    </ul>
                </li>
                <li><h4><a href="#borrower-faqs">{{ \Lang::get('borrower.faqs.borrower') }}</a></h4>
                    <ul class="nav">
                        <li><a href="#faq-25">{{ \Lang::get('borrower.faqs.question25') }}</a></li>
                        <li><a href="#faq-50">{{ \Lang::get('borrower.faqs.question50') }}</a></li>
                        <li><a href="#faq-26">{{ \Lang::get('borrower.faqs.question26') }}</a></li>
                        <li><a href="#faq-27">{{ \Lang::get('borrower.faqs.question27') }}</a></li>
                        <li><a href="#faq-28">{{ \Lang::get('borrower.faqs.question28') }}</a></li>
                        <li><a href="#faq-29">{{ \Lang::get('borrower.faqs.question29') }}</a></li>
                        <li><a href="#faq-36">{{ \Lang::get('borrower.faqs.question36') }}</a></li>
                        <li><a href="#faq-30">{{ \Lang::get('borrower.faqs.question30') }}</a></li>
                        <li><a href="#faq-56">{{ \Lang::get('borrower.faqs.question56') }}</a></li>
                        <li><a href="#faq-38">{{ \Lang::get('borrower.faqs.question38') }}</a></li>
                        <li><a href="#faq-37">{{ \Lang::get('borrower.faqs.question37') }}</a></li>
                        <li><a href="#faq-55">{{ \Lang::get('borrower.faqs.question55') }}</a></li>
                        <li><a href="#faq-31">{{ \Lang::get('borrower.faqs.question31') }}</a></li>
                        <li><a href="#faq-32">{{ \Lang::get('borrower.faqs.question32') }}</a></li>
                        <li><a href="#faq-33">{{ \Lang::get('borrower.faqs.question33') }}</a></li>
                        <li><a href="#faq-34">{{ \Lang::get('borrower.faqs.question34') }}</a></li>
                        <li><a href="#faq-35">{{ \Lang::get('borrower.faqs.question35') }}</a></li>
                        <li><a href="#faq-58">{{ \Lang::get('borrower.faqs.question58') }}</a></li>
                        <li><a href="#faq-57">{{ \Lang::get('borrower.faqs.question57') }}</a></li>
                        <li><a href="#faq-54">{{ \Lang::get('borrower.faqs.question54') }}</a></li>
                        <li><a href="#faq-39">{{ \Lang::get('borrower.faqs.question39') }}</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-sm-8 info-page">
        <h2 id="general-faqs">{{ \Lang::get('borrower.faqs.basic') }}</h2>
             
        <h4 id="faq-1">{{ \Lang::get('borrower.faqs.question1') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer1') }}</p>

     
        <h4 id="faq-2">{{ \Lang::get('borrower.faqs.question2') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer2') }}</p>
 
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question4') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer4') }}</p>
    
     
        <h4 id="faq-5">{{ \Lang::get('borrower.faqs.question5') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer5') }}</p>
  
     
        <h4 id="faq-3">{{ \Lang::get('borrower.faqs.question3') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer3') }}</p>
   
     
        <h4 id="faq-59">{{ \Lang::get('borrower.faqs.question59') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer59') }}</p>
   
     
        <h4 id="faq-47">{{ \Lang::get('borrower.faqs.question47') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer47') }}</p>
     
    
     
        <h4 id="faq-6">{{ \Lang::get('borrower.faqs.question6') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer6') }}</p>
     
    
        <h4 id="faq-52">{{ \Lang::get('borrower.faqs.question52') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer52') }}</p>
    
     
        <h4 id="faq-7">{{ \Lang::get('borrower.faqs.question7') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer7') }}</p>    
    
     
        <h4 id="faq-9">{{ \Lang::get('borrower.faqs.question9') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer9') }}</p>
    
     
        <h4 id="faq-10">{{ \Lang::get('borrower.faqs.question10') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer10') }}</p>
     
     
        <h4 id="faq-11">{{ \Lang::get('borrower.faqs.question11') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer11') }}</p>
     
     
        <h4 id="faq-12">{{ \Lang::get('borrower.faqs.question12') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer12') }}</p>
     
     
        <h4 id="faq-13">{{ \Lang::get('borrower.faqs.question13') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer13') }}</p>
     
     
        <h4 id="faq-15">{{ \Lang::get('borrower.faqs.question15') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer15') }}</p>
     
     
        <h4 id="faq-63">{{ \Lang::get('borrower.faqs.question63') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer63') }}</p>
     
     
        <h4 id="faq-49">{{ \Lang::get('borrower.faqs.question49') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer49') }}</p>
     
     
        <h4 id="faq-16">{{ \Lang::get('borrower.faqs.question16') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer16') }}</p>
     
     
        <h4 id="faq-17">{{ \Lang::get('borrower.faqs.question17') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer17') }}</p>
     
     
        <h4 id="faq-46">{{ \Lang::get('borrower.faqs.question46') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer46') }}</p>
     
     
        <h4 id="faq-51">{{ \Lang::get('borrower.faqs.question51') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer51') }}</p>
 
        <hr/>

        <h2 id="lender-faqs">{{ \Lang::get('borrower.faqs.lenders') }}</h2>
 
     
        <h4 id="faq-53">{{ \Lang::get('borrower.faqs.question53') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer53') }}</p>
     

     
        <h4 id="faq-45">{{ \Lang::get('borrower.faqs.question45') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer45') }}</p>
     

     
        <h4 id="faq-44">{{ \Lang::get('borrower.faqs.question44') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer44') }}</p>
     

     
        <h4 id="faq-43">{{ \Lang::get('borrower.faqs.question43') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer43') }}</p>
     

     
        <h4 id="faq-19">{{ \Lang::get('borrower.faqs.question19') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer19') }}</p>
     

     
        <h4 id="faq-21">{{ \Lang::get('borrower.faqs.question21') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer21') }}</p>
     

     
        <h4 id="faq-18">{{ \Lang::get('borrower.faqs.question18') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer18') }}</p>
     

     
        <h4 id="faq-60">{{ \Lang::get('borrower.faqs.question60') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer60') }}</p>
     

     
        <h4 id="faq-20">{{ \Lang::get('borrower.faqs.question20') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer20') }}</p>
     

     
        <h4 id="faq-22">{{ \Lang::get('borrower.faqs.question22') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer22') }}</p>
     

     
        <h4 id="faq-24">{{ \Lang::get('borrower.faqs.question24') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer24') }}</p>
     

     
        <h4 id="faq-61">{{ \Lang::get('borrower.faqs.question61') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer61') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question62') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer62') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question14') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer14') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question40') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer40') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question41') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer41') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question42') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer42') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question23') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer23') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question48') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer48') }}</p>

        <hr/>

        <h2 id="borrower-faqs">{{ \Lang::get('borrower.faqs.borrower') }}</h2>
 
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question25') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer25') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question50') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer50') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question26') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer26') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question27') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer27') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question28') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer28') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question29') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer29') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question36') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer36') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question30') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer30') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question56') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer56') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question38') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer38') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question37') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer37') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question55') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer55') }}</p>
     

     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question31') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer31') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question32') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer32') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question33') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer33') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question34') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer34') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question35') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer35') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question58') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer58') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question57') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer57') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question54') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer54') }}</p>
     
     
        <h4 id="faq-4">{{ \Lang::get('borrower.faqs.question39') }}</h4>
        <p>{{ \Lang::get('borrower.faqs.answer39') }}</p>         

    </div>
</div>
@stop