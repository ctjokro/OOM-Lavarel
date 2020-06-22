<?php

date_default_timezone_set('Australia/Sydney');
/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

//Route::get('/', function() {
//            return View::make('home.index');
//        });
Route::group(array('domain' => '{account}.imagetowebpage.com'), function()
{

});

Route::get('/contact', function() {
    return View::make('contact');
});

Route::post('/contact', function() {
    $data = Input::all();
    $rules = array(
        'subject' => 'required',
        'message' => 'required'
    );
    $validator = Validator::make($data, $rules);
    if ($validator->fails()) {
        return Redirect::to('contact')->withErrors($validator)->withInput();
    }
    return 'Your message has been sent';
});


Route::group(array('prefix' => 'admin'), function() {
    $adminId = Session::get('adminid');
    Route::get('/login', 'AdminController@showAdminlogin');
});




Route::get('/admin/user/admin_index', array('as' => 'admin.admin_index', 'uses' => 'UserController@all'));

Route::any('/contactus', 'HomeController@showContactus');

/* * ** users route ** */
Route::post('/admin/login', 'AdminController@showAdminlogin');
if("https://www" != strstr(URL::current(),'.',true)){ // comming soon calls

   Route::get('/', 'HomeController@showWelcome');
   
}else{ // welcome calls
 
   Route::get('/', 'HomeController@commingsoon');
}
//Route::get('/', 'HomeController@showWelcome');
Route::get('/welcome', 'HomeController@showWelcome');
//Route::get('/', 'HomeController@commingsoon');
//Route::get('/welcome', 'HomeController@showWelcome');
//Route::get('/', 'HomeController@showWelcome');
Route::get('/admin/logout', 'AdminController@showAdminlogout');
Route::get('/user/register', 'UserController@showregister');
Route::post('/user/register', 'UserController@showregister');
Route::any('/user/upgrade', 'UserController@showupgrade');
Route::any('/user/proceed/{slug}', 'UserController@Showprocced');
Route::any('/user/proceedtopay/{id}', 'UserController@proceedtopay');
Route::any('/user/success/{id}', 'UserController@success');
Route::any('/user/cancel/{id}', 'UserController@cancel');
Route::any('/home/numberformat/{price}', 'HomeController@numberformat');
Route::any('/home/thumbmode/{type}/{id}', 'HomeController@thumbmode');

Route::get('/admin/admindashboard', 'AdminController@showAdmindashboard');
Route::any('/admin/admintax', 'AdminController@showAdmintax');
Route::any('/admin/admincommission', 'AdminController@showAdmincommission');
Route::any('/admin/admindeliverycharge', 'AdminController@showAdmindeliverycharge');
Route::get('/admin/restaurants/admin_add', 'UserController@showAdmin_add');
Route::post('/admin/restaurants/admin_add', 'UserController@showAdmin_add');
Route::get('/admin/restaurants/addcategory', 'UserController@addfoodcategory_view'); 
Route::get('/admin/restaurants/categorylist', 'UserController@show_list_cat'); 
Route::get('/admin/restaurants/editcategory/{id}', 'UserController@editfoodcategory_view');
Route::post('/admin/restaurants/updatefoodcategory/{id}', 'UserController@updatefoodcategory');
Route::get('/admin/restaurants/deletecategory/{id}', 'UserController@delete_category');
Route::post('/admin/restaurants/addfoodcategory', 'UserController@savefoodcategory');
Route::get('/admin/restaurants/Admin_activeuser/{id}', 'UserController@showAdmin_activeuser');
Route::get('/admin/restaurants/Admin_deactiveuser/{id}', 'UserController@showAdmin_deactiveuser');
Route::get('/admin/restaurants/Admin_deleteuser/{id}', 'UserController@showAdmin_deleteuser');
Route::get('/admin/restaurants/Admin_edituser/{id}', 'UserController@showAdmin_edituser');
Route::post('/admin/restaurants/Admin_edituser/{id}', 'UserController@showAdmin_edituser');
Route::get('/admin/restaurants/admindeleteuser/{id}', 'UserController@showAdmindeleteuser');
Route::get('/admin/restaurants/admin_index', array('as' => 'admin.users', 'uses' => 'UserController@showAdmin_index'));
Route::post('/admin/restaurants/admin_index', array('as' => 'admin.users', 'uses' => 'UserController@showAdmin_index'));
Route::get('/admin/restaurants/addtimezone/{id}', 'UserController@Showtimezone_add');
Route::post('/admin/restaurants/addtimezonecurrency', array('as' => 'admin.timezone-currency', 'uses' => 'UserController@addTimezoneCurrency'));

