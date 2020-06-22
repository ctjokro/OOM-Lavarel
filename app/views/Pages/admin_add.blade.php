@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Add Page')
@extends('layouts/adminlayout')
@section('content')


<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('public/js/nicEdit.js') }}"></script>
<script>
    $(document).ready(function() {
        //bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
        new nicEditor({fullPanel: true, maxHeight: 400, maxWidth: 900, iconsPath: '<?php echo HTTP_PATH ?>public/img/nicEditorIcons.gif'}).panelInstance('editor2', {hasPanel: true});
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
                    <li class="active">Add Page</li>
                </ul>

                <section class="panel">

                    <header class="panel-heading">
                        Add Page
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>
                        {{ Form::open(array('url' => 'admin/page/admin_add', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                        <div class="form-group">
                            {{ HTML::decode(Form::label('name', "Page Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('name', "", array('class' => 'required form-control')) }}
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
                                {{ Form::select('category', $arr, input::old("name"), array('class'=>"small form-control required")) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ HTML::decode(Form::label('description', "Description <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::textarea('description', input::old("description"), array('class' => 'required form-control','id'=>"editor2")) }}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                {{ Form::submit('Save', array('class' => "btn btn-danger")) }}
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

@stop