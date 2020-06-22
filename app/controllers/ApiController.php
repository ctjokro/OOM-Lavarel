<?php

use Moltin\Cart\Cart;
//use Moltin\Cart\Storage\CartSession;
use Moltin\Cart\Identifier\Cookie;
use App\Classes\ImageManipulator;

class ApiController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default User Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add the route:
      |
      |	Route::get('/', 'HomeController@showWelcome');
      |
     */

    protected $layout = 'layouts.default';

    //create slug 
    public function createSlug($string) {
        $string = substr(strtolower($string), 0, 35);
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $return = strtolower(preg_replace($old_pattern, $new_pattern, $string)) . rand(111111, 9999999) . time();
        return $return;
    }

    //create order number
    public function createOrderNumber() {
        $lastOrder = DB::table('orders')
                        ->orderBy('id', 'DESC')->first();
        if (!empty($lastOrder)) {
            $lastOrderId = $lastOrder->id;
            $lastOrderId = $lastOrderId + 1;
        } else {
            $lastOrderId = 00001;
        }
        $st = "FOS";
        $orderNum = $st . sprintf('%06d', $lastOrderId) . rand(1, 9);
        return $orderNum;
    }

    //create register
    public function register() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            //print_r($dataJson); exit;
            $email_address = trim($dataJson['email_address']);


            if (empty($email_address)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $ifexist = DB::table('users')
                    ->where('email_address', $email_address)
                    ->first();
            //print_r($ifexist); exit;
            if (!empty($ifexist)) {
                echo $this->errorOutput('Email address is already exists.');
                exit;
            }

            $first_name = $dataJson['first_name'];
            $last_name = $dataJson['last_name'];
            $contact_number = $dataJson['contact_number'];

            $saveUser = array(
                'first_name' => $dataJson['first_name'],
                'last_name' => $dataJson['last_name'],
                'email_address' => $email_address,
                'contact' => $dataJson['contact_number'],
                'device_id' => $dataJson['device_id'],
                'device_type' => $dataJson['device_type'],
                'language' => $dataJson['language'],
                'password' => md5($dataJson['password']),
                'slug' => $this->createUniqueSlug($dataJson['first_name'], 'users'),
                'user_type' => "Customer",
                'created' => date('Y-m-d H:i:s'),
            );

            DB::table('users')->insert(
                    $saveUser
            );


            $user_id = DB::getPdo()->lastInsertId();

            // setup for activation link
            $reset_data = array(
                'user_id' => $user_id,
                'status' => '1',
                'type' => 'signup',
                'created' => date('Y-m-d H:i:s'),
                'code' => rand(90786778678, 8978978867857678),
                'slug' => $this->createUniqueSlug($dataJson['first_name'], 'reset_link'),
            );

            DB::table('reset_link')->insert(
                    $reset_data
            );

            $reset_link = HTTP_PATH . "activateprofile/" . $reset_data['slug'];

            // send email to user
            $userEmail = $dataJson['email_address'];

            // send email to administrator
            $mail_data = array(
                'text' => 'Your account is successfully created. Your registration is being reviewed and pending acceptance. After acceptance you can use below credentials for login to ' . SITE_TITLE . '.',
                'email' => $dataJson['email_address'],
                'password' => $dataJson['password'],
                'firstname' => $dataJson['first_name'],
                'resetLink' => '<a href="' . $reset_link . '">Click here<a> to activate your account.'
            );
            // print_r($mail_data); exit;
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created');
            });

            // get admin data
            $adminuser = DB::table('admins')
                    ->where('id', 1)
                    ->first();
            $adminEmail = $adminuser->email;

            // send email to administrator
            $mail_data = array(
                'text' => 'A request for new account has been received on ' . SITE_TITLE . '. Below are the details.',
                'name' => $first_name,
                'contact_number' => $contact_number,
                'email_address' => $email_address,
                'adminEmail' => $adminEmail,
                'firstname' => "Admin"
            );

            //   return View::make('emails.template')->with($mail_data); // to check mail template data to view
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['adminEmail'], $mail_data['firstname'])->subject('New Account Request on ' . SITE_TITLE . ' for Customer');
            });

            echo $this->successOutput('Congratulation! You are registered successfully, please check your email to activate your account. We are checking your details and will contact you shortly.');
            exit;
            //  return Redirect::to('/user/register')->withSuccess('You have successfully register on ' . SITE_TITLE);
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //create resto register
    public function restroregister() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        //print_r($_REQUEST); exit;

        if (!empty($dataJson)) {

            $email_address = trim($dataJson['email_address']);

            if (empty($email_address)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $ifexist = DB::table('users')
                    ->where('email_address', $email_address)
                    ->first();

            if (!empty($ifexist)) {
                echo $this->errorOutput('Email address is already exists.');
                exit;
            }

            $name = $dataJson['name'];
            $contact_number = $dataJson['contact_number'];
            $location = $dataJson['location'];

            $saveUser = array(
                'first_name' => $dataJson['name'],
                'city' => $dataJson['city'],
                'area' => $dataJson['area'],
                'email_address' => $email_address,
                'contact' => $dataJson['contact_number'],
                'device_id' => $dataJson['device_id'],
                'device_type' => $dataJson['device_type'],
                'paypal_email_address' => $dataJson['paypal_email_address'],
                'password' => md5($dataJson['password']),
                'slug' => $this->createUniqueSlug($dataJson['name'], 'users'),
                'user_type' => "Restaurant",
                'created' => date('Y-m-d H:i:s'),
            );

            DB::table('users')->insert(
                    $saveUser
            );

            $user_id = DB::getPdo()->lastInsertId();


//            $data = array(
//                'user_id' => $user_id,
//                'open_days' => 'mon',
//                'status' => '1',
//                'start_time' => "08:00:00",
//                'end_time' => "23:00:00",
//                'created' => date('Y-m-d H:is')
//            );

            $data = array(
                'user_id' => $user_id,
                'open_days' => 'mon,tue,wed,thu,fri,sat,sun',
                'status' => '1',
                'start_time' => "08:00:00,08:00:00,08:00:00,08:00:00,08:00:00,08:00:00,08:00:00",
                'end_time' => "23:00:00,23:00:00,23:00:00,23:00:00,23:00:00,23:00:00,23:00:00",
                'created' => date('Y-m-d H:is')
            );

            DB::table('opening_hours')
                    ->insert($data);


            // setup for activation link
            $reset_data = array(
                'user_id' => $user_id,
                'status' => '1',
                'type' => 'signup',
                'created' => date('Y-m-d H:i:s'),
                'code' => rand(90786778678, 8978978867857678),
                'slug' => $this->createUniqueSlug($dataJson['name'], 'reset_link'),
            );


            DB::table('reset_link')->insert(
                    $reset_data
            );

            $reset_link = HTTP_PATH . "activateprofile/" . $reset_data['slug'];

            // send email to user
            $userEmail = $dataJson['email_address'];

            // send email to administrator
            $mail_data = array(
                'text' => 'Your account is successfully created. Your registration is being reviewed and pending acceptance. After acceptance you can use below credentials for login to ' . SITE_TITLE . '.',
                'email' => $dataJson['email_address'],
                'password' => $dataJson['password'],
                'firstname' => $dataJson['name'],
                'resetLink' => '<a href="' . $reset_link . '">Click here<a> to activate your account.'
            );

            // return View::make('emails.template')->with($mail_data); // to check mail template data to view
//                die;

            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created as Restaurant');
            });




            // get admin data
            // get admin data
            $adminuser = DB::table('admins')
                    ->where('id', 1)
                    ->first();
            $adminEmail = $adminuser->email;

            // send email to administrator
            $mail_data = array(
                'text' => 'A request for new account has been received on ' . SITE_TITLE . '. Below are the details.',
                'name' => $name,
                //  'location' => $location ? $location :($userData->city_name.''.$userData->area_name ? $userData->city_name.' , '.$userData->area_name :''),
                'contact_number' => $contact_number,
                'email_address' => $email_address,
                'adminEmail' => $adminEmail,
                'firstname' => 'Admin'
            );

            //  return View::make('emails.template')->with($mail_data); // to check mail template data to view

            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['adminEmail'], $mail_data['firstname'])->subject('New Account Request on ' . SITE_TITLE . ' for Restaurant');
            });

            echo $this->successOutput('Congratulation! You are registered successfully, please check your email to activate your account. We are checking your details and will contact you shortly.');
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //login module 
    public function login() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {

            $email_address = $dataJson['email'];

            if (empty($email_address)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $planPass = $dataJson['password'];
            $password = md5($dataJson['password']);

            if (empty($password)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }
//
//            $user_type = $dataJson['user_type'];
//
//            if (empty($user_type)) {
//                echo $this->errorOutput('Invalid Request');
//                exit;
//            }

            $userData = DB::table('users')
                    ->leftjoin("areas", 'areas.id', '=', 'users.area')
                    ->leftjoin("cities", 'cities.id', '=', 'users.city')
                    ->where('email_address', $email_address)
                    ->where('password', $password)
                    ->select("users.*", 'cities.name as city_name', 'areas.name as area_name')
                    ->first();

            if (!empty($userData)) {

//                if ($userData->user_type <> $user_type) {
//                    echo $this->errorOutput('please login with correct user details');
//                    exit;
//                }

                if ($userData->activation_status == 0) {
                    echo $this->errorOutput('Your email address is not verified yet. Please check your email for verification link to verify your profile.');
                    exit;
                }

                // check admin approval
                if ($userData->approve_status == 0) {
                    echo $this->errorOutput('We are checking your details and will contact you shortly once we approve your account.');
                    exit;
                }

                // check activation status
                if ($userData->status == 0) {
                    echo $this->errorOutput('Your account might have been temporarily disabled.');
                    exit;
                }

                $device_id = $dataJson['device_id'];
                $device_type = $dataJson['device_type'];
                $language = $dataJson['language'];

                DB::table('users')
                        ->where('id', $userData->id)
                        ->update(['device_id' => "$device_id", 'device_type' => "$device_type", 'language' => "$language"]);

                if (empty($userData->profile_image)) {
                    $userData->profile_image = "";
                }
                if (empty($userData->country_id)) {
                    $userData->country_id = 0;
                }
                if (empty($userData->address)) {
                    $userData->address = "";
                }

                echo $this->output(str_replace(":null", ':""', json_encode($userData)));
                exit;
            } else {
                echo $this->errorOutput('Invalid email or password.');
                exit;
            }
        }
    }

    //forgot password 
    public function forgotpassword() {

        // echo "<pre>"; print_r($_POST); exit;

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        //     echo "<pre>"; print_r($_POST['data']); exit;

        if (!empty($dataJson)) {

            $email_address = $dataJson['email'];


            if (empty($email_address)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            // create our user data for the authentication
            $userData = DB::table('users')
                    ->where('email_address', $email_address)
                    ->first();

            // echo "<pre>"; print_r($userData); exit;

            if (!empty($userData)) {
                $userEmail = $userData->email_address;

                $userid = md5($userData->id);
                $user_id = $userData->id;
                $resetLink = "<a href='" . HTTP_PATH . "user/resetPassword/" . $user_id . "/" . $userid . "'>Click here</a> for reset your password</a>";

                // send email to user
                $mail_data = array(
                    'text' => 'Please reset your password.',
                    'email' => $userData->email_address,
                    'resetLink' => $resetLink,
                    'firstname' => $userData->first_name . ' ' . $userData->last_name,
                );

                $data = array(
                    'forget_password_status' => '1'
                );

                //echo "<pre>"; print_r($mail_data); exit;

                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);

                // return View::make('emails.template')->with($mail_data); // to check mail template data to view



                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['email'], $mail_data['firstname'])->subject('Reset Your Password');
                });

                echo $this->successOutput('A link to reset your password was sent to your email address. Please reset your password');
                exit;
            } else {

                // return error message
                echo $this->errorOutput('We cannot recognize your email address.');
                exit;
            }
        }
    }

    //show reset password 
    public function showResetPassword($user_id = null, $md_user_id = null) {

        $this->layout->title = TITLE_FOR_PAGES . 'Reset Password';
        $this->layout->content = View::make('/Users/resetPassword');

        if (Session::has('user_id')) {
            return Redirect::to('/user/myaccount');
        }


        $dataJson = Input::all();
        if (!empty($dataJson)) {

            $password = md5($dataJson['password']);
            $rules = array(
                'password' => 'required|min:8', // make sure the password field is not empty
                'cpassword' => 'required', // make sure the confirm password field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($dataJson, $rules);
            if ($validator->fails()) {
                return Redirect::to('/user/resetPassword')
                                ->withErrors($validator) // send back all errors to the login form
                                ->withInput(Input::except('password'));
            } else {

                // create our user data for the authentication
                $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->first();


                if (!empty($userData)) {
                    // check activation status
                    if ($userData->forget_password_status == 0) {
                        Session::put('captcha', 1);
                        Session::put('error_message', "You have already use this link.");
                        return Redirect::to('/user/resetPassword/' . $user_id . '/' . $md_user_id);
                    }
                    DB::table('users')
                            ->where('id', $user_id)
                            ->update(['password' => $password]);

                    DB::table('users')
                            ->where('id', $user_id)
                            ->update(['forget_password_status' => 0]);




                    // return to dashboard page
                    Session::put('success_message', "Your password changed succesfully. Please login.");
                    return Redirect::to('/');
                } else {

                    // return error message
                    Session::put('captcha', 1);
                    Session::put('error_message', "Invalid email or password");
                    return Redirect::to('/user/resetPassword/' . $user_id . '/' . $md_user_id);
                }
            }
        }
    }

    //get city list
    public function getCity() {

        $this->checkAPI($_REQUEST['api_key']);
        $cityData = DB::table('cities')
                ->select('cities.id', 'cities.name')
                ->where('cities.status', 1)
                ->orderBy('cities.name', 'asc')
                ->get();
        echo $this->output(json_encode($cityData));
        exit;
    }

    //get area by city id
    //get area by city id
    public function getArea() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {

            $cityData = DB::table('areas')
                    ->select('areas.id', 'areas.name')
                    ->where('areas.status', 1)
                    ->where('areas.city_id', $dataJson['city_id'])
                    ->orderBy('areas.name', 'asc')
                    ->get();

            echo $this->output(json_encode($cityData));
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //get user profile data via user_id
    public function showProfile() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            $user_id = $dataJson['user_id'];

            $userData = DB::table('users')
                    ->leftjoin("areas", 'areas.id', '=', 'users.area')
                    ->leftjoin("cities", 'cities.id', '=', 'users.city')
                    ->where('users.id', $user_id)
                    ->select("users.*", 'cities.name as city_name', 'areas.name as area_name')
                    ->first();

            if ($userData) {
                echo $this->output(str_replace(":null", ':""', json_encode($userData)));
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //edit profile data of user
    public function editProfile() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        $user_id = $dataJson['user_id'];

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        if (!empty($dataJson)) {
            if ($userData->user_type == "Restaurant") {

                $rules = array(
                    'first_name' => 'required', // make sure the first name field is not empty
                    'contact' => 'required', // make sure the  contact field is not empty
                    'address' => 'required', // make sure the address field is not empty
                );
            } else {
                $rules = array(
                    'first_name' => 'required', // make sure the first name field is not empty
                    'last_name' => 'required', // make sure the last name field is not empty
                    'contact' => 'required', // make sure the  contact field is not empty
                    'address' => 'required', // make sure the address field is not empty
                );
            }

            if ($userData->user_type == "Restaurant") {

                $data = array(
                    'first_name' => $dataJson['first_name'],
                    'city' => $dataJson['city'],
                    'area' => $dataJson['area'],
                    'address' => $dataJson['address'],
                    'contact' => $dataJson['contact'],
                );
            } else {
                $data = array(
                    'first_name' => $dataJson['first_name'],
                    'last_name' => $dataJson['last_name'],
                    'city' => $dataJson['city'],
                    'area' => $dataJson['area'],
                    'contact' => $dataJson['contact'],
                    'address' => $dataJson['address'],
                );
            }


            if ($userData->user_type == 'Restaurant') {
                // $data['deliver_to'] = implode(",", $dataJson['deliver_to']);
            }


            DB::table('users')
                    ->where('id', $user_id)
                    ->update($data);


            $userData = DB::table('users')
                    ->leftjoin("areas", 'areas.id', '=', 'users.area')
                    ->leftjoin("cities", 'cities.id', '=', 'users.city')
                    ->where('users.id', $user_id)
                    ->select("users.*", 'cities.name as city_name', 'areas.name as area_name')
                    ->first();


            if (empty($userData->profile_image)) {
                $userData->profile_image = "";
            }
            if (empty($userData->country_id)) {
                $userData->country_id = 0;
            }
            if (empty($userData->address)) {
                $userData->address = "";
            }

            $data['response_status'] = 'success';
            $data['response_msg'] = 'Profile information updated successfully.';
            $data['response_data'] = $userData;

            echo json_encode($data);
            exit;
        }
    }

    //change password of user account
    public function changePassword() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);


        $user_id = $dataJson['user_id'];
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();


        if (!empty($dataJson)) {
            $new_password = $dataJson['new_password'];
            $confirm_password = $dataJson['confirm_password'];
            $rules = array(
                'new_password' => 'required|min:8', // make sure the new password field is not empty
                'confirm_password' => 'required' // make sure the confirm password field is not empty
            );

            $oldDBpassword = $userData->password;

            $opassword = md5($dataJson['old_password']);



            $newPassword = md5($new_password);

            if ($oldDBpassword == $newPassword) {

                // return error message
                echo $this->errorOutput('Please do not enter new password same as old password');
                exit;
            }
            if ($oldDBpassword <> $opassword) {

                // return error message
                echo $this->errorOutput("Please enter correct old password");
                exit;
            } else {

                $data = array(
                    'password' => $newPassword,
                );
                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);

                $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->first();


                if (empty($userData->profile_image)) {
                    $userData->profile_image = "";
                }
                if (empty($userData->country_id)) {
                    $userData->country_id = 0;
                }
                if (empty($userData->address)) {
                    $userData->address = "";
                }

                $data['response_status'] = 'success';
                $data['response_msg'] = 'Pasword updated successfully.';
                $data['response_data'] = $userData;

                echo json_encode($data);
                exit;
            }
        }
    }

    //Customer app 
    //order listing for customer app
    public function showList() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);



        $query = DB::table('users');
        $query->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id');
        $query->leftjoin('areas', 'areas.id', '=', 'users.area');
        $query->leftjoin('cities', 'cities.id', '=', 'users.city');
        $query->leftjoin("menu_item", 'menu_item.user_id', '=', 'users.id')->groupBy('menu_item.user_id');

        $query->where('users.status', "=", 1);
        $query->where('users.approve_status', "=", 1);
        $query->where('users.user_type', "=", "Restaurant");


        // echo "<pre>"; print_r($dataJson); exit;
        // get search params
        $city = isset($dataJson['city']) ? addslashes($dataJson['city']) : "";
        $area = isset($dataJson['area']) ? addslashes($dataJson['area']) : "";
        $keyword = isset($dataJson['keyword']) ? addslashes($dataJson['keyword']) : "";


        if (isset($dataJson['catering_type']) && !empty($dataJson['catering_type'])) {
            $catering_type = explode(",", $dataJson['catering_type']);
        } else {
            $catering_type = "";
        }

        if (isset($dataJson['cuisine']) && !empty($dataJson['cuisine'])) {
            $cuisine = explode(",", $dataJson['cuisine']);
        } else {
            $cuisine = "";
        }



        //$cuisine = isset($dataJson['cuisine']) ? explode(",", $dataJson['cuisine']) : array();

        $sort = isset($dataJson['sort']) ? addslashes($dataJson['sort']) : "";
        $order = isset($dataJson['order']) ? addslashes($dataJson['order']) : "asc";




        //$cuisine = (isset($dataJson['cuisine']) and $dataJson['cuisine'][0]) ? $dataJson['cuisine'] : array();
        //$catering_type = (isset($dataJson['catering_type']) and $dataJson['catering_type'][0]) ? $dataJson['catering_type'] : array();
        //
        //$query = User::sortable();
        //$query->leftjoin("menu_item", 'menu_item.user_id', '=', 'users.id')->groupBy('menu_item.user_id');

        if (!empty($cuisine)) {
            //print_r($cusinaname); exit;
            $query
                    ->where(function ($query) use ($cuisine) {
                        foreach ($cuisine as $c)
                            $query->orwhere('menu_item.cuisines_id', '=', $c);
                    });
        }
        if ($city)
            $query->where('users.city', '=', $city);
        if ($area)
            $query->where('users.area', '=', $area);
        //$query->whereRaw("FIND_IN_SET('$area',users.area)");




        if (!empty($catering_type)) {
            $query->where(function ($query) use ($catering_type) {
                foreach ($catering_type as $c)
                    $query->orwhereRaw("FIND_IN_SET('$c',tbl_opening_hours.catering_type)");
            });
        }
        if ($keyword) {
            $keyword = trim($keyword);
            if (!empty($keyword)) {

                $cusinaname = DB::table('cuisines')
                        ->select('cuisines.id')
                        ->where("cuisines.name", "like", "%$keyword%")
                        ->first();
                if ($cusinaname) {
                    //print_r($cuisine);
                    if (isset($cuisine) && count($cuisine) > 0) {
                        $query->orwhere(function ($query) use ($cusinaname) {
                            foreach ($cusinaname as $c)
                            //echo 'menu_item.cuisines_id'.$c;
                                $query->where('menu_item.cuisines_id', '=', $c);
                        });
                    } else {
                        $query->where(function ($query) use ($cusinaname) {
                            foreach ($cusinaname as $c)
                            //echo 'menu_item.cuisines_id'.$c;
                                $query->where('menu_item.cuisines_id', '=', $c);
                        });
                    }
                } else {
                    $query->where('menu_item.item_name', "LIKE", "%$keyword%")
                            ->OrWhere(DB::raw("CONCAT_WS(' ', tbl_users.first_name,tbl_users.last_name)"), "like", "%$keyword%");
                }
                //print_r($cusinaname); exit;
//               
            }

//            $query->orwhere('menu_item.item_name', "=", "$keyword");
//            $query->orwhere(DB::raw("CONCAT_WS(' ', tbl_users.first_name,tbl_users.last_name)"), "like", "%$keyword%");
        }

        //echo "hello"; exit;
        //print_r($keyword);
        // Get all the users
