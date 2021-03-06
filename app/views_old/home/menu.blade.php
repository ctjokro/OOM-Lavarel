@section('content')
<!--{{ HTML::script('http://code.jquery.com/jquery-1.9.1.js'); }}-->
{{ HTML::script('public/js/front/jquery.jscroll.js'); }}
{{ HTML::style('public/css/front/owl.theme.default.min.css') }}
{{ HTML::style('public/css/front/owl.carousel.min.css') }}
{{ HTML::script('public/js/front/owl.carousel.js'); }}
<style>
    .price-template_fixed {
        position: fixed;
        top: 2px;
        transition: all 0.5s ease 0s;
        width: 300px;
        z-index: 9;
    }
</style>
<script>
    $(function () {
        var header = $("._newcart");
        var heigh = $('.carts_bx').offset().top;
        var heigh_max = $('.tell_hear').offset().top;
        var bottom_max = $('.tell_bot').offset().top;
        //      alert(heigh);
        $(window).scroll(function () {
            //alert(bottom_max);
            //alert(heigh_max);
            var scroll = $(window).scrollTop(); //alert(scroll);
            if (scroll >= heigh) {
                
                if($("#tab1_li").hasClass('active')){
                    if (scroll + $(".carts_bx").height() - 300 >= heigh) {

                        header.addClass("price-template_fixed");
                        $('._newcart').css({
                            'margin-top': '60px'
                        });
                    } else {
                        header.removeClass("price-template_fixed");
                        $('._newcart').css({
                            'margin-top': '0px'
                        });
                    }
                    //                alert(scroll + $(".blog_cost_calculator").height());
                    //                alert(bottom_max);
                    if (scroll + $(".carts_bx").height() + 200 >= bottom_max) { //alert(7);
                        header.removeClass("price-template_fixed");
                        $('._newcart').css({
                            'margin-top': '0px'
                        });
                    }
                }
                
            } else {
                header.removeClass("price-template_fixed");
                $('._newcart').css({ 
                    'margin-top': '0px'
                });
            }
        });
    });



</script>

