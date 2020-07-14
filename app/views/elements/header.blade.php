<?php

use Moltin\Cart\Cart;
use Moltin\Cart\Storage\CartSession;
use Moltin\Cart\Identifier\Cookie;

$cart = new Cart(new CartSession, new Cookie);
$adminuser = DB::table('site_settings')->first();
?>
<?php
if(Session::get('credit_crad') || Session::get('debit_crad'))
{
    $order_id = Session::put('order_id', '');    
}
else
{
    $order_id = Session::get('order_id');
}

if (!empty($order_id)) {
    $orderData = DB::table('orders')
            ->where("orders.id", "=", $order_id)
            ->first();
    ?>
    <div class="vsr_massage">
        <i class="fa fa-warning"></i>
        <div class="vsr_massageinm">
            You are modifiying order number: <span class="modor"><?php echo $orderData->order_number; ?></span>
        </div>
        <div class="vsr_massageinms">
            No want to modifying order? Please 
            <?php echo html_entity_decode(HTML::link('home/deletemodifyorder/' . $orderData->slug, 'click here', array('title' => 'Remove', 'class' => '', 'escape' => false, 'onclick' => "return confirm('Are you sure you want to remove modify this order?');"))); ?>
        </div>
    </div>
<?php } ?>
<header class="site-header header">
    <!--    <div class="top">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <p>Lorem Ipsum Dolor Sit Amet</p>
                    </div>
                    <div class="col-sm-6">
                        <ul class="list-inline pull-right">
                                 <li><a href="<?php echo $adminuser->facebook_link ?>"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="<?php echo $adminuser->twitter_link ?>"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="<?php echo $adminuser->instagram_link ?>"><i class="fa fa-instagram"></i></a></li> 
    
                            <li>
    <?php echo html_entity_decode(HTML::link('about', 'About Us', array('class' => (Request::is('about') ? "active" : ""), 'title' => 'About Us'))); ?>
                            </li>
    
                            <li>
    <?php echo html_entity_decode(HTML::link('home/contactus', 'Contact Us', array('class' => (Request::is('home/contactus') ? "active" : ""), 'title' => 'Contact Us'))); ?>
                            </li>
                        </ul>                        
                    </div>
                </div>
            </div>
        </div>-->
    <!--    <nav class="navbar navbar-default">
            <div class="container">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <i class="fa fa-bars"></i>
                </button>
    
    <?php
    if (file_exists(UPLOAD_LOGO_IMAGE_PATH . SITE_LOGO)) {
        ?>
                        <a class="navbar-brand" href="<?php echo HTTP_PATH; ?>">{{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, '', array()) }}</a>
        
        <?php
    } else {
        ?>
                        <a class="navbar-brand" href="<?php echo HTTP_PATH; ?>"><img src="{{ URL::asset('public/img/front') }}/logo.png" alt="logo" /></a>
        
        <?php
    }
    ?>
                 Collect the nav links, forms, and other content for toggling 
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <ul class="nav navbar-nav main-navbar-nav">
                        <li class="active"><a href="index.html" title="">HOME</a></li>
    
                        <li>
    <?php echo html_entity_decode(HTML::link('restaurants/list', 'Restaurants', array('class' => ((Request::is('restaurants/list*') OR Request::is('restaurants/menu*')) ? "active" : ""), 'title' => 'Restaurants'))); ?>
                        </li>
    
    <?php
    // check user logged in
    if (Session::has('user_id')) {
        ?>
                                  <li><?php echo html_entity_decode(HTML::link('user/myaccount', 'My Account', array('class' => 'myaccount', 'title' => 'My Account'))); ?>  </li>
                              <li>    <?php echo html_entity_decode(HTML::link('user/logout', 'Logout', array('class' => 'logout', 'title' => 'Logout'))); ?>  </li>
    <?php } else { ?>
                                <li> <a href="javascript:void(0)" class="popup-box" alt="login-window">Login</a> </li>
                                <li>   <a href="javascript:void(0)" class="popup-box" alt="signup-customer-window" title="Signup">Signup</a> </li>
                                <li>   <a href="javascript:void(0)" title="Restaurant Signup" class="popup-box" alt="signup-window">Restaurant Signup</a> </li>
    <?php } ?>
    
                            <li class="cart_menu" title="Go to cart">   {{html_entity_decode(HTML::link('order/confirm', '<i class="fa fa-shopping-cart" aria-hidden="true"></i>'.$cart->totalItems())); }}</li>
                    </ul>                           
                </div> /.navbar-collapse                 
                 END MAIN NAVIGATION 
            </div>
        </nav>  -->



    <nav id='cssmenu'>
        <div class="logo">

            <?php
            if (Session::has('user_id')) {
                 
                $user_id = Session::get('user_id');

                $userData = DB::table('users')
                        ->select('users.user_type','users.unique_name','users.profile_image','users.slug','users.logo')
                        ->where('users.id', $user_id)
                        ->first();
                if ($userData->user_type == 'Restaurant') {
                   
                    $path = url('public/uploads/logo').'/'.$userData->profile_image;
                    if($userData->unique_name){
                        $unique_name = $userData->unique_name;
                    }else{
                        $unique_name = $userData->slug;
                    }
                    $URL = "https://".$unique_name.".".SERVER_PATH;
                   
                   if (file_exists(DISPLAY_FULL_PROFILE_IMAGE_PATH . $userData->profile_image) and $userData->profile_image) {
                        ?>
                        
                        <!--<a class="navbar-brand asdf" href="<?php echo $URL; ?>">{{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, '', array()) }}</a>-->
                        <div class="navbar-brand asdf"><a  href="<?php echo $URL; ?>" style="font-size:12px;"><img style="border-radius: 12%;height: 75px;align-content: center;width: 100px;" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.DISPLAY_FULL_PROFILE_IMAGE_PATH.$userData->profile_image) }}"/></a><br><span style="color: gray;font-size: 12px;font-weight: 700;margin-left: -30px;"><a href="<?php echo $URL; ?>"/>powered by <img src=<?php echo $URL.DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO;?> style="width: 108px"></a></span></div>
                    
             
                        <?php
                    } else {
                        ?>
                        
                        <a class="navbar-brand" href="<?php echo $URL; ?>"><img src="{{ URL::asset('public/img/front') }}/logo.png" alt="logo" /></a>

                        <?php
                    }
                } else {
                   
                    $url = Request::url();
                    $url1 = explode('https://', $url);
                    $url2 = explode('.', $url1[1]);
                    $userData = DB::table('users')
                        ->select('users.user_type','users.unique_name','users.profile_image','users.slug','users.logo')
                        ->where('users.unique_name', $url2[0])
                        ->first();
                      
                        if($userData)
                        {
                            if (file_exists(DISPLAY_FULL_PROFILE_IMAGE_PATH . $userData->profile_image) and $userData->profile_image) {
                            
                            $path = url('public/uploads/logo').'/'.$userData->logo;
                        ?>
                        <!--<a class="navbar-brand" href="<?php echo MAIN_HTTP_PATH; ?>">{{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, '', array()) }}</a>-->
                     <?php  if($userData->unique_name){
                        $unique_name = $userData->unique_name;
                    }else{
                        $unique_name = $userData->slug;
                    }
                    $URL = "https://".$unique_name.".".SERVER_PATH; //print_r($URL);?>
                  
                        <div class="navbar-brand asdf"><a  href="<?php echo $URL; ?>" style="font-size:12px;"><img style="border-radius: 12%;height: 75px;align-content: center;width: 100px;" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.DISPLAY_FULL_PROFILE_IMAGE_PATH.$userData->profile_image.'&w=900&h=500&zc=2&q=500') }}"/></a><br><span style="color: gray;font-size: 12px;font-weight: 700;margin-left: -30px;"><a href="<?php echo MAIN_HTTP_PATH.'welcome'; ?>"/>powered by <img src=<?php echo $URL.DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO;?> style="width: 108px"></a></span></div>
                         <?php
                        }
                        else
                        { 
                         ?>
                        
                         <a class="navbar-brand" href="<?php echo MAIN_HTTP_PATH; ?>">{{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, '', array()) }}</a>
                        <?php
                        } } else { 
                         ?>
                        
                         <a class="navbar-brand" href="<?php echo MAIN_HTTP_PATH; ?>">{{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, '', array()) }}</a>
                        <?php
                        }
                        ?>
                        
                       

                        <?php
                    
                }
            } else {
               
               $url = Request::url();
                    $url1 = explode('https://', $url);
                    $url2 = explode('.', $url1[1]);
                    $userData = DB::table('users')
                        ->select('users.user_type','users.unique_name','users.profile_image','users.slug','users.logo')
                        ->where('users.unique_name', $url2[0])
                        ->first();
                    
                    if (file_exists(UPLOAD_LOGO_IMAGE_PATH . SITE_LOGO)) {
                        if($userData)
                        {
                            $path = url('public/uploads/logo').'/'.$userData->logo;
                        ?>
                        
                         <?php $path = url('public/uploads/logo').'/'.$userData->logo;
                    if($userData->unique_name){
                        $unique_name = $userData->unique_name;
                    }else{
                        $unique_name = $userData->slug;
                    }
                    $URL = "https://".$unique_name.".".SERVER_PATH; //print_r($URL);?>
                        <!--<a class="navbar-brand" href="<?php echo MAIN_HTTP_PATH; ?>">{{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, '', array()) }}</a>-->
                       
                        <div class="navbar-brand asdf"><a  href="<?php echo $URL; ?>" style="font-size:12px;"><img style="border-radius: 12%;height: 75px;align-content: center;width: 100px;" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.DISPLAY_FULL_PROFILE_IMAGE_PATH.$userData->profile_image.'&w=900&h=500&zc=2&q=500') }}"/></a><br><span style="color: gray;font-size: 12px;font-weight: 700;margin-left: -30px;"><a href="<?php echo MAIN_HTTP_PATH.'welcome'; ?>"/>powered by <img src=<?php echo $URL.DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO;?> style="width: 108px"></a></span></div>
                         <?php
                        }
                        else
                        {
                         ?>
                         <a class="navbar-brand" href="<?php echo MAIN_HTTP_PATH; ?>">{{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, '', array()) }}</a>
                        <?php
                        }
                        ?>
                        <?php
                    } else {
                        ?>
                        <a class="navbar-brand" href="<?php echo MAIN_HTTP_PATH; ?>"><img src="{{ URL::asset('public/img/front') }}/logo.png" alt="logo" /></a>

                        <?php
                    }
                
                
                }
                ?>

            </div>    