//        $query->orderBy('users.id', 'desc')
//                        ->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
//                        ->leftjoin('areas', 'areas.id', '=', 'users.area')
//                        ->leftjoin('cities', 'cities.id', '=', 'users.city')
//                        ->where('users.status', "=", "1")
//                        ->where('users.approve_status', "=", "1")
//                        ->where('users.user_type', "=", "'Restaurant'");
////                        ->offset($page)
////                        ->limit($order_per_page)
        //echo $sort; echo $order; exit;

        if (!empty($dataJson['page'])) {
            $page = $dataJson['page'];
        }
        if (!empty($dataJson['order_per_page'])) {
            $order_per_page = $dataJson['order_per_page'];
        }

        if ($page > 1) {
            $page = $page - 1;
            $pageData = $page * $order_per_page;
        } else {
            $pageData = 0;
        }
        if (empty($pageData)) {
            $pageData = "0";
        }
        if (empty($page)) {
            $page = "0";
        }


//        if ($sort == "name") {
//            $query->orderBy('users.first_name', $order);
//        } elseif ($sort == "rating") {
//            $query->orderBy('rating', $order);
//        } else {
//           // $query->orderBy('users.id', 'desc');
//            $query->orderBy('rating', 'desc')
//                ->orderBy('users.featured', 'desc');
//            
//        }


        if ($sort == "name") {
            $query->orderBy('users.first_name', $order);
        } elseif ($sort == "rating") {
            $query->orderBy('rating', $order);
        } else {
            $query->orderBy('rating', 'desc')
                    ->orderBy('users.featured', 'desc')
                    ->orderBy('users.first_name', 'asc');
        }

        $total_data = '';
        $total_data = count($query->get(array('users.*', "opening_hours.catering_type", "areas.name as area_name", "cities.name as city_name", "opening_hours.open_close", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "opening_hours.open_days", DB::raw("(select (avg(tbl_reviews.quality)+avg(tbl_reviews.packaging))/2 from `tbl_reviews` where tbl_reviews.caterer_id = tbl_users.id and tbl_reviews.status = '1') as rating"), DB::raw("(select count(tbl_reviews.id) from `tbl_reviews` where tbl_reviews.caterer_id = tbl_users.id and tbl_reviews.status = '1') as counter"))));
        // $total_data;

        $query->offset($pageData);
        $query->limit($order_per_page);
//        $query->select('SQL_CALC_FOUND_ROWS');
        // $query->orderBy('rating', 'desc')
        //       ->orderBy('users.featured', 'desc');





        $restroData = $query->get(array('users.*', "opening_hours.catering_type", "areas.name as area_name", "cities.name as city_name", "opening_hours.open_close", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "opening_hours.open_days", DB::raw("(select (avg(tbl_reviews.quality)+avg(tbl_reviews.packaging))/2 from `tbl_reviews` where tbl_reviews.caterer_id = tbl_users.id and tbl_reviews.status = '1') as rating"), DB::raw("(select count(tbl_reviews.id) from `tbl_reviews` where tbl_reviews.caterer_id = tbl_users.id and tbl_reviews.status = '1') as counter")));

//        echo '<prE>'; print_r($restroData);die;

        $i = 0;
        $restroDt = array();

        //$count = sizeof($restroCount);
        // echo '<pre>'; print_r($restroData);die;
//        if ($restroData) {
//            foreach ($restroData as $restroDt) {
//                $cuisine = DB::table('cuisines')
//                ->orderBy('cuisines.name', 'asc')
//                ->where("menu_item.user_id", "=", $restroDt->id)
//                ->where("cuisines.status", "=", 1)
//                ->join('menu_item', 'cuisines.id', '=', 'menu_item.cuisines_id')
//                ->select("cuisines.name", "cuisines.id")
//                ->groupBy('cuisines.id')
//                ->get();
//                
//                //print_r($cuisine);
//                
//                $restroDt->CuisineData = $cuisine;
//                
//            }
//            


        if ($restroData) {
            foreach ($restroData as $restroDt) {
                $restro[$i] = (array) $restroDt;
                if (empty($restro[$i]['last_name'])) {
                    $restro[$i]['last_name'] = "";
                }
                if (empty($restro[$i]['country_id'])) {
                    $restro[$i]['country_id'] = "";
                }
                if (empty($restro[$i]['paypal_email_address'])) {
                    $restro[$i]['paypal_email_address'] = "";
                }
                if (empty($restro[$i]['device_id'])) {
                    $restro[$i]['device_id'] = "";
                }
                if (empty($restro[$i]['device_type'])) {
                    $restro[$i]['device_type'] = "";
                }
                if (empty($restro[$i]['language'])) {
                    $restro[$i]['language'] = "";
                }
                if (empty($restro[$i]['rating'])) {
                    $restro[$i]['rating'] = "";
                }

                if (!empty($restro[$i]['open_days'])) {
                    $restro[$i]['open_days'] = strtoupper($restroDt->open_days);
                }


                $cuisine = DB::table('cuisines')
                        ->orderBy('cuisines.name', 'asc')
                        ->where("menu_item.user_id", "=", $restroDt->id)
                        ->where("cuisines.status", "=", 1)
                        ->join('menu_item', 'cuisines.id', '=', 'menu_item.cuisines_id')
                        ->select("cuisines.name", "cuisines.id")
                        ->groupBy('cuisines.id')
                        ->get();

                //print_r($cuisine);

                $restro[$i]['cuisineData'] = $cuisine;



                if ($restroDt->catering_type != "") {
                    $meals = explode(',', $restroDt->catering_type);

                    $mealsTypes = DB::table('mealtypes')
                            ->select("mealtypes.name")
                            ->whereIn('id', $meals)
                            ->get();


                    $restro[$i]['mealTypeData'] = $mealsTypes;

                    $arr = array();
                    foreach ($mealsTypes as $mealsType) {
                        $arr[] = $mealsType->name;
                    }


                    $restro[$i]['meal_type'] = implode(", ", $arr);
                }


                $pickc = DB::table('pickup_charges')->where('user_id', $restroDt->id)->first();

                //print_r($pickc);
                if ($pickc) {
                    $restro[$i]['pickup'] = $pickc->pick_up;
                } else {
                    $restro[$i]['pickup'] = "";
                }

                $menu_itemNonveg = DB::table('menu_item')
                        ->where('user_id', $restroDt->id)
                        ->where('non_veg', '1')
                        ->first();
                $menu_itemveg = DB::table('menu_item')
                        ->where('user_id', $restroDt->id)
                        ->where('non_veg', '0')
                        ->first();

                $restro[$i]['is_nonveg'] = !empty($menu_itemNonveg) ? "1" : "0";
                $restro[$i]['is_veg'] = !empty($menu_itemveg) ? "1" : "0";


                if ($restroDt->open_close) {
                    $day = date('D');
                    $open_days = explode(",", $restroDt->open_days);

                    $vrrcy = explode(',', $restroDt->start_time);
                    $endty = explode(',', $restroDt->end_time);

                    $startime = array_combine($open_days, $vrrcy);
                    $endtime = array_combine($open_days, $endty);
                    $start = "";
                    if (isset($startime[strtolower(date('D'))])) {

                        $start = $startime[strtolower(date('D'))];
                    }
                    if (isset($endtime[strtolower(date('D'))])) {
                        $end = $endtime[strtolower(date('D'))];
                    }

                    if ((isset($start) && strtotime($start) <= time()) && (isset($end) && time() <= strtotime($end)) and in_array(strtolower(date('D')), $open_days)) {
                        $opencl = 1;
                    } else {
                        $opencl = 0;
                    }
                } else {
                    $opencl = 0;
                }

                $restro[$i]['open_clos'] = $opencl;




                $i++;
            }

            echo $this->outputs(str_replace(":null", ':""', json_encode($restro)), $total_data);
        } else {
            echo $this->errorOutput('No More Records.');
        }




        exit;
    }

    //show menu list for customer app 
    public function showMenu() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        $restaurant_id = $dataJson['restaurant_id'];
        if (empty($restaurant_id)) {
            echo $this->errorOutput('Restaurant is required field.');
            exit;
        }

        $sort = isset($dataJson['sort']) ? addslashes($dataJson['sort']) : "";
        $order = isset($dataJson['order']) ? addslashes($dataJson['order']) : "asc";

        $caterer = DB::table('users')
                ->where('users.id', $restaurant_id)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                ->leftjoin('areas', 'areas.id', '=', 'users.area')
                ->leftjoin('cities', 'cities.id', '=', 'users.city')
                ->select("users.*", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name", 'opening_hours.catering_type')
                ->first();

        //  echo '<prE>' ; print_r($caterer);die;

        if (empty($caterer)) {
            echo $this->errorOutput('Invalid Restaurant Id.');
            exit;
        }

        //echo "<pre>"; print_r($caterer);
        // get caterer menu
        $query = DB::table('menu_item')
                ->where("menu_item.user_id", "=", $caterer->id)
                ->where("menu_item.status", "=", '1');




        if ($sort == "price") {
            $query->orderBy('cuisines.name', 'asc');
            $query->orderBy('menu_item.price', $order);
        } elseif ($sort == "loved") {
            $query->orderBy('cuisines.name', 'asc');
            $query->orderBy('counter', $order);
        } elseif ($sort == "item_name") {
            $query->orderBy('cuisines.name', 'asc');
            $query->orderBy('menu_item.item_name', $order);
        } else {
            $query->orderBy('cuisines.name', 'asc');
        }


        $query->orderBy('cuisines.name', 'asc');
        $query->leftjoin('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id');

        $items = $query->get(array("cuisines.name as cuisines_name", "menu_item.*", DB::raw("(select count(tbl_favorite_menu.id) from `tbl_favorite_menu` where tbl_favorite_menu.menu_id = tbl_menu_item.id) as counter")));


        //  echo "<pre>"; print_r($items);
        // get restaurant cuisines
        $cuisine = DB::table('cuisines')
                ->orderBy('cuisines.name', 'asc')
                ->where("menu_item.user_id", "=", $caterer->id)
                ->where("cuisines.status", "=", 1)
                ->join('menu_item', 'cuisines.id', '=', 'menu_item.cuisines_id')
                ->select("cuisines.name", "cuisines.id")
                ->groupBy('cuisines.id')
                ->get();


        $newArray = array();
        $response = array();
        $cuisineArray = array();


        if ($items) {
            $j = 0;
            $k = 0;
//            echo '<pre>';print_r($items);die;
            foreach ($items as $item) {
                //echo $item->id;

                $value = $item->cuisines_name;
                $idd = $item->id;

                $variantData = DB::table('variants')
                        ->where('menu_id', $idd)
                        ->orderBy('parent', 'desc')
                        ->get();

                $addonData = DB::table('addons')
                        ->where('menu_id', $idd)
                        ->get();

                $item->variant = $variantData;
                $item->addon = $addonData;

                $newArray[$value][] = $item;


//                $newArray[$value][]['variant'] = $variantData;
//                $newArray[$value][]['addon'] = $addonData;


                $j++;
                // $k++;
            }


            //echo "<pre>";print_r($newArray); exit;
            //  exit;

            $i = 0;
            foreach ($cuisine as $c) {
                $cuisineArray[$i]['id'] = $c->id;
                $cuisineArray[$i]['name'] = $c->name;
                $i++;
            }




            $response[] = $newArray;


            $data['response_status'] = 'success';
            $data['response_msg'] = '';
            $data['response_data'] = $newArray;
            $data['response_data_cuisine'] = $cuisineArray;

            echo json_encode($data);
            exit;
        } else {
            echo $this->errorOutput('No Menu Added Yet.');
            exit;
        }
    }

    //get cuisone list
    public function getCuisine() {

        $this->checkAPI($_REQUEST['api_key']);
        $cuisineData = DB::table('cuisines')
                ->select('cuisines.id', 'cuisines.name')
                ->where('cuisines.status', 1)
                ->orderBy('cuisines.name', 'asc')
                ->get();
        echo $this->output(json_encode($cuisineData));
        exit;
    }

    //get meal type
    public function getMealType() {
        $this->checkAPI($_REQUEST['api_key']);
        $mealData = DB::table('mealtypes')
                ->select('mealtypes.id', 'mealtypes.name')
                ->where('mealtypes.status', 1)
                ->orderBy('mealtypes.name', 'asc')
                ->get();
        echo $this->output(json_encode($mealData));
        exit;
    }

    //get banner list
    public function getBannerList() {
        $this->checkAPI($_REQUEST['api_key']);
        $query = DB::table('banner');
        $query->where('banner.id', ">", '0');
        $result = $query->get();
        echo $this->output(json_encode($result));
        exit;
    }

    //get admin configuration product
    public function getAdminConfigurationProduct() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);


        $restaurant_id = $dataJson['restaurant_id'];
        if (empty($restaurant_id)) {
            echo $this->errorOutput('Restaurant is required field.');
            exit;
        }

        $deliveryInfo = DB::table('pickup_charges')
                ->where('user_id', $restaurant_id)
                ->first();

        $adminData = DB::table('admins')
                ->where('id', '1')
                ->first();

        $data = array(
            'deliveryInfo' => $deliveryInfo,
            'adminData' => $adminData,
        );

        echo $this->output(str_replace(":null", ':""', json_encode($data)));
        exit;
    }

    //apply coupon 
    public function applyCoupon() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);


        $restaurant_id = $dataJson['restaurant_id'];
        $user_id = $dataJson['user_id'];
        $coupon = $dataJson['coupon'];
        if (empty($restaurant_id)) {
            echo $this->errorOutput('Restaurant is required field.');
            exit;
        }
        if (empty($coupon)) {
            echo $this->errorOutput('Coupon is required field.');
            exit;
        }

        $menuData = DB::table('coupons')
                ->where('code', $coupon)
                ->where('start_time', "<=", date('Y-m-d'))
                ->where('end_time', ">=", date('Y-m-d'))
                ->where('status', '1')
                ->first();

//        echo '<pre>'; print_r($menuData);die;
//                    
        if (!empty($menuData)) {
            if ($menuData->user_id == 0 || $menuData->user_id == $restaurant_id) {

                $check = DB::table('applied_coupons')
                        ->where('coupon', $coupon)
                        ->where('user_id', $user_id)
                        ->where('restaurant_id', $restaurant_id)
                        ->first();

                if (!empty($check)) {
                    echo $this->errorOutput('Coupon code already used.');
                    exit;
                } else {
                    echo $this->output(str_replace(":null", ':""', json_encode($menuData)));
                    exit;
                }
            } else {
                echo $this->errorOutput('Coupon code is invalid.');
                exit;
            }
        } else {
            echo $this->errorOutput('Coupon code is invalid.');
            exit;
        }

        exit;
    }

    //change picture 
    public function changePicture() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        $user_id = $dataJson['user_id'];

        // echo '<prE>'; print_r($dataJson);die;

        if (!empty($dataJson)) {


            include("vendor/ImageManipulator.php");
            if (isset($_FILES['profile_image']) && $_FILES['profile_image'] != '') {
                $file = $_FILES['profile_image'];

                //    echo '<prE>'; print_r($_FILES); exit;

                $errors = array();

                $image = time() . $file['name'];
                $file_tmp = $file['tmp_name'];
                $file_size = $file['size'];
                $file_type = $file['type'];

                $tmpp = explode('.', $file['name']);
                $file_ext = end($tmpp);

                $expensions = array("jpeg", "jpg", "png");

                if (in_array($file_ext, $expensions) === false) {
                    $msgString = "Extension not allowed. Please upload .jpg or.png file.";
                    echo $this->errorOutput($msgString);
                    exit;
                }

                if ($file_size > 2097152) {
                    $msgString = "File size should be less than 2MB";
                    echo $this->errorOutput($msgString);
                    exit;
                }

                move_uploaded_file($file_tmp, UPLOAD_FULL_PROFILE_IMAGE_PATH . "/" . $image);

                //$file->move(UPLOAD_FULL_PROFILE_IMAGE_PATH, time() . $file->getClientOriginalName());
                $data = array(
                    'profile_image' => $image,
                );
                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);

                $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->first();
                // print_r($userData);die;

                if (empty($userData->profile_image)) {
                    $userData->profile_image = "";
                }
                if (empty($userData->country_id)) {
                    $userData->country_id = 0;
                }
                if (empty($userData->address)) {
                    $userData->address = "";
                }

                $data['response_status'] = 'success';
                $data['response_msg'] = 'Profile image updated successfully.';
                $data['response_data'] = $userData;

                echo json_encode($data);
                exit;
            } else {
                $msgString = "No Image Selected";
                echo $this->errorOutput($msgString);
                exit;
            }
        }
    }

    //logout
    public function logout() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        $user_id = $dataJson['user_id'];
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();


        if (!empty($dataJson)) {
            $data = array(
                'device_id' => "",
                'device_type' => ""
            );
            DB::table('users')
                    ->where('id', $user_id)
                    ->update($data);

            $data['response_status'] = 'success';
            $data['response_msg'] = 'Logout successfully.';
            $data['response_data'] = array();

            echo json_encode($data);
            exit;
        }
    }

    //address list
    public function addressList() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        $user_id = isset($dataJson['user_id']) ? addslashes($dataJson['user_id']) : "";
        if (empty($user_id)) {
            echo $this->errorOutput('Invalid Request.');
            exit;
        }

        $query = DB::table('addresses');
        $query->leftjoin('areas', 'areas.id', '=', 'addresses.area');
        $query->leftjoin('cities', 'cities.id', '=', 'addresses.city');
        $query->where('addresses.user_id', "=", $user_id);


        if (!empty($dataJson['page'])) {
            $page = $dataJson['page'];
        }
        if (!empty($dataJson['order_per_page'])) {
            $order_per_page = $dataJson['order_per_page'];
        }

        if ($page > 1) {
            $page = $page - 1;
            $pageData = $page * $order_per_page;
        } else {
            $pageData = 0;
        }
        if (empty($pageData)) {
            $pageData = "0";
        }
        if (empty($page)) {
            $page = "0";
        }

        $query->orderBy('addresses.id', 'desc');

        $query->offset($pageData);
        $query->limit($order_per_page);


        $addressData = $query->get(array('addresses.*', "areas.name as area_name", "cities.name as city_name"));
        //echo "<pre>"; print_r($addressData); exit;

        if (!empty($addressData)) {
            echo $this->output(str_replace(":null", ':""', json_encode($addressData)));
            exit;
        } else {
            echo $this->errorOutput('No Address Records Saved.');
            exit;
        }
        exit;
    }

    //add address
    public function addAddress() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
