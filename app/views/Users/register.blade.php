@extends('layout')
@section('content')

{{ HTML::style('css/front/style.css') }}
{{ HTML::style('css/front/media.css') }}


{{ HTML::script('js/common.js') }}
{{ HTML::script('js/prototype.js') }}
{{ HTML::script('js/html5.js') }}
{{ HTML::script('js/listing.js') }}
{{ HTML::script('js/jquery-latest.js') }}
{{ HTML::script('js/jquery.validate.js') }}
<!--top part start -->

<style>
    ul {
    font-size: 16px;
    list-style-type: none;
    margin: 0;
    padding: 0;
}
    </style>
<?php /* Register Page */ ?>
<script type="text/javascript">
    $(document).ready(function() {
        $.validator.addMethod("alphanumeric", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9`~!@#$%^&*()+={}|;:'",.\/?\\-]+$/.test(value);
        }, "Please do not enter  special character like < or >");
        $.validator.addMethod("pass", function(value, element) {
            return  this.optional(element) || (/.{8,}/.test(value) && /((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,20})/.test(value));
        }, "Password minimum length must be 8 characters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.");
        $.validator.addMethod("contact", function(value, element) {
            return  this.optional(element) || (/^[0-9-]+$/.test(value));
        }, "Contact Number is not valid.");
        $("#userRegister").validate();
    });
</script>
<div id="top">
    <a href="{{url("/")}}">
        {{ HTML::image('img/front/logo.gif') }}
    </a>
    <ul>
        <li class="hover">{{ link_to('/', "Home", array('escape' => false)) }}</li>
        <li><a href="#">About us</a></li>
        <li><a href="#">Contact us</a></li>
    </ul>
</div>
<!--top part end -->
<!--header start -->

<div class="signup-form" style="margin-left: 100px; color: #000;   color: #000;
    margin-left: 100px;
    margin-top: 100px;
    width: 100%;">
{{ View::make('elements.actionMessage')->render() }}



@if(Session::has('success'))
<div class="alert-box success">
    <h2>{{ Session::get('success') }}</h2>
</div>
@endif
    <?php
    echo Form::open(array('url' => 'user/register', 'method' => 'post','id'=>'userRegister', 'files' => true))
    ?>
    <div class="row">
        <div class="label">
            <?php
            echo Form::label('first_name', 'First Name');
            ?>
        </div>
        <div class="field">
            <?php echo Form::text('first_name', "", array('class' => 'required')) ?>
        </div>
    </div>
    <div class="row">
        <div class="label">
            <?php
            echo Form::label('last_name', 'Last Name');
            ?>
        </div>
        <div class="field">
            <?php echo Form::text('last_name', "", array('class' => 'required')) ?>
        </div>
    </div>
    <div class="row">
        <div class="label">
            <?php
            echo Form::label('email_address', 'E-Mail Address');
            ?>
        </div>
        <div class="field">
            <?php echo Form::text('email_address', "", array('class' => 'required email')) ?>
        </div>
    </div>
    <div class="row">
        <div class="label">
            <?php
            echo Form::label('password', 'Password');
            ?>
        </div>
        <div class="field">
            <?php echo Form::password('password', "", array('class' => 'required')) ?>
        </div>
    </div>
    <div class="row">
        <div class="label">
            <?php
            echo Form::label('cpassword', 'Confirm Password');
            ?>
        </div>
        <div class="field">
            <?php echo Form::password('cpassword', "", array('class' => 'required')) ?>
        </div>
    </div>

<div class="row">
        <div class="label">
            <?php
            echo Form::label('profile_image', 'Profile Image');
            ?>
        </div>
        <div class="field">
            {{ Form::file('profile_image','',array('id'=>'','class'=>'')) }}
        </div>
    </div>
    
    <div class="row">
        <div class="label">
            <?php
            echo Form::label('country', 'Select Country');
            ?>
        </div>
        <div class="field">
            
           {{ $countries = DB::table('countries')->lists('country_name', 'id'); ?>
             Form::file('profile_image','',array('id'=>'','class'=>'')) }}
            echo Form::select('country_id', $countries, "",array('class' => 'required'));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="label">
            <?php
            echo Form::checkbox('term', '1',array('class' => 'required'));
            echo Form::label('term', 'Accept term and condition');
            ?>
        </div>
    </div>
    <div class="row">
        <div class="field">
            <?php echo Form::submit('Submit') ?>
            <?php echo Form::reset('Reset') ?>
        </div>
    </div>
    <?php
    echo Form::close();
    ?>
</div>
<!--body end -->
<!--footer start -->
<div id="footerMain">
    <div id="footer">
        <ul>
            <li><a href="{{url("/")}}">Home</a>|</li>
            <li><a href="#">Services</a>|</li>
            <li><a href="#">Testimonials</a>|</li>
            <li><a href="#">Projects</a>|</li>
            <li><a href="#">Privacy</a>|</li>
            <li><a href="#">Latest Ideas</a></li>
        </ul>
        <p class="copyright">&copy;Individual. All rights reserved.</p>
    </div>
</div>

@stop


