@section('content')
{{ HTML::script('public/js/front/jquery.jscroll.js'); }}
<script>
    $(document).ready(function() {
        $(".lis_left_menu").jScroll();
        $(".show_items").click(function() {
            var class_name = $(this).attr("id");
            $('html,body').animate({
                scrollTop: $("." + class_name).offset().top },
            'slow');
        })
    })
    $(document).on("click", ".counter_number", function() {
        $('.showcartloader').show();
        var type = $(this).attr("alt");
        var id_val = $(this).attr("id_val");
        var value = $('.preparation_time_' + id_val).val();
        value = value ? parseInt(value) : 0;
        if (type == 'minus') {
            value = (value - 1 < 0) ? 0 : (value - 1);
            $('.preparation_time_' + id_val).val(value);
            if (value < 0)
                return false;
        } else {
            if (value >= 999) {
                $('.preparation_time_' + id_val).val(value);
            }
            else {
                value = value + 1;
                $('.preparation_time_' + id_val).val(value);
            }
        }

        // ajax update for orfering food
        var data = {
            id: id_val,
            qty: value,
            type: type
        }
        $.ajax({
            url: "<?php echo HTTP_PATH . "home/addtocart" ?>",
            dataType: 'json',
            type: 'POST',
            data: data,
            success: function(data, textStatus, XMLHttpRequest)
            {
                if (data.valid)
                {
                    $(".carts_bx").html(data.data);
                    $.ajax({
                        url: "<?php echo HTTP_PATH . "home/totalcartvalue" ?>",
                        type: 'POST',
                        success: function(data, textStatus, XMLHttpRequest)
                        {
                            $('.showcartloader').hide();
                            $("#cart_bt").html(data);
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown)
                        {

                        }
                    });
                }
                else
                {
                    $('.showcartloader').hide();
                    swal({
                        title: "Sorry!",
                        text: data.message,
                        type: "error",
                        html: true
                    });
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                $('.showcartloader').hide();

                // Message
                swal({
                    title: "Sorry!",
                    text: "Error while contacting server, please try again",
                    type: "error",
                    html: true
                });
                $(".all_bg").hide();
            }
        });


    })


    $(document).on("click", ".leave_comment", function() {
        var id = $(this).attr("alt");
        $("#comment-box-" + id).toggle();
    })

    $(document).on("keyup", ".small-comment", function() {
        // update to cart array
        var data = {data: $(this).val(), id: $(this).attr('alt')}
        $.ajax({
            url: "<?php echo HTTP_PATH . "home/updatecarttext" ?>",
            dataType: 'json',
            type: 'POST',
        });
    })
    $(document).on("click", ".remove_cart", function() {
        $('.showcartloader').show();

        var id = $(this).attr("alt");
        var data = {id: $(this).attr('alt')}
        $(".preparation_time_" + id).val(0);
        $.ajax({
            url: "<?php echo HTTP_PATH . "home/removecart" ?>",
            dataType: 'json',
            type: 'POST',
            data: data,
            success: function(data, textStatus, XMLHttpRequest)
            {
                if (data.valid)
                {
                    $('.showcartloader').hide();
                    $(".carts_bx").html(data.data);
                    $.ajax({
                        url: "<?php echo HTTP_PATH . "home/totalcartvalue" ?>",
                        type: 'POST',
                        success: function(data, textStatus, XMLHttpRequest)
                        {

                            $("#cart_bt").html(data);
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown)
                        {

                        }
                    });
                }
                else
                {
                    $('.showcartloader').hide();
                    swal({
                        title: "Sorry!",
                        text: data.message,
                        type: "error",
                        html: true
                    });
                }
            }
        });
    })
    function like(menu_id) {
        $('#showlikeloader' + menu_id).show();
        var id = menu_id;
        var type = "like";
        var data = {id: menu_id, type: type}
        $.ajax({
            url: "<?php echo HTTP_PATH . "home/fav" ?>",
            type: 'POST',
            data: data,
            success: function(data, textStatus, XMLHttpRequest)
            {
                $('#showlikeloader' + menu_id).hide();
                $("#like" + menu_id).attr("onclick", "unlike(" + menu_id + " )");
                $('#like' + menu_id).html(data);
                swal({
                    title: "Great!",
                    text: "Loved It Successfully",
                    type: "success",
                    html: true
                });

            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {

            }
        });
    }

    function unlike(menu_id) {
        $('#showlikeloader' + menu_id).show();
        var id = menu_id;
        var type = "unlike";
        var data = {id: menu_id, type: type}
        $.ajax({
            url: "<?php echo HTTP_PATH . "home/fav" ?>",
            type: 'POST',
            data: data,
            success: function(data, textStatus, XMLHttpRequest)
            {
                $('#showlikeloader' + menu_id).hide();
                $("#like" + menu_id).attr("onclick", "like(" + menu_id + " )");
                $('#like' + menu_id).html(data);
                swal({
                    title: "Great!",
                    text: "Not Loved it",
                    type: "success",
                    html: true
                });

            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {

            }
        });
    }
    
    function loginchk() {
     swal({
                    title: "Sorry!",
                    text: "Please Login or Register for make items favorite!",
                    type: "error",
                    html: true
                });
                return false;
    }
</script>

<div class="clear"></div>
<div class="wrapper">
    <div class="list_bx">

        <div class="listing_bxs listing_bore">
            <div class="listing_bxs_left">
                <?php
                if (file_exists(DISPLAY_FULL_PROFILE_IMAGE_PATH . $caterer->profile_image) and $caterer->profile_image) {
                    ?>
                    <a href="{{HTTP_PATH.'restaurants/menu/'.$caterer->slug}}"><img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.DISPLAY_FULL_PROFILE_IMAGE_PATH.$caterer->profile_image.'&w=252&h=180&zc=2&q=100') }}" alt="img" /></a>
                    <?php
                } else {
                    ?>
                    <a href="{{HTTP_PATH.'restaurants/menu/'.$caterer->slug}}"> <img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=252&h=180&zc=2&q=100') }}" alt="img" /></a>
                    <?php
                }
                ?>
            </div>
            <div class="listing_bxs_right">
                <h1>{{ html_entity_decode(HTML::link('restaurants/menu/'.$caterer->slug,ucwords($caterer->first_name." ".$caterer->last_name ), ['class'=>'link-menu'])); }}</h1>
                <p><span>{{$caterer->city_name.($caterer->area_name?", ".$caterer->area_name:"") }}</span></p>
                <div class="open_img">
                    <?php
                    $open = 0;
                    // get carters open/close status
                    if ($caterer->open_close) {

                        $open_days = explode(",", $caterer->open_days);
                        if (strtotime($caterer->start_time) <= time() && time() <= strtotime($caterer->end_time) and in_array(strtolower(date('D')), $open_days)) {
                            $open = 1;
                            ?>
                            <img src="{{ URL::asset('public/img/front') }}/open_img2.png" alt="Closed" />
                            <?php
                        } else {
                            ?>
                            <img src="{{ URL::asset('public/img/front') }}/close_img2.png" alt="Closed" />
                            <?php
                        }
                    } else {
                        ?>
                        <img src="{{ URL::asset('public/img/front') }}/close_img2.png" alt="Closed" />
                        <?php
                    }
                    ?>
                </div>
                <div class="rating_bx"><img src="{{ URL::asset('public/img/front') }}/rating_img.png" alt="img" />
                    <span>15 Ratings</span>
                    <ul class="list_menus">
                        <li>
                            <h3>Minimum Order</h3>
                            <h2>{{$caterer->minimum_order? CURR." ".$caterer->minimum_order:" - "}}</h2>
                        </li>
                        <li>
                            <h3>Opening Hours</h3>
                            <h2>{{$caterer->start_time?date("h:i a", strtotime($caterer->start_time))." - ".date("h:i a", strtotime($caterer->end_time)):" - "}}</h2>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
            

        <?php
        
        $input = Input::all();
        if (!$items->isEmpty()) {
            
            ?>
            <div class="list_bx_dt">
                <div class="lis_left_menu lis_left_menu_detail">
                    <div class="cat_menus2">
                        <ul>
                            <?php
                            if (!empty($cuisine)) {
                                foreach ($cuisine as $c) {
                                    ?>
                                    <li class="show_items" id="{{'cls_'.$c->id}}">
                                        <div class="cuisine-menu">
                                            <label>{{ucfirst($c->name)}}</label>
                                            <i class="fa fa-server"></i>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                
                <div class="lis_right_site">
                    
                    <div class="lis_bxx">
                    
                        <div class="lis_bxx_leftd">
                            <?php
                            if (!$items->isEmpty()) {
                                $flag = "";

                                // create cart content array
                                $key_array = array();
                                if (!empty($cart_content)) {
                                    foreach ($cart_content as $key)
                                        $key_array[$key['id']] = $key['quantity'];
                                }
                                foreach ($items as $user) {
                                    ?>
                                    <div class="listing_bxsse">

                                        <?php if ($flag <> $user->cuisines_name) { ?>
                                            <a href="javascript:void(0)" class="shows {{'cls_'.$user->cuisines_id}}">
                                                <p> 
                                                    <?php
                                                    $flag = $user->cuisines_name;
                                                    echo ucfirst($flag);
                                                    ?>
                                                </p>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                        <div class="listing_bxs listing_bxs_white">
                                            <div class="listing_bxs_left2">
                                                <div class="img_fream">
                                                    <div class="img_fream_img">
                                                        <?php
                                                        if (file_exists(UPLOAD_FULL_ITEM_IMAGE_PATH . $user->image) and $user->image) {
                                                            ?>
                                                            <img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.UPLOAD_FULL_ITEM_IMAGE_PATH.$user->image.'&w=125&h=107&zc=2&q=100') }}" alt="img" />
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=125&h=107&zc=2&q=100') }}" alt="img" />
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="listing_bxs_right2">
                                                <h1>{{$user->item_name}}</h1>
                                                <div class="coma">
                                                    <div class="love_bx">
                                                        <div class="like_n">{{CURR." ".$user->price}}</div>
                                                        <?php if (Session::has('user_id')) {
                                                            ?>
                                                            <?php
                                                            $user_id = Session::get('user_id');
                                                            $chckFav = DB::table('favorite_menu')
                                                                    ->where('favorite_menu.user_id', $user_id)
                                                                    ->where('favorite_menu.menu_id', $user->id)
                                                                    ->first(); // chk favorite
                                                            if (!empty($chckFav)) {
                                                                ?>
                                                                <div  class="inlikem" id ="like<?php echo $user->id; ?>"  onclick="unlike(<?php echo $user->id; ?>)">
                                                                    <span>
                                                                        <img src="{{ URL::asset('public/img/front') }}/like.png" alt="img" />
                                                                    </span>
                                                                    <div class="lone_mem" id="liketext<?php echo $user->id; ?>">Loved it</div>
                                                                </div>
                                                            <?php } else { ?>
                                                                <div  class="inlikem" id ="like<?php echo $user->id; ?>"  onclick="like(<?php echo $user->id; ?>)">
                                                                    <span id="likeimg<?php echo $user->id; ?>">
                                                                        <img src="{{ URL::asset('public/img/front') }}/unlike.png" alt="img" />
                                                                    </span>
                                                                    <div class="lone_mem" id="liketext<?php echo $user->id; ?>"></div>
                                                                </div>
                                                            <?php } ?>
                                                                <div class="showlikeloader" id="showlikeloader<?php echo $user->id; ?>"></div>
                                                        <?php } else { ?>
                                                                <div  class="inlikem" onclick="loginchk()">
                                                            <span>
                                                                <img src="{{ URL::asset('public/img/front') }}/unlike.png" alt="img" />
                                                            </span>
                                                            <div class="lone_mem"></div>
                                                                </div>
                                                        <?php } ?>



                                                    </div>
                                                    <?php
                                                    if ($open) {
                                                        ?>
                                                        <div class="maininfocont">
                                                            <div class="left_main2">
                                                                
                                                                <input type="button" value="-" class="but_1 counter_number"  id_val="{{$user->id}}"  alt="minus" /><input readonly="readonly" maxlength="3" type="text" value="{{isset($key_array[$user->id]) ? $key_array[$user->id] : 0}}" class='{{"preparation_time_".$user->id}}' />
                                                                <input type="button" value="+" class="but_2 counter_number" id_val="{{$user->id}}"  alt="plus" />
                                                                
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <p>{{$user->description}}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $flag = $user->cuisines_name;
                                }
                                ?>
                                <div class="dataTables_paginate paging_bootstrap pagination">
                                    {{ $items->appends(Input::except('page'))->links() }}
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        
                        <div class="carts_bx">
                            <h2>Your Food Basket</h2>
                            <div class="cart_box">
                                <div class="showcartloader" style="display:none;"><img src="{{ URL::asset('public/img/front') }}/loader.gif" alt="img" /></div>
                                <?php
                                $cartItems = DB::table('order_item')->whereIn('menu_id', explode(',', $orderData->order_item_id))->get(); // get cart menu of this order
                                if (!empty($cartItems)) {
                                    ?>


                                    <div class="crt_detal">
                                       
                                            
                                            <ul>
                                                <?php
                                                foreach ($cartItems as $cartData) {
                                                     $menuData = DB::table('menu_item')
                                                                ->where('id', $cartData->menu_id)->first();  // get menu data from menu table

                                                $sub_total = $cartData->base_price * $cartData->quantity;
                                                $total[] = $sub_total;
                                                    ?>
                                                    <li>
                                                        <p><span><?php echo $menuData->item_name; ?> </span></p>
                                                        <div class="removelinkcont"><a href="javascript:void(0)" alt="{{$cartData->id }}" class="remove_cart"><i class="fa fa-close"></i></a></div>
                                                        <div class="hird0">
                                                            <div class="commentlink"><a href="javascript:void(0)" class="leave_comment" alt="<?php echo $cartData->id; ?>"><i class="fa fa-edit"></i>Comment</a></div>
                                                            <div class="left_main">

                                                                <input type="button" value="-" class="but_1 counter_number"  id_val="{{$cartData->id }}"  alt="minus" />
                                                                <input readonly="readonly" maxlength="3" type="text" value="{{$cartData->quantity}}" class='<?php echo "preparation_time_" . $cartData->id; ?>' />
                                                                <input type="button" value="+" class="but_2 counter_number" id_val="{{$cartData->id}}"  alt="plus" />
                                                            </div>
                                                            <div class="maininfocontainer">

                                                                <div class="right_main"><strong>{{$cartData->sub_total}} </strong>{{CURR}}</div>
                                                            </div>
                                                        </div>
                                                        <div class="comment-box" style="display:none" id="comment-box-{{$cartData->id }}" alt="{{$cartData->id}}">
                                                            {{Form::textarea('comment', (isset($cartData->comment)?$cartData->comment:""),  array('class' => 'small-comment', 'alt'=>$cartData->id))}}
                                                        </div>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        
                                        <div class = "chus">
                                          <?php  $total = array_sum($total); ?>
                                            <div class = "summary">
                                                <strong>Total</strong>
                                                <p><strong>{{ App::make("HomeController")->numberformat($total ,2)}}</strong>{{CURR}}</p>
                                            </div>
                                            <div class = "summary total">
                                                <strong>Grand Total</strong>
                                                <p><strong>{{ App::make("HomeController")->numberformat($total ,2)}}</strong>{{CURR}}</p>
                                            </div>
                                        </div>
                                        <div class="submit-form">
                                            {{html_entity_decode(HTML::link('order/confirm', "Order", array('title' => "Confirm Order"))); }}
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="cart_img"><img src="{{ URL::asset('public/img/front') }}/cart_img.png" alt="img" /></div>
                                    <div class="cart_txt">Your food basket is empty</div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <?php
        } else {
            ?>
            <div class="no-record-list">
                No menu available for this carter
            </div>
            <?php
        }
        ?>
    </div>
</div>
<div class="clear"></div>
@stop