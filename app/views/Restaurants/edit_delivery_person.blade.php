@extends('layout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $.validator.addMethod('positiveNumber',
        function (value) { 
            return Number(value) > 0;
        }, 'Enter a positive number.');
        $.validator.addMethod("pass", function(value, element) {
            return  this.optional(element) || (/.{8,}/.test(value) && /((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,20})/.test(value));
        }, "Password minimum length must be 8 characters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.");

        $("#myform").validate({
            submitHandler: function(form) {
                this.checkForm();

                if (this.valid()) { // checks form for validity
                    $('#formloader').show();
                    this.submit();
                } else {
                    return false;
                }
            }
        });
    });
</script>
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

                <div class="informetion informetion_new">
                    <div class="informetion_top">
                        <div class="tatils">Edit Delivery Person Details</div>
                        <div class="pery">
                            <div id="formloader" class="formloader" style="display: none;">
                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                            </div>
                            {{ View::make('elements.frontEndActionMessage')->render() }}
                            {{ Form::model($detail, array('url' => '/user/editdeliveryperson/'.$detail->slug, 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                            <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>


                            <div class="multiple-fields">

                                <div class="form_group">
                                    <div class="form_group_left">
                                        {{ HTML::decode(Form::label('first_name', "First Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::text('first_name', Input::old('first_name'),  array('class' => 'required form-control','id'=>"first_name"))}}
                                        </div>
                                    </div>
                                    <div class="form_group_left form_group_right">
                                        {{ HTML::decode(Form::label('last_name', "Last Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::text('last_name', Input::old('last_name'),  array('class' => 'required form-control','id'=>"last_name"))}}
                                        </div>
                                    </div>
                                </div>

                                <div class="form_group">
                                    <div class="form_group_left">
                                        {{ HTML::decode(Form::label('first_name', "Email Address <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::text('email_address', Input::old('email_address'),  array('class' => 'required form-control','id'=>"email_address", 'readonly' => 'readonly'))}}
                                        </div>
                                    </div>
                                    <div class="form_group_left form_group_right">
                                        {{ HTML::decode(Form::label('contact no', "Contact No. <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::text('contact', Input::old('contact'),  array('minlength' => 6, 'maxlength' => 16, 'class' => 'required form-control number','id'=>"contact"))}}
                                        </div>
                                    </div>
                                </div>
                                
                                <blockquote> Please only fill password, If you want to change password for Kitchen Staff </blockquote>

                                <div class="form_group">
                                    <div class="form_group_left">
                                        {{ HTML::decode(Form::label('password', "Password <span class='require'></span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{  Form::password('password',  array('type'=>'password','class' => ' pass form-control','minlength' => 8, 'maxlength' => '40','id'=>"password1"))}}
                                            <p class="help-block"> Password minimum length must be 8 charaters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number..</p>
                                        </div>
                                    </div>
                                    <div class="form_group_left form_group_right">
                                        {{ HTML::decode(Form::label('cpassword', "Confirm Password <span class='require'></span>",array('class'=>"control-label col-lg-2"))) }}
                                        <div class="in_upt">
                                            {{ Form::password('cpassword',  array('type'=>'password','class' => ' form-control','maxlength' => '40', 'equalTo' => '#password1')) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form_group form_groupse">
                                    <label>&nbsp;</label>
                                    <div class="in_upt in_upt_res">
                                        {{ Form::submit('Submit', array('class' => "btn btn-primary")) }}  
                                        {{ html_entity_decode(HTML::link(HTTP_PATH.'user/deliveryperson', "Cancel", array('class' => 'btn btn-default'), true)) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>

@stop


