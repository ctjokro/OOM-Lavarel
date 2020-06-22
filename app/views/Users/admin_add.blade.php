@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Add Restaurant')
@extends('layouts/adminlayout')
@section('content')


<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    $.validator.addMethod("validname", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9_]+$/.test(value);
        }, "*Note: Special characters and spaces are not allowed.");
    $.validator.addMethod("pass", function (value, element) {
        return  this.optional(element) || (/.{8,}/.test(value) && /([0-9].*[a-z])|([a-z].*[0-9])/.test(value));
    }, "Password minimum length must be 8 charaters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.");
    $.validator.addMethod("contact", function (value, element) {
        return  this.optional(element) || (/^[0-9-]+$/.test(value));
    }, "Contact Number is not valid.");
    $("#adminAdd").validate();
});
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#city").change(function () {
            $("#area").load("<?php echo HTTP_PATH . "customer/loadarea/" ?>" + $(this).val() + "/0");
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
                        <i class="fa fa-user"></i> 
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/user/admin_index', "Restaurants", array('id' => ''), true)) }}
                    </li>
                    <li class="active">Add Restaurant</li>
                </ul>

                <section class="panel">

                    <header class="panel-heading">
                        Add Restaurant
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>
                        {{ Form::open(array('url' => 'admin/restaurants/admin_add', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                        <div class="form-group">
                            {{ HTML::decode(Form::label('first_name', "Restaurant Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('first_name', Input::old('first_name'), array('class' => 'required form-control')) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('unique_name', "Unique Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('unique_name', Input::old('unique_name'), array('class' => 'required validname form-control')) }}
                            </div>
                        </div>
<!--                        <div class="form-group">
                            {{ HTML::decode(Form::label('last_name', "Last Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('last_name',Input::old('last_name'), array('class' => 'required form-control')) }} 
                            </div>
                        </div>-->
                        <div class="form-group">
                            {{ HTML::decode(Form::label('email_address', "Email Address <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('email_address', Input::old('email_address'), array('class' => 'required email form-control')) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('password', "Password <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{  Form::password('password',  array('type'=>'password','class' => 'required pass form-control','minlength' => 8, 'maxlength' => '40','id'=>"password"))}}
                                <p class="help-block"> Password minimum length must be 8 charaters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number..</p>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('cpassword', "Confirm Password <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::password('cpassword',  array('type'=>'password','class' => 'required form-control','maxlength' => '40', 'equalTo' => '#password')) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('contact', "Contact Number <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('contact',Input::old('contact'), array('class' => 'required number form-control','maxlength'=>'16'))}}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('address', "Address <span class='require'></span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::textarea('address',Input::old('address'), array('class' => 'form-control'))}}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('city', "City <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php
                                $cities_array = array(
                                    '' => 'Please Select'
                                );

                                $cities = City::orderBy('name', 'asc')->where('status', "=", "1")->lists('name', 'id');
                                if (!empty($cities)) {
                                    foreach ($cities as $key => $val)
                                        $cities_array[$key] = ucfirst($val);
                                }
                                ?>
                                {{ Form::select('city', $cities_array, Input::old('city'), array('class' => 'required  form-control', 'id'=>'city')) }}

                            </div>
                        </div>
                        
                        <div class="form-group">
                            {{ HTML::decode(Form::label('category', "Category <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php
                                $category_array = array(
                                    '' => 'Please Select'
                                );

                                
                                if (!empty($cat)) {
                                    foreach ($cat as $key => $val)
                                        $category_array[$val->id] = ucfirst($val->cat_name);
                                }
                                ?>
                                {{ Form::select('category', $category_array, Input::old('category'), array('class' => 'required  form-control', 'id'=>'category')) }}

                            </div>
                        </div>

                        <div class="form-group">
                            {{ HTML::decode(Form::label('area', "Area <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php
                                $area_array = array(
                                    '' => 'Please Select'
                                );
                                ?>
                                {{ Form::select('area', $area_array, Input::old('area'), array('class' => 'required form-control', 'id'=>'area')) }}
                            </div>
                        </div>
                           <?php /*
                        <div class="form-group">
                            {{ HTML::decode(Form::label('deliver_to', "Deliver to <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php
                                $area_array = array(
                                );
                                $area = Area::where('status', "=", ('1'))->orderBy('name', 'asc')->lists('name', 'id');
                                if (!empty($area)) {
                                    foreach ($area as $key => $val)
                                        $area_array[$key] = ucfirst($val);
                                }

                                $deliver_to = array();
                                ?>
                                {{ Form::select('deliver_to[]', $area_array, $deliver_to, array('multiple' => true,'data-placeholder'=>'Deliver to *', 'class' => 'chzn-select required form-control', 'id'=>'deliver_to')) }}
                            </div>
                        </div> */ ?>
                        <div class="form-group">
                            {{  Form::label('profile_image', 'Profile Image',array('class'=>"control-label col-lg-2")) }}
                            <div class="col-lg-10">
                                {{ Form::file('profile_image'); }}
                                <p class="help-block">Supported File Types: gif, jpg, jpeg, png. Max size 2MB.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                {{ Form::submit('Save', array('class' => "btn btn-danger",'onclick' => 'return imageValidation();')) }}
                                {{ Form::reset('Reset', array('class'=>"btn btn-default")) }}
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </section>
            </div>

        </div>
    </section>
</section>

{{ HTML::style('public/js/chosen/chosen.css'); }}
<!--scripts page-->
{{ HTML::script('public/js/chosen/chosen.jquery.js'); }}
<script type="text/javascript">
    $(".chzn-select").chosen();
</script>

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