@extends('layout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

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
                    $('#formloader').show();
                    this.submit();
                } else {
                    return false;
                }
            }
        });
        
        $("#city").change(function() {
            $("#area").load("<?php echo HTTP_PATH . "customer/loadarea/" ?>"+$(this).val()+"/0");
        })
        
<?php
if ($detail->city) {
    ?> 
                $("#area").load("<?php echo HTTP_PATH . "customer/loadarea/" . $detail->city . "/" . $detail->area ?>");
    <?php
}
?>
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
                        <div class="tatils">Edit Address Details</div>
                        <div class="pery">
                            <div id="formloader" class="formloader" style="display: none;">
                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                            </div>
                            {{ View::make('elements.frontEndActionMessage')->render() }}
                            {{ Form::model($detail, array('url' => '/user/editaddress/'.$detail->slug, 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                            <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>


                            <div class="multiple-fields">

                                <div class="form_group">
                                    <div class="form_group_left">
                                        {{ HTML::decode(Form::label('address_title', "Address Title <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::text('address_title', Input::old('address_title'),  array('class' => 'required form-control','id'=>"address_title"))}}
                                        </div>
                                    </div>
                                    <div class="form_group_left form_group_right">

                                        {{ HTML::decode(Form::label('address_type', "Address Type <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            <?php
                                            $address_type = array(
                                                '' => 'Please Select',
                                                'Home' => 'Home',
                                                'Work' => 'Work',
                                                'Other' => 'Other',
                                            );
                                            ?>
                                            {{ Form::select('address_type', $address_type, Input::old('address_type'), array('class' => 'form-control required', 'id'=>'address_type')) }}
                                        </div>

                                    </div>
                                </div>

                                <div class="form_group">
                                    <div class="form_group_left">
                                        {{ HTML::decode(Form::label('city', "City <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            <?php
                                            $cities_array = array(
                                                '' => 'City'
                                            );
                                            $cities = City::orderBy('name', 'asc')->where('status', "=", "1")->lists('name', 'id');
                                            if (!empty($cities)) {
                                                foreach ($cities as $key => $val)
                                                    $cities_array[$key] = ucfirst($val);
                                            }
                                            ?>
                                            {{ Form::select('city', $cities_array, Input::old('city'), array('class' => 'form-control required', 'id'=>'city')) }}
                                        </div>
                                    </div>
                                    <div class="form_group_left form_group_right">

                                        {{ HTML::decode(Form::label('area', "Area <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{ Form::select('area', array(''=>'Area'), Input::old('area'), array('class' => 'form-control required', 'id'=>'area')) }}

                                        </div>
                                    </div>
                                </div>

                                <div class="form_group">
                                    <div class="form_group_left">
                                        {{ HTML::decode(Form::label('street_name', "Street Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::text('street_name', Input::old('street_name'),  array('class' => 'required form-control','id'=>"street_name"))}}
                                        </div>
                                    </div>
                                    <div class="form_group_left form_group_right">

                                        {{ HTML::decode(Form::label('building', "Building ",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::text('building', Input::old('building'),  array('class' => 'form-control','id'=>"building"))}}
                                        </div>

                                    </div>
                                </div>
                                <div class="form_group">
                                    <div class="form_group_left">
                                        {{ HTML::decode(Form::label('floor', "Floor",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::text('floor', Input::old('floor'),  array('class' => 'form-control','id'=>"floor"))}}
                                        </div>
                                    </div>
                                    <div class="form_group_left form_group_right">

                                        {{ HTML::decode(Form::label('apartment', "Apartment",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::text('apartment', Input::old('apartment'),  array('class' => 'form-control','id'=>"apartment"))}}

                                        </div>
                                    </div>
                                </div>

                                <div class="form_group">
                                    <div class="form_group_left">
                                        {{ HTML::decode(Form::label('phone_number', "Phone Number <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::text('phone_number', Input::old('phone_number'),  array('maxlength'=>"16", 'class' => 'required number form-control','id'=>"phone_number"))}}
                                        </div>
                                    </div>

                                    <div class="form_group_left form_group_right">
                                        {{ HTML::decode(Form::label('extension', "Extension",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::text('extension', Input::old('extension'),  array('class' => 'form-control','id'=>"extension"))}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form_group form_groupse">
                                {{ HTML::decode(Form::label('directions', "Directions",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{  Form::textarea('directions',Input::old('directions'),  array('class' => 'form-control','id'=>"directions"))}}
                                </div>
                            </div>

                            <div class="form_group form_groupse">
                                <label>&nbsp;</label>
                                <div class="in_upt in_upt_res">
                                    {{ Form::submit('Submit', array('class' => "btn btn-primary")) }}  
                                    {{ html_entity_decode(HTML::link(HTTP_PATH.'user/manageaddresses', "Cancel", array('class' => 'btn btn-default'), true)) }}
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


