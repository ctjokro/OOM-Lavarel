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
                        <h1>Forgot password on {{ SITE_TITLE }}</h1>
                    </div>

                    {{ View::make('elements.frontEndActionMessage')->render() }}
                    {{ Form::open(array('url' => '/user/forgotpassword', 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                    <div class="logiv">
                        <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>
                        <div class="input_bx">
                            {{ HTML::decode(Form::label('email_address', "Email Address <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            {{ Form::text('email_address', "", array('class' => 'required email form-control')) }}
                        </div>

                        <div class="input_bx">
                            {{ Form::submit('Submit', array('class' => "btn btn-danger")) }}
                        </div>
                        <div class="input_bx">
                            <h2> <?php echo html_entity_decode(HTML::link('user/login', 'Login', array('class' => '', 'title' => 'Login'))); ?></h2>

                        </div>
                    </div>
                    {{ Form::close() }}
                </div>


            </div>
        </div>
        <div class="emploar"><img src="{{ URL::asset('public/img/front/') }}/emploays.png" alt="" /></div>
    </div>
</section>

@stop