<script>

    $(document).ready(function () {

        var length = $('.lis_bxx_leftd').height() - $('.newlimitc').height() + $('.lis_bxx_leftd').offset().top;
        var mainlength = $('.lis_bxx_leftd').height();

        $(window).scroll(function () {
            var y = $(this).scrollTop();
            var total = y * 1 - 100;

            var scroll = $(this).scrollTop();
            var height = $('.newlimitc').height() + 'px';
            if (scroll >= mainlength) {

                $(".lis_left_menu").css("margin-top", "0px");


            } else
                if (scroll < $('.lis_bxx_leftd').offset().top - 100) {

                    $(".lis_left_menu").css("margin-top", "0px");

                } else {
                var mar = total - 350;
                if (mar > 0) {
                    $(".lis_left_menu").css("margin-top", mar + "px");
                } else {
                    $(".lis_left_menu").css("margin-top", "0px");
                }

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
            addons: addons,
            variant_type: variant_type
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
            addons: addons,
            variant_type: variant_type
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
                text: "This order did not meet the minimum charge of <?php echo CURR; ?> " + min_order + "  by the restaurant",
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
                        <a href="javascript:void(0);"><img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.DISPLAY_FULL_PROFILE_IMAGE_PATH.$caterer->profile_image.'&w=900&h=500&zc=2&q=500') }}" alt="img" /></a>
                        <?php
                    } else {
                        ?>
                        <a href="javascript:void(0);"> <img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=600&h=200&zc=2&q=100') }}" alt="img" /></a>
                        <?php
                    }
                    ?>
                </div>
                <?php if ($caterer->address != "") { ?>
                    <div class="mapc">
                        <iframe style="border: none" width="100%" height="320" src="https://maps.google.it/maps?center={{urlencode($caterer->address.' '.$caterer->city_name)}}&zoom=14&scale=1&size=600x320&q={{urlencode($caterer->address.' '.$caterer->city_name)}}&output=embed"></iframe>
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
                        </script> -->


                            <!--<a target="_blank" href="https://www.google.com/maps/place/{{str_replace(' ','+',$caterer->address).'+'.$caterer->area_name}}/"><img src="https://maps.googleapis.com/maps/api/staticmap?center=63.259591,-144.667969&zoom=6&size=600x320&markers=color:blue%7Clabel:S%7C62.107733,-145.541936&markers=size:tiny%7Ccolor:green%7CDelta+Junction,AK&markers=size:mid%7Ccolor:0xFFFF00%7Clabel:C%7CTok,AK" alt="{{$caterer->address.', '.$caterer->area_name}}"></a>-->
                            <!--<a target="_blank" href="https://www.google.com/maps/place/{{str_replace(' ','+',$caterer->address).'+'.$caterer->area_name}}/"><img src="https://maps.googleapis.com/maps/api/staticmap?center={{urlencode($caterer->address.$caterer->area_name)}}&zoom=6&size=400x400&markers=color:blue%7Clabel:S%7C41.4592974,-90.57596999999998" alt="{{$caterer->address.', '.$caterer->area_name}}"></a>-->

                    </div>
                    <a class="_nebtn btn btn-primary" target="_blank" href="https://www.google.com/maps/place/{{str_replace(' ','+',$caterer->address).'+'.$caterer->area_name}}/">Get Directions</a>
                <?php } ?>
            </div>
            <div class="listing_bxs_right">
                <h1><a class="link-menu" href="javascript:void(0);"><?php echo ucwords($caterer->first_name . " " . $caterer->last_name); ?></a>

                    <?php
                    // print_r($caterer); exit;
                    $menu_itemNonveg = DB::table('menu_item')
                            ->where('user_id', $caterer->id)
                            ->where('non_veg', '1')
                            ->first();
                    $menu_itemveg = DB::table('menu_item')
                            ->where('user_id', $caterer->id)
                            ->where('non_veg', '0')
                            ->first();
                    if ($menu_itemNonveg) {
                        ?><span class="new-veg-non" title="Non-veg">{{ HTML::image(URL::asset('public/img/front/non-veg-img.png'), '', array()) }}</span><?php
                }
                if ($menu_itemveg) {
                        ?><span class="new-veg-non" title="Veg">{{ HTML::image(URL::asset('public/img/front/veg-img.png'), '', array()) }}</span><?php
                }
                    ?>

                </h1>
                <div class="address"> 
                    <span class="area_bold">{{$caterer->area_name}}, {{$caterer->city_name }}</span>
                    <span class="">{{$caterer->address}}, {{$caterer->area_name}}</span>
                    <div class="faviv">
                        <?php
                        $user_id = Session::get('user_id');

                        $allLikedata = DB::table('thumbs')
                                ->where('restro_id', $caterer->id)
                                ->where('type', 'like')
                                ->get();

                        $allDisLikedata = DB::table('thumbs')
                                ->where('restro_id', $caterer->id)
                                ->where('type', 'dislike')
                                ->get();

                        if ($user_id > 0) {
//echo $caterer->id; exit;
                            $ifexut = DB::table('thumbs')
                                    ->where('restro_id', $caterer->id)
                                    ->where('user_id', $user_id)
                                    ->first();

                            //print_r($ifexut);

                            $class = "";

                            if ($ifexut) {
                                if ($ifexut->type == "like") {
                                    $class = "like";
                                } else {
                                    $class = "dislike";
                                }
                            }
                            ?> <span class="_tttb likenlike <?php echo (isset($class) && $class != "" && $class == "like") ? "active" : ""; ?>" dataid ="<?php echo $caterer->id; ?>" access-mode="true" data-label="I like it." datatypec="like" title="I like it."><i class="fa fa-thumbs-up "></i> <?php echo count($allLikedata) ?></span>
                            <span class="_tttb likenlike <?php echo (isset($class) && $class != "" && $class == "dislike") ? "active" : ""; ?>" dataid ="<?php echo $caterer->id; ?>" access-mode="true" data-label="I don't like it." datatypec="dislike" title="I don't like it."><i class="fa fa-thumbs-down"></i> <?php echo count($allDisLikedata) ?></span>
                            <?php
                        } else {
                            ?>  <span class="_tttb likenlike" dataid ="<?php echo $caterer->id; ?>" access-mode="false" data-label="I like it." datatypec="like" title="I like it."><i class="fa fa-thumbs-up"></i> <?php echo count($allLikedata) ?></span>
                            <span class="_tttb likenlike" dataid ="<?php echo $caterer->id; ?>" access-mode="false" data-label="I don't like it." datatypec="dislike" title="I don't like it."><i class="fa fa-thumbs-down"></i> <?php echo count($allDisLikedata) ?></span>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <div class="_vyt">
                    <?php
                    if ($caterer->estimated_cost != "") {
                        echo "<span class='asdasdasd'>";
                        echo HTML::image(HTTP_PATH . "public/img/front/wc-sign.png", '') . " <strong> Cost for two people: <strong>" . App::make("HomeController")->numberformat($caterer->estimated_cost, 2);
                        echo "</span>";
                    }
                    if ($caterer->average_time != "") {
                        echo "<span class='asdasdasd'>";
                        echo HTML::image(HTTP_PATH . "public/img/front/delivery-man.png", '') . " <strong> Average Delivery Time: <strong>" . $caterer->average_time . ' Mintues';
                        echo "</span>";
                    }
                    ?>
                </div>        



                <div class="open_img open_img_new">
                    <?php
                    $open = 0;
// get carters open/close status
                    if ($caterer->open_close) {

                        $day = date('D');
                        $open_days = explode(",", $caterer->open_days);

                        $vrrcy = explode(',', $caterer->start_time);
                        $endty = explode(',', $caterer->end_time);


                        $startime = array_combine($open_days, $vrrcy);
                        $endtime = array_combine($open_days, $endty);
                        $start = "";
                        if (isset($startime[strtolower(date('D'))])) {

                            $start = $startime[strtolower(date('D'))];
                        }
                        if (isset($endtime[strtolower(date('D'))])) {
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
                    <span><a class="getallreviews" href="javascript:void(0);">({{$ratings->counter}})</a></span> 

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

                            <!--                            <a class="btn btn-primary" href="{{HTTP_PATH.'restaurants/reviews/'.$caterer->slug}}">Write a Review</a>-->

                        <?php } ?>
                    </div>
                </div>


                <div class="detail_listing">
                    <ul class="list_menus">
                        <li class="first_blockk">
                            <h3>Minimum Order</h3>
                            <h2>
                                <?php
                                /*  if($caterer->minimum_order > 0){
                                  ?>{{ action('HomeController@numberformat',$caterer->minimum_order) }}<?php
                                  }elsE{
                                  echo "Not Available";
                                  } */
                                echo App::make("HomeController")->numberformat($caterer->minimum_order);
                                ?>
                                <!--                                {{
                                                                
                                                                    $caterer->minimum_order?$caterer->minimum_order : "Not Available"}}-->
                                <span>
                                    <?php if ($caterer->minimum_order > 0) { ?>
                                        This is the minimum limit to place a order from  <?php echo ucwords($caterer->first_name . " " . $caterer->last_name); ?></span>
                                <?php } ?>
                            </h2>
                            <?php
                            $pickc = DB::table('pickup_charges')->where('user_id', $caterer->id)->first();

                            //print_r($pickc); exit;
                            if ($pickc) {
                                if ($pickc->pick_up == 1) {
                                    echo '<div class="asd_yy">' . HTML::image(HTTP_PATH . "public/img/front/pick_up.png", '') . '<span class="_cfff"> Pickup facility available </span></div>';
                                }
                            }
                            ?>
                        </li>
                        <li class="open_hrs">
                            <h3>Opening Hours </h3>
                            <!--<h2>Today {{$caterer->start_time?date("h:i a", strtotime($caterer->start_time))." - ".date("h:i a", strtotime($caterer->end_time)):" - "}}</h2>-->
                            <input type="hidden" id="xcff" value="<?php echo $caterer->minimum_order ?>"/>
                            <div class="see_more">
                                <!--<div class="showw">See More</div>-->
                                <div class="see_date">
                                    <?php
                                    $array = array('mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday');
                                    $open_days = explode(",", $caterer->open_days);

                                    $vrrcy = explode(',', $caterer->start_time);
                                    $endty = explode(',', $caterer->end_time);


                                    $startime = array_combine($open_days, $vrrcy);
                                    $endtime = array_combine($open_days, $endty);

                                    foreach ($array as $node => $arrayVal) {
                                        if (in_array($node, $open_days)) {
                                            ?><span class="date_div <?php echo (strtolower(date('D')) == $node) ? "active" : ""; ?>"><label><?php echo ucfirst($node) ?></label> <b>{{$caterer->start_time?date("h:i a", strtotime($startime[$node]))." to ".date("h:i a", strtotime($endtime[$node])):" - "}}</b></span> <?php
                                } else {
                                            ?><span class="date_div <?php echo (strtolower(date('D')) == $node) ? "active" : ""; ?>"><label><?php echo ucfirst($node) ?></label> <span class="clsed">Closed</span></span> <?php
                                }
                            }
                                    ?>

                                </div>
                            </div>
                        </li>

                        <li class="mealtype">
                            <h3>Meal Type</h3> 

                            <div class="meal_inp">
                                <?php
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
                                } else {
                                    echo "Not available";
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
                                    $max = count($cuisinename);
                                    $x = 1;

                                    foreach ($cuisinename as $CsVal) {
                                        if ($x < 8) {
                                            //secho $CsVal->name;
                                            // print_r($mealsVal); exit;
                                            ?><div class="checkboxx">
                                                <i class="fa fa-check"></i> <label for="">{{ html_entity_decode(HTML::link(HTTP_PATH.'restaurants/list?city=&area=&cuisine[]='.$CsVal->id.'&keyword=',ucwords($CsVal->name), ['class'=>'link-menu_m'])); }}</label>
                                            </div>
                                            <?php
                                        }
                                        $x++;
                                    }
                                    if ($max > 7) {
                                        ?><div class="checkboxx bvttydvrr"><i class="fa fa-arrow-right"></i> <span class='view_more'>View all</span>
                                            <div class="morehtt" style="display: none"><?php
                                foreach ($cuisinename as $CsVal) {
                                            ?><div class="checkboxx bvttydv">
                                                        <i class="fa fa-check"></i> <label for="">{{ html_entity_decode(HTML::link(HTTP_PATH.'restaurants/list?city=&area=&cuisine[]='.$CsVal->id.'&keyword=',ucwords($CsVal->name), ['class'=>'link-menu_m'])); }}</label>
                                                    </div>
                                                    <?php
                                                }
                                                ?></div></div><?php
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
        if (!empty($items)) {
            ?>

            <div class="left_new_section">
                <div class="col-md-12 col-sm-12 col-xs-12">

                    <div class="tab">
                        <ul class="dea_tabs">
                            <script>
                                function opendiv(id) {
                                    $('.rj').hide();
                                    $('#' + id).show();
                                    $('.ddlj').removeClass('active');
                                    $('#' + id + '_li').addClass('active');

                                    var header = $("._newcart");
                                    var heigh = $('.carts_bx').offset().top;
                                    var heigh_max = $('.tell_hear').offset().top;
                                    var bottom_max = $('.tell_bot').offset().top;
                                }

                            </script>

                            <li id="tab1_li" class="ddlj active" onclick="opendiv('tab1')">Menu</li>
                            <li id="tab2_li"  class="ddlj " onclick="opendiv('tab2')">Deals</li>
                            <li id="tab3_li"  class="ddlj " onclick="opendiv('tab3')">Review</li>
                            <li id="tab4_li"  class="ddlj " onclick="opendiv('tab4')">Discount</li>


                        </ul></div>

                    <div class="tab_wrap">              
                        <div  class="rj active" id="tab1">
                            <div class="col-md-4 col-sm-4 col-xs-12 lis_left_menu left_menu_new" id="scrolldiv">
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
                            <div class="lis_bxx_leftd menu_new_wrap">
                                <!--                                <div class="filters_dv">
                                                                    <ul>
                                                                        <li><span>Sort By <i class="fa fa-caret-right"></i></span></li>
                                                                        <li <?php echo (isset($input['s']) and $input['s'] == 'item_name') ? 'class="active"' : ""; ?>>{{ SortableTrait::link_to_sorting_action('item_name', 'A-Z', $caterer->slug) }}</li>
                                                                        <li <?php echo (isset($input['s']) and $input['s'] == 'price') ? 'class="active"' : ""; ?>>{{ SortableTrait::link_to_sorting_action('price', 'Price', $caterer->slug) }}</li>
                                                                        <li <?php echo (isset($input['s']) and $input['s'] == 'loved') ? 'class="active"' : ""; ?>>{{ SortableTrait::link_to_sorting_action('loved', 'Most Loved', $caterer->slug) }}</li>
                                                                    </ul>
                                                                </div>-->
                                <?php
                                if (!empty($items)) {
                                    $flag = "";

                                    // create cart content array
                                    $key_array = array();
                                    if (!empty($cart_content)) {
                                        foreach ($cart_content as $key)
                                            $key_array[$key['id']] = $key['quantity'];
                                    }
                                    $CusId = array();
                                    foreach ($items as $user) {
                                        $CusId[] = $user->cuisines_id;
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
                                                    <?php if ($user->deal == 1) { ?>
                                                        <div class="_div_newttvb"><span class="_ttvb _newttvb">   <img src="{{ URL::asset('public/img/front') }}/deal.png" alt="Deal" /> </div>
                                                    <?php } ?>
                                                    <div class="img_fream">
                                                        <div class="img_fream_img">


                                                            <?php if ($open == 1) {
                                                                ?><?php
                                                if (file_exists(UPLOAD_FULL_ITEM_IMAGE_PATH . $user->image) and $user->image) {
                                                                    ?>
                                                                    <img data-id='{{$user->id}}' class="namce" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.UPLOAD_FULL_ITEM_IMAGE_PATH.$user->image.'&w=150&h=150&zc=2&q=100') }}" alt="img" />
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <img data-id='{{$user->id}}' class="namce" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=150&h=150&zc=2&q=100') }}" alt="img" />
                                                                    <?php
                                                                }
                                                                ?>
                                                                <?php
                                                            } else {

                                                                if (file_exists(UPLOAD_FULL_ITEM_IMAGE_PATH . $user->image) and $user->image) {
                                                                    ?>
                                                                    <img data-id='{{$user->id}}' class="homcee" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.UPLOAD_FULL_ITEM_IMAGE_PATH.$user->image.'&w=150&h=150&zc=2&q=100') }}" alt="img" />
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <img data-id='{{$user->id}}' class="homcee" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=150&h=150&zc=2&q=100') }}" alt="img" />
                                                                    <?php
                                                                }
                                                            }
                                                            ?>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="listing_bxs_right2">
                                                    <?php if ($open == 1) {
                                                        ?><h1 data-id='{{$user->id}}' class="namce">{{$user->item_name}}

                                                            <?php
                                                            if ($user->spicy == 1) {
                                                                ?><span class="nonb borderfi" title="This is spicy food."><img data-id='{{$user->id}}' class="" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'/public/img/front/chilli.png'.'&w=20&h=20&zc=2&q=100') }}" alt="img" /></span><?php
                                        }
                                        if ($user->non_veg == 0) {
                                                                ?><span class="new-veg-non" title="Veg">{{ HTML::image(URL::asset('public/img/front/veg-img.png'), '', array()) }}</span><?php
                                            }
                                            if ($user->non_veg == 1) {
                                                                ?><span class="new-veg-non" title="Non-veg">{{ HTML::image(URL::asset('public/img/front/non-veg-img.png'), '', array()) }}</span><?php
                                        }
                                                            ?>
                                                        </h1><?php
                                        } else {
                                                            ?><h1 data-id='{{$user->id}}' class="homcee">{{$user->item_name}}<?php
                                        if ($user->non_veg == 0) {
                                                                ?><span class="new-veg-non" title="Veg">{{ HTML::image(URL::asset('public/img/front/veg-img.png'), '', array()) }}</span><?php
                                        }
                                        if ($user->non_veg == 1) {
                                                                ?><span class="new-veg-non" title="Non-Veg">{{ HTML::image(URL::asset('public/img/front/non-veg-img.png'), '', array()) }}</span><?php
                                        }
                                                            ?></h1><?php }
                                                        ?>

                                                    <!--                                                <div class="maininfocont">
                                                                                                        <div class="left_main2">
                                                    
                                                                                                            <input type="button" value="-" class="but_1 counter_number"  id_val="{{$user->id}}"  alt="minus" /><input readonly="readonly" maxlength="3" type="text" value="{{isset($key_array[$user->id]) ? $key_array[$user->id] : 0}}" class='{{"preparation_time_".$user->id}}' />
                                                                                                            <input type="button" value="+" class="but_2 counter_number" id_val="{{$user->id}}"  alt="plus" />
                                                    
                                                                                                        </div>
                                                                                                    </div>-->
                                                    <div class="coma">
                                                        <div class="love_bx">
                                                            <div class="like_n">{{ App::make("HomeController")->numberformat($user->price)}}</div>

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
                                                            <div class="lone_mem" id="liketext<?php echo $user->id; ?>"></div>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div  class="inlikem" id ="like<?php echo $user->id; ?>"  onclick="like(<?php echo $user->id; ?>)">
                                                            <span id="likeimg<?php echo $user->id; ?>">
                                                                <img src="{{ URL::asset('public/img/front') }}/unlike.png" alt="img" />
                                                            </span>
                                                            <div class="lone_mem" id="liketext<?php echo $user->id; ?>"></div>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="box_descrption"><p>{{$user->description}}</p></div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        $flag = $user->cuisines_name;
                                    }
                                    ?>

                                    <?php
                                }
                                ?>
                                <div class="tell_bot"></div>
                            </div>

                        </div>
                        <div class="rj" style="display:none" id="tab2">
                            <div class="discount_wrap">
                                <div class="_haeding">Deals</div>
                                <?php
                                $deals = DB::table('menu_item')
                                        ->where("menu_item.user_id", "=", $caterer->id)
                                        ->where("menu_item.status", "=", '1')
                                        ->where("menu_item.deal", "=", '1')
                                        ->get();
                                if ($deals) {
                                    $flag = "";

                                    foreach ($deals as $user) {

                                        $cuisine = DB::table('cuisines')
                                                ->where('cuisines.id', $user->cuisines_id)
                                                ->first();
                                        // print_r($cuisine);
                                        //$CusId[] = $user->cuisines_id;
                                        ?>
                                        <div class="listing_bxsse ladfhc">


                                            <div class="listing_bxs listing_bxs_white listing_box_new">
                                                <div class="listing_bxs_left2">
                                                    <div class="_div_newttvb"><span class="_ttvb">   <img src="{{ URL::asset('public/img/front') }}/deal.png" alt="Deal" /> </span></div>
                                                    <div class="img_fream">

                                                        <div class="img_fream_img">


                                                            <?php if ($open == 1) {
                                                                ?><?php
                                                if (file_exists(UPLOAD_FULL_ITEM_IMAGE_PATH . $user->image) and $user->image) {
                                                                    ?>
                                                                    <img data-id='{{$user->id}}' class="namce" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.UPLOAD_FULL_ITEM_IMAGE_PATH.$user->image.'&w=150&h=150&zc=2&q=100') }}" alt="img" />
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <img data-id='{{$user->id}}' class="namce" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=150&h=150&zc=2&q=100') }}" alt="img" />
                                                                    <?php
                                                                }
                                                                ?>
                                                                <?php
                                                            } else {

                                                                if (file_exists(UPLOAD_FULL_ITEM_IMAGE_PATH . $user->image) and $user->image) {
                                                                    ?>
                                                                    <img data-id='{{$user->id}}' class="homcee" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.UPLOAD_FULL_ITEM_IMAGE_PATH.$user->image.'&w=150&h=150&zc=2&q=100') }}" alt="img" />
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <img data-id='{{$user->id}}' class="homcee" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=150&h=150&zc=2&q=100') }}" alt="img" />
                                                                    <?php
                                                                }
                                                            }
                                                            ?>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="listing_bxs_right2">
                                                    <span class="_fyyv"><?php echo $cuisine->name ?></span>   
                                                    <?php if ($open == 1) {
                                                        ?><h1 data-id='{{$user->id}}' class="namce">{{$user->item_name}}

                                                            <?php
                                                            if ($user->spicy == 1) {
                                                                ?><span class="nonb borderfi" title="This is spicy food."><img data-id='{{$user->id}}' class="" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'/public/img/front/chilli.png'.'&w=20&h=20&zc=2&q=100') }}" alt="img" /></span><?php
                                        }
                                        if ($user->non_veg == 0) {
                                                                ?><span class="new-veg-non" title="Veg">{{ HTML::image(URL::asset('public/img/front/veg-img.png'), '', array()) }}</span><?php
                                            }
                                            if ($user->non_veg == 1) {
                                                                ?><span class="new-veg-non" title="Non-veg">{{ HTML::image(URL::asset('public/img/front/non-veg-img.png'), '', array()) }}</span><?php
                                        }
                                                            ?>
                                                        </h1><?php
                                        } else {
                                                            ?><h1 data-id='{{$user->id}}' class="homcee">{{$user->item_name}}<?php
                                        if ($user->non_veg == 0) {
                                                                ?><span class="new-veg-non" title="Veg">{{ HTML::image(URL::asset('public/img/front/veg-img.png'), '', array()) }}</span><?php
                                        }
                                        if ($user->non_veg == 1) {
                                                                ?><span class="new-veg-non" title="Non-Veg">{{ HTML::image(URL::asset('public/img/front/non-veg-img.png'), '', array()) }}</span><?php
                                        }
                                                            ?></h1><?php }
                                                        ?>

                                                    <!--                                                <div class="maininfocont">
                                                                                                        <div class="left_main2">
                                                    
                                                                                                            <input type="button" value="-" class="but_1 counter_number"  id_val="{{$user->id}}"  alt="minus" /><input readonly="readonly" maxlength="3" type="text" value="{{isset($key_array[$user->id]) ? $key_array[$user->id] : 0}}" class='{{"preparation_time_".$user->id}}' />
                                                                                                            <input type="button" value="+" class="but_2 counter_number" id_val="{{$user->id}}"  alt="plus" />
                                                    
                                                                                                        </div>
                                                                                                    </div>-->
                                                    <div class="coma">
                                                        <div class="love_bx">
                                                            <div class="like_n">{{ App::make("HomeController")->numberformat($user->price)}}</div>

                                                            <div class="showlikeloader" id="showlikeloader<?php echo $user->id; ?>"></div>



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
                                    }
                                } else {
                                    ?><DIV class="SAD">No Deals Found.</DIV><?php
                        }
                                ?>

                            </div> 



                        </div>
                        <div class="rj" style="display:none" id="tab3">
                            <div class="_haeding">Reviews</div>

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

                            $reviews = DB::table('reviews')->where('reviews.caterer_id', $caterer->id)
                                    ->where('reviews.status', '1')
                                    ->join('users', 'users.id', '=', 'reviews.user_id')
                                    ->select('reviews.*', 'users.first_name', 'users.last_name', 'users.profile_image')
                                    ->limit(10)
                                    ->get();

                            $allreviews = DB::table('reviews')->where('reviews.caterer_id', $caterer->id)
                                    ->where('reviews.status', '1')
                                    ->join('users', 'users.id', '=', 'reviews.user_id')
                                    ->select('reviews.*', 'users.first_name', 'users.last_name', 'users.profile_image')
                                    ->count();

                            $ratings = DB::table('reviews')
                                    ->select(DB::raw("avg(quality) as quality"), DB::raw("avg(packaging) as packaging"), DB::raw("avg(delivery) as delivery"), DB::raw("count(id) as counter"))
                                    ->where('caterer_id', $caterer->id)
                                    ->where('status', '1')
                                    ->first();

                            if ($reviews) {
                                ?>
                                <div class="wrap-counter">
                                    <div class="topp_revie">
                                        <!--                                        <div class="left_top_re">
                                                                                    <span class="txt--larger">
                                                                                                                           
                                                                                    </span>
                                                                                    <h6>Overall Ratings</h6></div>-->



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
                                </div>
                                <div class="list-contianer">
                                    <?php foreach ($reviews as $data) { ?>
                                        <div class="reviews-list">
                                            <div class="single_row">
                                                <div class="left_review">
                                                    <div class="avtar_review">
                                                        <?php
                                                        if (file_exists(UPLOAD_FULL_PROFILE_IMAGE_PATH . '/' . $data->profile_image) && $data->profile_image != "") {
                                                            echo HTML::image(HTTP_PATH . 'public/assets/timthumb.php?src=' . DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image . '&w=70&h=70&zc=2&q=100', '', array());
                                                        } else {
                                                            echo HTML::image(HTTP_PATH . 'public/assets/timthumb.php?src=' . HTTP_PATH . 'public/img/front/nouser.png&w=70&h=70&zc=2&q=100', '', array());
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="left_img_con">
                                                        <h6>{{ $data->first_name." ".$data->last_name }}</h6>

                                                        <p>{{ nl2br($data->comment)}}</p></div>
                                                </div>
                                                <div class="right_review">
                                                    <div class="rating">
                                                        <?php
                                                        $avg_ratng = round(($data->quality + $data->packaging) / 2);
                                                        for ($i = 0; $i < $avg_ratng; $i++) {
                                                            echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                                                        }
                                                        for ($j = 5; $j > $avg_ratng; $j--) {
                                                            echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                                                        }
                                                        ?>
                                                        <span class="span">{{$avg_ratng}}</span>
                                                    </div>
                                                    <span class="txt--small txt--light">{{ humanTiming(strtotime($data->created))}} </span>
                                                </div>
                                            </div>
                                        </div> 
                                    <?php } ?>
                                    <?php
                                    if ($allreviews > 10) {
                                        ?><span><a href="{{HTTP_PATH.'restaurants/reviews/'.$caterer->slug}}">View all reviews</a></span><?php
                        }
                                    ?>
                                </div>
                                <?php
                            } else {
                                ?>  <div class="_nov">This restaurant hasn't receive any reviews or ratings.</div><?php
                    }
                            ?>
                        </div>
                        <div class="rj" style="display:none" id="tab4">
                            <div class="info_cnt col-xs-12 col-sm-12 col-md-12 col-lg-12 std_padding">	
                                <div class="coupon_row no-padding-margin-on-mob col-xs-12 col-sm-12 col-md-12 col-lg-12 std_padding">
                                    <?php
                                    $coupans = DB::table('coupons')->where('coupons.user_id', $caterer->id)
                                            ->where('coupons.start_time', '<=', date('Y-m-d'))
                                            ->where('coupons.end_time', '>=', date('Y-m-d'))
                                            ->where('coupons.status', 1)
                                            ->orderBy('coupons.end_time', 'asc')
                                            ->get();
                                    if ($coupans) {
                                        foreach ($coupans as $coupansVal) {
                                            ?>
                                            <div class="coupon_cnt col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                                <div class="coupon_section col-xs-12 col-sm-12 col-md-12 col-lg-12 std_padding">
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 std_padding">

                                                        <div class="offer_ribbon_text col-xs-9 col-sm-9 col-md-9 col-lg-9 std_padding">
                                                            <div class="review_ribbon col-xs-12 col-sm-2 col-md-2 col-lg-2 std_padding">
                                                                <span>
                                                                    <?php echo $coupansVal->discount ?>%															
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="coupon col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                            <div class="coupon_bg col-xs-10 col-sm-10 col-md-10 col-lg-10">
                                                                <span class="coupon_code_txt"><?php echo $coupansVal->code ?></span>
                                                            </div>
                                                        </div>	
                                                        <div class="coupon_expiry_section col-xs-12 col-sm-12 col-md-12 col-lg-12 std_padding">
                                                            <div class="coupon_expiry col-xs-6 col-sm-10 col-md-9 col-lg-6 std_padding pull-right">
                                                                <span class="coupon_expiry_date">Expires <?php echo date('F d, Y', strtotime($coupansVal->end_time)) ?></span>
                                                            </div>
                                                        </div>
                                                    </div>	
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="_nov">No Discount coupons found.</div>
                                    <?php } ?> 

                                </div>
                            </div>
                        </div>


                    </div>  





                </div>

            </div>
            <div class="right_new_section left_menu_right">

                <div class="lis_bxx">



                    @include('elements/cart_m')
                    <div class="tell_hear"></div>
                </div>
            </div>
            <?php
            if ($CusId && count($CusId) > 0) {

                $SimilarMenu = DB::table('menu_item')
                        ->whereIn('menu_item.cuisines_id', $CusId)
                        ->where('menu_item.user_id', '<>', $caterer->id)
                        ->select('menu_item.user_id')
                        ->leftjoin('users', 'menu_item.user_id', '=', 'users.id')
                        ->select('users.*', "areas.name as area_name", "cities.name as city_name", "opening_hours.open_close", "opening_hours.catering_type", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "opening_hours.open_days", DB::raw("(select (avg(tbl_reviews.quality)+avg(tbl_reviews.packaging))/2 from `tbl_reviews` where tbl_reviews.caterer_id = tbl_users.id and tbl_reviews.status = '1') as rating"), DB::raw("(select count(tbl_reviews.id) from `tbl_reviews` where tbl_reviews.caterer_id = tbl_users.id and tbl_reviews.status = '1') as counter"))
                        ->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                        ->leftjoin('areas', 'areas.id', '=', 'users.area')
                        ->leftjoin('cities', 'cities.id', '=', 'users.city')
                        ->groupBy('menu_item.user_id')
                        ->where('users.status', "=", "1")
                        ->where('users.approve_status', "=", "1")
                        //->having('products_count', '>' , 1)
                        ->orderBy('users.featured', 'desc')
                        ->orderBy('opening_hours.open_close', 'desc')
                        ->orderBy('rating', 'desc')
                        ->orderBy('users.first_name', 'asc')
                        ->orderBy('users.id', 'desc')
                        ->limit(15)
                        ->get();

                //  echo "<pre>"; print_r($SimilarMenu); exit;

                if ($SimilarMenu) {
                    $idC = "";
                    $classCV = "";
                    if (count($SimilarMenu) > 5) {
                        $idC = "tabsliderr";
                        $classCV = "owl-carousel";
                    }
                    ?>
                    <div class="smily">
                        <h3>Similar Restaurant</h3>
                        <div class="similar_template_slider">
                            <?php
                            $newcl = "";
                            if (count($SimilarMenu) <= 5) {
                                $newcl = 'smallwidth';
                            }
                            ?>
                            <div class="similar_template_slider_inner <?php echo $newcl; ?>">
                                <div id="<?php echo $idC; ?>" class="<?php echo $classCV; ?>">
                                    <?php
                                    foreach ($SimilarMenu as $record) {

//                                $record = DB::table('users')
//                                        ->select(DB::raw("(select avg(quality) from tbl_reviews where tbl_reviews.caterer_id = tbl_users.id) as rating"), 'users.profile_image', 'users.id', 'users.slug', 'users.address', 'users.*', "areas.name as area_name", "cities.name as city_name", "opening_hours.open_close", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "opening_hours.open_days", 'users.first_name')
//                                        //                ->orderBy('users.id', 'desc')
//                                        ->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
//                                        ->leftjoin('areas', 'areas.id', '=', 'users.area')
//                                        ->leftjoin('cities', 'cities.id', '=', 'users.city')
//                                        ->where('users.id', "=", $productsVal->user_id)
//                                        ->first();
//                                             print_r($record); exit;
                                        if ($record) {
                                            ?>
                                            <div class="similar_slide">

                                                <div class="slide_image">
                                                    <div class="slide_imgg">
                                                        <?php
                                                        if (file_exists(DISPLAY_FULL_PROFILE_IMAGE_PATH . $record->profile_image) and $record->profile_image) {
                                                            ?>
                                                            <a href="<?php echo HTTP_PATH ?>restaurants/menu/<?php echo $record->slug ?>"><img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.DISPLAY_FULL_PROFILE_IMAGE_PATH.$record->profile_image.'&w=260&h=160&zc=3&q=500') }}" alt="img" class="imggg" /></a>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <a href="<?php echo HTTP_PATH ?>restaurants/menu/<?php echo $record->slug ?>"> <img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=260&h=160&zc=2&q=100') }}" alt="img" class="imggg" /></a>
                                                            <?php
                                                        }
                                                        ?>


                                                    </div>
                                                    <div class="_cyty">
                                                        <?php
                                                        if ($record->featured == 1) {
                                                            if (strtotime($record->expiry_date) > time()) {
                                                                echo "<span class='featured listing_span licttt'>Featured</span>";
                                                            } else {


                                                                DB::table('users')
                                                                        ->where('id', $record->id)
                                                                        ->update(array('featured' => 0));
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="botm">

                                                        <div class="tmt_names">
                                                            {{ html_entity_decode(HTML::link('restaurants/menu/'.$record->slug,ucwords($record->first_name." ".$record->last_name ), ['class'=>'link-menu_m'])); }}
                                                        </div>
                                                        <div class="rating_bx rating_bx_new sliderc">
                                                            <?php
                                                            $avg_ratng = round($record->rating);
                                                            for ($i = 0; $i < $avg_ratng; $i++) {
                                                                echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                                                            }
                                                            for ($j = 5; $j > $avg_ratng; $j--) {
                                                                echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                                                            }
                                                            ?>


                                                        </div>
                                                        <div class="similar_tm_dt">
                                                            <div class="uppef">
                                                                <?php
                                                                $menu_itemNonveg = DB::table('menu_item')
                                                                        ->where('user_id', $record->id)
                                                                        ->where('non_veg', '1')
                                                                        ->first();
                                                                $menu_itemveg = DB::table('menu_item')
                                                                        ->where('user_id', $record->id)
                                                                        ->where('non_veg', '0')
                                                                        ->first();
                                                                if ($menu_itemNonveg) {
                                                                    ?><span class="new-veg-non" title="Non-veg">{{ HTML::image(URL::asset('public/img/front/non-veg-img.png'), '', array()) }}</span><?php
                                        }
                                        if ($menu_itemveg) {
                                                                    ?><span class="new-veg-non" title="Veg">{{ HTML::image(URL::asset('public/img/front/veg-img.png'), '', array()) }}</span><?php
                                        }
                                                                ?>
                                                                <div class="ekdiv">
                                                                    <div class="open_img open_img_new">
                                                                        <?php
                                                                        // get carters open/close status
                                                                        if ($record->open_close) {
                                                                            $day = date('D');
                                                                            $open_days = explode(",", $record->open_days);

                                                                            $vrrcy = explode(',', $record->start_time);
                                                                            $endty = explode(',', $record->end_time);


                                                                            $startime = array_combine($open_days, $vrrcy);
                                                                            $endtime = array_combine($open_days, $endty);
                                                                            $start = "";
                                                                            if (isset($startime[strtolower(date('D'))])) {

                                                                                $start = $startime[strtolower(date('D'))];
                                                                            }
                                                                            if (isset($endtime[strtolower(date('D'))])) {
                                                                                $end = $endtime[strtolower(date('D'))];
                                                                            }


                                                                            if ((isset($start) && strtotime($start) <= time()) && (isset($end) && time() <= strtotime($end)) and in_array(strtolower(date('D')), $open_days)) {
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
                                                                </div>          

                                                            </div>





                                                        </div>                 

                                                        <div class="tmt_re">
                                                            <div class="template_review">
                                                                <span class="area_bold">{{$record->area_name }},  {{$record->city_name }}</span>
                                                                <span >{{$record->address }}, {{$record->area_name }}</span>
                                                            </div>

                                                        </div></div>




                                                </div>  </div>
                                            <?php
                                        }
                                    }
                                    ?>


                                </div>    
                                <?php if (count($SimilarMenu) > 5) { ?>
                                    <script>

                                        $(function () {
                                            $('#tabsliderr').owlCarousel({
                                                rtl: false,
                                                loop: true,
                                                nav: true,
                                                autoplay: true,
                                                autoplayTimeout: 3000,
                                                smartSpeed: 3000,
                                                slideSpeed: 3000,
                                                autoplayHoverPause: true,
                                                responsive: {
                                                    0: {items: 1},
                                                    479: {items: 1},
                                                    580: {items: 2},
                                                    766: {items: 3},
                                                    1100: {items: 4},
                                                    1280: {items: 4}
                                                }


                                            });
                                        });
                                    </script>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
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
<script>
    $('body').delegate('click', '.btnv', function (e) {

        var total = 0;
        var datatype = $(this).attr('data-type');
        if (datatype == "Add-on") {
            var dataname = $(this).attr('data-name');
            var id = $(this).attr('data-id');
            var dataprice = $(this).attr('data-price');
        } else {
            var dataname = $(this).attr('data-name-variant');
            var id = $(this).attr('data-id-variant');
            var dataprice = $(this).attr('data-price-variant');
        }


        // if(datatype == "")
        var btnhtml = $(this).html();
        if (btnhtml == "Get it") {
            $(this).html("Remove");
        } else {
            $(this).html("Get it");
        }


        var htmlCont = '<h3>Menu item summary</h3>';

        var addons = $('#addons').val();
        var variant = $('#variant_type').val();

        //htmlCont +='<div class="_ttb"><label>Base price: </label> {{CURR}}'+$('#base_price').val()+'</div>'; 

        if (datatype == "Add-on") {
            if (addons != "") {
                var splivArr = $('#addons').val().split(',');
                if (splivArr.indexOf(id) > -1) {
                    //alert(splivArr.indexOf(id));
                    splivArr.splice(splivArr.indexOf(id), 1);
                } else {
                    splivArr.push(id);

                }
                $('#addons').val(splivArr.join(','));
                addons = $('#addons').val();

            } else {
                if (datatype == "Add-on") {
                    //                alert(datatype);
                    //                htmlCont +='<div class="_ttb"><label>'+$("span[data-id='" + id +"']").attr('data-type')+' ('+$("span[data-id='" + id +"']").attr('data-name')+'):</label> {{CURR}} '+$("span[data-id='" + id +"']").attr('data-price')+'</div>';
                    //                total = parseFloat(total) + parseFloat($("span[data-id='" + id +"']").attr('data-price'));
                    $('#addons').val(id);
                    addons = $('#addons').val();
                }
            }
        } else {
            if (variant != "") {
                var splivArr = $('#variant_type').val().split(',');
                if (splivArr.indexOf(id) > -1) {
                    //alert(splivArr.indexOf(id));
                    if (splivArr.length == 1) {
                        alert("You need to purchase at least one variant.");
                        $(this).html("Remove");
                    } else {
                        splivArr.splice(splivArr.indexOf(id), 1);
                    }

                } else {
                    splivArr.push(id);

                }
                $('#variant_type').val(splivArr.join(','));
                variant = $('#variant_type').val();



            } else {
                if (datatype != "Add-on") {
                    // alert(datatype);
                    //  htmlCont +='<div class="_ttb"><label>'+$("span[data-id-variant='" + id +"']").attr('data-type')+' ('+$("span[data-id-variant='" + id +"']").attr('data-name-variant')+'):</label> {{CURR}} '+$("span[data-id-variant='" + id +"']").attr('data-price-variant')+'</div>';
                    //  total = parseFloat(total) + parseFloat($("span[data-id-variant='" + id +"']").attr('data-price-variant'));
                    $('#variant_type').val(id);
                    variant = $('#variant_type').val();
                }
            }
        }



        if (variant != "") {
            var splivArr = $('#variant_type').val().split(',');
            splivArr.forEach(function (e) {
                if (e > 0) {

                    total = parseFloat(total) + parseFloat($("span[data-id-variant='" + e + "']").attr('data-price-variant'));
                    htmlCont += '<div class="_ttb"><label>' + $("span[data-id-variant='" + e + "']").attr('data-type') + ' (' + $("span[data-id-variant='" + e + "']").attr('data-name-variant') + '):</label> {{CURR}} ' + $("span[data-id-variant='" + e + "']").attr('data-price-variant') + '</div>';
                }
            });
        }
        if (addons != "") {
            var splivArr = $('#addons').val().split(',');
            splivArr.forEach(function (e) {
                if (e > 0) {
                    total = parseFloat(total) + parseFloat($("span[data-id='" + e + "']").attr('data-price'));
                    htmlCont += '<div class="_ttb"><label>' + $("span[data-id='" + e + "']").attr('data-type') + ' (' + $("span[data-id='" + e + "']").attr('data-name') + '):</label> {{CURR}} ' + $("span[data-id='" + e + "']").attr('data-price') + '</div>';
                }
            });
        }

        htmlCont += '<div class="_ttb"><label>Total:</label> {{CURR}}<span id="ton"> ' + total.toFixed(2) + '</span></div>';
        $('#summary').html(htmlCont);

        if ($('#ton').html().trim() > 0) {
            $('.left_main2').show();
        } else {
            $('.left_main2').hide();
        }

    });

    $('body').delegate('click', '.close', function (e) {
        $('#myModal').hide();
        $('#innercontent').html("");
    });
    $('body').delegate('click', '.namce', function (e) {
        $('#myModal').show();
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
    
    
    $('body').delegate('click', '.homcee', function (e) {
        swal({
            title: "Sorry!",
            text: "The restaurant is closed right now.",
            type: "error",
            html: true
        });
    });
    
</script>
<script>
    $(document).on("click", ".counter_numberpop", function () {

        $('.showcartloader').show();
        var type = $(this).attr("alt");
        var id_val = $(this).attr("id_val");
        var value = $('.preparation_time_' + id_val).val();
        value = value ? parseInt(value) : 0;

        var addons = $('#addons').val();
        var variant_type = $('#variant_type').val();

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
            qty: 1,
            type: type,
            submenus: submenus,
            addons: addons,
            variant_type: variant_type
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

    $('body').on('click', '.getallreviews', function (e) {
        $('.ddlj').removeClass('active');
        $('#tab3_li').addClass('active');
        $('.rj').hide();
        $('#tab3').show();
        $('html,body').animate({
            scrollTop: $(".left_new_section").offset().top - 150},
        'slow');
    });
</script>    
@stop




