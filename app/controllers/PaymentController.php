<?php

//use Moltin\Cart\Cart;
//use Moltin\Cart\Storage\CartSession;
//use Moltin\Cart\Identifier\Cookie;

use Moltin\Cart\Cart;
use Moltin\Cart\Storage\CartSession;
use Moltin\Cart\Identifier\Cookie;

class PaymentController extends BaseController {

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

    public function cancel($id) {
        $this->logincheck('payment/cancel');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }


        $this->layout->title = TITLE_FOR_PAGES . 'Cancel';
        $user_id = Session::get('user_id');
        //   $orders = explode('|',$id);
        //   print_r($orders); exit;
        //echo $id; exit;
//        $main_ordershopData = DB::table('main_order')
//           // ->whereIn('order_id', $orders)
//            ->whereRaw("FIND_IN_SET('$orders[0]',order_id)")
//            ->where('user_id', $user_id)
//            ->delete();
        //   print_r($main_ordershopData); exit;


        $shopData = DB::table('orders')
                ->where('id', $id)
                ->where('user_id', $user_id)
                ->delete(); // get cart menu of this order
//        $main_ordershopData = DB::table('main_order')
//           // ->whereIn('order_id', $orders)
//            ->whereRaw("FIND_IN_SET('$orders[0]',order_id)")
//            ->where('user_id', $user_id)
//            ->get();



