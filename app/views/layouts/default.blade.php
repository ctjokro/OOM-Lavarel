<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <title> 
            <?php
            if (isset($title)) {
                echo $title;
            }
            ?>
        </title>
<!--        <link rel="icon" type="image/png" href="{{ asset('public/img/front/favicon-16x16.png') }}"/>-->
        
         <?php if(file_exists(UPLOAD_LOGO_IMAGE_PATH.SITE_FAVICON)){
                                 ?>
                 <link rel="icon" type="image/png" href="<?php echo HTTP_PATH.DISPLAY_LOGO_IMAGE_PATH.SITE_FAVICON; ?>"/>
            <?php   }else{   ?>
                     <link rel="icon" type="image/png" href="{{ asset('public/img/front/favicon.ico') }}"/>
                <?php     } ?>
        
             {{ HTML::style('public/css/front/bootstrap.min.css') }}
             {{ HTML::style('public/css/front/style.css?ver=1.0') }}
             {{ HTML::style('public/css/front/media.css') }}
       
        {{ HTML::style('public/css/front/font-awesome.css'); }}

        {{ HTML::script('public/js/jquery-1.8.2.min.js'); }}
       
        <!--{{ HTML::script('https://code.jquery.com/jquery-1.9.1.js'); }}-->
        
        {{ HTML::script('public/js/bootstrap.min.js'); }}
        {{ HTML::script('public/js/front/jquery-customselect.js'); }}
        
{{ HTML::script('public/js/front/menu.js'); }}
        {{ HTML::script('public/js/front/common.js'); }}
        
        <!-- moment js -->
        {{ HTML::script('public/js/moment.js'); }}
        {{ HTML::script('public/js/moment-timezone-with-data.js'); }}
        <!-- moment js -->
           
        {{ HTML::script('public/js/cssua.min.js'); }}
        {{ HTML::script('public/js/front/jquery.easing.1.3.js'); }}
        {{ HTML::script('public/js/front/jquery.bpopup.min.js'); }}
        {{ HTML::script('public/js/front/jquery.validate.js'); }}
        {{ HTML::script('public/css/front/lib/sweet-alert.min.js'); }}
        {{ HTML::style('public/css/front/stylee.css'); }}
        {{ HTML::style('public/css/front/lib/sweet-alert.css'); }}
        
        {{ HTML::style('public/css/front/jquery-customselect.css'); }}
        <script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>-->
        <script type="text/javascript">
$(document).ready(function () {
    $(".menu_device").click(function () {
        $(".menu").toggle(300);
    });
    $("button.close").click(function () {
        $(this).parent(".alert").fadeOut("slow");
    })
});
        </script>
    </head>
    <body>
        <div id="wrapper">
            <?php
            // get admin details
            $adminuser = DB::table('admins')
                    ->first();
            if ($adminuser->maintenance) {
                ?>
                <div style="width: 100%; text-align: center; margin-top: 10%;">
                    <img  style="text-align: center;" src="{{HTTP_PATH."public/img/front/under-maintenance.jpg"}}"/>
                </div>
                <?php
                die;
            }
//            exit;
                
            //  phpinfo();
            
                
               
            ?>

            @include('elements.header')
            @include('elements.popup')
            <main class="site-main">
            @yield('content')
            </main>
            
            @include('elements.footer')
            
        </div>
    </body>
</html>
