@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Tax Management')
@extends('layouts/adminlayout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
{{ HTML::style('public/js/chosen/chosen.css'); }}
<script type="text/javascript">
$(document).ready(function() {
    $(".cb-enable").click(function() {
        $('#is_commission').val('1');
        var parent = $(this).parents('.switch');
        $('.cb-disable', parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox', parent).attr('checked', true);
    });
    $(".cb-disable").click(function() {
        $('#is_commission').val('0');
        var parent = $(this).parents('.switch');
        $('.cb-enable', parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox', parent).attr('checked', false);
    });
});
</script>
<style>
    .cb-enable, .cb-disable, .cb-enable span, .cb-disable span { background: url(<?php echo HTTP_PATH . "public/css/front/" ?>switch.gif) repeat-x; display: block; float: left; }
    .cb-enable span, .cb-disable span { line-height: 30px; display: block; background-repeat: no-repeat; font-weight: bold; }
    .cb-enable span { background-position: left -90px; padding: 0 10px; }
    .cb-disable span { background-position: right -180px;padding: 0 10px; }
    .cb-disable.selected { background-position: 0 -30px; }
    .cb-disable.selected span { background-position: right -210px; color: #fff; }
    .cb-enable.selected { background-position: 0 -60px; }
    .cb-enable.selected span { background-position: left -150px; color: #fff; }
    .switch label { cursor: pointer; }
    .switch input { display: none; }

</style>
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
                    <li class="active"> Commission Management </li>
                </ul>
                <section class="panel">
                    <header class="panel-heading">
                        Commission Management
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>
                        <div class=" form">
                            <?php echo Form::model($detail, ['url' => ['/admin/admincommission'], 'id' => 'myform', 'class' => 'cmxform form-horizontal tasi-form form'], array( 'method' => 'post', 'id' => 'adminAdd')); ?>
                            
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Status <span class="require"></span></label>
                                <div class="col-lg-10">
                                    <p class="field switch">
                                        <label class="cb-enable {{($detail->is_commission==1)?'selected':''}}"><span>On</span></label>
                                        <label class="cb-disable {{($detail->is_commission==0)?'selected':''}}" ><span>Off</span></label>
                                        
                                    </p>
                                    

                                    <p class="help-block">     If you turn your status on this will be applicable to all orders!</p>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Commission (%) <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('commission', Input::old('commission'), array('id' => 'name', 'autofocus' => true, 'class' => "required form-control number", 'min' => "1", 'max' => "100",));
                                    ?>
                                </div>
                            </div>

                            
                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <input type="hidden" name="is_commission" id="is_commission" value="<?php  echo Input::old('is_commission');?>">
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
@stop
