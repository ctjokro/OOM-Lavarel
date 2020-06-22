@section('content')
<div class="wrapper new_wrapp">
    <div class="list_bx newccrr">
        <div class="liste_top">
            <h1>Please wait, We are redirecting you to payment gateway... </h1>
        </div>  
        
        <div class="right-cart payment"> 

            <div class="lis_bxx">
                 {{ View::make('elements.actionMessage')->render() }}

            <div class="form_area">
                <div class="form_headind">
                    
                    <span>Please do not refresh or click on back browser button</span>
                </div>
                <div class="loder_img cerntekej">
                                <span class="loading_img"></span>
                                <br/>
                               <?php
                                if (file_exists(UPLOAD_LOGO_IMAGE_PATH . SITE_LOGO)) {
                                    ?>
                                    <a href="<?php echo HTTP_PATH; ?>">{{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, '', array()) }}</a>

                                    <?php
                                } else {
                                    ?>
                                    <a href="<?php echo HTTP_PATH; ?>"><img src="{{ URL::asset('public/img/front') }}/logo.png" alt="logo" /></a>

                                    <?php
                                }
                                ?>
                            </div>

                <div class="cta_area cta_area_nome">
                    <div class="catr_link catr_link_left">
                        <form name="payment_form" action="<?php echo PAYPAL_URL; ?>" method="post" id="payment_form">

                            <input type="hidden" name="business" value="<?php echo $paypal_email; ?>">
                            <input type="hidden" name="currency_code" value="USD">
                            <input type="hidden" name="cmd" value="_xclick">
                            <input type="hidden" name="item_number" value="<?php echo time(); ?>"/>
                            <input type="hidden" name="item_name" value="Payment on <?php echo SITE_TITLE; ?>"/>
                            <input type="hidden" name="amount" value="<?php echo $total; ?>">
                            <input type="hidden" name="no_shipping" value="0">
                            <input type="hidden" name="lc" value="EN"/>
                            <input type="hidden" name="return" value="<?php echo HTTP_PATH . 'user/successcase/' . $id; ?> "/>
                            <input type="hidden" name="cancel_return" value="<?php echo HTTP_PATH . 'user/cancelcase/' . $id; ?>"/>
                            <!--<input type="hidden" name="notify_url" value="<?php echo HTTP_PATH . 'user/notifycase/' . $id; ?>"/>-->
                           
                            <div class="loader_img"><div class="loader_img"> <xml version="1.0" encoding="utf-8"><svg width='200px' height='200px' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-ripple"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><g> <animate attributeName="opacity" dur="2s" repeatCount="indefinite" begin="0s" keyTimes="0;0.33;1" values="1;1;0"></animate><circle cx="50" cy="50" r="40" stroke="#979799" fill="none" stroke-width="6" stroke-linecap="round"><animate attributeName="r" dur="2s" repeatCount="indefinite" begin="0s" keyTimes="0;0.33;1" values="0;22;44"></animate></circle></g><g><animate attributeName="opacity" dur="2s" repeatCount="indefinite" begin="1s" keyTimes="0;0.33;1" values="1;1;0"></animate><circle cx="50" cy="50" r="40" stroke="#0d8ee8" fill="none" stroke-width="6" stroke-linecap="round"><animate attributeName="r" dur="2s" repeatCount="indefinite" begin="1s" keyTimes="0;0.33;1" values="0;22;44"></animate></circle></g></svg></div></div>

                        </form>
                    </div>  
                    
                </div>


            </div>
            
            </div>
        </div>

    </div>
</div>



    <script type="text/javascript">

        $(function () {
            setTimeout(function () {
                $('#payment_form').submit();
            }
            , 2000);
        })
    </script>



    

@stop