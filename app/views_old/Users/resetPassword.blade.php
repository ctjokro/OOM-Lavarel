@extends('layouts.default')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $.validator.addMethod("pass", function(value, element) {
            return  this.optional(element) || (/.{8,}/.test(value) && /([0-9].*[a-z])|([a-z].*[0-9])/.test(value));
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
    <div class="wrapper">
        <div class="signup_wrapper">
            <div class="login_bx">
                <div class="signup_bx">
                    <div id="formloader" class="formloader" style="display: none;">
                        {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                    </div>
                    <div class="signup_tops"></div>
                    <div class="login_bg_bottom">
                        <h1>Reset Password on {{ SITE_TITLE }}</h1>
                    </div>

                    {{ View::make('elements.frontEndActionMessage')->render() }}
                    {{ Form::open(array('method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                    <div class="logiv">
                        <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>
                        <div class="input_bx">
                            {{ HTML::decode(Form::label('password', "New Password <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            {{  Form::password('password',  array('type'=>'password','class' => 'required pass form-control','minlength' => 8, 'maxlength' => '40','id'=>"password_em"))}}
                            <p class="help-block"> Password minimum length must be 8 charaters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.</p>
                        </div>
                        <div class="input_bx">
                            {{ HTML::decode(Form::label('cpassword', "Confirm Password <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            {{ Form::password('cpassword',  array('type'=>'password','class' => 'required form-control','maxlength' => '40', 'equalTo' => '#password_em')) }}
                        </div>

                        <div class="input_bx">
                            {{ Form::submit('Submit', array('class' => "btn btn-danger")) }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>


            </div>
        </div>
<!--        <div class="emploar"><img src="{{ URL::asset('public/img/front/') }}/emploays.png" alt="" /></div>-->
    </div>
</section>
@stop