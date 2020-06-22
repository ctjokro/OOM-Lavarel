<?php
$user_id = Session::get('user_id');

$userData = DB::table('users')
        ->select('users.*', "cities.name as city_name", "areas.name as area_name")
        ->where('users.id', $user_id)
        ->leftjoin("cities", 'cities.id', '=', 'users.city')
        ->leftjoin("areas", 'areas.id', '=', 'users.area')
        ->first();
//print_r($userData); exit;
?>
<div class="firstdiv">
    <div class="new_top_section">
        <div class="user_bx">
            <div class="side_img">
                <div class="user_pic">
                    <?php
                    if (file_exists(UPLOAD_FULL_PROFILE_IMAGE_PATH . '/' . $userData->profile_image) && $userData->profile_image != "") {
                        echo HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $userData->profile_image, '', array());
                    } else {
                        echo HTML::image('public/img/front/nouser.png', '', array());
                    }
                    ?>
                </div>
                <div class="chan_pich">
                    <?php echo html_entity_decode(HTML::link('user/changePicture', '<i class="fa fa-camera" aria-hidden="true"></i>', array('class' => 'btn btn-primary', 'title' => 'Change Picture'))); ?>
                </div></div>

            <div class="_vtttx">
                <div class="ad_right">
                    <h2>Welcome!</h2>
                    <h1><?php echo $userData->first_name . ' ' . $userData->last_name; ?></h1>
                    <?php if ($userData->user_type == "Restaurant") { ?>
                        <div class="rating_bx rating_bx_new_ttt">
                            <?php
                            // get all avg ratings
                            $ratings = DB::table('reviews')
                                    ->select(DB::raw("avg(quality) as quality"), DB::raw("avg(packaging) as packaging"), DB::raw("avg(delivery) as delivery"), DB::raw("count(id) as counter"))
                                    ->where('caterer_id', $user_id)
                                    ->where('status', '1')
                                    ->first();
                            ?>
                            <?php
                            $avg_ratng = round(($ratings->quality + $ratings->packaging) / 2);
                            for ($i = 0; $i < $avg_ratng; $i++) {
                                echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                            }
                            for ($j = 5; $j > $avg_ratng; $j--) {
                                echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                            }
                            ?>


                            </span> 

                        </div>
                    <?php } ?>
                    <div class="area_name"><?php
                    echo $userData->address ? $userData->address . ', ' : "";
                    echo $userData->area_name ? ucfirst($userData->area_name) : "";
                    echo $userData->city_name ? ', ' . ucfirst($userData->city_name) : "";
                    ?></div>
                </div>
            </div>

        </div>
        <?php
        if (isset($_COOKIE["browser_session_id"]) && $_COOKIE["browser_session_id"] != '') {
            $browser_session_id = $_COOKIE["browser_session_id"];
        } else {
            $browser_session_id = session_id();
            setcookie("browser_session_id", $browser_session_id, time() + 60 * 60 * 24 * 7, "/");
        }


        if ($userData->user_type != 'Restaurant') {

            $Orders = DB::table('orders')
                    ->where('orders.user_id', Session::get('user_id'))
                    ->where('orders.status', "!=" , "Pending" )
                    ->count();

            $query = DB::table('favorite_menu');
            $query->leftjoin("menu_item", 'menu_item.id', '=', 'favorite_menu.menu_id');
            $query->leftjoin("users", 'users.id', '=', 'favorite_menu.caterer_id');
            $query->where('favorite_menu.user_id', $user_id)
                    ->orwhere('favorite_menu.session_id', $browser_session_id);
            $favorite_menu = $query->count();

            $reviews = DB::table('reviews')
                    ->where('reviews.user_id', Session::get('user_id'))
                    ->where('reviews.status', "1")
                    ->count();

            $url = "order/myorders";
        } else {
            $Orders = DB::table('orders')
                    ->where('orders.caterer_id', Session::get('user_id'))
                    ->where('orders.status', "!=" , "Pending" )
                    ->count();

            $query = DB::table('favorite_menu');
            $query->leftjoin("menu_item", 'menu_item.id', '=', 'favorite_menu.menu_id');
            $query->leftjoin("users", 'users.id', '=', 'favorite_menu.caterer_id');
            $query->where('favorite_menu.user_id', $user_id)
                    ->orwhere('favorite_menu.session_id', $browser_session_id);
            $favorite_menu = $query->count();

            $reviews = DB::table('reviews')
                    ->where('reviews.caterer_id', Session::get('user_id'))
                    ->where('reviews.status', "1")
                    ->count();

            $url = "order/receivedorders";
        }
        ?>
        <div class="right_top_dash">
            <div class="right_dsh">
                <div class="right_dashh">
                    <div class="inne green">
                        <img src="{{ URL::asset('public/img/front') }}/box_icon_1.svg" alt="logo" />
                        <p>Orders</p> 
                        <h2><?php echo $Orders; ?></h2>
                        <a href="<?php echo HTTP_PATH . $url ?>" class="stats__link"></a></div>
                </div>
            </div>   
            <div class="right_dsh">
                <div class="right_dashh">
                    <div class="inne yello">
                        <img src="{{ URL::asset('public/img/front') }}/box_icon_2.svg" alt="logo" />
                        <p>Favourites</p> 
                        <h2><?php echo $favorite_menu; ?></h2>
                        <a href="<?php echo HTTP_PATH ?>user/myfavourite" class="stats__link"></a></div>
                </div>
            </div>   
            <div class="right_dsh">
                <div class="right_dashh">
                    <div class="inne red">
                        <img src="{{ URL::asset('public/img/front') }}/box_icon_3.svg" alt="logo" />
                        <p>Reviews</p> 
                        <h2><?php echo $reviews; ?></h2>
                        <a href="<?php echo HTTP_PATH ?>user/myreviews" class="stats__link"></a></div>
                </div>
            </div>   

        </div>
    </div>
</div>

