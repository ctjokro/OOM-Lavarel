<?php

use Moltin\Cart\Cart;
use Moltin\Cart\Storage\CartSession;
use Moltin\Cart\Identifier\Cookie;

class HomeController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default Home Controller
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

    /**
     * Website homepahe
     */
    public function showWelcome() {

        $restData = 0;
        $user_type = Session::get('user_type');
        $user_slug = Session::get('user_slug');
        if ($user_slug && $user_type == 'Restaurant') {
            $caterer = DB::table('users')
                    ->where('users.slug', $user_slug)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                    ->leftjoin('areas', 'areas.id', '=', 'users.area')
                    ->leftjoin('cities', 'cities.id', '=', 'users.city')
                    ->select("users.*", "opening_hours.catering_type", "opening_hours.average_time", "opening_hours.estimated_cost", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name", 'opening_hours.catering_type')
                    ->first();
            if ($caterer) {
                $restData = 1;
            }
        }



        if ($restData == 1) {
            $url = Request::url();
            $url1 = explode('https://', $url);
            $url2 = explode('.', $url1[1]);
            if ($url2[0] != 'www') {
                $user_slug = $url2[0];
                $catererUni = DB::table('users')
                        ->where('users.unique_name', $user_slug)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                        ->leftjoin('areas', 'areas.id', '=', 'users.area')
                        ->leftjoin('cities', 'cities.id', '=', 'users.city')
                        ->select("users.*", "opening_hours.catering_type", "opening_hours.average_time", "opening_hours.estimated_cost", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name", 'opening_hours.catering_type')
                        ->first();
                if (!empty($catererUni)) {
                    $caterer = $catererUni;
                } else {
                    $catererSlug = DB::table('users')
                            ->where('users.slug', $user_slug)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                            ->leftjoin('areas', 'areas.id', '=', 'users.area')
                            ->leftjoin('cities', 'cities.id', '=', 'users.city')
                            ->select("users.*", "opening_hours.catering_type", "opening_hours.average_time", "opening_hours.estimated_cost", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name", 'opening_hours.catering_type')
                            ->first();
                    $caterer = $catererSlug;
                }



                $query = DB::table('menu_item')
                        ->where("menu_item.user_id", "=", $caterer->id)
                        ->where("menu_item.status", "=", '1');

                // Get all the menu items
                if (isset($_REQUEST['o'])) {
                    $order = $_REQUEST['o'];
                } else {
                    $order = "ASC";
                }
                if (isset($_REQUEST['s']) && $_REQUEST['s'] == 'price') {
                    exit;
                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->orderBy('menu_item.price', $order)
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*")
                            ->paginate(10);
                } elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == 'loved') {

                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->orderBy('counter', $order)
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*", DB::raw("(select count(tbl_favorite_menu.id) from `tbl_favorite_menu` where tbl_favorite_menu.menu_id = tbl_menu_item.id) as counter"))
                            ->paginate(10);
                } elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == 'item_name') {

                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->orderBy('menu_item.item_name', $order)
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*")
                            ->paginate(10);
                } else {

                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*")
                            ->get();
                }

                // get restaurant cuisines
                $cuisine = DB::table('cuisines')
                        ->orderBy('cuisines.name', 'asc')
                        ->where("menu_item.user_id", "=", $caterer->id)
                        ->where("cuisines.status", "=", 1)
                        ->join('menu_item', 'cuisines.id', '=', 'menu_item.cuisines_id')
                        ->select("cuisines.name", "cuisines.id")
                        ->groupBy('cuisines.id')
                        ->get();

                // get cart contents
                $cart = new Cart(new CartSession, new Cookie);
                $content = $cart->contents(true);

                $this->layout->title = TITLE_FOR_PAGES . 'Restauranting Menu - ' . $caterer->first_name . " " . $caterer->last_name;
                $this->layout->content = View::make('/home/menu')
                        ->with('caterer', $caterer)
                        ->with("items", $items)
                        ->with("cuisine", $cuisine)
                        ->with("resto_slug", $user_slug)
                        ->with("cart_content", $content)
                        ->with("cart", $cart);
            } else {
                $query = DB::table('menu_item')
                        ->where("menu_item.user_id", "=", $caterer->id)
                        ->where("menu_item.status", "=", '1');

                // Get all the menu items
                if (isset($_REQUEST['o'])) {
                    $order = $_REQUEST['o'];
                } else {
                    $order = "ASC";
                }
                if (isset($_REQUEST['s']) && $_REQUEST['s'] == 'price') {
                    exit;
                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->orderBy('menu_item.price', $order)
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*")
                            ->paginate(10);
                } elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == 'loved') {

                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->orderBy('counter', $order)
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*", DB::raw("(select count(tbl_favorite_menu.id) from `tbl_favorite_menu` where tbl_favorite_menu.menu_id = tbl_menu_item.id) as counter"))
                            ->paginate(10);
                } elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == 'item_name') {

                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->orderBy('menu_item.item_name', $order)
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*")
                            ->paginate(10);
                } else {

                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*")
                            ->get();
                }

                // get restaurant cuisines
                $cuisine = DB::table('cuisines')
                        ->orderBy('cuisines.name', 'asc')
                        ->where("menu_item.user_id", "=", $caterer->id)
                        ->where("cuisines.status", "=", 1)
                        ->join('menu_item', 'cuisines.id', '=', 'menu_item.cuisines_id')
                        ->select("cuisines.name", "cuisines.id")
                        ->groupBy('cuisines.id')
                        ->get();

                // get cart contents
                $cart = new Cart(new CartSession, new Cookie);
                $content = $cart->contents(true);

                $this->layout->title = TITLE_FOR_PAGES . 'Restauranting Menu - ' . $caterer->first_name . " " . $caterer->last_name;
                $this->layout->content = View::make('/home/menu')
                        ->with('caterer', $caterer)
                        ->with("items", $items)
                        ->with("cuisine", $cuisine)
                        ->with("cart_content", $content)
                        ->with("cart", $cart);
            }
        } else {
            $url = Request::url();
            $url1 = explode('https://', $url);
            $url2 = explode('.', $url1[1]);
            if ($url2[0] != 'www') {
                $user_slug = $url2[0];
                $catererUni = DB::table('users')
                        ->where('users.unique_name', $user_slug)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                        ->leftjoin('areas', 'areas.id', '=', 'users.area')
                        ->leftjoin('cities', 'cities.id', '=', 'users.city')
                        ->select("users.*", "opening_hours.catering_type", "opening_hours.average_time", "opening_hours.estimated_cost", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name", 'opening_hours.catering_type')
                        ->first();
                if (!empty($catererUni)) {
                    $caterer = $catererUni;
                } else {
                    $catererSlug = DB::table('users')
                            ->where('users.slug', $user_slug)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                            ->leftjoin('areas', 'areas.id', '=', 'users.area')
                            ->leftjoin('cities', 'cities.id', '=', 'users.city')
                            ->select("users.*", "opening_hours.catering_type", "opening_hours.average_time", "opening_hours.estimated_cost", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name", 'opening_hours.catering_type')
                            ->first();
                    $caterer = $catererSlug;
                }

if (empty($caterer)) {
            return Redirect::to(MAIN_HTTP_PATH);
        }


                $query = DB::table('menu_item')
                        ->where("menu_item.user_id", "=", $caterer->id)
                        ->where("menu_item.status", "=", '1');

                // Get all the menu items
                if (isset($_REQUEST['o'])) {
                    $order = $_REQUEST['o'];
                } else {
                    $order = "ASC";
                }
                if (isset($_REQUEST['s']) && $_REQUEST['s'] == 'price') {
                    exit;
                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->orderBy('menu_item.price', $order)
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*")
                            ->paginate(10);
                } elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == 'loved') {

                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->orderBy('counter', $order)
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*", DB::raw("(select count(tbl_favorite_menu.id) from `tbl_favorite_menu` where tbl_favorite_menu.menu_id = tbl_menu_item.id) as counter"))
                            ->paginate(10);
                } elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == 'item_name') {

                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->orderBy('menu_item.item_name', $order)
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*")
                            ->paginate(10);
                } else {

                    $items = $query
                            ->orderBy('cuisines.name', 'asc')
                            ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                            ->select("cuisines.name as cuisines_name", "menu_item.*")
                            ->get();
                }

                // get restaurant cuisines
                $cuisine = DB::table('cuisines')
                        ->orderBy('cuisines.name', 'asc')
                        ->where("menu_item.user_id", "=", $caterer->id)
                        ->where("cuisines.status", "=", 1)
                        ->join('menu_item', 'cuisines.id', '=', 'menu_item.cuisines_id')
                        ->select("cuisines.name", "cuisines.id")
                        ->groupBy('cuisines.id')
                        ->get();

                // get cart contents
                $cart = new Cart(new CartSession, new Cookie);
                $content = $cart->contents(true);

                $this->layout->title = TITLE_FOR_PAGES . 'Restauranting Menu - ' . $caterer->first_name . " " . $caterer->last_name;
                $this->layout->content = View::make('/home/menu')
                        ->with('caterer', $caterer)
                        ->with("items", $items)
                        ->with("cuisine", $cuisine)
                        ->with("resto_slug", $user_slug)
                        ->with("cart_content", $content)
                        ->with("cart", $cart);
            } else {
                // get caterers
                $record = DB::table('users')
                        ->select(DB::raw("(select avg(quality) from tbl_reviews where tbl_reviews.caterer_id = tbl_users.id) as rating"), 'users.profile_image', 'users.id', 'users.slug', 'users.address', 'users.*', "areas.name as area_name", "cities.name as city_name", "opening_hours.open_close", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "opening_hours.open_days", 'users.first_name')
//                ->orderBy('users.id', 'desc')
                        ->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                        ->leftjoin('areas', 'areas.id', '=', 'users.area')
                        ->leftjoin('cities', 'cities.id', '=', 'users.city')
                        ->where('users.user_type', "=", "Restaurant")
                        ->where('users.status', "=", "1")
                        ->where('users.approve_status', "=", "1")
                        ->orderBy('users.featured', 'desc')
                        ->orderBy('rating', 'desc')
                        ->take(8)
                        ->get();



                // get all cuisines list
                $cuisines = Cuisine::where("status", "=", "1")->lists('name', 'id');
                $this->layout->title = TITLE_FOR_PAGES . 'Welcome';
                $this->layout->content = View::make('home.index')
                        ->with("cuisines", $cuisines)
                        ->with("record", $record);
            }
        }
    }

    public function showContactus() {

        // get admin details
        $adminuser = DB::table('admins')
                ->first();

        // get login user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->select('users.first_name', 'users.email_address', 'users.contact')
                ->where('users.id', $user_id)
                ->first();

        $input = Input::all();
        if (!empty($input)) {
            //  echo "<pre>"; print_r($input); exit;
            $rules = array(
                //   'g-recaptcha-response' => 'required',
                'name' => 'required',
                'subject' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'message' => 'required'
            );

            //  $messages = array('g-recaptcha-response.required' => 'Captcha is required field.');
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/home/contactus')->withErrors($validator)->withInput(Input::all());
            } else {

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
                // print_r($mail_data); exit;
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
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

                return Redirect::to('/contactus')->with('success_message', 'Thank you for contacting us');
            }
        } else {
            // get all cuisines list
            $this->layout->title = TITLE_FOR_PAGES . 'Contact Us';
            $this->layout->content = View::make('home.contactus')->with('detail', $adminuser)->with('userData', $userData);
        }
    }

    // generate captcha code
    public function showCapcha() {
        $this->layout = false;
        /*
         *
         * this code is based on captcha code by Simon Jarvis 
         * http://www.white-hat-web-design.co.uk/articles/php-captcha.php
         *
         * This program is free software; you can redistribute it and/or 
         * modify it under the terms of the GNU General Public License 
         * as published by the Free Software Foundation
         *
         * This program is distributed in the hope that it will be useful, 
         * but WITHOUT ANY WARRANTY; without even the implied warranty of 
         * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
         * GNU General Public License for more details: 
         * http://www.gnu.org/licenses/gpl.html
         */

//Settings: You can customize the captcha here
        $image_width = 120;
        $image_height = 40;
        $characters_on_image = 6;
        $font = 'public/img/monofont.ttf';

//The characters that can be used in the CAPTCHA code.
//avoid confusing characters (l 1 and i for example)
        $possible_letters = '23456789bcdfghjkmnpqrstvwxyz';
        $random_dots = 0;
        $random_lines = 20;
        $captcha_text_color = "0x142864";
        $captcha_noice_color = "0x142864";

        $code = '';


        $i = 0;
        while ($i < $characters_on_image) {
            $code .= substr($possible_letters, mt_rand(0, strlen($possible_letters) - 1), 1);
            $i++;
        }


        $font_size = $image_height * 0.75;
        $image = @imagecreate($image_width, $image_height);


        /* setting the background, text and noise colours here */
        $background_color = imagecolorallocate($image, 255, 255, 255);

        $arr_text_color = $this->hexrgb($captcha_text_color);
        $text_color = imagecolorallocate($image, $arr_text_color['red'], $arr_text_color['green'], $arr_text_color['blue']);

        $arr_noice_color = $this->hexrgb($captcha_noice_color);
        $image_noise_color = imagecolorallocate($image, $arr_noice_color['red'], $arr_noice_color['green'], $arr_noice_color['blue']);


        /* generating the dots randomly in background */
        for ($i = 0; $i < $random_dots; $i++) {
            imagefilledellipse($image, mt_rand(0, $image_width), mt_rand(0, $image_height), 2, 3, $image_noise_color);
        }


        /* generating lines randomly in background of image */
        for ($i = 0; $i < $random_lines; $i++) {
            imageline($image, mt_rand(0, $image_width), mt_rand(0, $image_height), mt_rand(0, $image_width), mt_rand(0, $image_height), $image_noise_color);
        }


        /* create a text box and add 6 letters code in it */
        $textbox = imagettfbbox($font_size, 0, $font, $code);
        $x = ($image_width - $textbox[4]) / 2;
        $y = ($image_height - $textbox[5]) / 2;
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $font, $code);


        /* Show captcha image in the page html page */
        header('Content-Type: image/jpeg'); // defining the image type to be shown in browser widow
        imagejpeg($image); //showing the image
        imagedestroy($image); //destroying the image instance
        Session::put('security_number', $code);
    }

    // function for captcha
    function hexrgb($hexstr) {
        $int = hexdec($hexstr);

        return array("red" => 0xFF & ($int >> 0x10),
            "green" => 0xFF & ($int >> 0x8),
            "blue" => 0xFF & $int);
    }

    function showList() {

        $input = Input::all();

        // get search params
        $city = isset($input['city']) ? addslashes($input['city']) : "";
        $area = isset($input['area']) ? addslashes($input['area']) : "";
        $keyword = isset($input['keyword']) ? addslashes($input['keyword']) : "";
        $cuisine = (isset($input['cuisine']) and $input['cuisine'][0]) ? $input['cuisine'] : array();
        $catering_type = (isset($input['catering_type']) and $input['catering_type'][0]) ? $input['catering_type'] : array();

        $query = User::sortable()
                ->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                ->leftjoin('areas', 'areas.id', '=', 'users.area')
                ->leftjoin('cities', 'cities.id', '=', 'users.city')
                ->where("users.user_type", "=", 'Restaurant')
                ->where("users.status", "=", '1');


        // $query->leftjoin("menu_item", 'menu_item.user_id', '=', 'users.id')->groupBy('menu_item.user_id');
        if ((isset($cuisine) && count($cuisine) > 0) || (isset($keyword) && $keyword != "")) {

            $query->leftjoin("menu_item", 'menu_item.user_id', '=', 'users.id')->groupBy('menu_item.user_id');
        }
        if (!empty($cuisine)) {
            //print_r($cusinaname); exit;
            $query
                    ->where(function ($query) use ($cuisine) {
                        foreach ($cuisine as $c)
                            $query->orwhere('menu_item.cuisines_id', '=', $c);
                    });
        }
        if ($city)
            $query->where('city', '=', $city);
        if ($area)
            $query->whereRaw("FIND_IN_SET('$area',tbl_users.area)");


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
                    $query->where('users.first_name', "LIKE", "%$keyword%");
                }
                //print_r($cusinaname); exit;
