@extends('layout')
@section('content')
{{ HTML::style('public/js/front/timepicker/jquery.timepicker.css'); }}
{{ HTML::style('public/js/chosen/chosen.css'); }}
{{ HTML::script('public/js/front/timepicker/jquery.timepicker.js'); }}
<script type="text/javascript">
$(document).ready(function() {
    $(".cb-enable").click(function() {
        $('#is_default_delivery').val('1');
        var parent = $(this).parents('.switch');
        $('.cb-disable', parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox', parent).attr('checked', true);
    });
    $(".cb-disable").click(function() {
        $('#is_default_delivery').val('0');
        var parent = $(this).parents('.switch');
        $('.cb-enable', parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox', parent).attr('checked', false);
    });
    
    
    $(".cb-enable1").click(function() {
        $('#pick_up').val('1');
        var parent = $(this).parents('.switch');
        $('.cb-disable1', parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox', parent).attr('checked', true);
    });
    $(".cb-disable1").click(function() {
        $('#pick_up').val('0');
        var parent = $(this).parents('.switch');
        $('.cb-enable1', parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox', parent).attr('checked', false);
    });
});
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $.validator.addMethod('positiveNumber',
                function (value) {
                    return Number(value) > 0;
                }, 'Enter a positive number.');
        $('.start_time').timepicker({'timeFormat': 'h:i a', 'step': 30, 'minTime': '01:00am', 'maxTime': '12:59am'});
        $('.end_time').timepicker({'timeFormat': 'h:i a', 'step': 30, 'minTime': '01:00am', 'maxTime': '12:59am'});
    });

//    $(document).ready(function () {
//        $(".cb-enable").click(function () {
//            var parent = $(this).parents('.switch');
//            $('.cb-disable', parent).removeClass('selected');
//            $(this).addClass('selected');
//            $('.checkbox', parent).attr('checked', true);
//        });
//        $(".cb-disable").click(function () {
//            var parent = $(this).parents('.switch');
//            $('.cb-enable', parent).removeClass('selected');
//            $(this).addClass('selected');
//            $('.checkbox', parent).attr('checked', false);
//        });
//    });
    function chkselect(val) {
        if (val == '') {
            $('#selectop :selected').attr('selected', '');

        }
    }
</script>
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#myform").validate();
    });
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
<style>
    .cb-enable1, .cb-disable1, .cb-enable1 span, .cb-disable1 span { background: url(<?php echo HTTP_PATH . "public/css/front/" ?>switch.gif) repeat-x; display: block; float: left; }
    .cb-enable1 span, .cb-disable1 span { line-height: 30px; display: block; background-repeat: no-repeat; font-weight: bold; }
    .cb-enable1 span { background-position: left -90px; padding: 0 10px; }
    .cb-disable1 span { background-position: right -180px;padding: 0 10px; }
    .cb-disable1.selected { background-position: 0 -30px; }
    .cb-disable1.selected span { background-position: right -210px; color: #fff; }
    .cb-enable1.selected { background-position: 0 -60px; }
    .cb-enable1.selected span { background-position: left -150px; color: #fff; }
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
                    <div class="informetion_top">
                        <div class="tatils">Manage Delivery Charges</div>
                        <div class="pery">
                            <div id="formloader" class="formloader" style="display: none;">
                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                            </div>
                            {{ View::make('elements.frontEndActionMessage')->render() }}
                            {{ Form::model($detail, array('url' => '/user/deliverycharges', 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                            <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>
                            <div class="fill_data_left">
                            <div class="user_data">
                                    
                                    
                            <div class="label">Pickup Avaliable <span class="require">*</span></div>
                            <div class="detail">
                                <?php //print_r($detail); exit; ?>
                                <p class="field switch">
                                    <label class="cb-enable1 {{($detail->pick_up==1)?'selected':''}}"><span>On</span></label>
                                    <label class="cb-disable1 {{($detail->pick_up==0)?'selected':''}}" ><span>Off</span></label>
                                     <p class="help-block">     If you turn the Pickup Avaliable option customer will able to pick her food direct to your location also on some time period.</p>
                                </p>

                            </div>
                                   
                                    
                                    
                            <div class="label">Status <span class="require">*</span></div>
                            <div class="detail">
                                <?php //print_r($detail); exit; ?>
                                <p class="field switch">
                                    <label class="cb-enable {{($detail->is_default_delivery==1)?'selected':''}}"><span>On</span></label>
                                    <label class="cb-disable {{($detail->is_default_delivery==0)?'selected':''}}" ><span>Off</span></label>
                                     <p class="help-block">     If you turn the status on this will be applicable to all orders and if off than delivery charge will be zero(0).</p>
                                </p>

                            </div>
                                 

                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Vespa Delivery Price (<?php echo CURR; ?>) <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('normal', Input::old('normal'), array('id' => 'normal', 'autofocus' => true, 'class' => "required form-control number", 'min' => "1"));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Car Delivery Price (<?php echo CURR; ?>) <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('advance', Input::old('advance'), array('id' => 'advance', 'autofocus' => true, 'class' => "required form-control number", 'min' => "1"));
                                    ?>
                                </div>
                            </div>
                             <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Delivery Charge Limit (<?php echo CURR; ?>) <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('delivery_charge_limit', Input::old('delivery_charge_limit'), array('id' => 'delivery_charge_limit', 'autofocus' => true, 'class' => "required form-control number", 'min' => "1"));
                                    ?>
                                    <p class="help-block">If order price is less then this delivery charge limit so vespa delivery charge applicable or If order price is greater then this delivery charge limit so car delivery price will be applicable.  </p>
                                </div>
                            </div> 


                            <div class="form_group">
                                <div class="in_upt in_upt_res center-element">
                                    <input type="hidden" name="pick_up" id="pick_up" value="<?php  echo $detail->pick_up;?>">
                                    <input type="hidden" name="is_default_delivery" id="is_default_delivery" value="<?php  echo $detail->is_default_delivery;?>">
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
<script type="text/javascript"> $(".chzn-select").chosen();
    $(".chzn-select-deselect").chosen({allow_single_deselect: true});
    $("#selectop").chosen({allow_single_deselect: true});
</script>

@stop


