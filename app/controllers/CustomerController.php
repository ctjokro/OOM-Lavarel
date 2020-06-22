<?php

class CustomerController extends BaseController {
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
                ->where("user_type", "=", 'Customer')
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
                    Session::put('success_message', 'Customer(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    Session::put('success_message', 'Customer(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->delete();
                    Session::put('success_message', 'Customer(s) deleted successfully');
                    break;
            }
        }

        $separator = implode("/", $separator);

        // Get all the users
        $users = $query->orderBy('id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('Customers/adminindex', compact('users'))->with('search_keyword', $search_keyword)
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
                'last_name' => 'required', // make sure the last name field is not empty
                'email_address' => 'required|unique:users|email', // make sure the email address field is not empty
                'password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'confirm_password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'profile_image' => 'mimes:jpeg,png,jpg',
                'contact_number' => 'required',
                'city' => 'required',
                'area' => 'required',
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/customer/admin_add')
                                ->withErrors($validator)
                                ->withInput(Input::all());
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
                    'last_name' => $input['last_name'],
                    'contact' => $input['contact_number'],
                    'address' => $input['address'],
                    'email_address' => $input['email_address'],
                    'password' => md5($input['password']),
                    'city' => $input['city'],
                    'area' => $input['area'],
                    'approve_status' => '1',
                    'profile_image' => $profileImageName,
                    'activation_status' => 1,
                    'status' => '1',
                    'slug' => $slug,
                    'user_type' => "Customer",
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('users')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();
                $arr = array(
                    'mon' => array(0 => '00:00 am-00:00 pm'),
                    'tue' => array(0 => '00:00 am-00:00 pm'),
                    'wed' => array(0 => '00:00 am-00:00 pm'),
                    'thu' => array(0 => '00:00 am-00:00 pm'),
                    'fri' => array(0 => '00:00 am-00:00 pm'),
                    'sat' => array(0 => '00:00 am-00:00 pm'),
                    'sun' => array(0 => '00:00 am-00:00 pm'),
                );

                $data = array(
                    'user_id' => $id,
                    'open_days' => 'mon,tue,wed,thu,fri,sat,sun',
                    //'delivery_time' => serialize($arr),
                    'status' => '1',
                    'created' => date('Y-m-d H:is')
                );
                DB::table('opening_hours')
                        ->insert($data);

                $userEmail = $input['email_address'];

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account has been successfully created by admin as Customer. Below are your login credentials.',
                    'email' => $input['email_address'],
                    'password' => $input['password'],
                    'firstname' => $input['first_name'] . ' ' . $input['last_name'],
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created by admin as Customer');
                });

                return Redirect::to('/admin/customer/admin_index')->with('success_message', 'Customer saved successfully.');
            }
        } else {
            return View::make('/Customers/admin_add');
        }
    }

    public function showWallethistory($slug = null) { 
        
        
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        $user_slug = $slug;
        $userData = DB::table('users')
                ->where('slug', $user_slug)
                ->first();
        
//       echo '<prE>'; print_r($userData->id);die;
        
        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }

        $query = Wallet::sortable()
                ->where("user_id", "=", $userData->id)
                ->where(function ($query) use ($search_keyword) {
            $query->where('trans_id', 'LIKE', '%' . $search_keyword . '%')
            ->orwhere('display_amount', 'LIKE', '%' . $search_keyword . '%');
        });
            
        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
            switch ($action) {
                case "Activate":
                    DB::table('wallets')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));
                    Session::put('success_message', 'Wallet(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('wallets')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    Session::put('success_message', 'Wallet(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('wallets')
                            ->whereIn('id', $idList)
                            ->delete();
                    Session::put('success_message', 'Wallet(s) deleted successfully');
                    break;
            }
        }
        $separator = implode("/", $separator);
        
//       Get all the users
        $wallet = $query->orderBy('id', 'desc')->sortable()->paginate(10);
        
       // echo '<pre>'; print_r($wallet);die;
        
