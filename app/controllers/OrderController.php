<?php

use Moltin\Cart\Cart;
use Moltin\Cart\Storage\CartSession;
use Moltin\Cart\Identifier\Cookie;

class OrderController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default User Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add rroute:
      |
      |	Route::get('/', 'HomeController@showWelcome');
      |
     */

    protected $layout = 'layouts.default';

    public function logincheck($url) {
        if (!Session::has('user_id')) {
            Session::put('return', $url);
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
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
        $query = Order::sortable()
                ->where(function ($query) use ($search_keyword) {
                    $query->where('order_number', 'LIKE', '%' . $search_keyword . '%');
                });

//        $query->join('users as u1', DB::raw('u1.id'), '=', 'orders.courier_id');
//        $query->join('users as u2', DB::raw('u2.id'), '=', 'orders.user_id');
//        $query->join('users as u3', DB::raw('u3.id'), '=', 'orders.caterer_id');
        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
        }

        $separator = implode("/", $separator);

        // Get all the users
        $mainorders = $query->orderBy('orders.id', 'desc')->sortable()->paginate(10);


        // Show the page
        return View::make('Orders/adminindex', compact('mainorders'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdminSub_view($slug = null) {
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
        $query = Order::sortable()
                ->where(function ($query) use ($search_keyword) {
                    $query->where('order_number', 'LIKE', '%' . $search_keyword . '%');
                });

        if ($slug != "") {
            $mainData = DB::table('main_order')
                    ->where('slug', $slug)
                    ->first();
            //  print_r($mainData);

            $query->whereIn('orders.order_number', explode(',', $mainData->order_id));
        }


        // $query->join('users as u1', DB::raw('u1.id'), '=', 'orders.caterer_id');
        $query->join('users', 'users.id', '=', 'orders.caterer_id')
                ->select('orders.*', 'users.first_name');


//        $query->join('users as u2', DB::raw('u2.id'), '=', 'orders.user_id');
//        $query->join('users as u3', DB::raw('u3.id'), '=', 'orders.caterer_id');
        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
        }

        $separator = implode("/", $separator);

        // Get all the users
        $orders = $query->orderBy('orders.id', 'desc')->sortable()->paginate(10);


        // Show the page
        return View::make('Orders/adminsubindex', compact('orders'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo)
                        ->with('slug', $slug);
    }

    public function showAdmin_deleteuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')->where('slug', $slug)->delete();
            return Redirect::to('/admin/order/admin_index')->with('success_message', 'Order deleted successfully');
        }
    }

    public function showAdmin_view($slug = null) {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $order = DB::table('orders')
                        ->where('slug', $slug)->first();


        $orderData = DB::table('orders')
                ->where('slug', $slug)
                ->first();

//         $main_ordershopData = DB::table('main_order')
//           // ->whereIn('order_id', $orders)
//            ->whereRaw("FIND_IN_SET('$orderData->order_number',order_id)")
//           
//            ->get();


        if (empty($orderData)) {
            return Redirect::to(
                            '/admin/order/admin_index');
        }

        $user_id = $orderData->caterer_id;
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();

        $customerData = DB::table('users')
                ->where('id', $orderData->user_id)
                ->first();

        $input = Input::all();


        $cartItems = DB::table('order_item')
                ->whereIn('menu_id', explode(',', $orderData->order_item_id))
                ->where('order_id', $orderData->id)
                ->get(); // get cart menu of this order

        if (!empty($input)) {
            $inputStatus = $input['status'];
            switch ($input['status']) {

                case "Confirm":
                    $orderStatus = "Confirmed";
                    $subjectMessageCustomer = "Your order has been confirmed by " . SITE_TITLE;
                    $subjectMessageRestaurant = "An order has been confirmed by " . SITE_TITLE;
                    $subjectMessageAdmin = "An order has been confirmed by " . SITE_TITLE;
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

                case "Delivered":
                    $orderStatus = "Delivered";
                    $subjectMessageCustomer = "Your order has been delivered by " . SITE_TITLE;

                    // check courier conditions start
//                    $courierData = DB::table('users')
//                                    ->where('mark_default', '1')->first();
//                    if (!empty($courierData)) {
//                        DB::table('orders')
//                                ->where('id', $orderData->id)
//                                ->update(['status' => 'Delivered']); // update order status
//                    }
                    // check courier conditions end
                    break;


                case "Modify":
                    $orderStatus = "Modify";
                    $subjectMessageCustomer = "Restaurant requested to modify your order on " . SITE_TITLE;

                    break;
                case "Cancel":
                    $orderStatus = "Cancelled";
                    DB::table('orders')
                    ->where('id', $orderData->id)
                    ->update(['cancel_reason' => $input['reason']]);
                    $subjectMessageCustomer = "You order has been cancelled by  " . SITE_TITLE;
                    $subjectMessageRestaurant = "You have cancelled order on " . SITE_TITLE;
                    $subjectMessageAdmin = "Restaurant cancelled order on " . SITE_TITLE;
                    break;
            }


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
                    ' . App::make("HomeController")->numberformat($cartData->base_price, 2) . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . $cartData->quantity . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . App::make("HomeController")->numberformat($sub_total, 2) . '
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
                    <td valig n = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
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
                    ' . App::make("HomeController")->numberformat($tax, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $tax;
            }
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($delivery_charge, 2) . '
                    </td>
                    </tr>';
            $gTotal = $gTotal + $delivery_charge;
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

            $orderContent .= '</table>';

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

                /*                 * * send mail to customer ** */
                $mail_courier_data = array(
                    'text' => $subjectMessageCouieer,
                    'customerContent' => $customerContent, 'orderContent' => $orderContent, 'orderStatus' => $orderStatus,
                    'sender_email' => $courierData->email_address,
                    'firstname' => $courierData->first_name . ' ' . $courierData->last_name,
                );

//                return View::make('emails.template')->with($mail_courier_data); // to check mail template data to view

                Mail::send('emails.template', $mail_courier_data, function($message) use ($mail_courier_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($mail_courier_data['sender_email'], $mail_courier_data ['firstname'])->subject($mail_courier_data['text']);
                        });
            }

            if ($input['status'] == 'Delivered') {
                /*                 * * send mail to customer ** */
                $mail_data = array(
                    'text' => $subjectMessageCustomer,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $customerData->email_address,
                    'firstname' => $customerData->first_name . ' ' . $customerData->last_name,);

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view

                Mail::send('emails.template', $mail_data, function($message ) use ( $mail_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject($mail_data['text']);
                        });
            } else {

                /*                 * * send mail to customer ** */
                $mail_data = array(
                    'text' => $subjectMessageCustomer,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $customerData->email_address,
                    'firstname' => $customerData->first_name . ' ' . $customerData->last_name,);

//            return View::make('emails.template')->with($mail_data); // to check mail template data to view

                Mail::send('emails.template', $mail_data, function($message ) use ( $mail_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject($mail_data['text']);
                        });

                /*                 * * send mail to restaurant ** */
                $caterer_mail_data = array(
                    'text' => $subjectMessageRestaurant,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $catererData->email_address,
                    'firstname' => $catererData->first_name . ' ' . $catererData->last_name,
                );

//            return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

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

//            return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

                Mail::send('emails.template', $admin_mail_data, function($message) use($admin_mail_data) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($admin_mail_data['sender_email'], 'Admin')->subject($admin_mail_data['text']);
                        });
            }



            DB::table('orders')
            ->where('id', $orderData->id)
            ->update(['status' => $inputStatus]);
            return Redirect::to('/admin/order/view/' . $slug)->with('success_message', 'Order status changed successfully.');
        } else {

            return View::make('/Orders/admin_view')
                            ->with('userData', $userData)
                            ->with('orderData', $orderData)->with('detail', $order);
            //->with('main_ordershopData', $main_ordershopData);
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

    public function showView($slug = null) {
        $this->logincheck('order/view/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }
        if ($this->chkUserType('Customer') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Order Details';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $orderData = DB::table('orders')
                ->where('slug', $slug)
                ->first();

        $main_ordershopData = DB::table('main_order')
                // ->whereIn('order_id', $orders)
                ->whereRaw("FIND_IN_SET('$orderData->order_number',order_id)")
                ->get();
        //  print_r($main_ordershopData); exit;
        if (empty($orderData)) {
            return Redirect::to('/user/myaccount');
        }
        
        $timezone_currency = DB::table('timezone_currency')
                        ->where("timezone_currency.user_id", "=", $orderData->caterer_id)
                        ->first();
                       
        $currency = $timezone_currency->currency;

        $this->layout->content = View::make('/Orders/view')
                ->with('userData', $userData)
                ->with('orderData', $orderData)
                ->with('main_ordershopData', $main_ordershopData)
                ->with('currency', $currency);
    }

    public function reorder($slug = null) {
        $cart = new Cart(new CartSession, new Cookie);
        $cart->destroy();

        $this->logincheck('restaurants/reorder/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }
        if ($this->chkUserType('Customer') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Order Details';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $orderData = DB::table('orders')
                ->where('slug', $slug)
                ->first();





        if (empty($orderData)) {
            return Redirect::to('/user/myaccount');
        } else {
            $orderItem = DB::table('order_item')
                    ->where('order_id', $orderData->id)
                    ->get();
            //  echo "<pre>"; print_r($orderItem); exit;
            if ($orderItem) {
                $mesage = "";

                foreach ($orderItem as $orderItemVal) {

                    $menu_item = DB::table('menu_item')
                            ->where('id', $orderItemVal->menu_id)
                            ->first();
                    if ($menu_item) {
//                         echo "<pre>"; print_r($orderItemVal); exit;
                        if (isset($orderItemVal->addon_id) && $orderItemVal->addon_id != "") {
                            $cart->insert(array(
                                'id' => $menu_item->id,
                                'name' => $menu_item->item_name,
                                'price' => $menu_item->price,
                                'quantity' => $orderItemVal->quantity,
                                'caterer_id' => $menu_item->user_id,
                                'variant_type' => ($orderItemVal->variant_id) ? $orderItemVal->variant_id : NULL,
                                'addons' => ($orderItemVal->addon_id) ? $orderItemVal->addon_id : NULL,
                            ));
                        } else {
                            $cart->insert(array(
                                'id' => $menu_item->id,
                                'name' => $menu_item->item_name,
                                'price' => $menu_item->price,
                                'quantity' => $orderItemVal->quantity,
                                'caterer_id' => $menu_item->user_id,
                                'variant_type' => ($orderItemVal->variant_id) ? $orderItemVal->variant_id : NULL,
                                'addons' => ($orderItemVal->addon_id) ? $orderItemVal->addon_id : NULL,
                            ));
                        }
                    } else {
                        if ($mesage == "") {
                            $mesage = "Note: The menu items that have been deleted by the restaurant will not be added in the cart.";
                        }
                    }
                }
            }
            if ($mesage != "") {
                return Redirect::to('/order/confirm')->with('error_message', $mesage);
            } else {
                return Redirect::to('/order/confirm');
            }
        }
    }

    public function showreceivedview($slug = null) {

        $this->logincheck('order/receivedview/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Restaurant') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Order Details';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        
        $timezone_currency = DB::table('timezone_currency')
                        ->where("timezone_currency.user_id", "=", $user_id)
                        ->first();
                       
        $currency = $timezone_currency->currency;
        
        $orderData = DB::table('orders')
                ->where('slug', $slug)
                ->first();

//        echo '<pre>';print_r($orderData);die;


        $numberofOrder = 1;

//        $main_ordershopData = DB::table('main_order')
//           // ->whereIn('order_id', $orders)
//            ->whereRaw("FIND_IN_SET('$orderData->order_number',order_id)")
//           
//            ->get();
//         
//        $numberofOrder = count(explode(',',$main_ordershopData[0]->order_id));

        $tax = $orderData->tax;
        $delivery_charge = $orderData->delivery_charge;
        $discount = $orderData->discount;

        if (empty($orderData)) {
            return Redirect::to('/user/myaccount');
        }


        $this->layout->content = View::make('/Orders/showreceivedview')
                ->with('userData', $userData)
                ->with('orderData', $orderData)
                ->with('currency', $currency);



//        die;
        //->with('main_ordershopData', $main_ordershopData);

        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();

        $customerData = DB::table('users')
                ->where('id', $orderData->user_id)
                ->first();

        $input = Input::all();

        // echo "<pre>"; print_r($orderData); exit;
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
                      $customerDatarestro = DB::table('users')
                        ->where('id', $orderData->caterer_id)
                        ->first();
                $restaurantName = $customerDatarestro->first_name;
                    $orderStatus = "Delivered";
                    $subjectMessageCustomer = "Your order has been delivered by " .$restaurantName." on " . SITE_TITLE;
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
                if ($input['comment'] != "") {

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
            /*             * * send mail to customer ** */
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


                /*                 * * send mail to customer ** */
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
                    'order_id' =>$orderData->id
                );

                if ($customerData->device_type == 'Android') {
                    $this->send_fcm_notify($customerData->device_id, $push_message, 'Customer');
                } else if ($customerData->device_type == 'iPhone') {
                    $this->send_iphone_notification($customerData->device_id, $push_message, 'Customer');
                }
            }
            /*             * * send mail to restaurant ** */

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






            /*             * * send mail to admin ** */

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


            /*             * **************************************** */

            if (!empty($input['kitchen_staff_id'])) {
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
                    'order_id' =>$orderData->id
                );

                if ($kitchenStaffInfo->device_type == 'Android') {
                    $this->send_fcm_notify($kitchenStaffInfo->device_id, $push_message, 'KitchenStaff');
                } else if ($kitchenStaffInfo->device_type == 'iPhone') {
                    $this->send_iphone_notification($kitchenStaffInfo->device_id, $push_message, 'KitchenStaff');
                }
                
                
            }

            if ($input['delivery_person_id']) {
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
                    "type" => 'kitchen_staff_order_assign',
                    "message" => 'Your have been assigned a new order.',
                    'customer_id' => $customerData->id,
                    'restaurant_id' => $catererData->id,
                    'delivery_person_id' => $deliveryPersonId,
                    'order_id' =>$orderData->id

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


            return Redirect::to('/order/receivedview/' . $slug)->with('success_message', 'Order status changed successfully.');
        }
    }

    function cancelOrder($orderSlug = NULL) {
        $this->layout = false;

        // get order data

        $orderData = DB::table('orders')
                ->where('slug', $orderSlug)
                ->first();
        $numberofOrder = 1;

//        $main_ordershopData = DB::table('main_order')
//           // ->whereIn('order_id', $orders)
//            ->whereRaw("FIND_IN_SET('$orderData->order_number',order_id)")
//           
//            ->get();
//         
//        $numberofOrder = count(explode(',',$main_ordershopData[0]->order_id));

        $tax = $orderData->tax;
        $delivery_charge = $orderData->delivery_charge;
        $discount = $orderData->discount;


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


        $numberofOrder = 1;


        $cartItems = DB::table('order_item')->whereIn('order_id', explode(',', $orderData->id))->get(); // get cart menu of this order
        if (!empty($orderData)) {

            $orderStatus = "Cancel";
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

            $customerCty = $orderContent . $newcnn . '</table>';


            //echo $customerCty; exit;


            $mail_data = array('text' => 'Order status changed successfully as ' . $orderStatus . '  on ' . SITE_TITLE . '.',
                'orderContent' => $customerCty,
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



            //echo $orderContent; exit;

            /*             * * send mail to customer ** */


            //return View::make('emails.template')->with($mail_data); // to check mail template data to view

            Mail::send('emails.template', $mail_data, function($message) use($mail_data) {
                        $message->setSender(array(
                            MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject('Order status changed successfully');
                    });

            /*             * * send mail to restaurant ** */
            $caterer_mail_data = array(
                'text' => 'Order status changed successfully as ' . $orderStatus . '  on ' . SITE_TITLE . '.', 'orderContent' => $orderContent,
                'sender_email' => $catererData->email_address,
                'firstname' => $catererData->first_name . ' ' . $catererData->last_name,
            );

            //   return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

            Mail::send('emails.template', $caterer_mail_data, function($message) use($caterer_mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($caterer_mail_data['sender_email']
                                , $caterer_mail_data['firstname'])->subject('Order cancelled successfully');
                    });


            /*             * * send mail to admin ** */

            $admin_mail_data = array(
                'text' => 'Order status changed successfully as ' . $orderStatus . '  on ' . SITE_TITLE . '.',
                'customerContent' => $customerContent,
                'orderContent' => $orderContent,
                'sender_email' => $adminuser->email,
                'firstname' => "Admin",
            );

            // return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

            Mail::send('emails.template', $admin_mail_data, function($message) use ($admin_mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($admin_mail_data['sender_email'], 'Admin')->subject('Order cancelled successfully');
                    });


            DB::table('orders')
            ->where('id', $orderData->id)
            ->update(['status' => $orderStatus, 'cancel_by_user' => '1']);
        }
        return Redirect::to('/order/view/' . $orderSlug)->with('success_message', 'Order cancelled successfully.');
    }

    public function showcourierview($slug = null) {

        $this->logincheck('order/courierview/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Courier') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Order Details';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $details = DB::table('order_courier')
                ->where('slug', $slug)
                ->first();

        $orderData = DB::table('orders')
                ->where('id', $details->order_id)
                ->first();
        if (empty($orderData)) {
            return Redirect::to('/user/myaccount');
        }

        $this->layout->content = View::make('/Orders/showcourierview')
                ->with('userData', $userData)
                ->with('details', $details);

        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();

        $customerData = DB::table('users')
                ->where('id', $orderData->user_id)
                ->first();

        $input = Input::all();

        $cartItems = DB::table('order_item')->whereIn('order_id', explode(',', $orderData->id))->get(); // get cart menu of this order

        if (!empty($input)) {
            $inputStatus = $input['status'];
            switch ($input['status']) {
                case "Confirm":
                    $cMessage = "Confirmed";
                    $orderStatus = "Confirmed";
                    break;
                case "Cancel":
                    $cMessage = "Cancelled";
                    $orderStatus = "Cancelled";
                    break;
            }


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
                    ' . App::make("HomeController")->numberformat($cartData->base_price, 2) . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . $cartData->quantity . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . App::make("HomeController")->numberformat($sub_total, 2) . '
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
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
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
                    ' . App::make("HomeController")->numberformat($tax, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $tax;
            }
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($delivery_charge, 2) . '
                    </td>
                    </tr>';
            $gTotal = $gTotal + $delivery_charge;
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

            $orderContent .= '</table>';



            /*             * * send mail to admin ** */
            $courier_name = $userData->first_name . ' ' . $userData->last_name;
            $admin_mail_data = array(
                'text' => 'Order status changed successfully by courier  "' . $courier_name . '" ' . $orderStatus . '  on ' . SITE_TITLE . '.',
                'customerContent' => $customerContent,
                'orderContent' => $orderContent,
                'sender_email' => $adminuser->email,
                'firstname' => "Admin",
            );

//            return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

            Mail::send('emails.template', $admin_mail_data, function($message) use($admin_mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($admin_mail_data['sender_email'], 'Admin')->subject('Order status changed successfully by courier on ' . SITE_TITLE . '.');
                    });


            DB::table('order_courier')
            ->where('id', $details->id)
            ->update(['status' => $inputStatus]);

            DB::table('orders')
            ->where('id', $orderData->id)
            ->update(['courier_id' => $userData->id]);

            return Redirect::to('/order/courierorders')->with('success_message', 'You have successfully ' . $cMessage . ' order ' . SITE_TITLE . '.');
        }
    }

    public function showMyorders($slug = null) {

        $this->logincheck('user/myorders');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Customer') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $this->chkUserType('Customer');

        // get my all addresses        
        $query = DB::table('orders');

        if ($slug != "") {
            $mainData = DB::table('main_order')
                    ->where('slug', $slug)
                    ->first();
            //  print_r($mainData);

            $query->where('orders.user_id', $user_id)
                    ->whereIn('orders.order_number', explode(',', $mainData->order_id))
                    ->join('users', 'users.id', '=', 'orders.caterer_id')
                    ->select('orders.*', 'users.first_name', 'users.slug as restroslug');
        } else {
            $query->where('orders.user_id', $user_id)
                    ->join('users', 'users.id', '=', 'orders.caterer_id')
                    ->select('orders.*', 'users.first_name', 'users.slug as restroslug');
        }

        $records = $query->orderBy('orders.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'My Orders';
        $this->layout->content = View::make('/Orders/myorders')
                ->with('userData', $userData)
                ->with('slug', $slug)
                ->with('records', $records);
    }

    public function showFavorders($slug = null) {

        $this->logincheck('orders/favorders');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Customer') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $this->chkUserType('Customer');

        // get my all addresses        
        $query = DB::table('orders');
        if ($slug != "") {
            $mainData = DB::table('main_order')
                    ->where('slug', $slug)
                    ->first();
            //  print_r($mainData);

            $query->where('orders.user_id', $user_id)
                    ->whereIn('orders.order_number', explode(',', $mainData->order_id))
                    ->join('users', 'users.id', '=', 'orders.caterer_id')
                    ->select('orders.*', 'users.first_name', 'users.slug as restroslug');
        } else {
            $query->where('orders.user_id', $user_id)->where('orders.is_favorite', 1)
                    ->join('users', 'users.id', '=', 'orders.caterer_id')
                    ->select('orders.*', 'users.first_name', 'users.slug as restroslug');
        }

        $records = $query->orderBy('orders.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Favourite Orders';
        $this->layout->content = View::make('/Orders/favorders')
                ->with('userData', $userData)
                ->with('slug', $slug)
                ->with('records', $records);
    }

    public function showMainorders() {

        $this->logincheck('user/mainorders');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Customer') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $this->chkUserType('Customer');

        // get my all addresses        
        $query = DB::table('main_order');
        $query->where('main_order.user_id', $user_id)
                ->select('main_order.*');
        $records = $query->orderBy('main_order.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'My Orders';
        $this->layout->content = View::make('/Orders/mainorders')
                ->with('userData', $userData)
                ->with('records', $records);
    }
    // order sound    
    public function order_sound(){
     $input = Input::all();
     $id = $input['user'];   
     $id2 = 41;
     $query = Order::where('orders.caterer_id', $id)
                ->where('orders.status', '=', "Paid")
                ->count();
     $res = array('code'=>'1','count'=>$query); 
     
     echo json_encode($res);     
      die;    
    }
    
    public function showreceivedorders() {

        $this->logincheck('user/receivedorders');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Restaurant') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }
        $input = Input::all();
        $search_keyword = "";
        $orderstatus = "";
        // get current user details
        $user_id = Session::get('user_id');
        //$user_id = 39;
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
            //    echo $search_keyword; exit;
        }
        if (!empty($input['status'])) {
            $orderstatus = trim($input['status']);
            //    echo $search_keyword; exit;
        }
        // get my all addresses     

        $query = DB::table('orders');
//        $query->where('orders.caterer_id', $user_id)
//                
//                ->select('orders.*');

        $query = Order::sortable()
                ->where('orders.caterer_id', $user_id)
                //->where('orders.status', '!=', "Pending")
                ->where(function ($query) use ($search_keyword) {
                            $query->where('order_number', 'LIKE', '%' . $search_keyword . '%');
                        })
                ->where(function ($query) use ($orderstatus) {
                    $query->where('status', 'LIKE', '%' . $orderstatus . '%');
                });

        $records = $query->orderBy('orders.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Received Orders';
        $this->layout->content = View::make('/Orders/receivedorders')->with('search_keyword', $search_keyword)
                ->with('userData', $userData)
                ->with('orderstatus', $orderstatus)
                ->with('records', $records);
    }

    public function showcourierorders() {

        $this->logincheck('user/courierorders');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Courier') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get my all addresses        
        $query = DB::table('order_courier')
                ->select("order_courier.*", "users.first_name", "users.last_name", "orders.order_number", "orders.slug as order_slug")
                ->join('users', 'users.id', ' = ', 'order_courier.user_id')
                ->join('orders', 'orders.id', ' = ', 'order_courier.order_id');
        $query->where('order_courier.user_id', $user_id);
        $records = $query->orderBy('order_courier.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Assinged Orders';
        $this->layout->content = View::make('/Orders/courierorders')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    function notify_customer($orderSlug = NULL) {
        $this->layout = false;

        // get order data
        $orderData = DB::table('orders')
                ->where('orders.slug', $orderSlug)
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

        $couirerData = DB::table('users')
                ->where('id', $orderData->courier_id)
                ->first();



        $cartItems = DB::table('order_item')->whereIn('order_id', explode(',', $orderData->id))->get(); // get cart menu of this order
        if (!empty($orderData)) {


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
            $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: center; background-color: rgb(108, 158, 22); padding: 7px;" colspan="4">Courier Details</td>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Courier Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $couirerData->first_name . ' ' . $couirerData->last_name . '
                                </td>
                            </tr>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Couirer Contact Number: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $couirerData->contact . '
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
                    ' . App::make("HomeController")->numberformat($cartData->base_price, 2) . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . $cartData->quantity . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . App::make("HomeController")->numberformat($sub_total, 2) . '
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
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
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
                    ' . App::make("HomeController")->numberformat($tax, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $tax;
            }
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($delivery_charge, 2) . '
                    </td>
                    </tr>';
            $gTotal = $gTotal + $delivery_charge;
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

            $orderContent .= '</table>';

            /*             * * send mail to customer ** */
            $mail_data = array(
                'text' => 'Your order has been approved on ' . SITE_TITLE . '.',
                'customerContent' => $customerContent,
                'orderContent' => $orderContent,
                'sender_email' => $customerData->email_address,
                'firstname' => $customerData->first_name . ' ' . $customerData->last_name,
            );

            // return View::make('emails.template')->with($mail_data); // to check mail template data to view

            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject('Order status changed successfully');
                    });
        }
        return Redirect::to('/admin/order/admin_index')->with('success_message', 'Notify Customer Successfully.');
    }

    function showModifyorders($slug = null, $orderSlug = null) {
        $this->layout = false;

        // get carters details
        $caterer = DB::table('users')
                ->where('users.slug', $slug)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                ->leftjoin('areas', 'areas.id', '=', 'users.area')
                ->leftjoin('cities', 'cities.id', '=', 'users.city')
                ->select("users.*", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name")
                ->first();


        $orderData = DB::table('orders')
                ->where("orders.slug", "=", $orderSlug)
                ->first();

        Session::put('modifyorder', '1');
        Session::put('order_id', $orderData->id);


        $cartItems = DB::table('order_item')->whereIn('menu_id', explode(',', $orderData->order_item_id))->get(); // get cart menu of this order

        if (!empty($cartItems)) {
            $cart = new Cart(new CartSession, new Cookie);
            foreach ($cartItems as $cartData) {

                // get item details
                $item = DB::table('menu_item')
                        ->where('menu_item.id', $cartData->menu_id)
                        ->select("item_name", "price")
                        ->first();

                $cart->insert(array(
                    'id' => $cartData->menu_id,
                    'order_item_id' => $cartData->id,
                    'name' => $item->item_name,
                    'price' => $cartData->base_price,
                    'quantity' => $cartData->quantity,
                    'submenus' => $cartData->submenus
                ));
            }
        }
        return Redirect::to('/caterers/menu/' . $slug);
    }

}