Route::get('/admin/restaurants/resetpass/{id}', 'UserController@showResetPass_form');
Route::post('/admin/restaurants/changepassword', 'UserController@resetPass_form');

/* * ** cuisines route ** */
Route::get('/admin/cuisine/admin_index', 'CuisineController@showAdmin_index');
Route::get('/admin/cuisine/admin_add', 'CuisineController@showAdmin_add');
Route::post('/admin/cuisine/admin_add', 'CuisineController@showAdmin_add');
Route::post('/admin/cuisine/admin_index', 'CuisineController@showAdmin_index');
Route::any('/admin/cuisine/admin_index', array('as' => 'admin.cuisine', 'uses' => 'CuisineController@showAdmin_index'));
Route::get('/admin/cuisine/Admin_activecuisine/{id}', 'CuisineController@showAdmin_activecuisine');
Route::get('/admin/cuisine/Admin_deactivecuisine/{id}', 'CuisineController@showAdmin_deactivecuisine');
Route::get('/admin/cuisine/Admin_deletecuisine/{id}', 'CuisineController@showAdmin_deletecuisine');
Route::get('/admin/cuisine/Admin_editcuisine/{id}', 'CuisineController@showAdmin_editcuisine');
Route::post('/admin/cuisine/Admin_editcuisine/{id}', 'CuisineController@showAdmin_editcuisine');
Route::get('/admin/cuisine/admindeletecuisine/{id}', 'CuisineController@showAdmindeletecuisine');

/* * ** cuisines route ** */
Route::get('/admin/sponsorship/admin_index', 'SponsorshipController@showAdmin_index');
Route::post('/admin/sponsorship/admin_index', 'SponsorshipController@showAdmin_index');
Route::any('/admin/sponsorship/admin_index', array('as' => 'admin.sponsorship', 'uses' => 'SponsorshipController@showAdmin_index'));
Route::get('/admin/sponsorship/Admin_edit/{id}', 'SponsorshipController@showAdmin_edit');
Route::post('/admin/sponsorship/Admin_edit/{id}', 'SponsorshipController@showAdmin_edit');



/* * ** cities route ** */
Route::get('/admin/cities/admin_index', 'CitiesController@showAdmin_index');
Route::get('/admin/cities/admin_add', 'CitiesController@showAdmin_add');
Route::post('/admin/cities/admin_add', 'CitiesController@showAdmin_add');
Route::post('/admin/cities/admin_index', 'CitiesController@showAdmin_index');

Route::any('/admin/cities/admin_index', array('as' => 'admin.cities', 'uses' => 'CitiesController@showAdmin_index'));

Route::get('/admin/cities/Admin_activecity/{id}', 'CitiesController@showAdmin_activecity');
Route::get('/admin/cities/Admin_deactivecity/{id}', 'CitiesController@showAdmin_deactivecity');
Route::get('/admin/cities/Admin_deletecity/{id}', 'CitiesController@showAdmin_deletecity');
Route::get('/admin/cities/Admin_editcity/{id}', 'CitiesController@showAdmin_editcity');
Route::post('/admin/cities/Admin_editcity/{id}', 'CitiesController@showAdmin_editcity');
Route::get('/admin/cities/admindeletecity/{id}', 'CitiesController@showAdmindeletecity');


/* * ** delivery charge ** */
Route::get('/admin/deliverycharge/admin_index', 'DeliverychargeController@showAdmin_index');
Route::get('/admin/deliverycharge/admin_add', 'DeliverychargeController@showAdmin_add');
Route::post('/admin/deliverycharge/admin_add', 'DeliverychargeController@showAdmin_add');
Route::post('/admin/deliverycharge/admin_index', 'DeliverychargeController@showAdmin_index');
Route::any('/admin/deliverycharge/admin_index', array('as' => 'admin.deliverycharge', 'uses' => 'DeliverychargeController@showAdmin_index'));
Route::get('/admin/deliverycharge/Admin_active/{id}', 'DeliverychargeController@showAdmin_active');
Route::get('/admin/deliverycharge/Admin_deactive/{id}', 'DeliverychargeController@showAdmin_deactive');
Route::get('/admin/deliverycharge/Admin_delete/{id}', 'DeliverychargeController@showAdmin_delete');
Route::get('/admin/deliverycharge/Admin_edit/{id}', 'DeliverychargeController@showAdmin_edit');
Route::post('/admin/deliverycharge/Admin_edit/{id}', 'DeliverychargeController@showAdmin_edit');
Route::get('/admin/deliverycharge/admindelete/{id}', 'DeliverychargeController@showAdmindelete');