//        print_r($dataJson);
//        exit;
        if (!empty($dataJson)) {

            $user_id = trim($dataJson['user_id']);
            $input = $dataJson;

            if (empty($input['address_title']) || empty($input['address_type']) || empty($input['city']) || empty($input['area']) || empty($input['street_name']) || empty($input['phone_number'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }


            $data = array(
                'address_title' => $input['address_title'],
                'address_type' => $input['address_type'],
                'city' => $input['city'],
                'area' => $input['area'],
                'street_name' => $input['street_name'],
                'building' => $input['building'],
                'floor' => $input['floor'],
                'apartment' => $input['apartment'],
                'phone_number' => $input['phone_number'],
                'extension' => $input['extension'],
                'directions' => $input['directions'],
                'user_id' => $user_id,
                'created' => date('Y-m-d H:i:s'),
                'status' => '1',
                'slug' => $this->createSlug($input['address_title'])
            );

            $id = DB::table('addresses')->insertGetId(
                    $data
            );

            echo $this->successOutput('Address Added Successfully');
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //edit address
    public function editAddress() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            // print_r($dataJson); exit;
            $user_id = trim($dataJson['user_id']);
            $address_id = trim($dataJson['address_id']);
            $input = $dataJson;

            $data = array(
                'address_title' => $input['address_title'],
                'address_type' => $input['address_type'],
                'city' => $input['city'],
                'area' => $input['area'],
                'street_name' => $input['street_name'],
                'building' => $input['building'],
                'floor' => $input['floor'],
                'apartment' => $input['apartment'],
                'phone_number' => $input['phone_number'],
                'extension' => $input['extension'],
                'directions' => $input['directions'],
            );

            DB::table('addresses')
                    ->where('id', $address_id)
                    ->update($data);

            echo $this->successOutput('Address Updated Successfully');
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //delete address
    public function deleteAddress() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            //print_r($dataJson); exit;
            $user_id = trim($dataJson['user_id']);
            $address_id = trim($dataJson['address_id']);

            $addressData = DB::table('addresses')
                    ->where('user_id', $user_id)
                    ->where('id', $address_id)
                    ->first();

            if ($addressData) {
                DB::table('addresses')->where('id', $address_id)->delete();

                echo $this->successOutput('Address deleted Successfully');
                exit;
            } else {
                echo $this->errorOutput('No record found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //show manage menu
    public function showManagemenu() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);


        $user_id = $dataJson['user_id'];
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get my all menu        
        $query = DB::table('menu_item');
        $query->where('menu_item.user_id', $user_id)->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                ->select('menu_item.*', 'cuisines.name');

        if (!empty($dataJson['page'])) {
            $page = $dataJson['page'];
            $page = $page - 1;
        }
        if (!empty($dataJson['order_per_page'])) {
            $order_per_page = $dataJson['order_per_page'];
        }


        $pageData = $page * $order_per_page . ",";
        if (empty($pageData) || $pageData == $order_per_page) {
            $pageData = ", ";
        }

        $records = $query->orderBy('menu_item.id', 'desc')->offset($page)
                ->limit($order_per_page)
                ->get();


        // get all posted input
        $dataJson = Input::all();

        echo $this->output(json_encode(compact('records')));
        exit;
    }

    //Cart checkout
    public function cartCheckout() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
//        echo '<pre>'; print_r($dataJson);die;



        if (!empty($dataJson)) {
            $paykey = $dataJson['transaction_id'];
            include("vendor/paypalplatform.php");
            $detail = CallPaymentDetails($paykey, '', '');
            
            if (isset($detail) && isset($detail['paymentInfoList.paymentInfo(0).senderTransactionId'])) {
                
            } else {
                $msgg = "Payment is not processed yet.";
                echo $this->outputwithError($msgg);
                exit;
            }

            $transaction_id = $detail['paymentInfoList.paymentInfo(0).senderTransactionId'];
            $trackingIdd = $detail['trackingId'];
            $payKeyy = $detail['payKey'];


            $user_id = trim($dataJson['user_id']);
            $address_id = trim($dataJson['address_id']);
            $caterer_id = trim($dataJson['caterer_id']);
            $coupon_id = trim($dataJson['coupon_id']);

            $item_total = number_format($dataJson['item_total'], 2);
            $total = $dataJson['amount'];
            $input = $dataJson;
            $item = $dataJson['item'];


            $orderNumber = $this->createOrderNumber();
            $orderData = array(
                'address_id' => $address_id,
                'order_number' => $orderNumber,
                'user_id' => $user_id,
                'delivery_charge' => $input['delivery_charge'],
                'delivery_type' => $input['delivery_type'],
                'caterer_id' => $caterer_id,
                'item_total' => $item_total,
                'total' => $total,
                'tax' => $input['tax'],
                'discount' => $input['discount'],
                'status' => ucfirst($input['status']),
                'created' => date('Y-m-d H:i:s'),
                'slug' => $this->createSlug('order')
            );

            if (isset($input['pickup_ready']) && $input['pickup_ready'] == 1) {
                $orderData['pickup_ready'] = 1;
                $orderData['pickup_now'] = $input['pickup_now'];
                $orderData['pickup_time'] = $input['pickup_time'];
            }
            if ($input['discount'] > 0) {
                $couponData = array(
                    'coupon' => $coupon_id,
                    'user_id' => $user_id,
                    'restaurant_id' => $caterer_id,
                    'created' => date('Y-m-d H:i:s')
                );

                DB::table('applied_coupons')->insert(
                        $couponData
                );
            }
            DB::table('orders')->insert(
                    $orderData
            );

            $order_id = DB::getPdo()->lastInsertId();
            
//           die;

            $saveUser1 = array(
                'transaction_id' => $transaction_id,
                'trackingId' => $trackingIdd,
                'payKey' => $payKeyy,
                'user_id' => $user_id,
                'price' => $total,
                'slug' => "Pay-" . time(),
                'type' => "Purchase",
                'status' => "Complete",
                'order_id' => $order_id,
                'created' => date('Y-m-d'),
            );
            DB::table('payments')->insert(
                    $saveUser1
            );

            $shopData = DB::table('orders')
                    ->where('user_id', $user_id)
                    ->where('id', $order_id)
                    ->first();

            $tax = $shopData->tax;
            $delivery_charge = $shopData->delivery_charge;
            $discount = $shopData->discount;


            $adminuser = DB::table('admins')
                    ->where('id', '1')
                    ->first();


            $sumTotal = 0;
            $menu_items = array();
            if ($item) {
                foreach ($item as $cartData) {
                    $subtotal = 0;
                    $menu_items[] = $cartData['item_id'];
                    $menu_id = $cartData['item_id'];
                    $variant_type = "";

                    if (isset($cartData['variants'])) {
                        $explode = explode(',', $cartData['variants']);
                        if ($explode) {
                            foreach ($explode as $explodeVal) {
                                $addonV = DB::table('variants')
                                        ->where('variants.id', $explodeVal)
                                        ->first();
                                if ($addonV) {
                                    $sub_total = $addonV->price * $cartData['quantity'];
                                    $subtotal = $subtotal + $sub_total;
                                }
                            }
                            $variant_type = $cartData['variants'];
                        }
                    }

                    $addons = "";
                    if (isset($cartData['addons'])) {
                        $explode = explode(',', $cartData['addons']);
                        if ($explode) {
                            foreach ($explode as $explodeVal) {

                                $addonV = DB::table('addons')
                                        ->where('addons.id', $explodeVal)
                                        ->first();
                                if ($addonV) {
                                    $sub_total = $addonV->addon_price * $cartData['quantity'];
                                    $subtotal = $subtotal + $sub_total;
                                }
                            }
                            $addons = $cartData['addons'];
                        }
                    }

                    $sumTotal = $sumTotal + $subtotal;

                    if (isset($cartData['comment']) && !empty($cartData['comment'])) {
                        $comment = $cartData['comment'];
                    } else {
                        $comment = "";
                    }

                    $data = array(
                        'menu_id' => $cartData['item_id'],
                        'base_price' => $cartData['price'],
                        'quantity' => $cartData['quantity'],
                        'comment' => $comment,
                        'submenus' => $cartData['submenus'],
                        'order_id' => $order_id,
                        'sub_total' => $subtotal,
                        'user_id' => $user_id,
                        'addon_id' => $addons,
                        'variant_id' => $variant_type,
                        'created' => date('Y-m-d H:i:s'),
                        'slug' => $this->createSlug('cart')
                    );
                    DB::table('order_item')->insert(
                            $data
                    );
                }
            }

            /*             * * save orders ** */
            $menu_items_id = implode(',', $menu_items);

            DB::table('orders')
                    ->where('id', $order_id)
                    ->update(array('order_item_id' => $menu_items_id));




            $userData = DB::table('users')
                    ->where('id', $user_id)
                    ->first();


            $customerData = $userData;
            $customerContent = "";
            $customerContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: center; background-color: rgb(108, 158, 22); padding: 7px;" colspan="4">Customer Details</td>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->first_name . ' ' . $customerData->last_name . '
                                </td>
                            </tr>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Contact Number: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->contact . '
                                </td>
                            </tr>';

            $customerContent .= '</table>';


            $orderContent = "";
            // send mails
            /// send mail to customer 
            $orderContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 16px;padding: 10px;word-wrap: break-word; background-color:rgb(108, 158, 22); font-weight:bold; text-align:center;">
                                    Order Details
                                </td>
                            </tr>';

            $grandTpotal = 0;

            $mailSubjectCustomer = 'Your order placed successfully on ' . SITE_TITLE;
            $mailSubjectRestaurant = 'New order received on ' . SITE_TITLE;
            $mailSubjectAdmin = 'New order received on ' . SITE_TITLE;
//                $tax = 0;
//                    $delivery_charge = 0;
//                    $discount = 0;
            $temContent = $orderContent;



            $totalVendor = 0;

            $catererData = DB::table('users')
                    ->where('id', $caterer_id)
                    ->first();

            $data = array(
                'user_id' => $user_id,
                'caterer_id' => $caterer_id,
                'created' => date('Y-m-d H:i:s'),
            );
            DB::table('user_reviews')->insert($data
            );

            $VendorTemp = "";

            $VendorTemp .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $orderNumber . ' (' . $catererData->first_name . ')
                                </td>
                                
                            </tr>';

            $VendorTemp .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;border-bottom:1px solid #ddd;word-wrap: break-word;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Items
                                </td>
                                 <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Base Price
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Quantity
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Sub Total
                                </td>
                            </tr>';
            //echo $orderContent; exit;

            $content = DB::table('order_item')
                    ->where('user_id', $user_id)
                    ->where('order_id', $order_id)
                    ->get();



            if (!empty($item)) {

                $total = array();
//                $variant_id ='';
                //echo "<pre>"; print_r($cartData); exit;
                foreach ($item as $cartData) {

                    $menuItem = DB::table('menu_item')
                            ->where('id', $cartData['menu_id'])
                            ->first();
                    $VendorTemp .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $menuItem->item_name . '</td>';

                    if ($cartData['variants'] != "") {
                        $variant_id = explode(',', $cartData['variants']);

                        $menuDataVal = DB::table('variants')
                                ->whereIn('id', $variant_id)
                                ->get();
                        //   echo "<pre>"; print_r($menuDataVal); exit;

                        foreach ($menuDataVal as $menuData) {

                            $sub_total = $menuData->price * $cartData['quantity'];

                            $total[] = $sub_total;
                            $VendorTemp .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   <strong>Variant </strong> (' . $menuData->name . ')
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($menuData->price, 2) . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $cartData['quantity'] . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                </td>
                                  </tr>';
                        }
                    }



                    if ($cartData['addons'] != "") {
                        $addon_id = explode(',', $cartData['addons']);
                        $menuDataVal = DB::table('addons')
                                ->whereIn('id', $addon_id)
                                ->get();
                        foreach ($menuDataVal as $menuData) {
                            $sub_total = $menuData->addon_price * $cartData['quantity'];

                            $total[] = $sub_total;
                            //  echo $sub_total;
                            $VendorTemp .= '<tr>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       <strong>Add-on </strong> (' . $menuData->addon_name . ')
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . App::make("HomeController")->numberformat($menuData->addon_price, 2) . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . $cartData['quantity'] . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                    </td>
                                      </tr>';
                        }
                    }


                    $VendorTemp .= '</tr>';
                }
            }

            $totalVendor = array_sum($total);
            $grandTpotal = $grandTpotal + $gTotal = array_sum($total);

            $cardata = $orderContent . $VendorTemp;
            $cardata .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd; font-weight:normal;">
                                   Total
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($totalVendor, 2) . '
                                </td>
                                  </tr>';


            if ($input['discount'])
                $cardata .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                                   Discount
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($discount, 2) . '
                                </td>
                                  </tr>';

            $gTotal = $totalVendor - $discount;
            $totalVendor = $totalVendor - $discount;

            if ($adminuser->is_tax) {
                $cardata .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                                   Tax
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($tax / count($shopData), 2) . '
                                </td>
                                  </tr>';
                $totalVendor = $totalVendor + $tax;
            }

            $cardata .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                                   Delivery Charge (' . $shopData->delivery_type . ')
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($delivery_charge / count($shopData), 2) . '
                                </td>
                                  </tr>';
            $gTotal = $totalVendor + $delivery_charge / count($shopData);

            $totalVendor = $totalVendor + $delivery_charge / count($shopData);

            if ($adminuser->is_commission == 1) {

                $comm_per = $adminuser->commission;
                $tax_amount = $comm_per * $totalVendor / 100;


//                $cardata .= '<tr>
//                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
//                                   Admin Commission
//                                </td>
//                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
//                                   ' . App::make("HomeController")->numberformat($tax_amount, 2) . '
//                                </td>
//                                  </tr>';
//                $totalVendor = $totalVendor - $tax_amount;
            }
            // $totalVendor = $totalVendor + $delivery_charge/count($shopData);



            $cardata .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                                  Grand Total
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                                   ' . App::make("HomeController")->numberformat($totalVendor, 2) . '
                                </td>
                                  </tr>';

            $cardata .= '</table>';
//               echo $catererData->email_address;
            // echo $cardata; //exit;

            /**             * send mail to caterer ** */
            $caterer_mail_data = array(
                'text' => 'Order placed successfully on ' . SITE_TITLE,
                'orderContent' => $cardata,
                'mailSubjectRestaurant' => $mailSubjectRestaurant,
                'sender_email' => $catererData->email_address,
                'firstname' => $catererData->first_name . ' ' . $catererData->last_name,
            );

            // return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

            Mail::send('emails.template', $caterer_mail_data, function($message) use ($caterer_mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($caterer_mail_data['sender_email'], $caterer_mail_data['firstname'])->subject($caterer_mail_data['mailSubjectRestaurant']);
            });
            /*             *  * * ** *  */
            $temContent .= $VendorTemp;




            $orderContent = $temContent;

            $orderContent .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd; font-weight:normal;">
                                   Total
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($grandTpotal, 2) . '
                                </td>
                                  </tr>';

            if ($shopData->discount)
                $orderContent .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                                   Discount
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                                   -' . App::make("HomeController")->numberformat($discount, 2) . '
                                </td>
                                  </tr>';

            if ($adminuser->is_tax) {
                $orderContent .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                                   Tax
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($tax, 2) . '
                                </td>
                                  </tr>';
                //  $gTotal = $gTotal + $tax;
            }

            $orderContent .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                                   Delivery Charge (' . $shopData->delivery_type . ')
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($delivery_charge, 2) . '
                                </td>
                                  </tr>';

            $gTotal = $grandTpotal + $delivery_charge + $tax - $discount;
            $orderContent .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                                  Grand Total
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                                   ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                                </td>
                                  </tr>';

            $orderContent .= '</table>';
            // echo $orderContent; exit;



            /*             * * send mail to customer ** */
            $mail_data = array(
                'text' => 'Order placed successfully on ' . SITE_TITLE . ". Your order is being reviewed, we will send you confirmation shortly.",
                'orderContent' => $orderContent,
                'mailSubjectCustomer' => $mailSubjectCustomer,
                'sender_email' => $userData->email_address,
                'firstname' => $userData->first_name . ' ' . $userData->last_name,
            );
//
//                 return View::make('emails.template')->with($mail_data); // to check mail template data to view
//
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject($mail_data['mailSubjectCustomer']);
            });




            $push_message = array(
                "type" => 'order_placed',
                "message" => 'Your have received a new order.',
                'customer_id' => $user_id,
                'restaurant_id' => $catererData->id
            );

            if ($catererData->device_type == 'Android') {
                $this->send_fcm_notify($catererData->device_id, $push_message, 'Restaurant');
            } else if ($catererData->device_type == 'iPhone') {
                $this->send_iphone_notification($catererData->device_id, $push_message, 'Restaurant');
            }



            /*             * * send mail to admin ** */

            $admin_mail_data = array(
                'text' => 'Order placed successfully on ' . SITE_TITLE,
                'customerContent' => $customerContent,
                'orderContent' => $orderContent,
                'mailSubjectAdmin' => $mailSubjectAdmin,
                'sender_email' => $adminuser->email,
                'firstname' => "Admin",
            );

            //   return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

            Mail::send('emails.template', $admin_mail_data, function($message) use ($admin_mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($admin_mail_data['sender_email'], 'Admin')->subject($admin_mail_data['mailSubjectAdmin']);
            });

//            die;
            
            echo $this->successOutput('Order sucessfully added');
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    public function paymentRequest() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        $caterer_id = trim($dataJson['caterer_id']);
        $amount = trim($dataJson['amount']);
        $catererData = DB::table('users')
                ->where('id', $caterer_id)
                ->first();

        if (empty($catererData) || $amount < 0) {
            $msgg = "Invalid Request";
            echo $this->outputwithError($msgg);
            exit;
        }

        if (empty($catererData->paypal_email_address)) {
            $msgg = "Payment is not configured for this restaurant yet.";
            echo $this->outputwithError($msgg);
            exit;
        }


        include("vendor/paypalplatform.php");
        $receiverEmailArray = array();
        $receiverAmountArray = array();
        $actionType = "PAY";
        $cancelUrl = HTTP_PATH . '/api/cancelPayment';
        $returnUrl = HTTP_PATH . '/api/successPayment';
        $currencyCode = "USD";

        array_push($receiverEmailArray, $catererData->paypal_email_address);
        array_push($receiverAmountArray, $amount);
        $receiverPrimaryArray = array(
            'false'
        );

//        print_r($receiverEmailArray); exit;
        // A chained payment can be made with 1 primary receiver and between 1 and 5 secondary
        // receivers
        // TODO - Specify the receiver emails
        //        Remove or set to an empty string the array entries for receivers that you
        //        do not have
        //echo   $this->Salar_Sum_Price ;
//            $receiverEmailArray = array(
//                ADMIN_PAYPAL,
//                $driver_paypal,
//                $refrece_paypal,
//                    //$taskerDetail['User']['email_address']
//            );
        // TODO - Specify the receiver amounts as the amount of money, for example, '5' or '5.55'
        //        Remove or set to an empty string the array entries for receivers that you
        //        do not have
//            $receiverAmountArray = array(    
//                $admin_charge,
//                $driver_charge,
//                $refrece_charge,
//            );
        // TODO - Set ONLY 1 receiver in the array to 'true' as the primary receiver, and set the
        //        other receivers corresponding to those indicated in receiverEmailArray to
        //        'false'. Make sure that you do NOT specify more values in this array than
        //        in the receiverEmailArray.
        // TODO - Set invoiceId to uniquely identify the transaction associated with each receiver
        //        Set the array entries with value for receivers that you have each of the array
        //        Values must be unique across all Pay calls made by the caller's API credentials
        $receiverInvoiceIdArray = array(
            ''
        );

        // Request specific optional fields
        //   Provide a value for each field that you want to include in the request;
        //   if left as an empty string, the field will not be passed in the request
        $senderEmail = ""; // TODO - If you are executing the Pay call against a preapprovalKey,
        // you should set senderEmail
        // It is not required if the web approval flow immediately
        // follows this Pay call
        $feesPayer = "";
        $ipnNotificationUrl = "";
        $memo = ""; // maxlength is 1000 characters
        $pin = ""; // TODO - If you are executing the Pay call against an existing preapproval
        // that requires a pin, then you must set this
        $preapprovalKey = ""; // TODO - If you are executing the Pay call against an existing
        // preapproval, set the preapprovalKey here
        $reverseAllParallelPaymentsOnError = ""; // TODO - Do not specify for chained payment
        $trackingId = generateTrackingID(); // generateTrackingID function is found
        // in paypalplatform.php
//            echo '<pre>';
//            print_r($receiverEmailArray);
//            print_r($receiverAmountArray);
//            exit;
        $resArray = CallPay($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray, $receiverAmountArray, $receiverPrimaryArray, $receiverInvoiceIdArray, $feesPayer, $ipnNotificationUrl, $memo, $pin, $preapprovalKey, $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId
        );
//echo '<pre>';print_r($resArray);
//exit;
        $ack = strtoupper($resArray["responseEnvelope.ack"]);
        if ($ack == "SUCCESS") {
            if ("" == $preapprovalKey) {
                // redirect for web approval flow
                //$cmd = "cmd=_ap-payment&paykey=" . urldecode($resArray["payKey"]);
                $payKey = urldecode($resArray["payKey"]);
                //$cmd = "cmd=_ap-preapproval&preapprovalkey=" . urldecode($resArray["preapprovalKey"]);
                //RedirectToPayPal($cmd);
            } else {
                // The Pay API call was made for an existing preapproval agreement, so no approval 
                // flow follows.
                // payKey is the key that you can use to identify the result from this Pay call.
                $payKey = urldecode($resArray["payKey"]);
                // paymentExecStatus is the status of the payment
                //$paymentExecStatus = urldecode($resArray["paymentExecStatus"]);
                // Note that in order to get the exact status of the transactions resulting from
                // a Pay API call, you should make the PaymentDetails API call for the payKey
            }

            $data['response_data'] = array('paykey' => $payKey);
            $data['response_status'] = 'success';
            $data['response_msg'] = 'Proceed to payment.';
            echo json_encode($data);
            exit;
        } else {

//            echo "<pre>";
//            print_r($resArray);
//            exit;
            //Display a user-friendly Error on the page using any of the following error information
            //returned by PayPal.
            //TODO - There can be more than 1 error, so check for "error(1).errorId",
            //       then "error(2).errorId", and so on until you find no more errors.
            $ErrorCode = urldecode($resArray["error(0).errorId"]);
            $ErrorMsg = urldecode($resArray["error(0).message"]);
            $ErrorDomain = urldecode($resArray["error(0).domain"]);
            $ErrorSeverity = urldecode($resArray["error(0).severity"]);
            $ErrorCategory = urldecode($resArray["error(0).category"]);

            echo $this->outputwithError($ErrorMsg);
            exit;
        }
    }

    public function successPayment() {
        exit;
    }

    public function cancelPayment() {
        exit;
    }

//    resturent app
    //Show order history
    public function showOrderHistorylist() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
//            print_r($dataJson); exit;
            $user_id = trim($dataJson['user_id']);

            if (empty($user_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $query = DB::table('orders');
            $query->leftjoin("payments", 'payments.order_id', '=', 'orders.id');
            $query->leftjoin("users", 'users.id', '=', 'orders.caterer_id');
            $query->where('orders.user_id', "=", $user_id)
                    ->select('orders.order_number', 'orders.status as order_status', 'orders.id', 'users.first_name', 'users.last_name', 'payments.price', 'orders.created', 'users.id as restro_id')
                    ->orderBy('orders.id', 'desc');
            $result = $query->get();

//            echo '<prE>'; print_r($result); exit;

            $restro = array();

            $i = 0;

            if ($result) {
                foreach ($result as $results) {
                    $restro[$i] = (array) $results;
                    if ($results->id) {
                        $query = DB::table('reviews');
                        $query->where('reviews.item', "=", $results->id);
                        $query->where('reviews.caterer_id', "=", $results->restro_id);
                        $resul = $query->first();
                        $restro[$i]['is_rated'] = $resul ? '1' : '0';
                        $i++;
                    }
                }
            }



//            echo '<prE>'; print_r(count($response_data)); exit;

            if ($result) {

                echo $this->output(str_replace(":null", ':""', json_encode($restro)));
                exit;
            } else {
                echo $this->errorOutput('No order found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //show order details
    public function showOrdeDetails() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
//            print_r($dataJson); exit;
            $order_id = trim($dataJson['order_id']);

            if (empty($order_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $query = DB::table('orders');
            $query->where('orders.id', "=", $order_id)
                    ->select('orders.*');
            $result = $query->first();

//            echo '<pre>';
//            print_r($result);
//            die;

            $response_data = array();
            $kitchenStaff = "";
            $deliveryPerson = "";

            if ($result) {

                $customerData = DB::table('users')
                        ->select("users.first_name", "users.last_name", "users.contact", "users.address", "areas.name as area_name", "cities.name as city_name")
                        ->leftjoin('areas', 'areas.id', '=', 'users.area')
                        ->leftjoin('cities', 'cities.id', '=', 'users.city')
                        ->where('users.id', $result->user_id)
                        ->first();

                if ($customerData) {
                    $response_data['customer_details'] = $customerData;
                } else {
                    $response_data['customer_details'] = array();
                }

                $RestaurantData = DB::table('users')
                        ->select("users.first_name", "users.last_name", "users.contact", "users.address", "areas.name as area_name", "cities.name as city_name")
                        ->leftjoin('areas', 'areas.id', '=', 'users.area')
                        ->leftjoin('cities', 'cities.id', '=', 'users.city')
                        ->where('users.id', $result->caterer_id)
                        ->first();

                if ($RestaurantData) {
                    $response_data['restaurant _details'] = $RestaurantData;
                } else {
                    $response_data['restaurant _details'] = array();
                }

                if (!empty($result->kitchen_staff_id)) {
                    $kitcData = DB::table('users')
                            ->select("users.first_name", "users.last_name")
                            ->where('users.id', $result->kitchen_staff_id)
                            ->first();
                    if (!empty($kitcData)) {
                        $kitchenStaff = $kitcData->first_name . " " . $kitcData->last_name;
                    }
                }

                if (!empty($result->delivery_person_id)) {
                    $deliData = DB::table('users')
                            ->select("users.first_name", "users.last_name")
                            ->where('users.id', $result->delivery_person_id)
                            ->first();
                    if (!empty($deliData)) {
                        $deliveryPerson = $deliData->first_name . " " . $deliData->last_name;
                    }
                }



                $deliveryData = DB::table('addresses')
                        ->select("addresses.address_title", "addresses.address_type", "areas.name as area_name", "cities.name as city_name", "addresses.street_name", "addresses.building", "addresses.floor", "addresses.apartment", "addresses.phone_number")
                        ->leftjoin('areas', 'areas.id', '=', 'addresses.area')
                        ->leftjoin('cities', 'cities.id', '=', 'addresses.city')
                        ->where('addresses.id', $result->address_id)
                        ->first();

                if ($deliveryData) {
                    $response_data['delivery _details'] = $deliveryData;
                } else {
                    $response_data['delivery _details'] = array();
                }

                $adminData = DB::table('admins')
                        ->where('id', '1')
                        ->first();

                $response_data['order_details'] = array(
                    'order_number' => $result->order_number,
                    'order_status' => $result->status,
                    'discount' => $result->discount,
                    'tax' => $result->tax,
                    'sub_total' => $result->item_total,
                    'total' => $result->total,
                    'delivery_charge' => $result->delivery_charge,
                    'delivery_type' => $result->delivery_type,
                    'is_commission' => $adminData->is_commission,
                    'commission' => $adminData->commission,
                    'created' => $result->created,
                    'kitchen_staff' => $kitchenStaff,
                    'delivery_person' => $deliveryPerson,
                );

                $cartItems = DB::table('order_item')
                        ->whereIn('menu_id', explode(',', $result->order_item_id))
                        ->where('order_id', $result->id)
                        ->get();

                $i = 0;
                foreach ($cartItems as $cartData) {
                    $addonprice = 0;
                    $menuData = DB::table('menu_item')
                                    ->where('id', $cartData->menu_id)->first();

                    $response_data['order_menus'][$i]['item_name'] = $menuData->item_name;
                    $response_data['order_menus'][$i]['comment'] = $cartData->comment;
                    $response_data['order_menus'][$i]['quantity'] = $cartData->quantity;

                    if (isset($cartData->variant_id)) {
                        $explode = explode(',', $cartData->variant_id);
                        if ($explode) {
                            foreach ($explode as $explodeVal) {

                                $addonV = DB::table('variants')
                                        ->where('variants.id', $explodeVal)
                                        ->first();

                                if ($addonV) {
                                    $addonprice = $addonprice + $addonV->price;
                                    $addonTotal[] = $addonprice;
                                    $response_data['order_menus'][$i]['variants'][] = $addonV;
                                } else {

                                    $response_data['order_menus'][$i]['variants'] = array();
                                }
                            }
                        }
                    } else {
                        $response_data['order_menus'][$i]['variants'] = array();
                    }
//
                    if (isset($cartData->addon_id)) {
                        $explode = explode(',', $cartData->addon_id);
                        if ($explode) {
                            foreach ($explode as $explodeVal) {
                                $addonV = DB::table('addons')
                                        ->where('addons.id', $explodeVal)
                                        ->first();
//                                echo '<prE>'; print_r($addonV);
                                if ($addonV) {
                                    $addonprice = $addonprice + $addonV->addon_price;
                                    $addonTotal[] = $addonprice;
                                    $response_data['order_menus'][$i]['addons'][] = $addonV;
                                } else {
                                    $response_data['order_menus'][$i]['addons'] = array();
                                }
                            }
                        }
                    } else {
                        $response_data['order_menus'][$i]['addons'] = array();
                    }

                    $addonprice = $addonprice * $cartData->quantity;
                    $total[] = $addonprice;

                    $i++;
                }
                $sub_Total = '';
                $gTotal = array_sum($total);
                $sub_Total = array_sum($total);

                if (!empty($result->tax)) {
                    $tax = $result->tax;
                    $gTotal = $gTotal + $tax;
                }

                $gTotal = $gTotal + $result->delivery_charge;

//                $response_data['total'] = $result->total;
//                $response_data['sub_total'] = $result->item_total;
//                
                $response_data['total'] = $gTotal;
                $response_data['sub_total'] = $sub_Total;

                echo $this->output(str_replace(":null", ':""', json_encode($response_data)));
                exit;
            } else {
                echo $this->errorOutput('No record found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //show coupon list
    public function showCouponList() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        if (!empty($dataJson)) {
            $rest_id = trim($dataJson['rest_id']);
            $query = DB::table('coupons');
            $query->where('coupons.status', "=", '1')
                    ->where('coupons.user_id', "=", $rest_id)
                    ->select('coupons.*');
            $result = $query->get();
            if ($result) {
                echo $this->output(json_encode($result));
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    // add coupon 
    public function addCouponRest() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        if (!empty($dataJson)) {
            $rest_id = trim($dataJson['rest_id']);
            $input = $dataJson;

            if (empty($input['code']) || empty($input['discount']) || empty($input['start_time']) || empty($input['end_time'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $data = array(
                'user_id' => $rest_id,
                'code' => $input['code'],
                'discount' => $input['discount'],
                'start_time' => date('Y-m-d', strtotime($input['start_time'])),
                'end_time' => date('Y-m-d', strtotime($input['end_time'])),
                'created' => date('Y-m-d H:i:s'),
                'status' => '1',
                'slug' => $this->createSlug($input['code'], 'coupons')
            );

            $id = DB::table('coupons')->insertGetId(
                    $data
            );

            if ($id) {
                echo $this->successOutput('Coupon successfully added');
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //show Restaurants order list
    public function showResOrderHistorylist() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            $rest_id = trim($dataJson['rest_id']);

            if (empty($rest_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $query = DB::table('orders');
            $query->leftjoin("payments", 'payments.order_id', '=', 'orders.id');
            $query->where('orders.caterer_id', "=", $rest_id)
                    ->where('orders.status', "!=", "Pending")
                    ->select('orders.order_number', 'orders.status as order_status', 'orders.id', 'orders.created', 'payments.transaction_id', 'payments.price')
                    ->groupBy('orders.id')
                    ->orderBy('orders.id', 'desc');
            $result = $query->get();


            if ($result) {
                echo $this->output(json_encode($result));

                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //Show Restaurants details
    public function showrestdetails() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            $rest_id = trim($dataJson['rest_id']);

            if (empty($rest_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $userData = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if ($userData) {
                echo $this->output(json_encode($userData));
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //add menu item
    public function showaddMenuitem() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        // echo '<pre>';print_r($dataJson);die;
        if (!empty($dataJson)) {
            $cusine_id = trim($dataJson['cuisines_id']);
            $user_id = trim($dataJson['user_id']);
            $menu_id = trim($dataJson['menu_id']);
            $input = $dataJson;

            if (empty($input['cuisines_id']) || empty($input['item_name']) || empty($input['description']) || empty($input['price'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            if ($input['menu_id']) {

                $menuData = DB::table('menu_item')
                        ->where('menu_item.id', $menu_id)
                        ->first();

                if ($menuData) {

                    $data = array(
                        'cuisines_id' => $input['cuisines_id'],
                        'item_name' => $input['item_name'],
                        'description' => strip_tags($input['description']),
                        'price' => $input['price'],
                        'user_id' => $user_id,
                        'created' => date('Y-m-d H:i:s'),
                        'status' => '1',
                        'slug' => $this->createSlug($input['item_name'])
                    );

                    if (isset($input['non_veg']) && $input['non_veg']) {
                        $data['non_veg'] = 1;
                    } else {
                        $data['non_veg'] = 0;
                    }
                    if (isset($input['spicy']) && $input['spicy']) {
                        $data['spicy'] = 1;
                    } else {
                        $data['spicy'] = 0;
                    }
                    if (isset($input['deal']) && $input['deal']) {
                        $data['deal'] = 1;
                    } else {
                        $data['deal'] = 0;
                    }

                    //   print_r($_FILES);die;
                    if (Input::hasFile('image')) {
                        $file = Input::file('image');
                        $image = time() . $file->getClientOriginalName();
                        $file->move(UPLOAD_FULL_ITEM_IMAGE_PATH, time() . $file->getClientOriginalName());
                    } else {
                        $image = "";
                    }
                    $data['image'] = $image ? $image : ($menuData->image ? $menuData->image : '');

                    DB::table('menu_item')
                            ->where('id', $menuData->id)
                            ->update($data);

                    echo $this->successOutput('Menu item successfully updated');
                    exit;
                } else {
                    echo $this->errorOutput('No data found');
                    exit;
                }
            } else {

                $data = array(
                    'cuisines_id' => $input['cuisines_id'],
                    'item_name' => $input['item_name'],
                    'description' => strip_tags($input['description']),
                    'price' => $input['price'],
                    'user_id' => $user_id,
                    'created' => date('Y-m-d H:i:s'),
                    'status' => '1',
                    'slug' => $this->createSlug($input['item_name'])
                );

                if (isset($input['non_veg']) && $input['non_veg']) {
                    $data['non_veg'] = 1;
                } else {
                    $data['non_veg'] = 0;
                }
                if (isset($input['spicy']) && $input['spicy']) {
                    $data['spicy'] = 1;
                } else {
                    $data['spicy'] = 0;
                }
                if (isset($input['deal']) && $input['deal']) {
                    $data['deal'] = 1;
                } else {
                    $data['deal'] = 0;
                }

                //   print_r($_FILES);die;
                if (Input::hasFile('image')) {
                    $file = Input::file('image');
                    $image = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_FULL_ITEM_IMAGE_PATH, time() . $file->getClientOriginalName());
                } else {
                    $image = "";
                }
                $data['image'] = $image;

                DB::table('menu_item')->insert(
                        $data
                );

                $id = DB::getPdo()->lastInsertId();

//                echo '<pre>'; print_r($id);

                /* Parent Variant */
                $parentvariantdata = array(
                    'user_id' => $user_id,
                    'menu_id' => $id,
                    'name' => $input['item_name'],
                    'price' => $input['price'],
                    'slug' => 'variant-' . time() . rand(10, 99),
                    'status' => 0,
                    'parent' => 1,
                    'created' => date('Y-m-d H:i:s')
                );

                DB::table('variants')
                        ->insert($parentvariantdata);
                /* Parent Variant */

                if ($id) {
                    echo $this->successOutput('Menu item successfully added');
                    exit;
                } else {
                    echo $this->errorOutput('No data found');
                    exit;
                }
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //add variant
    public function showAddvariant() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        if (!empty($dataJson)) {
            $menu_id = trim($dataJson['menu_id']);
            $user_id = trim($dataJson['user_id']);
            $input = $dataJson;

            if (empty($input['menu_id']) || empty($input['user_id']) || empty($input['variant_name']) || empty($input['variant_price'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $user_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }



            $menuData = DB::table('variants')
                    ->where('menu_id', $menu_id)
                    ->first();

            $data = array(
                'menu_id' => $menu_id,
                'user_id' => $user_id,
                'name' => $input['variant_name'],
                'price' => $input['variant_price'],
                'created' => date('Y-m-d H:i:s'),
                'parent' => $menuData ? '0' : '1',
                'slug' => 'variant-' . time() . rand(10, 99),
            );

            $id = DB::table('variants')->insertGetId(
                    $data
            );

            if ($id) {
                echo $this->successOutput('Varient successfully added');
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //add addon
    public function showAddaddons() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        if (!empty($dataJson)) {
            $menu_id = trim($dataJson['menu_id']);
            $user_id = trim($dataJson['user_id']);
            $input = $dataJson;

            if (empty($input['menu_id']) || empty($input['user_id']) || empty($input['addon_name']) || empty($input['addon_price'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $user_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            $menuData = DB::table('addons')
                    ->where('menu_id', $menu_id)
                    ->first();

            $data = array(
                'menu_id' => $menu_id,
                'user_id' => $user_id,
                'addon_name' => $input['addon_name'],
                'addon_price' => $input['addon_price'],
                'created' => date('Y-m-d H:i:s'),
                'slug' => 'addon-' . time() . rand(10, 99),
            );

            $id = DB::table('addons')->insertGetId(
                    $data
            );

            if ($id) {
                echo $this->successOutput('Addon successfully added');
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //show payment history
    public function showPaymentHistory() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
//        echo '<prE>'; print_r($dataJson);die;
        if (!empty($dataJson)) {
            $user_id = trim($dataJson['user_id']);


            if (empty($user_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $user_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }


            $query = DB::table('payments');
            $query->leftjoin("users", 'users.id', '=', 'payments.user_id');
            $query->leftjoin("sponsorship", 'sponsorship.id', '=', 'payments.package');
            $query->where('payments.user_id', "=", $user_id);


            $query->select('payments.order_id', 'payments.type', 'payments.transaction_id', 'sponsorship.name as plan_name', 'payments.status', 'payments.created', 'payments.price', 'users.expiry_date');
            $result = $query->get();
//            echo '<prE>';
//            print_r($result);
//            die;
            if ($result) {
                echo $this->output(str_replace(":null", ':""', json_encode($result)));
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //add additional status
    public function showAddAdditionalStatus() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        if (!empty($dataJson)) {
            $rest_id = trim($dataJson['rest_id']);
            $input = $dataJson;

            if (empty($rest_id) || empty($input['status_name'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            $data = array(
                'status_name' => $input['status_name'],
                'user_id' => $rest_id,
                'created' => date('Y-m-d H:i:s'),
                'status' => '1',
                'slug' => $this->createSlug($input['status_name'])
            );

            $id = DB::table('orderstatus')->insertGetId(
                    $data
            );

            if ($id) {
                echo $this->successOutput('Additional Order status added');
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //show additional status list
    public function showAdditionalStatusList() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
//            print_r($dataJson); exit;
            $rest_id = trim($dataJson['rest_id']);

            if (empty($rest_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            $query = DB::table('orderstatus');
            $query->where('orderstatus.user_id', "=", $rest_id)
                    ->select('orderstatus.id', 'orderstatus.status_name', 'orderstatus.created');
            $result = $query->get();

//            echo '<prE>'; print_r($response_data); exit;

            if ($result) {

                echo $this->output(str_replace(":null", ':""', json_encode($result)));
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //manage delivery
    public function showManageDelivery() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            $rest_id = trim($dataJson['rest_id']);
            $input = $dataJson;

            if (empty($rest_id) || empty($input['vespa_price']) || empty($input['car_price']) || empty($input['delivery_charge_limit'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }



            $users = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            $charges = DB::table('pickup_charges')
                    ->where('user_id', $rest_id)
                    ->first();
            //        
            if (empty($charges)) {
                $data = array(
                    'user_id' => $rest_id,
                    'is_default_delivery' => '0',
                    'pick_up' => '0',
                    'normal' => '',
                    'advance' => "",
                    'delivery_charge_limit' => "",
                    'created' => date('Y-m-d H:i:s')
                );
                $id = DB::table('pickup_charges')
                        ->insertGetId($data);

                if ($id) {

                    $charges = DB::table('pickup_charges')
                            ->where('id', $id)
                            ->first();

                    echo $this->outputresult(str_replace(":null", ':""', json_encode($charges)), 'Delivery charges changed');
//                  echo $this->successOutput('Delivery charges changed');
                    exit;
//                    echo $this->successOutput('Delivery charges changed');
//                    exit;
                } else {
                    echo $this->errorOutput('No data found');
                    exit;
                }
            } else {
                $data = array(
                    'normal' => $input['vespa_price'],
                    'advance' => $input['car_price'],
                    'delivery_charge_limit' => $input['delivery_charge_limit'],
                    'is_default_delivery' => $input['is_default_delivery'],
                    'pick_up' => $input['pick_up'],
                );

                DB::table('pickup_charges')
                        ->where('id', $charges->id)
                        ->update($data);
            }

            $charges = DB::table('pickup_charges')
                    ->where('user_id', $rest_id)
                    ->first();

            echo $this->outputresult(str_replace(":null", ':""', json_encode($charges)), 'Delivery charges changed');
//            echo $this->successOutput('Delivery charges changed');
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //manage opening hours 
    public function showManageOpeninghours() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        if (!empty($dataJson)) {
            $rest_id = trim($dataJson['rest_id']);
            $input = $dataJson;

            if (empty($rest_id) || empty($input['open_days']) || empty($input['start_time']) || empty($input['end_time']) || empty($input['minimum_order']) || empty($input['average_time']) || empty($input['estimated_cost']) || empty($input['catering_type'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }


            $users = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }


            // get opening hours details
            $opening_hours = DB::table('opening_hours')
                    ->where('user_id', $rest_id)
                    ->first();
//
            $days = array();
            $days = explode(',', trim($input['open_days']));
            $daysd = '';

            $newarray = array();
            foreach ($days as $dayss) {
                $days = strtolower(substr($dayss, 0, 3));
                $newarray[] = $days;
            }

            if (empty($opening_hours)) {

                $data = array(
                    'open_days' => implode(",", $newarray),
                    'user_id' => $rest_id,
//                    'open_days' => $input['open_days'],
                    'start_time' => $input['start_time'],
                    'end_time' => $input['end_time'],
                    'minimum_order' => $input['minimum_order'],
                    'average_time' => $input['average_time'],
                    'estimated_cost' => $input['estimated_cost'],
                    'catering_type' => $input['catering_type'],
                    'open_close' => isset($input['open_close']) ? $input['open_close'] : '0',
                    'status' => '1',
                    'created' => date('Y-m-d H:i:s')
                );

                $id = DB::table('opening_hours')
                        ->insertGetId($data);

                if ($id) {

                    // get opening hours details
                    $opening_hours = DB::table('opening_hours')
                            ->where('id', $id)
                            ->first();

                    echo $this->outputresult(str_replace(":null", ':""', json_encode($opening_hours)), 'Opening Hours Added');
                    exit;
                } else {
                    echo $this->errorOutput('No data found');
                    exit;
                }
            } else {
                $data = array(
                    'open_days' => implode(",", $newarray),
//                    'open_days' => $input['open_days'],
                    'start_time' => $input['start_time'],
                    'end_time' => $input['end_time'],
                    'minimum_order' => $input['minimum_order'],
                    'average_time' => $input['average_time'],
                    'estimated_cost' => $input['estimated_cost'],
                    'catering_type' => $input['catering_type'],
                    'open_close' => isset($input['open_close']) ? $input['open_close'] : '0'
                );

                DB::table('opening_hours')
                        ->where('id', $opening_hours->id)
                        ->where('user_id', $opening_hours->user_id)
                        ->update($data);
            }



            // get opening hours details
            $opening_hours = DB::table('opening_hours')
                    ->where('user_id', $rest_id)
                    ->first();

            echo $this->outputresult(str_replace(":null", ':""', json_encode($opening_hours)), 'Opening Hours successfully changed');
//            echo $this->successOutput('Delivery charges changed');
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //delete coupon
    public function showDeleteCoupon() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            //print_r($dataJson); exit;
            $coupon_id = trim($dataJson['coupon_id']);


            if (empty($coupon_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $couponData = DB::table('coupons')
                    ->where('id', $coupon_id)
                    ->first();

            if ($couponData) {

                DB::table('coupons')->where('id', $coupon_id)->delete();
                echo $this->successOutput('Coupon deleted');
                exit;
            } else {
                echo $this->errorOutput('No record found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //active/deactive coupon
    public function showActiveDeactiveCoupon() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            //print_r($dataJson); exit;

            $coupon_id = trim($dataJson['coupon_id']);
            $input = $dataJson;

            if (empty($coupon_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $status = $input['status'] ? 'activated' : 'deactived';

            $data = array(
                'status' => $input['status'],
            );

            DB::table('coupons')
                    ->where('id', $coupon_id)
                    ->update($data);

            echo $this->successOutput('Coupon ' . $status);
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //show review list
    public function showReviewList() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            //print_r($dataJson); exit;
            $rest_id = trim($dataJson['rest_id']);

            if (empty($rest_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }


            $query = DB::table('reviews');
            $query->leftjoin("users", 'users.id', '=', 'reviews.user_id');
            $query->where('reviews.caterer_id', "=", $rest_id)
                    ->select('reviews.*', 'users.first_name', 'users.last_name', 'users.profile_image as user_image');
            $query->orderBy('reviews.id', 'desc');
            $result = $query->get();


            $respone = array();
            if ($result) {
                $respone['review_list'] = $result;
            } else {
                $respone['review_list'] = array();
            }

            // get all avg ratings
            $ratings = DB::table('reviews')
                    ->leftjoin("users", 'users.id', '=', 'reviews.caterer_id')
                    ->select('users.first_name', 'users.last_name', 'users.profile_image', DB::raw("avg(quality) as quality"), DB::raw("avg(packaging) as packaging"), DB::raw("avg(delivery) as delivery"))
                    ->where('reviews.caterer_id', $rest_id)
                    ->where('reviews.status', '1')
                    ->first();



            if ($ratings) {
                $respone['restro_details'] = $ratings;
            } else {
                $respone['restro_details'] = array();
            }


            if ($result) {
                echo $this->output(str_replace(":null", ':""', json_encode($respone)));
                exit;
            } else {
                echo $this->errorOutput('No review for this restaurant');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //show favorite list
    public function showFavoriteList() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            $user_id = trim($dataJson['user_id']);

            if (empty($user_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $user_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }


            $query = DB::table('favorite_menu');
            $query->leftjoin("menu_item", 'menu_item.id', '=', 'favorite_menu.menu_id');
            $query->where('favorite_menu.user_id', $user_id)
                    ->select('menu_item.item_name', 'favorite_menu.created', 'favorite_menu.id', 'favorite_menu.menu_id')
                    ->groupBy('favorite_menu.menu_id');
            $result = $query->get();

            if ($result) {
                echo $this->output(str_replace(":null", ':""', json_encode($result)));
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //delete favorite
    public function showDeleteFavorite() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            $favorite_id = trim($dataJson['favorite_id']);

            if (empty($favorite_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $favoriteData = DB::table('favorite_menu')
                    ->where('id', $favorite_id)
                    ->first();

            if ($favoriteData) {
                DB::table('favorite_menu')->where('id', $favorite_id)->delete();

                echo $this->successOutput('favorite deleted');
                exit;
            } else {
                echo $this->errorOutput('No record found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //add review
    public function showAddReview() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
//        print_r($dataJson);die;
        if (!empty($dataJson)) {
            $user_id = trim($dataJson['user_id']);
            $rest_id = trim($dataJson['rest_id']);
            $order_id = trim($dataJson['order_id']);
            $input = $dataJson;

            if (empty($user_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $user_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }


            if (empty($rest_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $rest = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if (empty($rest)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            if (empty($order_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }


            if (empty($input['quality']) || empty($input['packaging']) || empty($input['delivery'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }


            $result = DB::table('reviews')
                    ->where('reviews.user_id', $user_id)
                    ->where('reviews.caterer_id', $rest_id)
                    ->where('reviews.item', $order_id)
                    ->first();


            if (empty($result)) {
                $data = array(
                    'user_id' => $user_id,
                    'caterer_id' => $rest_id,
                    'comment' => $input['comment'],
                    'quality' => $input['quality'],
                    'packaging' => $input['packaging'],
                    'item' => $input['order_id'],
                    'delivery' => $input['delivery'],
                    'created' => date('Y-m-d H:i:s'),
                    'status' => '1',
                    'slug' => $this->createSlug($input['comment'])
                );

                $id = DB::table('reviews')->insertGetId(
                        $data
                );

                if ($id) {
                    echo $this->successOutput('Review Added Successfully');
                    exit;
                } else {
                    echo $this->errorOutput('Review not added');
                    exit;
                }
            } else {
                echo $this->errorOutput('Review allready given');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //contact us 
    public function showContactus() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        if (!empty($dataJson)) {
            $input = $dataJson;

            $adminuser = DB::table('admins')
                    ->first();



            // send email to administrator
            $mail_data = array(
                'text' => '<b>Dear  Admin, </b><br/><br/>Inquiry received from ' . $input['name'],
                'email_address' => $input['email'],
                'contact_number' => $input['phone'],
                'subject' => $input['subject'],
                'name' => $input['name'],
                'message2' => $input['message'],
                'admin_mail' => $adminuser->email,
            );
            // pri
            //return View::make('emails.template')->with($mail_data); // to check mail template data to view

            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['admin_mail'], "Admin")->subject($mail_data['subject']);
            });


            // send contact reply to user
            $mail_data = array(
                'text' => "<b>Dear  " . $input['name'] . ", </b><br/><br/>Thank you for contacting us<br/><br/>" . "You are very important to us, all information received will always remain confidential. We will contact you as soon as we review your message.",
                'email_add' => $input['email'],
                'name_sub' => $input['name'],
            );
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['email_add'], $mail_data['name_sub'])->subject('Thank you for contacting us');
            });
            echo $this->successOutput('Thank you for contacting us');
            exit;
        } else {

            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    public function showTest1() {
        ?>
        <form method="post" enctype="multipart/form-data" action='http://demo.imagetowebpage.com/food_ordering/api/changePicture?api_key=FOOD2AMhgHbyVwOijJGJguIsrTbyBHUVAN784vnBYBBgUYGB&data={"user_id":"45"}'>
            <input type="file" name='profile_image'/>
            <input type='submit'/>
        </form>
        <?php
    }

    //show addon list 
    public function addonList() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        //  echo '<pre>';        print_r($dataJson);die;
        if (!empty($dataJson)) {
            $user_id = trim($dataJson['user_id']);
            $menu_id = trim($dataJson['menu_id']);

            if (empty($menu_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            if (empty($user_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $user_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }


            $query = DB::table('addons');
            $query->join('menu_item', 'menu_item.id', '=', 'addons.menu_id');
            $query->where('addons.user_id', "=", $user_id)
                    ->where('addons.menu_id', "=", $menu_id)
                    ->select('addons.*', 'menu_item.item_name');

            $result = $query->get();

            if ($result) {
                echo $this->output(json_encode($result));
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //show variants list 
    public function variantsList() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        //  echo '<pre>';        print_r($dataJson);die;
        if (!empty($dataJson)) {
            $user_id = trim($dataJson['user_id']);
            $menu_id = trim($dataJson['menu_id']);


            if (empty($menu_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            if (empty($user_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $user_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            $query = DB::table('variants');
            $query->join('menu_item', 'menu_item.id', '=', 'variants.menu_id');
            $query->where('variants.user_id', "=", $user_id)
                    ->where('variants.menu_id', "=", $menu_id)
                    ->select('variants.*', 'menu_item.item_name');

            $result = $query->get();

            if ($result) {
                echo $this->output(json_encode($result));
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //delete addons
    public function showDeleteAddon() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            //print_r($dataJson); exit;
            $addon_id = trim($dataJson['addon_id']);

            if (empty($addon_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $addonsData = DB::table('addons')
                    ->where('id', $addon_id)
                    ->first();

            if ($addonsData) {
                DB::table('addons')->where('id', $addon_id)->delete();
                echo $this->successOutput('Addon deleted');
                exit;
            } else {
                echo $this->errorOutput('No record found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //delete variant
    public function showDeleteVariant() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            $variant_id = trim($dataJson['variant_id']);

            if (empty($variant_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $variantsData = DB::table('variants')
                    ->where('id', $variant_id)
                    ->first();

            if ($variantsData) {
                DB::table('variants')->where('id', $variant_id)->delete();
                echo $this->successOutput('Variant deleted');
                exit;
            } else {
                echo $this->errorOutput('No record found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //change order status
    public function showChangeOrderStatus() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        //   echo '<pre>'; print_r($dataJson);
        if (!empty($dataJson)) {

            $order_number = trim($dataJson['order_number']);
            $status = trim($dataJson['status']);
            $reason = trim($dataJson['reason']);
            $input = $dataJson;



            if (empty($status)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            if (empty($order_number)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $query = DB::table('orders')->where('orders.order_number', "=", $order_number);
            $orderData = $query->first();
            if (empty($orderData)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $numberofOrder = 1;
            $tax = $orderData->tax;
            $delivery_charge = $orderData->delivery_charge;
            $discount = $orderData->discount;

            $adminuser = DB::table('admins')
                    ->where('id', '1')
                    ->first();

            $customerData = DB::table('users')
                    ->where('id', $orderData->user_id)
                    ->first();

            $catererData = DB::table('users')
                    ->where('id', $orderData->caterer_id)
                    ->first();

            $cartItems = DB::table('order_item')
                    ->whereIn('menu_id', explode(',', $orderData->order_item_id))
                    ->where('order_id', $orderData->id)
                    ->get();  // get cart menu of this order

            $orderstatus = Orderstatus::orderBy('status_name', 'asc')->where('status', "=", "1")->lists('status_name', 'id');




            if (!empty($input)) { //echo "<pre>"; print_r($input); exit;
                $inputStatus = $input['status'];
                switch ($input['status']) {
                    case "Confirm":
                        $orderStatus = "Confirmed";
                        $subjectMessageCustomer = "Your order has been confirmed by restaurant on " . SITE_TITLE;
                        $subjectMessageRestaurant = "You have confirmed order on " . SITE_TITLE;
                        $subjectMessageAdmin = "An order has been confirmed by restaurant on " . SITE_TITLE;
                        $subjectMessageCouieer = "An order has been assigned to you on " . SITE_TITLE;

                        // check courier conditions start
                        $courierData = DB::table('users')
                                        ->where('mark_default', '1')->first();
                        if (!empty($courierData)) {
                            DB::table('orders')
                                    ->where('id', $orderData->id)
                                    ->update(['is_courier' => 1, 'courier_id' => $courierData->id]); // update order status
                        }


                        // check courier conditions end
                        break;
                    case "Modify":
                        $orderStatus = "Modify";
                        $modifyArr = $input['modfiy'];
                        if (!empty($modifyArr)) {
                            foreach ($modifyArr as $mData) {
                                DB::table('order_item')
                                        ->where('id', $mData['id'])
                                        ->update(['modification'
                                            => $mData['comment'], 'is_modify' => '1']);
                            }
                        }
                        $subjectMessageCustomer = "Restaurant requested to modify your order on " . SITE_TITLE;
                        $subjectMessageRestaurant = "You have requested to modify order on " . SITE_TITLE;
                        $subjectMessageAdmin = "Restaurant placed modification request for order on " . SITE_TITLE;

                        break;
                    case "Cancel":
                        $orderStatus = "Cancelled";
                        DB::table('orders')
                                ->where('id', $orderData->id)
                                ->update(['cancel_reason' => $input['reason']]);
                        $subjectMessageCustomer = "Your order has been cancelled by restaurant on " . SITE_TITLE;
                        $subjectMessageRestaurant = "Your have cancelled order on " . SITE_TITLE;
                        $subjectMessageAdmin = "Restaurant cancelled order on " . SITE_TITLE;
                        break;

                    case "Delivered":
                        $orderStatus = "Delivered";
                        $subjectMessageCustomer = "Your order has been delivered by restaurant on " . SITE_TITLE;
                        $subjectMessageRestaurant = "You have mark order status to delivered on " . SITE_TITLE;
                        $subjectMessageAdmin = "Restaurant mark order as delivered on " . SITE_TITLE;

                        $data = array(
                            'user_id' => $customerData->id,
                            'caterer_id' => $orderData->caterer_id,
                            'created' => date('Y-m-d H:i:s'),
                        );
                        DB::table('user_reviews')->insert($data
                        );
                        break;
                    default:
                        $orderStatus = $input['status'];
                        DB::table('orders')
                                ->where('id', $orderData->id)
                                ->update(['comment' => $input['comment']]);
                        $subjectMessageCustomer = "Your order status has been changed to " . $input['status'] . " by restaurant on " . SITE_TITLE;
                        $subjectMessageRestaurant = "Your have changed the order status to " . $input['status'] . " on " . SITE_TITLE;
                        $subjectMessageAdmin = "Restaurant changed order status to " . $input['status'] . " on " . SITE_TITLE;

                        break;
                }


                if (isset($orderData->delivery_charge) && $orderData->delivery_charge != '') {

                    // $delivery_charge = $orderData->delivery_charge;
                    $delivery_type = $orderData->delivery_type;
                } else {
                    // $delivery_charge = "0";
                    $delivery_type = "N/A";
                }
                $orderNumber = $orderData->order_number;
                $customerContent = "";
                $customerContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
                $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: center; background-color: rgb(108, 158, 22); padding: 7px;" colspan="4">Customer Details</td>';
                $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->first_name . ' ' . $customerData->last_name . '
                                </td>
                            </tr>';

                $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Contact Number: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->contact . '
                                </td>
                            </tr>';

                $customerContent .= '</table>';


                $orderContent = "";
                // send mails
                /// send mail to customer 
                $orderContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
                $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 16px;padding: 10px;word-wrap: break-word; background-color:rgb(108, 158, 22); font-weight:bold; text-align:center;">
                                    Order Details
                                </td>
                            </tr>';
                if ($input['status'] != "Confirm" && $input['status'] != "Cancel" && $input['status'] != "Delivered") {
                    if (isset($input['comment']) && $input['comment'] != "") {

                        $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                     Order note: ' . $input['comment'] . '
                                </td>
                                
                            </tr>';
                    }
                }

                $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $orderNumber . '
                                </td>
                                
                            </tr>';

                $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;border-bottom:1px solid #ddd;word-wrap: break-word;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Items
                                </td>
                                 <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Base Price
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Quantity
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Sub Total
                                </td>
                            </tr>';


                if (!empty($cartItems)) {

                    $total = array();
                    foreach ($cartItems as $cartData) {
                        //echo "<pre>"; print_r($cartData); exit;
                        $menuItem = DB::table('menu_item')
                                ->where('id', $cartData->menu_id)
                                ->first();
                        $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $menuItem->item_name . '</td>';


                        $variant_id = explode(',', $cartData->variant_id);
                        $menuDataVal = DB::table('variants')
                                ->whereIn('id', $variant_id)
                                ->get();
                        //   echo "<pre>"; print_r($menuDataVal); exit;

                        foreach ($menuDataVal as $menuData) {

                            $sub_total = $menuData->price * $cartData->quantity;

                            $total[] = $sub_total;
                            $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   <strong>Variant </strong> (' . $menuData->name . ')
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($menuData->price, 2) . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $cartData->quantity . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                </td>
                                  </tr>';
                        }


                        if ($cartData->addon_id != "") {
                            $addon_id = explode(',', $cartData->addon_id);
                            $menuDataVal = DB::table('addons')
                                    ->whereIn('id', $addon_id)
                                    ->get();
                            foreach ($menuDataVal as $menuData) {
                                $sub_total = $menuData->addon_price * $cartData->quantity;

                                $total[] = $sub_total;
                                //  echo $sub_total;
                                $orderContent .= '<tr>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       <strong>Add-on </strong> (' . $menuData->addon_name . ')
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . App::make("HomeController")->numberformat($menuData->addon_price, 2) . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . $cartData->quantity . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                    </td>
                                      </tr>';
                            }
                        }


                        $orderContent .= '</tr>';
                    }
                }

                $catererData = DB::table('users')
                        ->where('id', $orderData->caterer_id)
                        ->first();
                $gTotal = array_sum($total);
//            if ($orderData->tax) {
//                $tax = $orderData->tax;
//            } else {
//                $tax = 0;
//            }

                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd; font-weight:normal;">
                    Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                    ' . ' ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';



                if ($orderData->discount) {
                    $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Discount
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    - ' . App::make("HomeController")->numberformat($orderData->discount, 2) . '
                    </td>
                    </tr>';
                    $gTotal = $gTotal - $orderData->discount;
                }
                if ($adminuser->is_tax) {
                    $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Tax
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($tax / $numberofOrder, 2) . '
                    </td>
                    </tr>';
                    $gTotal = $gTotal + $tax / $numberofOrder;
                }

                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($delivery_charge / $numberofOrder, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $delivery_charge / $numberofOrder;

                $newcnn = '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

                $customerCC = $orderContent . $newcnn . '</table>';
                //  echo $customerCC; //exit;
                /*                 * * send mail to customer ** */
                $mail_data = array(
                    'text' => $subjectMessageCustomer,
                    'orderContent' => $customerCC,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $customerData->email_address,
                    'firstname' => $customerData->first_name . ' ' . $customerData->last_name,
                );


                if ($adminuser->is_commission == 1) {

                    $comm_per = $adminuser->commission;
                    $tax_amount = $comm_per * $gTotal / 100;


//                $orderContent .= '<tr>
//                            <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
//                               Admin Commission
//                            </td>
//                            <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
//                               ' . App::make("HomeController")->numberformat($tax_amount, 2) . '
//                            </td>
//                              </tr>';
//                $gTotal = $gTotal - $tax_amount;
                }

                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

                $orderContent .= '</table>';
                // echo $orderContent; exit;
// send mail to couier 
                if ($input['status'] == 'Confirm' && !empty($courierData)) {

                    $saveData = array(
                        'order_id' => $orderData->id,
                        'user_id' => $courierData->id,
                        'slug' => $this->createSlug('cservice'),
                        'created' => date('Y-m-d H:i:s'),
                    );
                    DB::table('order_courier')->insert(
                            $saveData
                    );


                    /*                     * * send mail to customer ** */
                    $mail_courier_data = array(
                        'text' => $subjectMessageCouieer,
                        'customerContent' => $customerContent,
                        'orderContent' => $orderContent,
                        'orderStatus' => $orderStatus,
                        'sender_email' => $courierData->email_address,
                        'firstname' => $courierData->first_name . ' ' . $courierData->last_name,
                    );

                    //return View::make('emails.template')->with($mail_data); // to check mail template data to view

                    Mail::send('emails.template', $mail_courier_data, function($message) use($mail_courier_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_courier_data['sender_email'], $mail_courier_data['firstname'])->subject($mail_courier_data['text']);
                    });
                }


                //return View::make('emails.template')->with($mail_data); // to check mail template data to view


                if ($inputStatus != "Assign To Delivery") {
                    Mail::send('emails.template', $mail_data, function($message) use($mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject($mail_data['text']);
                    });


                    $push_message = array(
                        "type" => 'order_status_changed',
                        "message" => 'Your Order Status has been changed to ' . $inputStatus . '.',
                        'customer_id' => $customerData->id,
                        'restaurant_id' => $catererData->id,
                        'order_id' => $orderData->id
                    );

                    if ($customerData->device_type == 'Android') {
                        $this->send_fcm_notify($customerData->device_id, $push_message, 'Customer');
                    } else if ($customerData->device_type == 'iPhone') {
                        $this->send_iphone_notification($customerData->device_id, $push_message, 'Customer');
                    }
                }
                /*                 * * send mail to restaurant ** */

                $caterer_mail_data = array(
                    'text' => $subjectMessageRestaurant,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $catererData->email_address,
                    'firstname' => $catererData->first_name . ' ' . $catererData->last_name,
                );

                //   return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

                Mail::send('emails.template', $caterer_mail_data, function($message) use($caterer_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($caterer_mail_data['sender_email'], $caterer_mail_data['firstname'])->subject($caterer_mail_data['text']);
                });






                /*                 * * send mail to admin ** */

                $admin_mail_data = array(
                    'text' => $subjectMessageAdmin,
                    'customerContent' => $customerContent,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $adminuser->email,
                    'firstname' => "Admin",
                );

                // return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

                Mail::send('emails.template', $admin_mail_data, function($message) use($admin_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($admin_mail_data['sender_email'], 'Admin')->subject($admin_mail_data['text']);
                });


                /*                 * **************************************** */

                if (isset($input['kitchen_staff_id']) && !empty($input['kitchen_staff_id'])) {
                    $kitchenStaffId = $input['kitchen_staff_id'];

                    $kitchenStaffInfo = DB::table('users')->where('id', $input['kitchen_staff_id'])
                            ->first();
                    if ($kitchenStaffInfo) {
                        $mail_data = array(
                            'text' => 'You have been assigned a new order on ' . SITE_TITLE . '. Please login into Kitchen Staff App and prepare the order. ',
                            'sender_email' => $kitchenStaffInfo->email_address,
                            'firstname' => $kitchenStaffInfo->first_name . ' ' . $kitchenStaffInfo->last_name,
                        );

                        // return View::make('emails.template')->with($mail_data); // to check mail template data to view

                        Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject('You have been assigned a new order on ' . SITE_TITLE . '');
                        });
                    }

                    DB::table('orders')
                            ->where('id', $orderData->id)
                            ->update(['kitchen_staff_id' => $kitchenStaffId]);


                    $push_message = array(
                        "type" => 'kitchen_staff_order_assign',
                        "message" => 'Your have been assigned a new order.',
                        'customer_id' => $customerData->id,
                        'restaurant_id' => $catererData->id,
                        'kitchen_staff_id' => $kitchenStaffId,
                        'order_id' => $orderData->id
                    );

                    if ($kitchenStaffInfo->device_type == 'Android') {
                        $this->send_fcm_notify($kitchenStaffInfo->device_id, $push_message, 'KitchenStaff');
                    } else if ($kitchenStaffInfo->device_type == 'iPhone') {
                        $this->send_iphone_notification($kitchenStaffInfo->device_id, $push_message, 'KitchenStaff');
                    }
                }

                if (isset($input['delivery_person_id']) && !empty($input['delivery_person_id'])) {
                    $deliveryPersonId = $input['delivery_person_id'];

                    $deliveryPersonInfo = DB::table('users')->where('id', $input['delivery_person_id'])
                            ->first();

                    //echo '<pre>';print_r($orderData->order_number);die;

                    $mail_data = array(
                        'text' => 'You have received a new order ' . $orderData->order_number . ' on ' . SITE_TITLE . '. Please login into Delivery Person App and deliver the order. ',
                        'sender_email' => $deliveryPersonInfo->email_address,
                        'firstname' => $deliveryPersonInfo->first_name . ' ' . $deliveryPersonInfo->last_name,
                    );

                    // return View::make('emails.template')->with($mail_data); // to check mail template data to view

                    Mail::send('emails.template', $mail_data, function($message) use ($mail_data, $orderData) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject('You have received a new order ' . $orderData->order_number . ' on ' . SITE_TITLE . '');
                    });

                    DB::table('orders')
                            ->where('id', $orderData->id)
                            ->update(['delivery_person_id' => $deliveryPersonId]);

                    DB::table('users')
                            ->where('id', $deliveryPersonId)
                            ->update(['is_busy' => "1"]);


                    $push_message = array(
                        "type" => 'delivery_person_order_assign',
                        "message" => 'Your have been assigned a new order.',
                        'customer_id' => $customerData->id,
                        'restaurant_id' => $catererData->id,
                        'delivery_person_id' => $deliveryPersonId,
                        'order_id' => $orderData->id
                    );

                    if ($deliveryPersonInfo->device_type == 'Android') {
                        $this->send_fcm_notify($deliveryPersonInfo->device_id, $push_message, 'DeliveryPerson');
                    } else if ($deliveryPersonInfo->device_type == 'iPhone') {
                        $this->send_iphone_notification($deliveryPersonInfo->device_id, $push_message, 'DeliveryPerson');
                    }
                }

                //echo $kitchStaffId; exit;

                DB::table('orders')
                        ->where('id', $orderData->id)
                        ->update(['status' => $inputStatus]);

                // echo $orderContent; exit;

                echo $this->outputresult(json_encode($inputStatus), 'Status Updated Successfully');
                exit;
            }





//            if (!empty($orderData)) {
//                $data = array(
//                    'status' => $status,
//                    'order_number' => $order_number,
//                    'cancel_reason' => $reason,
//                );
//
//                DB::table('orders')
//                        ->where('order_number', $order_number)
//                        ->update($data);
//
//
//                $push_message = array(
//                    "type" => 'order_status_changed',
//                    "message" => 'Your Order Status has been changed to ' . $status . '.',
//                    'customer_id' => $customerData->id,
//                    'restaurant_id' => $catererData->id,
//                    'order_id' => $orderData->id
//                );
//
//                if ($customerData->device_type == 'Android') {
//                    $this->send_fcm_notify($customerData->device_id, $push_message, 'Customer');
//                } else if ($customerData->device_type == 'iPhone') {
//                    $this->send_iphone_notification($customerData->device_id, $push_message, 'Customer');
//                }
//
//
//                echo $this->outputresult(json_encode($status), 'Status Updated Successfully');
//                exit;
//            } else {
//                echo $this->errorOutput('Order not found');
//                exit;
//            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //Edit additional status
    public function showeEditAdditionaStatus() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {

            $rest_id = trim($dataJson['rest_id']);
            $status_id = trim($dataJson['status_id']);

            $input = $dataJson;

            if (empty($rest_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            $query = DB::table('orderstatus')->where('orderstatus.id', "=", $status_id)
                    ->where('orderstatus.user_id', "=", $rest_id);
            $result = $query->first();

            if (!empty($result)) {
                $data = array(
                    'status_name' => trim($input['status_name']),
                );

                DB::table('orderstatus')
                        ->where('id', $status_id)
                        ->where('user_id', $rest_id)
                        ->update($data);

                echo $this->successOutput('Status Updated Successfully');
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //Order status
    public function showeOrderStatus() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        if (!empty($dataJson)) {

            $rest_id = trim($dataJson['rest_id']);
            $input = $dataJson;

            if (empty($rest_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }


            $result = DB::table('orderstatus')
                    ->where('user_id', $rest_id)
                    ->select('orderstatus.status_name')
                    ->get();

            $order_status = array();

            if ($result) {
                foreach ($result as $results) {

                    $order_status[] = $results->status_name;
                }
            }

            $old_statusarray = array(
                '0' => 'Confirm',
                '1' => 'Cancel',
                '2' => 'Delivered'
            );

            $combine_status_array = array();
            $combine_status_array = array_merge($old_statusarray, $order_status);
            echo $this->output(json_encode($combine_status_array));
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //package list
    public function showPackageList() {

        $this->checkAPI($_REQUEST['api_key']);
        $query = DB::table('sponsorship')
                ->select('name', 'price', 'no_of_days', 'id')
                ->where('sponsorship.id', ">", '0');
        $result = $query->get();
        echo $this->output(json_encode($result));
        exit;
    }

    public function send_fcm_notify_to_rest($receiver_device_id, $message) {

        // include config
        //$url = 'https://android.googleapis.com/gcm/send';
        $url = 'https://fcm.googleapis.com/fcm/send';
        $receiver_device_id = array($receiver_device_id);
        $message = $message;

        $fields = array(
            'registration_ids' => $receiver_device_id,
            'data' => $message,
        );

        $headers = array(
            'Authorization: key=' . PUSH_NOTIFY_AUTH_KEY_CUST_SIDE,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
//        print_r($result);
//         print_r(json_encode($fields));
    }

    public function send_fcm_notify_to_cust($receiver_device_id, $message) {
        // include config
        //$url = 'https://android.googleapis.com/gcm/send';
        $url = 'https://fcm.googleapis.com/fcm/send';
        $receiver_device_id = array($receiver_device_id);
        $message = $message;

        $fields = array(
            'registration_ids' => $receiver_device_id,
            'data' => $message,
        );

        $headers = array(
            'Authorization: key=' . PUSH_NOTIFY_AUTH_KEY_REST_SIDE,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
//        print_r($result);
//         print_r(json_encode($fields));
    }

    public function send_iphone_notification_to_cust($receiver_device_id, $message, $text, $counter = '') {


// Put your device token here (without spaces):

        $deviceToken = $receiver_device_id;
// Put your device token here (without spaces):
// Put your private key's passphrase here:
        $passphrase = '';


///////////////////////////////////////////////////////////////////////////////////

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'files/FDcustomer.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server
        $fp = @stream_socket_client(
                        'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
//  $fp = @stream_socket_client(
//                        'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);


        if (!$fp)
            return 1;
//            exit("Failed to connect: $err $errstr" . PHP_EOL);
//        echo 'Connected to APNS' . PHP_EOL;
// Create the payload body
        $body['aps'] = array(
            'alert' => $text,
            'sound' => 'default',
            'body' => $message,
            'badge' => $counter,
            'content-available' => 1
        );

// Encode the payload as JSON
        $payload = json_encode($body);



// Build the binary notification
//        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        $msg = chr(0) . @pack('n', 32) . @pack('H*', $deviceToken) . @pack('n', strlen($payload)) . $payload;
//echo '<pre>';print_r($msg);
// Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

//        echo '<prE>'; print_r($result);
//
//        if (!$result)
//            echo 'Message not delivered' . PHP_EOL;
//        else
//            echo 'Message successfully delivered' . PHP_EOL;
// Close the connection to the server
        fclose($fp);
//        die;
    }

    //package payment
    public function showPackagePyament() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        if (!empty($dataJson)) {
            $user_id = trim($dataJson['user_id']);
            $transactionId = trim($dataJson['transaction_id']);
            $input = $dataJson;

            if (empty($transactionId)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            if (empty($user_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $user_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            if (empty($input['package']) || empty($input['no_of_days'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }


            $data = array(
                'price' => $input['price'],
                'transaction_id' => $transactionId,
                'user_id' => $user_id,
                'package' => $input['package'],
                'slug' => "Pay-" . time(),
                'type' => "Sponsorship",
                'status' => "Complete",
                'created' => date('Y-m-d'),
            );

            $id = DB::table('payments')->insertGetId(
                    $data
            );


            if ($id) {

                $dateofexp = strtotime('+' . $input['no_of_days'] . ' days', time());
                $saveList = array('expiry_date' => date('Y-m-d h:i:s', $dateofexp), 'featured' => 1, 'plan_id' => $input['package']);

                DB::table('users')
                        ->where('id', $user_id)
                        ->update($saveList);

                $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->first();

                $shopData = DB::table('payments')
                        ->where('user_id', $user_id)
                        ->where('id', $id)
                        ->first();

                $package = DB::table('sponsorship')
                        ->where('id', $input['package'])
                        ->first();



                $adminuser = DB::table('admins')
                        ->where('id', '1')
                        ->first();


                //echo $shopData->user_id; exit;

                $customerData = $userData;
                $customerContent = "";
                $bothContent = "";
                $headerContent = '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
                $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: left;  padding: 7px;" colspan="4">You have successfully make payment for purchase sponsorship (' . $package->name . ')</td>';
                $adminContent = '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: left;  padding: 7px;" colspan="4">Someone make payment for purchase sponsorship (' . $package->name . ')</td>';
                $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->first_name . ' ' . $customerData->last_name . '
                                </td>
                            </tr>';

                $bothContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Transaction id: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $shopData->transaction_id . '
                                </td>
                            </tr>';

                $bothContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Package: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $package->name . '
                                </td>
                            </tr>';

                $bothContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Price: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . CURR . ' ' . number_format($shopData->price, 2) . '
                                </td>
                            </tr>';

                $bothContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Duration: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $package->no_of_days . ' days
                                </td>
                            </tr>';


                $endContent = '</table>';

                $mailtocustomer = $headerContent . $customerContent . $bothContent . $endContent;

                $mailtoadmin = $headerContent . $adminContent . $bothContent . $endContent;

                // echo $mailtoadmin; exit;


                $mailSubjectRestaurant = 'Your payment successfully placed on ' . SITE_TITLE;
                $mailSubjectAdmin = 'New sponsorship payment received on ' . SITE_TITLE;

                $catererData = DB::table('users')
                        ->where('id', $shopData->user_id)
                        ->first();

                /**                 * send mail to caterer ** */
                $caterer_mail_data = array(
                    'text' => '',
                    'orderContent' => $mailtocustomer,
                    'mailSubjectRestaurant' => $mailSubjectRestaurant,
                    'sender_email' => $catererData->email_address,
                    'firstname' => '',
                );

                //  return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

                Mail::send('emails.template', $caterer_mail_data, function($message) use ($caterer_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($caterer_mail_data['sender_email'], $caterer_mail_data['firstname'])->subject($caterer_mail_data['mailSubjectRestaurant']);
                });


                /*                 * * send mail to admin ** */

                $admin_mail_data = array(
                    'text' => '',
                    'customerContent' => $customerContent,
                    'orderContent' => $mailtoadmin,
                    'mailSubjectAdmin' => $mailSubjectAdmin,
                    'sender_email' => $adminuser->email,
                    'firstname' => "Admin",
                );

//                   return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

                Mail::send('emails.template', $admin_mail_data, function($message) use ($admin_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($admin_mail_data['sender_email'], 'Admin')->subject($admin_mail_data['mailSubjectAdmin']);
                });

                echo $this->successOutput('Payment successfully.');
                exit;
            } else {
                echo $this->errorOutput('Please try again.');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //delete menu
    public function showdeleteMenu() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            //print_r($dataJson); exit;
            $user_id = trim($dataJson['user_id']);
            $menu_id = trim($dataJson['menu_id']);

            if (empty($user_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $user_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            if (empty($menu_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $menuData = DB::table('menu_item')
                    ->where('user_id', $user_id)
                    ->where('id', $menu_id)
                    ->first();

            if ($menuData) {
                DB::table('menu_item')->where('id', $menu_id)->delete();

                echo $this->successOutput('Menu deleted Successfully');
                exit;
            } else {
                echo $this->errorOutput('No record found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //opening hours 
    public function showOpeningHours() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            $rest_id = trim($dataJson['rest_id']);

            if (empty($rest_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            $opening_hours = DB::table('opening_hours')
                    ->where('user_id', $rest_id)
                    ->first();


            if ($opening_hours) {

                $catering_type = array();

                if (!empty($opening_hours->catering_type)) {
                    $catering_type = explode(',', $opening_hours->catering_type);
                }

                $catering = array();

                foreach ($catering_type as $catering_types) {

                    $catering_types = DB::table('mealtypes')
                            ->where('id', $catering_types)
                            ->first();

                    $catering[] = $catering_types->name;
                }

                $cat = '';
                $cat = implode(',', $catering);
                $opening_hours->cateringtype = $cat;
                echo $this->output(str_replace(":null", ':""', json_encode($opening_hours)));
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //deleivery charges 
    public function showDeliveryCharges() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {

            $rest_id = trim($dataJson['rest_id']);

            if (empty($rest_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            $pickup_charges = DB::table('pickup_charges')
                    ->where('user_id', $rest_id)
                    ->first();

//            echo '<pre>'; print_r($delivery_charges);die;

            if ($pickup_charges) {

                echo $this->output(str_replace(":null", ':""', json_encode($pickup_charges)));
                exit;
            } else {
                echo $this->errorOutput('No data found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //rest review list
    public function showRestReviewList() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            $rest_id = trim($dataJson['rest_id']);

            if (empty($rest_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $rest_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            $query = DB::table('reviews');
            $query->leftjoin("users", 'users.id', '=', 'reviews.user_id');
            $query->leftjoin("orders", 'orders.id', '=', 'reviews.item');
            $query->where('reviews.caterer_id', "=", $rest_id)
                    ->select('reviews.*', 'users.first_name', 'users.last_name', 'users.profile_image as user_image', 'orders.order_number');
            $query->orderBy('reviews.id', 'desc');
            $result = $query->get();

            if ($result) {
                echo $this->output(str_replace(":null", ':""', json_encode($result)));
                exit;
            } else {
                echo $this->errorOutput('No review for this restaurant');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

//  Staff order list  
    //staff order list
    public function showStaffOrderList() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
//        echo '<pre>'; print_r($dataJson);die;
        if (!empty($dataJson)) {

            $staff_id = trim($dataJson['staff_id']);
            $request_type = trim($dataJson['request_type']);

            if (empty($request_type)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            if (empty($staff_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $staff_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }


            if ($request_type == 'new') {

                $status = 'Confirm';
            } else if ($request_type == 'preparing') {

                $status = 'Preparing';
            } else if ($request_type == 'complete') {

                $status = 'Prepared';
            }

            $query = DB::table('orders')
                    ->select('orders.order_number', 'orders.id as order_id', 'orders.status as order_status', 'users.first_name', 'users.last_name', 'users.profile_image', 'orders.created', 'users.id as customer_id')
                    ->leftjoin("users", 'users.id', '=', 'orders.user_id')
                    ->where('orders.kitchen_staff_id', "=", $staff_id)
                    ->where('orders.status', "=", trim($status))
                    ->orderBy('orders.id', 'desc');

            $result = $query->get();

            echo $this->output(str_replace(":null", ':""', json_encode($result)));
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //staff changes status
    public function showStaffChangeStatus() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {

            $order_id = trim($dataJson['order_id']);
            $staff_id = trim($dataJson['staff_id']);
            $status = trim($dataJson['status']);
            $input = $dataJson;

            if (empty($staff_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $staff_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            $query = DB::table('orders')->where('orders.id', "=", $order_id)->where('orders.kitchen_staff_id', "=", $staff_id);
            $result = $query->first();

            if (!empty($result)) {


                $data = array(
                    'status' => $status,
                );

                DB::table('orders')
                        ->where('id', $order_id)
                        ->update($data);


                $orderData = DB::table('orders')
                        ->where('id', $order_id)
                        ->first();

                $tax = $orderData->tax;
                $delivery_charge = $orderData->delivery_charge;
                $discount = $orderData->discount;

                $adminuser = DB::table('admins')
                        ->where('id', '1')
                        ->first();

                $customerData = DB::table('users')
                        ->where('id', $orderData->user_id)
                        ->first();

                $numberofOrder = 1;

                $cartItems = DB::table('order_item')->whereIn('order_id', explode(',', $orderData->id))->get(); // get cart menu of this order
//                  echo '<prE>';
//                print_r($status);
//                die;

                $orderStatus = "";

                switch ($status) {
                    case "Preparing":
                        $orderStatus = "Preparing";
                        $subjectMessageCustomer = "Your order has been preparing by restaurant on " . SITE_TITLE;
                        $subjectMessageRestaurant = "You have preparing order on " . SITE_TITLE;
                        $subjectMessageAdmin = "An order has been preparing by restaurant on " . SITE_TITLE;


                        // check courier conditions end
                        break;

                    case "Prepared":
                        $orderStatus = "Prepared";
                        $subjectMessageCustomer = "Your order has been prepared by restaurant on " . SITE_TITLE;
                        $subjectMessageRestaurant = "You have prepared order on " . SITE_TITLE;
                        $subjectMessageAdmin = "An order has been prepared by restaurant on " . SITE_TITLE;

                        break;

                    default:

                        break;
                }

//                di/e;


                if (isset($orderData->delivery_charge) && $orderData->delivery_charge != '') {
                    $delivery_type = $orderData->delivery_type;
                } else {
                    // $delivery_charge = "0";
                    $delivery_type = "N/A";
                }
//                
                $orderNumber = $orderData->order_number;


                $customerContent = "";
                $customerContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
                $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: center; background-color: rgb(108, 158, 22); padding: 7px;" colspan="4">Customer Details</td>';
                $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->first_name . ' ' . $customerData->last_name . '
                                </td>
                            </tr>';

                $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Contact Number: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->contact . '
                                </td>
                            </tr>';

                $customerContent .= '</table>';


                $orderContent = "";
                // send mails
                /// send mail to customer 
                $orderContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
                $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 16px;padding: 10px;word-wrap: break-word; background-color:rgb(108, 158, 22); font-weight:bold; text-align:center;">
                                    Order Details
                                </td>
                            </tr>';


                $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $orderNumber . '
                                </td>
                                
                            </tr>';

//  echo '<pre>'; print_r($orderContent);die;

                $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;border-bottom:1px solid #ddd;word-wrap: break-word;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Items
                                </td>
                                 <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Base Price
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Quantity
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Sub Total
                                </td>
                            </tr>';



                if (!empty($cartItems)) {

                    $total = array();
                    foreach ($cartItems as $cartData) {
                        //echo "<pre>"; print_r($cartData); exit;
                        $menuItem = DB::table('menu_item')
                                ->where('id', $cartData->menu_id)
                                ->first();
                        $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $menuItem->item_name . '</td>';


                        $variant_id = explode(',', $cartData->variant_id);
                        $menuDataVal = DB::table('variants')
                                ->whereIn('id', $variant_id)
                                ->get();
                        //   echo "<pre>"; print_r($menuDataVal); exit;

                        foreach ($menuDataVal as $menuData) {

                            $sub_total = $menuData->price * $cartData->quantity;

                            $total[] = $sub_total;
                            $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   <strong>Variant </strong> (' . $menuData->name . ')
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($menuData->price, 2) . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $cartData->quantity . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                </td>
                                  </tr>';
                        }


                        if ($cartData->addon_id != "") {
                            $addon_id = explode(',', $cartData->addon_id);
                            $menuDataVal = DB::table('addons')
                                    ->whereIn('id', $addon_id)
                                    ->get();
                            foreach ($menuDataVal as $menuData) {
                                $sub_total = $menuData->addon_price * $cartData->quantity;

                                $total[] = $sub_total;
                                //  echo $sub_total;
                                $orderContent .= '<tr>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       <strong>Add-on </strong> (' . $menuData->addon_name . ')
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . App::make("HomeController")->numberformat($menuData->addon_price, 2) . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . $cartData->quantity . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                    </td>
                                      </tr>';
                            }
                        }


                        $orderContent .= '</tr>';
                    }
                }

                $catererData = DB::table('users')
                        ->where('id', $orderData->caterer_id)
                        ->first();
//                echo '<pre>'; print_r($catererData);die;
                $gTotal = array_sum($total);

                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd; font-weight:normal;">
                    Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                    ' . ' ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

// echo '<pre>'; print_r($orderData);die;

                if ($orderData->discount) {
                    $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Discount
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    - ' . App::make("HomeController")->numberformat($orderData->discount, 2) . '
                    </td>
                    </tr>';
                    $gTotal = $gTotal - $orderData->discount;
                }

//                echo '<prE>';
//                print_r($tax);
//                die;

                if ($adminuser->is_tax) {
                    $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Tax
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($tax / $numberofOrder, 2) . '
                    </td>
                    </tr>';
                    $gTotal = $gTotal + $tax / $numberofOrder;
                }


                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($delivery_charge / $numberofOrder, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $delivery_charge / $numberofOrder;


                $newcnn = '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

                $customerCC = $orderContent . $newcnn . '</table>';

//                die;
                /*                 * * send mail to customer ** */
                $mail_data = array(
                    'text' => $subjectMessageCustomer,
                    'orderContent' => $customerCC,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $customerData->email_address,
                    'firstname' => $customerData->first_name . ' ' . $customerData->last_name,
                );


                if ($adminuser->is_commission == 1) {

                    $comm_per = $adminuser->commission;
                    $tax_amount = $comm_per * $gTotal / 100;


//                    $orderContent .= '<tr>
//                            <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
//                               Admin Commission
//                            </td>
//                            <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
//                               ' . App::make("HomeController")->numberformat($tax_amount, 2) . '
//                            </td>
//                              </tr>';
//                    $gTotal = $gTotal - $tax_amount;
                }

                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

                $orderContent .= '</table>';

//                 echo $orderContent; exit;
// send mail to couier 
                if ($input['status'] == 'Confirm' && !empty($courierData)) {

                    $saveData = array(
                        'order_id' => $orderData->id,
                        'user_id' => $courierData->id,
                        'slug' => $this->createSlug('cservice'),
                        'created' => date('Y-m-d H:i:s'),
                    );
                    DB::table('order_courier')->insert(
                            $saveData
                    );


                    /*                     * * send mail to customer ** */
                    $mail_courier_data = array(
                        'text' => $subjectMessageCouieer,
                        'customerContent' => $customerContent,
                        'orderContent' => $orderContent,
                        'orderStatus' => $orderStatus,
                        'sender_email' => $courierData->email_address,
                        'firstname' => $courierData->first_name . ' ' . $courierData->last_name,
                    );

                    //return View::make('emails.template')->with($mail_data); // to check mail template data to view

                    Mail::send('emails.template', $mail_courier_data, function($message) use($mail_courier_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_courier_data['sender_email'], $mail_courier_data['firstname'])->subject($mail_courier_data['text']);
                    });
                }


                //return View::make('emails.template')->with($mail_data); // to check mail template data to view

                Mail::send('emails.template', $mail_data, function($message) use($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject($mail_data['text']);
                });



                $push_message = array(
                    "type" => 'order_status_changed',
                    "message" => 'Your Order Status has been changed to ' . $orderStatus . '.',
                    'customer_id' => $customerData->id,
                    'restaurant_id' => $catererData->id,
                    'order_id' => $order_id,
                );

                if ($customerData->device_type == 'Android') {
                    $this->send_fcm_notify($customerData->device_id, $push_message, 'Customer');
                } else if ($customerData->device_type == 'iPhone') {
                    $this->send_iphone_notification($customerData->device_id, $push_message, 'Customer');
                }



                /*                 * * send mail to restaurant ** */

                $caterer_mail_data = array(
                    'text' => $subjectMessageRestaurant,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $catererData->email_address,
                    'firstname' => $catererData->first_name . ' ' . $catererData->last_name,
                );

                //   return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

                Mail::send('emails.template', $caterer_mail_data, function($message) use($caterer_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($caterer_mail_data['sender_email'], $caterer_mail_data['firstname'])->subject($caterer_mail_data['text']);
                });


                $push_message = array(
                    "type" => 'order_status_changed',
                    "message" => 'Kitchen Staff has changed Order Status to ' . $orderStatus . '.',
                    'customer_id' => $customerData->id,
                    'restaurant_id' => $catererData->id,
                    'order_id' => $order_id,
                );

                if ($catererData->device_type == 'Android') {
                    $this->send_fcm_notify($catererData->device_id, $push_message, 'Restaurant');
                } else if ($catererData->device_type == 'iPhone') {
                    $this->send_iphone_notification($catererData->device_id, $push_message, 'Restaurant');
                }


                /*                 * * send mail to admin ** */

                $admin_mail_data = array(
                    'text' => $subjectMessageAdmin,
                    'customerContent' => $customerContent,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $adminuser->email,
                    'firstname' => "Admin",
                );

                // return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

                Mail::send('emails.template', $admin_mail_data, function($message) use($admin_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($admin_mail_data['sender_email'], 'Admin')->subject($admin_mail_data['text']);
                });

                echo $this->outputresult(json_encode($status), 'Status Updated Successfully');
                exit;
            } else {
                echo $this->errorOutput('Order not found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //staff order history list
    public function showStaffOrderHistoryList() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
//        echo '<pre>'; print_r($dataJson);die;
        if (!empty($dataJson)) {

            $staff_id = trim($dataJson['staff_id']);

            if (empty($staff_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }


            $users = DB::table('users')
                    ->where('id', $staff_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

//            die;
            $query = DB::table('orders')
                            ->select('orders.order_number', 'orders.id as order_id', 'orders.status as order_status', 'users.first_name', 'users.last_name', 'users.profile_image', 'orders.created', 'users.id as customer_id')
                            ->leftjoin("users", 'users.id', '=', 'orders.user_id')
                            ->where('orders.kitchen_staff_id', "=", $staff_id)
                            ->where(function ($query) {
                                $query->where('orders.status', '=', 'On Delivery')
                                ->orWhere('orders.status', '=', 'Delivered')
                                ->orWhere('orders.status', '=', 'Assign To Delivery');
                            })->orderBy('orders.id', 'desc');

            $result = $query->get();

            echo $this->output(str_replace(":null", ':""', json_encode($result)));
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

// Delivery Person
// 
    //staff order list
    public function showDeliveryPerOrderList() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
//        echo '<pre>'; print_r($dataJson);die;
        if (!empty($dataJson)) {

            $deliver_id = trim($dataJson['deliver_id']);

            if (empty($deliver_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $deliver_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }


            $query = DB::table('orders')
                    ->select('orders.order_number', 'addresses.id as address_id', 'orders.id as order_id', 'orders.status as order_status', 'users.first_name', 'users.last_name', 'users.profile_image', 'orders.created', 'users.id as customer_id', 'addresses.address_title', 'addresses.address_type', 'addresses.street_name', 'addresses.building', 'addresses.floor', 'addresses.apartment', 'addresses.phone_number', 'addresses.extension', 'addresses.directions', 'areas.name as area_name', 'cities.name as city_name')
                    ->leftjoin("users", 'users.id', '=', 'orders.user_id')
                    ->leftjoin("addresses", 'addresses.id', '=', 'orders.address_id')
                    ->leftjoin("cities", 'cities.id', '=', 'addresses.city')
                    ->leftjoin("areas", 'areas.id', '=', 'addresses.area')
                    ->where('orders.delivery_person_id', "=", $deliver_id)
                    ->where(function ($query) {
                        $query->Where('orders.status', '=', 'On Delivery')
                        ->orWhere('orders.status', '=', 'Assign To Delivery');
                    })
                    ->orderBy('orders.id', 'desc');

            $result = $query->first();

            if (!empty($result)) {
                $building = '';
                $building = $result->building ? $result->building . ',' : '';

                $floor = '';
                $floor = $result->floor ? $result->floor . ',' : '';

                $apartment = '';
                $apartment = $result->apartment ? $result->apartment . ',' : '';

                $extension = '';
                $extension = $result->extension ? $result->extension . ',' : '';

                $directions = '';
                $directions = $result->directions ? $result->directions : '';

                $area = '';
                $area = $result->area_name ? $result->area_name . ',' : '';

                $city = '';
                $city = $result->city_name ? $result->city_name . '' : '';

                $street_name = '';
                $street_name = $result->street_name ? $result->street_name . ',' : '';

                $address_title = '';
                $address_title = $result->address_title ? $result->address_title . ',' : '';


                $result->fulladdress = $floor . " " . $apartment . " " . $building . " " . $address_title . " " . $street_name . " " . $extension . " " . $directions . " " . $area . " " . $city;
                $result->mapaddress = $address_title . " " . $area . " " . $city;
            } else {
                $result = (object) array();
            }
            echo $this->output(str_replace(":null", ':""', json_encode($result)));
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //staff changes status
    public function showDeliverChangeStatus() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {

            $order_id = trim($dataJson['order_id']);
            $deliver_id = trim($dataJson['deliver_id']);
            $status = trim($dataJson['status']);
            $input = $dataJson;

            if (empty($deliver_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $users = DB::table('users')
                    ->where('id', $deliver_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

            if (empty($order_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $query = DB::table('orders')->where('orders.id', "=", $order_id)->where('orders.delivery_person_id', "=", $deliver_id);
            $result = $query->first();

//             
            if (!empty($result)) {


                $data = array(
                    'status' => $status,
                );

                DB::table('orders')
                        ->where('id', $order_id)
                        ->update($data);


                if ($status == 'Delivered') {
                    $data = array(
                        'is_busy' => '0',
                    );

                    DB::table('users')
                            ->where('id', $deliver_id)
                            ->update($data);


                    $datas = array(
                        'delivery_date' => date('Y-m-d H:i:s'),
                    );
                    DB::table('orders')
                            ->where('id', $order_id)
                            ->update($datas);
                }


                $orderData = DB::table('orders')
                        ->where('id', $order_id)
                        ->first();

                $tax = $orderData->tax;
                $delivery_charge = $orderData->delivery_charge;
                $discount = $orderData->discount;

                $adminuser = DB::table('admins')
                        ->where('id', '1')
                        ->first();

                $customerData = DB::table('users')
                        ->where('id', $orderData->user_id)
                        ->first();

                $numberofOrder = 1;

                $cartItems = DB::table('order_item')->whereIn('order_id', explode(',', $orderData->id))->get(); // get cart menu of this order
//                  echo '<prE>';
//                print_r($status);
//                die;

                switch ($status) {
                    case "On Delivery":
                        $orderStatus = "On Delivery";
                        $subjectMessageCustomer = "Your order is out for delivery by restaurant on " . SITE_TITLE;
                        $subjectMessageRestaurant = "You have on delivery order on " . SITE_TITLE;
                        $subjectMessageAdmin = "An order has been on delivery by restaurant on " . SITE_TITLE;


                        // check courier conditions end
                        break;

                    case "Delivered":
                        $orderStatus = "Prepared";
                        $subjectMessageCustomer = "Your order has been delivered by restaurant on " . SITE_TITLE;
                        $subjectMessageRestaurant = "You have delivered order on " . SITE_TITLE;
                        $subjectMessageAdmin = "An order has been delivered by restaurant on " . SITE_TITLE;

                        break;

                    default:

                        break;
                }

//                di/e;


                if (isset($orderData->delivery_charge) && $orderData->delivery_charge != '') {
                    $delivery_type = $orderData->delivery_type;
                } else {
                    // $delivery_charge = "0";
                    $delivery_type = "N/A";
                }
//                
                $orderNumber = $orderData->order_number;


                $customerContent = "";
                $customerContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
                $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: center; background-color: rgb(108, 158, 22); padding: 7px;" colspan="4">Customer Details</td>';
                $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->first_name . ' ' . $customerData->last_name . '
                                </td>
                            </tr>';

                $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Contact Number: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->contact . '
                                </td>
                            </tr>';

                $customerContent .= '</table>';


                $orderContent = "";
                // send mails
                /// send mail to customer 
                $orderContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
                $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 16px;padding: 10px;word-wrap: break-word; background-color:rgb(108, 158, 22); font-weight:bold; text-align:center;">
                                    Order Details
                                </td>
                            </tr>';


                $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $orderNumber . '
                                </td>
                                
                            </tr>';

//  echo '<pre>'; print_r($orderContent);die;

                $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;border-bottom:1px solid #ddd;word-wrap: break-word;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Items
                                </td>
                                 <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Base Price
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Quantity
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Sub Total
                                </td>
                            </tr>';



                if (!empty($cartItems)) {

                    $total = array();
                    foreach ($cartItems as $cartData) {
                        //echo "<pre>"; print_r($cartData); exit;
                        $menuItem = DB::table('menu_item')
                                ->where('id', $cartData->menu_id)
                                ->first();
                        $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $menuItem->item_name . '</td>';


                        $variant_id = explode(',', $cartData->variant_id);
                        $menuDataVal = DB::table('variants')
                                ->whereIn('id', $variant_id)
                                ->get();
                        //   echo "<pre>"; print_r($menuDataVal); exit;

                        foreach ($menuDataVal as $menuData) {

                            $sub_total = $menuData->price * $cartData->quantity;

                            $total[] = $sub_total;
                            $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   <strong>Variant </strong> (' . $menuData->name . ')
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($menuData->price, 2) . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $cartData->quantity . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                </td>
                                  </tr>';
                        }


                        if ($cartData->addon_id != "") {
                            $addon_id = explode(',', $cartData->addon_id);
                            $menuDataVal = DB::table('addons')
                                    ->whereIn('id', $addon_id)
                                    ->get();
                            foreach ($menuDataVal as $menuData) {
                                $sub_total = $menuData->addon_price * $cartData->quantity;

                                $total[] = $sub_total;
                                //  echo $sub_total;
                                $orderContent .= '<tr>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       <strong>Add-on </strong> (' . $menuData->addon_name . ')
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . App::make("HomeController")->numberformat($menuData->addon_price, 2) . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . $cartData->quantity . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                    </td>
                                      </tr>';
                            }
                        }


                        $orderContent .= '</tr>';
                    }
                }

                $catererData = DB::table('users')
                        ->where('id', $orderData->caterer_id)
                        ->first();
//                echo '<pre>'; print_r($catererData);die;
                $gTotal = array_sum($total);

                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd; font-weight:normal;">
                    Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                    ' . ' ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

// echo '<pre>'; print_r($orderData);die;

                if ($orderData->discount) {
                    $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Discount
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    - ' . App::make("HomeController")->numberformat($orderData->discount, 2) . '
                    </td>
                    </tr>';
                    $gTotal = $gTotal - $orderData->discount;
                }

//                echo '<prE>';
//                print_r($tax);
//                die;

                if ($adminuser->is_tax) {
                    $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Tax
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($tax / $numberofOrder, 2) . '
                    </td>
                    </tr>';
                    $gTotal = $gTotal + $tax / $numberofOrder;
                }


                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($delivery_charge / $numberofOrder, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $delivery_charge / $numberofOrder;


                $newcnn = '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

                $customerCC = $orderContent . $newcnn . '</table>';

//                die;
                /*                 * * send mail to customer ** */
                $mail_data = array(
                    'text' => $subjectMessageCustomer,
                    'orderContent' => $customerCC,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $customerData->email_address,
                    'firstname' => $customerData->first_name . ' ' . $customerData->last_name,
                );


                if ($adminuser->is_commission == 1) {

                    $comm_per = $adminuser->commission;
                    $tax_amount = $comm_per * $gTotal / 100;


//                    $orderContent .= '<tr>
//                            <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
//                               Admin Commission
//                            </td>
//                            <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
//                               ' . App::make("HomeController")->numberformat($tax_amount, 2) . '
//                            </td>
//                              </tr>';
//                    $gTotal = $gTotal - $tax_amount;
                }

                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

                $orderContent .= '</table>';

//                 echo $orderContent; exit;
// send mail to couier 
                if ($input['status'] == 'Confirm' && !empty($courierData)) {

                    $saveData = array(
                        'order_id' => $orderData->id,
                        'user_id' => $courierData->id,
                        'slug' => $this->createSlug('cservice'),
                        'created' => date('Y-m-d H:i:s'),
                    );
                    DB::table('order_courier')->insert(
                            $saveData
                    );


                    /*                     * * send mail to customer ** */
                    $mail_courier_data = array(
                        'text' => $subjectMessageCouieer,
                        'customerContent' => $customerContent,
                        'orderContent' => $orderContent,
                        'orderStatus' => $orderStatus,
                        'sender_email' => $courierData->email_address,
                        'firstname' => $courierData->first_name . ' ' . $courierData->last_name,
                    );

                    //return View::make('emails.template')->with($mail_data); // to check mail template data to view

                    Mail::send('emails.template', $mail_courier_data, function($message) use($mail_courier_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_courier_data['sender_email'], $mail_courier_data['firstname'])->subject($mail_courier_data['text']);
                    });
                }


                //return View::make('emails.template')->with($mail_data); // to check mail template data to view

                Mail::send('emails.template', $mail_data, function($message) use($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject($mail_data['text']);
                });



                $push_message = array(
                    "type" => 'order_status_changed',
                    "message" => 'Your Order Status has been changed to ' . $orderStatus . '.',
                    'customer_id' => $customerData->id,
                    'restaurant_id' => $catererData->id,
                    'order_id' => $order_id,
                );

                if ($customerData->device_type == 'Android') {
                    $this->send_fcm_notify($customerData->device_id, $push_message, 'Customer');
                } else if ($customerData->device_type == 'iPhone') {
                    $this->send_iphone_notification($customerData->device_id, $push_message, 'Customer');
                }




                /*                 * * send mail to restaurant ** */

                $caterer_mail_data = array(
                    'text' => $subjectMessageRestaurant,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $catererData->email_address,
                    'firstname' => $catererData->first_name . ' ' . $catererData->last_name,
                );

                //   return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

                Mail::send('emails.template', $caterer_mail_data, function($message) use($caterer_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($caterer_mail_data['sender_email'], $caterer_mail_data['firstname'])->subject($caterer_mail_data['text']);
                });


                $push_message = array(
                    "type" => 'order_status_changed',
                    "message" => 'Delivery Person has changed Order Status to ' . $orderStatus . '.',
                    'customer_id' => $customerData->id,
                    'restaurant_id' => $catererData->id,
                    'order_id' => $order_id,
                );

                if ($catererData->device_type == 'Android') {
                    $this->send_fcm_notify($catererData->device_id, $push_message, 'Restaurant');
                } else if ($catererData->device_type == 'iPhone') {
                    $this->send_iphone_notification($catererData->device_id, $push_message, 'Restaurant');
                }


                /*                 * * send mail to admin ** */

                $admin_mail_data = array(
                    'text' => $subjectMessageAdmin,
                    'customerContent' => $customerContent,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $adminuser->email,
                    'firstname' => "Admin",
                );

                // return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

                Mail::send('emails.template', $admin_mail_data, function($message) use($admin_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($admin_mail_data['sender_email'], 'Admin')->subject($admin_mail_data['text']);
                });

                echo $this->outputresult(json_encode($status), 'Status Updated Successfully');
                exit;


                echo $this->outputresult(json_encode($status), 'Status Updated Successfully');
                exit;
            } else {
                echo $this->errorOutput('Order not found');
                exit;
            }
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    //staff order history list
    public function showDeliverOrderHistoryList() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
//        echo '<pre>'; print_r($dataJson);die;
        if (!empty($dataJson)) {

            $deliver_id = trim($dataJson['deliver_id']);

            if (empty($deliver_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }


            $users = DB::table('users')
                    ->where('id', $deliver_id)
                    ->first();

            if (empty($users)) {
                echo $this->errorOutput('User not exist');
                exit;
            }

//            die;
            $query = DB::table('orders')
                            ->select('orders.order_number', 'orders.id as order_id', 'orders.status as order_status', 'users.first_name', 'users.last_name', 'users.profile_image', 'orders.created', 'users.id as customer_id', 'orders.delivery_date')
                            ->leftjoin("users", 'users.id', '=', 'orders.user_id')
                            ->where('orders.delivery_person_id', "=", $deliver_id)
                            ->where(function ($query) {
                                $query->Where('orders.status', '=', 'Delivered');
                            })->orderBy('orders.id', 'desc');

            $result = $query->get();

            echo $this->output(str_replace(":null", ':""', json_encode($result)));
            exit;
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    public function getAdminConfiguration() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        $adminData = DB::table('admins')
                ->where('id', '1')
                ->first();

        $data = array(
            'adminData' => $adminData,
        );

        echo $this->output(str_replace(":null", ':""', json_encode($data)));
        exit;
    }

    public function getUserDataWithStatus() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);


        $restaurant_id = $dataJson['restaurant_id'];
        if (empty($restaurant_id)) {
            echo $this->errorOutput('Restaurant is required field.');
            exit;
        }

        $kitchStaffArray = array();
        $deliveryPersonArray = array();


        $kitchenStaffs = User::orderBy('first_name', 'asc')->where('status', "=", "1")->where('user_type', "=", 'KitchenStaff')->where('restaurant_id', "=", $restaurant_id)->get();
        if (!empty($kitchenStaffs)) {
            $i = 0;
            foreach ($kitchenStaffs as $kitchenStaff) {
                $kitchStaffArray[$i]['id'] = $kitchenStaff->id;
                $kitchStaffArray[$i]['name'] = ucfirst($kitchenStaff->first_name . " " . $kitchenStaff->last_name);
                $i++;
            }
        }

        $totalKS = sizeof($kitchenStaffs);

        $deliveryPersons = User::orderBy('first_name', 'asc')->where('status', "=", "1")->where('is_busy', "=", "0")->where('user_type', "=", 'DeliveryPerson')->where('restaurant_id', "=", $restaurant_id)->get();
        if (!empty($deliveryPersons)) {
            $k = 0;
            foreach ($deliveryPersons as $deliveryPerson) {
                $deliveryPersonArray[$k]['id'] = ucfirst($deliveryPerson->id);
                $deliveryPersonArray[$k]['name'] = ucfirst($deliveryPerson->first_name . " " . $deliveryPerson->last_name);
                $k++;
            }
        }

        $totalDP = sizeof($deliveryPersons);




        $data = array(
            'kitchenStaffs' => $kitchStaffArray,
            'deliveryPersons' => $deliveryPersonArray,
//            'totalKitchenStaffs' => $totalKS,
//            'yotalDeliveryPersons' => $totalDP,
        );

        echo $this->output(str_replace(":null", ':""', json_encode($data)));
        exit;
    }

    public function showActiveRestaurantList() {
        $this->checkAPI($_REQUEST['api_key']);
        $query = DB::table('users');
        $query->where('users.status', "=", '1')
                ->where('users.activation_status', "=", '1')
                ->where('users.approve_status', "=", '1')
                ->where('users.user_type', "=", 'Restaurant')
                ->select('users.id', 'users.first_name')
                ->orderBy('users.first_name')
        ;
        $result = $query->get();
        if ($result) {
            echo $this->output(json_encode($result));
            exit;
        } else {
            echo $this->errorOutput('No data found');
            exit;
        }
    }

    public function staffRegister() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            //print_r($dataJson); exit;
            $email_address = trim($dataJson['email_address']);
            $restaurant_id = trim($dataJson['restaurant_id']);
            $userType = trim($dataJson['user_type']);

            if ($userType == "KitchenStaff") {
                $userType = "KitchenStaff";
            } else {
                $userType = "DeliveryPerson";
            }


            if (empty($email_address) || empty($restaurant_id)) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $ifexist = DB::table('users')
                    ->where('email_address', $email_address)
                    ->first();
            //print_r($ifexist); exit;
            if (!empty($ifexist)) {
                echo $this->errorOutput('Email address is already exists.');
                exit;
            }

            $first_name = $dataJson['first_name'];
            $last_name = $dataJson['last_name'];
            $contact_number = $dataJson['contact_number'];

            $saveUser = array(
                'first_name' => $dataJson['first_name'],
                'last_name' => $dataJson['last_name'],
                'restaurant_id' => $dataJson['restaurant_id'],
                'email_address' => $email_address,
                'contact' => $dataJson['contact_number'],
                'device_id' => $dataJson['device_id'],
                'device_type' => $dataJson['device_type'],
                'language' => $dataJson['language'],
                'password' => md5($dataJson['password']),
                'slug' => $this->createUniqueSlug($dataJson['first_name'], 'users'),
                'user_type' => $userType,
                'activation_status' => 1,
                'created' => date('Y-m-d H:i:s'),
            );

            DB::table('users')->insert(
                    $saveUser
            );


            $user_id = DB::getPdo()->lastInsertId();

            // setup for activation link
            // send email to user
            $userEmail = $dataJson['email_address'];





            // send email to administrator
            $mail_data = array(
                'text' => 'Your account is successfully created. Your registration is being reviewed and pending acceptance. After acceptance you can use below credentials for login as ' . $userType . " on " . SITE_TITLE . '.',
                'email' => $dataJson['email_address'],
                'password' => $dataJson['password'],
                'firstname' => $dataJson['first_name'],
            );
            // print_r($mail_data); exit;
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created');
            });

            // get admin data
            $adminuser = DB::table('admins')
                    ->where('id', 1)
                    ->first();
            $adminEmail = $adminuser->email;

            $restroData = DB::table('users')
                    ->where('id', $restaurant_id)
                    ->first();

            if ($restroData) {
                $adminEmail = $restroData->email_address;
                $restroName = $restroData->first_name;

                $mail_data = array(
                    'text' => 'A request for new account for ' . $userType . ' has been received on ' . SITE_TITLE . '. Below are the details.',
                    'name' => $first_name,
                    'contact_number' => $contact_number,
                    'email_address' => $email_address,
                    'adminEmail' => $adminEmail,
                    'firstname' => $restroName,
                    'userType' => $userType
                );

                //   return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['adminEmail'], $mail_data['firstname'])->subject('New Account Request on ' . SITE_TITLE . ' for ' . $mail_data['userType']);
                });
            }
            echo $this->successOutput('Congratulation! You are registered successfully, please check your email to activate your account. We are checking your details and will contact you shortly.');
            exit;
            //  return Redirect::to('/user/register')->withSuccess('You have successfully register on ' . SITE_TITLE);
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

}
