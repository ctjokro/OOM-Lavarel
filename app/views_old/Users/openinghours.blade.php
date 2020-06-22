@extends('layout')
@section('content')
{{ HTML::style('public/js/front/timepicker/jquery.timepicker.css'); }}
{{ HTML::style('public/js/chosen/chosen.css'); }}
{{ HTML::script('public/js/front/timepicker/jquery.timepicker.js'); }}
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
                    <div class="informetion_top">
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
                                            <label class="cb-enable {{$opening_hours->open_close?'selected':''}}"><span onclick="valueChanged(1);">On</span></label>
                                            <label class="cb-disable  {{$opening_hours->open_close?'':'selected'}}"><span onclick="valueChanged(0);">Off</span></label>
                                            {{ Form::checkbox('open_close', 1, ($opening_hours->open_close?TRUE:FALSE), ['id' => 'checkbox', 'class'=>'checkbox']) }}
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
                                        {{ Form::select('open_days[]', $array,  explode(',', $opening_hours->open_days), array('multiple' => true, 'class'=>'chzn-select')); }}
                                    </div>
                                    <div class="labeles">
                                        Opening Hours <span class="red-mark"> *</span>
                                    </div>
                                    <div class="detail" id="detail_field_page"> 
                                        <div class="menu_table menu_tablese menu_tablesefgt">

                                            <?php
                                            $openday = explode(',', $opening_hours->open_days);
                                            $vrrcy = explode(',', $opening_hours->start_time);
                                            $endty = explode(',', $opening_hours->end_time);

                                            $startime = array_combine($openday, $vrrcy);
                                            $endtime = array_combine($openday, $endty);


                                            foreach ($array as $vardhbb => $value) {
                                                $bloack = "block";
                                                if (!in_array($vardhbb, $openday)) {
                                                    $bloack = "none";
                                                }
                                                ?>
                                                <ul id="<?php echo $vardhbb; ?>_div" style="display: <?php echo $bloack; ?>">
                                                    <li>
                                                        <label> <?php echo $value; ?> </label>
                                                    </li>
                                                    <li>
                                                        {{ Form::hidden('open_days_label[]', $vardhbb, array('class' => 'start_time required form-control','placeholder'=>"Start Time")) }}
                                                        {{ Form::text('start_time['.$vardhbb.']', (isset($startime[$vardhbb]) && $startime[$vardhbb])?$startime[$vardhbb]:"", array('class' => 'start_time required form-control','placeholder'=>"Start Time")) }}
                                                    </li>
                                                    <li>
                                                        {{ Form::text('end_time['.$vardhbb.']', (isset($endtime[$vardhbb])&& $endtime[$vardhbb])?$endtime[$vardhbb]:"", array('class' => 'end_time required form-control','placeholder'=>"End Time")) }}
                                                    </li>
                                                </ul>
                                            <?php } ?>

                                        </div>
                                    </div> 
                                    <div class="labeles">
                                        Minimum Order ({{CURR}}) <span class="red-mark"> *</span>
                                    </div>

                                    <div class="detail" > 
                                        {{ Form::text('minimum_order', Input::old('minimum_order'), array('class' => 'required positiveNumber form-control','placeholder'=>"Minimum Order")) }}
                                    </div> 
                                    <div class="labeles">
                                        Estimated Cost for two people ({{CURR}}) <span class="red-mark"> *</span>
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
                                    $old = Input::old('catering_type') ? Input::old('catering_type') : explode(",", $opening_hours->catering_type);
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
            $('.chzn-select').attr('disabled', 'disabled');
            $('.chzn-container').addClass('dcbl_dv');
            $('.chzn-select').prop('disabled', true).trigger("liszt:updated");
            $('.hint-onoff_highlight').show();
        }else{
            $('.menu_tablesefgt input').removeAttr('disabled', 'disabled');
            $('.chzn-select').removeAttr('disabled', 'disabled');
            $('.chzn-container').removeClass('dcbl_dv');
            $('.chzn-select').prop('disabled', false).trigger("liszt:updated");
            $('.hint-onoff_highlight').hide();

        }
    }
</script>


<?php if ($opening_hours->open_close == 0) {
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.menu_tablesefgt input').attr('disabled', 'disabled');
            $('.chzn-select').attr('disabled', 'disabled');
            $('.chzn-select').prop('disabled', true).trigger("liszt:updated");
            $('.hint-onoff_highlight').show();

        });
    </script>
    <?php }
?>


@stop


