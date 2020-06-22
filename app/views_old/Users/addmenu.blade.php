@extends('layout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
{{ HTML::style('public/js/chosen/chosen.css'); }}
<script type="text/javascript">
    $(document).ready(function() {
        $.validator.addMethod('positiveNumber',
        function (value) { 
            return Number(value) > 0;
        }, 'Enter a positive number.');
        $.validator.addMethod("pass", function(value, element) {
            return  this.optional(element) || (/.{8,}/.test(value) && /((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,20})/.test(value));
        }, "Password minimum length must be 8 characters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.");


        $("#myform").validate({
            submitHandler: function(form) {
                this.checkForm();

                if (this.valid()) { // checks form for validity
                    //                    $('#formloader').show();
                    this.submit();
                } else {
                    return false;
                }
            }
        });
        $(document).on("click", ".counter_number", function() {
            var type = $(this).attr("alt");
            var value = $('#preparation_time').val();
            value = value?parseInt(value):0;
            if(type == 'minus') {
                $('#preparation_time').val((value-1 <0)?0:(value-1));
            } else {
                if(value >= 100)
                    $('#preparation_time').val(value);
                else
                    $('#preparation_time').val(value+1);
            }
        })
    });
    
    $(document).ready(function () {
        $(".cb-enable").click(function () {
            var parent = $(this).parents('.switch');
            $('.cb-disable', parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox', parent).attr('checked', true);
            $('.checkbox', parent).attr('value', '1');
        });
        
        $(".cb-disable").click(function () {
            var parent = $(this).parents('.switch');
            $('.cb-enable', parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox', parent).attr('checked', false);
            $('.checkbox', parent).attr('value', '0');
        });
        
        $(".cb-enable2").click(function () {
            var parent = $(this).parents('.switch');
            $('.cb-disable2', parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox2', parent).attr('checked', true);
            $('.checkbox2', parent).attr('value', '1');
        });
        
        $(".cb-disable2").click(function () {
            var parent = $(this).parents('.switch');
            $('.cb-enable2', parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox2', parent).attr('checked', false);
            $('.checkbox2', parent).attr('value', '0');
        });
        
         $(".cb-enable3").click(function () {
            var parent = $(this).parents('.switch');
            $('.cb-disable3', parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox3', parent).attr('checked', true);
            $('.checkbox3', parent).attr('value', '1');
        });
        
        $(".cb-disable3").click(function () {
            var parent = $(this).parents('.switch');
            $('.cb-enable3', parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox3', parent).attr('checked', false);
            $('.checkbox3', parent).attr('value', '0');
        });
        
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
    
    .cb-enable2, .cb-disable2, .cb-enable2 span, .cb-disable2 span { background: url(<?php echo HTTP_PATH . "public/css/front/" ?>switch.gif) repeat-x; display: block; float: left; }
    .cb-enable2 span, .cb-disable2 span { line-height: 30px; display: block; background-repeat: no-repeat; font-weight: bold; }
    .cb-enable2 span { background-position: left -90px; padding: 0 10px; }
    .cb-disable2 span { background-position: right -180px;padding: 0 10px; }
    .cb-disable2.selected { background-position: 0 -30px; }
    .cb-disable2.selected span { background-position: right -210px; color: #fff; }
    .cb-enable2.selected { background-position: 0 -60px; }
    .cb-enable2.selected span { background-position: left -150px; color: #fff; }
    .switch label { cursor: pointer; }
    .switch input { display: none; }
    
    .cb-enable3, .cb-disable3, .cb-enable3 span, .cb-disable3 span { background: url(<?php echo HTTP_PATH . "public/css/front/" ?>switch.gif) repeat-x; display: block; float: left; }
    .cb-enable3 span, .cb-disable3 span { line-height: 30px; display: block; background-repeat: no-repeat; font-weight: bold; }
    .cb-enable3 span { background-position: left -90px; padding: 0 10px; }
    .cb-disable3 span { background-position: right -180px;padding: 0 10px; }
    .cb-disable3.selected { background-position: 0 -30px; }
    .cb-disable3.selected span { background-position: right -210px; color: #fff; }
    .cb-enable3.selected { background-position: 0 -60px; }
    .cb-enable3.selected span { background-position: left -150px; color: #fff; }
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
                        <div class="tatils">Add Menu </div>
                        <div class="pery">
                            <div id="formloader" class="formloader" style="display: none;">
                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                            </div>
                            {{ View::make('elements.frontEndActionMessage')->render() }}
                            {{ Form::open(array('url' => '/user/addmenu', 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                            <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>
                            
                            <div class="fill_data_left col-lg-12" style="margin-top: 15px;">
                            <div class="col-lg-4">
                            <div class="label">Spicy <span class="red-mark"> *</span></div>
                                    <div class="detail">

                                        <p class="field switch">
                                            <label class="cb-enable "><span>Yes</span></label>
                                            <label class="cb-disable selected"><span>No</span></label>
                                            {{ Form::checkbox('spicy', 0, FALSE, ['id' => 'checkbox', 'class'=>'checkbox']) }}
                                        </p>
                                        <div class="hint-onoff">
                                            If you choose yes than food is marked as a spicy and chili icon will be visible in the menu listing!
                                        </div>
                                    </div>
                            </div>
                            <div class="col-lg-4">
                            <div class="label">Non-veg <span class="red-mark"> *</span></div>
                                    <div class="detail">

                                        <p class="field switch">
                                            <label class="cb-enable2 "><span>Yes</span></label>
                                            <label class="cb-disable2 selected"><span>No</span></label>
                                            {{ Form::checkbox('non_veg', 0, FALSE, ['id' => 'checkbox2', 'class'=>'checkbox2']) }}
                                        </p>
                                        <div class="hint-onoff">
                                            If you choose yes than food is marked as a nog-veg and mentioned in the menu listing.
                                        </div>
                                    </div>
                            </div>
                                <div class="col-lg-4">
                            <div class="label">Make a Deal <span class="red-mark"> *</span></div>
                                    <div class="detail">

                                        <p class="field switch">
                                            <label class="cb-enable3 "><span>Yes</span></label>
                                            <label class="cb-disable3 selected"><span>No</span></label>
                                            {{ Form::checkbox('deal', 0, FALSE, ['id' => 'checkbox3', 'class'=>'checkbox3']) }}
                                        </p>
                                        <div class="hint-onoff">
                                            If you choose yes than food is marked as a deal and display in separate section called best deals.
                                        </div>
                                    </div>
                            </div>
                            </div>
                            
                            <div class="form_group">
                                {{ HTML::decode(Form::label('cuisine', "Cuisine <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    <?php
                                    $cuisine_array = array(
                                        '' => 'Please Select'
                                    );
                                    $cuisine = Cuisine::orderBy('name')->where('status', "=", "1")->lists('name', 'id');
                                    if (!empty($cuisine)) {
                                        foreach ($cuisine as $key => $val)
                                            $cuisine_array[$key] = $val;
                                    }
                                    ?>
                                    {{ Form::select('cuisine', $cuisine_array, Input::old("cuisine"), array('class' => 'form-control required', 'id'=>'cuisine')) }}
                                </div>
                            </div>


                            <div class="form_group">
                                {{ HTML::decode(Form::label('item_name', "Item Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{  Form::text('item_name', Input::old("item_name"),  array('class' => 'required form-control','id'=>"item_name"))}}
                                </div>
                            </div>

                            <div class="form_group">
                                {{ HTML::decode(Form::label('price', "Price(".CURR.") <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{  Form::text('price', Input::old("price"),  array('class' => 'required positiveNumber number form-control','id'=>"price"))}}
<!--                                    <div class="_vttb">
                                        <label for="checkbox_half">Half</label> {{ Form::checkbox('half_plate', 1,FALSE,  ['id' => 'checkbox_half', 'class'=>'checkbox']) }} <span style="display: none" id="half_price_span" class="feildv">{{  Form::text('half_price', Input::old("half_price"),  array('class' => 'required positiveNumber number form-control','id'=>"half_price"))}}</span>
                                        <label for="checkbox_quarter">Quarter</label> {{ Form::checkbox('quarter_plate', 1, FALSE, ['id' => 'checkbox_quarter', 'class'=>'checkbox']) }} <span style="display: none" id="quarter_price_span" class="feildv">{{  Form::text('quarter_price', Input::old("quarter_price"),  array('class' => 'required positiveNumber number form-control','id'=>"quarter_price"))}}</span>
                                    </div>-->
                            
                                </div>
                                
                            </div>
                            <div class="_addc">    
                                <h4>Variant </h4>
                                <div id="itemsvarient" class="add_ons"></div>
                                <button type="button" class="add_field_button2 btn btn-primary">Add</button>
                            </div>

<!--                            <div class="form_group">
                                {{ HTML::decode(Form::label('preparation_time', "Preparation Time (Hours) <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt in_fot">
                                    {{  Form::text('preparation_time', Input::old("preparation_time"),  array('maxlength'=>4, 'class' => 'required positiveNumber preparation_time number form-control','id'=>"preparation_time"))}}
                                    <div class="counter-buttons">
                                        <div class="inner-button counter_number" alt="minus">
                                            <i class="fa  fa-minus-square "></i>
                                        </div> 
                                        <div class="inner-buttonright counter_number" alt="plus">
                                            <i class="fa fa-plus-square "></i>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
                            {{  Form::hidden('preparation_time', '0',  array('maxlength'=>4, 'class' => 'required positiveNumber preparation_time number form-control','id'=>"preparation_time"))}}


                            <div class="form_group">
                                {{ HTML::decode(Form::label('description', "Menu Description",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{  Form::textarea('description', Input::old("description"),  array('class' => 'form-control','id'=>"description" ,"maxlength"=>"250"))}}
                                </div>
                            </div>

<!--                            <div class="form_group">
                                {{ HTML::decode(Form::label('submenu', "Sub Menu",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{  Form::textarea('submenu',  Input::old("submenu"),  array('class' => 'form-control','id'=>"submenu"))}}
                                    <p class="help-block">Please use comma(,) to separate submenu items.</p>
                                </div>-->
                            <div class="form_group">
                                {{ HTML::decode(Form::label('image', "Item Image",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{ Form::file('image', array('class'=>"",'id'=>"image")); }}
                                    <p class="help-block">Supported File Types: gif, jpg, jpeg, png. Max size 2MB.</p>
                                </div>
                            </div>
                            <div class="_addc">    
                                <h4>Add-ons </h4>
                                <div id="items" class="add_ons"></div>
                                <button type="button" class="add_field_button btn btn-primary">Add</button>
                            </div>
                            <div class="form_group add_btn ">
                                <label></label>
                                <div class="add_btn_right"></div>
                            </div>

                        
                           
                            <div class="form_group input_bxxs">
                                <label>&nbsp;</label>
                                <div class="in_upt in_upt_res">
                                    {{ Form::submit('Submit', array('class' => "btn btn-primary",'id'=>'bubbb')) }}
                                    {{ Form::reset('Reset', array('class' => "btn btn-default")) }}
                                </div>
                            </div>
                            </div>




                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
$('body').delegate('change','.checkbox',function(e){
    var id = $(this).attr('id');
    var Cid = id.split('_');
    if($(this).is(":checked") == true){
       $('#'+Cid[1]+'_price_span').show();
    } else{
          $('#'+Cid[1]+'_price_span').hide();
     }
});

$(document).ready(function() {
    var max_fields = 20; //maximum input boxes allowed
    var wrapper = $("#items"); //Fields wrapper
    var wrapper2 = $("#itemsvarient"); //Fields wrapper
    var add_button = $(".add_field_button"); //Add button ID
    var add_button2 = $(".add_field_button2"); //Add button ID

    var x = 1; //initlal text box count
    var y = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    if(y < max_fields){ //max input box allowed
    y++; //text box increment
    
        $(wrapper).append('<div class="ekdiv"><div class="form_group"> {{ HTML::decode(Form::label("addonname", "Name <span class=\'require\'>*</span>",array("class"=>"control-label col-lg-2"))) }} <div class="in_upt"><input  class="required form-control newclassvs" name="addon_name[]" type="text"></div></div><div class="form_group"> {{ HTML::decode(Form::label("addonname", "Price(USD) <span class=\'require\'>*</span>",array("class"=>"control-label col-lg-2"))) }}<div class="in_upt"><input  class="required positiveNumber number form-control newclassvs" name="addon_price[]" type="text" data-rule-required="true"></div></div>' + '<a href="#" class="remove_field"><i class="fa fa-times" aria-hidden="true"></i> Remove</a></div>'); //add input box
       // $(wrapper).append('<div class="form-group"><label for="title">Author Email:</label>' +'<input class="form-control col-md-11" id="author_email" type="email" placeholder=""name="author"/>' +'<a href="#" class="remove_field"><i class="fa fa-times"></a></div>'); //add input box
    }
    });
    $(add_button2).click(function(e){ //on add input button click
    e.preventDefault();
    if(x < max_fields){ //max input box allowed
    x++; //text box increment
    
        $(wrapper2).append('<div class="ekdiv"><div class="form_group"> {{ HTML::decode(Form::label("variantname", "Variant Name <span class=\'require\'>*</span>",array("class"=>"control-label col-lg-2"))) }}<div class="in_upt"><input  class="required form-control newclassvs" name="variant_name[]" type="text"></div></div><div class="form_group"> {{ HTML::decode(Form::label("addonname", "Price(USD) <span class=\'require\'>*</span>",array("class"=>"control-label col-lg-2"))) }}<div class="in_upt"><input  class="required positiveNumber number form-control newclassvs" name="variant_price[]" type="text" data-rule-required="true"></div></div>' + '<a href="#" class="remove_field2 remove_field"><i class="fa fa-times" aria-hidden="true"></i> Remove</a></div>'); //add input box
       // $(wrapper).append('<div class="form-group"><label for="title">Author Email:</label>' +'<input class="form-control col-md-11" id="author_email" type="email" placeholder=""name="author"/>' +'<a href="#" class="remove_field"><i class="fa fa-times"></a></div>'); //add input box
    }
    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove field
    e.preventDefault(); $(this).parent('div').remove(); x--;
    })
    $(wrapper2).on("click",".remove_field2", function(e){ //user click on remove field
    e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});

$('body').delegate('click','#bubbb',function(w){

        var error = 0;
        $(".newclassvs").each(function(index, element){
            if(element.value == '' || element.value <= 0){
               
                error = 1;
                
            } 
        });
        
        if(error){
           
            alert('Please enter all the required fields.'); return false;
            w.preventdefault();
        }
});
</script>
@stop