        return Redirect::to('user/myaccount/')->with('error_message', 'Your Payment is cancelled.');
    }

    public function openshop($id) {
        $this->logincheck('payment/openshop');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Open Shop';
        $user_id = Session::get('user_id');
        // $orders = explode('|',$id);

        $shopData = DB::table('orders')
                ->where('id', $id)
                ->where('user_id', $user_id)
                ->first(); // get cart menu of this order
        // print_r($shopData); exit;

        $catererData = DB::table('users')
                ->where('id', $shopData->caterer_id)
                ->first();
//        echo '<prE>';print_r($catererData);die;

        $price = 0;
        $tax = 0;
        $delivery_charge = 0;
        $discount = 0;
        $grandTpotal = 0;
        $orderid = array();

        $total = Session::get('gTotal');

//         echo $total; exit;
        // echo "<pre>"; print_r($shopData); exit;
        // Show the page
        if ($shopData) {
            
        } else {
            return Redirect::to('/');
        }
        $this->layout->content = View::make('payment/openshop')
                ->with('shopData', $shopData)
                ->with('id', $id)
                ->with('total', $total)
                ->with('paypal_email', $catererData->paypal_email_address);
//        if ($shopData->confirm_shop != '1') {
//            return Redirect::to('payment/confirmmail/' . $shopData->slug)->with('error_message', 'You must need to confirm your mail. Please check your email or click on resend to send confirmation link.')
//                            ->with('shopData', $shopData);
//        }
    }

    public function paymentSuccess($slug = null, $trsid = null) {
        //exit;

        $cart = new Cart(new CartSession, new Cookie);
        $cart->destroy();
        Session::forget('coupon');
        Session::forget('pickup');
        Session::forget('when');
        $this->logincheck('payment/success');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }


        $this->layout->title = TITLE_FOR_PAGES . 'Payment Success';
        $user_id = Session::get('user_id');

        //New wallet by anand 
        $gTotal = Session::get('gTotal');


        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $shopData = DB::table('orders')
                ->where('user_id', $user_id)
                ->where('id', $slug)
                ->first();

        $tax = $shopData->tax;
        $delivery_charge = $shopData->delivery_charge;
        $discount = $shopData->discount;


        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();
                
                $catererData = DB::table('users')
                    ->where('id', $shopData->caterer_id)
                    ->first();

        //     echo "<pre>"; print_r($shopData); exit;
        
        if ($shopData->status == "Pending") {



            $price = 0;
            // $tax = 0;
            //   $delivery_charge = 0;
            //  $discount = 0;
            $grandTpotal = 0;
            $orderid = $shopData->id;



            $content = DB::table('order_item')
                    ->where('user_id', $user_id)
                    ->where('order_id', $shopData->id)
                    ->get();

            if (!empty($content)) {

                $total = array();

                foreach ($content as $cartData) {
                    //echo "<pre>"; print_r($cartData); exit;
                    $menuData = DB::table('menu_item')
                            ->where('id', $cartData->menu_id)
                            ->first();

                    $sub_total = $cartData->base_price * $cartData->quantity;
                    $total[] = $sub_total;
                }
            }

//                    $tax = $tax + $paymentInfoVal->tax;
//                    $discount = $discount + $paymentInfoVal->discount;
//                    $delivery_charge = $delivery_charge + $paymentInfoVal->tax;

            $grandTpotal = $grandTpotal + array_sum($total);



            $total = $grandTpotal + $delivery_charge - $discount;
            $total = $total + $tax;

            if ($shopData) {
                if ($trsid == "") {
                    $transactionId = "Pay-" . time() . rand(1, 9);
                } else {
                    $transactionId = $trsid;
                }


//                $saveUser = array(
//                    'transaction_id' => $transactionId,
//                    'user_id' => $user_id,
//                    'price' => $total,
//                    'slug' => "Pay-" . time(),
//                    'type' => "Purchase",
//                    'status' => "Complete",
//                    'order_id' => $orderid,
//                    'created' => date('Y-m-d'),
//                );
//                DB::table('payments')->insert(
//                        $saveUser
//                );
//                New wallet code anand 

                $saveUser = array(
                    'transaction_id' => $transactionId,
                    'user_id' => $user_id,
                    'price' => $gTotal,
                    'slug' => "Pay-" . time(),
                    'type' => "Purchase",
                    'status' => "Complete",
                    'order_id' => $orderid,
                    'created' => date('Y-m-d'),
                );
                DB::table('payments')->insert(
                        $saveUser
                );
            }


            $p_status = ($shopData->payment_method == 1) ? "Pending" : "Paid";
            $paid = ($shopData->payment_method == 1) ? 0 : 1;
            $saveList = array(
                'status' => $p_status, 'paid' => $paid
            );

            DB::table('orders')
                    ->where('id', $slug)
                    ->update($saveList);


            //New wallet code anand

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

            if ($shopData->payby_wallet > 0) {
                $trasid = 'FOOD' . time() . rand(1, 9);


                $data = array(
                    'type' => 'Debit',
                    'balance' => '0',
                    'trans_id' => $trasid,
                    'display_amount' => $shopData->payby_wallet,
                    'calculated_amount' => '-' . $shopData->payby_wallet,
                    'paid' => '1',
                    'status' => 'Paid',
                    'user_id' => $shopData->user_id,
                    'comment' => 'Debit In wallet for Order:' . $shopData->order_number,
                    'created' => date('Y-m-d')
                );

                DB::table('wallets')->insert(
                        $data
                );
            }

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
                    ->where('id', $shopData->caterer_id)
                    ->first();

            $data = array(
                'user_id' => $user_id,
                'caterer_id' => $shopData->caterer_id,
                'created' => date('Y-m-d H:i:s'),
            );
            DB::table('user_reviews')->insert($data
            );

            //   echo "<pre>"; print_r($shopData); exit;

            $VendorTemp = "";

            $VendorTemp .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $shopData->order_number . ' (' . $catererData->first_name . ')
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
                    ->where('order_id', $shopData->id)
                    ->get();



            if (!empty($content)) {

                $total = array();

                foreach ($content as $cartData) {
                    //echo "<pre>"; print_r($cartData); exit;
                    $menuItem = DB::table('menu_item')
                            ->where('id', $cartData->menu_id)
                            ->first();
                    $VendorTemp .= '<tr>
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
                        $VendorTemp .= '<tr>
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
                            $VendorTemp .= '<tr>
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


            if ($shopData->discount)
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
                'text' => 'You have received an order on ' . SITE_TITLE,
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
//
//              


            /*             * * send mail to admin ** */

            $admin_mail_data = array(
                'text' => 'A New Order has been placed on ' . SITE_TITLE,
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
                    
        }


        $push_message = array(
            "type" => 'order_placed',
            "message" => 'Your have received a new order.',
            'customer_id' => $user_id,
            'restaurant_id' => $catererData->id
        );

        if ($catererData->device_type == 'Android') {
            //$this->send_fcm_notify($catererData->device_id, $push_message, 'Restaurant');
        } else if ($catererData->device_type == 'iPhone') {
            //$this->send_iphone_notification($catererData->device_id, $push_message, 'Restaurant');
        }


        // exit;


        return Redirect::to('user/myaccount/')->with('success_message', 'Thanks for ordering with us. Your order details are submitted successfully. You will receive confirmation message after acceptance of your order.')
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

                $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->first();


                $tax = $shopData->tax;
                $delivery_charge = $shopData->delivery_charge;
                $discount = $shopData->discount;


                $adminuser = DB::table('admins')
                        ->where('id', '1')
                        ->first();


                if ($paymentInfo) {



                    if ($shopData->status == "Pending") {



                        $price = 0;
                        // $tax = 0;
                        //   $delivery_charge = 0;
                        //  $discount = 0;
                        $grandTpotal = 0;
                        $orderid = $shopData->id;



                        $content = DB::table('order_item')
                                ->where('user_id', $user_id)
                                ->where('order_id', $shopData->id)
                                ->get();

                        if (!empty($content)) {

                            $total = array();

                            foreach ($content as $cartData) {
                                //echo "<pre>"; print_r($cartData); exit;
                                $menuData = DB::table('menu_item')
                                        ->where('id', $cartData->menu_id)
                                        ->first();

                                $sub_total = $cartData->base_price * $cartData->quantity;
                                $total[] = $sub_total;
                            }
                        }

//                    $tax = $tax + $paymentInfoVal->tax;
//                    $discount = $discount + $paymentInfoVal->discount;
//                    $delivery_charge = $delivery_charge + $paymentInfoVal->tax;
                        $grandTpotal = $grandTpotal + array_sum($total);



                        $total = $grandTpotal + $delivery_charge - $discount;
                        $total = $total + $tax;

                        if ($shopData) {
                            $transactionId = "Pay-" . time() . rand(1, 9);

                            $saveUser = array(
                                'transaction_id' => $transactionId,
                                //'last_name' => $input['last_name'],
                                'user_id' => $user_id,
                                'price' => $total,
                                'slug' => "Pay-" . time(),
                                'type' => "Purchase",
                                'status' => "Complete",
                                'order_id' => $orderid,
                                'created' => date('Y-m-d'),
                            );
                            DB::table('payments')->insert(
                                    $saveUser
                            );
                        }



                        $saveList = array(
                            'status' => 'Paid'
                        );

                        DB::table('orders')
                                ->where('id', $slug)
                                ->update($saveList);


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
                                ->where('id', $shopData->caterer_id)
                                ->first();

                        $data = array(
                            'user_id' => $user_id,
                            'caterer_id' => $shopData->caterer_id,
                            'created' => date('Y-m-d H:i:s'),
                        );
                        DB::table('user_reviews')->insert($data
                        );

                        //   echo "<pre>"; print_r($shopData); exit;

                        $VendorTemp = "";

                        $VendorTemp .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $shopData->order_number . ' (' . $catererData->first_name . ')
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
                                ->where('order_id', $shopData->id)
                                ->get();



                        if (!empty($content)) {

                            $total = array();

                            foreach ($content as $cartData) {
                                //echo "<pre>"; print_r($cartData); exit;
                                $menuData = DB::table('menu_item')
                                        ->where('id', $cartData->menu_id)
                                        ->first();

                                $sub_total = $cartData->base_price * $cartData->quantity;
                                $total[] = $sub_total;
                                //  echo $sub_total;
                                $VendorTemp .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $menuData->item_name . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($cartData->base_price, 2) . '
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
                        //  $tax = $tax + $shopData->tax;
                        //  $delivery_charge = $delivery_charge + $shopData->delivery_charge;
                        //  $discount = $discount + $shopData->discount;
                        //echo $VendorTemp; exit;
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
                        if ($adminuser->is_tax) {
                            $cardata .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                                   Tax
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($tax, 2) . '
                                </td>
                                  </tr>';
                            $totalVendor = $totalVendor + $tax;
                        }

                        $cardata .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                                   Delivery Charge (' . $shopData->delivery_type . ')
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($delivery_charge, 2) . '
                                </td>
                                  </tr>';

                        if ($adminuser->is_commission == 1) {

                            $comm_per = $adminuser->commission;
                            $tax_amount = $comm_per * $totalVendor / 100;


//                            $cardata .= '<tr>
//                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
//                                   Admin Commission
//                                </td>
//                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
//                                   ' . App::make("HomeController")->numberformat($tax_amount, 2) . '
//                                </td>
//                                  </tr>';
//                            $totalVendor = $totalVendor - $tax_amount;
                        }
                        // $totalVendor = $totalVendor + $delivery_charge/count($shopData);
//                if ( $shopData->discount)
//                    $cardata .= '<tr>
//                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
//                                   Discount
//                                </td>
//                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
//                                   ' . App::make("HomeController")->numberformat($discount, 2)  . '
//                                </td>
//                                  </tr>';
                        $gTotal = $totalVendor + $delivery_charge;

                        $cardata .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                                  Grand Total
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                                   ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                                </td>
                                  </tr>';

                        $cardata .= '</table>';
//                echo $catererData->email_address;
//                echo $cardata; exit;

                        /**                         * send mail to caterer ** */
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
                        /*                         *  * * ** *  */
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
                        if ($shopData->discount)
                            $orderContent .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                                   Discount
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($discount, 2) . '
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
                        //echo $orderContent; exit;



                        /*                         * * send mail to customer ** */
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
//
//              


                        /*                         * * send mail to admin ** */

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
                    }
                }
            }
        }

        exit;
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
        $query = Listing::sortable()
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->select('payments.*', 'users.first_name', 'users.last_name')
                ->where(function ($query) use ($search_keyword) {
                    $query->where('title', 'LIKE', '%' . $search_keyword . '%');
                });

        if (!empty($input['action'])) {

            $action = $input['action'];
            $idList = $input['chkRecordId'];
            switch ($action) {
                case "Activate":
                    DB::table('payments')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));
                    Session::put('success_message', 'Service Product(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('payments')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    Session::put('success_message', 'Service Product(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('payments')
                            ->whereIn('id', $idList)
                            ->delete();
                    Session::put('success_message', 'Service Product(s) deleted successfully');
                    break;
            }
        }

        DB::table('payments')->where('type', '=', 'Purchase');

        $separator = implode("/", $separator);

        // Get all the users
        $payments = $query->orderBy('payments.id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('Listings/adminindex', compact('payments'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_sponsorships() {
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

        $query = Payment::sortable()
                ->where(function ($query) use ($search_keyword) {
                    $query->where('transaction_id', 'LIKE', '%' . $search_keyword . '%');
                });

        $separator = implode("/", $separator);

        $query->where('type', '=', 'Sponsorship');
        // Get all the users
        $payments = $query->orderBy('id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('payment/sponsorship_index', compact('payments'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_deleteuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')->where('slug', $slug)->delete();
            return Redirect::to('/admin/user/admin_index')->with('success_message', 'Service Provider deleted successfully');
        }
    }

    public function showAdmin_payment_index() {
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

        $query = Payment::sortable()
                ->where(function ($query) use ($search_keyword) {
                    $query->where('transaction_id', 'LIKE', '%' . $search_keyword . '%');
                });


        $separator = implode("/", $separator);

        $query->where('type', '=', 'Purchase');
        // Get all the users
        $payments = $query->orderBy('id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('payment/payment_index', compact('payments'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_deletepayment($slug = null) {


        // get menu item details
        $payments = DB::table('payments')
                ->where('slug', $slug)
                ->delete();

        //    return Redirect::to('/payment/dashboard/')->with('success_message', 'Listing item deleted successfully!');
        return Redirect::back()->with('success_message', 'Payment record deleted successfully');
    }

}
?>

