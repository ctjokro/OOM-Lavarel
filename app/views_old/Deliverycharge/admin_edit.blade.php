@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Edit City')
@extends('layouts/adminlayout')
@section('content')

<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    $("#adminAdd").validate();
});
</script>
<script type="text/javascript">
    $(document).ready(function () {
        var from_city_id = $('#from_city_id').val();
        var from_area_id = $('#from_area_id').val();

        var to_city_id = $('#to_city_id').val();
        var to_area_id = $('#to_area_id').val();
        if (from_city_id != '') {
            //  $("#from_area_id").load("<?php echo HTTP_PATH . "customer/loadfromarea/" ?>" + from_city_id + "/"+0);
        }
        if (to_city_id != '') {
            // $("#to_area_id").load("<?php echo HTTP_PATH . "customer/loadtoarea/" ?>" + to_city_id + "/"+0);
        }
        $("#from_city_id").change(function () {
            $("#from_area_id").load("<?php echo HTTP_PATH . "customer/loadfromarea/" ?>" + $(this).val() + "/0");
        });
        $("#to_city_id").change(function () {
            $("#to_area_id").load("<?php echo HTTP_PATH . "customer/loadtoarea/" ?>" + $(this).val() + "/0");
        });
    });
</script>
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <ul id="breadcrumb" class="breadcrumb">
                    <li>
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/admindashboard', '<i class="fa fa-dashboard"></i> Dashboard', array('id' => ''), true)) }}
                    </li>
                    <li>
                        <i class="fa fa-money"></i> 
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/deliverycharge/admin_index', "Delivery Charges", array('id' => ''), true)) }}
                    </li>
                    <li class="active">Edit Delivery Charge</li>
                </ul>
                <section class="panel">
                    <header class="panel-heading">
                        Edit Delivery Charge
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>

                        {{ Form::model($detail, array('url' => '/admin/deliverycharge/Admin_edit/'.$detail->slug, 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}

                        <div class="form-group">
                            {{ HTML::decode(Form::label('From City', "From City <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php
                                $from_cities_array = array(
                                    '' => 'Please Select'
                                );

                                $from_cities = City::orderBy('name', 'asc')->where('status', "=", "1")->lists('name', 'id');
                                if (!empty($from_cities)) {
                                    foreach ($from_cities as $key => $val)
                                        $from_cities_array[$key] = ucfirst($val);
                                }
                                ?>
                                {{ Form::select('from_city_id', $from_cities_array, Input::old('from_city_id'), array('class' => 'required  form-control', 'id'=>'from_city_id')) }}

                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('from_area_id', "From Area <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php
                                $from_area_array = array(
                                    '' => 'Please Select'
                                );
                                $fromarea = Area::where('city_id', "=", ($detail->from_city_id))->orderBy('name', 'asc')->lists('name', 'id');
                                if (!empty($fromarea)) {
                                    foreach ($fromarea as $key => $val)
                                        $from_area_array[$key] = ucfirst($val);
                                }
                                ?>
                                {{ Form::select('from_area_id', $from_area_array, Input::old('from_area_id'), array('class' => 'required form-control', 'id'=>'from_area_id')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ HTML::decode(Form::label('To City', "To City <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php
                                $to_cities_array = array(
                                    '' => 'Please Select'
                                );

                                $to_cities = City::orderBy('name', 'asc')->where('status', "=", "1")->lists('name', 'id');
                                if (!empty($to_cities)) {
                                    foreach ($to_cities as $key => $val)
                                        $to_cities_array[$key] = ucfirst($val);
                                }
                                ?>
                                {{ Form::select('to_city_id', $to_cities_array, Input::old('to_city_id'), array('class' => 'required  form-control', 'id'=>'to_city_id')) }}

                            </div>
                        </div>

                        <div class="form-group">
                            {{ HTML::decode(Form::label('to_area_id', "To Area <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php
                                $to_area_array = array(
                                    '' => 'Please Select'
                                );
                                $toarea = Area::where('city_id', "=", ($detail->to_city_id))->orderBy('name', 'asc')->lists('name', 'id');
                                if (!empty($toarea)) {
                                    foreach ($toarea as $key => $val)
                                        $to_area_array[$key] = ucfirst($val);
                                }
                                ?>
                                {{ Form::select('to_area_id', $to_area_array, Input::old('to_area_id'), array('class' => 'required form-control', 'id'=>'to_area_id')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ HTML::decode(Form::label('basic_charge', "Vespa Charge (".CURR.") <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('basic_charge', Input::old('basic_charge'), array('class' => 'required number form-control','min'=>'0','maxlength'=>'10')) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('advance_charge', "Car Charge (".CURR.") <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('advance_charge', Input::old('advance_charge'), array('class' => 'required number form-control','min'=>'0','maxlength'=>'10')) }}
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
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                {{ Form::submit('Update', array('class' => "btn btn-danger")) }}
                                {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/deliverycharge/admin_index', "Cancel", array('class' => 'btn btn-default'), true)) }}
                            </div>
                        </div>

                        {{ Form::close() }}

                    </div>


                </section>
            </div>

        </div>
    </section>
</section>

@stop