@extends('layout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css">

<script>
$(function () {
    $(".start_time").datepicker({
        defaultDate: "+1w",
        changeMonth: true,dateFormat: 'yy-mm-dd',
        numberOfMonths: 1, minDate: 0,
        onClose: function (selectedDate) {
            $(".end_time").datepicker("option", "minDate", selectedDate);
        }
    });
    $(".end_time").datepicker({
        defaultDate: "+1w", minDate: 0,
        changeMonth: true,dateFormat: 'yy-mm-dd',
        numberOfMonths: 1,
        onClose: function (selectedDate) {
            $(".start_time").datepicker("option", "maxDate", selectedDate);
        }
    });
});

 function  generatepromo(){
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        for( var i=0; i < 10; i++ ){
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }    

        $('#code').val(text);
    }
    
</script>
<script type="text/javascript">
$(document).ready(function () {
    $("#myform").validate();
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
                        <div class="tatils">Add Coupon code </div>
                        <div class="pery">
                            <div id="formloader" class="formloader" style="display: none;">
                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                            </div>
                            {{ View::make('elements.frontEndActionMessage')->render() }}
                            {{ Form::open(array('url' => '/user/addcouponcode', 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                            <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>

                            


                        <div class="form_group">
                            {{ HTML::decode(Form::label('code', "Coupon Code <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                           <div class="in_upt">
                                {{ Form::text('code', Input::old('code'), array('class' => 'required form-control')) }}
                                 <div onclick="generatepromo()" style="cursor: pointer;background:#78cd51; cursor: pointer;
                                
                                padding: 2px;
                                width: 200px; color: #fff;  text-align: center;">Generate Coupon Code</div>
                            </div>
                        </div>
                        <div class="form_group">
                            {{ HTML::decode(Form::label('discount', "Discount (%) <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                           <div class="in_upt">
                                {{ Form::text('discount',Input::old('discount'), array('class' => 'required form-control number', 'maxlength'=>2, 'max'=>99)) }} 
                            </div>
                        </div>


                        <div class="form_group">
                            {{ HTML::decode(Form::label('start_time', "Start Time <span class='require'>*</span>",array('class'=>"control-label col-lg-2 start_time"))) }}
                           <div class="in_upt">
                                {{ Form::text('start_time',Input::old('start_time'), array('class' => 'required form-control start_time',  'max'=>100)) }} 
                            </div>
                        </div>
                        <div class="form_group">
                            {{ HTML::decode(Form::label('end_time', "End Time <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                           <div class="in_upt">
                                {{ Form::text('end_time',Input::old('end_time'), array('class' => 'required form-control end_time')) }} 
                            </div>
                        </div>

                           
                            <div class="form_group input_bxxs">
                                <label>&nbsp;</label>
                                <div class="in_upt in_upt_res">
                                    {{ Form::submit('Submit', array('class' => "btn btn-primary")) }}
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
@stop


