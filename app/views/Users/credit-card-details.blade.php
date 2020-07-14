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
  margin-left: 500px;
  margin-right: 500px;
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

<div class="row" id="credit-details">
  <div class="col-75">
    <div class="container-xendt">
    <div id="pageloader" style="overflow: show;margin: auto;top: 0;left: 0;bottom: 0;right: 0;">
       <img src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.16.1/images/loader-large.gif" alt="processing..." />
    </div>
      <form id="payment-form" method="POST">
      
        <div class="row">
          <div class="col-50">
             <div class="website-logos">
                <div class="weblogo-in" style="margin-top: 15px;">
                    <a class="logos"><img src="<?php echo HTTP_PATH . "uploads/logo/1568633044logo.png" ?>" width="280" alt=""></a>
                </div>
            </div>
            <div>
                <h3>Payment <span style="font-size: 15px;float: right;">{{ Session::get('gTotal') }} IDR</span></h3>    
            </div>
            <label for="card-number">Credit card number</label>
            <input type="text" id="card_number" name="card_number" placeholder="1111-2222-3333-4444">
            <div class="custome_card_error"></div>
            <div class="row">
              <div class="col-50">
                <label for="card-exp-year">Exp Year</label>
                <input type="text" id="card_exp_year" name="card_exp_year" placeholder="YYYY" min="4">
                <div class="custome_exp_year_error"></div>
              </div>
              <div class="col-50">
                   <label for="card-exp-month">Exp Month</label>
                   <input type="text" id="card_exp_month" name="card_exp_month" placeholder="MM" min="2">
                   <div class="custome_exp_month_error"></div>
               </div>
            </div>
            <div class="row">
              <div class="col-50">
                <label for="card-cvn">CVV</label>
                <input type="text" id="card_cvn" name="card_cvn" placeholder="***">
                <div class="custome_cvv_error"></div>
              </div>
            </div>
          </div>
          
        </div>
        
        <input type="submit" value="Pay" class="btn submit" style="background-color:#457de2;">
      </form>
    </div>
  </div>
</div>
<div id="three-ds-container" style="display:none;">
    <iframe height="450" width="550" id="sample-inline-frame" name="sample-inline-frame"> </iframe>
</div>
</div>
<!-- Place this code in the <head> section of your page -->   
<script charset="utf-8" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://js.xendit.co/v1/xendit.min.js"></script>     
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script type="text/javascript">      
	 Xendit.setPublishableKey('xnd_public_production_PouFfL8s1bOmlpRoK7ccSzeVN9ei8NZ7lXDmRxmGzW8LGgCAZgQ');      
