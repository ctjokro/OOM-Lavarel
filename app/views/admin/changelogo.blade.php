@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Site Logo')
@extends('layouts/adminlayout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
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
                    <li class="active"> Site Logo </li>
                </ul>
                <section class="panel">
                    <header class="panel-heading">
                        Site Logo
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                       
                        <div class=" form">
                            <?php echo Form::model($detail, ['url' => ['/admin/changelogo'], 'id' => 'myform','files' => true, 'class' => 'cmxform form-horizontal tasi-form form'], array( 'method' => 'post', 'id' => 'adminAdd')); ?>
                            
                          
                        <div class="form-group">
                            {{  Form::label('logo', 'Logo',array('class'=>"control-label col-lg-2")) }}
                            <div class="col-lg-10">
                                {{ Form::file('logo',array('onchange' => 'return imageValidation("logo");')) }}
                                <p class="help-block">Supported File Types: gif, jpg, jpeg, png. Max size 2MB (Best:212 x 114px).</p>
                            </div>
                        </div>
                        <?php if (file_exists(UPLOAD_LOGO_IMAGE_PATH . '/' . $detail->logo) && $detail->logo != "") { ?>
                            <div class="form-group">
                                {{  Form::label('old_logo', 'Current Logo',array('class'=>"control-label col-lg-2")) }}
                                <div class="col-lg-10">
                                    {{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.$detail->logo, '', array('width' => '100px')) }}
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            {{  Form::label('favicon', 'Favicon',array('class'=>"control-label col-lg-2")) }}
                            <div class="col-lg-10">
                                {{ Form::file('favicon',array('onchange' => 'return imageValidation("favicon");')) }}
                                <p class="help-block">Supported File Types: ico. Max size 2MB.</p>
                            </div>
                        </div>
                        <?php if (file_exists(UPLOAD_LOGO_IMAGE_PATH . '/' . $detail->favicon) && $detail->favicon != "") { ?>
                            <div class="form-group">
                                {{  Form::label('old_logo', 'Current Favicon',array('class'=>"control-label col-lg-2")) }}
                                <div class="col-lg-10">
                                    {{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.$detail->favicon, '', array('width' => '10px')) }}
                                </div>
                            </div>
                        <?php } ?>
                            
                            
                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                     {{ Form::hidden('old_logo', $detail->logo, array('id' => '')) }}
                                     {{ Form::hidden('old_favicon', $detail->favicon, array('id' => '')) }}
                                    <button class="btn btn-danger" type="submit">Update</button>
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



    function imageValidation(ids) {

        var filename = document.getElementById(ids).value;
        if(ids == "logo"){
             var filetype = ['jpeg', 'png', 'jpg', 'gif'];
        }else{
             var filetype = ['ico'];
        }
       
        if (filename != '') {
            var ext = getExt(filename);
            ext = ext.toLowerCase();
            var checktype = in_array(ext, filetype);
            if (!checktype) {
                alert(ext + " file not allowed for "+ids);
                document.getElementById(ids).value = "";
                return false;
            } else {
                var fi = document.getElementById(ids);
                var filesize = fi.files[0].size;
                if (filesize > 2097152) {
                    alert('Maximum 2MB file size allowed for '+ids);
                    document.getElementById(ids).value = "";
                    return false;
                }
              
        }
        return true;
    }

</script>
@stop
