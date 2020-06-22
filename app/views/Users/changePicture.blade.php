@extends('layout')
@section('content')

{{ HTML::script('public/js/jquery.validate.js'); }}
{{ HTML::style('public/css/front/jquery.Jcrop.css'); }}
{{ HTML::script('public/js/front/jquery.Jcrop.js'); }}

<script type="text/javascript">
    $(document).ready(function() {
        $(".btn-danger").removeAttr("disabled", "disabled");
        $(".btn-danger").attr("value", "Submit");
        $("#myform").validate();
        $('#cropbox').Jcrop({
            aspectRatio: 0,
            onSelect: updateCoords,
<?php
if (isset($data['width']) and isset($data['height'])) {
    ?>
                    setSelect:  [ <?php echo $data['width']; ?>, <?php echo $data['height']; ?>, 0, 0 ]
<?php } ?>
        });
    });
    function checkDelete() {
        var result = confirm("Are you sure want to delete?");
        if (result) {
            window.location.href = '<?php echo HTTP_PATH . 'user/deleteUserImage'; ?>';
        } else {
            return false;
        }
    }
    function updateCoords(c)
    {
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.w);
        $('#h').val(c.h);
    }

    function checkCoords()
    {
        if (parseInt($('#w').val()))
            return true;
        alert('Please select a crop region then press submit.');
        return false;
    }
</script>
<?php
//echo "<pre>";
//print_r($data);die;
?>
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

                <div class="informetion informetion_new"><div class="informetion_top">
                        <div class="tatils">Change Picture</div>
                        <div class="pery">
                            <div id="formloader" class="formloader" style="display: none;">
                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                            </div>
                            {{ View::make('elements.frontEndActionMessage')->render() }}
                            {{ Form::open(array('url' => '/user/changePicture', 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                            <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>
                            <?php
                            if (!Input::hasFile('profile_image')) {
                                ?>
                                <div class="form_group">
                                    {{  Form::label('old_image', 'Current Profile Image',array('class'=>"control-label col-lg-2")) }}
                                    <div class="in_upt">
                                        <div class="in_upt_img">
                                            <?php if (file_exists(UPLOAD_FULL_PROFILE_IMAGE_PATH . '/' . $userData->profile_image) && $userData->profile_image != "") { ?>
                                                {{ HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH.$userData->profile_image, '') }}
                                                <div class="showchange"><a href="javascript:void();" onclick="checkDelete();">Delete</a></div>
                                                <?php
                                            } else {
                                                echo HTML::image('public/img/front/nouser.png', '', array());
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form_group">
                                    {{ HTML::decode(Form::label('name', "Profile Image <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                    <div class="in_upt">
                                        {{ Form::file('profile_image', array('class'=>"required",'id'=>"profile_image")); }}
                                        <p class="help-block">Supported File Types: gif, jpg, jpeg, png. Max size 2MB.</p>
                                    </div>
                                </div>
                                <?php
                            }
                            if (Input::hasFile('profile_image') and !Session::has("error_message")) {
                                ?>
                                <div class="form_group">
                                    {{  Form::label('crop', ' Crop your photo',array('class'=>"control-label col-lg-2 full-width")) }}

                                    <div class="cropping-image">
                                        <?php
                                        if (!empty($data['image'])) {
                                            ?>
                                            <img src="<?php echo HTTP_PATH . 'uploads/temp/' . $data['image'] ?>" id="cropbox"/>
                                            <?php
                                        }
                                        ?>
                                    </div>

                                    <div class="sdnsyh_sdmsj">
                                        <div class="sdnsyh">
                                            <div class="crop-image-section">
                                                <input type="hidden" name="add_photo" value="1"/>
                                                <?php if (!empty($data['image'])) { ?>
                                                    <input type="hidden" name="profile_image" value="<?php echo $data['image']; ?>"/>
                                                <?php }
                                                ?>
                                                <input type="hidden" id="x" name="x" value="0" />
                                                <input type="hidden" id="y" name="y" value="0" />
                                                <input type="hidden" id="w" name="w" value="<?php echo $data['width']; ?>"/>
                                                <input type="hidden" id="h" name="h" value="<?php echo $data['height']; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>


                            <div class="form_group">
                                <label>&nbsp;</label>
                                <div class="in_upt in_upt_res spacew">
                                    {{ Form::submit('Submit', array('class' => "btn btn-primary",'onclick' => 'return imageValidation();')) }}
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

        var filetype = ['jpeg', 'png', 'jpg'];
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


