@extends('layout')
@section('content')
{{ HTML::style('public/js/front/timepicker/jquery.timepicker.css'); }}
{{ HTML::style('public/js/chosen/chosen.css'); }}
{{ HTML::script('public/js/front/timepicker/jquery.timepicker.js'); }}
<!--
{{ HTML::script('public/js/date_jquerytimepicker.js'); }}
{{ HTML::style('public/css/date_jquerytimepicker.css'); }}
{{ HTML::script('public/js/date_jquery.min.js'); }}
 -->
<style>
@media screen and (max-width: 767px) {
 label {
    display: block;
}
.menu_table ul li {
    width: 100% !important;
} 
}
span.lunch {
    padding-left: 21px;
}
span.dinner {
    padding-left: 42px;
}
</style>
<script type="text/javascript">
    $(document).ready(function () {
        
        $.validator.addMethod('positiveNumber',
        function (value) {
            return Number(value) > 0;
        }, 'Enter a positive number.');
        $('#myform').validate();
        $('.start_time').timepicker({'timeFormat': 'h:i a', 'step': 30, 'minTime': '01:00am', 'maxTime': '12:59am'});
        $('.end_time').timepicker({'timeFormat': 'h:i a', 'step': 30, 'minTime': '01:00am', 'maxTime': '12:59am'});
    });

    $(document).ready(function () {
        $(".cb-enable").click(function () {
            var parent = $(this).parents('.switch');
            $('.cb-disable', parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox', parent).attr('checked', true);
        });
        $(".cb-disable").click(function () {
            var parent = $(this).parents('.switch');
            $('.cb-enable', parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox', parent).attr('checked', false);
        });
    });
    function chkselect(val) {
        if (val == '') {
            $('#selectop :selected').attr('selected', '');

        }
    }

 /*$(function() {
                $('#basicExample').timepicker();
            });
            $(function() {
                $('#basicExample1').timepicker();
            });    

(function($) {
    $(function() {
        $('input.timepicker').timepicker();
    });
})(jQuery);
*/
</script>

<style>
    .cb-enable, .cb-disable, .cb-enable span, .cb-disable span { background: url(<?php echo HTTP_PATH . "public/css/front/" ?>switch.gif) repeat-x; display: block; float: left; }
    .cb-enable span, .cb-disable span { line-height: 30px; display: block; background-repeat: no-repeat; font-weight: bold; }
    .cb-enable span { background-position: left -90px; padding: 0 10px; }
    .cb-disable span { background-position: right -180px;padding: 0 10px; }
    .cb-disable.selected { background-position: 0 -30px; }
    .cb-disable.selected span { background-position: right -210px; color: #fff; }
    .cb-enable.selected { background-position: 0 -60px; }
    .cb-enable.selected span { background-position: left -150px; color: #fff; }
    .switch label { cursor: pointer; }
    .switch input { display: none; }
    
    

</style>

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
                    <div style="width:100%" class="informetion_top">
                        <div class="tatils">Payment Method</div>
                        <div class="pery">
                            <div id="formloader" class="formloader" style="display: none;">
                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                            </div>
                            {{ View::make('elements.frontEndActionMessage')->render() }}
                            {{ Form::open(array('url' => '/user/paymentMethod', 'method' => 'post', 'id' => 'myform1', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                            <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>
                            <div class="fill_data_left">
                                <div class="user_data">
                                    
 

                                    <div class="labeles">
                                       Payment Method <span class="red-mark"> *</span>
                                    </div>
                                    
                                    <div class="detail" > 
                                     <!-- <select class="required form-control" name="payment_method[]" id="payment_method" multiple="multiple">
                                          <option value="">Please select Payment Method</option>
                                          <option value="paypal">Paypal</option>
                                          <option value ="bank_transfer">Bank Transfer</option>
                                          <option value="payment_gateway">Payment Gateway</option>
                                      </select>-->
                                    <?php
                                    $paymentmethod_array = array(
                                        '' => 'Please Select'
                                    );

                                   // $paymentmethod = ['','Paypal','Bank Transfer','Payment Gateway'];
                                    $paymentmethod = PaymentMethod::orderBy('id', 'asc')->lists('payment_method', 'payment_method');
                                    if (!empty($paymentmethod)) {
                                        foreach ($paymentmethod as $key => $val)
                                            $paymentmethod[$key] = ucfirst($val);
                                    }
                                   
                                    $old = Input::old('payment_method') ? Input::old('payment_method') : explode(",", @$payment_method->payment_method);

                                    ?>
                                     {{ Form::select('payment_method[]',$paymentmethod, $old, array('class' => 'required form-control','multiple'=>'multiple' ,'onchange'=>"chkselect(this.value);",'id'=>"selectop")) }}
                                    </div> 

                                </div>
                            </div>

                            <div class="form_group">
                                <div class="in_upt in_upt_res center-element">
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

{{ HTML::script('public/js/chosen/chosen.jquery.js'); }}
<script type="text/javascript"> 
    Array.prototype.diff = function (a) {
        return this.filter(function (i) {
            return a.indexOf(i) === -1;
        });
    };

    $('body').on('change','.chzn-select',function(e){
        
        var opendays = $(this).val();
        //    alert(opendays);
        var alwaysdays = new Array('mon', 'tue', 'wed', 'thu', 'fri' , 'sat', 'sun');
       
        for(var i = 0;i<= 6;i++){
            if(opendays.indexOf(alwaysdays[i]) == -1){
                $('#'+alwaysdays[i]+'_div').hide();
            }else{
                $('#'+alwaysdays[i]+'_div').show();
            }
        }
       
    });
    
    $(".chzn-select").chosen();
    $(".chzn-select-deselect").chosen({allow_single_deselect: true});
    $("#selectop").chosen({allow_single_deselect: true});
//    $("#selectop").chosen({disable: true});
    
    
    function valueChanged(value){
        if(value == 0){
            //$('.menu_tablesefgt input').attr('readonly', 'readonly');
            $('.menu_tablesefgt input').attr('disabled', 'disabled');
            $('.menu_tablesefgt select').attr('disabled', 'disabled');
            $('.chzn-select').attr('disabled', 'disabled');
            $('.chzn-container').addClass('dcbl_dv');
            $('.chzn-select').prop('disabled', true).trigger("liszt:updated");
            $('.hint-onoff_highlight').show();
        }else{
            $('.menu_tablesefgt input').removeAttr('disabled', 'disabled');
             $('.menu_tablesefgt select').removeAttr('disabled', 'disabled');
            $('.chzn-select').removeAttr('disabled', 'disabled');
            $('.chzn-container').removeClass('dcbl_dv');
            $('.chzn-select').prop('disabled', false).trigger("liszt:updated");
            $('.hint-onoff_highlight').hide();

        }
    }
</script>


<?php if (@$opening_hours->open_close == 0) {
    ?>
    <script type="text/javascript">
   
    
        $(document).ready(function () {
            $('.menu_tablesefgt select').attr('disabled', 'disabled');
            $('.menu_tablesefgt input').attr('disabled', 'disabled');
            $('.chzn-select').attr('disabled', 'disabled');
            $('.chzn-select').prop('disabled', true).trigger("liszt:updated");
            $('.hint-onoff_highlight').show();

        });
    </script>
    <?php }
?>


@stop


