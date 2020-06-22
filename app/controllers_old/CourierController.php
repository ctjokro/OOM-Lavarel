<?php

class CourierController extends BaseController {
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
                ->where("user_type", "=", 'Courier')
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
                    Session::put('success_message', 'Courier(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    Session::put('success_message', 'Courier(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->delete();
                    Session::put('success_message', 'Courier(s) deleted successfully');
                    break;
            }
        }

        $separator = implode("/", $separator);

        // Get all the users
        $users = $query->orderBy('id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('Couriers/adminindex', compact('users'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_order() {
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
        $query = OrderCourier::sortable()
                ->select("order_courier.*", "users.first_name", "users.last_name", "orders.order_number")
                ->join('users', 'users.id', '=', 'order_courier.user_id')
                ->join('orders', 'orders.id', '=', 'order_courier.order_id')
                ->where("user_type", "=", 'Courier')
                ->where(function ($query) use ($search_keyword) {
            $query->where('first_name', 'LIKE', '%' . $search_keyword . '%')
            ->orwhere('last_name', 'LIKE', '%' . $search_keyword . '%')
            ->orwhere('email_address', 'LIKE', '%' . $search_keyword . '%')
            ->orwhere('order_number', 'LIKE', '%' . $search_keyword . '%');
        });


        $separator = implode("/", $separator);

        // Get all the users
        $orders = $query->orderBy('id', 'desc')
                ->sortable()
                ->paginate(10);


        // Show the page
        return View::make('Couriers/adminorder', compact('orders'))->with('search_keyword', $search_keyword)
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
                'contact_number' => 'required'
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/courier/admin_add')
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
                    'profile_image' => $profileImageName,
                    'activation_status' => 1,
                    'approve_status' => '1',
                    'status' => '1',
                    'slug' => $slug,
                    'user_type' => "Courier",
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('users')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();

                $userEmail = $input['email_address'];

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account has been successfully created by admin as Courier. Below are your login credentials.',
                    'email' => $input['email_address'],
                    'password' => $input['password'],
                    'firstname' => $input['first_name'] . ' ' . $input['last_name'],
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created by admin as Courier');
                });

                return Redirect::to('/admin/courier/admin_index')->with('success_message', 'Courier saved successfully.');
            }
        } else {
            return View::make('/Couriers/admin_add');
        }
    }

    public function showAdmin_addorder() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'order_id' => 'required',
                'user_id' => 'required',
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/courier/admin_addorder')
                                ->withErrors($validator)
                                ->withInput(Input::all());
            } else {



                $saveData = array(
                    'order_id' => $input['order_id'],
                    'user_id' => $input['user_id'],
                    'slug' => $this->createSlug('cservice'),
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('order_courier')->insert(
                        $saveData
                );


                DB::table('orders')
                        ->where('id', $input['order_id'])
                        ->update(array('is_courier' => 1)); // order request 
                $orderData = DB::table('orders')
                        ->where('orders.id', $input['order_id'])
                        ->first();

                // get Customer data
                $customerData = DB::table('users')
                        ->where('users.id', $orderData->user_id)
                        ->first();

                // get Cateter data
                $catererData = DB::table('users')
                        ->where('users.id', $orderData->caterer_id)
                        ->first();

                $adminuser = DB::table('admins')
                        ->where('id', '1')
                        ->first();

                $customerData = DB::table('users')
                        ->where('id', $orderData->user_id)
                        ->first();

                $courierData = DB::table('users')
                        ->where('id', $input['user_id'])
                        ->first();
                $cartItems = DB::table('order_item')->whereIn('order_id', explode(',', $orderData->id))->get(); // get cart menu of this order


                if (isset($orderData->delivery_charge) && $orderData->delivery_charge != '') {

                    $delivery_charge = $orderData->delivery_charge;
                    $delivery_type = $orderData->delivery_type;
                } else {
                    $delivery_charge = "0";
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


                        $menuData = DB::table('menu_item')
                                        ->where('id', $cartData->menu_id)->first();  // get menu data from menu table

                        $sub_total = $cartData->base_price * $cartData->quantity;
                        $total[] = $sub_total;

                        $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $menuData->item_name . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . number_format($cartData->base_price, 2) . ' ' . CURR . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . $cartData->quantity . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . number_format($sub_total, 2) . ' ' . CURR . '
                    </td>
                    </tr>';
                    }
                }

                $catererData = DB::table('users')
                        ->where('id', $orderData->caterer_id)
                        ->first();
                $gTotal = array_sum($total);
                if ($orderData->tax) {
                    $tax = $orderData->tax;
                } else {
                    $tax = 0;
                }

                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd; font-weight:normal;">
                    Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                    ' . number_format($gTotal, 2) . ' ' . CURR . '
                    </td>
                    </tr>';
                if ($adminuser->is_tax) {
                    $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Tax
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . number_format($tax, 2) . ' ' . CURR . '
                    </td>
                    </tr>';
                    $gTotal = $gTotal + $tax;
                }
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . number_format($delivery_charge, 2) . ' ' . CURR . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $delivery_charge;
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . number_format($gTotal, 2) . ' ' . CURR . '
                    </td>
                    </tr>';

                $orderContent .= '</table>';

                /*                 * * send mail to courier ** */
                $courier_mail_data = array(
                    'text' => 'Order assinged by admin on ' . SITE_TITLE . '.',
                    'customerContent' => $customerContent,
                    'orderContent' => $orderContent,
                    'sender_email' => $courierData->email_address,
                    'firstname' => $courierData->first_name . ' ' . $courierData->last_name,
                );

                // return View::make('emails.template')->with($courier_mail_data); // to check mail template data to view

                Mail::send('emails.template', $courier_mail_data, function($message) use ($courier_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($courier_mail_data['sender_email'], $courier_mail_data['firstname'])->subject('Order assinged by admin on ' . SITE_TITLE);
                });



                return Redirect::to('/admin/courier/admin_order')->with('success_message', 'Order send to courier service successfully.');
            }
        } else {

            return View::make('/Couriers/admin_addorder');
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
                'last_name' => 'required', // make sure the last name field is not empty
                'profile_image' => 'mimes:jpeg,png,jpg',
                'contact' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/courier/Admin_edituser/' . $user->slug)
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
                    'contact' => $input['contact'],
                    'address' => $input['address'],
                    'profile_image' => $profileImageName,
                    'status' => '1',
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);


                return Redirect::to('/admin/courier/admin_index')->with('success_message', 'Courier profile details updated successfully.');
            }
        } else {



            return View::make('/Couriers/admin_edituser')->with('detail', $user);
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
                    'text' => 'Your account has been successfully confirmed by ' . SITE_TITLE . ' as Courier.',
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

            return Redirect::back()->with('success_message', 'Courier(s) activated successfully');
        }
    }

    public function showAdmin_deactiveuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Courier(s) deactivated successfully');
        }
    }

    public function showAdmin_activemarkuser($slug = null) {
        if (!empty($slug)) {

            DB::table('users')
                    ->where('slug', $slug)
                    ->update(['mark_default' => 1]);

            DB::table('users')
                    ->where('slug', '!=', $slug)
                    ->update(['mark_default' => 0]);

            return Redirect::back()->with('success_message', 'Courier(s) marked as default successfully');
        }
    }

    public function showAdmin_deactivemarkuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')
                    ->where('slug', $slug)
                    ->update(['mark_default' => 0]);

            return Redirect::back()->with('success_message', 'Courier(s) removed from marked successfully');
        }
    }

    public function showAdmin_deleteuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')->where('slug', $slug)->delete();
            return Redirect::to('/admin/courier/admin_index')->with('success_message', 'Courier deleted successfully');
        }
    }

    public function showAdmin_deleteorder($slug = null) {
        if (!empty($slug)) {
            DB::table('order_courier')->where('slug', $slug)->delete();
            return Redirect::to('/admin/courier/admin_order')->with('success_message', 'Courier service order deleted successfully.');
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

}