/* * * banner routes * * */
Route::get('/admin/banner/admin_add', 'BannerController@showAdmin_add');
Route::post('/admin/banner/admin_add', 'BannerController@showAdmin_add');
Route::get('/admin/banner/Admin_active/{id}', 'BannerController@showAdmin_active');
Route::get('/admin/banner/Admin_deactive/{id}', 'BannerController@showAdmin_deactive');
Route::get('/admin/banner/Admin_delete/{id}', 'BannerController@showAdmin_delete');
Route::get('/admin/banner/Admin_edit/{id}', 'BannerController@showAdmin_edit');
Route::post('/admin/banner/Admin_edit/{id}', 'BannerController@showAdmin_edit');
Route::get('/admin/banner/admindelete/{id}', 'BannerController@showAdmindelete');
Route::get('/admin/banner/admin_index', array('as' => 'admin.banner', 'uses' => 'BannerController@showAdmin_index'));
Route::post('/admin/banner/admin_index', array('as' => 'admin.banner', 'uses' => 'BannerController@showAdmin_index'));

/* * ** pages route ** */
Route::get('/admin/page/admin_index', 'PageController@showAdmin_index');
Route::get('/admin/page/admin_add', 'PageController@showAdmin_add');
Route::post('/admin/page/admin_add', 'PageController@showAdmin_add');
Route::post('/admin/page/admin_index', 'PageController@showAdmin_index');
Route::get('/admin/page/Admin_activepage/{id}', 'PageController@showAdmin_activepage');
Route::get('/admin/page/Admin_deactivepage/{id}', 'PageController@showAdmin_deactivepage');
Route::get('/admin/page/Admin_deletepage/{id}', 'PageController@showAdmin_deletepage');
Route::get('/admin/page/Admin_editpage/{id}', 'PageController@showAdmin_editpage');
Route::post('/admin/page/Admin_editpage/{id}', 'PageController@showAdmin_editpage');
Route::get('/admin/page/admindeletepage/{id}', 'PageController@showAdmindeletepage');


/* * ** areas route ** */
Route::get('/admin/area/admin_index', 'AreaController@showAdmin_index');
Route::get('/admin/area/admin_add/{id}', 'AreaController@showAdmin_add');
Route::post('/admin/area/admin_add/{id}', 'AreaController@showAdmin_add');
Route::get('/admin/area/admin_index/{id}', 'AreaController@showAdmin_index');
Route::post('/admin/area/admin_index/{id}', 'AreaController@showAdmin_index');
Route::get('/admin/area/Admin_activearea/{id}', 'AreaController@showAdmin_activearea');
Route::get('/admin/area/Admin_deactivearea/{id}', 'AreaController@showAdmin_deactivearea');
Route::get('/admin/area/Admin_deletearea/{id}', 'AreaController@showAdmin_deletearea');
Route::get('/admin/area/Admin_editarea/{id}', 'AreaController@showAdmin_editarea');
Route::post('/admin/area/Admin_editarea/{id}', 'AreaController@showAdmin_editarea');
Route::get('/admin/area/admindeletearea/{id}', 'AreaController@showAdmindeletearea');



Route::get('/captcha', 'HomeController@showCapcha');
Route::post('/admin/forgotpassword', 'AdminController@showForgotpassword');
Route::get('/admin/changepassword', 'AdminController@showChangepassword');
Route::post('/admin/changepassword', 'AdminController@showChangepassword');
Route::get('/admin/editprofile', 'AdminController@showEditprofile');
Route::post('/admin/editprofile', 'AdminController@showEditprofile');
Route::any('/admin/timeSettings', 'AdminController@showTimesettings');
Route::any('/admin/sitesetting', 'AdminController@sitesetting');
Route::any('/admin/changelogo', 'AdminController@changelogo');

Route::get('/adminpage', 'AdminController@showAdmindashboard');