//               
            }

//            $query->orwhere('menu_item.item_name', "=", "$keyword");
//            $query->orwhere(DB::raw("CONCAT_WS(' ', tbl_users.first_name,tbl_users.last_name)"), "like", "%$keyword%");
        }
        //print_r($keyword);
        // Get all the users
        $restro = $query
                        ->where('users.status', "=", "1")
                        ->where('opening_hours.open_close', "=", "1")
                        ->where('users.approve_status', "=", "1")
                        ->where('users.user_type', "=", "Restaurant")
                        ->select('users.*', "areas.name as area_name", "cities.name as city_name", "opening_hours.open_close", "opening_hours.catering_type", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "opening_hours.open_days", DB::raw("(select (avg(tbl_reviews.quality)+avg(tbl_reviews.packaging))/2 from `tbl_reviews` where tbl_reviews.caterer_id = tbl_users.id and tbl_reviews.status = '1') as rating"), DB::raw("(select count(tbl_reviews.id) from `tbl_reviews` where tbl_reviews.caterer_id = tbl_users.id and tbl_reviews.status = '1') as counter"))
                        //->having('products_count', '>' , 1)
                        ->orderBy('users.featured', 'desc')
                        ->orderBy('opening_hours.open_close', 'desc')
                        ->orderBy('rating', 'desc')
                        ->orderBy('users.first_name', 'asc')
                        ->orderBy('users.id', 'desc')
                        ->sortable()->paginate(20);

        // get all cuisines list
        //echo "<pre>"; print_r($users);
        $cuisines = Cuisine::where("status", "=", "1")->orderBy('name', 'asc')->lists('name', 'id');

        $this->layout->title = TITLE_FOR_PAGES . 'Restaurant List';
        $this->layout->content = View::make('/home/showlist', compact('restro'))
                ->with('cuisines', $cuisines);
    }

    function showMenu($slug = "") {
        // get carters details
        Session::forget('session_address_id');
        $caterer = DB::table('users')
                ->where('users.slug', $slug)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                ->leftjoin('areas', 'areas.id', '=', 'users.area')
                ->leftjoin('cities', 'cities.id', '=', 'users.city')
                ->select("users.*", "opening_hours.catering_type", "opening_hours.average_time", "opening_hours.estimated_cost", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name", 'opening_hours.catering_type')
                ->first();

        if (empty($caterer)) {
            return Redirect::to('/');
        }

        //  echo '<pre>'; print_r($caterer);die;
        // get caterer menu
        $query = DB::table('menu_item')
                ->where("menu_item.user_id", "=", $caterer->id)
                ->where("menu_item.status", "=", '1');

        // Get all the menu items
        if (isset($_REQUEST['o'])) {
            $order = $_REQUEST['o'];
        } else {
            $order = "ASC";
        }
        if (isset($_REQUEST['s']) && $_REQUEST['s'] == 'price') {
            exit;
            $items = $query
                    ->orderBy('cuisines.name', 'asc')
                    ->orderBy('menu_item.price', $order)
                    ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                    ->select("cuisines.name as cuisines_name", "menu_item.*")
                    ->paginate(10);
        } elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == 'loved') {

            $items = $query
                    ->orderBy('cuisines.name', 'asc')
                    ->orderBy('counter', $order)
                    ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                    ->select("cuisines.name as cuisines_name", "menu_item.*", DB::raw("(select count(tbl_favorite_menu.id) from `tbl_favorite_menu` where tbl_favorite_menu.menu_id = tbl_menu_item.id) as counter"))
                    ->paginate(10);
        } elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == 'item_name') {

            $items = $query
                    ->orderBy('cuisines.name', 'asc')
                    ->orderBy('menu_item.item_name', $order)
                    ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                    ->select("cuisines.name as cuisines_name", "menu_item.*")
                    ->paginate(10);
        } else {

            $items = $query
                    ->orderBy('cuisines.name', 'asc')
                    ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                    ->select("cuisines.name as cuisines_name", "menu_item.*")
                    ->get();
        }

        // get restaurant cuisines
        $cuisine = DB::table('cuisines')
                ->orderBy('cuisines.name', 'asc')
                ->where("menu_item.user_id", "=", $caterer->id)
                ->where("cuisines.status", "=", 1)
                ->join('menu_item', 'cuisines.id', '=', 'menu_item.cuisines_id')
                ->select("cuisines.name", "cuisines.id")
                ->groupBy('cuisines.id')
                ->get();

        // get cart contents
        $cart = new Cart(new CartSession, new Cookie);
        $content = $cart->contents(true);

        $this->layout->title = TITLE_FOR_PAGES . 'Restauranting Menu - ' . $caterer->first_name . " " . $caterer->last_name;
        $this->layout->content = View::make('/home/menu')
                ->with('caterer', $caterer)
                ->with("items", $items)
                ->with("cuisine", $cuisine)
                ->with("cart_content", $content)
                ->with("cart", $cart);
    }

    function showAddtocart($address_id = null) {
        if (!empty($address_id)) {
            Session::put('session_address_id', $address_id);
        }

        $address = Session::get('session_address_id');

        if ($address > 0) {
            
        }

        $cart = new Cart(new CartSession, new Cookie);
        $input = Input::all();
        if (!empty($input)) {
            // get item details
            $item = DB::table('menu_item')
                    ->where('menu_item.id', $input['id'])
                    ->select("item_name", "price", "user_id")
                    ->first();

            $content = $cart->contents();
            //   print_r($content);
            if (!empty($content)) {
                foreach ($content as $item2) {
                    $item_array2 = $item2->toArray();
                    $old_menu_id = $item_array2['id'];
                    $old_caterer_item = DB::table('menu_item')
                            ->where('menu_item.id', $old_menu_id)
                            ->select("item_name", "price", "user_id")
                            ->first();

                    $old_catererid = $old_caterer_item->user_id;
                    if ($old_catererid != $item->user_id) {
                        return json_encode(array('message' => "You can purchase food from single restaurant in an order.", 'valid' => false));
                    }

                    break;
                }
            }

            if ($input['type'] == 'plus') {
                // insert item to cart
                if ($input['submenus'] == "already") {
                    if (isset($input['addons'])) {
                        $cart->insert(array(
                            'id' => $input['id'],
                            'name' => $item->item_name,
                            'price' => $item->price,
                            'quantity' => 1,
                            'addons' => $input['addons'],
                            'variant_type' => $input['variant_type']
                        ));
                    } else {
                        $cart->insert(array(
                            'id' => $input['id'],
                            'name' => $item->item_name,
                            'price' => $item->price,
                            'quantity' => 1
                        ));
                    }
                } else {
                    if (isset($input['addons'])) {
                        $cart->insert(array(
                            'id' => $input['id'],
                            'name' => $item->item_name,
                            'price' => $item->price,
                            'quantity' => 1,
                            'caterer_id' => $item->user_id,
                            'submenus' => $input['submenus'],
                            'addons' => $input['addons'],
                            'variant_type' => $input['variant_type']
                        ));
                    } else {
                        $cart->insert(array(
                            'id' => $input['id'],
                            'name' => $item->item_name,
                            'price' => $item->price,
                            'quantity' => 1,
                            'caterer_id' => $item->user_id,
                            'submenus' => $input['submenus'],
                            'variant_type' => $input['variant_type']
                        ));
                    }
                }
            } else {

                // get cart contents
                $content = $cart->contents();
                if (!empty($content)) {
                    foreach ($content as $item) {
                        $item_array = $item->toArray();
                        if (isset($item_array['id']) and $item_array['id'] == $input['id']) {

                            if ($item_array['quantity'] - 1 <= 0) {

                                // remove item from cart
                                $item->remove();
                            } else {
                                $item->quantity = $item_array['quantity'] - 1;
                            }
                        }
                    }
                }
            }
        }
        $content = $cart->contents(true);
        $view = View::make('home.cart')->with('cart', $cart)->with('cart_content', $content)->with('order', (isset($input['order']) ? $input['order'] : 0));
        $html = $view->render();
        return json_encode(array('data' => $html, 'valid' => true));
    }

    function Updatecart($address_id = null) {
        if (!empty($address_id)) {
            Session::put('session_address_id', $address_id);
        } else {
            Session::forget('session_address_id');
        }
        $cart = new Cart(new CartSession, new Cookie);
        $input = Input::all();
        $content = $cart->contents();
        $content = $cart->contents(true);
        $view = View::make('home.updatecart')->with('cart', $cart)->with('cart_content', $content)->with('order', (isset($input['order']) ? $input['order'] : 0));
        $html = $view->render();
        return json_encode(array('data' => $html, 'valid' => true));
    }

    function pickup($op = null, $when = null) {
//        print_r($_SESSION);
        if ($op == 1) {

            Session::put('pickup', 1);
            if ($when != "") {
                Session::put('when', $when);
            }
        } else {
            Session::forget('pickup');
            if (Session::get('when') != "") {

                Session::forget('when');
            }
        }

        //echo    $pickup = Session::get('pickup')."sdasd"; exit;
        $cart = new Cart(new CartSession, new Cookie);
        $input = Input::all();
        $content = $cart->contents();
        $content = $cart->contents(true);
        $view = View::make('home.updatecart')->with('cart', $cart)->with('cart_content', $content)->with('order', (isset($input['order']) ? $input['order'] : 0));
        $html = $view->render();
        return json_encode(array('data' => $html, 'valid' => true));
    }

    function UpdatecartNewAddress($area_id = null) {

        $areaData = DB::table('areas')
                ->where('id', $area_id)
                ->first();
        $city_id = $areaData->city_id;
        $user_id = Session::get('user_id');


        $addressData = DB::table('addresses')
                ->where('city', $city_id)
                ->where('area', $area_id)
                ->where('user_id', $user_id)
                ->first();
        if (!empty($addressData)) {
            $address_id = $addressData->id;
        } else {
            $address_id = "";
        }

        Session::put('area_id', $area_id);
        Session::put('city_id', $city_id);
        if (!empty($address_id)) {
            Session::put('session_address_id', $address_id);
        } else {
            Session::forget('session_address_id');
        }
        $cart = new Cart(new CartSession, new Cookie);
        $input = Input::all();
        $content = $cart->contents();
        $content = $cart->contents(true);
        $view = View::make('home.updatecart')->with('cart', $cart)->with('cart_content', $content)->with('order', (isset($input['order']) ? $input['order'] : 0));
        $html = $view->render();
        return json_encode(array('data' => $html, 'valid' => true));
    }

    function showUpdatecarttext() {
        $cart = new Cart(new CartSession, new Cookie);
        $input = Input::all();

        $content = $cart->contents();
        if (!empty($content)) {
            foreach ($content as $item) {
                $item_array = $item->toArray();
                if (isset($item_array['id']) and $item_array['id'] == $input['id']) {
                    $item->comment = $input['data'];
                }
            }
        }

        $content = $cart->contents(true);
        $view = View::make('home.cart')->with('cart', $cart)->with('cart_content', $content)->with('order', (isset($input['order']) ? $input['order'] : 0));
        $html = $view->render();
        return json_encode(array('data' => $html, 'valid' => true));
    }

    function showRemovecart() {
        $cart = new Cart(new CartSession, new Cookie);
        $input = Input::all();
        $content = $cart->contents();
        if (!empty($content)) {
            foreach ($content as $item) {
                $item_array = $item->toArray();
                if (isset($item_array['id']) and $item_array['id'] == $input['id']) {
                    $item->remove();
                }
            }
        }
        $total_items = $cart->totalItems();
        if (empty($total_items)) {
            Session::forget('session_address_id');
        }
        $content = $cart->contents(true);
        $view = View::make('home.cart')->with('cart', $cart)->with('cart_content', $content)->with('order', (isset($input['order']) ? $input['order'] : 0));
        $html = $view->render();
        return json_encode(array('data' => $html, 'valid' => true));
    }

    function showemptycart() {
        $cart = new Cart(new CartSession, new Cookie);
        $input = Input::all();
        $content = $cart->contents();
        if (!empty($content)) {
            foreach ($content as $item) {
                $item_array = $item->toArray();
                // if (isset($item_array['id']) and $item_array['id'] == $input['id']) {
                $item->remove();
                // }
            }
        }

        $total_items = $cart->totalItems();
        if (empty($total_items)) {
            Session::forget('session_address_id');
        }
        $content = $cart->contents(true);
        $view = View::make('home.cart')->with('cart', $cart)->with('cart_content', $content)->with('order', (isset($input['order']) ? $input['order'] : 0));
        $html = $view->render();
        return json_encode(array('data' => $html, 'valid' => true));
    }

    function deletemodifyorder($orderSlug = null) {
        $this->layout = false;
        $cart = new Cart(new CartSession, new Cookie);
        $cart->destroy();
        Session::forget('modifyorder');
        Session::forget('order_id');
        return Redirect::to('/user/myaccount')->with('success_message', 'Your modifying order has been remove successfully.');
    }

    function showOrderold() {
        // get cart contents
        $cart = new Cart(new CartSession, new Cookie);

        // get current user details
        $user_id = Session::get('user_id');

        //   Session::forget('session_address_id');
        Session::forget('city_id');
        Session::forget('area_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

//        // get my addresses   
        $query = DB::table('addresses');
        $query->leftjoin("areas", 'addresses.id', '=', 'addresses.area');
        $query->leftjoin("cities", 'cities.id', '=', 'addresses.city');
        $query->where('addresses.user_id', $user_id)
                ->select('addresses.*', 'areas.name as area_name', 'cities.name as city_name');
        $records = $query->orderBy('addresses.id', 'desc')->paginate(15);


        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();


        $content = $cart->contents(true);
        if (!$content) {
            return Redirect::back()->with('error_message', 'Please add some menu items in cart.');
        }
        $this->layout->title = TITLE_FOR_PAGES . 'Confirm Order';
        $this->layout->content = View::make('/home/order')
                ->with("cart_content", $content)
                ->with("cart", $cart)
                ->with("records", $records)
                ->with("userData", $userData);


        //print_r($content);exit;
        $input = Input::all();

        if (!empty($input)) {
            $payment_mode = $input['payment_mode'];
            if ($userData->user_type != "Customer") {
                return Redirect::to('/order/confirm')->with('error_message', 'Only Customer can be able to place the order!');
            }

            if ($adminuser->is_tax) {
                $tax = $input['tax'];
            } else {
                $tax = 0;
            }
            //echo "<pre>"; print_r($input); exit;

            $total_items = $cart->totalItems();
            if (empty($total_items)) {
                return Redirect::to('/order/confirm')->with('error_message', 'Cart Empty! Please add item in your cart before place order!');
            }

            $discount = isset($input['discount']) ? $input['discount'] : 0;

            $is_address = $input['is_address'];

            if ($is_address == '1') {
                $rules = array(
                    'address_title' => 'required',
                    'address_type' => 'required',
                    'city' => 'required',
                    'area' => 'required',
                    'street_name' => 'required',
                    'phone_number' => 'required',
                );
            } else {
                $rules = array();
            }

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/order/confirm')
                                ->withErrors($validator); // send back all errors to the register form
            } else {

                if (isset($input['delivery_charge']) && $input['delivery_charge'] != '') {
                    $dData = explode('_', $input['delivery_charge']);
                    $delivery_charge = $dData['1'];
                    $delivery_type = $dData['0'];
                } else {
                    $delivery_charge = "0";
                    $delivery_type = "N/A";
                }

                if ($is_address == '1') {


                    /*                     * * save new address in db ** */
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
                    $address_id = DB::getPdo()->lastInsertId();
                } else {
                    if (isset($input['address'])) {
                        $address_id = $input['address'];
                    }
                }
                if (isset($input['pickup_ready']) && $input['pickup_ready'] == 1) {
                    $address_id = 0;
                }

                if (!empty($content)) {

                    foreach ($content as $cartData) {
                        $menu_id = $cartData['id'];
                        break;
                    }
                }

                if (!empty($content)) {
                    $newArray = array();
                    foreach ($content as $node => $cartDataN) {
                        $newArray[$cartDataN['caterer_id']][] = $cartDataN;
                    }
                }
                //  echo "<pre>"; print_r($newArray); exit;
                $menuData = DB::table('menu_item')->where('id', explode(',', $menu_id))->first(); // get cart menu of this order
                $catererData = DB::table('users')
                        ->where('id', $menuData->user_id)
                        ->first();


                $openiningHourData = DB::table('opening_hours')
                        ->where('user_id', $catererData->id)
                        ->first();
                $carttotal = Session::get('carttotal');
                //echo $carttotal; exit;
                if (!empty($openiningHourData)) {
                    if ($openiningHourData->minimum_order > $carttotal) {
                        Session::forget('session_address_id');
                        return Redirect::to('/order/confirm')->with('error_message', "Please place order value more then " . App::make("HomeController")->numberformat($openiningHourData->minimum_order, 2));
                    }
                }

                /*                 * * save cart items ** */
                $modifyorder = Session::get('modifyorder');
                $order_id = Session::get('order_id');


                if (!empty($order_id)) {
                    $mailSubjectCustomer = 'Your order modified successfully on ' . SITE_TITLE;
                    $mailSubjectRestaurant = 'Order modified on ' . SITE_TITLE;
                    $mailSubjectAdmin = 'Order modified on ' . SITE_TITLE;
                    $orderData = DB::table('orders')
                            ->where("orders.id", "=", $order_id)
                            ->first();

                    DB::table('order_item')->whereIn('menu_id', explode(',', $orderData->order_item_id))->delete();
                    $orderNumber = $orderData->order_number;
                } else {
                    $mailSubjectCustomer = 'Your order placed successfully on ' . SITE_TITLE;
                    $mailSubjectRestaurant = 'New order received on ' . SITE_TITLE;
                    $mailSubjectAdmin = 'New order received on ' . SITE_TITLE;
                    $orderNumber = $this->createOrderNumber();
                }

                if ($delivery_type == 'basic') {
                    $delivery_type = "Vespa";
                } else {
                    $delivery_type = "Car";
                }

                if (isset($input['pickup_ready']) && $input['pickup_ready'] == 1) {
                    $delivery_charge = "0";
                    $delivery_type = "Pickup";
                }

                if (!empty($order_id)) {
                    $orderData = array(
                        //'order_item_id' => $menu_items_id,
                        'delivery_charge' => $delivery_charge,
                        'delivery_type' => $delivery_type,
                        'caterer_id' => $menuData->user_id,
                        'tax' => $tax,
                        'discount' => $discount,
                        'status' => "Pending",
                    );
                    DB::table('orders')
                            ->where('id', $order_id)
                            ->update($orderData);
                } else {

                    $orderData = array(
                        'address_id' => $address_id,
                        'order_number' => $orderNumber,
                        'user_id' => $user_id,
                        'delivery_charge' => $delivery_charge,
                        'delivery_type' => $delivery_type,
                        'caterer_id' => $menuData->user_id,
                        'tax' => $tax,
                        'discount' => $discount,
                        'status' => "Pending",
                        'created' => date('Y-m-d H:i:s'),
                        'slug' => $this->createSlug('order')
                    );

                    if (isset($input['pickup_ready']) && $input['pickup_ready'] == 1) {
                        $orderData['pickup_ready'] = 1;
                        $orderData['pickup_now'] = $input['pickup_now'];
                        $orderData['pickup_time'] = $input['pickup_time'];
                    }

                    DB::table('orders')->insert(
                            $orderData
                    );
                    $order_id = DB::getPdo()->lastInsertId();
                }

                if (Session::get('coupon')) {
                    // insert applied coupom
                    $coupondata = array(
                        'coupon' => Session::get('coupon'),
                        'user_id' => $user_id,
                        'created' => date('Y-m-d H:i:s')
                    );
                    DB::table('applied_coupons')->insert(
                            $coupondata
                    );
                }
                //  print_r($content); exit;
                $sumTotal = 0;
                $menu_items = array();
                if (!empty($content)) {

                    foreach ($content as $cartData) {
                        $subtotal = 0;

                        $menu_items[] = $cartData['id'];
                        $menu_id = $cartData['id'];
                        $variant_type = "";

                        if (isset($cartData['variant_type'])) {
                            $explode = explode(',', $cartData['variant_type']);
                            if ($explode) {
                                foreach ($explode as $explodeVal) {

                                    $addonV = DB::table('variants')
                                            ->where('variants.id', $explodeVal)
                                            ->first();
                                    //    echo "<pre>"; print_r($addonV);
                                    if ($addonV) {
                                        $sub_total = $addonV->price * $cartData['quantity'];
                                        $subtotal = $subtotal + $sub_total;
                                    }
                                }
                                $variant_type = $cartData['variant_type'];
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
                                    // echo "<pre>"; print_r($addonV);
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
                            'menu_id' => $cartData['id'],
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
                        // echo "<pre>"; print_r($data); exit;
                        DB::table('order_item')->insert(
                                $data
                        );
                    }
                }
                //echo $sumTotal; exit;
                $gTotal = $sumTotal + $delivery_charge + $tax - $discount;

//
//                echo $gTotal;
//                die;
                //echo $payment_mode;die;
                //echo $gTotal; exit;
                /*                 * * save orders ** */
                $menu_items_id = implode(',', $menu_items);
                DB::table('orders')
                        ->where('id', $order_id)
                        ->update(array('order_item_id' => $menu_items_id));
                //  echo $order_id; exit;

                Session::put('gTotal', $gTotal);

//                $cart->destroy();
//                Session::forget('coupon');
                Session::forget('modifyorder');
                Session::forget('order_id');

//                echo $gTotal;die;
//                
                //return Redirect::to('/user/myaccount')->with('success_message', 'Thanks for ordering with us. Your order details are submitted successfully. You will receive confirmaion message after acceptance of your order.');
                if ($payment_mode == 0) {
                    $amount = $gTotal;
                    $email = $catererData->paypal_email_address;
                    //$userInfo = $this->User->find('first',array('conditions'=>array('User.id'=>$this->Session->read('user_id'))));

                    $nameArray = explode(' ', $input['full_name']);
                    $fname = $nameArray[0];
                    if (isset($nameArray[1]) && $nameArray[1]) {
                        $lname = $nameArray[1];
                    } else {
                        $lname = '';
                    }

//                    $countryInfo = $this->Country->find('first', array('conditions' => array('Country.id' => $this->data['Payment']['country'])));
                    //pr($countryInfo);exit;
//                    $zip11 = $this->data['Payment']['zipcode'];
//                    $currency11 = $campaigns['Campaign']['currency'];
//                    $country11 = $countryInfo['Country']['name'];
                    $zip11 = "";
                    $currency11 = CURR;
                    $country11 = '';

                    //pr($this->data);exit;
                    $cardType = 'VISA';
                    $cardNumber = $input['card_number'];
                    $cardVcc = $input['card_cvv'];
                    $month = $input['card_exp_month'];
                    $year = $input['card_exp_year'];
                    $countryCode = "";

                    // Set request-specific fields.
                    $paymentType = urlencode('Authorization');    // or 'Sale'
                    $firstName = urlencode($fname);
                    $lastName = urlencode($lname);
                    $creditCardType = urlencode($cardType);
                    $creditCardNumber = urlencode($cardNumber);
                    $expDateMonth = $month;
                    $padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));

                    $expDateYear = urlencode($year);
                    $cvv2Number = urlencode($cardVcc);
                    $zip = urlencode($zip11);
                    $country = urlencode($countryCode);    // US or other valid country code
                    $amount = urlencode($amount);
                    $currencyID = urlencode($currency11);       // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
                    // Add request-specific fields to the request string.
                    $nvpStr = "&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber" .
                            "&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName" .
                            "&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";


                    //pr($nvpStr);exit;
                    // Execute the API operation; see the PPHttpPost function above.
                    $httpParsedResponseAr = $this->PPHttpPost('DoDirectPayment', $nvpStr, $catererData->id);

                    //print_r($httpParsedResponseAr); exit;
                    if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
                        $transactionId = $httpParsedResponseAr['TRANSACTIONID'];

                        return Redirect::to('/payment/success/' . $order_id . '/' . $transactionId);
                    } else {
                        $error = urldecode($httpParsedResponseAr['L_LONGMESSAGE0']);

                        $shopData = DB::table('orders')
                                ->where('id', $order_id)
                                ->delete();
                        return Redirect::to('/order/confirm/')->with('error_message', $error);
                    }
                } else {
                    return Redirect::to('/payment/openshop/' . $order_id);
                }
            }
        }
    }

    function showOrder() {
        // get cart contents
        $cart = new Cart(new CartSession, new Cookie);

        // get current user details
        $user_id = Session::get('user_id');
        $address_id = 0;

        //   Session::forget('session_address_id');
        Session::forget('city_id');
        Session::forget('area_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

//        // get my addresses   
        $query = DB::table('addresses');
        $query->leftjoin("areas", 'addresses.id', '=', 'addresses.area');
        $query->leftjoin("cities", 'cities.id', '=', 'addresses.city');
        $query->where('addresses.user_id', $user_id)
                ->select('addresses.*', 'areas.name as area_name', 'cities.name as city_name');
        $records = $query->orderBy('addresses.id', 'desc')->paginate(15);


        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();


        $content = $cart->contents(true);
        if (!$content) {
            return Redirect::back()->with('error_message', 'Please add some menu items in cart.');
        }
        $this->layout->title = TITLE_FOR_PAGES . 'Confirm Order';
        $this->layout->content = View::make('/home/order')
                ->with("cart_content", $content)
                ->with("cart", $cart)
                ->with("records", $records)
                ->with("userData", $userData);


        //print_r($content);exit;
        $input = Input::all();

        if (!empty($input)) {
            //echo "<pre>";  print_r($input);exit;
            $payment_mode = $input['payment_mode'];
            if ($userData->user_type != "Customer") {
                return Redirect::to('/order/confirm')->with('error_message', 'Only Customer can be able to place the order!');
            }

            if ($adminuser->is_tax) {
                $tax = $input['tax'];
            } else {
                $tax = 0;
            }
            //echo "<pre>"; print_r($input); exit;

            $total_items = $cart->totalItems();
            if (empty($total_items)) {
                return Redirect::to('/order/confirm')->with('error_message', 'Cart Empty! Please add item in your cart before place order!');
            }

            $discount = isset($input['discount']) ? $input['discount'] : 0;

            $is_address = $input['is_address'];

            //if ($is_address == '1') {
            if (!empty($input['city'])) {
                $rules = array(
                    'address_title' => 'required',
                    'address_type' => 'required',
                    'city' => 'required',
                    'area' => 'required',
                    'street_name' => 'required',
                    'phone_number' => 'required',
                );
            } else {
                $rules = array();
            }

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/order/confirm')
                                ->withErrors($validator); // send back all errors to the register form
            } else {

                if (isset($input['delivery_charge']) && $input['delivery_charge'] != '') {
                    $dData = explode('_', $input['delivery_charge']);
                    $delivery_charge = $dData['1'];
                    $delivery_type = $dData['0'];
                } else {
                    $delivery_charge = "0";
                    $delivery_type = "N/A";
                }

                if (!empty($input['city'])) {


                    /*                     * * save new address in db ** */
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
                    $address_id = DB::getPdo()->lastInsertId();
                } else {
                    if (isset($input['address'])) {
                        $address_id = $input['address'];
                    }
                }
                if (isset($input['pickup_ready']) && $input['pickup_ready'] == 1) {
                    $address_id = 0;
                }

                if (!empty($content)) {

                    foreach ($content as $cartData) {
                        $menu_id = $cartData['id'];
                        break;
                    }
                }

                if (!empty($content)) {
                    $newArray = array();
                    foreach ($content as $node => $cartDataN) {
                        $newArray[$cartDataN['caterer_id']][] = $cartDataN;
                    }
                }
                //  echo "<pre>"; print_r($newArray); exit;
                $menuData = DB::table('menu_item')->where('id', explode(',', $menu_id))->first(); // get cart menu of this order
                $catererData = DB::table('users')
                        ->where('id', $menuData->user_id)
                        ->first();


                $openiningHourData = DB::table('opening_hours')
                        ->where('user_id', $catererData->id)
                        ->first();
                $carttotal = Session::get('carttotal');
                //echo $carttotal; exit;
                if (!empty($openiningHourData)) {
                    if ($openiningHourData->minimum_order > $carttotal) {
                        Session::forget('session_address_id');
                        return Redirect::to('/order/confirm')->with('error_message', "Please place order value more then " . App::make("HomeController")->numberformat($openiningHourData->minimum_order, 2));
                    }
                }

                /*                 * * save cart items ** */
                $modifyorder = Session::get('modifyorder');
                $order_id = Session::get('order_id');


                if (!empty($order_id)) {
                    $mailSubjectCustomer = 'Your order modified successfully on ' . SITE_TITLE;
                    $mailSubjectRestaurant = 'Order modified on ' . SITE_TITLE;
                    $mailSubjectAdmin = 'Order modified on ' . SITE_TITLE;
                    $orderData = DB::table('orders')
                            ->where("orders.id", "=", $order_id)
                            ->first();

                    DB::table('order_item')->whereIn('menu_id', explode(',', $orderData->order_item_id))->delete();
                    $orderNumber = $orderData->order_number;
                } else {
                    $mailSubjectCustomer = 'Your order placed successfully on ' . SITE_TITLE;
                    $mailSubjectRestaurant = 'New order received on ' . SITE_TITLE;
                    $mailSubjectAdmin = 'New order received on ' . SITE_TITLE;
                    $orderNumber = $this->createOrderNumber();
                }

                if ($delivery_type == 'basic') {
                    $delivery_type = "Vespa";
                } else {
                    $delivery_type = "Car";
                }

                if (isset($input['pickup_ready']) && $input['pickup_ready'] == 1) {
                    $delivery_charge = "0";
                    $delivery_type = "Pickup";
                }

                if (!empty($order_id)) {
                    $orderData = array(
                        //'order_item_id' => $menu_items_id,
                        'delivery_charge' => $delivery_charge,
                        'delivery_type' => $delivery_type,
                        'caterer_id' => $menuData->user_id,
                        'tax' => $tax,
                        'discount' => $discount,
                        'status' => "Pending",
                    );
                    DB::table('orders')
                            ->where('id', $order_id)
                            ->update($orderData);
                } else {

                    $orderData = array(
                        'address_id' => $address_id,
                        'order_number' => $orderNumber,
                        'user_id' => $user_id,
                        'delivery_charge' => $delivery_charge,
                        'delivery_type' => $delivery_type,
                        'caterer_id' => $menuData->user_id,
                        'tax' => $tax,
                        'discount' => $discount,
                        'status' => "Pending",
                        'created' => date('Y-m-d H:i:s'),
                        'slug' => $this->createSlug('order')
                    );

                    if (isset($input['pickup_ready']) && $input['pickup_ready'] == 1) {
                        $orderData['pickup_ready'] = 1;
                        $orderData['pickup_now'] = $input['pickup_now'];
                        $orderData['pickup_time'] = $input['pickup_time'];
                    }

                    DB::table('orders')->insert(
                            $orderData
                    );
                    $order_id = DB::getPdo()->lastInsertId();
                }

                if (Session::get('coupon')) {
                    // insert applied coupom
                    $coupondata = array(
                        'coupon' => Session::get('coupon'),
                        'user_id' => $user_id,
                        'created' => date('Y-m-d H:i:s')
                    );
                    DB::table('applied_coupons')->insert(
                            $coupondata
                    );
                }
                //  print_r($content); exit;
                $sumTotal = 0;
                $menu_items = array();
                if (!empty($content)) {

                    foreach ($content as $cartData) {
                        $subtotal = 0;

                        $menu_items[] = $cartData['id'];
                        $menu_id = $cartData['id'];
                        $variant_type = "";

                        if (isset($cartData['variant_type'])) {
                            $explode = explode(',', $cartData['variant_type']);
                            if ($explode) {
                                foreach ($explode as $explodeVal) {

                                    $addonV = DB::table('variants')
                                            ->where('variants.id', $explodeVal)
                                            ->first();
                                    //    echo "<pre>"; print_r($addonV);
                                    if ($addonV) {
                                        $sub_total = $addonV->price * $cartData['quantity'];
                                        $subtotal = $subtotal + $sub_total;
                                    }
                                }
                                $variant_type = $cartData['variant_type'];
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
                                    // echo "<pre>"; print_r($addonV);
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
                            'menu_id' => $cartData['id'],
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
                        // echo "<pre>"; print_r($data); exit;
                        DB::table('order_item')->insert(
                                $data
                        );
                    }
                }
                //echo $sumTotal; exit;
                $gTotal = $sumTotal + $delivery_charge + $tax - $discount;

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


                if ($amount > 0) {
                    $total_decate = '';
                    $total_decate = $gTotal - $amount;
//                    echo $gTotal; die;
                    if ($total_decate > 0) {
                        $gTotal = $total_decate;
                        DB::table('orders')
                                ->where('id', $order_id)
                                ->update(array('payby_wallet' => $amount, 'payby_direct' => $gTotal));
                    } else {
                        DB::table('orders')
                                ->where('id', $order_id)
                                ->update(array('payby_wallet' => $gTotal, 'payby_direct' => '0'));
                    }
                }

//
//                echo $gTotal;
//                die;
                //echo $payment_mode;die;
                //echo $gTotal; exit;

                /*                 * * save orders ** */
                $menu_items_id = implode(',', $menu_items);
                DB::table('orders')
                        ->where('id', $order_id)
                        ->update(array('order_item_id' => $menu_items_id));
                //  echo $order_id; exit;

                Session::put('gTotal', $gTotal);

//                $cart->destroy();
//                Session::forget('coupon');
                Session::forget('modifyorder');
                Session::forget('order_id');

//                echo $gTotal;die;

                if ($gTotal > 0) {
//                
                    //return Redirect::to('/user/myaccount')->with('success_message', 'Thanks for ordering with us. Your order details are submitted successfully. You will receive confirmaion message after acceptance of your order.');
                    if ($payment_mode == 0) {
                        $amount = $gTotal;
                        $email = $catererData->paypal_email_address;
                        //$userInfo = $this->User->find('first',array('conditions'=>array('User.id'=>$this->Session->read('user_id'))));

                        $nameArray = explode(' ', $input['full_name']);
                        $fname = $nameArray[0];
                        if (isset($nameArray[1]) && $nameArray[1]) {
                            $lname = $nameArray[1];
                        } else {
                            $lname = '';
                        }

//                    $countryInfo = $this->Country->find('first', array('conditions' => array('Country.id' => $this->data['Payment']['country'])));
                        //pr($countryInfo);exit;
//                    $zip11 = $this->data['Payment']['zipcode'];
//                    $currency11 = $campaigns['Campaign']['currency'];
//                    $country11 = $countryInfo['Country']['name'];
                        $zip11 = "";
                        $currency11 = CURR;
                        $country11 = '';

                        //pr($this->data);exit;
                        $cardType = 'VISA';
                        $cardNumber = $input['card_number'];
                        $cardVcc = $input['card_cvv'];
                        $month = $input['card_exp_month'];
                        $year = $input['card_exp_year'];
                        $countryCode = "";

                        // Set request-specific fields.
                        $paymentType = urlencode('Authorization');    // or 'Sale'
                        $firstName = urlencode($fname);
                        $lastName = urlencode($lname);
                        $creditCardType = urlencode($cardType);
                        $creditCardNumber = urlencode($cardNumber);
                        $expDateMonth = $month;
                        $padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));

                        $expDateYear = urlencode($year);
                        $cvv2Number = urlencode($cardVcc);
                        $zip = urlencode($zip11);
                        $country = urlencode($countryCode);    // US or other valid country code
                        $amount = urlencode($amount);
                        $currencyID = urlencode($currency11);       // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
                        // Add request-specific fields to the request string.
                        $nvpStr = "&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber" .
                                "&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName" .
                                "&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";


                        //pr($nvpStr);exit;
                        // Execute the API operation; see the PPHttpPost function above.
                        $httpParsedResponseAr = $this->PPHttpPost('DoDirectPayment', $nvpStr, $catererData->id);

                        //print_r($httpParsedResponseAr); exit;
                        if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
                            $transactionId = $httpParsedResponseAr['TRANSACTIONID'];

                            return Redirect::to('/payment/success/' . $order_id . '/' . $transactionId);
                        } else {
                            $error = urldecode($httpParsedResponseAr['L_LONGMESSAGE0']);

                            $shopData = DB::table('orders')
                                    ->where('id', $order_id)
                                    ->delete();
                            return Redirect::to('/order/confirm/')->with('error_message', $error);
                        }
                    } else {
                        return Redirect::to('/payment/openshop/' . $order_id);
                    }
                } else {
                    return Redirect::to('/payment/success/' . $order_id);
                }
            }
        }
    }

    function PPHttpPost($methodName_, $nvpStr_, $catid) {
        $environment = 'sandbox';

        $catdata = DB::table('users')
                ->where('id', $catid)
                ->first();

        $API_UserName = urlencode($catdata->paypal_username);
        $API_Password = urlencode($catdata->paypal_password);
        $API_Signature = urlencode($catdata->paypal_signature);
//
//        $API_UserName = urlencode('sudhir_1346750800_biz_api1.logicspice.com');
//        $API_Password = urlencode('1346750825');
//        $API_Signature = urlencode('AfYYmqcYysf.IPeZ5AFdE76z4TvQAjsTMvuVnDU-HTzFFScTWsqXE.zR');


        $API_Endpoint = "https://api-3t.paypal.com/nvp";
        if ("sandbox" === $environment || "beta-sandbox" === $environment) {
            $API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
        }
        $version = urlencode('51.0');

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API operation, version, and API signature in the request.
        $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        // Get response from the server.
        $httpResponse = curl_exec($ch);

        if (!$httpResponse) {
            exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }

        // Extract the response details.
        $httpResponseAr = explode("&", $httpResponse);

        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
        }

        return $httpParsedResponseAr;
    }

    function showOrder_backup() {
        // get cart contents
        $cart = new Cart(new CartSession, new Cookie);

        // get current user details
        $user_id = Session::get('user_id');

        //   Session::forget('session_address_id');
        Session::forget('city_id');
        Session::forget('area_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

//        // get my addresses   
        $query = DB::table('addresses');
        $query->where('addresses.user_id', $user_id)
                ->select('addresses.*');
        $records = $query->orderBy('addresses.id', 'desc')->paginate(15);


        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();


        $content = $cart->contents(true);
        $this->layout->title = TITLE_FOR_PAGES . 'Confirm Order';
        $this->layout->content = View::make('/home/order')
                ->with("cart_content", $content)
                ->with("cart", $cart)
                ->with("records", $records)
                ->with("userData", $userData);


        //print_r($content);exit;
        $input = Input::all();

        if (!empty($input)) {
            if ($userData->user_type != "Customer") {
                return Redirect::to('/order/confirm')->with('error_message', 'Only Customer can be able to place the order!');
            }

            if ($adminuser->is_tax) {
                $tax = $input['tax'];
            } else {
                $tax = 0;
            }
            echo "<pre>";
            print_r($input);
            exit;

            $total_items = $cart->totalItems();
            if (empty($total_items)) {
                return Redirect::to('/order/confirm')->with('error_message', 'Cart Empty! Please add item in your cart before place order!');
            }

            $discount = isset($input['discount']) ? $input['discount'] : 0;

            $is_address = $input['is_address'];
            if ($is_address == '1') {
                $rules = array(
                    'address_title' => 'required',
                    'address_type' => 'required',
                    'city' => 'required',
                    'area' => 'required',
                    'street_name' => 'required',
                    'phone_number' => 'required',
                );
            } else {
                $rules = array();
            }

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/order/confirm')
                                ->withErrors($validator); // send back all errors to the register form
            } else {

                if (isset($input['delivery_charge']) && $input['delivery_charge'] != '') {
                    $dData = explode('_', $input['delivery_charge']);
                    $delivery_charge = $dData['1'];
                    $delivery_type = $dData['0'];
                } else {
                    $delivery_charge = "0";
                    $delivery_type = "N/A";
                }

                if ($is_address == '1') {


                    /*                     * * save new address in db ** */
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
                    $address_id = DB::getPdo()->lastInsertId();
                } else {
                    if (isset($input['address'])) {
                        $address_id = $input['address'];
                    }
                }
                if (isset($input['pickup_ready']) && $input['pickup_ready'] == 1) {
                    $address_id = 0;
                }

                if (!empty($content)) {

                    foreach ($content as $cartData) {
                        $menu_id = $cartData['id'];
                        break;
                    }
                }
                if (!empty($content)) {
                    $newArray = array();
                    foreach ($content as $node => $cartDataN) {
                        $newArray[$cartDataN['caterer_id']][] = $cartDataN;
                    }
                }
                //  echo "<pre>"; print_r($newArray); exit;
                $menuData = DB::table('menu_item')->where('id', explode(',', $menu_id))->first(); // get cart menu of this order
                $catererData = DB::table('users')
                        ->where('id', $menuData->user_id)
                        ->first();


                $openiningHourData = DB::table('opening_hours')
                        ->where('user_id', $catererData->id)
                        ->first();
                $carttotal = Session::get('carttotal');
                //echo $carttotal; exit;
                if (!empty($openiningHourData)) {
                    if ($openiningHourData->minimum_order > $carttotal) {
                        Session::forget('session_address_id');
                        return Redirect::to('/order/confirm')->with('error_message', "Please place order value more then " . App::make("HomeController")->numberformat($openiningHourData->minimum_order, 2));
                    }
                }

                /*                 * * save cart items ** */
                $modifyorder = Session::get('modifyorder');
                $order_id = Session::get('order_id');


                if (!empty($order_id)) {
                    $mailSubjectCustomer = 'Your order modified successfully on ' . SITE_TITLE;
                    $mailSubjectRestaurant = 'Order modified on ' . SITE_TITLE;
                    $mailSubjectAdmin = 'Order modified on ' . SITE_TITLE;
                    $orderData = DB::table('orders')
                            ->where("orders.id", "=", $order_id)
                            ->first();

                    DB::table('order_item')->whereIn('menu_id', explode(',', $orderData->order_item_id))->delete();
                    $orderNumber = $orderData->order_number;
                } else {
                    $mailSubjectCustomer = 'Your order placed successfully on ' . SITE_TITLE;
                    $mailSubjectRestaurant = 'New order received on ' . SITE_TITLE;
                    $mailSubjectAdmin = 'New order received on ' . SITE_TITLE;
                    $orderNumber = $this->createOrderNumber();
                }

                if ($delivery_type == 'basic') {
                    $delivery_type = "Vespa";
                } else {
                    $delivery_type = "Car";
                }
                if (isset($input['pickup_ready']) && $input['pickup_ready'] == 1) {
                    $delivery_charge = "0";
                    $delivery_type = "Pickup";
                }

                if (!empty($order_id)) {
                    $orderData = array(
                        //'order_item_id' => $menu_items_id,
                        'delivery_charge' => $delivery_charge,
                        'delivery_type' => $delivery_type,
                        'caterer_id' => $menuData->user_id,
                        'tax' => $tax,
                        'discount' => $discount,
                        'status' => "Pending",
                    );
                    DB::table('orders')
                            ->where('id', $order_id)
                            ->update($orderData);
                } else {

                    $orderData = array(
                        'address_id' => $address_id,
                        //'order_item_id' => $menu_items_id,
                        'order_number' => $orderNumber,
                        'user_id' => $user_id,
                        'delivery_charge' => $delivery_charge,
                        'delivery_type' => $delivery_type,
                        'caterer_id' => $menuData->user_id,
                        'tax' => $tax,
                        'discount' => $discount,
                        'status' => "Pending",
                        'created' => date('Y-m-d H:i:s'),
                        'slug' => $this->createSlug('order')
                    );
                    if (isset($input['pickup_ready']) && $input['pickup_ready'] == 1) {
                        $orderData['pickup_ready'] = 1;
                        $orderData['pickup_now'] = $input['pickup_now'];
                        $orderData['pickup_time'] = $input['pickup_time'];
                    }

                    DB::table('orders')->insert(
                            $orderData
                    );
                    $order_id = DB::getPdo()->lastInsertId();
                }

                if (Session::get('coupon')) {
                    // insert applied coupom
                    $coupondata = array(
                        'coupon' => Session::get('coupon'),
                        'user_id' => $user_id,
                        'created' => date('Y-m-d H:i:s')
                    );
                    DB::table('applied_coupons')->insert(
                            $coupondata
                    );
                }
                //  print_r($content); exit;
                $sumTotal = 0;
                $menu_items = array();
                if (!empty($content)) {

                    foreach ($content as $cartData) {
                        $subtotal = 0;

                        $menu_items[] = $cartData['id'];
                        $menu_id = $cartData['id'];
                        $variant_type = "";

                        if (isset($cartData['variant_type'])) {
                            $explode = explode(',', $cartData['variant_type']);
                            if ($explode) {
                                foreach ($explode as $explodeVal) {

                                    $addonV = DB::table('variants')
                                            ->where('variants.id', $explodeVal)
                                            ->first();
                                    //    echo "<pre>"; print_r($addonV);
                                    if ($addonV) {
                                        $sub_total = $addonV->price * $cartData['quantity'];
                                        $subtotal = $subtotal + $sub_total;
                                    }
                                }
                                $variant_type = $cartData['variant_type'];
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
                                    // echo "<pre>"; print_r($addonV);
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
                            'menu_id' => $cartData['id'],
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
                        // echo "<pre>"; print_r($data); exit;
                        DB::table('order_item')->insert(
                                $data
                        );
                    }
                }
                //echo $sumTotal; exit;
                $gTotal = $sumTotal + $delivery_charge + $tax - $discount;
                //echo $gTotal; exit;
                /*                 * * save orders ** */
                $menu_items_id = implode(',', $menu_items);
                DB::table('orders')
                        ->where('id', $order_id)
                        ->update(array('order_item_id' => $menu_items_id));
                //  echo $order_id; exit;

                Session::put('gTotal', $gTotal);

//                $cart->destroy();
//                Session::forget('coupon');
                Session::forget('modifyorder');
                Session::forget('order_id');


                //return Redirect::to('/user/myaccount')->with('success_message', 'Thanks for ordering with us. Your order details are submitted successfully. You will receive confirmaion message after acceptance of your order.');
                return Redirect::to('/payment/openshop/' . $order_id);
            }
        }
    }

    public function notify($slug) {

        //$this->logincheck('user/notify/'.$slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }


        $this->layout->title = TITLE_FOR_PAGES . 'Payment Success';
        $user_id = Session::get('user_id');



        $shopData = DB::table('orders')
                ->where('slug', $slug)
                ->first();

        $tax = $shopData->tax;
        $delivery_charge = $shopData->delivery_charge;
        $discount = $shopData->discount;
        $total = array();

        $restroData = DB::table('users')
                ->where('id', $user_id)
                ->first();


        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();

        $orders = DB::table('orders')
                ->where('slug', $slug)
                ->first();
        if (!$orders) {
            return Redirect::to('/');
        }

        $userData = DB::table('users')
                ->where('id', $orders->user_id)
                ->first();

        $customerData = $userData;
        $customerContent = "";
        $bothContent = "";
        $headerContent = '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
        $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: left;  padding: 7px;" colspan="4">Hello ' . $userData->first_name . ' ' . $userData->last_name . ',</td>';
        $customerContent .= '<tr><td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: left;  padding: 7px;" colspan="4">Your food is ready, please pickup your food from ' . $restroData->first_name . '</td></tr>';
        $endContent = '</table>';



        $mailtocustomer = $headerContent . $customerContent . $endContent;

        $orderid = $shopData->id;



        $content = DB::table('order_item')
                ->where('order_id', $shopData->id)
                ->get();

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
        $VendorTemp = "";

        $VendorTemp .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $shopData->order_number . ' (' . $restroData->first_name . ')
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
                                       ' . App::make("HomeController")->numberformat($menuData->addon_price, 2) . ' ' . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . $cartData->quantity . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . App::make("HomeController")->numberformat($sub_total, 2) . ' ' . '
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
                                   ' . App::make("HomeController")->numberformat($totalVendor, 2) . ' ' . '
                                </td>
                                  </tr>';


        if ($shopData->discount)
            $cardata .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                                   Discount
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($discount, 2) . ' ' . '
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
                                   ' . App::make("HomeController")->numberformat($tax / count($shopData), 2) . ' ' . '
                                </td>
                                  </tr>';
            $totalVendor = $totalVendor + $tax;
        }

        $cardata .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                                   Delivery Charge (' . $shopData->delivery_type . ')
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                                   ' . App::make("HomeController")->numberformat($delivery_charge / count($shopData), 2) . ' ' . '
                                </td>
                                  </tr>';
        $gTotal = $totalVendor + $delivery_charge / count($shopData);

        $totalVendor = $totalVendor + $delivery_charge / count($shopData);


        // $totalVendor = $totalVendor + $delivery_charge/count($shopData);



        $cardata .= '<tr>
                                <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                                  Grand Total
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                                   ' . App::make("HomeController")->numberformat($totalVendor, 2) . ' ' . '
                                </td>
                                  </tr>';

        $cardata .= '</table>';

        // echo $cardata; exit;    
        //  echo $mailtocustomer.$cardata; exit;


        $mailSubjectCustomer = 'Your food is ready from: ' . $restroData->first_name;



        /**         * send mail to caterer ** */
        $caterer_mail_data = array(
            'text' => '',
            'orderContent' => $mailtocustomer . $cardata,
            'mailSubjectRestaurant' => $mailSubjectCustomer,
            'sender_email' => $userData->email_address,
            'firstname' => '',
        );

        // return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

        Mail::send('emails.template', $caterer_mail_data, function($message) use ($caterer_mail_data) {
            $message->setSender(array(MAIL_FROM => SITE_TITLE));
            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
            $message->to($caterer_mail_data['sender_email'], $caterer_mail_data['firstname'])->subject($caterer_mail_data['mailSubjectRestaurant']);
        });




        return Redirect::to('user/myaccount/')->with('success_message', 'Nofication is successfully send to customer, you can also send notifcations multiple times.')
                        ->with('shopData', $shopData);
    }

    function createSlug($string) {
        $string = substr(strtolower($string), 0, 35);
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $return = strtolower(preg_replace($old_pattern, $new_pattern, $string)) . rand(111111, 9999999) . time();
        return $return;
    }

    function totalcartvalue() {
        $this->layout = false;
        return View::make('home/totalcartvalue');
    }

    function createOrderNumber() {
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

    function createMainOrderNumber() {
        $lastOrder = DB::table('main_order')
                        ->orderBy('id', 'DESC')->first();
        if (!empty($lastOrder)) {
            $lastOrderId = $lastOrder->id;
            $lastOrderId = $lastOrderId + 1;
        } else {
            $lastOrderId = 00001;
        }
        $st = "FOSM";
        $orderNum = $st . sprintf('%06d', $lastOrderId);
        return $orderNum;
    }

    function fav() {
        if (isset($_COOKIE["browser_session_id"]) && $_COOKIE["browser_session_id"] != '') {
            $browser_session_id = $_COOKIE["browser_session_id"];
        } else {
            $browser_session_id = session_id();
            setcookie("browser_session_id", $browser_session_id, time() + 60 * 60 * 24 * 7, "/");
        }

        $this->layout = false;
        $input = Input::all();
        $user_id = Session::get('user_id');
        $menu_id = $input['id'];
        $type = $input['type'];
        if ($type == 'like') {
            if ($user_id > 0) {
                $menuData = DB::table('menu_item')
                                ->where('id', $menu_id)->first();  // get menu data from menu table
                $caterer_id = $menuData->user_id;
                $like = array(
                    'user_id' => $user_id,
                    'menu_id' => $menu_id,
                    'caterer_id' => $caterer_id,
                    'session_id' => $browser_session_id,
                    'created' => date('Y-m-d H:i:s'),
                    'slug' => $this->createSlug('like')
                );
                DB::table('favorite_menu')->insert(
                        $like
                );
                return View::make('home/fav')->with('menu_id', $menu_id)->with('type', $type);
            } else {
                $allcount = DB::table('favorite_menu')
                        //->where('favorite_menu.user_id', $user_id)
                        ->where('favorite_menu.session_id', $browser_session_id)
                        // ->where('favorite_menu.menu_id', $user->id)
                        ->get(); // chk favorite
                //echo count($allcount);
                if (count($allcount) >= 4) {
                    return View::make('home/max')->with('menu_id', $menu_id)->with('mode', 'max')->with('type', 'unlike');
                } else {
                    $menuData = DB::table('menu_item')
                                    ->where('id', $menu_id)->first();  // get menu data from menu table
                    $caterer_id = $menuData->user_id;
                    $like = array(
                        'user_id' => $user_id,
                        'menu_id' => $menu_id,
                        'caterer_id' => $caterer_id,
                        'session_id' => $browser_session_id,
                        'created' => date('Y-m-d H:i:s'),
                        'slug' => $this->createSlug('like')
                    );
                    DB::table('favorite_menu')->insert(
                            $like
                    );
                    return View::make('home/fav')->with('menu_id', $menu_id)->with('type', $type);
                }
            }
        } else {
            DB::table('favorite_menu')->where('menu_id', $menu_id)->where('user_id', $user_id)->where('session_id', $browser_session_id)->delete();
            return View::make('home/fav')->with('menu_id', $menu_id)->with('type', $type);
        }
    }

    function applycoupon() {

        // get coupon code amount
        $cart = new Cart(new CartSession, new Cookie);
        $content = $cart->contents(true);

        $this->layout = false;
        $input = Input::all();
        $user_id = Session::get('user_id');
        $coupon = $input['coupon'];

        // check coupon code is valid or not
        $menuData = DB::table('coupons')
                ->where('code', $coupon)
                ->where('start_time', "<=", date('Y-m-d'))
                ->where('end_time', ">=", date('Y-m-d'))
                ->where('status', '1')
                ->first();

        // print_r($menuData); exit;
        if (!empty($content)) {
            foreach ($content as $cat) {
                $restro_id = $cat['caterer_id'];
            }
        }

        if (empty($content)) {
            $array = array(
                'valid' => false,
                'error' => 'Cart is empty'
            );
            return json_encode($array);
        }

        if (!empty($menuData)) {
            // echo $menuData->user_id.'|'.$restro_id;
            if ($menuData->user_id == 0 || $menuData->user_id == $restro_id) {

                // check coupon code is used once
                $check = DB::table('applied_coupons')
                        ->where('coupon', $coupon)
                        ->where('user_id', $user_id)
                        ->first();
                if (!empty($check)) {
                    $array = array(
                        'valid' => false,
                        'error' => 'Coupon code is invalid'
                    );
                } else {

                    Session::put('coupon', $coupon);
                    $array = array(
                        'valid' => true,
                    );
                }
            } else {
                $array = array(
                    'valid' => false,
                    'error' => 'Coupon code is invalid'
                );
            }
        } else {
            $array = array(
                'valid' => false,
                'error' => 'Coupon code is invalid'
            );
        }
        return json_encode($array);
    }

    function removecoupon() {
        $array = array(
            'valid' => true,
        );
        Session::forget('coupon');
        return json_encode($array);
    }

    function showReview($slug = "", $slug2 = "") {

        $user_id = Session::get('user_id');

        // get current user details
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        //   print_r($userData);
        // get carters details
        $caterer = DB::table('users')
                ->where('users.slug', $slug)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                ->leftjoin('areas', 'areas.id', '=', 'users.area')
                ->leftjoin('cities', 'cities.id', '=', 'users.city')
                ->select("users.*", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name")
                ->first();

        if (empty($caterer)) {
            return Redirect::to('/');
        }

        $orderData = DB::table('orders')
                ->where('slug', $slug2)
                ->first();

//        if (empty($orderData)) {
//            return Redirect::to('/');
//        }
        // get all reviews     
        $query = DB::table('reviews');
        $query
                ->where('reviews.caterer_id', $caterer->id)
                ->where('reviews.status', '1')
                ->join('users', 'users.id', '=', 'reviews.user_id')
                ->select('reviews.*', 'users.first_name', 'users.last_name', 'users.profile_image');

        $records = $query->orderBy('reviews.id', 'desc')->paginate(10);

        $this->layout->title = TITLE_FOR_PAGES . 'Post Your Reviews - ' . $caterer->first_name . " " . $caterer->last_name;

        $input = Input::all();
        if (Request::isMethod('post')) {


            $rules = array(
                'comment' => 'required'
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('restaurants/reviews/' . $caterer->slug . '/' . $orderData->slug)->withErrors($validator)->withInput(Input::all());
            } else {

                $update = array(
                    'is_review' => '1',
                );
                DB::table('orders')
                        ->where('id', $orderData->id)
                        ->update($update);

                // delete reviews condition
                DB::table('user_reviews')->where('user_id', Session::get('user_id'))->where('caterer_id', $caterer->id)->delete();

                $saveUser = array(
                    'comment' => $input['comment'],
                    'quality' => $input['quality'],
                    'packaging' => $input['packaging'],
                    'delivery' => $input['delivery'],
                    'caterer_id' => $caterer->id,
                    'user_id' => $user_id,
                    'slug' => $this->createSlug($input['comment']),
                    'status' => "1",
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('reviews')->insert(
                        $saveUser
                );
                //  echo "<pre>"; print_r($input);
                /* Mail to Restro */
                $quality = "";
                $packaging = "";
                $delivery = "";

                $avg_ratng = round(($input['quality']));
                for ($i = 0; $i < $avg_ratng; $i++) {
                    $quality .= '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                }
                for ($j = 5; $j > $avg_ratng; $j--) {
                    $quality .= '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                }

                $packagingC = round(($input['packaging']));
                for ($i = 0; $i < $packagingC; $i++) {
                    $packaging .= '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                }
                for ($j = 5; $j > $packagingC; $j--) {
                    $packaging .= '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                }

                $deliveryC = round(($input['delivery']));
                for ($i = 0; $i < $deliveryC; $i++) {
                    $delivery .= '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                }
                for ($j = 5; $j > $deliveryC; $j--) {
                    $delivery .= '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                }

                $customerData = $userData;
                $customerContent = "";
                $bothContent = "";
                $headerContent = '<table style="width:100%; border-collapse: collapse; text-align: left;">';

                $customerContent = '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: left;  padding: 7px;" colspan="4">' . $userData->first_name . ' ' . $userData->last_name . ' leaved review for your restaurant.</td>';
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
                                    Quality & Taste: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $quality . '
                                </td>
                            </tr>';

                $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Packaging: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $packaging . '
                                </td>
                            </tr>';
                $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Delivery: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $delivery . '
                                </td>
                            </tr>';


                $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Comment: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . nl2br($input['comment']) . '
                                </td>
                            </tr>';





                $endContent = '</table>';


                $mailtocustomer = $headerContent . $customerContent . $bothContent . $endContent;



                // echo $mailtoadmin; exit;


                $mailSubjectRestaurant = 'New review on your restaurant on ' . SITE_TITLE;

                /**                 * send mail to caterer ** */
                $caterer_mail_data = array(
                    'text' => 'New review on your restaurant on ' . SITE_TITLE,
                    'orderContent' => $mailtocustomer,
                    'mailSubjectRestaurant' => $mailSubjectRestaurant,
                    'sender_email' => $caterer->email_address,
                    'firstname' => $caterer->first_name . ' ' . $caterer->last_name,
                );

                // return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

                Mail::send('emails.template', $caterer_mail_data, function($message) use ($caterer_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($caterer_mail_data['sender_email'], $caterer_mail_data['firstname'])->subject($caterer_mail_data['mailSubjectRestaurant']);
                });
                /* */


                $id = DB::getPdo()->lastInsertId();

                return Redirect::to('restaurants/reviews/' . $caterer->slug . '/' . $orderData->slug)->with('success_message', 'Review successfully posted.');
            }
        } else {
            $this->layout->content = View::make('/home/review')
                    ->with('caterer', $caterer)
                    ->with('records', $records)
                    ->with('userData', $userData)
                    ->with('orderData', $orderData);
        }
    }

    function cronnotificationconfirm() {

        $this->layout = false;

        // get all orders whose status is delivered
        $userData = DB::table('orders')
                ->select('orders.user_id', 'orders.id as order_id', 'orders.caterer_id', DB::raw('u1.first_name'), DB::raw('u1.last_name'), DB::raw('u1.email_address'), DB::raw('u1.contact'), DB::raw('u2.first_name as cfirst_name'), DB::raw('u2.last_name as clast_name'), DB::raw('u2.email_address as cemail_address'), DB::raw('u2.contact as ccontact'), DB::raw('u2.slug'))
                ->join('users as u1', DB::raw('u1.id'), '=', 'orders.user_id')
                ->join('users as u2', DB::raw('u2.id'), '=', 'orders.caterer_id')
                ->where('orders.status', 'Delivered')
                ->where('orders.is_review', '0')
                ->get();

        if (!empty($userData)) {
            foreach ($userData as $key => $val) {

                // insert tbl_user_reviews
                $data = array(
                    'user_id' => $val->user_id,
                    'caterer_id' => $val->caterer_id,
                    'created' => date('Y-m-d H:i:s'),
                );
//                DB::table('user_reviews')->insert(
//                        $data
//                );
                // update orders is_review
                $update = array(
                    'is_review' => '1',
                );
                DB::table('orders')
                        ->where('id', $val->order_id)
                        ->update($update);

                // send notification to user
                $mail_data = array(
                    'text' => '<b>Dear  ' . $val->first_name . " " . $val->last_name . ", </b><br/><br/>It's time to place a review on '<b>" . $val->cfirst_name . " " . $val->clast_name . "</b>' about their Quality, Packaging and Delivery. <br/> <br/> <a href='" . HTTP_PATH . "restaurants/reviews/" . $val->slug . "'>Click here</a> to place your review.",
                    'noify_email' => $val->email_address,
                    'noify_user' => $val->first_name . " " . $val->last_name
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['noify_email'], $mail_data['noify_user'])->subject("It's time to place a review");
                });
            }
        }die;
    }

    public function getmenu($id) {

        $menuData = DB::table('menu_item')
                        ->where('id', $id)->first();
        //print_r($menuData); exit;
        $caterer = DB::table('users')
                ->where('users.id', $menuData->user_id)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                ->leftjoin('areas', 'areas.id', '=', 'users.area')
                ->leftjoin('cities', 'cities.id', '=', 'users.city')
                ->select("users.*", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name", 'opening_hours.catering_type')
                ->first();

        // return View::make('home/menudetail')->with('menuData', $menuData)->with('caterer', $caterer);

        $cuisine = DB::table('cuisines')
                ->orderBy('cuisines.name', 'asc')
                ->where("menu_item.user_id", "=", $caterer->id)
                ->where("cuisines.status", "=", 1)
                ->join('menu_item', 'cuisines.id', '=', 'menu_item.cuisines_id')
                ->select("cuisines.name", "cuisines.id")
                ->groupBy('cuisines.id')
                ->get();

        // get cart contents
        $cart = new Cart(new CartSession, new Cookie);
        $content = $cart->contents(true);

        //$this->layout->title = TITLE_FOR_PAGES . 'Restauranting Menu - ' . $caterer->first_name . " " . $caterer->last_name;
        return View::make('/home/menudetail')
                        ->with('caterer', $caterer)
                        ->with("menuData", $menuData)
                        ->with("cuisine", $cuisine)
                        ->with("cart_content", $content)
                        ->with("cart", $cart);
    }

    public function numberformat($price, $coun = 2) {
        return CURR . ' ' . number_format($price, 2);
    }

    public function thumbmode($type, $dataid) {
        $user_id = Session::get('user_id');
        if ($user_id > 0) {
            $ifR = DB::table('thumbs')
                    ->where('user_id', $user_id)
                    ->where('restro_id', $dataid)
                    ->first();
            if ($ifR) {
                $update = array(
                    'type' => "$type",
                );
                DB::table('thumbs')
                        ->where('id', $ifR->id)
                        ->update($update);
            } else {
                $data = array('user_id' => $user_id, 'restro_id' => $dataid, 'type' => $type);
                DB::table('thumbs')->insert(
                        $data
                );
            }
        }
        $allLikedata = DB::table('thumbs')
                ->where('restro_id', $dataid)
                ->where('type', 'like')
                ->get();

        $allDisLikedata = DB::table('thumbs')
                ->where('restro_id', $dataid)
                ->where('type', 'dislike')
                ->get();
        echo json_encode(array('valid' => 1, 'likes' => count($allLikedata), 'dislike' => count($allDisLikedata)));
        exit;
    }

}
