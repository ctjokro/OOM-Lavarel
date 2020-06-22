@extends('layout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    $("#myform").validate({
        submitHandler: function (form) {
            this.checkForm();

            if (this.valid()) { // checks form for validity
                $('#formloader').show();
                this.submit();
            } else {
                return false;
            }
        }
    });
});
</script>


<section>
    <div class="top_menus">
         <div class="dash_toppart">
         <div class="wrapper"> 
        <div class="_cttv">
                 @include('elements/left_menu')
                 
                 
        </div></div></div>
        <div class="wrapper">
          
            <div class="acc_bar acc_bar_new">
                 @include('elements/oderc_menu')
                <div class="informetion informetion_new">
                    <div class="informetion_top">
                        <div class="tatils">Credit card payment gateway setup</div>
                        <div class="pery">
                            <div id="formloader" class="formloader" style="display: none;">
                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                            </div>
                            {{ View::make('elements.actionMessage')->render() }}
                            {{ Form::model($userData, array('url' => '/user/creditcardsetup', 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                            <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>
                             <div class="form_group">
                                {{ HTML::decode(Form::label('paypal_username', "Paypal Username <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{ Form::text('paypal_username', Input::old('paypal_username'), array('class' => 'required form-control', 'maxlength'=>254)) }}
                                </div>
                            </div>
                             <div class="form_group">
                                {{ HTML::decode(Form::label('paypal_password', "Paypal Password <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{ Form::text('paypal_password', Input::old('paypal_password'), array('class' => 'form-control required', 'maxlength'=>254)) }}
                                </div>
                            </div>
                             <div class="form_group">
                                {{ HTML::decode(Form::label('paypal_signature', "Paypal Signature <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{ Form::text('paypal_signature', Input::old('paypal_signature'), array('class' => 'required form-control', 'maxlength'=>254)) }}
                                </div>
                            </div>
                            
                           

                            <div class="form_group input_bxxs">
                                <label>&nbsp;</label>
                                <div class="in_upt in_upt_res">
                                    {{ Form::submit('Submit', array('class' => "btn btn-primary")) }}
                                    {{ html_entity_decode(HTML::link(HTTP_PATH.'user/myaccount', "Cancel", array('class' => 'btn btn-default'), true)) }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{ HTML::style('public/js/chosen/chosen.css'); }}
<!--scripts page-->
{{ HTML::script('public/js/chosen/chosen.jquery.js'); }}
<script type="text/javascript">
    $(".chzn-select").chosen();
</script>

@stop