Route::any('/payment/openshop/{id}', 'PaymentController@openshop');
Route::any('/home/getmenu/{id}', 'HomeController@getmenu');
Route::any('/home/pickup/{id}', 'HomeController@pickup');
Route::any('/home/pickup/{id}/{slug}', 'HomeController@pickup');
Route::any('/user/notify/{slug}', 'HomeController@notify');
Route::any('/payment/success/{id}', 'PaymentController@paymentSuccess');
Route::any('/payment/success/{id}/{slug}/', 'PaymentController@paymentSuccess');
Route::any('/payment/notify/{id}', 'PaymentController@notify');
Route::any('/payment/cancel/{id}', 'PaymentController@cancel');

//.Route::post('/admin/payments', array('as' => 'admin.payments', 'uses' => 'PaymentController@showAdmin_payment_index'));
Route::any('/admin/payments/sponsorships', array('as' => 'admin.payments.sponsorships', 'uses' => 'PaymentController@showAdmin_sponsorships'));
Route::any('/admin/payments', array('as' => 'admin.payments', 'uses' => 'PaymentController@showAdmin_payment_index'));

Route::any('/admin/payment/deletepayment/{slug}', array('as' => 'admin.paymentsc', 'uses' => 'PaymentController@showAdmin_deletepayment'));

/* * * set page route *** */
Route::get('/how-do-i-order', 'PageController@showhowtoorder');
Route::get('/{id}', 'PageController@showindex');
Route::get('page/{id}', 'PageController@showIndexNew');
Route::get('/data/{id}', 'PageController@showdata');


/* * * set front end function  ** */

//fb login
Route::any('/user/fblogin', 'UserController@showfbLogin');
Route::any('/fbconnect', 'UserController@showfbConnect');

Route::get('/caterer/contact', 'UserController@showRestaurant_contact');
Route::post('/user/caterer_contact', 'UserController@showRestaurant_contact');
Route::post('/user/customersignup', 'UserController@showCustomersignup');

Route::get('/user/login', 'UserController@showLogin');
Route::post('/user/login', 'UserController@showLogin');

Route::get('/user/forgotpassword', 'UserController@showForgotpassword');
Route::get('/user/gopro/{id}', 'UserController@showgopro');
Route::any('/user/successcase/{id}', 'UserController@successcase');
Route::any('/user/cancelcase/{id}', 'UserController@cancelcase');
Route::post('/user/forgotpassword', 'UserController@showForgotpassword');

Route::get('/user/resetPassword/{value1}/{value2}', 'UserController@showResetPassword');
Route::post('/user/resetPassword/{value1}/{value2}', 'UserController@showResetPassword');

Route::get('/user/deleteActionMenu/{value1}/{value2}', 'UserController@deleteActionMenu');

Route::get('/user/myaccount', 'UserController@showMyaccount');
Route::get('/user/logout', 'UserController@showLogout');

Route::get('/user/editProfile', 'UserController@showEditProfile');
Route::post('/user/editProfile', 'UserController@showEditProfile');

Route::get('/user/creditcardsetup', 'UserController@showcreditcard');
Route::post('/user/creditcardsetup', 'UserController@showcreditcard');

Route::get('/user/wallet', 'UserController@wallet');
Route::post('/user/wallet', 'UserController@wallet');

Route::get('/user/changePicture', 'UserController@showChangePicture');
Route::post('/user/changePicture', 'UserController@showChangePicture');

Route::get('/user/changePassword', 'UserController@showChangePassword');
Route::post('/user/changePassword', 'UserController@showChangePassword');
Route::get('/user/deleteUserImage', 'UserController@showDeleteUserImage');


Route::get('/user/openinghours', 'UserController@showOpeninghours');
Route::post('/user/openinghours', 'UserController@showOpeninghours');

Route::get('/user/deliverycharges', 'UserController@deliverycharges');
Route::post('/user/deliverycharges', 'UserController@deliverycharges');
Route::get('/user/myreviews', 'UserController@showReview');

Route::any('/user/paymenthistory/{type}', 'UserController@paymenthistory');
Route::any('/user/receivedpayment', 'UserController@receivedpayment');
Route::any('/user/bankTransferPayment', 'UserController@bankTransferPayment');



Route::get('/user/managemenu', 'UserController@showManagemenu');
Route::post('/user/managemenu', 'UserController@showManagemenu');

Route::get('/user/addmenu', 'UserController@showAddmenu');
Route::post('/user/addmenu', 'UserController@showAddmenu');

Route::get('/user/editmenu/{id}', 'UserController@showEditmenu');
Route::post('/user/editmenu/{id}', 'UserController@showEditmenu');


