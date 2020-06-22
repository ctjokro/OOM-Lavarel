@extends('layouts.default')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function() {
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
function refreshCaptcha()
{
    var img = document.images['captchaimg'];
    var img_reset = document.images['captchaimg_reset'];
    img_reset.src = img.src = img.src.substring(0, img.src.lastIndexOf("?")) + "?rand=" + Math.random() * 1000;
}
</script>


<section>
    <div class="wrapper">
        <div class="login_wrapper">
            <div class="login_bx">
                <div class="login_bx_left">
                    <div id="formloader" class="formloader" style="display: none;">
                        {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                    </div>
                    <div class="login_tops"></div>
                    <div class="login_bg_bottom">
                        <h1>Login</h1>
                    </div>
                    {{ View::make('elements.frontEndActionMessage')->render() }}
                    {{ Form::open(array('url' => '/user/login', 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                    <div class="logiv">
                        <div class="input_bx">
                            {{ Form::text('email_address', "", array('class' => 'required email form-control','placeholder'=>"Email Address",'type'=>'email')) }}
                        </div>
                        <div class="input_bx">
                            {{  Form::password('password', array('type'=>'password','class' => 'required form-control','placeholder'=>"Password"))}}
                        </div>

                        <?php
                        // check captcha code here
                        if (Session::has('captcha')) {
                            $class = "";
                        } else {
                            $class = "captcha_show";
                        }
                        ?>
                        <div class="<?php echo $class; ?> captcha-section" style="display:block;">
                            <!--<label for="pass"><span class="big">Security code</span></label>-->
                            <div class="input_bx captcha_input">
                            <?php
                            echo Form::text('captcha', null, array('id' => 'login', 'autofocus' => true, 'class' => "required form-control", 'placeholder' => 'Security code'));
                            ?>
                            </div>
                            <span class="captcha_right_sec">
                                <img src="<?php echo HTTP_PATH; ?>captcha?rand=<?php echo rand(); ?>" id='captchaimg' >
                                <a href='javascript: refreshCaptcha();'>
                                    <img src="{{ URL::asset('public/img') }}/captcha_refresh.gif" width="35" height="35" alt="">
                                </a>
                            </span>
                            
                        </div>

                        <div class="input_bx">
                        	<div class="remember_me_box"><label>
                                        <?php echo Form::checkbox('remember_me', '', null, ['class' => 'remem_checkbox']); ?>
                                        <span>Remember Me</span></label></div>
                            <div class="for_got">
                                <?php echo html_entity_decode(HTML::link('user/forgotpassword', 'Forgot Password?', array('class' => '', 'title' => 'Forgot Password'))); ?>
                            </div>
                        </div>
                        <div class="input_bx">
                            {{ Form::submit('Login', array('class' => "btn btn-danger")) }}
                        </div>
                        <div class="input_bx">
                            <p>You don’t have an {{ SITE_TITLE }} account? </p>
                            <h2>
                                <a href="#">Sign up Now</a>
                                <?php // echo html_entity_decode(HTML::link('caterer/contact', 'Sign up Now', array('class' => '', 'title' => 'Sign up Now'))); ?>
                            </h2>	
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>

                <div class="login_bx_right">
                    <h2>New to {{ SITE_TITLE }} </h2>
                    <p>Not Signed Up Yet? Be sure to provide us with your email address and sign up to receive email updates on {{SITE_TITLE}}'s newest products and technologies, exclusive sales, special birthday offers and more. </p>
                    <div class="new_acc">
                        <?php echo html_entity_decode(HTML::link('caterer/contact', 'Request Restaurant Account', array('class' => '', 'title' => 'Create New Account'))); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="emploar"><img src="{{ URL::asset('public/img/front/') }}/emploays.png" alt="" /></div>
    </div>
</section>



@stop