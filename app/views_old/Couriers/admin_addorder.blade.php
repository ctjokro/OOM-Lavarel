@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Add Courier Service Order')
@extends('layouts/adminlayout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
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
                        <i class="fa fa-wheelchair"></i> 
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/courier/admin_order', "Courier Service Order Lists", array('id' => ''), true)) }}
                    </li>
                    <li class="active">Assign Order to Courier</li>
                </ul>

                <section class="panel">

                    <header class="panel-heading">
                        Assign Order to Courier
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>
                        {{ Form::open(array('url' => 'admin/courier/admin_addorder', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                        <div class="form-group">
                            {{ HTML::decode(Form::label('order_id', "Order Number <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php 
                                
                                $order_array = array(
                                '' => 'Please Select'
                                );
                                $orderData = DB::table('orders')
                                        ->where('status', 'Confirm')
                                        ->where('is_courier', '0')
                                        ->orderBy('id','DESC')->get(); // get cart menu of this order
                                
                                
                                if (!empty($orderData)) {
                                foreach ($orderData as $oData)
                                $order_array[$oData->id] = $oData->order_number;
                                }
                                ?>
                                {{ Form::select('order_id', $order_array, '', array('class' => 'required  form-control', 'id'=>'order_id')) }}
                                
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('courier_name', "Courier Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php 
                                
                                $user_array = array(
                                '' => 'Please Select'
                                );
                                $userData = DB::table('users')
                                        ->where('status', '1')
                                        ->where('user_type', 'Courier')
                                        ->orderBy('first_name','asc')->get(); // get cart menu of this order
                                
                                if (!empty($userData)) {
                                foreach ($userData as $uData)
                                $user_array[$uData->id] = ucfirst($uData->first_name.' '.$uData->last_name).' ('.$uData->email_address.')';
                                }
                                ?>
                                {{ Form::select('user_id', $user_array, '', array('class' => 'required  form-control', 'id'=>'user_id')) }}
                                
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