Route::get('/user/deletemenu/{id}', 'UserController@showDeletemenu');
Route::get('/activateprofile/{id}', 'UserController@showActivateprofile');
Route::get('/resendcode/{id}', 'UserController@showResendcode');


/* * * customer routes * * */
Route::get('/admin/customer/admin_add', 'CustomerController@showAdmin_add');
Route::post('/admin/customer/admin_add', 'CustomerController@showAdmin_add');
Route::get('/admin/customer/Admin_activeuser/{id}', 'CustomerController@showAdmin_activeuser');
Route::get('/admin/customer/Admin_deactiveuser/{id}', 'CustomerController@showAdmin_deactiveuser');
Route::get('/admin/customer/Admin_deleteuser/{id}', 'CustomerController@showAdmin_deleteuser');
Route::get('/admin/customer/Admin_edituser/{id}', 'CustomerController@showAdmin_edituser');
Route::post('/admin/customer/Admin_edituser/{id}', 'CustomerController@showAdmin_edituser');
Route::get('/admin/customer/admindeleteuser/{id}', 'CustomerController@showAdmindeleteuser');
Route::get('/admin/customer/admin_index', array('as' => 'admin.customers', 'uses' => 'CustomerController@showAdmin_index'));
Route::post('/admin/customer/admin_index', array('as' => 'admin.customers', 'uses' => 'CustomerController@showAdmin_index'));




/* * * courier routes * * */
Route::get('/admin/courier/admin_add', 'CourierController@showAdmin_add');
Route::post('/admin/courier/admin_add', 'CourierController@showAdmin_add');
Route::get('/admin/courier/Admin_activeuser/{id}', 'CourierController@showAdmin_activeuser');
Route::get('/admin/courier/Admin_deactiveuser/{id}', 'CourierController@showAdmin_deactiveuser');
Route::get('/admin/courier/Admin_activemarkuser/{id}', 'CourierController@showAdmin_activemarkuser');
Route::get('/admin/courier/Admin_deactivemarkuser/{id}', 'CourierController@showAdmin_deactivemarkuser');
Route::get('/admin/courier/Admin_deleteuser/{id}', 'CourierController@showAdmin_deleteuser');
Route::get('/admin/courier/Admin_edituser/{id}', 'CourierController@showAdmin_edituser');
Route::post('/admin/courier/Admin_edituser/{id}', 'CourierController@showAdmin_edituser');
Route::get('/admin/courier/admindeleteuser/{id}', 'CourierController@showAdmindeleteuser');
Route::any('/admin/courier/admin_index', array('as' => 'admin.index', 'uses' => 'CourierController@showAdmin_index'));
Route::any('/admin/courier/admin_order', array('as' => 'admin.couriers', 'uses' => 'CourierController@showAdmin_order'));
Route::any('/admin/courier/admin_addorder', 'CourierController@showAdmin_addorder');


Route::any('/admin/courier/Admin_deleteorder/{id}', 'CourierController@showAdmin_deleteorder');

Route::any('/user/orderstatus', 'UserController@showorderstatus');
Route::any('/user/addstatus', 'UserController@showaddstatus');
Route::any('/user/editstatus/{slug}', 'UserController@showeditstatus');

Route::any('/user/couponcodes', 'UserController@showcouponcodes');
Route::any('/user/addcouponcode', 'UserController@showaddcouponcode');
Route::any('/user/editcouponcode/{slug}', 'UserController@showeditcouponcode');
Route::get('/user/coupon_active/{id}', 'UserController@showcoupon_active');
Route::get('/user/coupon_deactive/{id}', 'UserController@showcoupon_deactive');
Route::get('/user/coupon_delete/{id}', 'UserController@showcoupon_delete');

//wallet history

Route::any('/admin/customer/wallethistory/{id}', 'CustomerController@showWallethistory');
    
Route::get('/customer/register', 'CustomerController@showRegister');

App::missing(function($exception) {
    return Response::view('errors.missing', array(), 404);
});

/* * * manage addresses routes * * */
Route::get('/user/manageaddresses', 'CustomerController@showManageaddress');
Route::get('/user/addaddress', 'CustomerController@showAddaddress');
Route::post('/user/addaddress', 'CustomerController@showAddaddress');
Route::get('/user/editaddress/{id}', 'CustomerController@showEditaddress');
Route::post('/user/editaddress/{id}', 'CustomerController@showEditaddress');
Route::get('/user/deleteaddress/{id}', 'CustomerController@showDeleteaddress');
Route::any('/user/myfavourite', 'UserController@showMyfavourite');
Route::any('/user/deletefav/{id}', 'UserController@showdeletefav');
Route::any('/user/makefav/{id}/', 'UserController@showmakefav');
Route::any('/user/removefav/{id}/', 'UserController@showremovefav');



