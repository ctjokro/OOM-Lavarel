@section('content')
{{ HTML::script('public/js/front/jquery.jscroll.js'); }}
<script>
    $(document).ready(function () {
        $(".lis_left_menu").jScroll();
        $(".show_items").click(function () {
            var class_name = $(this).attr("id");
            $('html,body').animate({
                scrollTop: $("." + class_name).offset().top},
            'slow');
        })
    })
    $(document).on("click", ".counter_number", function () {
        $('.showcartloader').show();
        var type = $(this).attr("alt");
        var id_val = $(this).attr("id_val");
        var value = $('.preparation_time_' + id_val).val();
        value = value ? parseInt(value) : 0;

        var submenus = "";
        var checkedVals = $('.smenu' + id_val + ':checkbox:checked').map(function () {
            return this.value;
        }).get();
        if (checkedVals != '') {
            submenus = checkedVals.join(",");
        } else {
            submenus = "";
        }

        // ajax update for orfering food
        var data = {
            id: id_val,
            qty: value,
            type: type,
            submenus: submenus
        }
        $.ajax({
            url: "<?php echo HTTP_PATH . "home/addtocart" ?>",
            dataType: 'json',
            type: 'POST',
            data: data,
            success: function (data, textStatus, XMLHttpRequest)
            {
                if (data.valid)
                {

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
                    $(".carts_bx").html(data.data);
                    $.ajax({
                        url: "<?php echo HTTP_PATH . "home/totalcartvalue" ?>",
                        type: 'POST',
                        success: function (data, textStatus, XMLHttpRequest)
                        {

                            $('.showcartloader').hide();
                            $("#cart_bt").html(data);
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown)
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
            error: function (XMLHttpRequest, textStatus, errorThrown)
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

    $(document).on("click", ".counter_number2", function () {
        $('.showcartloader').show();
        var type = $(this).attr("alt");
        var id_val = $(this).attr("id_val");
        var value = $('.preparation_time_' + id_val).val();
        value = value ? parseInt(value) : 0;

        // ajax update for orfering food
        var data = {
            id: id_val,
            qty: value,
            type: type,
            submenus: "already"
        }
        $.ajax({
            url: "<?php echo HTTP_PATH . "home/addtocart" ?>",
            dataType: 'json',
            type: 'POST',
            data: data,
            success: function (data, textStatus, XMLHttpRequest)
            {
                if (data.valid)
                {

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
                    $(".carts_bx").html(data.data);
                    $.ajax({
                        url: "<?php echo HTTP_PATH . "home/totalcartvalue" ?>",
                        type: 'POST',
                        success: function (data, textStatus, XMLHttpRequest)
                        {

                            $('.showcartloader').hide();
                            $("#cart_bt").html(data);
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown)
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
            error: function (XMLHttpRequest, textStatus, errorThrown)
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


    $(document).on("click", ".leave_comment", function () {
        var id = $(this).attr("alt");
        $("#comment-box-" + id).toggle();
    })

    $(document).on("keyup", ".small-comment", function () {
        // update to cart array
        var data = {data: $(this).val(), id: $(this).attr('alt')}
        $.ajax({
            url: "<?php echo HTTP_PATH . "home/updatecarttext" ?>",
            dataType: 'json',
            data: data,
            type: 'POST',
        });
    })
    $(document).on("click", ".remove_cart", function () {
        $('.showcartloader').show();

        var id = $(this).attr("alt");
        var data = {id: $(this).attr('alt')}
        $(".preparation_time_" + id).val(0);
        $.ajax({
            url: "<?php echo HTTP_PATH . "home/removecart" ?>",
            dataType: 'json',
            type: 'POST',
            data: data,
            success: function (data, textStatus, XMLHttpRequest)
            {
                if (data.valid)
                {
                    $('.showcartloader').hide();
                    $(".carts_bx").html(data.data);
                    $.ajax({
                        url: "<?php echo HTTP_PATH . "home/totalcartvalue" ?>",
                        type: 'POST',
                        success: function (data, textStatus, XMLHttpRequest)
                        {

                            $("#cart_bt").html(data);
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown)
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
            success: function (data, textStatus, XMLHttpRequest)
            {
                $('#showlikeloader' + menu_id).hide();
                $("#like" + menu_id).attr("onclick", "unlike(" + menu_id + " )");
                $('#like' + menu_id).html(data);
                swal({
                    title: "Great!",
                    text: "This food successfully added in your favorite list",
                    type: "success",
                    html: true
                });

            },
            error: function (XMLHttpRequest, textStatus, errorThrown)
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
            success: function (data, textStatus, XMLHttpRequest)
            {
                $('#showlikeloader' + menu_id).hide();
                $("#like" + menu_id).attr("onclick", "like(" + menu_id + " )");
                $('#like' + menu_id).html(data);
                swal({
                    title: "Great!",
                    text: "This food successfully removed from your favorite list",
                    type: "success",
                    html: true
                });

            },
            error: function (XMLHttpRequest, textStatus, errorThrown)
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
    function submenutoggle(val) {
        $('#submenusec' + val).slideToggle();
    }
</script>

<script type="text/javascript" src="<?php echo HTTP_PATH; ?>public/lib/jquery.raty.min.js"></script>
<script>
    $(document).ready(function () {

        $("#reviews").validate();
        $('.quality').raty({
            starOff: "<?php echo HTTP_PATH . "public/lib/img/star-off.png"; ?>",
            starOn: "<?php echo HTTP_PATH . "public/lib/img/star-on.png"; ?>",
            click: function (score, evt) {
                $('#quality').val(score);
            }
        });
        $('.packaging').raty({
            starOff: "<?php echo HTTP_PATH . "public/lib/img/star-off.png"; ?>",
            starOn: "<?php echo HTTP_PATH . "public/lib/img/star-on.png"; ?>",
            click: function (score, evt) {
                $('#packaging').val(score);
            }
        });
        $('.delivery').raty({
            starOff: "<?php echo HTTP_PATH . "public/lib/img/star-off.png"; ?>",
            starOn: "<?php echo HTTP_PATH . "public/lib/img/star-on.png"; ?>",
            click: function (score, evt) {
                $('#delivery').val(score);
            }
        });

        $(".reset_btn").click(function () {
            $('.delivery').raty('score', 0);
            $('.quality').raty('score', 0);
            $('.packaging').raty('score', 0);
        })

    })
</script>
<?php

function humanTiming($time) {

    $time = time() - $time; // to get the time since that moment
    $time = ($time < 1) ? 1 : $time;
    $tokens = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit)
            continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . " ago";
    }
}

// get all avg ratings
$ratings = DB::table('reviews')
        ->select(DB::raw("avg(quality) as quality"), DB::raw("avg(packaging) as packaging"), DB::raw("avg(delivery) as delivery"), DB::raw("count(id) as counter"))
        ->where('caterer_id', $caterer->id)
        ->where('status', '1')
        ->first();
?>
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
                <h1><a class="link-menu" href="javascript:void(0);"><?php echo ucwords($caterer->first_name . " " . $caterer->last_name); ?></a></h1>
                <div class="address"> 
                    <span>{{$caterer->address }} {{($caterer->area_name?", ".$caterer->area_name:"").' '.$caterer->city_name }}</span></div>
                <div class="open_img open_img_new">
                    <?php
                    $open = 0;
                    // get carters open/close status
                    if ($caterer->open_close) {

                       $day = date('D');
                        $open_days = explode(",", $caterer->open_days);

                        $vrrcy = explode(',',$caterer->start_time);
                        $endty = explode(',',$caterer->end_time);


                        $startime = array_combine($open_days, $vrrcy);
                        $endtime = array_combine($open_days, $endty);
                        $start = "";
                        if(isset($startime[strtolower(date('D'))])){

                            $start = $startime[strtolower(date('D'))];
                        }
                        if(isset($endtime[strtolower(date('D'))])){
                              $end = $endtime[strtolower(date('D'))];
                        }


                    if ((isset($start) && strtotime($start) <= time()) && (isset($end) && time() <= strtotime($end)) and in_array(strtolower(date('D')), $open_days)) {
                            $open = 1;
                            ?>
                            <span class="open same_style">open</span>
                            <?php
                        } else {
                            ?>
                             <span class="closee same_style">closed</span>
                            <?php
                        }
                    } else {
                        ?>
                        <span class="closee same_style">closed</span>
                        <?php
                    }
                    ?>
                </div>
                <div class="rating_bx">
                    <?php
                    $avg_ratng = round(($ratings->quality + $ratings->packaging + $ratings->delivery) / 3);
                    for ($i = 0; $i < $avg_ratng; $i++) {
                        echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                    }
                    for ($j = 5; $j > $avg_ratng; $j--) {
                        echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                    }
                    ?>
                    <span>{{$ratings->counter}} Ratings</span>
                    <ul class="list_menus">
<!--                        <li>
                            <h3>Minimum Order</h3>
                            <h2>{{$caterer->minimum_order?$caterer->minimum_order:" - "}}</h2>
                        </li>-->
                        <li>
                            <h3>Opening Hours</h3>
                            <h2>{{$caterer->start_time?date("h:i a", strtotime($caterer->start_time))." - ".date("h:i a", strtotime($caterer->end_time)):" - "}}</h2>
                        </li>
                    </ul>
                </div>
            </div>
        </div>



        <div class="list_bx_dt">
            <div class="list_bcome">
                <div class="comde">{{ View::make('elements.actionMessage')->render() }}
                    <div class="comd_revie">
                        <div class="co_comer">
                            Overall Rating
                            <div class="tres">
                                <?php
                                $avg_ratng = round(($ratings->quality + $ratings->packaging + $ratings->delivery) / 3);
                                for ($i = 0; $i < $avg_ratng; $i++) {
                                    echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                                }
                                for ($j = 5; $j > $avg_ratng; $j--) {
                                    echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="small-text">
                             Average rating based on {{$ratings->counter}} reviews.
                        </div>
                        <div class="all-ratings">
                            <ul class="rsde_bx_alrge">
                                <li class="_tttcrf">
                                    <label>Quality & Taste</label>
                                    <div class="tres">
                                        <?php
                                        $avg_ratng = round(($ratings->quality));
                                        for ($i = 0; $i < $avg_ratng; $i++) {
                                            echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                                        }
                                        for ($j = 5; $j > $avg_ratng; $j--) {
                                            echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                                        }
                                        ?>

                                    </div>
                                </li>
                                <li class="_tttcrf">
                                    <label>Packaging</label>
                                    <div class="tres">
                                        <?php
                                        $avg_ratng = round(($ratings->packaging));
                                        for ($i = 0; $i < $avg_ratng; $i++) {
                                            echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                                        }
                                        for ($j = 5; $j > $avg_ratng; $j--) {
                                            echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                                        }
                                        ?>
                                    </div>
                                </li>
                                <li class="_tttcrf">
                                    <label>Delivery</label>
                                    <div class="tres">
                                        <?php
                                        $avg_ratng = round(($ratings->quality));
                                        for ($i = 0; $i < $avg_ratng; $i++) {
                                            echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                                        }
                                        for ($j = 5; $j > $avg_ratng; $j--) {
                                            echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                                        }
                                        ?>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="mas_bx">
                            <?php
                            if (!$records->isEmpty()) {
                                foreach ($records as $data) {
                                    ?>
                                    <div class="max_bx">
                                        <div class="left_imfd">
                                            <?php
                                            if (file_exists(UPLOAD_FULL_PROFILE_IMAGE_PATH . '/' . $data->profile_image) && $data->profile_image != "") {
                                                echo HTML::image(HTTP_PATH . 'public/assets/timthumb.php?src=' . DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image . '&w=70&h=70&zc=2&q=100', '', array());
                                            } else {
                                                echo HTML::image(HTTP_PATH . 'public/assets/timthumb.php?src=' . HTTP_PATH . 'public/img/front/nouser.png&w=70&h=70&zc=2&q=100', '', array());
                                            }
                                            ?>
                                        </div>
                                        <div class="righr_hcd">
                                            <h3> {{ $data->first_name." ".$data->last_name }}</h3>
                                            <p>{{ humanTiming(strtotime($data->created))}}</p>
                                            <h4>{{ nl2br($data->comment)}}</h4>
                                            <?php
                                            $avg_ratng = round(($data->quality + $data->packaging) / 2);
                                            for ($i = 0; $i < $avg_ratng; $i++) {
                                                echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                                            }
                                            for ($j = 5; $j > $avg_ratng; $j--) {
                                                echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="dataTables_paginate">
                                    {{ $records->appends(Request::only('search','from_date','to_date'))->links() }}
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    
                    // check user is enabled to place a review or not 
                    $check = DB::table('user_reviews')
                            ->select('user_reviews.id')
                            ->where('user_reviews.user_id', Session::get('user_id'))
                            ->where('user_reviews.caterer_id', $caterer->id)
                            ->first();
                   // print_r($orderData); exit;
                    if(isset($orderData)){
                    if (!empty($userData) and $userData->user_type != "Restaurant" and empty($orderData->is_review)) {
                        ?>
                        <div class="rsde_bxes">
                            <div class="co_comer_1">
                                Write Your Review Based On Your Experience
                            </div>
                            {{ Form::open(array('url' => 'restaurants/reviews/'.$caterer->slug .'/'.$orderData->slug, 'method' => 'post', 'id' => 'reviews')) }}
                            <ul class="rsde_bx">
                                <li>
                                    <label>Quality & Taste</label>
                                    <div class="tres">
                                        <div class="quality"></div>
                                    </div>
                                    <input type="hidden" id="quality" name="quality" />
                                </li>
                                <li>
                                    <label>Packaging</label>
                                    <div class="tres">
                                        <div class="packaging"></div>
                                    </div>
                                    <input type="hidden" id="packaging" name="packaging"/>
                                </li>
                                <li>
                                    <label>Delivery</label>
                                    <div class="tres">
                                        <div class="delivery"></div>
                                    </div>
                                    <input type="hidden" id="delivery" name="delivery"/>
                                </li>
                            </ul>
                            <div class="reting_bex"> 
                                <div class="profile-pic">
                                    <?php
                                    if (file_exists(UPLOAD_FULL_PROFILE_IMAGE_PATH . '/' . $userData->profile_image) && $userData->profile_image != "") {
                                        echo HTML::image(HTTP_PATH . 'public/assets/timthumb.php?src=' . DISPLAY_FULL_PROFILE_IMAGE_PATH . $userData->profile_image . '&w=50&h=50&zc=2&q=100', '', array());
                                    } else {
                                        echo HTML::image(HTTP_PATH . 'public/assets/timthumb.php?src=' . HTTP_PATH . 'public/img/front/nouser.png&w=50&h=50&zc=2&q=100', '', array());
                                    }
                                    ?>
                                </div>
                                <div class="fields">
                                    <div class="in_udy">
                                        {{ Form::textarea('comment',  "", array('class' => 'required ', 'placeholder'=>"Your Review..")) }}
                                    </div>
                                    <div class="in_udy">
                                        {{ Form::submit('Post',['class'=>'btn btn-primary']) }}
                                        {{ Form::reset('Reset', ['class'=>'reset_btn btn btn-default']) }}  
                                    </div>
                                </div>

                            </div>
                            </form>
                        </div>
                    <?php  } } ?>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="clear"></div>
@stop