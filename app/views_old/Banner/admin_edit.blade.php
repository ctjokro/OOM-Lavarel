@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Edit Banner')
@extends('layouts/adminlayout')
@section('content')

<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $.validator.addMethod("pass", function(value, element) {
            return  this.optional(element) || (/.{8,}/.test(value) && /([0-9].*[a-z])|([a-z].*[0-9])/.test(value));
        }, "Password minimum length must be 8 charaters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.");
        $.validator.addMethod("contact", function(value, element) {
            return  this.optional(element) || (/^[0-9-]+$/.test(value));
        }, "Contact Number is not valid.");
        $("#adminAdd").validate();
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
                        <i class="fa fa-image"></i> 
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/banner/admin_index', "Banners", array('id' => ''), true)) }}
                    </li>
                    <li class="active">Edit Banner</li>
                </ul>
                <section class="panel">
                    <header class="panel-heading"> 
                        Edit Banner
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>

                        {{ Form::model($detail, array('url' => '/admin/banner/Admin_edit/'.$detail->slug, 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                        
                        <div class="form-group">
                            {{ HTML::decode(Form::label('title', "Title <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php echo Form::text('title', Input::old('title'), array('class' => 'required form-control')); ?>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            {{  Form::label('file_name', 'Image',array('class'=>"control-label col-lg-2")) }}
                            <div class="col-lg-10">
                                {{ Form::file('file_name', array('class' => '','onchange'=>'return imageValidation();')); }}
                                <p class="help-block">Supported File Types: gif, jpg, jpeg, png. Max size 2MB (best resolution :1600 x 582px).</p>
                            </div>
                        </div>
                        <?php if (file_exists(UPLOAD_BANNER_IMAGE_PATH . '/' . $detail->file_name) && $detail->file_name != "") { ?>
                            <div class="form-group">
                                {{  Form::label('old_image', 'Current Image',array('class'=>"control-label col-lg-2")) }}
                                <div class="col-lg-10">
                                    {{ HTML::image(DISPLAY_BANNER_IMAGE_PATH.$detail->file_name, '', array('width' => '100px')) }}
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                {{ Form::hidden('old_file_name', $detail->file_name, array('id' => '')) }}
                                {{ Form::submit('Update', array('class' => "btn btn-danger",'onclick' => 'return imageValidation();')) }}
                                {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/banner/admin_index', "Cancel", array('class' => 'btn btn-default'), true)) }}
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

        var filename = document.getElementById("file_name").value;

        var filetype = ['jpeg', 'png', 'jpg', 'gif'];
        if (filename != '') {
            var ext = getExt(filename);
            ext = ext.toLowerCase();
            var checktype = in_array(ext, filetype);
            if (!checktype) {
                alert(ext + " file not allowed for Banner Image.");
                document.getElementById("file_name").value = "";
                return false;
            } else {
                var fi = document.getElementById('file_name');
                
                var reader = new FileReader();
                //Read the contents of Image File.
                reader.readAsDataURL(fi.files[0]);
                reader.onload = function (e) {
                    //Initiate the JavaScript Image object.
                    var image = new Image();

                    //Set the Base64 string return from FileReader as source.
                    image.src = e.target.result;

                    //Validate the File Height and Width.
                    image.onload = function () {
                        var height = this.height;
                        var width = this.width;
                       
                        if (height != 582 && width != 1600) {
                            
                            alert("Height and Width should be same as 1600 x 582px.");
                            return false;
                        }
                       // alert("Uploaded image has valid Height and Width.");
                        return true;
                    };

                }

                var filesize = fi.files[0].size;
                if (filesize > 2097152) {
                    alert('Maximum 2MB file size allowed for Profile Image.');
                    document.getElementById("file_name").value = "";
                    return false;
                }
            }
        }
        return true;
    }

</script>
@stop