Route::get('/customer/loadarea/{id}/{id1}', 'CustomerController@showLoadarea');
Route::get('/customer/loadfromarea/{id}/{id1}', 'CustomerController@showLoadfromarea');
Route::get('/customer/loadtoarea/{id}/{id1}', 'CustomerController@showLoadtoarea');

//Route::get('/restaurants/list', 'HomeController@showList');
//Route::pattern('id', '[0-9]+');
Route::get('/restaurants/list', array('as' => 'restaurants.list', 'uses' => 'HomeController@showList'));
Route::any('/restaurants/menu/{id}', array('as' => 'restaurants.menu', 'uses' => 'HomeController@showMenu'));
Route::any('/restaurants/reviews/{id}/{id2}', array('as' => 'restaurants.review', 'uses' => 'HomeController@showReview'));
Route::any('/restaurants/reviews/{id}', array('as' => 'restaurants.review', 'uses' => 'HomeController@showReview'));
//Route::resource('articles', 'ArticlesController', ['except' => ['show', 'edit']]);
//Route::get('/home/menu', 'HomeController@showPage2');



Route::any('/home/addtocart', 'HomeController@showAddtocart');
Route::any('/home/emptycart', 'HomeController@showemptycart');
Route::any('/home/updatecarttext', 'HomeController@showUpdatecarttext');
Route::any('/home/removecart', 'HomeController@showRemovecart');

Route::any('/order/confirm', 'HomeController@showOrder');
Route::any('/restaurants/reorder/{slug}', 'OrderController@reorder');
Route::any('/home/updatecart/{id}', 'HomeController@updatecart');
Route::any('/home/updatecartNewAddress/{id}', 'HomeController@UpdatecartNewAddress');
Route::any('/home/totalcartvalue', 'HomeController@totalcartvalue');
Route::any('/home/fav', 'HomeController@fav');

Route::any('/user/contactcaterer', 'UserController@contactcaterer');
Route::any('/home/deletemodifyorder/{id}', 'HomeController@deletemodifyorder');


/* * ** orders route ** */


Route::any('/admin/order/admin_index', array('as' => 'admin.orders', 'uses' => 'OrderController@showAdmin_index'));
Route::any('/admin/order/suborders/{slug}', array('as' => 'admin.innerorder', 'uses' => 'OrderController@showAdminSub_view'));

Route::any('/admin/order/view/{id}', 'OrderController@showAdmin_view');

Route::any('/order/view/{id}', 'OrderController@showView');
Route::any('/order/myorders', 'OrderController@showMyorders');
Route::any('/order/favorders', 'OrderController@showFavorders');
Route::any('/order/myorders/{slug}', 'OrderController@showMyorders');
Route::any('/order/mainorders', 'OrderController@showMainorders');
Route::any('/order/receivedorders', 'OrderController@showreceivedorders');
Route::any('/order/receivedview/{id}', 'OrderController@showreceivedview');

Route::any('/order/receivedviewadmin/{id}', 'OrderController@showreceivedviewadmin');

Route::any('/order/courierorders', 'OrderController@showcourierorders');
Route::any('/order/courierview/{id}', 'OrderController@showcourierview');
Route::any('/order/cancelOrder/{id}', 'OrderController@cancelOrder');
Route::any('/order/notify_customer/{id}', 'OrderController@notify_customer');
Route::any('/order/modifyorders/{id}/{slug}', 'OrderController@showModifyorders');



/* * ** review page route ** */
Route::any('/admin/reviews/admin_index', array('as' => 'admin.reviews', 'uses' => 'ReviewsController@showAdmin_index'));
Route::get('/admin/reviews/Admin_active/{id}', 'ReviewsController@showAdmin_active');
Route::get('/admin/reviews/Admin_deactive/{id}', 'ReviewsController@showAdmin_deactive');
Route::get('/admin/reviews/Admin_delete/{id}', 'ReviewsController@showAdmin_delete');

