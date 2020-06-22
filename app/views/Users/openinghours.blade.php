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
                        <div class="tatils">Manage Opening Hours</div>
                        <div class="pery">
                            <div id="formloader" class="formloader" style="display: none;">
                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                            </div>
                            {{ View::make('elements.frontEndActionMessage')->render() }}
                            {{ Form::model($opening_hours, array('url' => '/user/openinghours', 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                            <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>
                            <div class="fill_data_left">
                                <div class="user_data">
                                    <div class="label">Restaurant Status <span class="red-mark"> *</span></div>
                                    <div class="detail">

                                        <p class="field switch">
                                            <label class="cb-enable {{@$opening_hours->open_close?'selected':''}}"><span onclick="valueChanged(1);">On</span></label>
                                            <label class="cb-disable  {{@$opening_hours->open_close?'':'selected'}}"><span onclick="valueChanged(0);">Off</span></label>
                                            {{ Form::checkbox('open_close', 1, (@$opening_hours->open_close?TRUE:FALSE), ['id' => 'checkbox', 'class'=>'checkbox']) }}
                                        </p>
                                        <div class="hint-onoff">
<!--                                            If you turn your status off this will override your opening hours and no customers will be able to order!-->
                                        </div>
                                        
                                         <span class="hint-onoff hint-onoff_highlight require" style="display:none">
                                            Kindly mark the restaurant status On to set the opening days and opening hours of restaurant.
                                        </span>
                                    </div>

                                    <div class="label">Open Days <span class="red-mark"> *</span></div>
                                    <div class="detail">
                                        <?php
                                        $array = array('mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday');
                                        ?>
                                        {{ Form::select('open_days[]', $array,  explode(',', @$opening_hours->open_days), array('multiple' => true, 'class'=>'chzn-select')); }}
                                    </div>
                                    <div class="labeles">
                                        Lunch and Dinner Hours <span class="red-mark"> *</span>
                                    </div>
                                    <div class="detail" id="detail_field_page"> 
                                        <div class="menu_table menu_tablese menu_tablesefgt">
                                            <div class="row"> 
                                            <div class="col-md-1"></div>
                                            <div class="col-md-3">
                                            <span class="break_fast">   <label>Breakfast Time </label> </span>
                                            </div>
                                            <div class="col-md-3">
                                            <span class="lunch">   <label>Lunch Time </label> </span>
                                            </div>
                                            
                                            <div class="col-md-5">
                                            <span class="dinner">   <label>Dinner Time </label> </span>
                                            </div> 
                                            </div>
                                       <!--<p><input id="basicExample" type="text" class="time"  /> <input id="basicExample1" type="text" class="time" /></p>
                                       <input type="text" class="timepicker" name="time"/>
                                       -->

                                            <?php
                                            $openday = explode(',', @$opening_hours->open_days);
                                            $vrrcy = explode(',', @$opening_hours->start_time);
                                            $endty = explode(',', @$opening_hours->end_time);
                                            $breakfast = explode(',', @$opening_hours->breakfast_time);
                                            $startime = array_combine($openday, $vrrcy);
                                            $endtime = array_combine($openday, $endty);
                                            $breakfast_time = array_combine($openday, $breakfast);
                                            

                                            foreach ($array as $vardhbb => $value) {
                                                $bloack = "block";
                                                if (!in_array($vardhbb, $openday)) {
                                                    $bloack = "none";
                                                }
                                                ?>
                                                <?php 
                                                    
                                                    //For Breakfast Start
                                                    $bt = explode('to', $breakfast_time[$vardhbb]);
                                                    $datetime = new DateTime($bt[0]);
                                                    $timezone = new DateTimeZone($tz);
                                                    $datetime->setTimezone($timezone);
                                                    $bf_start_time = $datetime->format('H:i a');
                                                    
                                                    $datetime = new DateTime($bt[1]);
                                                    $timezone = new DateTimeZone($tz);
                                                    $datetime->setTimezone($timezone);
                                                    $bf_end_time = $datetime->format('H:i a');
                                                    //For Breakfast End
                                                    
                                                    //For Lunch Start
                                                    $lt = explode('to', $startime[$vardhbb]);
                                                    $datetime = new DateTime($lt[0]);
                                                    $timezone = new DateTimeZone($tz);
                                                    $datetime->setTimezone($timezone);
                                                    $lt_start_time = $datetime->format('H:i a');
                                                    
                                                    $datetime = new DateTime($lt[1]);
                                                    $timezone = new DateTimeZone($tz);
                                                    $datetime->setTimezone($timezone);
                                                    $lt_end_time = $datetime->format('H:i a');
                                                    //For Lunch End
                                                    
                                                    //For Dinner Start
                                                    $dt = explode('to', $endtime[$vardhbb]);
                                                    $datetime = new DateTime($dt[0]);
                                                    $timezone = new DateTimeZone($tz);
                                                    $datetime->setTimezone($timezone);
                                                    $dt_start_time = $datetime->format('H:i a');
                                                    
                                                    $datetime = new DateTime($dt[1]);
                                                    $timezone = new DateTimeZone($tz);
                                                    $datetime->setTimezone($timezone);
                                                    $dt_end_time = $datetime->format('H:i a');
                                                    //For Dinner End
                                                ?>
                                                <ul id="<?php echo $vardhbb; ?>_div" style="display: <?php echo $bloack; ?>">
                                                  <!--  <li>  -->
                                                     <span class="days_left">   <label style="width:8%"> <?php echo $value; ?> </label> </span>
                                                     
                                                     
                                                 <!--   </li>  -->
                                                    <li style="width: 28%;">
                                                        
                                                      {{ Form::hidden('open_days_label[]', $vardhbb, array('class' => 'start_time required form-control','placeholder'=>"Start Time")) }}    
                                                        <select class="start_time1 required form-control" name="breakfast_time[{{ $vardhbb }}]" placeholder="Breakfast Time" style="width: 42%;">
                                                            <option value="24:00:00" <?php if($bf_start_time == '24:00 am') { echo "selected"; } ?> >12:00 am</option>
                                                        	<option value="00:30:00" <?php if($bf_start_time == '00:30 am') { echo "selected"; } ?> >12:30 am</option>
                                                        	<option value="01:00:00" <?php if($bf_start_time == '01:00 am') { echo "selected"; } ?> >01:00 am</option>
                                                        	<option value="01:30:00" <?php if($bf_start_time == '01:30 am') { echo "selected"; } ?> >01:30 am</option>
                                                        	<option value="02:00:00" <?php if($bf_start_time == '02:00 am') { echo "selected"; } ?> >02:00 am </option>
                                                        	<option value="02:30:00" <?php if($bf_start_time == '02:30 am') { echo "selected"; } ?> >02:30 am</option>
                                                        	<option value="03:00:00" <?php if($bf_start_time == '03:00 am') { echo "selected"; } ?> >03:00 am</option>
                                                        	<option value="03:30:00" <?php if($bf_start_time == '03:30 am') { echo "selected"; } ?> >03:30 am</option>
                                                        	<option value="04:00:00" <?php if($bf_start_time == '04:00 am') { echo "selected"; } ?> >04:00 am</option>
                                                        	<option value="04:30:00" <?php if($bf_start_time == '04:30 am') { echo "selected"; } ?> >04:30 am</option>
                                                        	<option value="05:00:00" <?php if($bf_start_time == '05:00 am') { echo "selected"; } ?> >05:00 am</option>
                                                        	<option value="05:30:00" <?php if($bf_start_time == '05:30 am') { echo "selected"; } ?> >05:30 am</option>
                                                        	<option value="06:00:00" <?php if($bf_start_time == '06:00 am') { echo "selected"; } ?> >06:00 am</option>
                                                        	<option value="06:30:00" <?php if($bf_start_time == '06:30 am') { echo "selected"; } ?> >06:30 am</option>
                                                        	<option value="07:00:00" <?php if($bf_start_time == '07:00 am') { echo "selected"; } ?> >07:00 am</option>
                                                        	<option value="07:30:00" <?php if($bf_start_time == '07:30 am') { echo "selected"; } ?> >07:30 am</option>
                                                        	<option value="08:00:00" <?php if($bf_start_time == '08:00 am') { echo "selected"; } ?> >08:00 am</option>
                                                        	<option value="08:30:00" <?php if($bf_start_time == '08:30 am') { echo "selected"; } ?> >08:30 am</option>
                                                        	<option value="09:00:00" <?php if($bf_start_time == '09:00 am') { echo "selected"; } ?> >09:00 am</option>
                                                        	<option value="09:30:00" <?php if($bf_start_time == '09:30 am') { echo "selected"; } ?> >09:30 am</option>
                                                        	<option value="10:00:00" <?php if($bf_start_time == '10:00 am') { echo "selected"; } ?> >10:00 am</option>
                                                        	<option value="10:30:00" <?php if($bf_start_time == '10:30 am') { echo "selected"; } ?> >10:30 am</option>
                                                        	<option value="11:00:00" <?php if($bf_start_time == '11:00 am') { echo "selected"; } ?> >11:00 am</option>
                                                        	<option value="11:30:00" <?php if($bf_start_time == '11:30 am') { echo "selected"; } ?> >11:30 am</option>
                                                        	<option value="12:00:00" <?php if($bf_start_time == '12:00 pm') { echo "selected"; } ?> >12:00 pm</option>
                                                        	<option value="12:30:00" <?php if($bf_start_time == '12:30 pm') { echo "selected"; } ?> >12:30 pm</option>
                                                        	<option value="13:00:00" <?php if($bf_start_time == '13:00 pm') { echo "selected"; } ?> >01:00 pm</option>
                                                        	<option value="13:30:00" <?php if($bf_start_time == '13:30 pm') { echo "selected"; } ?> >01:30 pm</option>
                                                        	<option value="14:00:00" <?php if($bf_start_time == '14:00 pm') { echo "selected"; } ?> >02:00 pm</option>
                                                        	<option value="14:30:00" <?php if($bf_start_time == '14:30 pm') { echo "selected"; } ?> >02:30 pm</option>
                                                        	<option value="15:00:00" <?php if($bf_start_time == '15:00 pm') { echo "selected"; } ?> >03:00 pm</option>
                                                        	<option value="15:30:00" <?php if($bf_start_time == '15:30 pm') { echo "selected"; } ?> >03:30 pm</option>
                                                        	<option value="16:00:00" <?php if($bf_start_time == '16:00 pm') { echo "selected"; } ?> >04:00 pm</option>
                                                        	<option value="16:30:00" <?php if($bf_start_time == '16:30 pm') { echo "selected"; } ?> >04:30 pm</option>
                                                        	<option value="17:00:00" <?php if($bf_start_time == '17:00 pm') { echo "selected"; } ?> >05:00 pm</option>
                                                        	<option value="17:30:00" <?php if($bf_start_time == '17:30 pm') { echo "selected"; } ?> >05:30 pm</option>
                                                        	<option value="18:00:00" <?php if($bf_start_time == '18:00 pm') { echo "selected"; } ?> >06:00 pm</option>
                                                        	<option value="18:30:00" <?php if($bf_start_time == '18:30 pm') { echo "selected"; } ?> >06:30 pm</option>
                                                        	<option value="19:00:00" <?php if($bf_start_time == '19:00 pm') { echo "selected"; } ?> >07:00 pm</option>
                                                        	<option value="19:30:00" <?php if($bf_start_time == '19:30 pm') { echo "selected"; } ?> >07:30 pm</option>
                                                        	<option value="20:00:00" <?php if($bf_start_time == '20:00 pm') { echo "selected"; } ?> >08:00 pm</option>
                                                        	<option value="20:30:00" <?php if($bf_start_time == '20:30 pm') { echo "selected"; } ?> >08:30 pm</option>
                                                        	<option value="21:00:00" <?php if($bf_start_time == '21:00 pm') { echo "selected"; } ?> >09:00 pm</option>
                                                        	<option value="21:30:00" <?php if($bf_start_time == '21:30 pm') { echo "selected"; } ?> >09:30 pm</option>
                                                        	<option value="22:00:00" <?php if($bf_start_time == '22:00 pm') { echo "selected"; } ?> >10:00 pm</option>
                                                        	<option value="22:30:00" <?php if($bf_start_time == '22:30 pm') { echo "selected"; } ?> >10:30 pm</option>
                                                        	<option value="23:00:00" <?php if($bf_start_time == '23:00 pm') { echo "selected"; } ?> >11:00 pm</option>
                                                        	<option value="23:30:00" <?php if($bf_start_time == '23:30 pm') { echo "selected"; } ?> >11:30 pm</option> 
                                                            </select>
                                                            
                                                            <!------------------------------------My breakfast_time_end here------------------------------>
                                                            
                                                        <select class="start_time1 required form-control" name="breakfast_time_end[{{ $vardhbb }}]" placeholder="Breakfast Time" style="width: 42%;">
                                                            <option value="24:00:00" <?php if($bf_end_time == '24:00 am') { echo "selected"; } ?> >12:00 am</option>
                                                        	<option value="00:30:00" <?php if($bf_end_time == '00:30 am') { echo "selected"; } ?> >12:30 am</option>
                                                        	<option value="01:00:00" <?php if($bf_end_time == '01:00 am') { echo "selected"; } ?> >01:00 am</option>
                                                        	<option value="01:30:00" <?php if($bf_end_time == '01:30 am') { echo "selected"; } ?> >01:30 am</option>
                                                        	<option value="02:00:00" <?php if($bf_end_time == '02:00 am') { echo "selected"; } ?> >02:00 am </option>
                                                        	<option value="02:30:00" <?php if($bf_end_time == '02:30 am') { echo "selected"; } ?> >02:30 am</option>
                                                        	<option value="03:00:00" <?php if($bf_end_time == '03:00 am') { echo "selected"; } ?> >03:00 am</option>
                                                        	<option value="03:30:00" <?php if($bf_end_time == '03:30 am') { echo "selected"; } ?> >03:30 am</option>
                                                        	<option value="04:00:00" <?php if($bf_end_time == '04:00 am') { echo "selected"; } ?> >04:00 am</option>
                                                        	<option value="04:30:00" <?php if($bf_end_time == '04:30 am') { echo "selected"; } ?> >04:30 am</option>
                                                        	<option value="05:00:00" <?php if($bf_end_time == '05:00 am') { echo "selected"; } ?> >05:00 am</option>
                                                        	<option value="05:30:00" <?php if($bf_end_time == '05:30 am') { echo "selected"; } ?> >05:30 am</option>
                                                        	<option value="06:00:00" <?php if($bf_end_time == '06:00 am') { echo "selected"; } ?> >06:00 am</option>
                                                        	<option value="06:30:00" <?php if($bf_end_time == '06:30 am') { echo "selected"; } ?> >06:30 am</option>
                                                        	<option value="07:00:00" <?php if($bf_end_time == '07:00 am') { echo "selected"; } ?> >07:00 am</option>
                                                        	<option value="07:30:00" <?php if($bf_end_time == '07:30 am') { echo "selected"; } ?> >07:30 am</option>
                                                        	<option value="08:00:00" <?php if($bf_end_time == '08:00 am') { echo "selected"; } ?> >08:00 am</option>
                                                        	<option value="08:30:00" <?php if($bf_end_time == '08:30 am') { echo "selected"; } ?> >08:30 am</option>
                                                        	<option value="09:00:00" <?php if($bf_end_time == '09:00 am') { echo "selected"; } ?> >09:00 am</option>
                                                        	<option value="09:30:00" <?php if($bf_end_time == '09:30 am') { echo "selected"; } ?> >09:30 am</option>
                                                        	<option value="10:00:00" <?php if($bf_end_time == '10:00 am') { echo "selected"; } ?> >10:00 am</option>
                                                        	<option value="10:30:00" <?php if($bf_end_time == '10:30 am') { echo "selected"; } ?> >10:30 am</option>
                                                        	<option value="11:00:00" <?php if($bf_end_time == '11:00 am') { echo "selected"; } ?> >11:00 am</option>
                                                        	<option value="11:30:00" <?php if($bf_end_time == '11:30 am') { echo "selected"; } ?> >11:30 am</option>
                                                        	<option value="12:00:00" <?php if($bf_end_time == '12:00 pm') { echo "selected"; } ?> >12:00 pm</option>
                                                        	<option value="12:30:00" <?php if($bf_end_time == '12:30 pm') { echo "selected"; } ?> >12:30 pm</option>
                                                        	<option value="13:00:00" <?php if($bf_end_time == '13:00 pm') { echo "selected"; } ?> >01:00 pm</option>
                                                        	<option value="13:30:00" <?php if($bf_end_time == '13:30 pm') { echo "selected"; } ?> >01:30 pm</option>
                                                        	<option value="14:00:00" <?php if($bf_end_time == '14:00 pm') { echo "selected"; } ?> >02:00 pm</option>
                                                        	<option value="14:30:00" <?php if($bf_end_time == '14:30 pm') { echo "selected"; } ?> >02:30 pm</option>
                                                        	<option value="15:00:00" <?php if($bf_end_time == '15:00 pm') { echo "selected"; } ?> >03:00 pm</option>
                                                        	<option value="15:30:00" <?php if($bf_end_time == '15:30 pm') { echo "selected"; } ?> >03:30 pm</option>
                                                        	<option value="16:00:00" <?php if($bf_end_time == '16:00 pm') { echo "selected"; } ?> >04:00 pm</option>
                                                        	<option value="16:30:00" <?php if($bf_end_time == '16:30 pm') { echo "selected"; } ?> >04:30 pm</option>
                                                        	<option value="17:00:00" <?php if($bf_end_time == '17:00 pm') { echo "selected"; } ?> >05:00 pm</option>
                                                        	<option value="17:30:00" <?php if($bf_end_time == '17:30 pm') { echo "selected"; } ?> >05:30 pm</option>
                                                        	<option value="18:00:00" <?php if($bf_end_time == '18:00 pm') { echo "selected"; } ?> >06:00 pm</option>
                                                        	<option value="18:30:00" <?php if($bf_end_time == '18:30 pm') { echo "selected"; } ?> >06:30 pm</option>
                                                        	<option value="19:00:00" <?php if($bf_end_time == '19:00 pm') { echo "selected"; } ?> >07:00 pm</option>
                                                        	<option value="19:30:00" <?php if($bf_end_time == '19:30 pm') { echo "selected"; } ?> >07:30 pm</option>
                                                        	<option value="20:00:00" <?php if($bf_end_time == '20:00 pm') { echo "selected"; } ?> >08:00 pm</option>
                                                        	<option value="20:30:00" <?php if($bf_end_time == '20:30 pm') { echo "selected"; } ?> >08:30 pm</option>
                                                        	<option value="21:00:00" <?php if($bf_end_time == '21:00 pm') { echo "selected"; } ?> >09:00 pm</option>
                                                        	<option value="21:30:00" <?php if($bf_end_time == '21:30 pm') { echo "selected"; } ?> >09:30 pm</option>
                                                        	<option value="22:00:00" <?php if($bf_end_time == '22:00 pm') { echo "selected"; } ?> >10:00 pm</option>
                                                        	<option value="22:30:00" <?php if($bf_end_time == '22:30 pm') { echo "selected"; } ?> >10:30 pm</option>
                                                        	<option value="23:00:00" <?php if($bf_end_time == '23:00 pm') { echo "selected"; } ?> >11:00 pm</option>
                                                        	<option value="23:30:00" <?php if($bf_end_time == '23:30 pm') { echo "selected"; } ?> >11:30 pm</option> 
                                                            </select>
                                                            <!------------------------------------My breakfast_time_end start here------------------------------>
                                                         
                                                    </li>
                                                    <li style="width: 28%;">
                                                        <select class="start_time1 required form-control" name="start_time[{{ $vardhbb }}]" placeholder="Start Time" style="width: 42%;">
                                                            <option value="24:00:00" <?php if($lt_start_time == '24:00 am') { echo "selected"; } ?> >12:00 am</option>
                                                        	<option value="00:30:00" <?php if($lt_start_time == '00:30 am') { echo "selected"; } ?> >12:30 am</option>
                                                        	<option value="01:00:00" <?php if($lt_start_time == '01:00 am') { echo "selected"; } ?> >01:00 am</option>
                                                        	<option value="01:30:00" <?php if($lt_start_time == '01:30 am') { echo "selected"; } ?> >01:30 am</option>
                                                        	<option value="02:00:00" <?php if($lt_start_time == '02:00 am') { echo "selected"; } ?> >02:00 am </option>
                                                        	<option value="02:30:00" <?php if($lt_start_time == '02:30 am') { echo "selected"; } ?> >02:30 am</option>
                                                        	<option value="03:00:00" <?php if($lt_start_time == '03:00 am') { echo "selected"; } ?> >03:00 am</option>
                                                        	<option value="03:30:00" <?php if($lt_start_time == '03:30 am') { echo "selected"; } ?> >03:30 am</option>
                                                        	<option value="04:00:00" <?php if($lt_start_time == '04:00 am') { echo "selected"; } ?> >04:00 am</option>
                                                        	<option value="04:30:00" <?php if($lt_start_time == '04:30 am') { echo "selected"; } ?> >04:30 am</option>
                                                        	<option value="05:00:00" <?php if($lt_start_time == '05:00 am') { echo "selected"; } ?> >05:00 am</option>
                                                        	<option value="05:30:00" <?php if($lt_start_time == '05:30 am') { echo "selected"; } ?> >05:30 am</option>
                                                        	<option value="06:00:00" <?php if($lt_start_time == '06:00 am') { echo "selected"; } ?> >06:00 am</option>
                                                        	<option value="06:30:00" <?php if($lt_start_time == '06:30 am') { echo "selected"; } ?> >06:30 am</option>
                                                        	<option value="07:00:00" <?php if($lt_start_time == '07:00 am') { echo "selected"; } ?> >07:00 am</option>
                                                        	<option value="07:30:00" <?php if($lt_start_time == '07:30 am') { echo "selected"; } ?> >07:30 am</option>
                                                        	<option value="08:00:00" <?php if($lt_start_time == '08:00 am') { echo "selected"; } ?> >08:00 am</option>
                                                        	<option value="08:30:00" <?php if($lt_start_time == '08:30 am') { echo "selected"; } ?> >08:30 am</option>
                                                        	<option value="09:00:00" <?php if($lt_start_time == '09:00 am') { echo "selected"; } ?> >09:00 am</option>
                                                        	<option value="09:30:00" <?php if($lt_start_time == '09:30 am') { echo "selected"; } ?> >09:30 am</option>
                                                        	<option value="10:00:00" <?php if($lt_start_time == '10:00 am') { echo "selected"; } ?> >10:00 am</option>
                                                        	<option value="10:30:00" <?php if($lt_start_time == '10:30 am') { echo "selected"; } ?> >10:30 am</option>
                                                        	<option value="11:00:00" <?php if($lt_start_time == '11:00 am') { echo "selected"; } ?> >11:00 am</option>
                                                        	<option value="11:30:00" <?php if($lt_start_time == '11:30 am') { echo "selected"; } ?> >11:30 am</option>
                                                        	<option value="12:00:00" <?php if($lt_start_time == '12:00 pm') { echo "selected"; } ?> >12:00 pm</option>
                                                        	<option value="12:30:00" <?php if($lt_start_time == '12:30 pm') { echo "selected"; } ?> >12:30 pm</option>
                                                        	<option value="13:00:00" <?php if($lt_start_time == '13:00 pm') { echo "selected"; } ?> >01:00 pm</option>
                                                        	<option value="13:30:00" <?php if($lt_start_time == '13:30 pm') { echo "selected"; } ?> >01:30 pm</option>
                                                        	<option value="14:00:00" <?php if($lt_start_time == '14:00 pm') { echo "selected"; } ?> >02:00 pm</option>
                                                        	<option value="14:30:00" <?php if($lt_start_time == '14:30 pm') { echo "selected"; } ?> >02:30 pm</option>
                                                        	<option value="15:00:00" <?php if($lt_start_time == '15:00 pm') { echo "selected"; } ?> >03:00 pm</option>
                                                        	<option value="15:30:00" <?php if($lt_start_time == '15:30 pm') { echo "selected"; } ?> >03:30 pm</option>
                                                        	<option value="16:00:00" <?php if($lt_start_time == '16:00 pm') { echo "selected"; } ?> >04:00 pm</option>
                                                        	<option value="16:30:00" <?php if($lt_start_time == '16:30 pm') { echo "selected"; } ?> >04:30 pm</option>
                                                        	<option value="17:00:00" <?php if($lt_start_time == '17:00 pm') { echo "selected"; } ?> >05:00 pm</option>
                                                        	<option value="17:30:00" <?php if($lt_start_time == '17:30 pm') { echo "selected"; } ?> >05:30 pm</option>
                                                        	<option value="18:00:00" <?php if($lt_start_time == '18:00 pm') { echo "selected"; } ?> >06:00 pm</option>
                                                        	<option value="18:30:00" <?php if($lt_start_time == '18:30 pm') { echo "selected"; } ?> >06:30 pm</option>
                                                        	<option value="19:00:00" <?php if($lt_start_time == '19:00 pm') { echo "selected"; } ?> >07:00 pm</option>
                                                        	<option value="19:30:00" <?php if($lt_start_time == '19:30 pm') { echo "selected"; } ?> >07:30 pm</option>
                                                        	<option value="20:00:00" <?php if($lt_start_time == '20:00 pm') { echo "selected"; } ?> >08:00 pm</option>
                                                        	<option value="20:30:00" <?php if($lt_start_time == '20:30 pm') { echo "selected"; } ?> >08:30 pm</option>
                                                        	<option value="21:00:00" <?php if($lt_start_time == '21:00 pm') { echo "selected"; } ?> >09:00 pm</option>
                                                        	<option value="21:30:00" <?php if($lt_start_time == '21:30 pm') { echo "selected"; } ?> >09:30 pm</option>
                                                        	<option value="22:00:00" <?php if($lt_start_time == '22:00 pm') { echo "selected"; } ?> >10:00 pm</option>
                                                        	<option value="22:30:00" <?php if($lt_start_time == '22:30 pm') { echo "selected"; } ?> >10:30 pm</option>
                                                        	<option value="23:00:00" <?php if($lt_start_time == '23:00 pm') { echo "selected"; } ?> >11:00 pm</option>
                                                        	<option value="23:30:00" <?php if($lt_start_time == '23:30 pm') { echo "selected"; } ?> >11:30 pm</option> 
                                                            </select>
                                                   <!--     {{ Form::text('start_time['.$vardhbb.']', (isset($startime[$vardhbb]) && $startime[$vardhbb])?$startime[$vardhbb]:"", array('class' => 'start_time1 required form-control','placeholder'=>"Start Time")) }}
                                                   -->
                                                    <!------------------------------------My start_time start here------------------------------>
                                                    
                                                     <select class="start_time1 required form-control" name="start_time_end[{{ $vardhbb }}]" placeholder="Start Time" style="width: 42%;">
                                                            <option value="24:00:00" <?php if($lt_end_time == '24:00 am') { echo "selected"; } ?> >12:00 am</option>
                                                        	<option value="00:30:00" <?php if($lt_end_time == '00:30 am') { echo "selected"; } ?> >12:30 am</option>
                                                        	<option value="01:00:00" <?php if($lt_end_time == '01:00 am') { echo "selected"; } ?> >01:00 am</option>
                                                        	<option value="01:30:00" <?php if($lt_end_time == '01:30 am') { echo "selected"; } ?> >01:30 am</option>
                                                        	<option value="02:00:00" <?php if($lt_end_time == '02:00 am') { echo "selected"; } ?> >02:00 am </option>
                                                        	<option value="02:30:00" <?php if($lt_end_time == '02:30 am') { echo "selected"; } ?> >02:30 am</option>
                                                        	<option value="03:00:00" <?php if($lt_end_time == '03:00 am') { echo "selected"; } ?> >03:00 am</option>
                                                        	<option value="03:30:00" <?php if($lt_end_time == '03:30 am') { echo "selected"; } ?> >03:30 am</option>
                                                        	<option value="04:00:00" <?php if($lt_end_time == '04:00 am') { echo "selected"; } ?> >04:00 am</option>
                                                        	<option value="04:30:00" <?php if($lt_end_time == '04:30 am') { echo "selected"; } ?> >04:30 am</option>
                                                        	<option value="05:00:00" <?php if($lt_end_time == '05:00 am') { echo "selected"; } ?> >05:00 am</option>
                                                        	<option value="05:30:00" <?php if($lt_end_time == '05:30 am') { echo "selected"; } ?> >05:30 am</option>
                                                        	<option value="06:00:00" <?php if($lt_end_time == '06:00 am') { echo "selected"; } ?> >06:00 am</option>
                                                        	<option value="06:30:00" <?php if($lt_end_time == '06:30 am') { echo "selected"; } ?> >06:30 am</option>
                                                        	<option value="07:00:00" <?php if($lt_end_time == '07:00 am') { echo "selected"; } ?> >07:00 am</option>
                                                        	<option value="07:30:00" <?php if($lt_end_time == '07:30 am') { echo "selected"; } ?> >07:30 am</option>
                                                        	<option value="08:00:00" <?php if($lt_end_time == '08:00 am') { echo "selected"; } ?> >08:00 am</option>
                                                        	<option value="08:30:00" <?php if($lt_end_time == '08:30 am') { echo "selected"; } ?> >08:30 am</option>
                                                        	<option value="09:00:00" <?php if($lt_end_time == '09:00 am') { echo "selected"; } ?> >09:00 am</option>
                                                        	<option value="09:30:00" <?php if($lt_end_time == '09:30 am') { echo "selected"; } ?> >09:30 am</option>
                                                        	<option value="10:00:00" <?php if($lt_end_time == '10:00 am') { echo "selected"; } ?> >10:00 am</option>
                                                        	<option value="10:30:00" <?php if($lt_end_time == '10:30 am') { echo "selected"; } ?> >10:30 am</option>
                                                        	<option value="11:00:00" <?php if($lt_end_time == '11:00 am') { echo "selected"; } ?> >11:00 am</option>
                                                        	<option value="11:30:00" <?php if($lt_end_time == '11:30 am') { echo "selected"; } ?> >11:30 am</option>
                                                        	<option value="12:00:00" <?php if($lt_end_time == '12:00 pm') { echo "selected"; } ?> >12:00 pm</option>
                                                        	<option value="12:30:00" <?php if($lt_end_time == '12:30 pm') { echo "selected"; } ?> >12:30 pm</option>
                                                        	<option value="13:00:00" <?php if($lt_end_time == '13:00 pm') { echo "selected"; } ?> >01:00 pm</option>
                                                        	<option value="13:30:00" <?php if($lt_end_time == '13:30 pm') { echo "selected"; } ?> >01:30 pm</option>
                                                        	<option value="14:00:00" <?php if($lt_end_time == '14:00 pm') { echo "selected"; } ?> >02:00 pm</option>
                                                        	<option value="14:30:00" <?php if($lt_end_time == '14:30 pm') { echo "selected"; } ?> >02:30 pm</option>
                                                        	<option value="15:00:00" <?php if($lt_end_time == '15:00 pm') { echo "selected"; } ?> >03:00 pm</option>
                                                        	<option value="15:30:00" <?php if($lt_end_time == '15:30 pm') { echo "selected"; } ?> >03:30 pm</option>
                                                        	<option value="16:00:00" <?php if($lt_end_time == '16:00 pm') { echo "selected"; } ?> >04:00 pm</option>
                                                        	<option value="16:30:00" <?php if($lt_end_time == '16:30 pm') { echo "selected"; } ?> >04:30 pm</option>
                                                        	<option value="17:00:00" <?php if($lt_end_time == '17:00 pm') { echo "selected"; } ?> >05:00 pm</option>
                                                        	<option value="17:30:00" <?php if($lt_end_time == '17:30 pm') { echo "selected"; } ?> >05:30 pm</option>
                                                        	<option value="18:00:00" <?php if($lt_end_time == '18:00 pm') { echo "selected"; } ?> >06:00 pm</option>
                                                        	<option value="18:30:00" <?php if($lt_end_time == '18:30 pm') { echo "selected"; } ?> >06:30 pm</option>
                                                        	<option value="19:00:00" <?php if($lt_end_time == '19:00 pm') { echo "selected"; } ?> >07:00 pm</option>
                                                        	<option value="19:30:00" <?php if($lt_end_time == '19:30 pm') { echo "selected"; } ?> >07:30 pm</option>
                                                        	<option value="20:00:00" <?php if($lt_end_time == '20:00 pm') { echo "selected"; } ?> >08:00 pm</option>
                                                        	<option value="20:30:00" <?php if($lt_end_time == '20:30 pm') { echo "selected"; } ?> >08:30 pm</option>
                                                        	<option value="21:00:00" <?php if($lt_end_time == '21:00 pm') { echo "selected"; } ?> >09:00 pm</option>
                                                        	<option value="21:30:00" <?php if($lt_end_time == '21:30 pm') { echo "selected"; } ?> >09:30 pm</option>
                                                        	<option value="22:00:00" <?php if($lt_end_time == '22:00 pm') { echo "selected"; } ?> >10:00 pm</option>
                                                        	<option value="22:30:00" <?php if($lt_end_time == '22:30 pm') { echo "selected"; } ?> >10:30 pm</option>
                                                        	<option value="23:00:00" <?php if($lt_end_time == '23:00 pm') { echo "selected"; } ?> >11:00 pm</option>
                                                        	<option value="23:30:00" <?php if($lt_end_time == '23:30 pm') { echo "selected"; } ?> >11:30 pm</option>
                                                            </select>
                                                    
                                                    <!------------------------------------My start_time end here------------------------------>
                                                    </li>
                                                    <li style="width: 28%;">
                                                       <select class="end_time1 required form-control" name="end_time[{{ $vardhbb }}]" placeholder="End Time" style="width: 42%;">
                                                            <option value="24:00:00" <?php if($dt_start_time == '24:00 am') { echo "selected"; } ?> >12:00 am</option>
                                                        	<option value="00:30:00" <?php if($dt_start_time == '00:30 am') { echo "selected"; } ?> >12:30 am</option>
                                                        	<option value="01:00:00" <?php if($dt_start_time == '01:00 am') { echo "selected"; } ?> >01:00 am</option>
                                                        	<option value="01:30:00" <?php if($dt_start_time == '01:30 am') { echo "selected"; } ?> >01:30 am</option>
                                                        	<option value="02:00:00" <?php if($dt_start_time == '02:00 am') { echo "selected"; } ?> >02:00 am </option>
                                                        	<option value="02:30:00" <?php if($dt_start_time == '02:30 am') { echo "selected"; } ?> >02:30 am</option>
                                                        	<option value="03:00:00" <?php if($dt_start_time == '03:00 am') { echo "selected"; } ?> >03:00 am</option>
                                                        	<option value="03:30:00" <?php if($dt_start_time == '03:30 am') { echo "selected"; } ?> >03:30 am</option>
                                                        	<option value="04:00:00" <?php if($dt_start_time == '04:00 am') { echo "selected"; } ?> >04:00 am</option>
                                                        	<option value="04:30:00" <?php if($dt_start_time == '04:30 am') { echo "selected"; } ?> >04:30 am</option>
                                                        	<option value="05:00:00" <?php if($dt_start_time == '05:00 am') { echo "selected"; } ?> >05:00 am</option>
                                                        	<option value="05:30:00" <?php if($dt_start_time == '05:30 am') { echo "selected"; } ?> >05:30 am</option>
                                                        	<option value="06:00:00" <?php if($dt_start_time == '06:00 am') { echo "selected"; } ?> >06:00 am</option>
                                                        	<option value="06:30:00" <?php if($dt_start_time == '06:30 am') { echo "selected"; } ?> >06:30 am</option>
                                                        	<option value="07:00:00" <?php if($dt_start_time == '07:00 am') { echo "selected"; } ?> >07:00 am</option>
                                                        	<option value="07:30:00" <?php if($dt_start_time == '07:30 am') { echo "selected"; } ?> >07:30 am</option>
                                                        	<option value="08:00:00" <?php if($dt_start_time == '08:00 am') { echo "selected"; } ?> >08:00 am</option>
                                                        	<option value="08:30:00" <?php if($dt_start_time == '08:30 am') { echo "selected"; } ?> >08:30 am</option>
                                                        	<option value="09:00:00" <?php if($dt_start_time == '09:00 am') { echo "selected"; } ?> >09:00 am</option>
                                                        	<option value="09:30:00" <?php if($dt_start_time == '09:30 am') { echo "selected"; } ?> >09:30 am</option>
                                                        	<option value="10:00:00" <?php if($dt_start_time == '10:00 am') { echo "selected"; } ?> >10:00 am</option>
                                                        	<option value="10:30:00" <?php if($dt_start_time == '10:30 am') { echo "selected"; } ?> >10:30 am</option>
                                                        	<option value="11:00:00" <?php if($dt_start_time == '11:00 am') { echo "selected"; } ?> >11:00 am</option>
                                                        	<option value="11:30:00" <?php if($dt_start_time == '11:30 am') { echo "selected"; } ?> >11:30 am</option>
                                                        	<option value="12:00:00" <?php if($dt_start_time == '12:00 pm') { echo "selected"; } ?> >12:00 pm</option>
                                                        	<option value="12:30:00" <?php if($dt_start_time == '12:30 pm') { echo "selected"; } ?> >12:30 pm</option>
                                                        	<option value="13:00:00" <?php if($dt_start_time == '13:00 pm') { echo "selected"; } ?> >01:00 pm</option>
                                                        	<option value="13:30:00" <?php if($dt_start_time == '13:30 pm') { echo "selected"; } ?> >01:30 pm</option>
                                                        	<option value="14:00:00" <?php if($dt_start_time == '14:00 pm') { echo "selected"; } ?> >02:00 pm</option>
                                                        	<option value="14:30:00" <?php if($dt_start_time == '14:30 pm') { echo "selected"; } ?> >02:30 pm</option>
                                                        	<option value="15:00:00" <?php if($dt_start_time == '15:00 pm') { echo "selected"; } ?> >03:00 pm</option>
                                                        	<option value="15:30:00" <?php if($dt_start_time == '15:30 pm') { echo "selected"; } ?> >03:30 pm</option>
                                                        	<option value="16:00:00" <?php if($dt_start_time == '16:00 pm') { echo "selected"; } ?> >04:00 pm</option>
                                                        	<option value="16:30:00" <?php if($dt_start_time == '16:30 pm') { echo "selected"; } ?> >04:30 pm</option>
                                                        	<option value="17:00:00" <?php if($dt_start_time == '17:00 pm') { echo "selected"; } ?> >05:00 pm</option>
                                                        	<option value="17:30:00" <?php if($dt_start_time == '17:30 pm') { echo "selected"; } ?> >05:30 pm</option>
                                                        	<option value="18:00:00" <?php if($dt_start_time == '18:00 pm') { echo "selected"; } ?> >06:00 pm</option>
                                                        	<option value="18:30:00" <?php if($dt_start_time == '18:30 pm') { echo "selected"; } ?> >06:30 pm</option>
                                                        	<option value="19:00:00" <?php if($dt_start_time == '19:00 pm') { echo "selected"; } ?> >07:00 pm</option>
                                                        	<option value="19:30:00" <?php if($dt_start_time == '19:30 pm') { echo "selected"; } ?> >07:30 pm</option>
                                                        	<option value="20:00:00" <?php if($dt_start_time == '20:00 pm') { echo "selected"; } ?> >08:00 pm</option>
                                                        	<option value="20:30:00" <?php if($dt_start_time == '20:30 pm') { echo "selected"; } ?> >08:30 pm</option>
                                                        	<option value="21:00:00" <?php if($dt_start_time == '21:00 pm') { echo "selected"; } ?> >09:00 pm</option>
                                                        	<option value="21:30:00" <?php if($dt_start_time == '21:30 pm') { echo "selected"; } ?> >09:30 pm</option>
                                                        	<option value="22:00:00" <?php if($dt_start_time == '22:00 pm') { echo "selected"; } ?> >10:00 pm</option>
                                                        	<option value="22:30:00" <?php if($dt_start_time == '22:30 pm') { echo "selected"; } ?> >10:30 pm</option>
                                                        	<option value="23:00:00" <?php if($dt_start_time == '23:00 pm') { echo "selected"; } ?> >11:00 pm</option>
                                                        	<option value="23:30:00" <?php if($dt_start_time == '23:30 pm') { echo "selected"; } ?> >11:30 pm</option> 
                                                            </select>
                                               <!--         {{ Form::text('end_time['.$vardhbb.']', (isset($endtime[$vardhbb])&& $endtime[$vardhbb])?$endtime[$vardhbb]:"", array('class' => 'end_time required form-control','placeholder'=>"End Time")) }}
                                                    -->
                                                    <!------------------------------------My end_time_end end here------------------------------>
                                                    <select class="end_time1 required form-control" name="end_time_end[{{ $vardhbb }}]" placeholder="End Time" style="width: 42%;">
                                                            <option value="24:00:00" <?php if($dt_end_time == '24:00 am') { echo "selected"; } ?> >12:00 am</option>
                                                        	<option value="00:30:00" <?php if($dt_end_time == '00:30 am') { echo "selected"; } ?> >12:30 am</option>
                                                        	<option value="01:00:00" <?php if($dt_end_time == '01:00 am') { echo "selected"; } ?> >01:00 am</option>
                                                        	<option value="01:30:00" <?php if($dt_end_time == '01:30 am') { echo "selected"; } ?> >01:30 am</option>
                                                        	<option value="02:00:00" <?php if($dt_end_time == '02:00 am') { echo "selected"; } ?> >02:00 am </option>
                                                        	<option value="02:30:00" <?php if($dt_end_time == '02:30 am') { echo "selected"; } ?> >02:30 am</option>
                                                        	<option value="03:00:00" <?php if($dt_end_time == '03:00 am') { echo "selected"; } ?> >03:00 am</option>
                                                        	<option value="03:30:00" <?php if($dt_end_time == '03:30 am') { echo "selected"; } ?> >03:30 am</option>
                                                        	<option value="04:00:00" <?php if($dt_end_time == '04:00 am') { echo "selected"; } ?> >04:00 am</option>
                                                        	<option value="04:30:00" <?php if($dt_end_time == '04:30 am') { echo "selected"; } ?> >04:30 am</option>
                                                        	<option value="05:00:00" <?php if($dt_end_time == '05:00 am') { echo "selected"; } ?> >05:00 am</option>
                                                        	<option value="05:30:00" <?php if($dt_end_time == '05:30 am') { echo "selected"; } ?> >05:30 am</option>
                                                        	<option value="06:00:00" <?php if($dt_end_time == '06:00 am') { echo "selected"; } ?> >06:00 am</option>
                                                        	<option value="06:30:00" <?php if($dt_end_time == '06:30 am') { echo "selected"; } ?> >06:30 am</option>
                                                        	<option value="07:00:00" <?php if($dt_end_time == '07:00 am') { echo "selected"; } ?> >07:00 am</option>
                                                        	<option value="07:30:00" <?php if($dt_end_time == '07:30 am') { echo "selected"; } ?> >07:30 am</option>
                                                        	<option value="08:00:00" <?php if($dt_end_time == '08:00 am') { echo "selected"; } ?> >08:00 am</option>
                                                        	<option value="08:30:00" <?php if($dt_end_time == '08:30 am') { echo "selected"; } ?> >08:30 am</option>
                                                        	<option value="09:00:00" <?php if($dt_end_time == '09:00 am') { echo "selected"; } ?> >09:00 am</option>
                                                        	<option value="09:30:00" <?php if($dt_end_time == '09:30 am') { echo "selected"; } ?> >09:30 am</option>
                                                        	<option value="10:00:00" <?php if($dt_end_time == '10:00 am') { echo "selected"; } ?> >10:00 am</option>
                                                        	<option value="10:30:00" <?php if($dt_end_time == '10:30 am') { echo "selected"; } ?> >10:30 am</option>
                                                        	<option value="11:00:00" <?php if($dt_end_time == '11:00 am') { echo "selected"; } ?> >11:00 am</option>
                                                        	<option value="11:30:00" <?php if($dt_end_time == '11:30 am') { echo "selected"; } ?> >11:30 am</option>
                                                        	<option value="12:00:00" <?php if($dt_end_time == '12:00 pm') { echo "selected"; } ?> >12:00 pm</option>
                                                        	<option value="12:30:00" <?php if($dt_end_time == '12:30 pm') { echo "selected"; } ?> >12:30 pm</option>
                                                        	<option value="13:00:00" <?php if($dt_end_time == '13:00 pm') { echo "selected"; } ?> >01:00 pm</option>
                                                        	<option value="13:30:00" <?php if($dt_end_time == '13:30 pm') { echo "selected"; } ?> >01:30 pm</option>
                                                        	<option value="14:00:00" <?php if($dt_end_time == '14:00 pm') { echo "selected"; } ?> >02:00 pm</option>
                                                        	<option value="14:30:00" <?php if($dt_end_time == '14:30 pm') { echo "selected"; } ?> >02:30 pm</option>
                                                        	<option value="15:00:00" <?php if($dt_end_time == '15:00 pm') { echo "selected"; } ?> >03:00 pm</option>
                                                        	<option value="15:30:00" <?php if($dt_end_time == '15:30 pm') { echo "selected"; } ?> >03:30 pm</option>
                                                        	<option value="16:00:00" <?php if($dt_end_time == '16:00 pm') { echo "selected"; } ?> >04:00 pm</option>
                                                        	<option value="16:30:00" <?php if($dt_end_time == '16:30 pm') { echo "selected"; } ?> >04:30 pm</option>
                                                        	<option value="17:00:00" <?php if($dt_end_time == '17:00 pm') { echo "selected"; } ?> >05:00 pm</option>
                                                        	<option value="17:30:00" <?php if($dt_end_time == '17:30 pm') { echo "selected"; } ?> >05:30 pm</option>
                                                        	<option value="18:00:00" <?php if($dt_end_time == '18:00 pm') { echo "selected"; } ?> >06:00 pm</option>
                                                        	<option value="18:30:00" <?php if($dt_end_time == '18:30 pm') { echo "selected"; } ?> >06:30 pm</option>
                                                        	<option value="19:00:00" <?php if($dt_end_time == '19:00 pm') { echo "selected"; } ?> >07:00 pm</option>
                                                        	<option value="19:30:00" <?php if($dt_end_time == '19:30 pm') { echo "selected"; } ?> >07:30 pm</option>
                                                        	<option value="20:00:00" <?php if($dt_end_time == '20:00 pm') { echo "selected"; } ?> >08:00 pm</option>
                                                        	<option value="20:30:00" <?php if($dt_end_time == '20:30 pm') { echo "selected"; } ?> >08:30 pm</option>
                                                        	<option value="21:00:00" <?php if($dt_end_time == '21:00 pm') { echo "selected"; } ?> >09:00 pm</option>
                                                        	<option value="21:30:00" <?php if($dt_end_time == '21:30 pm') { echo "selected"; } ?> >09:30 pm</option>
                                                        	<option value="22:00:00" <?php if($dt_end_time == '22:00 pm') { echo "selected"; } ?> >10:00 pm</option>
                                                        	<option value="22:30:00" <?php if($dt_end_time == '22:30 pm') { echo "selected"; } ?> >10:30 pm</option>
                                                        	<option value="23:00:00" <?php if($dt_end_time == '23:00 pm') { echo "selected"; } ?> >11:00 pm</option>
                                                        	<option value="23:30:00" <?php if($dt_end_time == '23:30 pm') { echo "selected"; } ?> >11:30 pm</option> 
                                                            </select>
                                                    
                                                    <!------------------------------------My end_time_end end here------------------------------>
                                                    
                                                    </li>
                                                </ul>
                                            <?php } 
                                            
                                           // echo "<pre>";
                                            //print_r($breakfast_time);
                                           // echo "------------breakfast time--------";
                                            //print_r($breakfast);
                                            
                                            
                                            //print_r($startime);
                                           // echo "------------start time--------";
                                           // print_r($vrrcy);
                                            
                                            //print_r($endtime);
                                           // echo "------------end time--------";
                                            
                                          //  print_r($endty);
                                            
                                            ?>

                                        </div>
                                    </div> 
                                    <div class="labeles">
                                    
                                        <?php
                                         foreach(Config::get('constant') as $key => $c)
                                            {
                                                if($key == $currency)
                                                {
                                                    ?>
                                                    <!--Minimum Order ({{CURR}}) <span class="red-mark"> *</span>-->
                                                     Minimum Order (<?php echo $c;?>) <span class="red-mark"> *</span>
                                               <?php  }
                                            }
                                        ?>
                                        
                                    </div>

                                    <div class="detail" > 
                                        {{ Form::text('minimum_order', Input::old('minimum_order'), array('class' => 'required positiveNumber form-control','placeholder'=>"Minimum Order")) }}
                                    </div> 
                                    <div class="labeles">
                                        <?php
                                         foreach(Config::get('constant') as $key => $c)
                                            {
                                                if($key == $currency)
                                                {
                                                    ?>
                                                    <!--Minimum Order ({{CURR}}) <span class="red-mark"> *</span>-->
                                                     Estimated Cost for two people (<?php echo $c;?>) <span class="red-mark"> *</span>
                                               <?php  }
                                            }
                                        ?>
                                       <!--Estimated Cost for two people ({{CURR}}) <span class="red-mark"> *</span>-->
                                    </div>

                                    <div class="detail" > 
                                        {{ Form::text('estimated_cost', Input::old('estimated_cost'), array('class' => 'required positiveNumber form-control','placeholder'=>"Estimated Cost for two")) }}
                                    </div> 
                                    <div class="labeles">
                                        Average Delivery Time (Minutes)<span class="red-mark"> *</span>
                                    </div>
                                    <div class="detail" > 
                                        {{ Form::text('average_time', Input::old('average_time'), array('class' => 'required form-control number','placeholder'=>"Average Delivery Time")) }}
                                    </div> 

                                    <div class="labeles">
                                        Meal type <span class="red-mark"> *</span>
                                    </div>
                                    <?php
                                    $mealtype_array = array(
                                        '' => 'Please Select'
                                    );

                                    $mealtype = Mealtype::orderBy('name', 'asc')->where('status', "1")->lists('name', 'id');
                                    if (!empty($mealtype)) {
                                        foreach ($mealtype as $key => $val)
                                            $mealtype[$key] = ucfirst($val);
                                    }

//                                    global $mealtype;
//                                    $array = array(
//                                        'Meals' => 'Meals',
//                                        'Event' => 'Event',
//                                        'Fast Homemade Food' => 'Fast Homemade Food',
//                                    );
                                    $old = Input::old('catering_type') ? Input::old('catering_type') : explode(",", @$opening_hours->catering_type);
//                                    $old = explode(",", $old);
                                    ?>
                                    <div class="detail" > 
                                        {{ Form::select('catering_type[]',$mealtype, $old, array('class' => 'required form-control','multiple'=>'multiple' ,'onchange'=>"chkselect(this.value);",'id'=>"selectop")) }}
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