</script>
<script>
    $(function() {
	var $form = $('#payment-form');
	var amount = Math.ceil(<?php echo $gTotal; ?>);
	$form.submit(function(event) {
	    var card_number = $('#card_number').val();
	    var card_exp_year = $('#card_exp_year').val();
	    var card_exp_month = $('#card_exp_month').val();
	    var card_cvn = $('#card_cvn').val();
	    var error = 0;
	    if(card_number == '')
	    {
	       $('.custome_card_error').html('<p class="format-error">Please enter card number.</p>') 
	       error = 1;
	    }
	    else if(!$.isNumeric(card_number))
	    {
	        $('.custome_card_error').html('<p class="format-error">Card value must be in numeric.</p>') 
	        error = 1;
	    }
	    else
	    {
	        $('.custome_card_error').html(''); 
	    }
	    
	    if(card_exp_year == '')
	    {
	       $('.custome_exp_year_error').html('<p class="format-error">Please enter expiry year.</p>') 
	       error = 1;
	    }
	    else if(!$.isNumeric(card_exp_year))
	    {
	        $('.custome_exp_year_error').html('<p class="format-error">Expiry year value must be in numeric.</p>') 
	        error = 1;
	    }
	    else if(card_exp_year.length > 4 || card_exp_year.length < 4)
	    {
	        $('.custome_exp_year_error').html('<p class="format-error">Expiry year value must be in 4 digit.</p>') 
	        error = 1;
	    }
	    else
	    {
	        $('.custome_exp_year_error').html(''); 
	    }
	    
	    if(card_exp_month == '')
	    {
	       $('.custome_exp_month_error').html('<p class="format-error">Please enter expiry month.</p>') 
	       error = 1;
	    }
	    else if(!$.isNumeric(card_exp_month))
	    {
	        $('.custome_exp_month_error').html('<p class="format-error">Expiry month value must be in numeric.</p>') 
	        error = 1;
	    }
	    else if(card_exp_month.length > 2 || card_exp_month.length < 2)
	    {
	        $('.custome_exp_month_error').html('<p class="format-error">Expiry month value must be in 2 digit.</p>') 
	        error = 1;
	    }
	    else
	    {
	        $('.custome_exp_month_error').html(''); 
	    }
	    
	    if(card_cvn == '')
	    {
	       $('.custome_cvv_error').html('<p class="format-error">Please enter cvv number.</p>') 
	       error = 1;
	    }
	    else if(!$.isNumeric(card_cvn))
	    {
	        $('.custome_cvv_error').html('<p class="format-error">CVV value must be in numeric.</p>') 
	        error = 1;
	    }
	    else if(card_cvn.length > 3 || card_cvn.length < 3)
	    {
	        $('.custome_cvv_error').html('<p class="format-error">CVV value must be in 3 digit.</p>') 
	        error = 1;
	    }
	    else
	    {
	        $('.custome_cvv_error').html(''); 
	    }
	    
	    if(error == 1)
	    {
	        event.preventDefault();
	        event.stopPropagation();
	    }
	    else
	    {
	        $("#pageloader").fadeIn();
    		// Disable the submit button to prevent repeated clicks:        
    		$form.find('.submit').prop('disabled', true);        
    		
           
    		// Request a token from Xendit:        
    		Xendit.card.createToken({        
    			//amount: $form.find('#amount').val('75000'),        
    			amount: amount,        
    			card_number: $form.find('#card_number').val(),        
    			card_exp_month: $form.find('#card_exp_month').val(),        
    			card_exp_year: $form.find('#card_exp_year').val(),        
    			card_cvn: $form.find('#card_cvn').val(),        
    			is_multiple_use: false,
    			should_authenticate: true
    		}, xenditResponseHandler);        
            
    		// Prevent the form from being submitted:        
    		return false;     
	    }
	});        
});

function xenditResponseHandler (err, creditCardCharge) {
    var gTotal = Math.ceil(<?php echo $gTotal; ?>);
    var order_id = "<?php echo $order_id; ?>";
    var user_id = "<?php echo $user_id; ?>";
    
    var $form = $('#payment-form');
	if (err) {        
		// Show the errors on the form        
		$('#error pre').text(err.message);        
		$('#error').show();        
		$form.find('.submit').prop('disabled', false); // Re-enable submission        
		return;        
	}        
        
	if (creditCardCharge.status === 'VERIFIED') {
	    $('#credit-details').show();
	    $('#three-ds-container').css('display','none');
	    $("#pageloader").fadeIn();
		// Get the token ID:        
		var token = creditCardCharge.id;    
		var url = window.location.href
        var arr = url.split("/");
        var result = arr[0] + "//" + arr[2]
		$.ajax({
		    url:"/xendit-create-charge",
		    method: "POST",
		    dataType: "JSON",
		    data: {token: token, authentication_id: creditCardCharge.authentication_id, gTotal: gTotal, order_id: order_id, user_id:user_id},
		    success: function(data)
		    {
		        if(data.status == 'true')
		        {
		            <?php Session::put('xendit_success_message', 'Thanks for ordering with us. Your order details are submitted successfully. You will receive confirmation message after acceptance of your order.'); ?>
		            window.location.href = result + '/user/myaccount';
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
		})
		return false;
		
		// Insert the token into the form so it gets submitted to the server:        
		//$form.append($('<input type="hidden" name="xenditToken" />').val(token));        
		// Submit the form to your server:        
		//$form.get(0).submit();
	} else if (creditCardCharge.status === 'IN_REVIEW') {
	    $('#credit-details').hide();
	    $('#three-ds-container').css('display','block');
	    $('#three-ds-container').css('margin-left','370px');
		window.open(creditCardCharge.payer_authentication_url, 'sample-inline-frame');        
		$('#three-ds-container').show();        
	} else if (creditCardCharge.status === 'FAILED') {        
		$('#error pre').text(creditCardCharge.failure_reason);        
		$('#error').show();        
		$form.find('.submit').prop('disabled', false); // Re-enable submission
		var url = window.location.href
        var arr = url.split("/");
        var result = arr[0] + "//" + arr[2] + "/" + arr[3]
		window.location.href = result + '/order/confirm';
	}      
}
</script>
</body>
</html>