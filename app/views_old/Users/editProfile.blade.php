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
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
<script>
function initialize() {

var input = document.getElementById('address3_');
var autocomplete = new google.maps.places.Autocomplete(input);

}

google.maps.event.addDomListener(window, 'load', initialize);
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#city").change(function () {
            $("#area").load("<?php echo HTTP_PATH . "customer/loadarea/" ?>" + $(this).val() + "/0");
        })
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
                        <div class="tatils">Edit Profile</div>
                        <div class="pery">
                            <div id="formloader" class="formloader" style="display: none;">
                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                            </div>
                            {{ View::make('elements.actionMessage')->render() }}
                            {{ Form::model($userData, array('url' => '/user/editProfile', 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                            <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>
                            <div class="form_group">
                                {{ HTML::decode(Form::label('email_address', "Email Address <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt emasce">
                                    {{ $userData->email_address }}
                                </div>
                            </div>
                            <?php if($userData->user_type == "Restaurant"){ ?>
                                <div class="form_group">
                                {{ HTML::decode(Form::label('first_name', "Restaurant Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{ Form::text('first_name', Input::old('first_name'), array('class' => 'required form-control')) }}
                                </div>
                                </div>
                                
                            <?php }else{ ?>
                                <div class="form_group">
                                {{ HTML::decode(Form::label('first_name', "First Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{ Form::text('first_name', Input::old('first_name'), array('class' => 'required form-control')) }}
                                </div>
                                </div>
                                <div class="form_group">
                                    {{ HTML::decode(Form::label('last_name', "Last Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                    <div class="in_upt">
                                        {{ Form::text('last_name', Input::old('last_name'), array('class' => 'required form-control')) }}
                                    </div>
                                </div>
                            <?php } ?>
                            
                            <div class="form_group">
                                {{ HTML::decode(Form::label('contact', "Contact Number <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{ Form::text('contact', Input::old('contact'), array('class' => 'required form-control', 'maxlength'=>16)) }}
                                </div>
                            </div>
                            <div class="form_group">
                                {{ HTML::decode(Form::label('address', "Address <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{ Form::text('address', Input::old('address'), array('class' => 'required form-control','id'=>'address3')) }}
                                </div>
                            </div>

                            <?php if ($userData->user_type <> 'Courier') { ?>
                                <div class="form_group">
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
                                        {{ Form::select('city', $cities_array, Input::old('city'), array('class' => 'required form-control', 'id'=>'city')) }}
                                    </div>
                                </div>
                                <div class="form_group">
                                    {{ HTML::decode(Form::label('area', "Area <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                    <div class="in_upt">
                                        <?php
                                        $area_array = array(
                                            '' => 'Area'
                                        );
                                        $area = Area::where('city_id', "=", ($userData->city))->orderBy('name', 'asc')->lists('name', 'id');
                                        if (!empty($area)) {
                                            foreach ($area as $key => $val)
                                                $area_array[$key] = ucfirst($val);
                                        }
                                        ?>
                                        {{ Form::select('area', $area_array, Input::old('area'), array('class' => 'required form-control', 'id'=>'area')) }}
                                    </div>
                                </div>
                            <?php if($userData->user_type == "Restaurant"){ ?>
                                
                            <div class="form_group">
                                {{ HTML::decode(Form::label('paypal_email_address', "Paypal Email Address <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{ Form::text('paypal_email_address', Input::old('paypal_email_address'), array('class' => 'required email form-control', 'maxlength'=>254)) }}
                                </div>
                            </div>
                            
                            <?php } ?>
                                <?php
                             /*   if ($userData->user_type == 'Restaurant') {
                                    ?>
                                    <div class="form_group">
                                        {{ HTML::decode(Form::label('deliver_to', "Deliver To <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            <?php
                                            $area_array = array(
                                            );
                                            $area = Area::where('status', "=", ('1'))->orderBy('name', 'asc')->lists('name', 'id');
                                            if (!empty($area)) {
                                                foreach ($area as $key => $val)
                                                    $area_array[$key] = ucfirst($val);
                                            }
                                            $deliver_to = $userData->deliver_to ? explode(",", $userData->deliver_to) : array();
                                            ?>
                                            {{ Form::select('deliver_to[]', $area_array, $deliver_to, array('multiple' => true,'data-placeholder'=>'Deliver to *', 'class' => 'chzn-select required form-control', 'id'=>'deliver_to')) }}
                                        </div>
                                    </div>

                                    <?php
                                } */
                            }
                            if ($userData->user_type == 'Courier') {
                                ?>
                                <div class="form_group">
                                    {{ HTML::decode(Form::label('availability', "Availability <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                    <div class="in_upt">
                                        <p class="field switch">
                                            <label class="cb-enable {{$userData->availability?'selected':''}}"><span>On</span></label>
                                            <label class="cb-disable  {{$userData->availability?'':'selected'}}"><span>Off</span></label>
                                            {{ Form::checkbox('availability', 1, ($userData->availability?TRUE:FALSE), ['id' => 'checkbox', 'class'=>'checkbox']) }}
                                        </p>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

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