// Show the page
        return View::make('Customers/wallet', compact('wallet'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo)
                        ->with('user_slug', $userData->slug);
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
                'last_name' => 'required', // make sure the last name field is not empty
                'profile_image' => 'mimes:jpeg,png,jpg',
                'contact' => 'required',
                'city' => 'required',
                'area' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/customer/Admin_edituser/' . $user->slug)
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
                    'last_name' => $input['last_name'],
                    'city' => $input['city'],
                    'area' => $input['area'],
                    'contact' => $input['contact'],
                    'address' => $input['address'],
                    'profile_image' => $profileImageName,
                    'status' => '1',
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);


                return Redirect::to('/admin/customer/admin_index')->with('success_message', 'Customer profile details updated successfully.');
            }
        } else {



            return View::make('/Customers/admin_edituser')->with('detail', $user);
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
                    'text' => 'Your account has been successfully confirmed by ' . SITE_TITLE . ' as Customer.',
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

            return Redirect::back()->with('success_message', 'Customer(s) activated successfully');
        }
    }

    public function showAdmin_deactiveuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Customer(s) deactivated successfully');
        }
    }

    public function showAdmin_deleteuser($slug = null) {
        if (!empty($slug)) {
            $user = DB::table('users')
                            ->where('slug', $slug)->first();
            $user_id = $user->id;

            DB::table('menu_item')->where('user_id', $user_id)->delete();
            DB::table('favorite_menu')->where('user_id', $user_id)->delete();
            DB::table('main_order')->where('user_id', $user_id)->delete();
            DB::table('orders')->where('user_id', $user_id)->delete();
            DB::table('order_item')->where('user_id', $user_id)->delete();
            DB::table('reviews')->where('user_id', $user_id)->delete();
            DB::table('user_reviews')->where('user_id', $user_id)->delete();
            DB::table('reset_link')->where('user_id', $user_id)->delete();

            DB::table('users')->where('slug', $slug)->delete();
            return Redirect::to('/admin/customer/admin_index')->with('success_message', 'Customer deleted successfully');
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

    public function showManageaddress() {
        $this->logincheck('user/manageaddresses');
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
        $query = DB::table('addresses');
        $query->where('addresses.user_id', $user_id)
                ->select('addresses.*');
        $records = $query->orderBy('addresses.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Manage Addresses';
        $this->layout->content = View::make('/Customers/manageaddress')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showAddaddress() {
        $this->logincheck('user/addaddress');
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

        // get all posted input
        $input = Input::all();
        $lats = $this->getlatlong();
        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Add New Address';
        $this->layout->content = View::make('/Customers/addaddress')
                ->with('userData', $userData)
                ->with('lats', $lats);

        if (!empty($input)) {

            $rules = array(
                'address_title' => 'required',
                'address_type' => 'required',
                'city' => 'required',
                'area' => 'required',
                'street_name' => 'required',
                'phone_number' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/addmenu')
                                ->withErrors($validator)->withInput(Input::all());
            } else {

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

                DB::table('addresses')->insert(
                        $data
                );

                return Redirect::to('/user/manageaddresses')->with('success_message', 'Address successfully added.');
            }
        }
    }

    public function showEditaddress($slug = "") {
        $this->logincheck('user/editaddress/' . $slug);
        if (!Session::has('user_id')) {
            return Redirect::to('/');
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get menu item details
        $menudata = DB::table('addresses')
                ->where('slug', $slug)
                ->first();
        if (empty($menudata)) {

            // redirect to the menu page
            return Redirect::to('/user/manageaddresses')->with('error_message', 'Something went wrong, please try after some time.');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Edit Address Details';
        $this->layout->content = View::make('/Customers/editaddress')
                ->with('userData', $userData)
                ->with('detail', $menudata);
        $input = Input::all();


        if (!empty($input)) {
            $rules = array(
                'address_title' => 'required',
                'address_type' => 'required',
                'city' => 'required',
                'area' => 'required',
                'street_name' => 'required',
                'phone_number' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/user/editaddress')
                                ->withErrors($validator)->withInput(Input::all());
            } else {

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
                        ->where('slug', $slug)
                        ->update($data);

                return Redirect::to('/user/manageaddresses')->with('success_message', 'Address successfully updated.');
            }
        }
    }

    public function showDeleteaddress($slug = null) {
        DB::table('addresses')->where('slug', $slug)->delete();
        return Redirect::to('/user/manageaddresses')->with('success_message', 'Address deleted successfully');
    }

    public function showLoadarea($id, $area_id = 0) {
        $options = "<option value=''>Area</option>";
        $record = Area::where("status", 1)->where("city_id", $id)->orderBy('name', 'asc')->lists('name', 'id');
        if (!empty($record)) {
            foreach ($record as $key => $val)
                $options .= "<option value='$key' " . ($area_id == $key ? "selected='selected'" : "") . ">" . ucfirst($val) . "</option>";
        }
        return $options;
    }

    public function showLoadfromarea($id, $area_id = 0) {
        $options = "<option value=''>Area</option>";
        $record = Area::where("city_id", $id)->orderBy('name', 'asc')->lists('name', 'id');
        if (!empty($record)) {
            foreach ($record as $key => $val)
                $options .= "<option value='$key' " . ($area_id == $key ? "selected='selected'" : "") . ">" . ucfirst($val) . "</option>";
        }
        return $options;
    }

    public function showLoadtoarea($id, $area_id = 0) {
        $options = "<option value=''>Area</option>";
        $record = Area::where("city_id", $id)->orderBy('name', 'asc')->lists('name', 'id');
        if (!empty($record)) {
            foreach ($record as $key => $val)
                $options .= "<option value='$key' " . ($area_id == $key ? "selected='selected'" : "") . ">" . ucfirst($val) . "</option>";
        }
        return $options;
    }

}
