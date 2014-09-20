@extends('layouts.master')

@section('page-title')
Terms Of Use and Privacy Policy
@stop

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2 info-page">
        
        <h1 class="page-title">Terms of Use and Privacy Policy</h1>

        <ul class="nav nav-tabs nav-justified" role="tablist">
            <li class="active">
                <a href="#terms" role="tab" data-toggle="tab">
                    Terms of Use
                </a>
            </li>
            <li>
                <a href="#privacy" role="tab" data-toggle="tab">
                    Privacy Policy
                </a>
            </li>
        </ul>

        <div id="tab-content" class="tab-content highlight highlight-top">
            <div class="tab-pane fade active in" id="terms">

                <br/>
                
                @include('lender.terms-of-use')

            </div>

            <div class="tab-pane fade" id="privacy">

                <br/>

                <a href="//www.iubenda.com/privacy-policy/629677" class="iubenda-white no-brand iub-body-embed iub-legal-only iubenda-embed" title="Privacy Policy">Privacy Policy</a><script type="text/javascript">(function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src = "//cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);</script>
            
            </div>
        </div>

    </div>
</div>
@stop

@section('script-footer')
<script type="text/javascript">
    $('.nav-tabs a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    })
</script>
@stop