/* * ** coupons route ** */
Route::any('/admin/coupon/admin_index', array('as' => 'admin.coupon.admin_index', 'uses' => 'CouponController@showAdmin_index'));
Route::any('/admin/coupon/admin_add', 'CouponController@showAdmin_add');
Route::get('/admin/coupon/Admin_active/{id}', 'CouponController@showAdmin_active');
Route::get('/admin/coupon/Admin_deactive/{id}', 'CouponController@showAdmin_deactive');
Route::get('/admin/coupon/Admin_delete/{id}', 'CouponController@showAdmin_delete');


/* * ** Mealtype route ** */
Route::any('/admin/mealtype/admin_index', array('as' => 'admin.admin_index', 'uses' => 'MealtypeController@showAdmin_index'));
Route::any('/admin/mealtype/admin_add', 'MealtypeController@showAdmin_add');
Route::get('/admin/mealtype/Admin_edit/{slug}', 'MealtypeController@showAdmin_edit');
Route::post('/admin/mealtype/Admin_edit/{slug}', 'MealtypeController@showAdmin_edit');
Route::get('/admin/mealtype/Admin_active/{id}', 'MealtypeController@showAdmin_active');
Route::get('/admin/mealtype/Admin_deactive/{id}', 'MealtypeController@showAdmin_deactive');
Route::get('/admin/mealtype/Admin_delete/{id}', 'MealtypeController@showAdmin_delete');


Route::post('/user/ordersound', 'OrderController@order_sound'); 

Route::any('/users/gmaillogin', 'UserController@gmaillogin');
Route::any('/home/applycoupon', 'HomeController@applycoupon');


Route::any('/home/applycoupon', 'HomeController@applycoupon');
Route::any('/home/removecoupon', 'HomeController@removecoupon');


Route::any('/cron/reviewnotification', 'HomeController@cronnotificationconfirm');


Route::get('/user/kitchenstaff', 'RestaurantController@showManagekitchenstaff');
Route::get('/user/addkitchenstaff', 'RestaurantController@showAddkitchenstaff');
Route::post('/user/addkitchenstaff', 'RestaurantController@showAddkitchenstaff');
Route::get('/user/editkitchenstaff/{id}', 'RestaurantController@showEditkitchenstaff');
Route::post('/user/editkitchenstaff/{id}', 'RestaurantController@showEditkitchenstaff');
Route::get('/user/deletekitchenstaff/{id}', 'RestaurantController@showDeletekitchenstaff');


// Delivery Person
Route::get('/user/deliveryperson', 'RestaurantController@showManagedeliveryperson');
Route::get('/user/adddeliveryperson', 'RestaurantController@showAdddeliveryperson');
Route::post('/user/adddeliveryperson', 'RestaurantController@showAdddeliveryperson');
Route::get('/user/editdeliveryperson/{id}', 'RestaurantController@showEditdeliveryperson');
Route::post('/user/editdeliveryperson/{id}', 'RestaurantController@showEditdeliveryperson');
Route::get('/user/deletedeliveryperson/{id}', 'RestaurantController@showDeletedeliveryperson');



/* API CONTROLLER */

Route::any('/api/register', 'ApiController@register');
Route::any('/api/restroregister', 'ApiController@restroregister');
Route::any('/api/login', 'ApiController@login');
Route::any('/api/forgotpassword', 'ApiController@forgotpassword');
Route::any('/api/getCity', 'ApiController@getCity');
Route::any('/api/getArea', 'ApiController@getArea');
Route::any('/api/editProfile', 'ApiController@editProfile');
Route::any('/api/changePassword', 'ApiController@changePassword');
Route::any('/api/showList', 'ApiController@showList');
Route::any('/api/showManagemenu', 'ApiController@showManagemenu');

Route::any('/api/getMealType', 'ApiController@getMealType');
Route::any('/api/getCuisine', 'ApiController@getCuisine');
Route::any('/api/getBannerList', 'ApiController@getBannerList');
Route::any('/api/logout', 'ApiController@logout');
Route::any('/api/changePicture', 'ApiController@changePicture');

Route::any('/api/showMenu', 'ApiController@showMenu');
Route::any('/api/addressList', 'ApiController@addressList');
Route::any('/api/addAddress', 'ApiController@addAddress');
Route::any('/api/editAddress', 'ApiController@editAddress');
Route::any('/api/deleteAddress', 'ApiController@deleteAddress');

Route::any('/api/getAdminConfigurationProduct', 'ApiController@getAdminConfigurationProduct');
Route::any('/api/applyCoupon', 'ApiController@applyCoupon');

