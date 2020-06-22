@section('content')
{{ HTML::script('public/js/front/jquery.jscroll.js'); }}

<style>
    .price-template_fixed {
        position: fixed;
        top: 20px;
        transition: all 0.5s ease 0s;
        width: 300px;
        z-index: 9;
    }
</style>
<script>

    $(document).ready(function () {

        var length = $('.lis_bxx_leftd').height() - $('.newlimitc').height() + $('.lis_bxx_leftd').offset().top;
        var mainlength = $('.lis_bxx_leftd').height();

        $(window).scroll(function () {
            var y = $(this).scrollTop();
            var total = y * 1 - 100;

            var scroll = $(this).scrollTop();
            var height = $('.newlimitc').height() + 'px';
            if (scroll >= mainlength - 300) {

                $('._newcart').css({
                    'margin-top': '0px'
                });


                // $('._newcart').removeClass('price-template_fixed');
                //$('.lis_left_menu').removeClass('price-template_fixed');
                //$(".lis_left_menu").css("margin-top",total - 250 +"px");
                $(".lis_left_menu").css("margin-top", total - 250 + "px");


            } else
            if (scroll < $('.lis_bxx_leftd').offset().top - 100) {
                // alert();

                $('._newcart').css({
                    'margin-top': '0px'
                });
                //   alert();
                $('._newcart').removeClass('price-template_fixed');
                //  $('.carts_bx').removeClass('newlimitc');
                $(".lis_left_menu").css("margin-top", "0px");

            } else {

                $('._newcart').css({
                    'margin-top': '50px'
                });
                $('._newcart').addClass('price-template_fixed');
                $(".lis_left_menu").css("margin-top", total - 250 + "px");
            }
        });
    });



    $(document).ready(function () {
        // $(".lis_left_menu").jScroll();
//         $('.lis_left_menu').jScroll(
//          
//            $('.lis_left_menu').
//          
//        );
        $(".show_items").click(function () {
            var class_name = $(this).attr("id");

            var topc = 150;
            if ($('.header').hasClass('fixed-header') == true) {
                var topc = 90;
            }

            $('html,body').animate({
                scrollTop: $("." + class_name).offset().top - topc},
                    'slow');
        })
    })
    $(document).ready(function () {
        //  $(".carts_bx").jScroll();

    })
   
    $(document).on("click", ".counter_number", function () {

        $('.showcartloader').show();
        var type = $(this).attr("alt");
        var id_val = $(this).attr("id_val");
        var value = $('.preparation_time_' + id_val).val();
        value = value ? parseInt(value) : 0;
        
        var addons = $(this).attr('data-addon');
        var variant_type = $(this).attr('data-vaiant');

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
            submenus: submenus,
            addons:addons,
            variant_type:variant_type
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
                        } else {
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
                            $('#myModal').hide();
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown)
                        {

                        }
                    });
                } else
                {

                    $('.showcartloader').hide();
//                    swal({
//                        title: "Sorry!",
//                        text: data.message,
//                        type: "error",
//                        html: true
//                    });
//                    
                    swal({
                        title: "Are you sure?",
                        text: data.message,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes, empty my cart!",
                        closeOnConfirm: false
                    },
                            function () {

                                $.ajax({
                                    url: "<?php echo HTTP_PATH . "home/emptycart" ?>",
                                    dataType: 'json',
                                    data: data,
                                    type: 'POST',
                                    success: function (data, textStatus, XMLHttpRequest)
                                    {
                                        $(".carts_bx").html(data.data);
                                        swal("Deleted!", "Your cart is empty Now.", "success");

                                    }
                                });

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
        
        var addons = $(this).attr('data-addon');
        var variant_type = $(this).attr('data-vaiant');

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
            submenus: submenus,
            addons:addons,
            variant_type:variant_type
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
                        } else {
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
                            $('#myModal').hide();
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown)
                        {

                        }
                    });
                } else
                {

                    $('.showcartloader').hide();
//                    swal({
//                        title: "Sorry!",
//                        text: data.message,
//                        type: "error",
//                        html: true
//                    });
//                    
                    swal({
                        title: "Are you sure?",
                        text: data.message,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes, empty my cart!",
                        closeOnConfirm: false
                    },
                            function () {

                                $.ajax({
                                    url: "<?php echo HTTP_PATH . "home/emptycart" ?>",
                                    dataType: 'json',
                                    data: data,
                                    type: 'POST',
                                    success: function (data, textStatus, XMLHttpRequest)
                                    {
                                        $(".carts_bx").html(data.data);
                                        swal("Deleted!", "Your cart is empty Now.", "success");

                                    }
                                });

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
    
    
//    $(document).on("click", ".counter_number2", function () {
//        $('.showcartloader').show();
//        var type = $(this).attr("alt");
//        var id_val = $(this).attr("id_val");
//        var value = $('.preparation_time_' + id_val).val();
//        value = value ? parseInt(value) : 0;
//
//        // ajax update for orfering food
//        var data = {
//            id: id_val,
//            qty: value,
//            type: type,
//            submenus: "already"
//        }
//        $.ajax({
//            url: "<?php echo HTTP_PATH . "home/addtocart" ?>",
//            dataType: 'json',
//            type: 'POST',
//            data: data,
//            success: function (data, textStatus, XMLHttpRequest)
//            {
//                if (data.valid)
//                {
//
//                    if (type == 'minus') {
//                        value = (value - 1 < 0) ? 0 : (value - 1);
//                        $('.preparation_time_' + id_val).val(value);
//                        if (value < 0)
//                            return false;
//                    } else {
//                        if (value >= 999) {
//                            $('.preparation_time_' + id_val).val(value);
//                        } else {
//                            value = value + 1;
//                            $('.preparation_time_' + id_val).val(value);
//                        }
//                    }
//
//                    $(".carts_bx").html(data.data);
//                    $.ajax({
//                        url: "<?php echo HTTP_PATH . "home/totalcartvalue" ?>",
//                        type: 'POST',
//                        success: function (data, textStatus, XMLHttpRequest)
//                        {
//
//                            $('.showcartloader').hide();
//
//                            $("#cart_bt").html(data);
//                        },
//                        error: function (XMLHttpRequest, textStatus, errorThrown)
//                        {
//
//                        }
//                    });
//                } else
//                {
//                    $('.showcartloader').hide();
//                    swal({
//                        title: "Sorry!",
//                        text: data.message,
//                        type: "error",
//                        html: true
//                    });
//                }
//            },
//            error: function (XMLHttpRequest, textStatus, errorThrown)
//            {
//                $('.showcartloader').hide();
//
//                // Message
//                swal({
//                    title: "Sorry!",
//                    text: "Error while contacting server, please try again",
//                    type: "error",
//                    html: true
//                });
//                $(".all_bg").hide();
//            }
//        });
//
//
//    })


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
                } else
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
    function allcoutC() {
        swal({
            title: "Sorry!",
            text: "You can only add max 4 items in favorite!",
            type: "error",
            html: true
        });
        return false;
    }
    function submenutoggle(val) {
        $('#submenusec' + val).slideToggle();
    }

    $(document).on("click", ".submit-form a", function (e) {
        e.preventDefault();
        var href = $(this).attr("href");
        var min_order = $("#xcff").val();
        var total = parseFloat($(".summary p strong").attr('data-total'));
        //alert(total);
        if (total < min_order) {
            swal({
                title: "Sorry!",
                text: "This order did not meet the minimum charge of " + min_order + " <?php echo CURR; ?> by the restaurant",
                type: "error",
                html: true
            });
            return false;
        } else {
            window.location = href;
        }
    })
</script>
<script>
    $(document).ready(function () {
        $(".showw").click(function () {
            $(".see_date").toggle();
        });
    });
</script> 
<div class="clear"></div>
<div class="wrapper">
    <div class="list_bx menu_new">

        <div class="col-md-12 col-sm-12 col-xs-12">

            <div class="listing_bxs_left">
                <div class="_imagec">
                    <?php
                    if (file_exists(DISPLAY_FULL_PROFILE_IMAGE_PATH . $caterer->profile_image) and $caterer->profile_image) {
                        ?>
                        <a href="{{HTTP_PATH.'restaurants/menu/'.$caterer->slug}}"><img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.DISPLAY_FULL_PROFILE_IMAGE_PATH.$caterer->profile_image.'&w=900&h=500&zc=2&q=500') }}" alt="img" /></a>
                        <?php
                    } else {
                        ?>
                        <a href="{{HTTP_PATH.'restaurants/menu/'.$caterer->slug}}"> <img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=600&h=200&zc=2&q=100') }}" alt="img" /></a>
                        <?php
                    }
                    ?>
                </div>
                <?php if ($caterer->address != "") { ?>
                    <div class="mapc">
                        <iframe style="border: none" width="100%" height="320" src="https://maps.google.it/maps?center={{urlencode($caterer->address.$caterer->area_name)}}&zoom=14&scale=1&size=600x320&q={{urlencode($caterer->address.$caterer->area_name)}}&output=embed"></iframe>
    <!--                        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
                        <script type="text/javascript">

                        var geocoder = new google.maps.Geocoder();
                        var address = '<?php echo $caterer->address ?>';

                        geocoder.geocode( { 'address': address}, function(results, status) {

                        if (status == google.maps.GeocoderStatus.OK) {
                            var latitude = results[0].geometry.location.lat();
                            var longitude = results[0].geometry.location.lng();
    //                            alert(latitude);
    //                            alert(longitude);
                            //alert(latitude);
                            } 
                        }); 
                        </script>
                        
                        <a target="_blank" href="https://www.google.com/maps/place/{{str_replace(' ','+',$caterer->address).'+'.$caterer->area_name}}/"><img src="https://maps.googleapis.com/maps/api/staticmap?center={{urlencode($caterer->address.$caterer->area_name)}}&zoom=14&scale=1&size=600x320&maptype=roadmap&format=png&visual_refresh=true&markers=size:tiny%7Ccolor:green%7C41.4592974,-90.57596999999998" alt="{{$caterer->address.', '.$caterer->area_name}}"></a>-->
                        <!--<a target="_blank" href="https://www.google.com/maps/place/{{str_replace(' ','+',$caterer->address).'+'.$caterer->area_name}}/"><img src="https://maps.googleapis.com/maps/api/staticmap?center=63.259591,-144.667969&zoom=6&size=600x320&markers=color:blue%7Clabel:S%7C62.107733,-145.541936&markers=size:tiny%7Ccolor:green%7CDelta+Junction,AK&markers=size:mid%7Ccolor:0xFFFF00%7Clabel:C%7CTok,AK" alt="{{$caterer->address.', '.$caterer->area_name}}"></a>-->
                        <!--<a target="_blank" href="https://www.google.com/maps/place/{{str_replace(' ','+',$caterer->address).'+'.$caterer->area_name}}/"><img src="https://maps.googleapis.com/maps/api/staticmap?center={{urlencode($caterer->address.$caterer->area_name)}}&zoom=6&size=400x400&markers=color:blue%7Clabel:S%7C41.4592974,-90.57596999999998" alt="{{$caterer->address.', '.$caterer->area_name}}"></a>-->

                    </div>
                <?php } ?>
            </div>
            <div class="listing_bxs_right">
                <h1><a class="link-menu" href="javascript:void(0);"><?php echo ucwords($caterer->first_name . " " . $caterer->last_name); ?></a></h1>
                <div class="address"> <span class="area_bold">{{$caterer->area_name}}, {{$caterer->city_name }}</span>
                    <span class="">{{$caterer->address}}, {{$caterer->area_name}}</span>

                </div>



                <div class="open_img open_img_new">
                    <?php
                    $open = 0;
                    // get carters open/close status
                    if ($caterer->open_close) {

                        $open_days = explode(",", $caterer->open_days);
                        if (strtotime($caterer->start_time) <= time() && time() <= strtotime($caterer->end_time) and in_array(strtolower(date('D')), $open_days)) {
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
                <div class="rating_bx rating_bx_new">
                    <?php
                    // get all avg ratings
                    $ratings = DB::table('reviews')
                            ->select(DB::raw("avg(quality) as quality"), DB::raw("avg(packaging) as packaging"), DB::raw("avg(delivery) as delivery"), DB::raw("count(id) as counter"))
                            ->where('caterer_id', $caterer->id)
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
                    <span><a href="{{HTTP_PATH.'restaurants/reviews/'.$caterer->slug}}">({{$ratings->counter}})</a></span> 

                </div>
                <div class="fullwidth">
                    <div class="all-ratings leftdivc">
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
                    <div class="rigthvv">
                        <?php
                        // check user is enabled to place a review or not  
                        $check = DB::table('user_reviews')
                                ->select('user_reviews.id')
                                ->where('user_reviews.user_id', Session::get('user_id'))
                                ->where('user_reviews.caterer_id', $caterer->id)
                                ->first();
                        if (!empty($check)) {
                            ?>

                            <a class="btn btn-primary" href="{{HTTP_PATH.'restaurants/reviews/'.$caterer->slug}}">Write a Review</a>

                        <?php } ?>
                    </div>
                </div>


                <div class="detail_listing">
                    <ul class="list_menus">
                        <li class="first_blockk">
                            <h3>Minimum Order</h3>
                            <h2>{{$caterer->minimum_order?$caterer->minimum_order.' '.CURR : "Not Available"}}
                                <span>
                                    <?php if ($caterer->minimum_order > 0) { ?>
                                        This is the minimum limit to place a order for  <?php echo ucwords($caterer->first_name . " " . $caterer->last_name); ?></span>
                                <?php } ?>
                            </h2>
                        </li>
                        <li class="open_hrs">
                            <h3>Opening Hours </h3>
                            <!--<h2>Today {{$caterer->start_time?date("h:i a", strtotime($caterer->start_time))." - ".date("h:i a", strtotime($caterer->end_time)):" - "}}</h2>-->
                            <input type="hidden" id="xcff" value="<?php echo $caterer->minimum_order ?>"/>
                            <div class="see_more">
                                <!--<div class="showw">See More</div>-->
                                <div class="see_date">

                                    <span class="date_div <?php echo (date('D') == "Mon") ? "active" : ""; ?>"><label>Mon</label> <b>{{$caterer->start_time?date("h:i a", strtotime($caterer->start_time))." to ".date("h:i a", strtotime($caterer->end_time)):" - "}}</b></span>
                                    <span class="date_div <?php echo (date('D') == "Tue") ? "active" : ""; ?>"><label>Tue</label> <b>{{$caterer->start_time?date("h:i a", strtotime($caterer->start_time))." to ".date("h:i a", strtotime($caterer->end_time)):" - "}}</b></span>
                                    <span  class="date_div <?php echo (date('D') == "Wed") ? "active" : ""; ?>"><label>Wed</label> <b>{{$caterer->start_time?date("h:i a", strtotime($caterer->start_time))." to ".date("h:i a", strtotime($caterer->end_time)):" - "}}</b></span>
                                    <span class="date_div <?php echo (date('D') == "Thu") ? "active" : ""; ?>"><label>Thu</label> <b>{{$caterer->start_time?date("h:i a", strtotime($caterer->start_time))." to ".date("h:i a", strtotime($caterer->end_time)):" - "}}</b></span>
                                    <span class="date_div <?php echo (date('D') == "Fri") ? "active" : ""; ?>"><label>Fri</label> <b>{{$caterer->start_time?date("h:i a", strtotime($caterer->start_time))." to ".date("h:i a", strtotime($caterer->end_time)):" - "}}</b></span>
                                    <span class="date_div <?php echo (date('D') == "Sat") ? "active" : ""; ?>"><label>Sat</label> <b>{{$caterer->start_time?date("h:i a", strtotime($caterer->start_time))." to ".date("h:i a", strtotime($caterer->end_time)):" - "}}</b></span>
                                    <span class="date_div <?php echo (date('D') == "Sun") ? "active" : ""; ?>"><label>Sun</label> <b>{{$caterer->start_time?date("h:i a", strtotime($caterer->start_time))." to ".date("h:i a", strtotime($caterer->end_time)):" - "}}</b></span>
                                </div>
                            </div>
                        </li>

                        <li class="mealtype">
                            <h3>Meal Type</h3> 

                            <div class="meal_inp">
                                <?php
                                //echo "<pre>"; print_r($caterer); exit;

                                if ($caterer->catering_type != "") {
                                    $meals = explode(',', $caterer->catering_type);
                                    $mealsTypes = DB::table('mealtypes')
                                            ->select("mealtypes.id", "mealtypes.name")
                                            ->whereIn('id', $meals)
                                            ->get();
                                    foreach ($mealsTypes as $mealsVal) {
                                        // print_r($mealsVal); exit;
                                        ?><div class="checkboxx">
                                            <i class="fa fa-check"></i> <label for=""><?php echo $mealsVal->name; ?></label>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>

                            </div>


                        </li>
                        <li class="mealtype">
                            <h3>Cuisine</h3> 

                            <div class="meal_inp">

                                <?php
                                $cusrinesId = Menu::where('user_id', $caterer->id)->lists('cuisines_id');
                                if ($cusrinesId) {
                                    $cuisinename = DB::table('cuisines')->whereIn('id', $cusrinesId)->select("cuisines.id", "cuisines.name")->get();
                                    // print_r($cuisinename); exit;
                                    foreach ($cuisinename as $CsVal) {
                                        //secho $CsVal->name;
                                        // print_r($mealsVal); exit;
                                        ?><div class="checkboxx">
                                            <i class="fa fa-check"></i> <label for="">{{ html_entity_decode(HTML::link(HTTP_PATH.'restaurants/list?city=&area=&cuisine[]='.$CsVal->id.'&keyword=',ucwords($CsVal->name), ['class'=>'link-menu_m'])); }}</label>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo "Not available";
                                }
                                ?>

                            </div>


                        </li>



                    </ul>

                </div>
            </div>
        </div>

        <?php
        $input = Input::all();
        if (!$items->isEmpty()) {
            ?>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="col-md-3 col-sm-3 col-xs-12 lis_left_menu left_menu_new" id="scrolldiv">
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
                <div class="col-md-9 col-sm-12 col-xs-12 left_menu_right">
                    <div class="filters_dv">
                        <ul>
                            <li><span>Sort By <i class="fa fa-caret-right"></i></span></li>
                            <li <?php echo (isset($input['s']) and $input['s'] == 'item_name') ? 'class="active"' : ""; ?>>{{ SortableTrait::link_to_sorting_action('item_name', 'A-Z', $caterer->slug) }}</li>
                            <li <?php echo (isset($input['s']) and $input['s'] == 'price') ? 'class="active"' : ""; ?>>{{ SortableTrait::link_to_sorting_action('price', 'Price', $caterer->slug) }}</li>
                            <li <?php echo (isset($input['s']) and $input['s'] == 'loved') ? 'class="active"' : ""; ?>>{{ SortableTrait::link_to_sorting_action('loved', 'Most Loved', $caterer->slug) }}</li>
                        </ul>
                    </div>
                    <div class="lis_bxx">
                        <div class="lis_bxx_leftd menu_new_wrap">
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
                                            <a  href="javascript:void(0)" class="shows {{'cls_'.$user->cuisines_id}}">

                                                <?php
                                                $flag = $user->cuisines_name;
                                                echo ucfirst($flag);
                                                ?>

                                            </a>
                                            <?php
                                        }
                                        ?>
                                        <div class="listing_bxs listing_bxs_white listing_box_new">
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
                                                <h1 data-id='{{$user->id}}' class="namce">{{$user->item_name}}</h1>
<!--                                                <div class="maininfocont">
                                                    <div class="left_main2">

                                                        <input type="button" value="-" class="but_1 counter_number"  id_val="{{$user->id}}"  alt="minus" /><input readonly="readonly" maxlength="3" type="text" value="{{isset($key_array[$user->id]) ? $key_array[$user->id] : 0}}" class='{{"preparation_time_".$user->id}}' />
                                                        <input type="button" value="+" class="but_2 counter_number" id_val="{{$user->id}}"  alt="plus" />

                                                    </div>
                                                </div>-->
                                                <div class="coma">
                                                    <div class="love_bx">
                                                        <div class="like_n">{{$user->price.' '.CURR}}</div>
                                                        <?php
                                                        // if (Session::has('user_id')) {
                                                        if (isset($_COOKIE["browser_session_id"]) && $_COOKIE["browser_session_id"] != '') {
                                                            $browser_session_id = $_COOKIE["browser_session_id"];
                                                        } else {
                                                            $browser_session_id = session_id();
                                                            setcookie("browser_session_id", $browser_session_id, time() + 60 * 60 * 24 * 7, "/");
                                                        }
                                                        ?>
                                                        <?php
                                                        //print_r($user); exit;
                                                        $user_id = Session::get('user_id');
                                                        if ($user_id > 0) {
                                                            $allcount = DB::table('favorite_menu')
                                                                    ->where('favorite_menu.user_id', $user_id)
                                                                    ->where('favorite_menu.session_id', $browser_session_id)
                                                                    //->where('favorite_menu.menu_id', $user->id)
                                                                    ->get(); // chk favorite
                                                        } else {
                                                            $allcount = DB::table('favorite_menu')
                                                                    //->where('favorite_menu.user_id', $user_id)
                                                                    ->where('favorite_menu.session_id', $browser_session_id)
                                                                    // ->where('favorite_menu.menu_id', $user->id)
                                                                    ->get(); // chk favorite
                                                        }
                                                        $chckFav = "";
                                                        if ($user_id > 0) {

//                                                               $query->where('menu_item.item_name', "LIKE", "%$keyword%")
//                                                                        ->OrWhere(DB::raw("CONCAT_WS(' ', tbl_users.first_name,tbl_users.last_name)"), "like", "%$keyword%");

                                                            $chckFav = DB::table('favorite_menu')->where(function ($chckFav) use ($user) {
                                                                        $chckFav->where('favorite_menu.menu_id', '=', $user->id);
                                                                    })->where(function ($chckFav) use ($user_id, $browser_session_id) {
                                                                        $chckFav->where('favorite_menu.user_id', '=', $user_id)
                                                                                ->orWhere('favorite_menu.session_id', '=', $browser_session_id);
                                                                    })->first();
                                                            //$chckFav->first();
//                                                                ->first();
//                                                                 $chckFav = DB::table('favorite_menu')
//                                                                     ->where('favorite_menuc.menu_id', $user->id)    
//                                                                    ->where('favorite_menu.user_id', $user_id)
//                                                                    ->orwhere('favorite_menu.session_id', $browser_session_id)
                                                            // chk favorite
                                                            // print_r($chckFav); exit;
                                                        } else {
                                                            $chckFav = DB::table('favorite_menu')
                                                                    ->where('favorite_menu.menu_id', $user->id)
                                                                    ->where('favorite_menu.session_id', $browser_session_id)
                                                                    ->first(); // chk favorite
                                                        }

                                                        // echo count($chckFav);
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
                                                                <div class="lone_mem" id="liketext<?php echo $user->id; ?>">Love it</div>
                                                            </div>
            <?php } ?>
                                                        <div class="showlikeloader" id="showlikeloader<?php echo $user->id; ?>"></div>
            <?php /* } else { ?>
              <div  class="inlikem" onclick="loginchk()">
              <span>
              <img src="{{ URL::asset('public/img/front') }}/unlike.png" alt="img" />
              </span>
              <div class="lone_mem">Love it</div>
              </div>
              <?php } */ ?>



                                                    </div>
                                                        <?php
                                                        if ($open) {
                                                            ?>

                                                        <?php
                                                        if (!empty($user->submenu)) {
                                                            ?>
                                                            <span class="submenu-title" id="exsubmenu<?php echo $user->id; ?>" onclick="submenutoggle(<?php echo $user->id; ?>)">More Options</span>                                                            <div class="submenus">
                                                                <div class="submenu-sec" id="submenusec<?php echo $user->id; ?>" style="display:none;">
                                                            <?php
                                                            $submenus = explode(',', $user->submenu);
                                                            $i = 1;
                                                            foreach ($submenus as $smenu) {
                                                                ?>
                                                                        <div class="submenus-in" id="subsec<?php echo $user->id; ?>">
                                                                            <input type="checkbox" value="<?php echo $smenu; ?>" id="submenu<?php echo $i; ?>" class="smenu<?php echo $user->id; ?>">
                                                                        <?php echo $smenu; ?>
                                                                        </div>
                                                                        <?php
                                                                        $i++;
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                                    <?php } ?>
                                                                <?php /*  if (!empty($user->preparation_time)) {
                                                                  ?>
                                                                  <div class="time_whatch"><i class="fa fa-clock-o"></i><span> Preparation Time: {{$user->preparation_time}} Hours</span></div>
                                                                  <?php } */ ?>


                                                    <?php } ?>
                                                </div>
                                                <div class="box_descrption"><p>{{$user->description}}</p></div>
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

                        @include('elements/cart_m')
                    </div>
                </div>
            </div>
                            <?php
                        } else {
                            ?>
            <div class="no-record-list">
                No menu available for this restaurant  
            </div>
    <?php
}
?>
    </div>
</div>
<div class="clear"></div>
<div id="myModal" class="modal new_pop_up">

    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="_ttvb" id="innercontent"><p>Loading..</p></div>
    </div>

</div>
<style>
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto;  /*Enable scroll if needed */ 
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */ 
    }

    /* Modal Content/Box */
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
         /* Could be more or less, depending on screen size */
    }

    /* The Close Button */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
  .platechoose {
  background: #ccc none repeat scroll 0 0;
  border-radius: 2px;
  display: inline-block;
  float: left;
  margin: 1px;
  padding: 6px;
}
.platechoose.active {
  background: #333 none repeat scroll 0 0;
  color: #fff;
}
</style>
<script>
    $('body').delegate('click', '.close', function (e) {
        $('#myModal').hide();
        $('#innercontent').html("");
    });
    $('body').delegate('click', '.namce', function (e) {
        var dataid = $(this).attr('data-id');
        $.ajax({
            url: "<?php echo HTTP_PATH . "home/getmenu" ?>/" + dataid,
            dataType: 'text',
            type: 'GET',
            success: function (data, textStatus, XMLHttpRequest)
            {
                $('#myModal').show();
                $('#innercontent').html(data);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                $('.showcartloader').hide();
                swal({
                    title: "Sorry!",
                    text: "Error contacting to server!",
                    type: "error",
                    html: true
                });

            }
        });

    });
</script>
@stop




