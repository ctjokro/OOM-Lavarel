<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<style>
body {
  font-family: Arial;
  font-size: 17px;
  padding: 8px;
}

* {
  box-sizing: border-box;
}

.row {
  display: -ms-flexbox; /* IE10 */
  display: flex;
  -ms-flex-wrap: wrap; /* IE10 */
  flex-wrap: wrap;
  margin: 0 -16px;
}

.col-25 {
  -ms-flex: 25%; /* IE10 */
  flex: 25%;
}

.col-50 {
  -ms-flex: 50%; /* IE10 */
  flex: 50%;
}

.col-75 {
  -ms-flex: 75%; /* IE10 */
  flex: 75%;
}

.col-25,
.col-50,
.col-75 {
  padding: 0 16px;
}

.container-xendt {
  background-color: #f2f2f2;
  padding: 5px 20px 15px 20px;
  border: 1px solid lightgrey;
  border-radius: 3px;
  margin-left: 480px;
  margin-right: 480px;
  margin-top: 50px;
}

input[type=text] {
  width: 100%;
  margin-bottom: 20px;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 3px;
}

label {
  margin-bottom: 10px;
  display: block;
}

.icon-container {
  margin-bottom: 20px;
  padding: 7px 0;
  font-size: 24px;
}

.btn {
  background-color: #4CAF50;
  color: white;
  padding: 12px;
  margin: 10px 0;
  border: none;
  width: 100%;
  border-radius: 3px;
  cursor: pointer;
  font-size: 17px;
}

.btn:hover {
  background-color: #45a049;
}

a {
  color: #2196F3;
}

hr {
  border: 1px solid lightgrey;
}

span.price {
  float: right;
  color: grey;
}

#pageloader
{
  background: rgba( 255, 255, 255, 0.8 );
  display: none;
  height: 100%;
  position: fixed;
  width: 100%;
  z-index: 9999;
}

#pageloader img
{
  left: 50%;
  margin-left: 0px;
  margin-top: 0px;
  position: absolute;
  top: 40%;
}

.format-error
{
    margin-top: -18px;
    font-size: 13px;
    color: red;  
    margin-bottom: 15px;
}

.message-format{
    margin-bottom: 15px;
    font-size: 17px;
}

.alert-message{
    margin-top: -10px;
    font-size: 15px;
    color: red;
}

/* Responsive layout - when the screen is less than 800px wide, make the two columns stack on top of each other instead of next to each other (also change the direction - make the "cart" column go on top) */
@media (max-width: 800px) {
  .row {
    flex-direction: column-reverse;
  }
  .col-25 {
    margin-bottom: 20px;
  }
}
</style>
</head>
<body>
<?php 
session_start(); 
$gTotal = Session::get('gTotal');
$order_id = Session::get('order_id');
$user_id = Session::get('user_id');
?>

<div class="container">

<div class="row">
  <div class="col-75">
    <div class="container-xendt">
    <div id="pageloader" style="overflow: show;margin: auto;top: 0;left: 0;bottom: 0;right: 0;">
       <img src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.16.1/images/loader-large.gif" alt="processing..." />
    </div>
      <form id="otp-verification-form"  method="POST">
      
        <div class="row">
          <div class="col-50">
             <div class="website-logos">
                <div class="weblogo-in" style="margin-top: 15px;">
                    <a class="logos"><img src="<?php echo HTTP_PATH . "uploads/logo/1568633044logo.png" ?>" width="280" alt=""></a>
                </div>
            </div>
            @if($errors->has('something_wrong'))
                <div class="alert-message">
                    <p>{{ $errors->first('something_wrong') }}</p>    
                </div>
            @endif
            <div>
                <h3>PAYMENT VERIFICATION</h3>    
            </div>
            <input type="hidden" name="linkedAccountTokenId" id="linkedAccountTokenId" value="{{Session::get('linked_account_id')}}">
            <input type="hidden" name="gTotal" id="gTotal" value="{{Session::get('gTotal')}}">
            <input type="hidden" name="order_id" id="order_id" value="{{Session::get('order_id')}}">
            <input type="hidden" name="user_id" id="user_id" value="{{Session::get('user_id')}}">
            <input type="hidden" name="debit_card_last_four" id="debit_card_last_four" value="{{Session::get('debit_card_last_four')}}">
            
            @if(Session::has('payment_verification_message'))
                <div class="message-format">
                  <div class="alert alert-info">{{ Session::get('payment_verification_message') }}</div>
                </div>
            @endif
            <label for="sent_otp">Linked Account OTP</label>
            <input type="text" id="sent_otp" name="sent_otp">
            <div class="sent_otp_error"></div>
            <!--@if($errors->has('sent_otp'))-->
            <!--    <div class="error format-error">{{ $errors->first('sent_otp') }}</div>-->
            <!--@endif-->
            <div class="custome_mobile_number_error"></div>
            @if($errors->has('linkedAccountTokenId'))
                <div class="alert-message">
                    <p>{{ $errors->first('linkedAccountTokenId') }}</p>    
                </div>
            @endif
            
          </div>
          
        </div>
        
        <input type="submit" value="Verify" class="btn submit" style="background-color:#457de2;">
      </form>
    </div>
  </div>
</div>
</div>
<!-- Place this code in the <head> section of your page -->   
<script charset="utf-8" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script>
    $(document).ready(function(){
        // $("#otp-verification-form").submit(function (e) {
        //     $("#pageloader").fadeIn();
        // });
        
        $("#otp-verification-form").submit(function (e) {
            var sent_otp = $('#sent_otp').val();
            var error = 0;
            if(sent_otp == "")
            {
                $('.sent_otp_error').html('<p class="format-error">Please enter OTP.</p>');
                error = 1;
            }
            else if(!$.isNumeric(sent_otp))
            {
                $('.sent_otp_error').html('<p class="format-error">OTP must be in numeric.</p>')
                error = 1;
            }
            else if(sent_otp.length > 6 || sent_otp.length < 6)
    	    {
    	        $('.sent_otp_error').html('<p class="format-error">OTP must be in 6 digit.</p>') 
    	        error = 1;
    	    }
            else
            {
                $('.sent_otp_error').html('')
            }
            
            if(error == 1)
    	    {
    	        e.preventDefault();
    	        e.stopPropagation();
    	    }
    	    else
    	    {
    	        $("#pageloader").fadeIn();
                var url = window.location.href
                var arr = url.split("/");
                var result = arr[0] + "//" + arr[2]
                $.ajax({
                    type: 'POST',
                    url: '/verify-customer-otp',
                    dataType: 'JSON',
                    data: $('#otp-verification-form').serialize(),
                    success: function(resp) {
                        if(resp.status == "true")
                        {
                            window.location.href = result + '/payment';      
                        }
                        else
                        {
                            <?php Session::put('xendit_success_message', ''); ?>
        		            swal({
                                title: "Sorry!",
                                text: "Something went wrong. Please try again.",
                                type: "error",
                                html: true
                            }, function() {
                                window.location.href = result + '/order/confirm';
                            });
                        }
                    }
                });
                return false;
    	    }
        });
    })
</script>
</body>
</html>