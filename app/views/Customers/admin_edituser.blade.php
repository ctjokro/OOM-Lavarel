@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Edit Customer')
@extends('layouts/adminlayout')
@section('content')

<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $.validator.addMethod("pass", function(value, element) {
            return  this.optional(element) || (/.{8,}/.test(value) && /([0-9].*[a-z])|([a-z].*[0-9])/.test(value));
        }, "Password minimum length must be 8 characters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.");
        $.validator.addMethod("contact", function(value, element) {
            return  this.optional(element) || (/^[0-9-]+$/.test(value));
        }, "Contact Number is not valid.");
        $("#adminAdd").validate();
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#city").change(function() {
            $("#area").load("<?php echo HTTP_PATH . "customer/loadarea/" ?>"+$(this).val()+"/0");
        })
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
                        <i class="fa fa-users"></i> 
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/customer/admin_index', "Customers", array('id' => ''), true)) }}
                    </li>
                    <li class="active">Edit Customer</li>
                </ul>
                <section class="panel">
                    <header class="panel-heading"> 
                        Edit Customer
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>

                        {{ Form::model($detail, array('url' => '/admin/customer/Admin_edituser/'.$detail->slug, 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                        <div class="form-group">
                            {{ HTML::decode(Form::label('email_address', "Email Address",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10" style="margin-top: 7px">
                                {{ $detail->email_address }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('first_name', "First Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php echo Form::text('first_name', Input::old('first_name'), array('class' => 'required form-control')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('last_name', "Last Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('last_name', Input::old('last_name'), array('class' => 'required form-control')) }} 
                            </div>
                        </div>


                        <div class="form-group">
                            {{ HTML::decode(Form::label('contact', "Contact Number <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('contact', Input::old('contact'), array('class' => 'required number form-control','maxlength'=>'16'))}}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('address', "Address <span class='require'></span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::textarea('address', Input::old('address'), array('class' => 'form-control'))}}
                            </div>
                        </div><div class="form-group">
                            {{ HTML::decode(Form::label('city', "City <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php
                                $cities_array = array(
                                    '' => 'Please Select'
                                );

                                $cities = City::orderBy('name', 'asc')->where('status', "1")->lists('name', 'id');
                                if (!empty($cities)) {
                                    foreach ($cities as $key => $val)
                                        $cities_array[$key] = ucfirst($val);
                                }
                                ?>
                                {{ Form::select('city', $cities_array, Input::old('city'), array('class' => 'required form-control', 'id'=>'city')) }}

                            </div>
                        </div>

                        <div class="form-group">
                            {{ HTML::decode(Form::label('area', "Area <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php
                                $area_array = array(
                                    '' => 'Please Select'
                                );
                                $area = Area::where('city_id', "=", ($detail->city))->orderBy('name', 'asc')->lists('name', 'id');
                                if (!empty($area)) {
                                    foreach ($area as $key => $val)
                                        $area_array[$key] = ucfirst($val);
                                }
                                ?>
                                {{ Form::select('area', $area_array, Input::old('area'), array('class' => 'required form-control', 'id'=>'area')) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{  Form::label('profile_image', 'Profile Image',array('class'=>"control-label col-lg-2")) }}
                            <div class="col-lg-10">
                                {{ Form::file('profile_image'); }}
                                <p class="help-block">Supported File Types: gif, jpg, jpeg, png. Max size 2MB.</p>
                            </div>
                        </div>
                        <?php if (file_exists(UPLOAD_FULL_PROFILE_IMAGE_PATH . '/' . $detail->profile_image) && $detail->profile_image != "") { ?>
                            <div class="form-group">
                                {{  Form::label('old_image', 'Current Profile Image',array('class'=>"control-label col-lg-2")) }}
                                <div class="col-lg-10">
                                    {{ HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH.$detail->profile_image, '', array('width' => '100px')) }}
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                {{ Form::hidden('old_profile_image', $detail->profile_image, array('id' => '')) }}
                                {{ Form::submit('Update', array('class' => "btn btn-danger",'onclick' => 'return imageValidation();')) }}
                                {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/customer/admin_index', "Cancel", array('class' => 'btn btn-default'), true)) }}
                            </div>
                        </div>

                        {{ Form::close() }}

                    </div>


                </section>
            </div>

        </div>
    </section>
</section>
<script>
    function in_array(needle, haystack) {
        for (var i = 0, j = haystack.length; i < j; i++) {
            if (needle == haystack[i])
                return true;
        }
        return false;
    }

    function getExt(filename) {
        var dot_pos = filename.lastIndexOf(".");
        if (dot_pos == -1)
            return "";
        return filename.substr(dot_pos + 1).toLowerCase();
    }



    function imageValidation() {

        var filename = document.getElementById("profile_image").value;

        var filetype = ['jpeg', 'png', 'jpg', 'gif'];
        if (filename != '') {
            var ext = getExt(filename);
            ext = ext.toLowerCase();
            var checktype = in_array(ext, filetype);
            if (!checktype) {
                alert(ext + " file not allowed for Profile Image.");
                document.getElementById("profile_image").value = "";
                return false;
            } else {
                var fi = document.getElementById('profile_image');
                var filesize = fi.files[0].size;
                if (filesize > 2097152) {
                    alert('Maximum 2MB file size allowed for Profile Image.');
                    document.getElementById("profile_image").value = "";
                    return false;
                }
            }
        }
        return true;
    }

</script>
@stop