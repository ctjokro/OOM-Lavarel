@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Edit Profile')
@extends('layouts/adminlayout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    $.validator.addMethod("contact", function (value, element) {
        return  this.optional(element) || (/^[0-9-]+$/.test(value));
    }, "Contact Number is not valid.");
    $("#myform").validate();
});
</script>

<section id="main-content">
    <section class="wrapper">
        <!-- page start-->
        <div class="row">
            <div class="col-lg-12"> 
                <ul id="breadcrumb" class="breadcrumb">
                    <li>
                        {{ html_entity_decode(link_to('/admin/admindashboard', '<i class="fa fa-dashboard"></i> Dashboard', array('escape' => false))) }}
                    </li>
                    <li class="active"> Edit Profile </li>
                </ul>
                <section class="panel">
                    <header class="panel-heading">
                        Manage Profile
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>
                        <div class=" form">
                            <?php echo Form::model($detail, ['url' => ['/admin/editprofile'], 'id' => 'myform', 'class' => 'cmxform form-horizontal tasi-form form'], array('url' => 'admin/changetext', 'method' => 'post', 'id' => 'adminAdd')); ?>

                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Company name <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('name', Input::old('name'), array('id' => 'name', 'autofocus' => true, 'class' => "required form-control", 'placeholder' => 'Company name'));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="email" class="control-label col-lg-2">Email <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('email', Input::old('email'), array('id' => 'email', 'autofocus' => true, 'class' => "email required form-control", 'placeholder' => 'Email'));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="username" class="control-label col-lg-2">Username <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('username', Input::old('username'), array('id' => 'username', 'autofocus' => true, 'class' => "required form-control", 'placeholder' => 'Username'));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="phone" class="control-label col-lg-2">Phone <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('phone', Input::old('phone'), array('id' => 'phone', 'autofocus' => true, 'class' => "number required form-control", 'placeholder' => 'Phone', 'maxlength' => "16"));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="address" class="control-label col-lg-2">Address <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::textarea('address', Input::old('address'), array('id' => 'address', 'autofocus' => true, 'class' => "required form-control", 'placeholder' => 'Address'));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="maintenance" class="control-label col-lg-2">Maintenance Mode <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    $cities_array = array(
                                        '' => 'Please Select',
                                        '1'=>'On',
                                        '0'=>'Off'
                                    );
                                    ?>
                                    {{ Form::select('maintenance', $cities_array, Input::old('maintenance'), array('class' => 'required  form-control', 'id'=>'maintenance')) }}
                                   
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <button class="btn btn-danger" type="submit">Update Profile</button>
                                    {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/admindashboard', "Cancel", array('class' => 'btn btn-default'), true)) }}
                                </div>
                            </div>
                            <?php echo Form::close(); ?>
                        </div>

                    </div>
                </section>
            </div>
        </div>
        <!-- page end-->
    </section>
</section>
@stop
