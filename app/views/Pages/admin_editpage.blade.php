@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Edit Page')
@extends('layouts/adminlayout')
@section('content')

<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('public/js/nicEdit.js?v=1.1') }}"></script>
<script>
    $(document).ready(function(){
        //bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
        new nicEditor({fullPanel : true,  maxHeight : 400, maxWidth : 900, iconsPath : '<?php echo HTTP_PATH ?>public/img/nicEditorIcons.gif'}).panelInstance('editor2',{hasPanel : true});
    });
</script>

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
                        <i class="fa fa-files-o"></i> 
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/page/admin_index', "Pages", array('id' => ''), true)) }}
                    </li>
                    <li class="active">Edit Page</li>
                </ul>
                <section class="panel">
                    <header class="panel-heading">
                        Edit Page
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>

                        {{ Form::model($detail, array('url' => '/admin/page/Admin_editpage/'.$detail->slug, 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}

                        <div class="form-group">
                            {{ HTML::decode(Form::label('name', "Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php echo Form::text('name', Input::old('name'), array('class' => 'required form-control')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('name', "Category <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                <?php
                                $arr = array(
                                    '' => "Select Category",
                                    'Main' => "Main",
                                    //'Popular Areas' => "Popular Areas",
                                  //  'Popular Cuisines' => "Popular Cuisines",
                                   // 'Restaurants / Chef' => "Restaurants / Chef",
                                );
                                ?>
                                {{ Form::select('category', $arr, Input::old('category'), array('class'=>"small form-control required",'id'=>'action')) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('description', "Description <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::textarea('description', Input::old('description'), array('class' => 'required form-control','id'=>"editor2")) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                {{ Form::submit('Update', array('class' => "btn btn-danger")) }}
                                {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/page/admin_index', "Cancel", array('class' => 'btn btn-default'), true)) }}
                            </div>
                        </div>

                        {{ Form::close() }}

                    </div>


                </section>
            </div>

        </div>
    </section>
</section>

@stop