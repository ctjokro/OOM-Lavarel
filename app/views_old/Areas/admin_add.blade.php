@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Add Area')
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
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/admindashboard', "<i class='fa fa-dashboard'></i> Dashboard", array('id' => ''), true)) }}
                    </li>
                    <li>
                        <i class="fa fa-building-o"></i> 
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/cities/admin_index', "Cities", array('id' => '','title'=>"Cities List"), true)) }}
                    </li>
                    <li>
                        <i class="fa fa-building-o"></i> 
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/area/admin_index/'.$cityData->slug, $cityData->name, array('id' => '','title'=>"Area List"), true)) }}
                    </li>

                    <li class="active">Add Area</li>
                </ul>

                <section class="panel">

                    <header class="panel-heading">
                        Add Area
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span> {{ Form::open(array('url' => 'admin/area/admin_add/'.$cityData->slug, 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                        <div class="form-group">
                            {{ HTML::decode(Form::label('name', "Area Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="col-lg-10">
                                {{ Form::text('name', Input::old("name"), array('class' => 'required form-control')) }}
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