Route::any('/api/checkout', 'ApiController@cartCheckout');
Route::any('/api/paymentRequest', 'ApiController@paymentRequest');
Route::any('/api/successPayment', 'ApiController@successPayment');
Route::any('/api/cancelPayment', 'ApiController@cancelPayment');

Route::any('/api/orderHistory', 'ApiController@showOrderHistorylist');
Route::any('/api/orderDetails', 'ApiController@showOrdeDetails');
Route::any('/api/couponList', 'ApiController@showCouponList');
Route::any('/api/addCoupon', 'ApiController@addCouponRest');
Route::any('/api/restOrderHistory', 'ApiController@showResOrderHistorylist');
Route::any('/api/restDetails', 'ApiController@showrestdetails');
Route::any('/api/addMenu', 'ApiController@showaddMenuitem');
Route::any('/api/addVariant', 'ApiController@showAddvariant');
Route::any('/api/addAddons', 'ApiController@showAddaddons');
Route::any('/api/paymentHistory', 'ApiController@showPaymentHistory');
Route::any('/api/addAdditionalStatus', 'ApiController@showAddAdditionalStatus');
Route::any('/api/additionalStatusList', 'ApiController@showAdditionalStatusList');
Route::any('/api/manageDeliveryCharges', 'ApiController@showManageDelivery');
Route::any('/api/manageOpeninghours', 'ApiController@showManageOpeninghours');
Route::any('/api/deleteCoupon', 'ApiController@showDeleteCoupon');
Route::any('/api/activeDeactiveCoupon', 'ApiController@showActiveDeactiveCoupon');
Route::any('/api/reviewList', 'ApiController@showReviewList');
Route::any('/api/favoriteList', 'ApiController@showFavoriteList');
Route::any('/api/deleteFavorite', 'ApiController@showDeleteFavorite');
Route::any('/api/addReview', 'ApiController@showAddReview');
Route::any('/api/contactus', 'ApiController@showContactus');
Route::any('/api/viewProfile', 'ApiController@showProfile');
Route::any('/api/addonList', 'ApiController@addonList');
Route::any('/api/variantsList', 'ApiController@variantsList');
Route::any('/api/deleteAddon', 'ApiController@showDeleteAddon');
Route::any('/api/deleteVariant', 'ApiController@showDeleteVariant');
Route::any('/api/changeOrderStatus', 'ApiController@showChangeOrderStatus');
Route::any('/api/editAdditionaStatus', 'ApiController@showeEditAdditionaStatus');
Route::any('/api/orderStatus', 'ApiController@showeOrderStatus');
Route::any('/api/packageList', 'ApiController@showPackageList');
Route::any('/api/packagePyament', 'ApiController@showPackagePyament');
Route::any('/api/deleteMenu', 'ApiController@showdeleteMenu');
Route::any('/api/openingHours', 'ApiController@showOpeningHours');
Route::any('/api/deliveryCharges', 'ApiController@showDeliveryCharges');



/////////////////////Rest app /////////////////////////////
Route::any('/api/restReviewList', 'ApiController@showRestReviewList');  

Route::any('/api/getAdminConfiguration', 'ApiController@getAdminConfiguration');
Route::any('/api/getUserDataWithStatus', 'ApiController@getUserDataWithStatus');


/////////////////kitchan Staff//////////////////////

Route::any('/api/staffRequestOrder', 'ApiController@showStaffOrderList');
Route::any('/api/staffChangeStatus', 'ApiController@showStaffChangeStatus');
Route::any('/api/staffOrderHistory', 'ApiController@showStaffOrderHistoryList');

////////////////Delivery person///////////////////

Route::any('/api/deliveryPersonOrder', 'ApiController@showDeliveryPerOrderList');
Route::any('/api/deliverChangeStatus', 'ApiController@showDeliverChangeStatus');
Route::any('/api/deliverOrderHistory', 'ApiController@showDeliverOrderHistoryList');


Route::any('/api/showActiveRestaurantList', 'ApiController@showActiveRestaurantList');
Route::any('/api/staffRegister', 'ApiController@staffRegister');


Route::get('/user/activedeliveryperson/{id}', 'UserController@activedeliveryperson');
Route::get('/user/deactivedeliveryperson/{id}', 'UserController@deactivedeliveryperson');
Route::get('/user/activekitchenstaff/{id}', 'UserController@activekitchenstaff');
Route::get('/user/deactivekitchenstaff/{id}', 'UserController@deactivekitchenstaff');

