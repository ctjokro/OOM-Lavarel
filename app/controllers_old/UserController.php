<?php

use Moltin\Cart\Cart;
use Moltin\Cart\Storage\CartSession;
use Moltin\Cart\Identifier\Cookie;

class UserController extends BaseController {
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

    public function logincheck($url) {

        if (!Session::has('user_id')) {
            Session::put('return', $url);
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        } else {

            $user_id = Session::get('user_id');
            $userData = DB::table('users')
                    ->where('id', $user_id)
                    ->first();
            if (empty($userData)) {
                Session::forget('user_id');
                return Redirect::to('/');
            }
        }
    }

    public function showAdmin_index() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();
        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }
        $query = User::sortable()
                ->where("user_type", "=", 'Restaurant')
                ->where(function ($query) use ($search_keyword) {
                    $query->where('first_name', 'LIKE', '%' . $search_keyword . '%')
                    ->orwhere('last_name', 'LIKE', '%' . $search_keyword . '%')
                    ->orwhere('email_address', 'LIKE', '%' . $search_keyword . '%');
                });
        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
            switch ($action) {
                case "Activate":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));

                    Session::put('success_message', 'Restaurant(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    Session::put('success_message', 'Restaurant(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->delete();
                    Session::put('success_message', 'Restaurant(s) deleted successfully');
                    break;
            }
        }

        $separator = implode("/", $separator);

        // Get all the users
        $users = $query->orderBy('id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('Users/adminindex', compact('users'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_add() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        $input = Input::all();
        if (!empty($input)) {


            $email_address = trim($input['email_address']);
            $rules = array(
                'first_name' => 'required', // make sure the first name field is not empty
                //  'last_name' => 'required', // make sure the last name field is not empty
                'email_address' => 'required|unique:users|email', // make sure the email address field is not empty
                'password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'cpassword' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'city' => 'required',
                'area' => 'required',
                //    'deliver_to' => 'required',
                'profile_image' => 'mimes:jpeg,png,jpg',
                'contact' => 'required'
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/restaurants/admin_add')->withErrors($validator)->withInput(Input::all());
            } else {

                if (Input::hasFile('profile_image')) {
                    $file = Input::file('profile_image');
                    $profileImageName = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_FULL_PROFILE_IMAGE_PATH, time() . $file->getClientOriginalName());
                } else {
                    $profileImageName = "";
                }
                $slug = $this->createUniqueSlug($input['first_name'], 'users');
                $saveUser = array(
                    'first_name' => $input['first_name'],
                    //'last_name' => $input['last_name'],
                    'contact' => $input['contact'],
                    'address' => $input['address'],
                    'email_address' => $input['email_address'],
                    'password' => md5($input['password']),
                    'profile_image' => $profileImageName,
                    'activation_status' => 1,
                    'status' => '1',
                    'city' => $input['city'],
                    // 'deliver_to' => implode(",", $input['deliver_to']),
                    'approve_status' => '1',
                    'area' => $input['area'],
                    'slug' => $slug,
                    'user_type' => "Restaurant",
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('users')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();

                $data = array(
                    'user_id' => $id,
                    'open_days' => 'mon,tue,wed,thu,fri,sat,sun',
                    'status' => '1',
                    'start_time' => "08:00:00,08:00:00,08:00:00,08:00:00,08:00:00,08:00:00,08:00:00",
                    'end_time' => "23:00:00,23:00:00,23:00:00,23:00:00,23:00:00,23:00:00,23:00:00",
                    'created' => date('Y-m-d H:is')
                );
                DB::table('opening_hours')
                        ->insert($data);

                $user_id = DB::getPdo()->lastInsertId();



                $dataew = array(
                    'user_id' => $id,
                    'is_default_delivery' => '0',
                    'pick_up' => '0',
                    'normal' => '',
                    'advance' => "",
                    'delivery_charge_limit' => "",
                    'created' => date('Y-m-d H:is')
                );
                DB::table('pickup_charges')
                        ->insert($dataew);


                $userEmail = $input['email_address'];

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account is successfully created by admin as Restaurant. Below are your login credentials.',
                    'email' => $input['email_address'],
                    'password' => $input['password'],
                    'firstname' => $input['first_name'],
                );

                //print_r($mail_data); exit;
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created by admin as Restaurant');
                        });

                return Redirect::to('/admin/restaurants/admin_index')->with('success_message', 'Restaurant saved successfully.');
            }
        } else {
            return View::make('/Users/admin_add');
        }
    }

    public function showAdmin_edituser($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $input = Input::all();

        $user = DB::table('users')
                        ->where('slug', $slug)->first();
        $user_id = $user->id;


        if (!empty($input)) {
            $old_profile_image = $input['old_profile_image'];
            $rules = array(
                'first_name' => 'required', // make sure the first name field is not empty
                //   'last_name' => 'required', // make sure the last name field is not empty
                'city' => 'required',
                'area' => 'required',
                // 'deliver_to' => 'required',
                'profile_image' => 'mimes:jpeg,png,jpg',
                'contact' => 'required'
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/restaurants/Admin_edituser/' . $user->slug)
                                ->withErrors($validator) // send back all errors
                                ->withInput(Input::all());
            } else {

                if (Input::hasFile('profile_image')) {
                    $file = Input::file('profile_image');
                    $profileImageName = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_FULL_PROFILE_IMAGE_PATH, time() . $file->getClientOriginalName());

                    @unlink(UPLOAD_FULL_PROFILE_IMAGE_PATH . $old_profile_image);
                } else {
                    $profileImageName = $old_profile_image;
                }
                $data = array(
                    'first_name' => $input['first_name'],
                    //   'last_name' => $input['last_name'],
                    'contact' => $input['contact'],
                    'address' => $input['address'],
                    'profile_image' => $profileImageName,
                    'status' => '1',
                    'city' => $input['city'],
                    // 'deliver_to' => implode(",", $input['deliver_to']),
                    'area' => $input['area'],
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);


                return Redirect::to('/admin/restaurants/admin_index')->with('success_message', 'Restaurant profile details updated successfully.');
            }
        } else {



            return View::make('/Users/admin_edituser')->with('detail', $user);
        }
    }

    public function showAdmin_activeuser($slug = null) {
        if (!empty($slug)) {

            // check admin approval
            $Data = DB::table('users')
                    ->select("approve_status", "email_address", "first_name")
                    ->where('slug', $slug)
                    ->first();
            if (!$Data->approve_status) {
                // send email to user for account varification
                $userEmail = $Data->email_address;

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account has been successfully confirmed by ' . SITE_TITLE . ' as Restaurant.',
                    'email' => $userEmail,
                    'firstname' => $Data->first_name
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully approved by ' . SITE_TITLE);
                        });
            }

            DB::table('users')
            ->where('slug', $slug)
            ->update(['status' => 1, 'approve_status' => 1]);

            return Redirect::to('/admin/restaurants/admin_index')->with('success_message', 'Restaurant(s) activated successfully');
        }
    }

    public function showAdmin_deactiveuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')
            ->where('slug', $slug)
            ->update(['status' => 0]);

            return Redirect::to('/admin/restaurants/admin_index')->with('success_message', 'Restaurant(s) deactivated successfully');
        }
    }

    public function showAdmin_deleteuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')->where('slug', $slug)->delete();
            return Redirect::to('/admin/restaurants/admin_index')->with('success_message', 'Restaurant deleted successfully');
        }
    }

    public function showRegister() {
        $input = Input::all();
        if (!empty($input)) {
            $email_address = trim($input['email_address']);
            $rules = array(
                'first_name' => 'required', // make sure the first name field is not empty
                'last_name' => 'required', // make sure the last name field is not empty
                'email_address' => 'required|unique:users|email', // make sure the email address field is not empty
                'password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'cpassword' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'profile_image' => 'required'
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/register')
                                ->withErrors($validator) // send back all errors to the register form
                                ->withInput(Input::except('password'));
            } else {

                if (Input::hasFile('image')) {
                    $file = Input::file('image');
                    $file->move('uploads', $file->getClientOriginalName());
                }
                $saveUser = array(
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'email_address' => $input['email_address'],
                    'password' => md5($input['password']),
                    'country_id' => $input['country_id'],
                    'status' => '1',
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('users')->insert(
                        $saveUser
                );

                return Redirect::to('/user/register')->withSuccess('You have successfully register on ' . SITE_TITLE);
            }
        } else {
            return View::make('Users/register');
        }
    }

    // activate user profile
    public function showActivateprofile($slug = "") {
        $this->layout = false;

        // check code fro reset table
        $Data = DB::table('reset_link')
                ->where('reset_link.slug', $slug)->join('users', 'users.id', '=', 'reset_link.user_id')
                ->first();
        if (!empty($Data)) {

            // get user details
            $user = DB::table('users')
                    ->where('users.id', $Data->user_id)
                    ->first();

            if ($user->user_type == "Restaurant") {
                DB::table('users')
                ->where('id', $Data->user_id)
                ->update(['activation_status' => 1]);
            } else {
                DB::table('users')
                ->where('id', $Data->user_id)
                ->update(['activation_status' => 1, 'approve_status' => 1, 'status' => 1]);
            }
            // process for activate profile goes here
            // delete data from reset table
            DB::table('reset_link')->where('slug', $slug)->delete();


            // get admin data
            $adminuser = DB::table('admins')
                    ->where('id', 1)
                    ->first();
            $adminEmail = $adminuser->email;

            // send email to administrator
            $mail_data = array(
                'text' => ucfirst($user->first_name) . ' activated their profile. Below are the details.',
                'name' => $user->first_name . " " . $user->last_name,
                'email_address' => $user->email_address,
                'adminEmail' => $adminEmail,
                'firstname' => "Admin"
            );

//            return View::make('emails.template')->with($mail_data); // to check mail template data to view
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_data['adminEmail'], "Admin")->subject(ucfirst($mail_data['name']) . ' activated their profile.');
                    });

            // return error message
            if ($user->user_type == "Restaurant") {
                Session::put('success_message', "Your account verification has been completed successfully.Now you can log in only after when admin will approve your account.");
            } else {
                Session::put('success_message', "Your account verification has been completed successfully.");
            }

            return Redirect::to('/');
        } else {

            // return error message
            Session::put('error_message', "You have already used this link.");
            return Redirect::to('/');
        }
    }

    // resend activation code to user
    public function showResendcode($user_slug = "") {
        // get user details
        $user_details = DB::table('users')
                ->select("first_name", "email_address", "slug", "id")
                ->where('slug', $user_slug)
                ->first();

        if (empty($user_details)) {
            Session::put('error_message', "Something went wrong, please try after some time.");
            return Redirect::to('/');
        }

        // get admin data
        $reset_data = DB::table('reset_link')
                ->where('user_id', $user_details->id)
                ->first();

        if (empty($reset_data)) {
            Session::put('error_message', "Something went wrong, please try after some time.");
            return Redirect::to('/');
        }

        // get reset link
        $reset_link = HTTP_PATH . "activateprofile/" . $reset_data->slug;

        // send email to user
        $mail_data = array(
            'text' => '',
            'firstname' => $user_details->first_name,
            'email_varify' => $user_details->email_address,
            'resetLink' => '<a href="' . $reset_link . '">Click here<a> to activate your account.'
        );

        // return View::make('emails.template')->with($mail_data); // to check mail template data to view
        Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['email_varify'], $mail_data['firstname'])->subject('Account activation link');
                });

        Session::put('success_message', "Please check your email address to activate your account.");
        return Redirect::to('/');
    }

    public function showRestaurant_contact() {
        $this->layout->title = TITLE_FOR_PAGES . 'Request Restaurant Account';
        $this->layout->content = View::make('/Users/caterer_contact');
        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'name' => 'required', // make sure the name field is not empty
                'location' => 'required', // make sure the location field is not empty
                'contact_number' => 'required', // make sure the contact number field is not empty
                'email_address' => 'required|email|unique:users', // make sure the email _address field is not empty
                'password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                // 'message' => 'required', // make sure the message field is not empty
                'city' => 'required',
                'area' => 'required',
                'paypal_email_address' => 'required',
                    //'deliver_to' => 'required',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                $errors_input = $validator->messages()->all();
                $err = implode("<br/>", $errors_input);
                return json_encode(array('message' => $err, 'valid' => false));
            } else {

                if (!empty($input['name'])) {
                    $name = trim($input['name']);
                } else {
                    $name = "N/A";
                }
                if (!empty($input['location'])) {
                    $location = $input['location'];
                } else {
                    $location = "N/A";
                }
                if (!empty($input['contact_number'])) {
                    $contact_number = $input['contact_number'];
                } else {
                    $contact_number = "N/A";
                }
                if (!empty($input['email_address'])) {
                    $email_address = $input['email_address'];
                } else {
                    $email_address = "N/A";
                }
                if (!empty($input['message'])) {
                    $message = $input['message'];
                } else {
                    $message = "N/A";
                }

                // register a user
                $saveUser = array(
                    'first_name' => $name,
                    'email_address' => $email_address,
                    'contact' => $contact_number,
                    'address' => $location,
                    'password' => md5($input['password']),
                    'city' => $input['city'],
                    'area' => $input['area'],
                    'paypal_email_address' => $input['paypal_email_address'],
                    // 'deliver_to' => implode(",", $input['deliver_to']),
                    'slug' => $this->createUniqueSlug($input['name'], 'users'),
                    'user_type' => "Restaurant",
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('users')->insert(
                        $saveUser
                );
                $user_id = DB::getPdo()->lastInsertId();

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

                $dataew = array(
                    'user_id' => $user_id,
                    'is_default_delivery' => '0',
                    'normal' => '',
                    'advance' => "",
                    'pick_up' => '0',
                    'delivery_charge_limit' => "",
                    'created' => date('Y-m-d H:is')
                );
                DB::table('pickup_charges')
                        ->insert($dataew);


                // setup for activation link
                $reset_data = array(
                    'user_id' => $user_id,
                    'status' => '1',
                    'type' => 'signup',
                    'created' => date('Y-m-d H:i:s'),
                    'code' => rand(90786778678, 8978978867857678),
                    'slug' => $this->createUniqueSlug($input['name'], 'reset_link'),
                );
                DB::table('reset_link')->insert(
                        $reset_data
                );
                $reset_link = HTTP_PATH . "activateprofile/" . $reset_data['slug'];

                // send email to user
                $userEmail = $input['email_address'];

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account is successfully created. Your registration is being reviewed and pending acceptance. After acceptance you can use below credentials for login to ' . SITE_TITLE . '.',
                    'email' => $input['email_address'],
                    'password' => $input['password'],
                    'firstname' => $input['name'],
                    'resetLink' => '<a href="' . $reset_link . '">Click here<a> to activate your account.'
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created as Restaurant');
                        });


                // get admin data
                $adminuser = DB::table('admins')
                        ->where('id', 1)
                        ->first();
                $adminEmail = $adminuser->email;

                // send email to administrator
                $mail_data = array(
                    'text' => 'A request for new account has been received on ' . SITE_TITLE . '. Below are the details.',
                    'name' => $name,
                    'location' => $location,
                    'contact_number' => $contact_number,
                    'email_address' => $email_address,
                    //    'message2' => $message,
                    'adminEmail' => $adminEmail,
                    'firstname' => "Admin"
                );

                //   return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($mail_data['adminEmail'], $mail_data['firstname'])->subject('New Account Request on ' . SITE_TITLE . ' for Restaurant');
                        });

                echo json_encode(array('message' => "Congratulation! You are registered successfully, please check your email to activate your account. We are checking your details and will contact you shortly.", 'redirect' => HTTP_PATH, 'valid' => true));
                die;
            }
        }
    }

    public function showCustomersignup() {
        $this->layout->title = TITLE_FOR_PAGES . 'Customer Signup';
        $this->layout->content = View::make('/Users/caterer_contact');
        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'first_name' => 'required', // make sure the name field is not empty
                'last_name' => 'required', // make sure the name field is not empty
                'contact_number' => 'required', // make sure the contact number field is not empty
                'email_address' => 'required|email|unique:users', // make sure the email _address field is not empty
                'city' => 'required',
                'area' => 'required',
                'address' => 'required',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                $errors_input = $validator->messages()->all();
                $err = implode("<br/>", $errors_input);
                return json_encode(array('message' => $err, 'valid' => false));
            } else {

                if (!empty($input['first_name'])) {
                    $first_name = trim($input['first_name']);
                } else {
                    $first_name = "N/A";
                }
                if (!empty($input['last_name'])) {
                    $last_name = trim($input['last_name']);
                } else {
                    $last_name = "N/A";
                }
                if (!empty($input['contact_number'])) {
                    $contact_number = $input['contact_number'];
                } else {
                    $contact_number = "N/A";
                }
                if (!empty($input['address'])) {
                    $address = $input['address'];
                } else {
                    $address = "N/A";
                }
                if (!empty($input['email_address'])) {
                    $email_address = $input['email_address'];
                } else {
                    $email_address = "N/A";
                }

                // register a user
                $saveUser = array(
                    'first_name' => $first_name,
                    'city' => $input['city'],
                    'area' => $input['area'],
                    'last_name' => $last_name,
                    'email_address' => $email_address,
                    'contact' => $contact_number,
                    'address' => $address,
                    'password' => md5($input['password']),
                    'slug' => $this->createUniqueSlug($input['first_name'], 'users'),
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
                    'slug' => $this->createUniqueSlug($input['first_name'], 'reset_link'),
                );
                DB::table('reset_link')->insert(
                        $reset_data
                );
                $reset_link = HTTP_PATH . "activateprofile/" . $reset_data['slug'];

                // send email to user
                $userEmail = $input['email_address'];

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account is successfully created. Kindly complete the registration process by activating your account. After activation you can use below credentials to login to the ' . SITE_TITLE . '.',
                    'email' => $input['email_address'],
                    'password' => $input['password'],
                    'firstname' => $input['first_name'],
                    'resetLink' => '<a href="' . $reset_link . '">Click here<a> to activate your account.'
                );

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

                echo json_encode(array('message' => "Congratulation! You are registered successfully, please check your email to activate your account.", 'redirect' => HTTP_PATH, 'valid' => true));
                die;
            }
        }
    }

    public function showlogin() {
        $this->layout = false;

        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        if (Session::has('user_id')) {
            echo json_encode(array('message' => 'Something went wrong, please try after some time...', 'valid' => false));
            die;
        }

        $input = Input::all();
        if (!empty($input)) {

            $email_address = $input['email'];
            $planPass = $input['password'];
            $password = md5($input['password']);
            $rules = array(
                'email' => 'required|email', // make sure the username field is not empty
                'password' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {
                $errors_input = $validator->messages()->all();
//                echo '<pre>';print_r($errors_input);
                $err = implode("<br/>", $errors_input);
                echo json_encode(array('message' => $err, 'valid' => false));
                die;
            } else {
                //  if($email_address == "mikehudson@flywindies.com"){
//                    $userData = DB::table('users')
//                        ->where('email_address', $email_address)
//                        //->where('password', $password)
//                        ->first();
                // }else{
                // create our user data for the authentication
                $userData = DB::table('users')
                        ->where('email_address', $email_address)
                        ->where('password', $password)
                        ->first();
                // }
                if (!empty($userData)) {
//                    if($userData->id == 55){
//                        
//                    }else{
                    // check activation status

                    if ($userData->user_type == "KitchenStaff" || $userData->user_type == "DeliveryPerson") {
                        // return error message
                        return json_encode(array('message' => 'You are not allowed to login.', 'valid' => false));
                        die;
                    }

                    if ($userData->activation_status == 0) {
                        return json_encode(array('message' => "Your email address is not verified yet. Please check your email for verification link to verify your profile. Can't receive email yet?   <a class='resend-code' href='" . HTTP_PATH . "resendcode/" . ($userData->slug) . "'>Click here</a> to resend email.", 'valid' => false));
                    }

                    // check admin approval
                    if ($userData->approve_status == 0) {
                        return json_encode(array('message' => 'We are checking your details and will contact you shortly once we approve your account', 'valid' => false));
                    }

                    // check activation status
                    if ($userData->status == 0) {
                        return json_encode(array('message' => 'Your account might have been temporarily disabled.', 'valid' => false));
                    }
                    //  }

                    if (isset($input['remember'])) {
                        Session::put('email_address', $email_address); // 30 days
                        Session::put('planPass', $planPass); // 30 days
                        Session::put('remember', '1'); // 30 days
                    } else {
                        Session::put('email_address', ''); // 30 days
                        Session::put('password', ''); // 30 days
                        Session::put('remember', ''); // 30 days
                    }

                    // return to dashboard page
                    Session::put('user_id', $userData->id);


                    $cart = new Cart(new CartSession, new Cookie);
                    $totalCart = $cart->totalItems();

                    if (!empty($totalCart)) {
                        return json_encode(array('message' => 'Login successfull...', 'redirect' => (HTTP_PATH . 'order/confirm'), 'valid' => true));
                    } else {
                        $redirect = Session::get('return');
                        return json_encode(array('message' => 'Login successfull...', 'redirect' => ($redirect ? (HTTP_PATH . $redirect) : HTTP_PATH . 'user/myaccount'), 'valid' => true));
                    }

                    Session::forget('return');
                } else {
                    // return error message
                    return json_encode(array('message' => 'Invalid email or password.', 'valid' => false));
                    die;
                }
            }
        }
    }

    public function showForgotpassword() {

        $this->layout = false;

        if (Session::has('user_id')) {
            echo json_encode(array('message' => 'Something went wrong, please try after some time...', 'valid' => false));
            die;
        }


        $input = Input::all();
        if (!empty($input)) {

            $email_address = $input['email'];
            $rules = array(
                'email' => 'required|email', // make sure the username field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                $errors_input = $validator->messages()->all();
                $err = implode("<br/>", $errors_input);
                echo json_encode(array('message' => $err, 'valid' => false));
                die;
            } else {

                // create our user data for the authentication
                $userData = DB::table('users')
                        ->where('email_address', $email_address)
                        ->first();

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


                    DB::table('users')
                    ->where('id', $user_id)
                    ->update(['forget_password_status' => 1]);

                    // return View::make('emails.template')->with($mail_data); // to check mail template data to view
                    Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Reset Your Password');
                            });

                    echo json_encode(array('message' => 'A link to reset your password was sent to your email address. Please reset your password', 'valid' => true, 'redirect' => HTTP_PATH));
                    die;
                } else {

                    // return error message
                    echo json_encode(array('message' => 'We cannot recognize your email address.', 'valid' => false));
                    die;
                }
            }
        }
    }

    public function showResetPassword($user_id = null, $md_user_id = null) {

        $this->layout->title = TITLE_FOR_PAGES . 'Reset Password';
        $this->layout->content = View::make('/Users/resetPassword');

        if (Session::has('user_id')) {
            return Redirect::to('/user/myaccount');
        }


        $input = Input::all();
        if (!empty($input)) {

            $password = md5($input['password']);
            $rules = array(
                'password' => 'required|min:8', // make sure the password field is not empty
                'cpassword' => 'required', // make sure the confirm password field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);
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
                    Session::put('success_message', "Your password changed successfully. Please login.");
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

    public function showMyaccount() {

        $this->logincheck('user/myaccount');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'My Account';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->select('users.*', "cities.name as city_name", "areas.name as area_name")
                ->where('users.id', $user_id)
                ->leftjoin("cities", 'cities.id', '=', 'users.city')
                ->leftjoin("areas", 'areas.id', '=', 'users.area')
                ->first();
        $this->layout->content = View::make('/Users/myaccount')
                ->with('userData', $userData);
    }

    public function showLogout() {
        Session::forget('user_id');
        Session::put('success_message', "You have successfully logout.");
        return Redirect::to('/');
    }

    public function showEditProfile() {
        $this->logincheck('user/editProfile');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Edit Profile';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $this->layout->content = View::make('/Users/editProfile')
                ->with('userData', $userData);

        $input = Input::all();


        if (!empty($input)) {
            if ($userData->user_type == "Restaurant") {

                $rules = array(
                    'first_name' => 'required', // make sure the first name field is not empty
                    'contact' => 'required', // make sure the  contact field is not empty
                    'address' => 'required', // make sure the address field is not empty
                    'paypal_email_address' => 'required', // make sure the address field is not empty
                );
            } else {
                $rules = array(
                    'first_name' => 'required', // make sure the first name field is not empty
                    'last_name' => 'required', // make sure the last name field is not empty
                    'contact' => 'required', // make sure the  contact field is not empty
                    'address' => 'required', // make sure the address field is not empty
                );
            }

            if ($userData->user_type <> 'Courier') {
                $rules['city'] = "required";
                $rules['area'] = "required";
            }
            if ($userData->user_type == 'Restaurant') {
                // $rules['deliver_to'] = "required";
            }


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/editProfile')
                                ->withErrors($validator);
            } else {

                if ($userData->user_type == "Restaurant") {

                    $data = array(
                        'first_name' => $input['first_name'],
                        //'last_name' => $input['last_name'],
                        'contact' => $input['contact'],
                        'address' => $input['address'],
                        'paypal_email_address' => $input['paypal_email_address'],
                    );
                } else {
                    $data = array(
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'contact' => $input['contact'],
                        'address' => $input['address'],
                    );
                }

                if ($userData->user_type <> 'Courier') {
                    $data['city'] = $input['city'];
                    $data['area'] = $input['area'];
                }
                if ($userData->user_type == 'Restaurant') {
                    // $data['deliver_to'] = implode(",", $input['deliver_to']);
                }
                if ($userData->user_type == 'Courier') {
                    $data['availability'] = isset($input['availability']) ? $input['availability'] : '0';
                }
                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);


                return Redirect::to('/user/myaccount')->with('success_message', 'Profile updated successfully.');
            }
        }
    }

    public function showcreditcard() {
        $this->logincheck('user/creditcardsetup');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Credit card payment gateway setup';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $this->layout->content = View::make('/Users/creditcardsetup')
                ->with('userData', $userData);

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'paypal_username' => 'required', // make sure the first name field is not empty
                'paypal_password' => 'required', // make sure the last name field is not empty
                'paypal_signature' => 'required'
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/creditcardsetup')
                                ->withErrors($validator);
            } else {


                $data = array(
                    'paypal_username' => $input['paypal_username'],
                    'paypal_password' => $input['paypal_password'],
                    'paypal_signature' => $input['paypal_signature']
                );



                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);


                return Redirect::to('/user/myaccount')->with('success_message', 'Credit card payment gateway setup successfully.');
            }
        }
    }

    public function wallet() {
        $this->logincheck('user/wallet');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Wallet';
        $user_id = Session::get('user_id');

        $query = DB::table('wallets');
        $query->where('wallets.user_id', $user_id)
                ->select('wallets.*');
        $records = $query->orderBy('wallets.id', 'desc')->paginate(10);

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $this->layout->content = View::make('/Users/wallet')
                        ->with('userData', $userData)->with('records', $records);

        $input = Input::all();




        if (!empty($input)) {

            $rules = array(
                'amount' => 'required'
            );





            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/wallet')
                                ->withErrors($validator);
            } else {
                $trasid = 'FOOD' . time() . rand(1, 9);
                $data = array(
                    'trans_id' => $trasid,
                    'display_amount' => $input['amount'],
                    'calculated_amount' => '+' . $input['amount'],
                    'status' => 'Pending',
                    'user_id' => $user_id,
                    'type' => "Credit",
                    'created' => date('Y-m-d H:i:s'),
                    'comment' => 'Added In wallet for Order:' . $trasid,
                );

                DB::table('wallets')->insert(
                        $data
                );



                $order_id = DB::getPdo()->lastInsertId();



                return Redirect::to('/user/gopro/' . $order_id);
            }
        }
    }

    public function showgopro($id) {
        $this->logincheck('payment/gopro');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Procced';
        $user_id = Session::get('user_id');
        // $orders = explode('|',$id);

        $shopData = DB::table('wallets')
                ->where('id', $id)
                ->where('user_id', $user_id)
                ->first(); // get cart menu of this order


        $adminData = DB::table('site_settings')
                ->where('id', 1)
                ->first(); // get cart menu of this order



        $total = $shopData->display_amount;
        //echo $total; exit;
        // echo "<pre>"; print_r($shopData); exit;
        // Show the page
        if ($shopData) {
            
        } else {
            return Redirect::to('/');
        }
        $this->layout->content = View::make('Users/showgopro')
                ->with('shopData', $shopData)
                ->with('id', $id)
                ->with('total', $total)
                ->with('paypal_email', $adminData->paypal_email_address);
    }

    public function successcase($slug = null) {

        $this->logincheck('user/successcase');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }


        $this->layout->title = TITLE_FOR_PAGES . 'Payment Success';
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $shopData = DB::table('wallets')
                ->where('user_id', $user_id)
                ->where('id', $slug)
                ->first();
        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();


        if ($shopData->status == "Pending") {


            $orderid = $shopData->id;

            $grandTpotal = $shopData->display_amount;

            $saveList = array(
                'status' => 'Paid', 'paid' => '1'
            );

            DB::table('wallets')
                    ->where('id', $slug)
                    ->update($saveList);

//            $amount = DB::table('wallets')
//                    ->where('wallets.user_id', Session::get('user_id'))
//                    ->where('wallets.paid', 1)
//                    ->sum('wallets.display_amount');
//            // wallet by anand 
            $credit_amount = DB::table('wallets')
                    ->where('wallets.user_id', Session::get('user_id'))
                    ->where('wallets.type', 'Credit')
                    ->where('wallets.paid', 1)
                    ->sum('wallets.display_amount');

            $debit_amount = DB::table('wallets')
                    ->where('wallets.user_id', Session::get('user_id'))
                    ->where('wallets.type', 'Debit')
                    ->where('wallets.paid', 1)
                    ->sum('wallets.display_amount');

            $amount = abs($credit_amount - $debit_amount);
//
//

            $saveListC = array(
                'balance' => $amount
            );

            DB::table('wallets')
                    ->where('id', $slug)
                    ->update($saveListC);

            $customerData = $userData;
            $customerContent = "";
            $customerContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: center; background-color: rgb(108, 158, 22); padding: 7px;" colspan="4">Customer Details</td>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $userData->first_name . ' ' . $userData->last_name . '
                                </td>
                            </tr>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Contact Number: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $userData->contact . '
                                </td>
                            </tr>';

            $customerContent .= '</table>';

            // echo $customerContent; exit;

            $orderContent = "<table>";



            $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word; font-weight:bold;">
                                   You have successfully credit ' . App::make("HomeController")->numberformat($shopData->display_amount, 2) . ' (#' . $shopData->trans_id . ') amount in your wallet.
                                </td>
                            </tr>';

            $orderContent .= '</table>';


            $adminContent = "<table>";
            $adminContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;font-weight:bold;">
                                   ' . $userData->first_name . ' ' . $userData->last_name . ' have successfully credit ' . App::make("HomeController")->numberformat($shopData->display_amount, 2) . ' (#' . $shopData->trans_id . ') amount in his wallet.
                                </td>
                            </tr>';

            $adminContent .= '</table>';
//               echo $catererData->email_address;
            //echo $adminContent; exit;


            /*             * * send mail to customer ** */
            $mail_data = array(
                'text' => "",
                'orderContent' => $orderContent,
                'mailSubjectCustomer' => App::make("HomeController")->numberformat($shopData->display_amount, 2) . ' credit in your wallet',
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
//
//              


            /*             * * send mail to admin ** */

            $admin_mail_data = array(
                'text' => 'Order placed successfully on ' . SITE_TITLE,
                'customerContent' => $customerContent,
                'orderContent' => $adminContent,
                'mailSubjectAdmin' => $userData->first_name . ' ' . $userData->last_name . ' has credit ' . App::make("HomeController")->numberformat($shopData->display_amount, 2) . ' amount in his wallet',
                'sender_email' => $adminuser->email,
                'firstname' => "Admin",
            );

            //   return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

            Mail::send('emails.template', $admin_mail_data, function($message) use ($admin_mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($admin_mail_data['sender_email'], 'Admin')->subject($admin_mail_data['mailSubjectAdmin']);
                    });
        }

        return Redirect::to('user/wallet')->with('success_message', 'You have successfully added money in your wallet.')
                        ->with('shopData', $shopData);
    }

    public function cancelcase($id) {
        $this->logincheck('user/cancelcase');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }


        $this->layout->title = TITLE_FOR_PAGES . 'Cancel';
        $user_id = Session::get('user_id');
        $shopData = DB::table('wallets')
                ->where('id', $id)
                ->where('user_id', $user_id)
                ->delete(); // get cart menu of this order


        return Redirect::to('user/myaccount/')->with('error_message', 'Your Payment is cancelled.');
    }

    public function showchangePassword() {
        $this->logincheck('user/changePassword');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $this->layout->title = TITLE_FOR_PAGES . 'Change Password';
        $this->layout->content = View::make('/Users/changePassword')
                ->with('userData', $userData);
        $input = Input::all();


        if (!empty($input)) {
            $new_password = $input['new_password'];
            $confirm_password = $input['confirm_password'];
            $rules = array(
                'new_password' => 'required|min:8', // make sure the new password field is not empty
                'confirm_password' => 'required' // make sure the confirm password field is not empty
            );

            $oldDBpassword = $userData->password;

            $opassword = md5($input['old_password']);

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/changePassword')
                                ->withErrors($validator);
            } else {

                $newPassword = md5($new_password);


                // check new password with old password
                if ($oldDBpassword == $newPassword) {

                    // return error message
                    Session::put('error_message', "Please do not enter new password same as old password");
                    return Redirect::to('/user/changePassword');
                }
                if ($oldDBpassword <> $opassword) {

                    // return error message
                    Session::put('error_message', "Please enter correct old password");
                    return Redirect::to('/user/changePassword');
                } else {

                    $data = array(
                        'password' => $newPassword,
                    );
                    DB::table('users')
                            ->where('id', $user_id)
                            ->update($data);


                    return Redirect::to('/user/myaccount')->with('success_message', 'Password changed successfully.');
                }
            }
        }
    }

    public function showOpeninghours() {
        $this->logincheck('user/openinghours');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get opening hours details
        $opening_hours = DB::table('opening_hours')
                ->where('user_id', $user_id)
                ->first();


        $this->layout->title = TITLE_FOR_PAGES . 'Manage Opening Hours';
        $this->layout->content = View::make('/Users/openinghours')
                ->with('userData', $userData)
                ->with('opening_hours', $opening_hours);

        $input = Input::all();


        if (!empty($input)) {


            $rules = array(
//                'open_days' => 'required',
//                'start_time' => 'required',
//                'end_time' => 'required',
                'minimum_order' => 'required',
                'catering_type' => 'required',
                'average_time' => 'required',
                'estimated_cost' => 'required'
            );
            $messages = array('catering_type.required' => 'Meal type field is required.');
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules, $messages);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/openinghours')
                                ->withErrors($validator);
            } else {

                if (isset($input['open_days'])) {
                    $open_days = $input['open_days'];
                    $open_days = implode(',', $open_days);

                    foreach ($input['open_days'] as $varr) {
                        $open[] = date("H:i:s", strtotime($input['start_time'][$varr]));
                        $close[] = date("H:i:s", strtotime($input['end_time'][$varr]));
                    }

//                 echo "<pre>"; print_r($open); 
//                 echo "<pre>"; print_r($close); exit;

                    $data = array(
                        'open_days' => $open_days,
                        'start_time' => implode(',', $open),
                        'end_time' => implode(',', $close),
                        'minimum_order' => $input['minimum_order'],
                        'average_time' => $input['average_time'],
                        'estimated_cost' => $input['estimated_cost'],
                        'catering_type' => implode(",", $input['catering_type']),
                        'open_close' => isset($input['open_close']) ? $input['open_close'] : '0'
                    );
                } else {
                    $data = array(
                        'minimum_order' => $input['minimum_order'],
                        'average_time' => $input['average_time'],
                        'estimated_cost' => $input['estimated_cost'],
                        'catering_type' => implode(",", $input['catering_type']),
                        'open_close' => isset($input['open_close']) ? $input['open_close'] : '0'
                    );
                }




                //echo "<pre>"; print_r($data); exit;

                DB::table('opening_hours')
                        ->where('id', $opening_hours->id)
                        ->update($data);

                return Redirect::to('/user/myaccount')->with('success_message', 'Opening hours successfully updated.');
            }
        }
    }

    public function deliverycharges() {
        $this->logincheck('user/deliverycharges');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {

            return Redirect::to('/');
        }

        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $charges = DB::table('pickup_charges')
                ->where('user_id', $user_id)
                ->first();

//        echo '<pre>';print_r($user_id);die;
//        
        if (empty($charges)) {
            $data = array(
                'user_id' => $user_id,
                'is_default_delivery' => '0',
                'pick_up' => '0',
                'normal' => '',
                'advance' => "",
                'delivery_charge_limit' => "",
                'created' => date('Y-m-d H:i:s')
            );
            DB::table('pickup_charges')
                    ->insert($data);
            $charges = DB::table('pickup_charges')
                    ->where('user_id', $user_id)
                    ->first();
        }

        // get opening hours details

        $this->layout->title = TITLE_FOR_PAGES . 'Manage Delivery charges';
        $this->layout->content = View::make('/Users/deliverycharges')
                ->with('userData', $userData)
                ->with('detail', $charges);

        $input = Input::all();


        if (!empty($input)) {

            // set validatin rules
            $rules = array(
                'normal' => 'required',
                'advance' => 'required',
                'delivery_charge_limit' => 'required',
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/user/deliverycharges')
                                ->withErrors($validator)->withInput(Input::all());
            } else {
//                echo "<pre>";
//                print_r($input);
//                exit;
                // update admin profile
                $data = array(
                    'normal' => $input['normal'],
                    'advance' => $input['advance'],
                    'delivery_charge_limit' => $input['delivery_charge_limit'],
                    'is_default_delivery' => $input['is_default_delivery'],
                    'pick_up' => $input['pick_up'],
                );
//                print_r($data); exit;

                DB::table('pickup_charges')
                        ->where('id', $charges->id)
                        ->update($data);


                Session::put('success_message', "Information has been successfully updated.");
                return Redirect::to('/user/deliverycharges');
            }
        }
    }

    public function showManagemenu() {
        $this->logincheck('user/managemenu');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get my all menu        
        $query = DB::table('menu_item');
        $query->where('menu_item.user_id', $user_id)->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                ->select('menu_item.*', 'cuisines.name');
        $records = $query->orderBy('menu_item.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Manage Menu';
        $this->layout->content = View::make('/Users/managemenu')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showorderstatus() {
        $this->logincheck('user/showorderstatus');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get my all menu        
        $query = DB::table('orderstatus');
        $query->where('orderstatus.user_id', $user_id)
                ->select('orderstatus.*');
        $records = $query->orderBy('orderstatus.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Additional Order Stauts';
        $this->layout->content = View::make('/Users/orderstatus')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showAddmenu() {
        $this->logincheck('user/addmenu');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
            ;
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Add Menu';
        $this->layout->content = View::make('/Users/addmenu')
                ->with('userData', $userData);

        if (!empty($input)) {

//            Validator::extend('img_min_size', function($attribute, $value, $parameters) {
//                        $file = Request::file($attribute);
//                        $image_info = getimagesize($file);
//                        $image_width = $image_info[0];
//                        $image_height = $image_info[1];
//                        if ((isset($parameters[0]) && $parameters[0] != 0) && $image_width < $parameters[0])
//                            return false;
//                        if ((isset($parameters[1]) && $parameters[1] != 0) && $image_height < $parameters[1])
//                            return false;
//                        return true;
//                    });

            $rules = array(
                'cuisine' => 'required',
                'item_name' => 'required',
                'price' => 'required',
                'preparation_time' => 'required',
                'image' => 'mimes:jpeg,png,jpg',
            );
            //echo "<pre>"; print_r($input); exit;
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/addmenu')
                                ->withErrors($validator);
            } else {

                $data = array(
                    'cuisines_id' => $input['cuisine'],
                    'item_name' => $input['item_name'],
                    'preparation_time' => $input['preparation_time'],
                    'description' => $input['description'],
                    'price' => $input['price'],
                    // 'submenu' => $input['submenu'],
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

                if (isset($input['addon_name']) && count($input['addon_name']) > 0) {
                    foreach ($input['addon_name'] as $node => $addon) {
                        $data = array(
                            'user_id' => $user_id,
                            'menu_id' => $id,
                            'addon_name' => $addon,
                            'addon_price' => $input['addon_price'][$node],
                            'slug' => 'addon-' . time() . rand(10, 99),
                            'created' => date('Y-m-d H:i:s')
                        );
                        DB::table('addons')
                                ->insert($data);
                    }
                }
                if (isset($input['variant_name']) && count($input['variant_name']) > 0) {
                    foreach ($input['variant_name'] as $node => $priceon) {
                        $data = array(
                            'user_id' => $user_id,
                            'menu_id' => $id,
                            'name' => $priceon,
                            'price' => $input['variant_price'][$node],
                            'slug' => 'variant-' . time() . rand(10, 99),
                            'created' => date('Y-m-d H:i:s')
                        );
                        DB::table('variants')
                                ->insert($data);
                    }
                }


                return Redirect::to('/user/managemenu')->with('success_message', 'Menu item successfully added.');
            }
        }
    }

    public function showaddstatus() {
        $this->logincheck('user/addstatus');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
            ;
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Add Status';
        $this->layout->content = View::make('/Users/addstatus')
                ->with('userData', $userData);

        if (!empty($input)) {

//            Validator::extend('img_min_size', function($attribute, $value, $parameters) {
//                        $file = Request::file($attribute);
//                        $image_info = getimagesize($file);
//                        $image_width = $image_info[0];
//                        $image_height = $image_info[1];
//                        if ((isset($parameters[0]) && $parameters[0] != 0) && $image_width < $parameters[0])
//                            return false;
//                        if ((isset($parameters[1]) && $parameters[1] != 0) && $image_height < $parameters[1])
//                            return false;
//                        return true;
//                    });

            $rules = array(
                'status_name' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/addstatus')
                                ->withErrors($validator);
            } else {

                $data = array(
                    'status_name' => $input['status_name'],
                    // 'submenu' => $input['submenu'],
                    'user_id' => $user_id,
                    'created' => date('Y-m-d H:i:s'),
                    'status' => '1',
                    'slug' => $this->createSlug($input['status_name'])
                );



                DB::table('orderstatus')->insert(
                        $data
                );

                return Redirect::to('/user/orderstatus')->with('success_message', 'Status successfully added.');
            }
        }
    }

    public function showEditmenu($slug = "") {

        $this->logincheck('user/editmenu/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
            ;
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get menu item details
        $menudata = DB::table('menu_item')
                ->where('slug', $slug)
                ->first();
        if (empty($menudata)) {
            // redirect to the menu page
            return Redirect::to('/user/managemenu')->with('error_message', 'Something went wrong, please try after some time.');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Edit Menu';
        $this->layout->content = View::make('/Users/editmenu')
                ->with('userData', $userData)
                ->with('menudata', $menudata);
        $input = Input::all();


        if (!empty($input)) {
            //  echo "<pre>"; print_r($input); exit;

            $rules = array(
                'cuisine' => 'required',
                'item_name' => 'required',
                'price' => 'required',
                'preparation_time' => 'required',
                'image' => 'mimes:jpeg,png,jpg',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);
            //echo "<pre>"; print_r($input); exit;
            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/editmenu/' . $slug)
                                ->withErrors($validator);
            } else {

                $data = array(
                    'cuisines_id' => $input['cuisine'],
                    'item_name' => $input['item_name'],
                    'price' => $input['price'],
                    'status' => 1,
                    // 'submenu' => $input['submenu'],
                    'preparation_time' => $input['preparation_time'],
                    'description' => $input['description'],
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


                if (Input::hasFile('image')) {
                    $file = Input::file('image');
                    $image = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_FULL_ITEM_IMAGE_PATH, time() . $file->getClientOriginalName());
                    @unlink(UPLOAD_FULL_ITEM_IMAGE_PATH . $menudata->image);
                } else {
                    $image = $input['old_image'];
                }
                $data['image'] = $image;

                if (isset($input['addon_name']) && count($input['addon_name']) > 0) {
                    foreach ($input['addon_name'] as $node => $addon) {

                        $dataC = array(
                            'user_id' => $user_id,
                            'menu_id' => $menudata->id,
                            'addon_name' => $addon,
                            'addon_price' => $input['addon_price'][$node],
                            'slug' => 'addon-' . time() . rand(10, 99),
                            'created' => date('Y-m-d H:i:s')
                        );
                        //echo $input['id'][$node]; exit;
                        if (isset($input['id'][$node])) {

                            DB::table('addons')
                                    ->where('id', $input['id'][$node])
                                    ->update($dataC);
                        } else {
                            //echo "<pre>"; print_r($data); exit;
                            DB::table('addons')
                                    ->insert($dataC);
                        }
                    }
                }
                if (isset($input['variant_name']) && count($input['variant_name']) > 0) {
                    foreach ($input['variant_name'] as $node => $addon) {

                        $dataCV = array(
                            'user_id' => $user_id,
                            'menu_id' => $menudata->id,
                            'name' => $addon,
                            'price' => $input['variant_price'][$node],
                            'slug' => 'variant-' . time() . rand(10, 99),
                            'created' => date('Y-m-d H:i:s')
                        );
                        //echo $input['id'][$node]; exit;
                        if (isset($input['vid'][$node])) {

                            DB::table('variants')
                                    ->where('id', $input['vid'][$node])
                                    ->update($dataCV);
                        } else {
                            //echo "<pre>"; print_r($data); exit;
                            DB::table('variants')
                                    ->insert($dataCV);
                        }
                    }
                }

                $parentv = DB::table('variants')
                        ->where('menu_id', $menudata->id)
                        ->where('parent', 1)
                        ->first();
                if ($parentv) {

                    /* Parent Variant */
                    $parentvariantdata = array(
                        'user_id' => $user_id,
                        'menu_id' => $menudata->id,
                        'name' => $input['item_name'],
                        'price' => $input['price'],
                        'slug' => 'variant-' . time() . rand(10, 99),
                        'status' => 0,
                        'parent' => 1,
                        'created' => date('Y-m-d H:i:s')
                    );
                    /* Parent Variant */
                    DB::table('variants')
                            ->where('id', $parentv->id)
                            ->update($parentvariantdata);
                } else {
                    /* Parent Variant */
                    $parentvariantdata = array(
                        'user_id' => $user_id,
                        'menu_id' => $menudata->id,
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
                }

                DB::table('menu_item')
                        ->where('slug', $slug)
                        ->update($data);





                return Redirect::to('/user/managemenu')->with('success_message', 'Menu item successfully updated.');
            }
        }
    }

    public function showeditstatus($slug = "") {

        $this->logincheck('user/editstatus/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
            ;
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get menu item details
        $menudata = DB::table('orderstatus')
                ->where('slug', $slug)
                ->first();
        if (empty($menudata)) {
            // redirect to the menu page
            return Redirect::to('/user/orderstatus')->with('error_message', 'Something went wrong, please try after some time.');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Edit Status';
        $this->layout->content = View::make('/Users/editstatus')
                ->with('userData', $userData)
                ->with('menudata', $menudata);
        $input = Input::all();


        if (!empty($input)) {
            //echo "<pre>"; print_r($input); exit;

            $rules = array(
                'status_name' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/editstatus/' . $slug)
                                ->withErrors($validator);
            } else {

                $data = array(
                    'status_name' => $input['status_name'],
                    'slug' => $this->createSlug($input['status_name'])
                );



                DB::table('orderstatus')
                        ->where('slug', $slug)
                        ->update($data);

                return Redirect::to('/user/orderstatus')->with('success_message', 'Status successfully updated.');
            }
        }
    }

    public function showDeletemenu($slug = null) {
        // get menu item details
        $menudata = DB::table('menu_item')
                ->where('slug', $slug)
                ->first();
        if (empty($menudata)) {
            // delete image
            @unlink(UPLOAD_FULL_ITEM_IMAGE_PATH . $menudata->image);
        }
        DB::table('menu_item')->where('slug', $slug)->delete();
        return Redirect::to('/user/managemenu')->with('success_message', 'Menu item deleted successfully');
    }

    public function deleteActionMenu($type, $id) {
        // get menu item details
        if ($type != "") {
            if ($type == "variant") {
                DB::table('variants')->where('id', $id)->delete();
            } else {
                DB::table('addons')->where('id', $id)->delete();
            }
        }
    }

// --------------------------------------------------------------------
// Create slug for secure URL
    function createSlug($string) {
        $string = substr(strtolower($string), 0, 35);
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $return = strtolower(preg_replace($old_pattern, $new_pattern, $string)) . rand(111111, 9999999) . time();
        return $return;
    }

    public function showchangePicture() {
        $this->logincheck('user/changePicture');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
            ;
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Change Picture';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();
        $this->layout->content = View::make('/Users/changePicture')
                ->with('userData', $userData);

        if (!empty($input)) {
            if (Input::hasFile('profile_image'))
                $rules = array(
                    'profile_image' => 'required|mimes:jpeg,png,jpg',
                );
            else
                $rules = array(
                    'profile_image' => 'required',
                );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/changePicture')
                                ->withErrors($validator);
            } else {

                include("vendor/ImageManipulator.php");
                if (Input::hasFile('profile_image')) {
                    $file = Input::file('profile_image');
                    $profileImageName = time() . $file->getClientOriginalName();
                    $file->move(TEMP_PATH, time() . $file->getClientOriginalName());

                    list($width, $height, $type, $attr) = getimagesize('uploads/temp/' . $profileImageName);
                    if ($width > 600) {
                        $manipulator = new ImageManipulator('uploads/temp/' . $profileImageName);

                        // resizing to 200x200
                        $manipulator->resample(600, 600);
                        $manipulator->save('uploads/temp/' . $profileImageName);
                    }

                    $data['image'] = $profileImageName;
                    if ($width > $height) {
                        $data['width'] = $height;
                        $data['height'] = $height;
                    } elseif ($width < $height) {
                        $data['width'] = $width;
                        $data['height'] = $width;
                    } else {
                        $data['width'] = $width;
                        $data['height'] = $height;
                    }
                    $this->layout->content = View::make('/Users/changePicture')
                            ->with('userData', $userData)
                            ->with('data', $data);
                }

                if (isset($input['add_photo'])) {

                    $manipulator = new ImageManipulator('uploads/temp/' . $input['profile_image']);
                    $width = $manipulator->getWidth();
                    $height = $manipulator->getHeight();
                    $centreX = round($width / 2);
                    $centreY = round($height / 2);
                    // our dimensions will be 200x130
                    $x1 = $centreX - $input['w'] / 2; // 200 / 2
                    $y1 = $centreY - $input['h'] / 2; // 130 / 2

                    $x2 = $centreX + 100; // 200 / 2
                    $y2 = $centreY + 65; // 130 / 2
                    //
//                    echo "<pre>";
//                    print_r($input);
                    // center cropping to 200x130
                    $newImage = $manipulator->crop($input['x'], $input['y'], $input['w'], $input['h']);

                    // saving file to uploads folder
                    $manipulator->save("uploads/users/" . $input['profile_image']);

                    // update it to database
                    $data = array(
                        'profile_image' => $input['profile_image'],
                    );
                    DB::table('users')
                            ->where('id', $user_id)
                            ->update($data);

                    // remove old image
                    @unlink(UPLOAD_FULL_PROFILE_IMAGE_PATH . $userData->image);

                    // return to error/success message
                    return Redirect::to('/user/myaccount')->with('success_message', 'Image updated successfully.');
                }
            }
        }
    }

    public function showDeleteUserImage() {

        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        DB::table('users')
                ->where('id', $user_id)
                ->update(array('profile_image' => ''));
        @unlink(UPLOAD_FULL_PROFILE_IMAGE_PATH . $userData->profile_image);

        return Redirect::to('/user/changePicture')->with('success_message', 'Image deleted successfully');
    }

    public function showMyfavourite() {

        if (isset($_COOKIE["browser_session_id"]) && $_COOKIE["browser_session_id"] != '') {
            $browser_session_id = $_COOKIE["browser_session_id"];
        } else {
            $browser_session_id = session_id();
            setcookie("browser_session_id", $browser_session_id, time() + 60 * 60 * 24 * 7, "/");
        }


        $this->logincheck('user/myfavourite');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get my all addresses        

        $query = DB::table('favorite_menu');
        $query->leftjoin("menu_item", 'menu_item.id', '=', 'favorite_menu.menu_id');
        $query->leftjoin("users", 'users.id', '=', 'favorite_menu.caterer_id');
        $query->where('favorite_menu.user_id', $user_id)
                ->orwhere('favorite_menu.session_id', $browser_session_id)
                ->select('favorite_menu.*', 'menu_item.id as menu_id', 'menu_item.item_name', 'users.slug as user_slug');
        $records = $query->orderBy('favorite_menu.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'My Favorite';
        $this->layout->content = View::make('/Users/myfavourite')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showDeletefav($slug = null) {
        // get menu item details
        $menudata = DB::table('favorite_menu')
                ->where('slug', $slug)
                ->first();
        DB::table('favorite_menu')->where('slug', $slug)->delete();
        return Redirect::to('/user/myfavourite')->with('success_message', 'Favorite Menu item deleted successfully');
    }

    public function showMakefav($slug = null, $mainorder = null) {
        // get menu item details

        DB::table('orders')
                ->where('slug', $slug)
                ->update(array('is_favorite' => 1));
        return Redirect::to('/order/myorders/')->with('success_message', 'Your order is successfully added in favourite list.');
    }

    public function showRemovefav($slug = null, $mainorder = null) {
        // get menu item details
        DB::table('orders')
                ->where('slug', $slug)
                ->update(array('is_favorite' => 0));
        return Redirect::to('/order/myorders/' . $mainorder)->with('success_message', 'Order remove from favourite list successfully');
    }

    public function contactcaterer() {


        $this->layout = false;

        $input = Input::all();
        if (!empty($input)) {

            $message = $input['message'];
            $order_id = $input['order_id'];
            $rules = array(
                'message' => 'required', // make sure the message field is not empty
                'order_id' => 'required', // make sure the message field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                $errors_input = $validator->messages()->all();
                $err = implode("<br/>", $errors_input);
                echo json_encode(array('message' => $err, 'valid' => false));
                die;
            } else {

                $user_id = Session::get('user_id');
                // create our user data for the authentication
                $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->first();
                $orderData = DB::table('orders')
                        ->where('id', $order_id)
                        ->first();
                $catererId = $orderData->caterer_id;
                $catererData = DB::table('users')
                        ->where('id', $catererId)
                        ->first();


                if (!empty($catererData)) {
                    // send email to user
                    $mail_data = array(
                        'text' => 'Contact query from customer ' . $userData->first_name . ' ' . $userData->last_name . ' regarding order number ' . $orderData->order_number . '.',
                        'email' => $catererData->email_address,
                        'message2' => $message,
                        'firstname' => $catererData->first_name . ' ' . $catererData->last_name,
                    );
                    //   return View::make('emails.template')->with($mail_data); // to check mail template data to view
                    Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Contact query from customer');
                            });


                    // send email to admin
                    $adminuser = DB::table('admins')
                            ->where('id', 1)
                            ->first();
                    $adminEmail = $adminuser->email;
                    $mail_data = array(
                        'text' => 'Contact query from customer ' . $userData->first_name . ' ' . $userData->last_name . ' of regarding order number ' . $orderData->order_number . '.',
                        'email' => $adminEmail,
                        'message2' => $message,
                        'firstname' => "Admin",
                    );
                    //   return View::make('emails.template')->with($mail_data); // to check mail template data to view
                    Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Contact query from customer');
                            });
                    echo json_encode(array('message' => 'Thank you for contacting us. We will get back to you shortly', 'valid' => true, 'redirect' => HTTP_PATH));
                    die;
                }
            }
        }
    }

    public function showcouponcodes() {
        $this->logincheck('user/showcouponcodes');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get my all menu        
        $query = DB::table('coupons');
        $query->where('coupons.user_id', $user_id)
                ->select('coupons.*');
        $records = $query->orderBy('coupons.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Manage Coupon codes';
        $this->layout->content = View::make('/Users/coupons')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showaddcouponcode() {
        $this->logincheck('user/showaddcouponcode');

        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'code' => 'required|unique:coupons',
                'discount' => 'required',
                'coupon_image' => 'mimes:jpeg,png,jpg',
                'start_time' => 'required',
                'end_time' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/addcouponcode')->withErrors($validator)->withInput(Input::all());
            } else {

                if (Input::hasFile('coupon_image')) {
                    $file = Input::file('coupon_image');
                    $imageName = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_FULL_COUPON_IMAGE_PATH, time() . $file->getClientOriginalName());
                } else {
                    $imageName = "";
                }
                $slug = $this->createUniqueSlug($input['code'], 'coupons');
                $saveUser = array(
                    'status' => '1',
                    'code' => $input['code'],
                    'start_time' => $input['start_time'],
                    'user_id' => $user_id,
                    'discount' => $input['discount'],
                    'end_time' => $input['end_time'],
                    'coupon_image' => $imageName,
                    'slug' => $slug,
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('coupons')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();

                return Redirect::to('/user/couponcodes')->with('success_message', 'Coupon saved successfully.');
            }
        } else {

            $this->layout->title = TITLE_FOR_PAGES . 'Add Coupon code';
            $this->layout->content = View::make('/Users/add_coupon')
                    ->with('userData', $userData);
        }
    }

    public function showcoupon_active($slug = null) {
        if (!empty($slug)) {
            DB::table('coupons')
            ->where('slug', $slug)
            ->update(['status' => 1, 'status' => 1]);

            return Redirect::back()->with('success_message', 'Coupon(s) activated successfully');
        }
    }

    public function showcoupon_deactive($slug = null) {
        if (!empty($slug)) {
            DB::table('coupons')
            ->where('slug', $slug)
            ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Coupon(s) deactivated successfully');
        }
    }

    public function showcoupon_delete($slug = null) {
        if (!empty($slug)) {
            DB::table('coupons')->where('slug', $slug)->delete();
            return Redirect::to('/user/couponcodes')->with('success_message', 'Coupon deleted successfully');
        }
    }

    public function showupgrade() {
        $this->logincheck('user/showupgrade');

        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $packages = DB::table('sponsorship')
                ->get();

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        // print_r($userData);
        if ($userData->featured > 0) {
            //exit;
            if (strtotime($userData->expiry_date) > time()) {

                return Redirect::to('user/myaccount/')->with('error_message', 'You cannot purchase multiple plans at the same time.');
            }
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Upgrade to featured';
        $this->layout->content = View::make('/Users/featured')
                ->with('userData', $userData)
                ->with('packages', $packages);
    }

    public function Showprocced($slug) {
        $this->logincheck('user/Showprocced');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Proceed to payment';
        $user_id = Session::get('user_id');


        $package = DB::table('sponsorship')
                ->where('slug', $slug)
                ->first();
        if (!$package) {
            return Redirect::to('/');
        }
        $total = $package->price;
        $transactionId = "Pay-" . time() . rand(1, 9);



        if ($total == 0) {
            $saveUser = array(
                'transaction_id' => $transactionId,
                //'last_name' => $input['last_name'],
                'user_id' => $user_id,
                'price' => $total,
                'package' => $package->id,
                'slug' => "Pay-" . time(),
                'type' => "Sponsorship",
                'status' => "Pending",
                'created' => date('Y-m-d'),
            );
            DB::table('payments')->insert(
                    $saveUser
            );

            $id = DB::getPdo()->lastInsertId();
            return Redirect::to('/user/success/' . $id);
        } else {
            $saveUser = array(
                'transaction_id' => $transactionId,
                //'last_name' => $input['last_name'],
                'user_id' => $user_id,
                'price' => $total,
                'package' => $package->id,
                'slug' => "Pay-" . time(),
                'type' => "Sponsorship",
                'status' => "Pending",
                'created' => date('Y-m-d'),
            );
            DB::table('payments')->insert(
                    $saveUser
            );

            $id = DB::getPdo()->lastInsertId();
            return Redirect::to('/user/proceedtopay/' . $id);
        }
    }

    public function proceedtopay($id) {
        $this->logincheck('user/proceedtopay');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Proceed to payment';
        $user_id = Session::get('user_id');
        // $orders = explode('|',$id);

        $shopData = DB::table('payments')
                ->where('id', $id)
                ->where('user_id', $user_id)
                ->first(); // get cart menu of this order
        // print_r($shopData); exit;

        $catererData = DB::table('users')
                ->where('id', $user_id)
                ->first();


        $grandTpotal = 0;
        $orderid = array();

        $total = $shopData->price;
        //echo $total; exit;
        // echo "<pre>"; print_r($shopData); exit;
        // Show the page
        if ($shopData) {
            
        } else {
            return Redirect::to('/');
        }
        $this->layout->content = View::make('Users/proceedtopay')
                ->with('shopData', $shopData)
                ->with('id', $id)
                ->with('total', $total)
                ->with('paypal_email', PAYPAL_EMAIL);
//        if ($shopData->confirm_shop != '1') {
//            return Redirect::to('payment/confirmmail/' . $shopData->slug)->with('error_message', 'You must need to confirm your mail. Please check your email or click on resend to send confirmation link.')
//                            ->with('shopData', $shopData);
//        }
    }

    public function cancel($id) {

        $this->logincheck('user/cancel/' . $id);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }


        $this->layout->title = TITLE_FOR_PAGES . 'Cancel';
        $user_id = Session::get('user_id');



        $shopData = DB::table('payments')
                ->where('id', $id)
                ->where('user_id', $user_id)
                ->delete(); // get cart menu of this order




        return Redirect::to('user/myaccount/')->with('error_message', 'Your Payment is cancelled.');
    }

    public function success($slug = null) {
        //exit;


        $this->logincheck('user/success');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }


        $this->layout->title = TITLE_FOR_PAGES . 'Payment Success';
        $user_id = Session::get('user_id');


        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();


        $shopData = DB::table('payments')
                ->where('user_id', $user_id)
                ->where('id', $slug)
                ->first();

        $package = DB::table('sponsorship')
                ->where('id', $shopData->package)
                ->first();



        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();


        $price = 0;

        $grandTpotal = 0;
        $orderid = $shopData->id;

        if ($shopData->status == "Pending") {

            DB::table('payments')
            ->where('id', $shopData->id)
            ->update(['status' => 'Complete']);


            $dateofexp = strtotime('+' . $package->no_of_days . ' days', time());
            //echo date('Y-m-d h:i:s',$dateofexp); exit;
            $saveList = array('expiry_date' => date('Y-m-d h:i:s', $dateofexp), 'featured' => 1, 'plan_id' => $shopData->package);

            // print_r($saveList); exit;
            DB::table('users')
                    ->where('id', $shopData->user_id)
                    ->update($saveList);
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

            /**             * send mail to caterer ** */
            $caterer_mail_data = array(
                'text' => '',
                'orderContent' => $mailtocustomer,
                'mailSubjectRestaurant' => $mailSubjectRestaurant,
                'sender_email' => $catererData->email_address,
                'firstname' => '',
            );

            // return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

            Mail::send('emails.template', $caterer_mail_data, function($message) use ($caterer_mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($caterer_mail_data['sender_email'], $caterer_mail_data['firstname'])->subject($caterer_mail_data['mailSubjectRestaurant']);
                    });

//                 
//                 
//              


            /*             * * send mail to admin ** */

            $admin_mail_data = array(
                'text' => '',
                'customerContent' => $customerContent,
                'orderContent' => $mailtoadmin,
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



            // exit;
        }
        return Redirect::to('user/myaccount/')->with('success_message', 'Your payment is successfully complete. Your restaurant will now appear in the featured listing.')
                        ->with('shopData', $shopData);
    }

    public function notify($paymentNumber = null) {




        if (!empty($_REQUEST) && !empty($_REQUEST['item_number']) && $paymentNumber != '') {
            //if(1){

            if (isset($_REQUEST['txn_id'])) {
                $transactionId = $_REQUEST['txn_id'];
                $amountPaid = $_REQUEST['mc_gross'];
            } elseif ($_REQUEST['tx']) {
                $transactionId = $_REQUEST['tx'];
                $amountPaid = $_REQUEST['amt'];
            }

            $st = $_REQUEST['st'];

            $shopData = DB::table('orders')
                    ->where('id', $slug)
                    ->first();

            $user_id = $shopData->user_id;
            if ($transactionId) {








                $shopData = DB::table('payments')
                        ->where('id', $slug)
                        ->first();

                $user_id = $shopData->user_id;
                $package = DB::table('sponsorship')
                        ->where('id', $shopData->package)
                        ->first();


                $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->first();

                $adminuser = DB::table('admins')
                        ->where('id', '1')
                        ->first();


                $price = 0;

                $grandTpotal = 0;
                $orderid = $shopData->id;

                if ($shopData->status == "Pending") {

                    DB::table('payments')
                    ->where('id', $shopData->id)
                    ->update(['status' => 'Complete']);


                    $dateofexp = strtotime('+' . $package->no_of_days . ' days', time());
                    //echo date('Y-m-d h:i:s',$dateofexp); exit;
                    $saveList = array('expiry_date' => date('Y-m-d h:i:s', $dateofexp), 'featured' => 1);
                    DB::table('users')
                            ->where('id', $shopData->user_id)
                            ->update($saveList);


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

                    /**                     * send mail to caterer ** */
                    $caterer_mail_data = array(
                        'text' => 'Order placed successfully on ' . SITE_TITLE,
                        'orderContent' => $mailtocustomer,
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

//                 
//                 
//              


                    /*                     * * send mail to admin ** */

                    $admin_mail_data = array(
                        'text' => 'Order placed successfully on ' . SITE_TITLE,
                        'customerContent' => $customerContent,
                        'orderContent' => $mailtoadmin,
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
                }
            }
        }

        exit;
    }

    public function paymenthistory($slug) {
        $this->logincheck('user/paymenthistory');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }


        $this->layout->title = TITLE_FOR_PAGES . 'Sponsorship History';

        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();
        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }

        $query = Payment::sortable()
                ->where(function ($query) use ($search_keyword) {
                    $query->where('transaction_id', 'LIKE', '%' . $search_keyword . '%');
                });


        $separator = implode("/", $separator);

        $user_id = Session::get('user_id');
        if ($slug == "purchase") {
            $query->where('type', '=', 'Purchase');
        } else {
            $query->where('type', '=', 'Sponsorship');
        }

        $query->where('user_id', '=', $user_id);
        // Get all the users
        $payments = $query->orderBy('id', 'desc')->sortable()->paginate(10);

        // Show the page

        $this->layout->content = View::make('Users/paymenthistory', compact('payments'))->with('search_keyword', $search_keyword)
                ->with('searchByDateFrom', $searchByDateFrom)
                ->with('searchByDateTo', $searchByDateTo)
                ->with('paymentslug', $slug);
    }

    public function receivedpayment() {
        $this->logincheck('user/receivedpayment');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        $slug = "purchase";


        $this->layout->title = TITLE_FOR_PAGES . 'Received Payment History';

        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();
        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }

        $query = Payment::sortable()
                ->where(function ($query) use ($search_keyword) {
                    $query->where('transaction_id', 'LIKE', '%' . $search_keyword . '%');
                });


        $separator = implode("/", $separator);

        $user_id = Session::get('user_id');


        $query->where('caterer_id', '=', $user_id);
        // Get all the users
        $payments = $query->join('orders', 'orders.id', '=', 'payments.order_id')->select('payments.*', 'orders.caterer_id as caterer_id')->orderBy('id', 'desc')->sortable()->paginate(10);

        // Show the page
        $this->layout->content = View::make('Users/receivedpayment', compact('payments'))->with('search_keyword', $search_keyword)
                ->with('searchByDateFrom', $searchByDateFrom)
                ->with('searchByDateTo', $searchByDateTo)
                ->with('paymentslug', $slug);
    }

    function showReview() {

        $user_id = Session::get('user_id');

        // get current user details
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $query = DB::table('reviews');
        if ($userData->user_type == "Restaurant") {

            $query
                    ->where('reviews.caterer_id', $user_id)
                    ->where('reviews.status', '1')
                    ->join('users', 'users.id', '=', 'reviews.user_id')
                    ->select('reviews.*', 'users.first_name', 'users.last_name', 'users.profile_image');
        } else {
            $query
                    ->where('reviews.user_id', $user_id)
                    ->where('reviews.status', '1')
                    ->join('users', 'users.id', '=', 'reviews.caterer_id')
                    ->select('reviews.*', 'users.first_name', 'users.last_name', 'users.profile_image');
        }


        // get all reviews     


        $records = $query->orderBy('reviews.id', 'desc')->paginate(10);

        $this->layout->title = TITLE_FOR_PAGES . 'My Reviews';
        $this->layout->content = View::make('/Users/review')
                ->with('records', $records)
                ->with('userData', $userData);
    }

    function thousandsFormat($value) {
        if ($value > 999 && $value <= 999999) {
            $result = floor($value / 1000) . 'K';
        } elseif ($value > 999999) {
            $result = floor($value / 1000000) . 'M';
        } else {
            $result = $value;
        }
        return $result;
    }

//    function gmaillogin() {
//
//        $this->layout = "client";
//        $this->set('title_for_layout', TITLE_FOR_PAGES . "Gmail Login");
//        $userid = $this->Session->read("user_id");
//
//
//        if (empty($userid)) {
//            require_once GMAILCLIENT;
//            require_once GMAILOAUTH;
//
//            $google_client_id = GMAIL_CLIENT_ID;
//          //  echo $google_client_id;die;
//            $google_client_secret = GMAIL_SECRET;
//            $google_redirect_url = GMAILREDIRECT;
//            $google_developer_key = GMAIL_DEVELOPER_KEY;
//            $language_session = $_SESSION['Config']['language'];
//            session_destroy();
//            session_start();
//            $_SESSION['Config']['language'] = $language_session;
//            $gClient = new Google_Client();
//            $gClient->setApplicationName('Login to Event  Planner');
//            $gClient->setClientId($google_client_id);
//            $gClient->setClientSecret($google_client_secret);
//            $gClient->setRedirectUri($google_redirect_url);
//            $gClient->setDeveloperKey($google_developer_key);
//
//            $google_oauthV2 = new Google_Oauth2Service($gClient);
//
//            //If user wish to log out, we just unset Session variable
//            if (isset($_REQUEST['reset'])) {
//                unset($_SESSION['token']);
//                $gClient->revokeToken();
//                header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
//            }
//
//            //Redirect user to google authentication page for code, if code is empty.
//            //Code is required to aquire Access Token from google
//            //Once we have access token, assign token to session variable
//            //and we can redirect user back to page and login.
//            if (isset($_REQUEST['code'])) {
//                $gClient->authenticate($_REQUEST['code']);
//                $_SESSION['token'] = $gClient->getAccessToken();
////                header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
////                return;
//            }
//
//
//            if (isset($_SESSION['token'])) {
//                $gClient->setAccessToken($_SESSION['token']);
//            }
//
//
//            if ($gClient->getAccessToken()) {
//                //Get user details if user is logged in
//                $user = $google_oauthV2->userinfo->get();
//                $user_id = $user['id'];
//                $user_name = filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
//                $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
//                $profile_url = filter_var($user['link'], FILTER_VALIDATE_URL);
//                $profile_image_url = filter_var($user['picture'], FILTER_VALIDATE_URL);
//                $personMarkup = "$email<div><img src='$profile_image_url?sz=50'></div>";
//                $_SESSION['token'] = $gClient->getAccessToken();
//            } else {
//                //get google login url
//                $authUrl = $gClient->createAuthUrl();
//            }
//
////        echo '<pre>'; 
////        print_r($user);
////        exit;
//
//            if ($gClient->getAccessToken()) {
//                //            session_start();
//                //            $_SESSION['userdata'] = $user;
//                //            $redirect = REDIRECT;
//                if ($user['gender'] == 'male' || $user['gender'] == 'Male') {
//                    $gender = 'Male';
//                } else {
//                    $gender = 'Female';
//                }
//
//                $userInfo = $this->User->find("first", array("conditions" => array("User.email_address" => $user['email'])));
//                if ($userInfo) {
//                    if ($userInfo['User']['status'] == '1') {
//                        $this->User->id = $userInfo['User']['id'];
//                        $this->request->data['User']['gmail_id'] = $user['id'];
//                        $this->request->data['User']['gmail_link'] = $user['link'];
//                        $this->request->data['User']['first_name'] = $user['given_name'];
//                        $this->request->data['User']['last_name'] = $user['family_name'];
//                        if ($user['gender']) {
//                            $this->request->data['User']['gender'] = $gender;
//                        }
//                        if ($user['picture']) {
//                            $chars = "abcdefghijkmnopqrstuvwxyz023456789";
//                            srand((double) microtime() * 1000000);
//                            $i = 1;
//                            $imagename = '';
//                            while ($i <= 10) {
//                                $num = rand() % 33;
//                                $tmp = substr($chars, $num, 1);
//                                $imagename = $imagename . $tmp;
//                                $i++;
//                            }
//                            $imagename = $imagename . '.jpg';
//                            $fullpath = UPLOAD_FULL_PROFILE_IMAGE_PATH . $imagename;
//                            $remote_img = $user['picture'];
//                            $img_file = file_get_contents($remote_img);
//                            $file_handler = fopen($fullpath, 'w');
//                            if (fwrite($file_handler, $img_file) == false) {
//                                echo 'error';
//                            }
//                            fclose($file_handler);
//                            $this->request->data['User']['profile_image'] = $imagename;
//                            copy(UPLOAD_FULL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_THUMB_PROFILE_IMAGE_PATH . $imagename);
//                            copy(UPLOAD_FULL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_SMALL_PROFILE_IMAGE_PATH . $imagename);
//                            $this->PImageTest->resize(UPLOAD_THUMB_PROFILE_IMAGE_PATH . $imagename, UPLOAD_THUMB_PROFILE_IMAGE_PATH . $imagename, UPLOAD_THUMB_PROFILE_IMAGE_WIDTH, UPLOAD_THUMB_PROFILE_IMAGE_HEIGHT, 100);
//                            $this->PImageTest->resize(UPLOAD_SMALL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_SMALL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_SMALL_PROFILE_IMAGE_WIDTH, UPLOAD_SMALL_PROFILE_IMAGE_HEIGHT, 100);
//                        }
//                        //exit;
////                        $lat = $_SESSION['latitude'];
////                        $long = $_SESSION['longitude'];
////                        $this->request->data['User']['latitude'] = $lat;
////                        $this->request->data['User']['longitude'] = $long;
//
//                        $this->request->data['User']['ip_address'] = $_SERVER['REMOTE_ADDR'];
//
//                        $this->User->save($this->data);
//                        if ($this->data['User']['ip_address']) {
//                            $ipaddress = $this->Ipaddress->find('first', array('conditions' => array('Ipaddress.ip_address' => $this->data['User']['ip_address'])));
//                            if (!$ipaddress) {
//                                $this->request->data['Ipaddress']['ip_address'] = $this->data['User']['ip_address'];
//                                $this->request->data['Ipaddress']['status'] = 1;
//                                $this->request->data['Ipaddress']['slug'] = $this->stringToSlugUnique($this->data['Ipaddress']['ip_address'], 'Ipaddress', 'slug');
//                                $this->Ipaddress->save($this->data);
//                            }
//                        }
//                        $this->Session->write("user_id", $userInfo['User']['id']);
//                        $this->Session->write("user_name", $userInfo['User']['username']);
//                        $this->Session->write("email_address", $userInfo['User']['email_address']);
//
//                        echo "<script>
//                            window.close();
//                            window.opener.location.reload();
//                            </script>";
//
//                        //                if ($this->Session->read("returnsubUrl") != '') {
//                        //                    $this->redirect('/' . $this->Session->read("returnsubUrl"));
//                        //                } else {
//                        //                    $this->redirect('/users/myaccount');
//                        //                }
//                    } else {
//                        $this->Session->setFlash('Your account is deactivated by admin', 'error_msg');
//                        //$this->redirect('/');
//                        echo "<script>
//                            window.close();
//                            window.opener.location.reload();
//                            </script>";
//                    }
//                } else {
//                    $this->request->data['User']['gmail_id'] = $user['id'];
//                    $this->request->data['User']['gmail_link'] = $user['link'];
//                    $this->request->data['User']['email_address'] = $user['email'];
//                    $this->request->data['User']['first_name'] = $user['given_name'];
//                    $this->request->data['User']['last_name'] = $user['family_name'];
//                    if ($user['gender']) {
//                        $this->request->data['User']['gender'] = $gender;
//                    }
//                    if ($user['picture']) {
//                        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
//                        srand((double) microtime() * 1000000);
//                        $i = 1;
//                        $imagename = '';
//                        while ($i <= 10) {
//                            $num = rand() % 33;
//                            $tmp = substr($chars, $num, 1);
//                            $imagename = $imagename . $tmp;
//                            $i++;
//                        }
//                        $imagename = $imagename . '.jpg';
//                        $fullpath = UPLOAD_FULL_PROFILE_IMAGE_PATH . $imagename;
//                        $remote_img = $user['picture'];
//                        $img_file = file_get_contents($remote_img);
//                        $file_handler = fopen($fullpath, 'w');
//                        if (fwrite($file_handler, $img_file) == false) {
//                            echo 'error';
//                        }
//                        fclose($file_handler);
//                        $this->request->data['User']['profile_image'] = $imagename;
//                        copy(UPLOAD_FULL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_THUMB_PROFILE_IMAGE_PATH . $imagename);
//                        copy(UPLOAD_FULL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_SMALL_PROFILE_IMAGE_PATH . $imagename);
//                        $this->PImageTest->resize(UPLOAD_THUMB_PROFILE_IMAGE_PATH . $imagename, UPLOAD_THUMB_PROFILE_IMAGE_PATH . $imagename, UPLOAD_THUMB_PROFILE_IMAGE_WIDTH, UPLOAD_THUMB_PROFILE_IMAGE_HEIGHT, 100);
//                        $this->PImageTest->resize(UPLOAD_SMALL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_SMALL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_SMALL_PROFILE_IMAGE_WIDTH, UPLOAD_SMALL_PROFILE_IMAGE_HEIGHT, 100);
//                    }
//
//                    $voteAmount = $this->VoteAmount->find('first', array('conditions' => array('VoteAmount.user_id' => ''), 'fields' => array('votes_1vs1_poll_per_hour', 'votes_1vs1_poll_per_day', 'votes_poll_list_per_hour', 'votes_poll_list_per_day')));
//
//                    $this->request->data['User']['ip_address'] = $_SERVER['REMOTE_ADDR'];
//                    $this->request->data['User']['username'] = $this->stringToSlugUnique($user['given_name'], 'User', 'slug');
//                    $this->request->data['User']['slug'] = $this->data['User']['username'];
//                    $this->request->data['User']['status'] = '1';
//                    $this->request->data['User']['activation_status'] = '1';
//                    $this->request->data['User']['votes_1vs1_poll_per_hour'] = $voteAmount['VoteAmount']['votes_1vs1_poll_per_hour'];
//                    $this->request->data['User']['votes_1vs1_poll_per_day'] = $voteAmount['VoteAmount']['votes_1vs1_poll_per_day'];
//                    $this->request->data['User']['votes_poll_list_per_hour'] = $voteAmount['VoteAmount']['votes_poll_list_per_hour'];
//                    $this->request->data['User']['votes_poll_list_per_day'] = $voteAmount['VoteAmount']['votes_poll_list_per_day'];
//                    srand((double) microtime() * 1000000);
//                    $i = 1;
//                    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
//                    $password = '';
//                    while ($i <= 10) {
//                        $num = rand() % 33;
//                        $tmp = substr($chars, $num, 1);
//                        $password = $password . $tmp;
//                        $i++;
//                    }
//                    $passwordPlain = $password;
//                    $salt = uniqid(mt_rand(), true);
//                    $new_password = crypt($passwordPlain, '$2a$07$' . $salt . '$');
//                    $this->request->data['User']['password'] = $new_password;
////                    $lat = $_SESSION['latitude'];
////                    $long = $_SESSION['longitude'];
////                    $this->request->data['User']['latitude'] = $lat;
////                    $this->request->data['User']['longitude'] = $long;
//                    //$_SESSION['facebook_data'] = $this->data;
//                    if ($this->User->save($this->data)) {
//                        $userId = $this->User->id;
//                        if ($this->data['User']['ip_address']) {
//                            $ipaddress = $this->Ipaddress->find('first', array('conditions' => array('Ipaddress.ip_address' => $this->data['User']['ip_address'])));
//                            if (!$ipaddress) {
//                                $this->request->data['Ipaddress']['ip_address'] = $this->data['User']['ip_address'];
//                                $this->request->data['Ipaddress']['status'] = 1;
//                                $this->request->data['Ipaddress']['slug'] = $this->stringToSlugUnique($this->data['Ipaddress']['ip_address'], 'Ipaddress', 'slug');
//                                $this->Ipaddress->save($this->data);
//                            }
//                        }
//
//                        $email = $this->data["User"]["email_address"];
//                        $name = $this->data["User"]["username"];
//                        $source = "Gmail";
//                        $this->Email->to = $email;
//                        //$this->Email->cc =$this->Admin->field('cc_email', array('Admin.id' => 1));
//                        $emailtemplateMessage = $this->Emailtemplate->find("first", array("conditions" => "Emailtemplate.id='14'"));
//                        $this->Email->subject = 'Your ' . SITE_TITLE . ' Account has been created using ' . $source;
//                        //$this->Email->subject = $emailtemplateMessage['Emailtemplate']['subject'];
//                        $this->Email->replyTo = SITE_TITLE . "<" . MAIL_FROM . ">";
//                        $this->Email->from = SITE_TITLE . "<" . MAIL_FROM . ">";
//                        $currentYear = date('Y', time());
//                        $sitelink = '<a style="color:#000; text-decoration: underline;" href="mailto:' . MAIL_FROM . '">' . MAIL_FROM . '</a>';
//                        $toRepArray = array('[!username!]', '[!user_type!]', '[!email!]', '[!password!]', '[!DATE!]', '[!HTTP_PATH!]', '[!SITE_TITLE!]', '[!SITE_LINK!]', '[!SITE_URL!]', '[!SOURCE!]');
//                        $fromRepArray = array($name, 'User', $email, $passwordPlain, $currentYear, HTTP_PATH, SITE_TITLE, $sitelink, SITE_URL, $source);
//                        $messageToSend = str_replace($toRepArray, $fromRepArray, $emailtemplateMessage['Emailtemplate']['template']);
//                        $this->Email->layout = 'default';
//                        $this->set('messageToSend', $messageToSend);
//                        $this->Email->template = 'email_template';
//                        $this->Email->sendAs = 'html';
//                        $this->Email->send();
//
//                        $this->Session->write("user_id", $userId);
//                        $this->Session->write("user_name", $this->data["User"]["username"]);
//                        $this->Session->write("email_address", $this->data["User"]["email_address"]);
//                        //$this->redirect('/users/myaccount');
//                        echo "<script>
//                         window.close();
//                         window.opener.location.href = '" . HTTP_PATH . "/alerts/listalert" . "';
//                         </script>";
//                    }
//
////                    echo "<script>
////                        window.close();
////                        window.opener.location.href = '" . HTTP_PATH . "/users/facebookLogin/Gmail" . "';
////                        </script>";
//                }
//            } else {
//                echo "<script>
//                window.close();
//                window.opener.location.href = '" . HTTP_PATH . "/users/login" . "';
//                </script>";
//            }
//        } else {
//            require_once GMAILCLIENT;
//            require_once GMAILOAUTH;
//
//            $google_client_id = GMAIL_CLIENT_ID;
//            $google_client_secret = GMAIL_SECRET;
//            $google_redirect_url = GMAILREDIRECT;
//            $google_developer_key = GMAIL_DEVELOPER_KEY;
//            $language_session = $_SESSION['Config']['language'];
//            session_destroy();
//            session_start();
//            $_SESSION['Config']['language'] = $language_session;
//            $gClient = new Google_Client();
//            $gClient->setApplicationName('Login to Event  Planner');
//            $gClient->setClientId($google_client_id);
//            $gClient->setClientSecret($google_client_secret);
//            $gClient->setRedirectUri($google_redirect_url);
//            $gClient->setDeveloperKey($google_developer_key);
//
//            $google_oauthV2 = new Google_Oauth2Service($gClient);
//
//            //If user wish to log out, we just unset Session variable
//            if (isset($_REQUEST['reset'])) {
//                unset($_SESSION['token']);
//                $gClient->revokeToken();
//                header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
//            }
//
//            //Redirect user to google authentication page for code, if code is empty.
//            //Code is required to aquire Access Token from google
//            //Once we have access token, assign token to session variable
//            //and we can redirect user back to page and login.
//            if (isset($_REQUEST['code'])) {
//                $gClient->authenticate($_REQUEST['code']);
//                $_SESSION['token'] = $gClient->getAccessToken();
//                //                header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
//                //                return;
//            }
//
//
//            if (isset($_SESSION['token'])) {
//                $gClient->setAccessToken($_SESSION['token']);
//            }
//
//
//            if ($gClient->getAccessToken()) {
//                //Get user details if user is logged in
//                $user = $google_oauthV2->userinfo->get();
//                $user_id = $user['id'];
//                $user_name = filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
//                $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
//                $profile_url = filter_var($user['link'], FILTER_VALIDATE_URL);
//                $profile_image_url = filter_var($user['picture'], FILTER_VALIDATE_URL);
//                $personMarkup = "$email<div><img src='$profile_image_url?sz=50'></div>";
//                $_SESSION['token'] = $gClient->getAccessToken();
//            } else {
//                //get google login url
//                $authUrl = $gClient->createAuthUrl();
//            }
//
//            //        echo '<pre>'; 
//            //        print_r($user);
//            //        exit;
//
//
//            if ($gClient->getAccessToken()) {
//                //            session_start();
//                //            $_SESSION['userdata'] = $user;
//                //            $redirect = REDIRECT;
//                if ($user['gender'] == 'male' || $user['gender'] == 'Male') {
//                    $gender = 'Male';
//                } else {
//                    $gender = 'Female';
//                }
//
//                $this->User->id = $userid;
//                $this->request->data['User']['gmail_id'] = $user['id'];
//                $this->request->data['User']['gmail_link'] = $user['link'];
//                $this->request->data['User']['first_name'] = $user['given_name'];
//                $this->request->data['User']['last_name'] = $user['family_name'];
//                if ($user['gender']) {
//                    $this->request->data['User']['gender'] = $gender;
//                }
//                if ($user['picture']) {
//                    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
//                    srand((double) microtime() * 1000000);
//                    $i = 1;
//                    $imagename = '';
//                    while ($i <= 10) {
//                        $num = rand() % 33;
//                        $tmp = substr($chars, $num, 1);
//                        $imagename = $imagename . $tmp;
//                        $i++;
//                    }
//                    $imagename = $imagename . '.jpg';
//                    $fullpath = UPLOAD_FULL_PROFILE_IMAGE_PATH . $imagename;
//                    $remote_img = $user['picture'];
//                    $img_file = file_get_contents($remote_img);
//                    $file_handler = fopen($fullpath, 'w');
//                    if (fwrite($file_handler, $img_file) == false) {
//                        echo 'error';
//                    }
//                    fclose($file_handler);
//                    $this->request->data['User']['profile_image'] = $imagename;
//                    copy(UPLOAD_FULL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_THUMB_PROFILE_IMAGE_PATH . $imagename);
//                    copy(UPLOAD_FULL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_SMALL_PROFILE_IMAGE_PATH . $imagename);
//                    $this->PImageTest->resize(UPLOAD_THUMB_PROFILE_IMAGE_PATH . $imagename, UPLOAD_THUMB_PROFILE_IMAGE_PATH . $imagename, UPLOAD_THUMB_PROFILE_IMAGE_WIDTH, UPLOAD_THUMB_PROFILE_IMAGE_HEIGHT, 100);
//                    $this->PImageTest->resize(UPLOAD_SMALL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_SMALL_PROFILE_IMAGE_PATH . $imagename, UPLOAD_SMALL_PROFILE_IMAGE_WIDTH, UPLOAD_SMALL_PROFILE_IMAGE_HEIGHT, 100);
//                }
//                //exit;
////                $lat = $_SESSION['latitude'];
////                $long = $_SESSION['longitude'];
////                $this->request->data['User']['latitude'] = $lat;
////                $this->request->data['User']['longitude'] = $long;
//
//                $this->request->data['User']['ip_address'] = $_SERVER['REMOTE_ADDR'];
//                $this->User->save($this->data);
//                if ($this->data['User']['ip_address']) {
//                    $ipaddress = $this->Ipaddress->find('first', array('conditions' => array('Ipaddress.ip_address' => $this->data['User']['ip_address'])));
//                    if (!$ipaddress) {
//                        $this->request->data['Ipaddress']['ip_address'] = $this->data['User']['ip_address'];
//                        $this->request->data['Ipaddress']['status'] = 1;
//                        $this->request->data['Ipaddress']['slug'] = $this->stringToSlugUnique($this->data['Ipaddress']['ip_address'], 'Ipaddress', 'slug');
//                        $this->Ipaddress->save($this->data);
//                    }
//                }
//                $userCheck = $this->User->find('first', array('conditions' => array('User.id' => $userid)));
//                $this->Session->write("user_id", $userCheck['User']['id']);
//                $this->Session->write("email_address", $userCheck['User']['email_address']);
//                $this->Session->write("user_name", $userCheck['User']['username']);
//                echo "<script>
//                                    window.close();
//                                    window.opener.location.reload();
//                                    </script>";
//
//                //                if ($this->Session->read("returnsubUrl") != '') {
//                //                    $this->redirect('/' . $this->Session->read("returnsubUrl"));
//                //                } else {
//                //                    $this->redirect('/users/myaccount');
//                //                }
//            } else {
//
//                echo "<script>
//                        window.close();
//                        window.opener.location.href = '" . HTTP_PATH . "/users/login" . "';
//                        </script>";
//            }
//        }
//    }



    public function gmaillogin() {
        $user_id = Session::get('user_id');
        if (empty($userid)) {
            require_once GMAILCLIENT;
            require_once GMAILOAUTH;

            $google_client_id = GMAIL_CLIENT_ID;
            $google_client_secret = GMAIL_SECRET;
            $google_redirect_url = GMAILREDIRECT;
            $google_developer_key = GMAIL_DEVELOPER_KEY;
            //$language_session = $_SESSION['Config']['language'];
//            session_destroy();
            session_start();
            // $_SESSION['Config']['language'] = $language_session;
            $gClient = new Google_Client();
            $gClient->setApplicationName('Login to food ordering');
            $gClient->setClientId($google_client_id);
            $gClient->setClientSecret($google_client_secret);
            $gClient->setRedirectUri($google_redirect_url);
            $gClient->setDeveloperKey($google_developer_key);
            $google_oauthV2 = new Google_Oauth2Service($gClient);

            //If user wish to log out, we just unset Session variable
            if (isset($_REQUEST['reset'])) {
                unset($_SESSION['token']);
                $gClient->revokeToken();
                header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
            }

            //Redirect user to google authentication page for code, if code is empty.
            //Code is required to aquire Access Token from google
            //Once we have access token, assign token to session variable
            //and we can redirect user back to page and login.
            if (isset($_REQUEST['code'])) {
                $gClient->authenticate($_REQUEST['code']);
                $_SESSION['token'] = $gClient->getAccessToken();
//                header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
//                return;
            }


            if (isset($_SESSION['token'])) {
                $gClient->setAccessToken($_SESSION['token']);
            }

            if ($gClient->getAccessToken()) {
                //Get user details if user is logged in
                $user = $google_oauthV2->userinfo->get();
                $user_id = $user['id'];
                $user_name = filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
                $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
                $profile_image_url = filter_var($user['picture'], FILTER_VALIDATE_URL);
                $personMarkup = "$email<div><img src='$profile_image_url?sz=50'></div>";
                $_SESSION['token'] = $gClient->getAccessToken();
            } else {
                //get google login url
                $authUrl = $gClient->createAuthUrl();
            }

//        echo '<pre>'; 
//        print_r($user);
//        exit;

            if ($gClient->getAccessToken()) {
                //            session_start();
                //            $_SESSION['userdata'] = $user;
                //            $redirect = REDIRECT;

                $userInfo = DB::table('users')
                        ->where('email_address', $user['email'])
                        ->first();

                if ($userInfo) {

                    if ($userInfo->status == '1') {

                        $user_array = array(
                            'gmail_id' => $user['id'],
                            'modified' => date('Y-m-d H:i:s'),
                            'first_name' => $user['given_name'],
                            'last_name' => $user['family_name'],
                            'modified' => date('Y-m-d H:i:s'),
                        );

                        if ($user['picture']) {
                            $chars = "abcdefghijkmnopqrstuvwxyz023456789";
                            srand((double) microtime() * 1000000);
                            $i = 1;
                            $imagename = '';
                            while ($i <= 10) {
                                $num = rand() % 33;
                                $tmp = substr($chars, $num, 1);
                                $imagename = $imagename . $tmp;
                                $i++;
                            }
                            $imagename = $imagename . '.jpg';
                            $fullpath = UPLOAD_FULL_PROFILE_IMAGE_PATH . '/' . $imagename;
                            $remote_img = $user['picture'];
                            $img_file = file_get_contents($remote_img);
                            $file_handler = fopen($fullpath, 'w');
                            if (fwrite($file_handler, $img_file) == false) {
                                echo 'error';
                            }
                            fclose($file_handler);
                            $user_array['profile_image'] = $imagename;
                        }

                        //exit;
                        DB::table('users')
                                ->where('id', $userInfo->id)
                                ->update(
                                        $user_array
                        );

                        //ip old 
//                        if ( $user_array['ip_address']) {
//                            
//                            $ipaddress = $this->Ipaddress->find('first', array('conditions' => array('Ipaddress.ip_address' => $this->data['User']['ip_address'])));
//                            if (!$ipaddress) {
//                                $this->request->data['Ipaddress']['ip_address'] = $this->data['User']['ip_address'];
//                                $this->request->data['Ipaddress']['status'] = 1;
//                                $this->request->data['Ipaddress']['slug'] = $this->stringToSlugUnique($this->data['Ipaddress']['ip_address'], 'Ipaddress', 'slug');
//                                $this->Ipaddress->save($this->data);
//                            }
//                        }
//                        
                        Session::put('user_id', $userInfo->id);
                        Session::put('email_address', $userInfo->email_address);
                        Session::save();

                        $url = HTTP_PATH . "user/myaccount";

                        echo "<script>
                            window.close();
                           window.opener.location.href = '" . $url . "';
                            </script>";


//                        echo "<script>
//                            window.close();
//                            window.opener.location.reload();
//                            </script>";
                        //                if ($this->Session->read("returnsubUrl") != '') {
                        //                    $this->redirect('/' . $this->Session->read("returnsubUrl"));
                        //                } else {
                        //                    $this->redirect('/users/myaccount');
                        //                }
                    } else {
                        Session::put('error_message', "Your account is deactivated by admin");
                        //$this->redirect('/');
                        echo "<script>
                            window.close();
                            window.opener.location.reload();
                            </script>";
                    }
                } else {

                    $user_array = array(
                        'status' => '1',
                        'activation_status' => '1',
                        'approve_status' => '1',
                        'user_type' => 'Customer',
                        'gmail_id' => $user['id'],
                        'email_address' => $user['email'],
                        'slug' => $this->createUniqueSlug($user['given_name'], 'users'),
                        'first_name' => $user['given_name'],
                        'last_name' => $user['family_name'],
                        'created' => date('Y-m-d H:i:s')
                    );

                    if ($user['picture']) {
                        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
                        srand((double) microtime() * 1000000);
                        $i = 1;
                        $imagename = '';
                        while ($i <= 10) {
                            $num = rand() % 33;
                            $tmp = substr($chars, $num, 1);
                            $imagename = $imagename . $tmp;
                            $i++;
                        }
                        $imagename = $imagename . '.jpg';
                        $fullpath = UPLOAD_FULL_PROFILE_IMAGE_PATH . '/' . $imagename;
                        $remote_img = $user['picture'];
                        $img_file = file_get_contents($remote_img);
                        $file_handler = fopen($fullpath, 'w');
                        if (fwrite($file_handler, $img_file) == false) {
                            echo 'error';
                        }
                        fclose($file_handler);
                        $user_array['profile_image'] = $imagename;
                    }

                    srand((double) microtime() * 1000000);
                    $i = 1;
                    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
                    $password = '';
                    while ($i <= 10) {
                        $num = rand() % 33;
                        $tmp = substr($chars, $num, 1);
                        $password = $password . $tmp;
                        $i++;
                    }
                    $passwordPlain = $password;
                    $salt = uniqid(mt_rand(), true);
                    $new_password = crypt($passwordPlain, '$2a$07$' . $salt . '$');
                    $user_array['password'] = $new_password;

//                    $lat = $_SESSION['latitude'];
//                    $long = $_SESSION['longitude'];
//                    $this->request->data['User']['latitude'] = $lat;
//                    $this->request->data['User']['longitude'] = $long;
                    //$_SESSION['facebook_data'] = $this->data;

                    DB::table('users')->insert(
                            $user_array
                    );

                    $id = DB::getPdo()->lastInsertId();


                    if ($id) {

                        $userId = $id;
                        $email = $user['email'];

                        // send email to customer

                        $mail_data = array(
                            'text' => 'Your account is successfully created.you can use below credentials for login to ' . SITE_TITLE . '.',
                            'email' => $user['email'],
                            'password' => $new_password,
                            'firstname' => $user['given_name']
                        );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                        Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                                    $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created');
                                });



//                        $source = "Gmail";
//                        $this->Email->to = $email;
//                        //$this->Email->cc =$this->Admin->field('cc_email', array('Admin.id' => 1));
//                        $emailtemplateMessage = $this->Emailtemplate->find("first", array("conditions" => "Emailtemplate.id='14'"));
//                        $this->Email->subject = 'Your ' . SITE_TITLE . ' Account has been created using ' . $source;
//                        //$this->Email->subject = $emailtemplateMessage['Emailtemplate']['subject'];
//                        $this->Email->replyTo = SITE_TITLE . "<" . MAIL_FROM . ">";
//                        $this->Email->from = SITE_TITLE . "<" . MAIL_FROM . ">";
//                        $currentYear = date('Y', time());
//                        $sitelink = '<a style="color:#000; text-decoration: underline;" href="mailto:' . MAIL_FROM . '">' . MAIL_FROM . '</a>';
//                        $toRepArray = array('[!username!]', '[!user_type!]', '[!email!]', '[!password!]', '[!DATE!]', '[!HTTP_PATH!]', '[!SITE_TITLE!]', '[!SITE_LINK!]', '[!SITE_URL!]', '[!SOURCE!]');
//                        $fromRepArray = array($name, 'User', $email, $passwordPlain, $currentYear, HTTP_PATH, SITE_TITLE, $sitelink, SITE_URL, $source);
//                        $messageToSend = str_replace($toRepArray, $fromRepArray, $emailtemplateMessage['Emailtemplate']['template']);
//                        $this->Email->layout = 'default';
//                        $this->set('messageToSend', $messageToSend);
//                        $this->Email->template = 'email_template';
//                        $this->Email->sendAs = 'html';
//                        $this->Email->send();


                        Session::put('user_id', $userId);
                        Session::put('email_address', $email);
                        Session::save();

                        //$this->redirect('/users/myaccount');
                        echo "<script>
                         window.close();
                         window.opener.location.href = '" . HTTP_PATH . "user/myaccount" . "';
                         </script>";
                    }

//                    echo "<script>
//                        window.close();
//                        window.opener.location.href = '" . HTTP_PATH . "/users/facebookLogin/Gmail" . "';
//                        </script>";
                }
            } else {

                Session::put('error_message', "Error to connecting, please try later");
                echo "<script>
                window.close();
                window.opener.location.href = '" . HTTP_PATH . "';
                </script>";
            }
        } else {
            require_once GMAILCLIENT;
            require_once GMAILOAUTH;

            $google_client_id = GMAIL_CLIENT_ID;
            $google_client_secret = GMAIL_SECRET;
            $google_redirect_url = GMAILREDIRECT;
            $google_developer_key = GMAIL_DEVELOPER_KEY;
            $language_session = $_SESSION['Config']['language'];
            session_destroy();
            session_start();
            $_SESSION['Config']['language'] = $language_session;
            $gClient = new Google_Client();
            $gClient->setApplicationName('Login to Customer');
            $gClient->setClientId($google_client_id);
            $gClient->setClientSecret($google_client_secret);
            $gClient->setRedirectUri($google_redirect_url);
            $gClient->setDeveloperKey($google_developer_key);

            $google_oauthV2 = new Google_Oauth2Service($gClient);

            //If user wish to log out, we just unset Session variable
            if (isset($_REQUEST['reset'])) {
                unset($_SESSION['token']);
                $gClient->revokeToken();
                header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
            }

            //Redirect user to google authentication page for code, if code is empty.
            //Code is required to aquire Access Token from google
            //Once we have access token, assign token to session variable
            //and we can redirect user back to page and login.
            if (isset($_REQUEST['code'])) {
                $gClient->authenticate($_REQUEST['code']);
                $_SESSION['token'] = $gClient->getAccessToken();
                //                header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
                //                return;
            }


            if (isset($_SESSION['token'])) {
                $gClient->setAccessToken($_SESSION['token']);
            }


            if ($gClient->getAccessToken()) {
                //Get user details if user is logged in
                $user = $google_oauthV2->userinfo->get();
                $user_id = $user['id'];
                $user_name = filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
                $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
                $profile_image_url = filter_var($user['picture'], FILTER_VALIDATE_URL);
                $personMarkup = "$email<div><img src='$profile_image_url?sz=50'></div>";
                $_SESSION['token'] = $gClient->getAccessToken();
            } else {
                //get google login url
                $authUrl = $gClient->createAuthUrl();
            }

            //        echo '<pre>'; 
            //        print_r($user);
            //        exit;


            if ($gClient->getAccessToken()) {
                //            session_start();
                //            $_SESSION['userdata'] = $user;
                //            $redirect = REDIRECT;
//                if ($user['gender'] == 'male' || $user['gender'] == 'Male') {
//                    $gender = 'Male';
//                } else {
//                    $gender = 'Female';
//                }

                $user_array = array(
                    'gmail_id' => $user['id'],
                    'status' => '1',
                    'slug' => $this->createUniqueSlug($user['given_name'], 'users'),
                    'activation_status' => '1',
                    'approve_status' => '1',
                    'user_type' => 'Customer',
                    'created' => date('Y-m-d H:i:s'),
                    'first_name' => $user['given_name'],
                    'last_name' => $user['family_name'],
                );

//                $this->User->id = $userid;
//                $this->request->data['User']['gmail_id'] = $user['id'];
//                $this->request->data['User']['gmail_link'] = $user['link'];
//                $this->request->data['User']['first_name'] = $user['given_name'];
//                $this->request->data['User']['last_name'] = $user['family_name'];
//                if ($user['gender']) {
//                    $this->request->data['User']['gender'] = $gender;
//                }

                if ($user['picture']) {
                    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
                    srand((double) microtime() * 1000000);
                    $i = 1;
                    $imagename = '';
                    while ($i <= 10) {
                        $num = rand() % 33;
                        $tmp = substr($chars, $num, 1);
                        $imagename = $imagename . $tmp;
                        $i++;
                    }
                    $imagename = $imagename . '.jpg';
                    $fullpath = UPLOAD_FULL_PROFILE_IMAGE_PATH . '/' . $imagename;
                    $remote_img = $user['picture'];
                    $img_file = file_get_contents($remote_img);
                    $file_handler = fopen($fullpath, 'w');
                    if (fwrite($file_handler, $img_file) == false) {
                        echo 'error';
                    }
                    fclose($file_handler);
                    $user_array['profile_image'] = $imagename;
                }

                srand((double) microtime() * 1000000);
                $i = 1;
                $chars = "abcdefghijkmnopqrstuvwxyz023456789";
                $password = '';
                while ($i <= 10) {
                    $num = rand() % 33;
                    $tmp = substr($chars, $num, 1);
                    $password = $password . $tmp;
                    $i++;
                }
                $passwordPlain = $password;
                $salt = uniqid(mt_rand(), true);
                $new_password = crypt($passwordPlain, '$2a$07$' . $salt . '$');
                $user_array['password'] = $new_password;




                //exit;
//                $lat = $_SESSION['latitude'];
//                $long = $_SESSION['longitude'];
//                $this->request->data['User']['latitude'] = $lat;
//                $this->request->data['User']['longitude'] = $long;
//                $this->request->data['User']['ip_address'] = $_SERVER['REMOTE_ADDR'];
//                $this->User->save($this->data);
//                if ($this->data['User']['ip_address']) {
//                    $ipaddress = $this->Ipaddress->find('first', array('conditions' => array('Ipaddress.ip_address' => $this->data['User']['ip_address'])));
//                    if (!$ipaddress) {
//                        $this->request->data['Ipaddress']['ip_address'] = $this->data['User']['ip_address'];
//                        $this->request->data['Ipaddress']['status'] = 1;
//                        $this->request->data['Ipaddress']['slug'] = $this->stringToSlugUnique($this->data['Ipaddress']['ip_address'], 'Ipaddress', 'slug');
//                        $this->Ipaddress->save($this->data);
//                    }
//                }


                DB::table('users')->insert(
                        $user_array
                );

                $id = DB::getPdo()->lastInsertId();

                if ($id) {

                    $email = $user['email'];

                    // send email to customer

                    $mail_data = array(
                        'text' => 'Your account is successfully created.you can use below credentials for login to ' . SITE_TITLE . '.',
                        'email' => $user['email'],
                        'password' => $new_password,
                        'firstname' => $user['given_name']
                    );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                    Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created');
                            });
                }

                $userCheck = DB::table('users')
                        ->where('id', $id)
                        ->first();

                Session::put('user_id', $userCheck->id);
                Session::put('email_address', $userCheck->email_address);
                Session::save();

                echo "<script>
                                    window.close();
                                    window.opener.location.reload();
                                    </script>";

                //                if ($this->Session->read("returnsubUrl") != '') {
                //                    $this->redirect('/' . $this->Session->read("returnsubUrl"));
                //                } else {
                //                    $this->redirect('/users/myaccount');
                //                }
            } else {

                echo "<script>
                        window.close();
                        window.opener.location.href = '" . HTTP_PATH . "';
                        </script>";
            }
        }
    }

    // Facebook login 
    public function showfbLogin() {
        session_start();
        if (isset($_SESSION['FB']) && $_SESSION['FB'] != '') {
            $fbID = $_SESSION['FB']['fbid'];
            $fb_first_name = $_SESSION['FB']['first_name'];
            $fb_last_name = $_SESSION['FB']['last_name'];
            $fb_username = $_SESSION['FB']['first_name'] . time();
            $fb_email = $_SESSION['FB']['email'];

            unset($_SESSION['FB']);

            $userInfo = DB::table('users')
                    ->where('email_address', $fb_email)
                    ->first();

            if ($userInfo) {
                if ($userInfo->status == '1') {

                    $saveUser = array(
                        'facebook_user_id' => $fbID,
                        'first_name' => trim($fb_first_name),
                        'last_name' => trim($fb_last_name),
                        'modified' => date('Y-m-d H:i:s'),
                        'created' => date('Y-m-d H:i:s')
                    );

                    DB::table('users')
                            ->where('id', $userInfo->id)
                            ->update(
                                    $saveUser
                    );

                    Session::put('user_id', $userInfo->id);
                    Session::save();
                    $url = HTTP_PATH . "user/myaccount";

                    echo "<script>
                window.close();
                window.opener.location.href = '" . $url . "';
                </script>";
                } else {
                    Session::put('error_message', "Your account might have been temporarily disabled. Please contact us for more details.<br>', 'error_msg'");
                    echo "<script>
                window.close();
                window.opener.location.reload();
                </script>";
                }
            } else {

                $saveUser = array(
                    'facebook_user_id' => $fbID,
                    'email_address' => $fb_email,
                    'first_name' => trim($fb_first_name),
                    'last_name' => trim($fb_last_name),
                    'modified' => date('Y-m-d H:i:s'),
                    'created' => date('Y-m-d H:i:s')
                );

                $image = $this->createSlug($fb_first_name) . ".jpg";
                $content = file_get_contents("https://graph.facebook.com/" . $fbID . "/picture?type=large");
                // $content1 = file_get_contents("https://graph.facebook.com/" . $fbID . "/picture?type=small");
                $fp = fopen("uploads/users/" . $image, "w");
                fwrite($fp, $content);
                fclose($fp);

                $saveUser['profile_image'] = $image;
                $saveUser['slug'] = $this->createUniqueSlug($fb_first_name, 'users');
                $saveUser['status'] = '1';
                $saveUser['activation_status'] = '1';
                $saveUser['activation_status'] = '1';


                srand((double) microtime() * 1000000);
                $i = 1;
                $chars = "abcdefghijkmnopqrstuvwxyz023456789";
                $password = '';
                while ($i <= 10) {
                    $num = rand() % 33;
                    $tmp = substr($chars, $num, 1);
                    $password = $password . $tmp;
                    $i++;
                }
                $passwordPlain = $password;
                $salt = uniqid(mt_rand(), true);
                $password = crypt($passwordPlain, '$2a$07$' . $salt . '$');
                $saveUser['password'] = $password;
                DB::table('users')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();

                if ($id) {

                    $mail_data = array(
                        'text' => 'Your account is successfully created.you can use below credentials for login to ' . SITE_TITLE . '.',
                        'email' => $fb_email,
                        'password' => $password,
                        'firstname' => $fb_first_name
                    );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                    Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created');
                            });
//                    
                    Session::put('user_id', $id);
                    Session::save();

                    $url = HTTP_PATH . "user/myaccount";
                    // echo $url;die;
//                    
//                    if ($this->Session->read("returnUrl")) {
//                        $returnUrl = $this->Session->read("returnUrl");
//                        $this->Session->delete("returnUrl");
//                        $url = HTTP_PATH . $returnUrl;
//                    } else {
//
//                        $url = HTTP_PATH . "account";
//                    }

                    echo "<script>
                window.close();
                window.opener.location.href = '" . $url . "';
                </script>";
                }
            }
        } else {
            header("Location: " . HTTP_PATH . "app/Social/facebook/fbconfig.php");
            exit;
        }
        exit;
    }

    // FB connect if allready login with facebook 
    public function showfbConnect() {
        session_start();
        if (isset($_SESSION['FB']) && $_SESSION['FB'] != '') {
            $fbID = $_SESSION['FB']['fbid'];
            $fb_first_name = $_SESSION['FB']['first_name'];
            $fb_last_name = $_SESSION['FB']['last_name'];
            $fb_username = $_SESSION['FB']['first_name'] . time();
            $fb_email = $_SESSION['FB']['email'];

            unset($_SESSION['FB']);

            $userInfo = DB::table('users')
                    ->where('email_address', $fb_email)
                    ->first();

            if ($userInfo) {
                if ($userInfo->email == $fb_email) {

                    $saveUser = array(
                        'facebook_user_id' => $fbID,
                        'created' => date('Y-m-d H:i:s')
                    );

                    DB::table('users')
                            ->where('id', $userInfo->id)
                            ->update(
                                    $saveUser
                    );

                    Session::put('user_id', $userInfo->id);
                    Session::save();
                    $url = HTTP_PATH . "user/myaccount";
//                    
//                    if (Session::has('returnUrl')) {
//                        $returnUrl = Session::get('returnUrl');
//                        Session::forget('returnUrl');
//                        $url = HTTP_PATH . $returnUrl;
//                    } else {
//                        $url = HTTP_PATH . "/account";
//                    }
                    //echo $url; exit;

                    echo "<script>
                window.close();
                window.opener.location.href = '" . $url . "';
                </script>";
                } else {
                    Session::put('error_message', "Your account might have been temporarily disabled. Please contact us for more details.<br>', 'error_msg'");
                    echo "<script>
                window.close();
                window.opener.location.reload();
                </script>";
                }
            } else {

                $url = HTTP_PATH;
//                    
//                    if ($this->Session->read("returnUrl")) {
//                        $returnUrl = $this->Session->read("returnUrl");
//                        $this->Session->delete("returnUrl");
//                        $url = HTTP_PATH . $returnUrl;
//                    } else {
//
//                        $url = HTTP_PATH . "account";
//                    }

                echo "<script>
                window.close();
                window.opener.location.href = '" . $url . "';
                </script>";
            }
        } else {
            header("Location: " . HTTP_PATH . "app/Social/facebook/fbconfig.php");
            exit;
        }
        exit;
    }

    public function activekitchenstaff($slug = null) {
        if (!empty($slug)) {
            // check admin approval
            $Data = DB::table('users')
                    ->select("approve_status", "email_address", "first_name")
                    ->where('slug', $slug)
                    ->first();

            if (!$Data->approve_status) {
                // send email to user for account varification
                $userEmail = $Data->email_address;

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account has been successfully confirmed by ' . SITE_TITLE . '.',
                    'email' => $userEmail,
                    'firstname' => $Data->first_name
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully approved by ' . SITE_TITLE);
                        });
            }

            DB::table('users')
            ->where('slug', $slug)
            ->update(['status' => 1, 'approve_status' => 1]);

            return Redirect::to('/user/kitchenstaff')->with('success_message', 'Kitchen Staff activated successfully');
        }
    }

    public function deactivekitchenstaff($slug = null) {
        if (!empty($slug)) {
            DB::table('users')
            ->where('slug', $slug)
            ->update(['status' => 0]);

            return Redirect::to('/user/kitchenstaff')->with('success_message', 'Kitchen Staff deactivated successfully');
        }
    }

    public function activedeliveryperson($slug = null) {
        if (!empty($slug)) {
            // check admin approval
            $Data = DB::table('users')
                    ->select("approve_status", "email_address", "first_name")
                    ->where('slug', $slug)
                    ->first();

            if (!$Data->approve_status) {
                // send email to user for account varification
                $userEmail = $Data->email_address;

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account has been successfully confirmed by ' . SITE_TITLE . '.',
                    'email' => $userEmail,
                    'firstname' => $Data->first_name
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully approved by ' . SITE_TITLE);
                        });
            }

            DB::table('users')
            ->where('slug', $slug)
            ->update(['status' => 1, 'approve_status' => 1]);

            return Redirect::to('/user/deliveryperson')->with('success_message', 'Kitchen Staff activated successfully');
        }
    }

    public function deactivedeliveryperson($slug = null) {
        if (!empty($slug)) {
            DB::table('users')
            ->where('slug', $slug)
            ->update(['status' => 0]);

            return Redirect::to('/user/deliveryperson')->with('success_message', 'Kitchen Staff deactivated successfully');
        }
    }

}
