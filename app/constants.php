<?php
ob_start();
$adminuser = DB::table('site_settings')->first();

/* * * define constants ** */
define('SITE_TITLE', $adminuser->title);
define('SITE_URL', $adminuser->url);
define('TAG_LINE', $adminuser->tagline);
define('MAIL_FROM', $adminuser->mail_from);



define('TITLE_FOR_PAGES', SITE_TITLE .' | '.TAG_LINE." - ");

  //  define('HTTP_PATH', (!empty( $_SERVER['HTTPS'])?"https://":"http://" ) . $_SERVER['SERVER_NAME'] . "/");
  
  // define('SERVER_PATH', "online-order.menu/dev/");
  //  define('MAIN_HTTP_PATH', "https://www.online-order.menu/dev/");
 //    define('HTTP_PATH', "https://". $_SERVER['SERVER_NAME'] . "/dev/");
  //  define("BASE_PATH", $_SERVER['DOCUMENT_ROOT'] . "/dev/");
    define('SERVER_PATH', "online-order.menu/");
    define('MAIN_HTTP_PATH', "https://www.online-order.menu/");
    define('HTTP_PATH', "https://". $_SERVER['SERVER_NAME'] . "/");
    define("BASE_PATH", $_SERVER['DOCUMENT_ROOT'] . "");

//echo HTTP_PATH;
//echo "<pre>"; print_r($_SERVER);

define('SITE_LOGO', $adminuser->logo);
define('SITE_FAVICON', $adminuser->favicon);


define('PAYPAL_EMAIL',$adminuser->paypal_email_address);
define('PAYPAL_URL', $adminuser->paypal_url);
define('CURR', '');

/* * ******************  users images ************************ */
define('UPLOAD_FULL_PROFILE_IMAGE_PATH', 'uploads/users');

define('UPLOAD_FULL_LOCATION_IMAGE_PATH', 'uploads/locations');

define('UPLOAD_FULL_ITEM_IMAGE_PATH', 'uploads/items/');

define('UPLOAD_FULL_COUPON_IMAGE_PATH', 'uploads/coupons/');

define('DISPLAY_FULL_PROFILE_IMAGE_PATH', 'uploads/users/');

define('TEMP_PATH', 'uploads/temp');

define('UPLOAD_LOGO_IMAGE_PATH', 'uploads/logo/');
define('DISPLAY_LOGO_IMAGE_PATH', 'uploads/logo/');
define('CAPTCHA_KEY', '6LfFdgkUAAAAAAuyZPrYswKbxxOBLVy_841PSSKj');
define('PUSH_NOTIFY_AUTH_KEY_REST_SIDE', 'AIzaSyAU73-9taFtqRlHCYf1UdcnIBQD8yNsst8');
define('PUSH_NOTIFY_AUTH_KEY_CUST_SIDE', 'AIzaSyBIOVXZWxLLAQMLHzPBzYZKHj8RJAuxq8M');

define('PUSH_NOTIFY_AUTH_KEY_DP_SIDE', 'AIzaSyDeTrAMrFX_nAsvQk3W-PsHkX8qKUlDUTV'); //Dummy
define('PUSH_NOTIFY_AUTH_KEY_KS_SIDE', 'AIzaSyDeTrAMrFX_nAsvQk3W-PsHkX8qKUlDUTV'); //Dummy





define('UPLOAD_BANNER_IMAGE_PATH', 'uploads/banner/');
define('DISPLAY_BANNER_IMAGE_PATH', 'uploads/banner/');

global $cateterStatus;
$cateterStatus = array(
    'Confirm' => 'Confirm',
   // 'Modify' => 'Modify',
    'Cancel' => 'Cancel',
);



global $adminStatus;
$adminStatus = array(
    'Confirm' => 'Confirm',
    'Cancel' => 'Cancel',
    'Preparing' => 'Preparing',
    'Prepared' => 'Prepared',
    'Assign To Delivery' => 'Assign To Delivery',
    'On Delivery' => 'On Delivery',
    'Delivered' => 'Delivered',
    
);

global $customerStatus;
$customerStatus = array(
    'Modify' => 'Modify',
    'Cancel' => 'Cancel',
);

global $courierStatus;
$courierStatus = array(
    'Confirm' => 'Confirm',
    'Cancel' => 'Cancel',
);
global $mealtype;
$mealtype = array(
    'Breakfast' => 'Breakfast',
    'Lunch' => 'Lunch',
    'Dinner' => 'Dinner',
);


global $days;
global $month;
global $years;
$days = range(1, 31);
$days  =  array_combine($days, $days);
$month = range(1, 12);
$month  =  array_combine($month, $month);
$now = date('Y');
$years = range($now,  date('Y',strtotime('+20 year')) );
$years  =  array_combine($years, $years);

// Gmail  App id and Secret
define('GMAIL_CLIENT_ID', '981083152826-d4fqg1loeqd9qp0vfqak2aa70r4svd5b.apps.googleusercontent.com');
define('GMAIL_SECRET', '0C4CDf-jLvQvgw_Z4F4pDSgw');
define('GMAIL_DEVELOPER_KEY', '981083152826-d4fqg1loeqd9qp0vfqak2aa70r4svd5b@developer.gserviceaccount.com');
define('GMAILREDIRECT', HTTP_PATH.'users/gmaillogin');
define("GMAILCLIENT", BASE_PATH . "/app/gmailsrc/Google_Client.php");
define("GMAILOAUTH", BASE_PATH . "/app/gmailsrc/contrib/Google_Oauth2Service.php");

define('APIKEY', 'FOOD2AMhgHbyVwOijJGJguIsrTbyBHUVAN784vnBYBBrTSRM');
define('IPHONEMODE', 'ssl://gateway.push.apple.com:2195');


define('API_AppID', 'APP-80W284485P519543T');
define('ADMIN_PAYPAL', 'anil_1346915456_biz@logicspice.com');
define('Env', 'sandbox');
define('API_UserName', 'anil_1346915456_biz_api1.logicspice.com');
define('API_Password', '1346915479');
define('API_Signature', 'AhmbVlLoNX2YMviviwprA6IAND-4AKOFxUca9Es5nW1vuWwwDXKbTgFK');