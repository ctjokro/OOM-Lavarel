@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Add Coupon Code')
@extends('layouts/adminlayout')
@section('content')

<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    $.validator.addMethod("pass", function (value, element) {
        return  this.optional(element) || (/.{8,}/.test(value) && /([0-9].*[a-z])|([a-z].*[0-9])/.test(value));
    }, "Password minimum length must be 8 charaters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.");

    $("#adminAdd").validate();
});
</script>
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
    
    function  generatepromo(){
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        for( var i=0; i < 10; i++ ){
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }    

        $('#PromoCodeCode').val(text);
    }
    
    $(".end_time").datepicker({
        defaultDate: "+1w", minDate: 0,
        changeMonth: true,dateFormat: 'yy-mm-dd',
        numberOfMonths: 1,
        onClose: function (selectedDate) {
            $(".start_time").datepicker("option", "maxDate", selectedDate);
        }
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
                        <i class="fa fa-tags"></i> 
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/coupon/admin_index', "Coupons", array('id' => ''), true)) }}
                    </li>
                    <li class="active">Add Coupon</li>
                </ul>

                <section class="panel">

                    <header class="panel-heading">
                        Add Coupon
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>
                        {{ Form::open(array('url' => 'admin/coupon/admin_add', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                        <div class="form-group">
                            {{ HTML::decode(Form::label('code', "Coupon Code <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('code', Input::old('code'), array('class' => 'required form-control')) }}
                               
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('discount', "Discount (%) <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('discount',Input::old('discount'), array('class' => 'required form-control number', 'maxlength'=>2, 'max'=>99)) }} 
                            </div>
                        </div>


                        <div class="form-group">
                            {{ HTML::decode(Form::label('start_time', "Start Time <span class='require'>*</span>",array('class'=>"control-label col-lg-2 start_time"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('start_time',Input::old('start_time'), array('class' => 'required form-control start_time',  'max'=>100)) }} 
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('end_time', "End Time <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('end_time',Input::old('end_time'), array('class' => 'required form-control end_time')) }} 
                            </div>
                        </div>

<!--                        <div class="form-group">
                            {{  Form::label('coupon_image', 'Coupon Image',array('class'=>"control-label col-lg-2", 'onchange'=>'imageValidation()')) }}
                            <div class="col-lg-10">
                                {{ Form::file('coupon_image'); }}
                                <p class="help-block">Supported File Types: gif, jpg, jpeg, png. Max size 2MB.</p>
                            </div>
                        </div>-->
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

        var filename = document.getElementById("coupon_image").value;

        var filetype = ['jpeg', 'png', 'jpg', 'gif'];
        if (filename != '') {
            var ext = getExt(filename);
            ext = ext.toLowerCase();
            var checktype = in_array(ext, filetype);
            if (!checktype) {
                alert(ext + " file not allowed for Image.");
                document.getElementById("coupon_image").value = "";
                return false;
            } else {
                var fi = document.getElementById('coupon_image');
                var filesize = fi.files[0].size;
                if (filesize > 2097152) {
                    alert('Maximum 2MB file size allowed for Image.');
                    document.getElementById("coupon_image").value = "";
                    return false;
                }
            }
        }
        return true;
    }

</script>
@stop