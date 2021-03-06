@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
<script src="https://www.google.com/recaptcha/api.js?onload=myCallBack&render=explicit" async defer></script>
<script>
      var recaptcha1;
      var recaptcha2;
      var myCallBack = function() {
        //Render the recaptcha1 on the element with ID "recaptcha1"
        if($("#recaptcha1").length > 0) {
            recaptcha1 = grecaptcha.render('recaptcha1', {
              'sitekey' : '<?php echo CAPTCHA_KEY; ?>', //Replace this with your Site key
              'theme' : 'light'
            });
        }
        
        
      };
    </script>
<script type="text/javascript">
$(document).ready(function () {
    $.validator.addMethod("contact", function (value, element) {
        return  this.optional(element) || (/^[0-9-]+$/.test(value));
    }, "Phone Number is not valid.");
    $("#contactus").validate();
});
</script>
<div class="clear"></div>
<div class="wrapper">
    <div class="ptitle down"><h1>
            Contact Us
        </h1>
    </div>
    <div class="clear"></div>      
</div>

<div class="bnr">
    <img src="{{ URL::asset('public/img/front') }}/bnr.jpg" alt="img" />
</div>
<div class="contact_form_sec">
    <div class="container">
        
            <div class="work_logo contact_logo"></div>
            <div class="full_wrap"> <div class="left_cont">
                <img src="{{ URL::asset('public/img/front') }}/shef.jpg" alt="img" />
            </div>
           
            <div class="right_cont">
                
                <h2>Contact Us  </h2>
                <div class="address_wrpd">
                    <div class="container_1">
                        <div class="address_box">
                            <div class="address_box_heading">
                                <h2>Our Location</h2>
                            </div>
                            <div class="address_box_body">
                                <ul>
                                    <li><img src="{{ URL::asset('public/img/front') }}/phone.png" alt="img" /><span>{{$detail->phone}}</span></li>
                                    <li><img src="{{ URL::asset('public/img/front') }}/mail.png" alt="img" /><span>{{$detail->email}}</span></li>
                                    <li><img src="{{ URL::asset('public/img/front') }}/location.png" alt="img" /><span>{{$detail->address}}</span></li>
                                </ul>
                            </div>
                        </div>
                        

                      
                        </div>
                    
                    </div>
                
                </div> </div>
            <div class="botom_wrap">
            <div class="formc">
                {{ View::make('elements.actionMessage')->render() }}
                {{ Form::open(array('url' => '/contactus', 'method' => 'post', 'id' => 'contactus', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}

                <div class="form-group">
                    {{ HTML::decode(Form::label('name', "Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                    <div class="col-lg-10">
                        {{ Form::text('name', Input::old('name'), array('class' => 'required form-control')) }}
                    </div>
                </div>
                <div class="form-group">
                    {{ HTML::decode(Form::label('email', "Email <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                    <div class="col-lg-10">
                        {{ Form::text('email', Input::old('email'), array('class' => 'required email form-control')) }}
                    </div>
                </div>
                <div class="form-group">
                    {{ HTML::decode(Form::label('phone', "Phone No. <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                    <div class="col-lg-10">
                        {{ Form::text('phone', Input::old('phone'), array('class' => 'required number form-control')) }}
                    </div>
                </div>
                <div class="form-group">
                    {{ HTML::decode(Form::label('subject', "Subject <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                    <div class="col-lg-10">
                        {{ Form::text('subject', Input::old('subject'), array('class' => 'required form-control')) }}
                    </div>
                </div>
                <div class="form-group">
                    {{ HTML::decode(Form::label('message', "Message <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                    <div class="col-lg-10">
                        {{ Form::textArea('message', Input::old('message'), array('class' => 'required form-control')) }}
                    </div>
                </div>
<!--                <div class="form-group">

                    <div id="recaptcha1" style="transform: scale(1); transform-origin: left top;"></div>

                </div>-->

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10 con_btn">
                        {{ Form::submit('Save', array('class' => "btn btn-primary")) }}
                        {{ Form::reset('Reset', array('class'=>"btn btn-default")) }}
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <div class="contact_map contact_map_new">
    <!--<img src="{{ URL::asset('public/img/front') }}/location_map.jpg" alt="img" />-->

    <iframe width="100%" height="415" src="https://maps.google.it/maps?q=<?php echo $detail->address; ?>&output=embed"></iframe>
    
            </div></div>
           
        </div> 
        <div class="clear"></div>  
    </div>

</div>


<div class="clear"></div>
@stop