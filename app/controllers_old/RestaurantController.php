<?php

class RestaurantController extends BaseController {
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

// --------------------------------------------------------------------
// Create slug for secure URL
    function createSlug($string) {
        $string = substr(strtolower($string), 0, 35);
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $return = strtolower(preg_replace($old_pattern, $new_pattern, $string)) . rand(111111, 9999999) . time();
        return $return;
    }

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

    public function showManagekitchenstaff() {
        $this->logincheck('user/kitchenstaff');
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

        //echo "<pre>"; print_r($userData);exit;
        // get my all addresses        
        $query = DB::table('users');
        $query->where('users.restaurant_id', $user_id)->where('users.user_type', 'KitchenStaff')
                ->select('users.*');
        $records = $query->orderBy('users.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Manage Kitchen Staff';
        $this->layout->content = View::make('/Restaurants/manage_kitchen_staff')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showAddkitchenstaff() {
        $this->logincheck('user/addkitchenstaff');
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
        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Add New Kitchen Staff';
        $this->layout->content = View::make('/Restaurants/add_kitchen_staff')
                ->with('userData', $userData);

        if (!empty($input)) {



//            echo "<pre>"; print_r($input);exit;

            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'email_address' => 'required|unique:users|email', // make sure the email address field is not empty
                'password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'cpassword' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'contact' => 'required'
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/user/addkitchenstaff')
                                ->withErrors($validator)->withInput(Input::all());
                exit;
            } else {

                $slug = $this->createUniqueSlug($input['first_name'], 'users');
                $data = array(
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'restaurant_id' => $user_id,
                    'email_address' => $input['email_address'],
                    'password' => md5($input['password']),
                    'activation_status' => 1,
                    'status' => '1',
                    'approve_status' => '1',
                    'contact' => $input['contact'],
                    'slug' => $slug,
                    'user_type' => "KitchenStaff",
                    'created' => date('Y-m-d H:i:s'),
                );

//                echo "<pre>"; print_r($data);exit;
                DB::table('users')->insert(
                        $data
                );

//                print_r($userData);



                $userEmail = $input['email_address'];

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account is successfully created by ' . $userData->first_name . ' Restaurant as Kitchen Staff. Below are your login credentials.',
                    'email' => $input['email_address'],
                    'password' => $input['password'],
                    'firstname' => $input['first_name'],
                    'restaurant_namee' => $userData->first_name
                );

                // print_r($mail_data);   exit;
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view

                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created by ' . $mail_data['restaurant_namee'] . ' admin as Kitchen Staff ');
                        });

                return Redirect::to('/user/kitchenstaff')->with('success_message', 'Kitchen Staff successfully added.');
                exit;
            }
        }
    }

    public function showEditkitchenstaff($slug = "") {
        $this->logincheck('user/editkitchenstaff/' . $slug);
        if (!Session::has('user_id')) {
            return Redirect::to('/');
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();



        // get menu item details
        $kitchenstaffdata = DB::table('users')
                ->where('slug', $slug)
                ->first();
        if (empty($kitchenstaffdata)) {

            // redirect to the menu page
            return Redirect::to('/user/kitchenstaff')->with('error_message', 'Something went wrong, please try after some time.');
        }



        $this->layout->title = TITLE_FOR_PAGES . 'Edit Kitchen Staff Details';
        $this->layout->content = View::make('/Restaurants/edit_kitchen_staff')
                ->with('userData', $userData)
                ->with('detail', $kitchenstaffdata);
        $input = Input::all();
        if (!empty($input)) {

            if (empty($input['password'])) {
                $rules = array(
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'contact' => 'required',
                );
            } else {
                $rules = array(
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                    'cpassword' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                    'contact' => 'required'
                );
            }


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/user/editaddress')
                                ->withErrors($validator)->withInput(Input::all());
            } else {
                if (empty($input['password'])) {
                    $data = array(
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'contact' => $input['contact'],
                        'modified' => date('Y-m-d H:i:s'),
                    );
                } else {
                    $data = array(
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'password' => md5($input['password']),
                        'contact' => $input['contact'],
                        'modified' => date('Y-m-d H:i:s'),
                    );
                }

                DB::table('users')
                        ->where('slug', $slug)
                        ->update($data);

                return Redirect::to('/user/kitchenstaff')->with('success_message', 'Kitchen Staff successfully updated.');
            }
        }
    }

    public function showDeletekitchenstaff($slug = null) {
        DB::table('users')->where('slug', $slug)->delete();
        return Redirect::to('/user/kitchenstaff')->with('success_message', 'Kitchen Staff deleted successfully');
    }

    //delivery person

    public function showManagedeliveryperson() {
        $this->logincheck('user/deliveryperson');
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

        //echo "<pre>"; print_r($userData);exit;
        // get my all addresses        
        $query = DB::table('users');
        $query->where('users.restaurant_id', $user_id)->where('users.user_type', 'DeliveryPerson')
                ->select('users.*');
        $records = $query->orderBy('users.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Manage Delivery Person';
        $this->layout->content = View::make('/Restaurants/manage_delivery_person')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showAdddeliveryperson() {
        $this->logincheck('user/adddeliveryperson');
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
        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Add New Delivery Person';
        $this->layout->content = View::make('/Restaurants/add_delivery_person')
                ->with('userData', $userData);

        if (!empty($input)) {

//            echo "<pre>"; print_r($input);exit;

            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'email_address' => 'required|unique:users|email', // make sure the email address field is not empty
                'password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'cpassword' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'contact' => 'required'
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/adddeliveryperson')
                                ->withErrors($validator)->withInput(Input::all());
            } else {

                $slug = $this->createUniqueSlug($input['first_name'], 'users');
                $data = array(
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'restaurant_id' => $user_id,
                    'email_address' => $input['email_address'],
                    'password' => md5($input['password']),
                    'activation_status' => 1,
                    'status' => '1',
                    'approve_status' => '1',
                    'contact' => $input['contact'],
                    'slug' => $slug,
                    'user_type' => "DeliveryPerson",
                    'created' => date('Y-m-d H:i:s'),
                );

                //echo "<pre>"; print_r($data);exit;
                DB::table('users')->insert(
                        $data
                );


                $userEmail = $input['email_address'];

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account is successfully created by ' . $userData->first_name . ' Restaurant as Delivery Person. Below are your login credentials.',
                    'email' => $input['email_address'],
                    'password' => $input['password'],
                    'firstname' => $input['first_name'],
                    'restaurant_name' => $userData->first_name,
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created by ' . $mail_data['restaurant_name'] . ' as Delivery Person');
                });

               
                return Redirect::to('/user/deliveryperson')->with('success_message', 'Delivery Person successfully added.');
                exit;
            }
        }
    }

    public function showEditdeliveryperson($slug = "") {
        $this->logincheck('user/editdeliveryperson/' . $slug);
        if (!Session::has('user_id')) {
            return Redirect::to('/');
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();



        // get menu item details
        $deliverypersondata = DB::table('users')
                ->where('slug', $slug)
                ->first();

//        echo '<pre>'; print_r($deliverypersondata);die;

        if (empty($deliverypersondata)) {
            // redirect to the menu page
            return Redirect::to('/user/deliveryperson')->with('error_message', 'Something went wrong, please try after some time.');
        }



        $this->layout->title = TITLE_FOR_PAGES . 'Edit Delivery Person Details';
        $this->layout->content = View::make('/Restaurants/edit_delivery_person')
                ->with('userData', $userData)
                ->with('detail', $deliverypersondata);

        $input = Input::all();

        if (!empty($input)) {

            if (empty($input['password'])) {
                $rules = array(
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'contact' => 'required',
                );
            } else {
                $rules = array(
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                    'cpassword' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                    'contact' => 'required'
                );
            }

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/user/editdeliveryperson')
                                ->withErrors($validator)->withInput(Input::all());
                exit;
            } else {
                if (empty($input['password'])) {
                    $data = array(
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'contact' => $input['contact'],
                        'modified' => date('Y-m-d H:i:s'),
                    );
                } else {
                    $data = array(
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'password' => md5($input['password']),
                        'contact' => $input['contact'],
                        'modified' => date('Y-m-d H:i:s'),
                    );
                }

                DB::table('users')
                        ->where('slug', $slug)
                        ->update($data);

                return Redirect::to('/user/deliveryperson')->with('success_message', 'Delivey Person successfully updated.');
                exit;
            }
        }
    }

    public function showDeletedeliveryperson($slug = null) {
        DB::table('users')->where('slug', $slug)->delete();
        return Redirect::to('/user/deliveryperson')->with('success_message', 'Kitchen Staff deleted successfully');
    }

    public function showLoadarea($id, $area_id = 0) {
        $options = "<option value=''>Area</option>";
        $record = Area::where("city_id", $id)->orderBy('name', 'asc')->lists('name', 'id');
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

?>