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
    margin-top: -15px;
    margin-bottom: 10px;
    font-size: 14px;
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
      <form id="debit-link-up-form" method="POST">
      
        <div class="row">
          <div class="col-50">
             <div class="website-logos">
                <div class="weblogo-in" style="margin-top: 15px;">
                    <a class="logos"><img src="<?php echo HTTP_PATH . "uploads/logo/1568633044logo.png" ?>" width="280" alt=""></a>
                </div>
            </div>
            <div>
                <h3>CUSTOMER INFORMATION</h3>    
            </div>
            <label for="card-number">Customer Name</label>
            <input type="text" id="c_name" name="c_name" placeholder="example. Jhon Doe">
            
            <div class="error_c_name format-error"></div>
            
            <label for="card-number">Email Address</label>
            <input type="text" id="email" name="email" placeholder="example@gmail.com">
            <div class="error_email format-error"></div>
            
            <label for="card-number">Mobile Number</label>
            <input type="text" id="mobile" name="mobile" placeholder="+62**********">
            <div class="error_mobile format-error"></div>
            
            <div>
                <h3>CARD INFORMATION</h3>    
            </div>
            <label for="card-number">Debit card number</label>
            <input type="text" id="debit_card_last_four" name="debit_card_last_four" placeholder="Last Four Digit">
            <div class="error_debit_card_last_four format-error"></div>
            
            <div class="row">
              <div class="col-50">
                <label for="card-exp-year">Exp Year</label>
                <input type="text" id="exp_year" name="exp_year" placeholder="YY">
                <div class="error_exp_year format-error"></div>
              </div>
              <div class="col-50">
                   <label for="card-exp-month">Exp Month</label>
                   <input type="text" id="exp_month" name="exp_month" placeholder="MM">
                    <div class="error_exp_month format-error"></div>
               </div>
            </div>
          </div>
          
        </div>
        
        <input type="submit" value="Continue" class="btn submit" style="background-color:#457de2;">
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
        $("#debit-link-up-form").submit(function (e) {
            // $("#pageloader").fadeIn();
            var debit_card_last_four = $('#debit_card_last_four').val();
            var exp_year = $('#exp_year').val();
            var exp_month = $('#exp_month').val();
            var c_name = $('#c_name').val();
            var mobile = $('#mobile').val();
            var email = $('#email').val();
            var error = 0;
            
            if(c_name == '')
    	    {
    	       $('.error_c_name').html('<p class="format-error">Please enter customer name.</p>') 
    	       error = 1;
    	    }
    	    else if($.isNumeric(c_name))
    	    {
    	        $('.error_c_name').html('<p class="format-error">Customer name must be in strign.</p>') 
    	        error = 1;
    	    }
    	    else
    	    {
    	        $('.error_c_name').html(''); 
    	    }
            
            if(mobile == '')
    	    {
    	       $('.error_mobile').html('<p class="format-error">Please enter mobile number.</p>') 
    	       error = 1;
    	    }
    	    else if(!$.isNumeric(mobile))
    	    {
    	        $('.error_mobile').html('<p class="format-error">Mobile number must be in numeric.</p>') 
    	        error = 1;
    	    }
    	    else
    	    {
    	        $('.error_mobile').html(''); 
    	    }
    	    
    	    var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
    	    if(email == '')
    	    {
    	       $('.error_email').html('<p class="format-error">Please enter email.</p>') 
    	       error = 1;
    	    }
    	    else if(!testEmail.test(email))
    	    {
    	        $('.error_email').html('<p class="format-error">Please enter valid email.</p>') 
    	        error = 1;
    	    }
    	    else
    	    {
    	        $('.error_email').html(''); 
    	    }
            
            if(debit_card_last_four == '')
    	    {
    	       $('.error_debit_card_last_four').html('<p class="format-error">Please enter card number.</p>') 
    	       error = 1;
    	    }
    	    else if(!$.isNumeric(debit_card_last_four))
    	    {
    	        $('.error_debit_card_last_four').html('<p class="format-error">Card value must be in numeric.</p>') 
    	        error = 1;
    	    }
    	    else if(debit_card_last_four.length > 4 || debit_card_last_four.length < 4)
    	    {
    	        $('.error_debit_card_last_four').html('<p class="format-error">Car number must be in 4 digit.</p>') 
    	        error = 1;
    	    }
    	    else
    	    {
    	        $('.error_debit_card_last_four').html(''); 
    	    }
    	    
    	    if(exp_year == '')
    	    {
    	       $('.error_exp_year').html('<p class="format-error">Please enter expiry year.</p>') 
    	       error = 1;
    	    }
    	    else if(!$.isNumeric(exp_year))
    	    {
    	        $('.error_exp_year').html('<p class="format-error">Expiry year value must be in numeric.</p>') 
    	        error = 1;
    	    }
    	    else if(exp_year.length > 2 || exp_year.length < 2)
    	    {
    	        $('.error_exp_year').html('<p class="format-error">Expiry year value must be in 2 digit.</p>') 
    	        error = 1;
    	    }
    	    else
    	    {
    	        $('.error_exp_year').html(''); 
    	    }
    	    
    	    if(exp_month == '')
    	    {
    	       $('.error_exp_month').html('<p class="format-error">Please enter expiry month.</p>') 
    	       error = 1;
    	    }
    	    else if(!$.isNumeric(exp_month))
    	    {
    	        $('.error_exp_month').html('<p class="format-error">Expiry month value must be in numeric.</p>') 
    	        error = 1;
    	    }
    	    else if(exp_month.length > 2 || exp_month.length < 2)
    	    {
    	        $('.error_exp_month').html('<p class="format-error">Expiry month value must be in 2 digit.</p>') 
    	        error = 1;
    	    }
    	    else
    	    {
    	        $('.error_exp_month').html(''); 
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
                    url: '/link-customer-to-bank',
                    dataType: 'JSON',
                    data: $('#debit-link-up-form').serialize(),
                    success: function(resp) {
                        if(resp.status == "true")
                        {
                            window.location.href = result + '/verify-otp';    
                        }
                        else
                        {
                            //$("#pageloader").stop().fadeOut();
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