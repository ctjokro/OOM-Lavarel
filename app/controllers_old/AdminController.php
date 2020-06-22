<?php

class AdminController extends BaseController {

    public function __construct() {
        $adminId = Session::get('adminid');

        if ($adminId == '') {
            return Redirect::to('/admin/login');
        }
    }

    // initialize admin layout
    protected $layout = 'adminloginlayout';

    // admin login page
    public function showAdminlogin() {
        //echo md5('328649291');
        if (Session::has('adminid')) {
            return Redirect::to('/admin/admindashboard');
        }
        $this->layout->content = View::make('admin.adminlogin');
        $input = Input::all();
        if (!empty($input)) {
            $username = trim($input['username']);
            $password = md5(trim($input['password'])); // exit;
            $rules = array(
                'username' => 'required', // make sure the username field is not empty
                'password' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
            );
            if (Session::has('captcha'))
                $rules['captcha'] = 'required|checkcaptcha';

            //captcha custom validation rule
            Validator::extend('checkcaptcha', function($attribute, $value, $parameters) {
                        if (Session::get('security_number') <> $value) {
                            return false;
                        }
                        return true;
                    });

            // captcha custom message
            $messages = array(
                'checkcaptcha' => 'Please enter valid security code.'
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules, $messages);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/login')
                                ->withErrors($validator) // send back all errors to the login form
                                ->withInput(Input::except('password'))
                                ->withInput(Input::except('captcha')); // send back the input (not the password) so that we can repopulate the form
            } else {

                // create our user data for the authentication
                $adminuser = DB::table('admins')
                        ->where('username', $username)
                        ->where('password', $password)
                        ->first();

                if (!empty($adminuser)) {

                    // destroy captcha from login
                    Session::forget('captcha');

                    // return to dashboard page
                    Session::put('adminid', $adminuser->id);
                    return Redirect::to('/admin/admindashboard');
                } else {

                    // return error message
                    Session::put('captcha', 1);
                    Session::put('error_message', "Invalid username or password");
                    return Redirect::to('/admin/login');
                }
            }
        } else {
            return $this->layout->content = View::make('admin.adminlogin');
        }
    }

    // admin dashboard page
    public function showAdmindashboard() {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        // count all users
        $dates = array();
        $year = date('Y');
        for ($i = 1; $i <= date("m"); $i++) {
            $results = DB::select('select count(tbl_users.id) as counter from tbl_users where user_type = "Restaurant" and DATE_FORMAT(tbl_users.created, "%Y-%m") = "' . $year . '-' . str_pad($i, 2, 0, STR_PAD_LEFT) . '"');
            $dates[] = $results[0]->counter;
        }

        $results = DB::select('select count(tbl_users.id) as counter from tbl_users where user_type = "Restaurant" and date_sub(curdate(), INTERVAL 7 DAY) <= tbl_users.created  AND NOW() >= UNIX_TIMESTAMP(tbl_users.created)');
        $last_seven_days = $results[0]->counter;

        return View::make('admin/admindashboard', array('last_seven_days' => $last_seven_days))->with('dates', $dates);
    }

    // goto admin login page
    public function showAdminlogout() {
        Session::forget('adminid');
        return Redirect::to('/admin/login');
    }

    // forgotpassword page
    public function showForgotpassword() {
        $this->layout = false;
        $input = Input::all();
        $email = $input['email'];
        $rules = array(
            'email' => 'required'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make($input, $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            $errors->first('email');
            return json_encode(array('message' => $errors, 'valid' => 0));
            exit;
        } else {

            // create our user data for the authentication
            $adminuser = DB::table('admins')
                    ->where('email', $email)
                    ->first();

            if (!empty($adminuser)) {
                // generate random password
                 $password = rand(18973824, 989721389);
                $Newpassword = md5($password);

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your password has been retrived successfully',
                    'email' => $adminuser->email,
                    'username' => $adminuser->username,
                    'new_password' => $password,
                    'firstname' => 'Admin',
                );
                
               
                DB::table('admins')
                            ->where('id', 1)
                            ->update(array('password' => $Newpassword));
               //  print_r($mail_data); exit;
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($adminuser) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($adminuser->email, 'Admin')->subject('Forgot Password');
                        });
                if (count(Mail::failures()) > 0) {
                    echo $errors = 'Failed to send password reset email, please try again.';
                    foreach (Mail::failures() as $email_address) {
                        echo " - $email_address <br />";
                    }
                }
                
               
                
                // end mail script

                return json_encode(array('message' => 'Your password has been sent on your email id.', 'valid' => 1));
                exit;
            } else {

                // return error message
                return json_encode(array('message' => 'You have entered wrong email address please re-enter.', 'valid' => 0));
                exit;
            }
        }
    }

    // admin change password page
    public function showChangepassword() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $input = Input::all();
        if (!empty($input)) {
            $opassword = md5($input['opassword']);
            $password = md5($input['password']);
            $rules = array(
                'opassword' => 'required',
                'password' => 'required', // make sure the username field is not empty
                'cpassword' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/changepassword')
                                ->withErrors($validator) // send back all errors to the login form
                                ->withInput(Input::except('opassword'))
                                ->withInput(Input::except('password'))
                                ->withInput(Input::except('cpassword')); // send back the input (not the password) so that we can repopulate the form
            } else {
                // create our user data for the authentication
                $adminuser = DB::table('admins')
                        ->where('password', $opassword)
                        ->first();

                if (!empty($adminuser)) {

                    // check new password with old password
                    if ($password == $opassword) {

                        // return error message
                        Session::put('error_message', "You cannot put your old password for the new password!");
                        return Redirect::to('/admin/changepassword');
                    }

                    // update admin password
                    DB::table('admins')
                            ->where('id', Session::get("adminid"))
                            ->update(array('password' => $password));
                    
                    Session::put('success_message', "Password successfully changed");
                    return Redirect::to('/admin/changepassword');
                } else {

                    // return error message
                    Session::put('error_message', "Please enter correct old password");
                    return Redirect::to('/admin/changepassword');
                }
            }
        } else {
            return View::make('admin.changepassword');
        }
    }

    // admin edit profile page
    public function showEditprofile() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        // create our user data for the authentication
        $adminuser = DB::table('admins')
                ->where('id', Session::get('adminid'))
                ->first();

        $input = Input::all();
        if (!empty($input)) {

            // set validatin rules
            $rules = array(
                'name' => 'required',
                'email' => 'required|email',
                'username' => 'required|alpha_num',
                'phone' => 'required',
                'address' => 'required',
                'maintenance' => 'required',
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/admin/editprofile')
                                ->withErrors($validator)->withInput(Input::all());
            } else {

                // update admin profile
                $data = array(
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'username' => $input['username'],
                    'phone' => $input['phone'],
                    'address' => $input['address'],
                    'maintenance' => $input['maintenance'],
                );
                DB::table('admins')
                        ->where('id', Session::get("adminid"))
                        ->update($data);
                Session::put('success_message', "Profile Information is successfully updated.");
                return Redirect::to('/admin/editprofile');
            }
        } else {
            return View::make('admin.editprofile')->with('detail', $adminuser);
        }
    }
    
        public function showTimesettings() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        // create our user data for the authentication
        $adminuser = DB::table('admins')
                ->where('id', Session::get('adminid'))
                ->first();

        $input = Input::all();
        if (!empty($input)) {

            // set validatin rules
            $rules = array(
                'customer_time' => 'required',
                'caterer_time' => 'required',
                'courier_time' => 'required',
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/admin/timeSettings')
                                ->withErrors($validator)->withInput(Input::all());
            } else {

                // update admin profile
                $data = array(
                    'customer_time' => $input['customer_time'],
                    'caterer_time' => $input['caterer_time'],
                    'courier_time' => $input['courier_time'],
                );
                DB::table('admins')
                        ->where('id', Session::get("adminid"))
                        ->update($data);
                Session::put('success_message', "Time Settings is successfully updated.");
                return Redirect::to('/admin/timeSettings');
            }
        } else {
            return View::make('admin.timesettings')->with('detail', $adminuser);
        }
    }
    
    
     // admin edit profile page
    public function showAdmintax() {
        
        
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        // create our user data for the authentication
        $adminuser = DB::table('admins')
                ->where('id', Session::get('adminid'))
                ->first();

        $input = Input::all();
        if (!empty($input)) {

            // set validatin rules
            $rules = array(
                'tax' => 'required',
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/admin/admintax')
                                ->withErrors($validator)->withInput(Input::all());
            } else {
//                echo "<pre>";
//                print_r($input);
//                exit;
                // update admin profile
                $data = array(
                    'tax' => $input['tax'],
                    'is_tax' => $input['is_tax'],
                );
                DB::table('admins')
                        ->where('id', Session::get("adminid"))
                        ->update($data);
                Session::put('success_message', "Tax Information is successfully updated.");
                return Redirect::to('/admin/admintax');
            }
        } else {
            return View::make('/admin/admintax')->with('detail', $adminuser);
        }
    }
     // admin edit profile page
    public function showAdmincommission() {
        
        
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        // create our user data for the authentication
        $adminuser = DB::table('admins')
                ->where('id', Session::get('adminid'))
                ->first();

        $input = Input::all();
        if (!empty($input)) {

            // set validatin rules
            $rules = array(
                'commission' => 'required',
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/admin/admincommission')
                                ->withErrors($validator)->withInput(Input::all());
            } else {
//                echo "<pre>";
//                print_r($input);
//                exit;
                // update admin profile
                $data = array(
                    'commission' => $input['commission'],
                    'is_commission' => $input['is_commission'],
                );
                DB::table('admins')
                        ->where('id', Session::get("adminid"))
                        ->update($data);
                Session::put('success_message', "Commission is successfully updated.");
                return Redirect::to('/admin/admincommission');
            }
        } else {
            return View::make('/admin/admincommission')->with('detail', $adminuser);
        }
    }
      public function showAdmindeliverycharge() {
        
        
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        // create our user data for the authentication
        $adminuser = DB::table('admins')
                ->where('id', Session::get('adminid'))
                ->first();

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
                return Redirect::to('/admin/admindeliverycharge')
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
                );
                DB::table('admins')
                        ->where('id', Session::get("adminid"))
                        ->update($data);
                Session::put('success_message', "Devilvery Charges is successfully updated.");
                return Redirect::to('/admin/admindeliverycharge');
            }
        } else {
            return View::make('/admin/admindeliverycharge')->with('detail', $adminuser);
        }
    }
    
    
      public function sitesetting() {
        
        
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        // create our user data for the authentication
        $adminuser = DB::table('site_settings')
                ->first();
        
//              echo "<pre>";
//                print_r($adminuser);
//                exit;
        $input = Input::all();
        if (!empty($input)) {
            
            // set validatin rules
            $rules = array(
                'title' => 'required',
                'url' => 'required',
                'mail_from' => 'required',
                'tagline' => 'required',
                'phone' => 'required',
                'paypal_url' => 'required',
                'paypal_email_address' => 'required',
               
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/admin/sitesetting')
                                ->withErrors($validator)->withInput(Input::all());
            } else {
//                echo "<pre>";
//                print_r($input);
//                exit;
                // update admin profile
                $data = array(
                    'title' => $input['title'],
                    'url' => $input['url'],
                    'mail_from' => $input['mail_from'],
                    'tagline' => $input['tagline'],
                    'phone' => $input['phone'],
                    'facebook_link' => $input['facebook_link'],
                    'twitter_link' => $input['twitter_link'],
                    'instagram_link' => $input['instagram_link'],
                    'paypal_url' => $input['paypal_url'],
                    'paypal_email_address' => $input['paypal_email_address'],
//                    'linkedin_link' => $input['linkedin_link'],
//                    'pinterest' => $input['pinterest'],
                );
//                 echo "<pre>";
//                print_r($data);
//                exit;
                DB::table('site_settings')
                        ->where('id', 1)
                        ->update($data);
                Session::put('success_message', "Site Configuration is successfully updated.");
                return Redirect::to('/admin/sitesetting');
            }
        } else {
            return View::make('/admin/sitesetting')->with('detail', $adminuser);
        }
    }
    
      public function changelogo() {
        
        
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        // create our user data for the authentication
        $adminuser = DB::table('site_settings')
                ->first();
        
//              echo "<pre>";
//                print_r($adminuser);
//                exit;
        $input = Input::all();
        if (!empty($input)) {
             $old_logo = $input['old_logo'];
             $old_favicon = $input['old_favicon'];
//              echo "<pre>";
//                print_r($input);
//                exit;
            // set validatin rules
            $rules = array(
               'logo' => 'mimes:jpeg,png,jpg',
               'favicon' => 'mimes:ico',
               
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/admin/changelogo')
                                ->withErrors($validator)->withInput(Input::all());
            } else {
//                echo "<pre>";
//                print_r($input);
//                exit;
                // update admin profile
                 if (Input::hasFile('logo')) {
                    $file = Input::file('logo');
                    $profileImageName = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_LOGO_IMAGE_PATH, time() . $file->getClientOriginalName());

                    @unlink(UPLOAD_LOGO_IMAGE_PATH . $old_logo);
                } else {
                    $profileImageName = $old_logo;
                }
                 if (Input::hasFile('favicon')) {
                    $fileC = Input::file('favicon');
                    $faviconImageName = time() . $fileC->getClientOriginalName();
                    $fileC->move(UPLOAD_LOGO_IMAGE_PATH, time() . $fileC->getClientOriginalName());

                    @unlink(UPLOAD_LOGO_IMAGE_PATH . $old_favicon);
                } else {
                    $faviconImageName = $old_favicon;
                }
                $data = array(
                    'logo' => $profileImageName,
                    'favicon' => $faviconImageName,
                   
                );
//                 echo "<pre>";
//                print_r($data);
//                exit;
                DB::table('site_settings')
                        ->where('id', 1)
                        ->update($data);
                Session::put('success_message', "Site Logo is successfully updated.");
                return Redirect::to('/admin/changelogo');
            }
        } else {
            return View::make('/admin/changelogo')->with('detail', $adminuser);
        }
    }

}
