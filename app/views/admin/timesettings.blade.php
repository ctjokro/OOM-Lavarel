@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Time Settings')
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
                    <li class="active"> Time Settings </li>
                </ul>
                <section class="panel">
                    <header class="panel-heading">
                        Time Settings
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>
                        <div class=" form">
                            <?php echo Form::model($detail, ['url' => ['/admin/timeSettings'], 'id' => 'myform', 'class' => 'cmxform form-horizontal tasi-form form'], array('url' => 'admin/changetext', 'method' => 'post', 'id' => 'adminAdd')); ?>

                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">For Customers (In Minutes) <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('customer_time', Input::old('customer_time'), array('min' => '1', 'autofocus' => true, 'class' => "required digits form-control"));
                                    ?>
                                    <p class="help-block">Time of order confirmation/cancellation/ order modification</p>
                                </div>
                            </div>
                             <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">For Restaurants (In Minutes) <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('caterer_time', Input::old('caterer_time'), array('min' => '1', 'autofocus' => true, 'class' => "required digits form-control"));
                                    ?>
                                    <p class="help-block">Time of order confirmation/cancellation/ modification Request</p>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">For Courier (In Minutes) <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('courier_time', Input::old('courier_time'), array('min' => '1', 'autofocus' => true, 'class' => "required digits form-control"));
                                    ?>
                                    <p class="help-block">Time of delivery confirmation/cancellation</p>
                                </div>
                            </div>
                            
                 

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
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