<style>
    @media screen and (max-width: 992px)
.cart_menu .mobilemenu i {
    width: 55px !important;
    height: 46px !important;
    position: absolute !important;
    right: 0 !important;
    top: 10px !important;
    cursor: pointer !important;
    z-index: 12399994 !important;
    
}

.mobilemenu
{
    margin-top: 23px !important;
}


.mobilemenu a
{
background-color: white !important;
}

.mobilemenu i
{
/*margin-left: 100px !important;*/
margin-left: -38px !important;
    margin-top: -11px !important;
}

.cart_menu .mobilemenu a
{
    margin-left: -100px !important;
    margin-top: 4px !important;
    
}


.cart_menu .mobilemenu a:hover {
    background: transparent !important;
}
</style>

            <div id="head-mobile"><div class="cart_menu mobilemenu" id="cart_bt" title="Go to cart">{{html_entity_decode(HTML::link('order/confirm', '<i class="fa fa-shopping-cart" aria-hidden="true"></i>'.$cart->totalItems())); }}</div> </div>
           
            <div class="buttonn"></div>
            
            <ul>
                <li>
                    <?php
                    if (!isset($unique_name)) {
                    if (isset($_REQUEST['city']) && $_REQUEST['city'] > 0) {
                        if ((isset($_REQUEST['city']) && $_REQUEST['city'] > 0) && (isset($_REQUEST['area']) && $_REQUEST['area'] > 0)) {
                            echo html_entity_decode(HTML::link('restaurants/list?city=' . $_REQUEST['city'] . '&area=' . $_REQUEST['area'], 'Restaurants', array('class' => ((Request::is('restaurants/list*') OR Request::is('restaurants/menu*')) ? "active" : ""), 'title' => 'Restaurants')));
                        } else {
                            echo html_entity_decode(HTML::link('restaurants/list?city=' . $_REQUEST['city'], 'Restaurants', array('class' => ((Request::is('restaurants/list*') OR Request::is('restaurants/menu*')) ? "active" : ""), 'title' => 'Restaurants')));
                        }
                    } else {
$url = Request::url();
            $url1 = explode('https://', $url);
            $url2 = explode('.', $url1[1]);
            if($url2[0] == 'www'){
                        echo html_entity_decode(HTML::link('restaurants/list', 'Restaurants', array('class' => ((Request::is('restaurants/list*') OR Request::is('restaurants/menu*')) ? "active" : ""), 'title' => 'Restaurants')));
                    }
                        
                    }
                    }
                    ?>
                </li>

                <?php
                // check user logged in
                if (Session::has('user_id')) { 
                    ?>
                    <li><?php echo html_entity_decode(HTML::link('user/myaccount', 'My Account', array('class' => 'myaccount', 'title' => 'My Account'))); ?>  </li>
                    <li>    <?php echo html_entity_decode(HTML::link('user/logout', 'Logout', array('class' => 'logout', 'title' => 'Logout'))); ?>  </li>
                <?php } else {
                
                ?>
                    <li> <a href="javascript:void(0)" class="popup-box" alt="login-window">Login</a> </li>
                    <li>   <a href="javascript:void(0)" class="popup-box" alt="signup-customer-window" title="Signup">Signup</a> </li>
                    <li>   <a href="javascript:void(0)" title="Restaurant Signup" class="popup-box" alt="signup-window">Restaurant Signup</a> </li>
                <?php } ?>

                <li class="cart_menu" id="cart_bt" title="Go to cart">   {{html_entity_decode(HTML::link('order/confirm', '<i class="fa fa-shopping-cart" aria-hidden="true"></i>'.$cart->totalItems())); }}</li>




            </ul>
        </nav>
    </header>


    <script type="text/javascript">

        $(window).scroll(function () {
            if ($(window).scrollTop() >= 50) {
                $('.header').addClass('fixed-header');
                //  $('.carts_bx').addClass('newlimitc');
                $('.lis_left_menu').addClass('newlimitcleft');



            } else {
                $('.header').removeClass('fixed-header');
                // $('.carts_bx').removeClass('newlimitc');
                $('.lis_left_menu').removeClass('newlimitcleft');
            }
        });

    </script> 
    <script>

        $('body').on('click', '.likenlike', function (e) {
            var datatypec = $(this).attr('datatypec');

            var dataid = $(this).attr('dataid');
            var accesstype = $(this).attr('access-mode');



            if (accesstype == "false") {

                swal({
                    title: "Sorry!",
                    text: 'Please login first.',
                    type: "error",
                    html: true
                });

            } else {
                $(this).find('.fa').css({'font-size': '20px'});
                $('span[dataid="' + dataid + '"]').removeClass('active');
                $('span[dataid="' + dataid + '"][datatypec="' + datatypec + '"]').addClass('active');
                $.ajax({
                    url: "<?php echo HTTP_PATH . "home/thumbmode" ?>/" + datatypec + "/" + dataid,
                dataType: 'json',
                type: 'POST',
                success: function (data)
                {
                    if (data.valid)
                    {

                        $('span[dataid="' + dataid + '"][datatypec="like"]').html('<i class="fa fa-thumbs-up "></i>' + data.likes);
                        $('span[dataid="' + dataid + '"][datatypec="dislike"]').html('<i class="fa fa-thumbs-down "></i>' + data.dislike);

                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown)
                {
                    $('.showcartloader').hide();

                    // Message
                    swal({
                        title: "Sorry!",
                        text: "Error while contacting server, please try again",
                        type: "error",
                        html: true
                    });
                    $(".all_bg").hide();
                }
            });

        }

    });
</script>