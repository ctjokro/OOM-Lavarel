@section('content')
{{ HTML::script('public/js/front/jquery.jscroll.js'); }}
{{ HTML::script('public/js/jquery.validate.js') }}
<style>
    input#checkboxs.error {
        outline: 1px solid red;
    }
    .chs label.error {
        display: none !important;
    }
    #block_container
    {
        text-align:center;
    }
    #block1, #block2
    {
        display:inline;
    }
    .circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 15px;
        color: #fff;
        line-height: 50px;
        text-align: center;
        background: #a09b9b;
        transform: translate(650%, 0%);
    }
</style>
<script>
    $(document).ready(function () {
        $("#cartForm").validate();
        $(".lis_left_menu").jScroll();
        $(".show_items").click(function () {
            var class_name = $(this).attr("id");
            $('html,body').animate({
                scrollTop: $("." + class_name).offset().top},
                    'slow');
        })


        $(".show_form2").click(function () {
            var origin   = window.location.origin;
            var url = window.location.href
            var arr = url.split("/");
            var result = arr[0] + "//" + arr[2] + "/" + arr[3]
            $.ajax({
                //url: "<?php echo "https://zuastrestaurants.online-order.menu/staging/home/updatecart/" ?>" + 0,
                //url: result + '/home/updatecart/'+ 0,
                url: "<?php echo HTTP_PATH . "home/updatecart/" ?>" + 0,
                type: 'POST',
                dataType: 'json',
                success: function (data, textStatus, XMLHttpRequest)
                {
                    //  $('.show_form').hide();
                    $('.showcartloader').hide();
                    $(".carts_bx").html(data.data);

                    if ($(this).attr('data-rute') != undefined) {
                        $('#pickupoption').show();
                        $('#head').show();
                    }

                },
                error: function (XMLHttpRequest, textStatus, errorThrown)
                {

                }
            });
            $(".add_form").toggle();
            $("#is_address").val('1');
            $("#addradiobtn input:radio").attr("checked", false);
        })
       
    })
    
     $(document).ready(function () {
        $(".city").change(function () {
            /*$(".area").load("<?php echo "https://zuastrestaurants.online-order.menu/staging/customer/loadarea/" ?>" + $(this).val() + "/0");*/
            $(".area").load("<?php echo HTTP_PATH . "customer/loadarea/" ?>" + $(this).val() + "/0");
            
        })

        $(".city_caterer").change(function () {
            $(".area_caterer").load("<?php echo HTTP_PATH . "customer/loadarea/" ?>" + $(this).val() + "/0");
        })

    });
    
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
        var url = window.location.href
        var arr = url.split("/");
        var result = arr[0] + "//" + arr[2] + "/" + arr[3]
        $.ajax({
            //url: result + '/home/addtocart',
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
                        //url: result + '/home/totalcartvalue',
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
                                    //url: result + '/home/emptycart',
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


    $(document).on("click", ".leave_comment", function () {
        var id = $(this).attr("alt");
        $("#comment-box-" + id).toggle();
    })

    $(document).on("keyup", ".small-comment", function () {


        // update to cart array
        var data = {data: $(this).val(), id: $(this).attr('alt'), order: 1}
        var url = window.location.href
        var arr = url.split("/");
        var result = arr[0] + "//" + arr[2] + "/" + arr[3]
        $.ajax({
            /*url: "<?php echo "https://zuastrestaurants.online-order.menu/staging/home/updatecarttext" ?>",*/
            //url: result + '/home/updatecarttext',
            url: "<?php echo HTTP_PATH . "home/updatecarttext" ?>",
            dataType: 'json',
            type: 'POST',
            data: data
        });
    })
    $(document).on("click", ".remove_cart", function () {
        $('.showcartloader').show();
        var id = $(this).attr("alt");
        var data = {id: $(this).attr('alt'), order: 1}
        var url = window.location.href
        var arr = url.split("/");
        var result = arr[0] + "//" + arr[2] + "/" + arr[3]
        $.ajax({
            //url: result + '/home/removecart',
            url: "<?php echo HTTP_PATH . "home/removecart" ?>",
            dataType: 'json',
            type: 'POST',
            data: data,
            success: function (data, textStatus, XMLHttpRequest)
            {
                if (data.valid)
                {
                    $("#addradiobtn input:radio").attr("checked", false);
                    $('.showcartloader').hide();
                    $(".carts_bx").html(data.data);
                    $.ajax({
                         //url: result + '/home/totalcartvalue',
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
                } else
                {
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


//    $(document).on("change", "#area", function () {
//        $('.showcartloader').show();
//        var val = $('#area').val();
//        $.ajax({
//            url: "<?php echo HTTP_PATH . "home/updatecartNewAddress/" ?>" + val,
//            type: 'POST',
//            dataType: 'json',
//            success: function (data, textStatus, XMLHttpRequest)
//            {
//                $('.showcartloader').hide();
//                $(".carts_bx").html(data.data);
//            },
//            error: function (XMLHttpRequest, textStatus, errorThrown)
//            {
//
//            }
//        });
//    });


    function chkRadioClick(val) {
        //$('.showcartloader').show();
        var url = window.location.href
        var arr = url.split("/");
        var result = arr[0] + "//" + arr[2] + "/" + arr[3]
        $.ajax({
            //url: result + '/home/updatecart/'+ val,
            url: "<?php echo HTTP_PATH . "home/updatecart/" ?>" + val,
            type: 'POST',
            dataType: 'json',
            success: function (data, textStatus, XMLHttpRequest)
            {
                $('.showcartloader').hide();
                $(".carts_bx").html(data.data);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown)
            {

            }
        });
        $("#is_address").val('0');
        $('.add_form').hide();
    }

//    function chkForm() {
//        var myDiv = $('#addradiobtn');
//        if (myDiv.length) {
//            if ($('#addradiobtn input:radio:checked').length == 0 && $('#is_address').val() == '0') {
//                if ($('#pickupav').val() == 1) {
//
//                    if ($("input[name='pickup_ready']:checked").val() != undefined) {
//                        if ($("input[name='pickup_ready']:checked").val() == 1) {
//                            if ($("input[name='pickup_now']:checked").val() != undefined) {
//                                if ($("input[name='pickup_now']:checked").val() == 0 && $('#pickup_time').val() == 0) {
//                                    swal({
//                                        title: "Sorry!",
//                                        text: "Please enter your pick up time!",
//                                        type: "error",
//                                        html: true
//                                    });
//                                    return false;
//                                }
//                            } else {
//                                swal({
//                                    title: "Sorry!",
//                                    text: "Please select pick up option!",
//                                    type: "error",
//                                    html: true
//                                });
//                                return false;
//                            }
//                        } else {
//                            swal({
//                                title: "Sorry!",
//                                text: "Please select delivery address first!",
//                                type: "error",
//                                html: true
//                            });
//                            return false;
//                        }
//                    } else {
//                        swal({
//                            title: "Sorry!",
//                            text: "Please select delivery type first!",
//                            type: "error",
//                            html: true
//                        });
//                        return false;
//                    }
//                } else {
//                    swal({
//                        title: "Sorry!",
//                        text: "Please select delivery address first!",
//                        type: "error",
//                        html: true
//                    });
//                    return false;
//                }
//            } else {
//                if ($("#checkboxs").prop('checked') == true) {
//                    return true;
//                } else {
//                    swal({
//                        title: "Sorry!",
//                        text: "Please select terms and conditions first!",
//                        type: "error",
//                        html: true
//                    });
//                    return false;
//                }
//
//            }
//        } else {
//            return true;
//        }
//
//    }


function chkForm() { 
        var myDiv = $('#addradiobtn');
        if (myDiv.length) {
            if ($('#addradiobtn input:radio:checked').length == 0 && $('#is_address').val() == '0') {
                if($('#pickupav').val() == 1){
                  
                  if($("input[name='pickup_ready']:checked").val() != undefined){
                      if($("input[name='pickup_ready']:checked").val() == 1){
                        if($("input[name='pickup_now']:checked").val() != undefined){
                           if($("input[name='pickup_now']:checked").val() == 0 && $('#pickup_time').val() == 0){
                               swal({
                                    title: "Sorry!",
                                    text: "Please enter your pick up time!",
                                    type: "error",
                                    html: true
                                });
                                return false;
                           }
                        }else{
                            swal({
                                title: "Sorry!",
                                text: "Please select pick up option!",
                                type: "error",
                                html: true
                            });
                            return false;
                        }
                      }else{
                          swal({
                            title: "Sorry!",
                            text: "Please select delivery address first!",
                            type: "error",
                            html: true
                        });
                        return false;
                      }
                  }else{
                       swal({
                        title: "Sorry!",
                        text: "Please select delivery type first!",
                        type: "error",
                        html: true
                    });
                    return false;
                  }
                }else{
                    swal({
                        title: "Sorry!",
                        text: "Please select delivery address first!",
                        type: "error",
                        html: true
                    });
                    return false;
                }
            } else {
                /*if ($("#checkboxs").prop('checked') == true) {
                    return true;
                } else {
                    swal({
                        title: "Sorry!",
                        text: "Please select terms and conditions first!",
                        type: "error",
                        html: true
                    });
                    return false;
                }*/
                if (typeof $("input[name='payment_method']:checked").val() === "undefined") {
                    swal({
                        title: "Sorry!",
                        text: "Please select any payment option!",
                        type: "error",
                        html: true
                    });
                    return false;
                }else{
                    if ($("#checkboxs").prop('checked') == true) {
                        return true;
                    } else {
                        swal({
                            title: "Sorry!",
                            text: "Please select terms and conditions first!",
                            type: "error",
                            html: true
                        });
                        return false;
                    }
                    return true;
                }

            }
        } else {
            
            
            if($('#pickupav').val() == 1){
                  
                  if($("input[name='pickup_ready']:checked").val() != undefined){
                      if($("input[name='pickup_ready']:checked").val() == 1){
                        if($("input[name='pickup_now']:checked").val() != undefined){
                           if($("input[name='pickup_now']:checked").val() == 0 && $('#pickup_time').val() == 0){
                               swal({
                                    title: "Sorry!",
                                    text: "Please enter your pick up time!",
                                    type: "error",
                                    html: true
                                });
                                return false;
                           }
                        }else{
                            swal({
                                title: "Sorry!",
                                text: "Please select pick up option!",
                                type: "error",
                                html: true
                            });
                            return false;
                        }
                      }
                  }
                }
                
                if ($("#checkboxs").prop('checked') == true) {
                    return true;
                } else {
                    swal({
                        title: "Sorry!",
                        text: "Please select terms and conditions first!",
                        type: "error",
                        html: true
                    });
                    return false;
                }
            
            return true;
        }

    }

    function chkDelivery() {
        var basicDeliveryCharge = $('#basicDeliveryCharge').val();
        basicDeliveryCharge = basicDeliveryCharge.split("_");
        basicDeliveryCharge = basicDeliveryCharge[1];
        var advanceDeliveryCharge = $('#advanceDeliveryCharge').val();
        advanceDeliveryCharge = advanceDeliveryCharge.split("_");
        advanceDeliveryCharge = advanceDeliveryCharge[1];
        var gtotal = $("#gtotalwf").val();
        var nval = 0;
        if ($('#basicDeliveryCharge').is(':checked')) {
            nval = parseFloat(gtotal)
        }
        if ($('#advanceDeliveryCharge').is(':checked')) {
            nval = parseFloat(gtotal) + parseFloat(advanceDeliveryCharge) - parseFloat(basicDeliveryCharge);
        }


        nval = parseFloat(Math.round(nval * 100) / 100).toFixed(2);
        $('#gtotal').html(nval);
    }
    $(document).ready(function () {
        $('#delicon').mouseover(function () {
            $('#del_desc').show();
        });
        $('#delicon').mouseout(function () {
            $('#del_desc').hide();
        });
        $('#delicon').click(function () {
            $('#del_desc').show();
        });
    })

    $(document).on("click", "#submit_coupon", function () {
        // check the coupon code is valid
        if ($("#coupon").val()) {
            var data = {coupon: $("#coupon").val()};
            var url = window.location.href
            var arr = url.split("/");
            var result = arr[0] + "//" + arr[2] + "/" + arr[3]
            $.ajax({
                //url: result + '/home/applycoupon',
                url: "<?php echo HTTP_PATH . "home/applycoupon" ?>",
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function (data, textStatus, XMLHttpRequest)
                {
                    if (data.valid) {
                        window.location.reload();
                    } else {

                        $(".all_bg").hide();
                        swal({
                            title: "Sorry!",
                            text: data.error,
                            type: "error",
                            html: true
                        });
                        $("#coupon").val('');
                        return false;
                    }


                },
                error: function (XMLHttpRequest, textStatus, errorThrown)
                {
                    $(".all_bg").hide();
                    swal({
                        title: "Error!",
                        text: "Something went wrong, please try after some time.",
                        type: "error",
                        html: true
                    });
                    return false;
                },
                beforeSend: function ()
                {
                    $(".all_bg").show();
                }
            });

        } else {
            swal({
                title: "Error!",
                text: "Please enter coupon code",
                type: "error",
                html: true
            });
            return false;
        }

    })

    $(document).on("click", "#cancel_code", function () {
        var url = window.location.href
        var arr = url.split("/");
        var result = arr[0] + "//" + arr[2] + "/" + arr[3]
        $.ajax({
            //url: result + '/home/removecoupon',
            url: "<?php echo HTTP_PATH . "home/removecoupon" ?>",
            type: 'POST',
            dataType: 'json',
            success: function (data, textStatus, XMLHttpRequest)
            {
                if (data.valid) {
                    swal({
                        title: "",
                        text: "Coupon successfully removed",
                        type: "success",
                        html: true,
                    }, function () {
                        window.location.reload();
                    });
                } else {
                    swal({
                        title: "Sorry!",
                        text: data.error,
                        type: "error",
                        html: true
                    });
                    $("#coupon").val('');
                    return false;
                }


            },
            error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                swal({
                    title: "Error!",
                    text: "Something went wrong, please try after some time.",
                    type: "error",
                    html: true
                });
                return false;
            }
        });
    });
</script>
<div class="clear"></div>
<?php
//print_r($_SESSION);
$adminData = DB::table('admins')
        ->where('id', '1')
        ->first();
?>
<div class="all_bg">
    <div class="all_bg_ldr">
        <img src="{{ URL::asset('public/img/front') }}/loader.gif" alt="Please Wait..." />
    </div>
</div>
<div class="wrapper">
    <div class="list_bx">
        <div class="liste_top">
            <h1>Submit Your Order </h1>
            <?php 
                /*echo '<pre>';
                print_r($cart_content);
                foreach($cart_content as $tt)
                {
                    
                    echo $tt['id'];
                    echo $tt['caterer_id'];
                    
                }
                echo '</pre>';
                die;*/
            ?>
        </div>  
        <div class="left-order">
        </div>
        <div class="right-cart">

            <div class="col-md-12 col-sm-12 col-xs-12">
                <?php
                echo Form::open(array('url' => 'order/confirm', 'method' => 'post', 'id' => 'cartForm', 'files' => true))
                ?>
                <div class="ord_left">
                    <script>
                        $('body').on('click', '._cutt', function (e) {
                            $('#choosepick').show();
                            $('#adredsc').hide();
                        });
                        $('body').on('click', '._cuttC', function (e) {
                            /* Main Div show */
                            $('#choosepick').show();
                            $('#pickupoption').show();
                            $('#head').show();
                            $('._cuttC').hide();
                            /* Main Div show */

                            /* Address Div hide */
                            $('#adredsc').hide();
                            /* Address Div hide */

                            $("#is_address").val('0');

                            $('#ifyes').hide();
                            $('#pickuptime').hide();
                            $("#addradiobtn input:radio").attr("checked", false);
                            $("input[name='pickup_now']").attr("checked", false);
                            $("input[name='pickup_ready']").attr("checked", false);
                            
                            var url = window.location.href
                            var arr = url.split("/");
                            var result = arr[0] + "//" + arr[2] + "/" + arr[3]
                            
                            $.ajax({
                                //url: result + '/home/pickup/0',
                                url: "<?php echo HTTP_PATH . "home/pickup/0" ?>",
                                type: 'POST',
                                dataType: 'json',
                                success: function (data, textStatus, XMLHttpRequest)
                                {
                                    $('.showcartloader').hide();
                                    $(".carts_bx").html(data.data);
                                },
                                error: function (XMLHttpRequest, textStatus, errorThrown)
                                {

                                }
                            });
                            /*    $.ajax({
                             url: "<?php echo HTTP_PATH . "home/updatecart/" ?>" + 0,
                             type: 'POST',
                             dataType: 'json',
                             success: function (data, textStatus, XMLHttpRequest)
                             {
                             //  $('.show_form').hide();
                             $('.showcartloader').hide();
                             $(".carts_bx").html(data.data);
                             
                             if($(this).attr('data-rute') != undefined){
                             $('#pickupoption').show();
                             $('#head').show();
                             }
                             
                             },
                             error: function (XMLHttpRequest, textStatus, errorThrown)
                             {
                             
                             }
                             }); */
                        });
                    </script>
                    {{ View::make('elements.actionMessage')->render() }}
                    <input type="hidden" id="pickupav" value="0"  />
                    <?php
                    $user_id = Session::get('user_id');
                    $session_address_id = Session::get('session_address_id');
                    if (!empty($userData)) {
                        ?>
                        <div class="ord_left_bx">
                            <?php
                            $class = "block";
                            $restro_id = 0;
                            $starttime = "01:00am";
                            $endtime = "12:59am";
                            $pickup_charges = "";

                            if (!empty($cart_content)) {

                                foreach ($cart_content as $cat) {
                                    //echo "<pre>"; print_r($cat); exit;
                                    $restro_id = $cat['caterer_id'];
                                }
                                if ($restro_id) {
                                    $pickup_charges = DB::table('pickup_charges')
                                            ->where('user_id', $restro_id)
                                            ->first();

                                    $opening_hours = DB::table('opening_hours')
                                            ->where('user_id', $restro_id)
                                            ->first();

                                    if ($opening_hours) {
                                        if (date('i') > 30) {
                                            
                                        } else {
                                            
                                        }
                                        $starttime = date('h:ia', strtotime(date('Y-m-d') . ' ' . $opening_hours->start_time));
                                        $endtime = date('h:ia', strtotime(date('Y-m-d') . ' ' . $opening_hours->end_time));
                                    }



                                    if ($pickup_charges) { // echo "<pre>";print_r($pickup_charges); echo "</pre>";
                                     //   if ($pickup_charges->pick_up == 1) {
                                        if ($pickup_charges->pick_up !== 0) {    
                                            $choosediv = "block";
                                            if ($session_address_id) {
                                                $class = "block";
                                                $choosediv = "none";
                                            } else {
                                                $class = "none";
                                            }
                                            $pickup = Session::get('pickup');
                                            $when = Session::get('when');
                                            ?>
                                            <script>
                                                $('#pickupav').val('1');
                                            </script>
                                            <div id="choosepick" style="display: <?php echo $choosediv; ?>;height: 140px;">
                                                <h3 id="head">Choose your delivery type
                                                    <?php
                                                    //    if($pickup){
                                                    ?><span style="display: <?php echo (isset($pickup)) ? "block" : "none"; ?>" data-rute='typec' class="_cuttC show_form">Change delivery type</span><?php
                                                    //  }
                                                    ?>
                                                </h3>
                                                <div class="ore_input_bx fadeoutif" id="pickupoption" style="display: <?php echo isset($pickup) ? "none" : "block"; ?>">
                                                    
                                                    <?php if($pickup_charges->pick_up == 1) {  ?>
                                                    <input class="btn btn-primary parentcheck"  type="radio" name="pickup_ready" value="1" id="pickup_1" <?php
                                                    if (isset($pickup)) {
                                                        echo "checked";
                                                    }
                                                    ?>   /><label for="pickup_1"> Pick up</label>
                                                    
                                                    <?php } ?>
                                                    
                                                    <?php if($pickup_charges->pick_up == 2) {  ?>
                                                    
                                                    <input class="btn btn-primary parentcheck"  type="radio" name="pickup_ready" value="0" id="pickup_0"  /><label for="pickup_0"> Deliver at my home 1</label>
                                                    <?php } ?>
                                                    
                                                    <?php if($pickup_charges->pick_up == 3) {  ?>
                                                    <input class="btn btn-primary parentcheck"  type="radio" name="pickup_ready" value="1" id="pickup_1" <?php
                                                    if (isset($pickup)) {
                                                        echo "checked";
                                                    }
                                                    ?>   /><label for="pickup_1"> Pick up</label>
                                                    <br>
                                                    <input class="btn btn-primary parentcheck"  type="radio" name="pickup_ready" value="0" id="pickup_0"  /><label for="pickup_0"> Deliver at my home 1</label>
                                                    <?php } ?>                
                                                </div>
                                                <div class="ore_input_bx fadeoutif" id="ifyes" style="display: <?php echo isset($pickup) ? "block" : "none"; ?>">
                                                    <input class="btn btn-primary subcheck"  type="radio" name="pickup_now" value="1" id="pickup_now_1"  <?php
                                                    if (isset($when) && $when == "now") {
                                                        echo "checked";
                                                    }
                                                    ?> /><label for="pickup_now_1"> Pick up now</label>
                                                    <br>
                                                    <input class="btn btn-primary subcheck"  type="radio" name="pickup_now" value="0" id="pickup_later_0" <?php
                                                    if (isset($when) && $when == "later") {
                                                        echo "checked";
                                                        ?><?php } ?>  /><label for="pickup_later_0"> Pick up later</label>
                                                        <br>
                                                        <label for="pickup_now_1" > Comment</label>
                                                    <br>
                                                        <textarea class="form-control" id="pick_comment" name="pick_comment" style="width: 50%; "></textarea>

                                                </div>
                                            </div>
                                            <?php
                                        } else {
                                            ?><script>
                                                                    $('#pickupav').val('0');
                                            </script><?php
                                        }
                                    }
                                }
                            }
                            ?>
                            {{ HTML::style('public/js/front/timepicker/jquery.timepicker.css'); }}
                            {{ HTML::script('public/js/front/timepicker/jquery.timepicker.js'); }}

                            <script>
                                $('body').on('click', '.parentcheck', function (e) {
                                    $("#is_address").val('0');
                                    var yesorno = $(this).val();
                                    $('.show_form').show();
                                    var url = window.location.href
                                    var arr = url.split("/");
                                    var result = arr[0] + "//" + arr[2] + "/" + arr[3]
                                    if (yesorno == 1) {
                                        $('#pickupav').val('1');
                                        $('#pickupoption').fadeOut('slow');
                                        $('#ifyes').fadeIn('slow');
                                        $('.cuttC').hide();
                                        $.ajax({
                                            //url: result + '/home/pickup/1',
                                            url: "<?php echo HTTP_PATH . "home/pickup/1" ?>",
                                            type: 'POST',
                                            dataType: 'json',
                                            success: function (data, textStatus, XMLHttpRequest)
                                            {
                                                $('.showcartloader').hide();
                                                $(".carts_bx").html(data.data);
                                            },
                                            error: function (XMLHttpRequest, textStatus, errorThrown)
                                            {

                                            }
                                        });
                                    } else {
                                        $('#pickupoption').fadeOut('slow');
                                        $('#adredsc').fadeIn('slow');
                                        $('#head').hide();
                                    }
                                });

                                $('body').on('click', '.subcheck', function (e) {
                                    $("#is_address").val('0');
                                    var yesorno = $(this).val();
                                    var url = window.location.href
                                    var arr = url.split("/");
                                    var result = arr[0] + "//" + arr[2] + "/" + arr[3]
                                    if (yesorno == 1) {

                                        $('#pickuptime').hide();
                                        $.ajax({
                                            //url: result + '/home/pickup/1/now',
                                            url: "<?php echo HTTP_PATH . "home/pickup/1/now" ?>",
                                            type: 'POST',
                                            dataType: 'json',
                                            success: function (data, textStatus, XMLHttpRequest)
                                            {
                                                $('.showcartloader').hide();
                                                $(".carts_bx").html(data.data);
                                            },
                                            error: function (XMLHttpRequest, textStatus, errorThrown)
                                            {

                                            }
                                        });
                                    } else {
                                        //  $('#ifyes').fadeOut('slow');
                                        $('#pickuptime').fadeIn('slow');
                                        // $('#head').hide();
                                        $.ajax({
                                            //url: result + '/home/pickup/1/later',
                                            url: "<?php echo HTTP_PATH . "home/pickup/1/later" ?>",
                                            type: 'POST',
                                            dataType: 'json',
                                            success: function (data, textStatus, XMLHttpRequest)
                                            {
                                                $('.showcartloader').hide();
                                                $(".carts_bx").html(data.data);
                                            },
                                            error: function (XMLHttpRequest, textStatus, errorThrown)
                                            {

                                            }
                                        });
                                    }
                                });
                                $(document).ready(function () {
                                    $('#pickup_time').timepicker({'defaultTime': 'now', 'forceRoundTime': true, 'timeFormat': 'h:i a', 'step': 30, 'minTime': '<?php echo date('h:i a'); ?>', 'maxTime': '<?php echo $endtime; ?>'});
                                });
                            </script> 

                            <style>
                                .ore_input.cvnnn {
                                    margin-top: 82px;
                                }
                            </style>
                            <div id="pickuptime" style="display: none" class="ore_input cvnnn">
                                <h3>Enter your pick up time</h3>
                                <div class="multiple-fields">
                                    <div class="form_group">
                                        <div class="form_group_left">
                                            {{ HTML::decode(Form::label('pickup_time', "Pick up time <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                            <div class="in_upt">
                                                {{  Form::text('pickup_time', Input::old('pickup_time'),  array('class' => 'required form-control','id'=>"pickup_time"))}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>    
                            <?php if (isset($when) && $when == "later") { ?>
                                <script>  $('#pickuptime').show();</script>
                            <?php } ?>
                            <div id="adredsc" style="display: <?php echo $class; ?>">
                                <?php if (!$records->isEmpty()) { ?>
                                    <h3>Choose your delivery address
                                        <?php
                                        $bl = "none";
                                        if ($pickup_charges) {
                                            if ($pickup_charges->pick_up == 1) {
                                                if (isset($pickup)) {
                                                    $bl = "none";
                                                } else {
                                                    $bl = "block";
                                                }
                                            }
                                        }
                                        //echo $pickup_charges->pick_up;
//                                if($pickup_charges){
//                                if($pickup_charges->pick_up == 1){
//                                    if($session_address_id){
                                        ?>  

                                        <span data-rute='typec' style="display: <?php echo $bl; ?>" class="_cuttC show_form">Change delivery type</span>
                                        <?php
//                                    }
//                                }}
                                        ?>
                                    </h3>
                                <?php } else { ?>
                                    <h3>Fill your delivery address details <span style="display: none" data-rute='typec' class="_cuttC show_form">Change delivery type</span></h3>     
                                <?php } ?>
                                <div class="ore_input">
                                    <?php
                                    if (!$records->isEmpty()) {

                                        $session_address_id = Session::get('session_address_id');
                                        ?>
                                        <div id ="addradiobtn">

                                            <?php
                                            foreach ($records as $data) {
                                                $checked = "";
                                                if ($session_address_id > 0 && $data->id == $session_address_id) {
                                                    $checked = "checked";
                                                }
//                                            print_r($data);
                                                ?>
                                                <div class="ore_input_bx">
                                                    <input class="btn btn-primary" <?php echo $checked; ?> type="radio" name="address" value="{{$data->id}}" id="address_{{$data->id}}" onclick="chkRadioClick(this.value);" />
                                                    <label for="address_{{$data->id}}"> <?php echo $data->address_title ? $data->address_title . ',' : ' ' ?><?php echo $data->building ? $data->building . ',' : ' '; ?> <?php echo $data->floor ? $data->floor . ',' : ' ' ?> <?php echo $data->apartment ? $data->apartment . ',' : ' '; ?> <?php echo $data->area_name ? $data->area_name . ',' : ' '; ?> <?php echo $data->city_name ? $data->city_name : ' ' ?></label>
                                                </div>
                                            <?php } ?>
                                            <div class="or_add"><a href="javascript:void(0)" class="show_form2">or add new address</a></div>  
                                        </div>
                                        <?php echo Form::hidden('is_address', "0", array('id' => 'is_address')); ?>
                                    <?php } else {
                                        ?>
                                        <?php echo Form::hidden('is_address', "1", array('id' => 'is_address')); ?>
                                    <?php }
                                    ?>
                                    <div {{!$records->isEmpty()?"style='display:none'":""}} class="add_form">
                                        <div class="multiple-fields">
                                            <div class="form_group">
                                                <div class="form_group_left">
                                                    {{ HTML::decode(Form::label('address_title', "Address Title <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                                    <div class="in_upt">
                                                        {{  Form::text('address_title', Input::old('address_title'),  array('class' => 'required form-control','id'=>"address_title"))}}
                                                    </div>
                                                </div>
                                                <div class="form_group_left form_group_right">

                                                    {{ HTML::decode(Form::label('address_type', "Address Type <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                                    <div class="in_upt">
                                                        <?php
                                                        $address_type = array(
                                                            '' => 'Please Select',
                                                            'Home' => 'Home',
                                                            'Work' => 'Work',
                                                            'Other' => 'Other',
                                                        );
                                                        ?>
                                                        {{ Form::select('address_type', $address_type, Input::old('address_type'), array('class' => 'form-control required', 'id'=>'address_type')) }}
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="form_group">
                                                <div class="form_group_left">
                                                    {{ HTML::decode(Form::label('city', "City <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                                    <div class="in_upt">
                                                        <?php
            $cities_array = array(
                '' => 'City'
            );

            // get Cairo city id
            $city_id = City::where('name', "like", "%Cairo%")->lists('id');
            $city_id = isset($city_id[0]) ? $city_id[0] : "";

            //$cities = City::where('status', "=", "1")->orderBy('name', 'asc')->lists('name', 'id');
            $cities = City::where('status', '1')->orderBy('name', 'asc')->lists('name', 'id');
           
            if (!empty($cities)) {
                foreach ($cities as $key => $val)
                    $cities_array[$key] = ucfirst($val);
            }
            ?>
            {{ Form::select('city', $cities_array, $city_id, array('class' => 'required form-control city_caterer')) }}
                                                    </div>
                                                </div>
                                                <div class="form_group_left form_group_right">

                                                    {{ HTML::decode(Form::label('area', "Area <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                                    <div class="in_upt">
                                                        <?php
            $area_array = array(
                '' => 'Area'
            );
            $area = Area::orderBy('name', 'asc')->where('status', "=", "1")->where('city_id', "=", $city_id)->lists('name', 'id');
            if (!empty($area)) {
                foreach ($area as $key => $val)
                    $area_array[$key] = ucfirst($val);
            }
            ?>
            {{ Form::select('area', $area_array, Input::old('area'), array('class' => 'required form-control area_caterer')) }}

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form_group">
                                                <div class="form_group_left">
                                                    {{ HTML::decode(Form::label('street_name', "Street Name <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                                    <div class="in_upt">
                                                        {{  Form::text('street_name', Input::old('street_name'),  array('class' => 'required form-control','id'=>"street_name"))}}
                                                    </div>
                                                </div>
                                                <div class="form_group_left form_group_right">

                                                    {{ HTML::decode(Form::label('building', "Building ",array('class'=>"control-label col-lg-2"))) }}
                                                    <div class="in_upt">
                                                        {{  Form::text('building', Input::old('building'),  array('class' => 'form-control','id'=>"building"))}}
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form_group">
                                                <div class="form_group_left">
                                                    {{ HTML::decode(Form::label('floor', "Floor",array('class'=>"control-label col-lg-2"))) }}
                                                    <div class="in_upt">
                                                        {{  Form::text('floor', Input::old('floor'),  array('class' => 'form-control','id'=>"floor"))}}
                                                    </div>
                                                </div>
                                                <div class="form_group_left form_group_right">

                                                    {{ HTML::decode(Form::label('apartment', "Apartment",array('class'=>"control-label col-lg-2"))) }}
                                                    <div class="in_upt">
                                                        {{  Form::text('apartment', Input::old('apartment'),  array('class' => 'form-control','id'=>"apartment"))}}

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form_group">
                                                <div class="form_group_left">
                                                    {{ HTML::decode(Form::label('phone_number', "Phone Number <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                                    <div class="in_upt">
                                                        {{  Form::text('phone_number', Input::old('phone_number'),  array('maxlength'=>"16", 'class' => 'required number form-control','id'=>"phone_number"))}}
                                                    </div>
                                                </div>

                                                <div class="form_group_left form_group_right">
                                                    {{ HTML::decode(Form::label('extension', "Extension",array('class'=>"control-label col-lg-2"))) }}
                                                    <div class="in_upt">
                                                        {{  Form::text('extension', Input::old('extension'),  array('class' => 'form-control','id'=>"extension"))}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form_group form_groupse">
                                            {{ HTML::decode(Form::label('directions', "Directions",array('class'=>"control-label col-lg-2"))) }}
                                            <div class="in_upt">
                                                {{  Form::textarea('directions',Input::old('directions'),  array('class' => 'form-control','id'=>"directions"))}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ord_left_bx">
                            <?php
                            // check coupon code if exists
                            $coupon = Session::get('coupon');
                            $coupon_detail = DB::table('coupons')
                                    ->where('code', $coupon)
                                    ->where('start_time', "<=", date('Y-m-d'))
                                    ->where('end_time', ">=", date('Y-m-d'))
                                    ->where('status', '1')
                                    ->first();
                            if (!empty($coupon_detail)) {
                                ?>
                                <div class="applied-code">
                                    Congratulation! <span title="You are getting {{$coupon_detail->discount}}% discount">{{$coupon}}</span> successfully applied and you get {{$coupon_detail->discount}}% discount
                                    <span class="close-coupon">
                                        <a href="javascript:void(0)" title="Cancel coupon code" id="cancel_code">
                                            X
                                        </a>
                                    </span>
                                </div>
                                <?php
                            } else {
                                ?>

                                <h3>Do you have a coupon? Enter code below then click “Apply”</h3>
                                <div class="ore_input">
                                    <div class="ore_input_bx">
                                        <input type="text" id="coupon" value="" placeholder="Please enter coupon code here"/>
                                        <input  class="btn btn-primary" type="button" value="Apply" id="submit_coupon" />
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <?php  if ($restro_id > 0) {  ?>
                            <div class="ord_left_bx">    
                                <div class="_chttd">
                                    <h3>Choose Payment Method</h3>
                                    <div class="_ttc" id="methods">

                                       <!-- <div class="_cttvvv active" data-div="paypal" data-value="1">
                                            <span class="_imgh">{{HTML::image(HTTP_PATH . "public/img/paypal.png")}}</span>
                                            Paypal </div>-->
                                            
                                             <label>
                                             <?php  $user_restro = $restro_id;
                                                $user_detail = DB::table('users')->where('id', $user_restro)->first(); ?>
                                                <?php 
                                                $rf = explode(',',$user_detail->payment_method);
                                               
                                                 //print_r(array_count($user_detail->payment_method));
                                                 for ($i = 0; $i < count($rf); $i++) { 
                                                 // for($i=0;$i<=count($user_detail->payment_method);$i++) { 
                                                  if($rf[$i]== 'Paypal') { 
                                                ?>
                                             <div class="_cttvvv active" data-div="paypal" data-value="1">
                                                  <input type="radio" name="payment_method" value="small">
                                                    <span class="_imgh">{{HTML::image(HTTP_PATH . "public/img/paypal.png")}}</span>
                                                    <!--Pay safely and faster with Paypal--> </div>
                                                    </label>
                                            <?php } else if($rf[$i]== 'Bank Transfer') { ?>
                                            &nbsp;&nbsp;&nbsp;
                                            <?php //$user_id = Session::get('user_id');
                                            $user_restro = $restro_id;
                                                $bank_detail = DB::table('bank_transfer_detail')->where('user_id', $user_restro)->first();
                                                $user = DB::table('users')->where('id', $user_restro)->first();
                                               
                                                ?>
                                                <input type="hidden" name="restaurant_id" value="<?php echo $user_restro; ?>">
                                            <?php if($bank_detail != '')
                                               {
                                               ?>
                                             <label>
                                               <div class="_cttvvv active" data-div="bank_transfer" data-value="2">
                                                   <input type="radio" name="payment_method" value="bank_transfer">
                                              
                                                <b>Bank Transfer</b> </div>
                                            </label>
                                            <?php } else { ?>
                                               
                                                 <?php } ?>
                                                
                                                <?php } else if($rf[$i]== 'Qrcode') { ?>
                                            &nbsp;&nbsp;&nbsp;
                                            <?php //$user_id = Session::get('user_id');
                                            $user_restro = $restro_id;
                                                $qrcode = DB::table('users')->where('id', $user_restro)->first();
                                                //$user = DB::table('users')->where('id', $user_restro)->first();
                                               
                                                ?>
                                                <input type="hidden" name="restaurant_id" value="<?php echo $user_restro; ?>">
                                                
                                            <?php if($qrcode->payment_qrcode != '')
                                               {
                                               ?>
                                             <label>
                                               <div class="_cttvvv active" data-div="Qrcode" data-value="4">
                                                   <input type="radio" name="payment_method" id="Qrcode" value="Qrcode">
                                              
                                                <b>Qrcode</b> </div>
                                                
                                            </label>
                                            <?php } else { ?>
                                               
                                                <?php } ?>
                                                <?php } if($rf[$i]== 'Payment Gateway') { ?>
                                                &nbsp;&nbsp;&nbsp;
                                                <label>
                                                <div class="_cttvvv active" data-div="payment_gateway" data-value="3">
                                                   <input type="radio" name="payment_method" id="xendit_gateway" value="xendit_gateway">
                                              
                                                <b>CreditCard/DebitCard</b> </div>
                                                </label>
                                            <?php //$user_id = Session::get('user_id');
                                            $user_restro = $restro_id;
                                                $bank_detail = DB::table('bank_transfer_detail')->where('user_id', $user_restro)->first();
                                                $user = DB::table('users')->where('id', $user_restro)->first();
                                               
                                                ?>
                                                
                                                <?php } } ?>
                                            
                                            <div id="xendit-option" style='display:none'>
                                                <input type="radio" name="xendit-card-selection" value="xendit_credit"/>&nbsp;<b>Credit</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type="radio" name="xendit-card-selection" value="xendit_debit"/>&nbsp;<b>Debit(BANK BRI)</b>
                                                <div style="margin-top: 10px;font-size: 15px;color: red;"><p>Note: This Payment option is only for indonesian customers.</p></div>
                                            </div>   
                                              
                                            <div id='show-me' style='display:none'>
                                               
                                              <?php  $user_restro = $restro_id;
                                                $bank_detail = DB::table('bank_transfer_detail')->where('user_id', $user_restro)->first();
                                                $user = DB::table('users')->where('id', $user_restro)->first(); ?>
                                               <?php if($bank_detail != '')
                                               {
                                               ?>
                                               <div id="block_container">
                                                    <div id="block1">
                                                       
                                                        <span><b>BSB Number :-</b> <?php echo $bank_detail->bsbno;?></span><br>
                                                        <span><b>Account Number :-</b> <?php echo $bank_detail->account_no;?></span><br>
                                                        <span><b>Account Name :- </b><?php echo $bank_detail->account_name;?></span><br>
                                                    </div>
                                                    <br>
                                                    <!--<p style="padding-top: 20px;font-size: 20px;font-weight: bold;">OR</p>-->
                                                    <?php if($user_restro == 158){ ?> <div class="circle">OR</div> <?php } ?>
                                                    <br>
                                                    <div id="block2">
                                                       <label>
                                                            <div class="_cttvvv active">
                                                               <?php if($user_restro == 158){ ?> <figure style="margin-left: 15px;"><figcaption style="text-align: center;">Opal Griya</figcaption><img src="{{asset('public/img/QR_Code/trans-opal-griya.png')}}"><figcaption>NMID - ID2020034197760</figcaption><figcaption style="text-align: center;">A01</figcaption></figure> <?php } ?>
                                                            </div>
                                                        </label> 
                                                    </div>
                                                </div>
                                                <span><b style="font-size: 15px;color: #fd0404;">(Please send receipt on <?php echo $user->email_address;?> email after payment done.)</span><br>
                                                <?php } else { ?>
                                               
                                                <?php } ?>
                                                
                                                </div>
                                                
                                                <div id='show-qrcode' style='display:none'>
                                              <?php  $user_restro = $restro_id;
                                                $qrcode_detail = DB::table('users')->where('id', $user_restro)->first();
                                                
                                               // $user = DB::table('users')->where('id', $user_restro); ?>
                                                
                                               <?php if($qrcode_detail->payment_qrcode != '')
                                               {
                                               ?>
                                                <div id="block_container">
                                                    <div id="block1">
                                                       
                                                       <div class="form_group">
                                                            
                                                            <div class="in_upt">
                                                                <div class="in_upt_img">
                                                                    <?php if (file_exists(UPLOAD_FULL_PROFILE_IMAGE_PATH . '/' . $qrcode_detail->payment_qrcode) && $qrcode_detail->payment_qrcode != "") { ?>
                                                                        {{ HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH.$qrcode_detail->payment_qrcode, '') }}
                                                                       
                                                                        <?php
                                                                    } else {
                                                                        echo HTML::image('public/img/front/nouser.png', '', array());
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                </div>
                                               
                                                <?php } else { ?>
                                               
                                                <?php } ?>
                                                
                                            </div>
                                            <script>
                                                $("input[name='payment_method']").click(function () {
                                                        $('#show-me').css('display', ($(this).val() === 'bank_transfer') ? 'block':'none');
                                                        $('#show-qrcode').css('display', ($(this).val() === 'Qrcode') ? 'block':'none');
                                                        $('#xendit-option').css('display', ($(this).val() === 'xendit_gateway') ? 'block':'none');
                                                    });
                                            </script>

                                        <?php
                                        $catererData = DB::table('users')->where('id', $restro_id)->first();
                                        //print_r($catererData);
                                        if ($catererData->paypal_username && $catererData->paypal_password && $catererData->paypal_signature) {
                                            ?>
<!--                                            <div class="_cttvvv " data-div="creditcard" data-value="0">
                                                <span class="_imgh">{{HTML::image(HTTP_PATH . "public/img/credit-card.png")}}</span>
                                                <span class="_asdyty">Credit Card</span> 

                                            </div>-->
                                            <div class="dash_board_inn_detail_new donate_box" id="creditcard" style="display: none">
                                                <div class="dash_board_cen_new">

                                                    <div class="reward_new">
                                                        <div class="deat_wrap">

                                                            <div class="detai_info">
                                                                <div class="lable_txt">Your Name</div>
                                                                <div class="src_uuf">
                                                                    <span>

                                                                        {{  Form::text('full_name', $userData->first_name.' '.$userData->last_name,  array('class' => 'required form-control','id'=>"full_name"))}}
                                                                    </span>

                                                                </div>
                                                            </div>

                                                        </div>




                                                        <div class="deat_wrap">

                                                            <div class="detai_info">
                                                                <div class="lable_txt"><i class="fa fa-lock"></i>Credit or Debit Number</div>
                                                                <div class="src_uuf">
                                                                    <span>
                                                                        {{  Form::text('card_number', Input::old("card_number"),  array('class' => 'required form-control','id'=>"card_number",'maxlength'=>'16'))}}


                                                                    </span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="deat_wrap">
                                                            <div class="deat_wrap_left ">
                                                                <div class="lable_txt">Expiration Month</div>
                                                                <div class="detai_info">
                                                                    <div class="src_uuf_select">
                                                                        <span>
                                                                            <?php
                                                                            global $month;
                                                                            ?>
                                                                            {{ Form::select('card_exp_month', $month, Input::old("card_exp_month"), array('class' => 'form-control required', 'id'=>'card_exp_month')) }}
                                                                        </span></div>
                                                                </div>
                                                            </div>
                                                            <div class="deat_wrap_left deat_wrap_right">
                                                                <div class="lable_txt">Expiration Year</div>
                                                                <div class="detai_info">
                                                                    <div class="src_uuf_select">
                                                                        <span><?php
                                                                            global $years;
                                                                            ?>
                                                                            {{ Form::select('card_exp_year', $years, Input::old("card_exp_year"), array('class' => 'form-control required', 'id'=>'card_exp_year')) }}
                                                                        </span>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        </div>
                                                        <div class="deat_wrap">
                                                            <div class="deat_wrap_left ">
                                                                <div class="lable_txt">Security Code</div>
                                                                <div class="detai_info">
                                                                    {{  Form::text('card_cvv', Input::old("card_cvv"),  array('class' => 'required form-control','id'=>"card_cvv",'maxlength'=>'16'))}}


                                                                </div>
                                                            </div>
                                                            <div class="deat_wrap_left deat_wrap_right">
                                                                <div class="crd_img">
                                                                    {{HTML::image(HTTP_PATH . "public/img/bank_card.png")}}

                                                                </div>

                                                            </div>
                                                            <label class="in-label">Enter 3-digits on back of card. AmEx uses front 4-digit code</label>

                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        <?php } ?>

                                    </div>

                                </div>
                            </div>
                        <?php } else { echo 'dssadas';  } ?>
                        <div class="chs">
                            <input type="hidden" name="payment_mode" value="1" id="payment_mode">

                            <?php
                            echo Form::checkbox('terms', "", null, array('id' => 'checkboxs', 'class' => 'required'));
                            ?>
                            <label for="term_window">I have read and accept {{SITE_TITLE}} <a href="javascript:void(0)" class="term_window" title="Copyright Policy">Terms &amp; Conditions.</a> </label>
                        
                        </div>
                        <span>Note: If you or someone your’re ordering for has a food allergy or intolerance, please contact restaurant on Restaurant number</span>
                        <div class="chs">

                            <?php echo Form::submit('Submit', array("onclick" => "return chkForm(this.form);", 'class' => 'btn btn-primary')) ?>
                        </div>

                        <?php
                    } else {
                        ?>
                        <div class="log_sig_bt_order">
                            <span>
                                You need to login/signup for placing order over site.
                            </span>
                            <a alt="login-window" class="popup-box btn btn-primary" href="javascript:void(0)">Login</a>
                            <a title="Signup" alt="signup-customer-window" class="popup-box btn btn-primary" href="javascript:void(0)">Signup</a>
                        </div>
                        <?php
                    }
                    ?>

                </div>

                <div class="carts_bx ord_right half_order">
                    <h2>Your Food Basket <span>{{$cart->totalItems()}}</span></h2>
                    <div class="cart_box">
                        <div class="showcartloader" style="display:none;"><img src="{{ URL::asset('public/img/front') }}/loader.gif" alt="img" /></div>

                        <?php
                        // create cart content array
                        $restro_id = 0;
                        $key_array = array();
                        if (!empty($cart_content)) {
                            foreach ($cart_content as $key)
                                $key_array[$key['id']] = $key['quantity'];
                        }
                        if (!empty($cart_content)) {
                            // categorized menu according to the carters name
                            $new_cat_array = array();
                            foreach ($cart_content as $cat) {
                                //   print_r($cat);
                                // get menu details
                                $item = DB::table('menu_item')
                                        ->leftjoin('users', 'users.id', '=', 'menu_item.user_id')
                                        ->where('menu_item.id', $cat['id'])
                                        ->select("item_name", "price", "users.first_name", "users.id", "users.last_name", "users.slug")
                                        ->first();
                                // print_r($item); exit;
                                $cat['slug'] = $item->slug;
                                $new_cat_array[$item->first_name . " " . $item->last_name][] = $cat;
                                $restro_id = $cat['caterer_id'];
                            }
                            //echo $restro_id; exit;
                            ?>
                            <!-- ********** Delivery Charge Conditions Start *********** -->
                            <?php
                            //print_r($session_address_id); exit;

                            $userData = DB::table('users')
                                    ->where('id', $user_id)
                                    ->first();
                            $defaultDeliveryCarge = DB::table('admins')
                                    ->where('id', 1)
                                    ->first();
                            $isDefaultDeliveryApplicable = $defaultDeliveryCarge->is_default_delivery;
                            $normalDefaultPrice = $defaultDeliveryCarge->normal;
                            $advanceDefaultPrice = $defaultDeliveryCarge->advance;
                            $deliveryChargeLimit = $defaultDeliveryCarge->delivery_charge_limit;
                            // echo $restro_id; exit;
                            if (!empty($userData)) {

                                if (!empty($session_address_id)) {
                                    $addressData = DB::table('addresses')
                                            ->where('id', $session_address_id)
                                            ->first();
                                    $user_city_id = $addressData->city;
                                    $user_area_id = $addressData->area;
                                    $delivery = 1;
                                } else {
                                    $user_city_id = 0;
                                    $user_area_id = 0;
                                    $delivery = 0;
                                }

                                $catererData = DB::table('users')
                                        ->where('id', $item->id)
                                        ->first();

                                //   print_r($catererData); exit;
                                $caterer_city_id = $catererData->city;
                                $caterer_area_id = $catererData->area;

                                if ($isDefaultDeliveryApplicable == 1) {
                                    $basicDeliverycharge = $normalDefaultPrice;
                                    $advanceDeliverycharge = $advanceDefaultPrice;
                                } else {
                                    if (!empty($user_city_id) && !empty($user_area_id) && !empty($caterer_city_id) && !empty($caterer_area_id)) {
                                        $deliveryCharge = DB::table('delivery_charges')
                                                ->where('from_city_id', $caterer_city_id)
                                                ->where('from_area_id', $caterer_area_id)
                                                ->where('to_city_id', $user_city_id)
                                                ->where('to_area_id', $user_area_id)
                                                ->first();
                                        if (!empty($deliveryCharge)) {
                                            $delivery = '1';
                                            $basicDeliverycharge = $deliveryCharge->basic_charge;
                                            $advanceDeliverycharge = $deliveryCharge->advance_charge;
                                            $deliveryChargeLimit = $deliveryCharge->delivery_charge_limit;
                                        } else {
                                            $delivery = 0;
                                        }
                                    } else {
                                        $delivery = 0;
                                    }
                                }
                            } else {

                                $delivery = 0;
                                $catererData = DB::table('users')
                                        ->where('id', $restro_id)
                                        ->first();
                            }
                            $gtotal = 0;
                            $addonTotal = array();
                            $total = array();
                            ?>

                            <!-- ********** Delivery Charge Conditions End ************* -->
                            <div class="crt_detal">
                                <?php
                                foreach ($new_cat_array as $carter => $items) {
                                    ?>
                                    <h3>{{html_entity_decode(HTML::link('restaurants/menu/' . $items[0]['slug'], $carter, array('target' => '_blank', 'title' => $carter))); }}</h3>
                                    <ul>
                                        <?php
                                        foreach ($items as $item_detail) {
                                            ?>
                                            <li>
                                                <div class="_ncbvvv">
                                                    <span>{{$item_detail['name']}} </span>
                                                    <?php if (!empty($item_detail['submenus'])) { ?>
                                                        <span class="sumss">{{$item_detail['submenus']}} </span>
                                                    <?php } ?>
                                                    <?php
                                                    $addonprice = 0;
                                                    if (isset($item_detail['variant_type'])) {
                                                        $explode = explode(',', $item_detail['variant_type']);
                                                        if ($explode) {
                                                            foreach ($explode as $explodeVal) {

                                                                $addonV = DB::table('variants')
                                                                        ->where('variants.id', $explodeVal)
                                                                        ->first();
                                                                if ($addonV) {
                                                                    $addonprice = $addonprice + $addonV->price;
                                                                    $addonTotal[] = $addonprice;
                                                                            $timezone_currency = DB::table('timezone_currency')
                        ->where("timezone_currency.user_id", "=", $restro_id)
                        ->first();
                       
        $currency = $timezone_currency->currency;
                                                                    
                                                                    ?> <div class="top_p"><span class="sumss_new"><i class="fa fa-tag"></i> Variant ({{$addonV->name}}) </span> <span class="pricev"><strong><?php foreach(Config::get('constant') as $key => $c) { 
                                                                    if($key == $currency)
                                                                        {
                                                                          echo $c; }}?>{{$addonV->price}} </strong></span> </div><?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                    //print_r($item_detail); exit;
                                                    if (isset($item_detail['addons'])) {
                                                        $explode = explode(',', $item_detail['addons']);
                                                        if ($explode) {
                                                            foreach ($explode as $explodeVal) {

                                                                $addonV = DB::table('addons')
                                                                        ->where('addons.id', $explodeVal)
                                                                        ->first();
                                                                if ($addonV) {
                                                                    $addonprice = $addonprice + $addonV->addon_price;
                                                                    $addonTotal[] = $addonprice;
                                                                    ?> <div class="top_p"><span class="sumss_new"><i class="fa fa-tag"></i> Add-on ({{$addonV->addon_name}}) </span> <span class="pricev"><strong><?php foreach(Config::get('constant') as $key => $c) { 
                                                                    if($key == $currency)
                                                                        {
                                                                          echo $c; }}?>{{$addonV->addon_price}} </strong></span></div><?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $addonprice = $addonprice * $item_detail['quantity'];
                                                    $total[] = $addonprice;
                                                    //print_r($item_detail); exit;
                                                    ?>
                                                </div>
                                                <div class="removelinkcont"><a href="javascript:void(0)" alt="{{$item_detail['id']}}" class="remove_cart"><i class="fa fa-close"></i></a></div>
                                                <div class="hird0">
                                                    <div class="commentlink"><a href="javascript:void(0)" class="leave_comment" alt="{{$item_detail['id']}}"><i class="fa fa-edit"></i>Comment</a></div>
                                                    <div class="left_main">

                                                        <input type="button" value="-" data-addon="<?php echo $item_detail['addons'] ?>" data-vaiant="<?php echo $item_detail['variant_type'] ?>" class="but_1 counter_number"  id_val="{{$item_detail['id']}}"  alt="minus" /><input readonly="readonly" maxlength="3" type="text" value="{{isset($key_array[$item_detail['id']]) ? $key_array[$item_detail['id']] : 0}}" class='<?php echo "preparation_time_" . $item_detail['id']; ?>' />
                                                        <input type="button" value="+" data-addon="<?php echo $item_detail['addons'] ?>" data-vaiant="<?php echo $item_detail['variant_type'] ?>" class="but_2 counter_number" id_val="{{$item_detail['id']}}"  alt="plus" />
                                                    </div>
                                                    <div class="maininfocontainer">

                                                        <div class="right_main"><strong><?php foreach(Config::get('constant') as $key => $c) { 
                                                                    if($key == $currency)
                                                                        {
                                                                          echo $c; }}?>{{$addonprice}} </strong></div>
                                                    </div>
                                                </div>
                                                <div class="comment-box" <?php if (!isset($item_detail['comment']) OR ( isset($item_detail['comment']) and ! $item_detail['comment'])) { ?>style="display:none"<?php } ?> id="comment-box-{{$item_detail['id']}}" alt="{{$item_detail['id']}}">
                                                    {{Form::textarea('comment', (isset($item_detail['comment'])?$item_detail['comment']:""),  array('class' => 'small-comment', 'alt'=>$item_detail['id']))}}
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php }
                                ?>
                                <div class = "chus">
                                    <div class = "summary">
                                        <strong>Total</strong>
                                        <p><strong data-total="{{$addonprice}}"><?php foreach(Config::get('constant') as $key => $c) { 
                                                                    if($key == $currency)
                                                                        {
                                                                          echo $c; }}?>{{ App::make("HomeController")->numberformat(array_sum($total) ,2)}}</strong></p>
                                    </div>

                                    <?php
                                    $gtotal = array_sum($total);

                                    // check coupon code if added
                                    $coupon = Session::get('coupon');
                                    $coupon_detail = DB::table('coupons')
                                            ->where('code', $coupon)
                                            ->where('start_time', "<=", date('Y-m-d'))
                                            ->where('end_time', ">=", date('Y-m-d'))
                                            ->where('status', '1')
                                            ->first();
                                    if (!empty($coupon_detail)) {
                                        $discount = $gtotal * $coupon_detail->discount / 100;
                                        $gtotal = $gtotal - $discount;
                                        ?>
                                        <div class = "summary">
                                            <strong>Discount ({{$coupon_detail->discount."%"}})</strong>
                                            <p><strong><?php foreach(Config::get('constant') as $key => $c) { 
                                                                    if($key == $currency)
                                                                        {
                                                                          echo $c; }}?>- {{ App::make("HomeController")->numberformat($discount ,2)}}</strong></p>
                                            <input type="hidden" name="discount" value="{{$discount}}" />
                                        </div>
                                        <?php
                                    }

                                    $deliveryInfo = DB::table('pickup_charges')
                                            ->where('user_id', $restro_id)
                                            ->first();
                                    if ($deliveryInfo) {
                                        $delivery = $deliveryInfo->is_default_delivery;
                                    } else {
                                        $delivery = 0;
                                    }
                                    $pickup = Session::get('pickup');
                                    if (isset($pickup) && $pickup == 1) {
                                        $delivery = 0;
                                    }
                                    if ($delivery == '1') {
                                        $deliveryChargeLimit = $deliveryInfo->delivery_charge_limit;
                                        $basicDeliverycharge = $deliveryInfo->normal;
                                        $advanceDeliverycharge = $deliveryInfo->advance;
                                        if ($deliveryChargeLimit > $gtotal) {
                                            $delCharge = $basicDeliverycharge;
                                            ?>
                                            <input type="hidden" name="delivery_charge" value="basic_<?php echo $basicDeliverycharge; ?>" />
                                            <?php
                                        } else {
                                            $delCharge = $advanceDeliverycharge;
                                            ?>
                                            <input type="hidden" name="delivery_charge" value="advance_<?php echo $advanceDeliverycharge; ?>" />
                                        <?php } ?>
                                        <div class="summary devee">
                                            <strong>Delivery Charge <span class="icon" id="delicon"><img src="{{ URL::asset('public/img/front') }}/ques.png" alt="delivery price" /></span></strong>
                                            <p><strong><?php foreach(Config::get('constant') as $key => $c) { 
                                                                    if($key == $currency)
                                                                        {
                                                                          echo $c; }}?><?php echo $delCharge; ?></strong></p>
                                        </div>
                                        <div class="summary devee" style="display:none;" id="del_desc">
                                            <p class="del_contnet">If order amount is less then <b><?php echo $deliveryChargeLimit; ?></b> so Vespa Delivery charge will be applicable or If order amount greater then <b><?php echo $deliveryChargeLimit; ?></b> so car delivery price will be applicable.</p>
                                        </div>

                                        <?php
                                        $gtotal = $gtotal + $delCharge;
                                    }
                                    // echo $gtotal;
                                    ?>
                                    <?php
                                    if ($adminData->is_tax == 1) {
                                        $tax_per = $adminData->tax;
                                        $tax_amount = $tax_per * $gtotal / 100;
                                        $gtotal = $gtotal + $tax_amount;
                                        ?>
                                        <div class = "summary">
                                            <strong>Tax</strong>
                                            <p><strong><?php foreach(Config::get('constant') as $key => $c) { 
                                                                    if($key == $currency)
                                                                        {
                                                                          echo $c; }}?>{{ App::make("HomeController")->numberformat($tax_amount ,2)}}</strong></p>
                                        </div>
                                        <input type="hidden" value="<?php echo $tax_amount; ?>" name="tax" id="taxwf">
                                    <?php } else { ?>
                                        <input type="hidden" name="tax" value="0" id="taxwf">
                                    <?php } ?>       
                                    <div class = "summary total">
                                        <strong>Grand Total</strong>
                                        <p><strong id="gtotal"><?php foreach(Config::get('constant') as $key => $c) { 
                                                                    if($key == $currency)
                                                                        {
                                                                          echo $c; }}?>{{ App::make("HomeController")->numberformat($gtotal ,2)}}</strong></p>

                                        <input type="hidden" value="<?php echo $gtotal; ?>" id="gtotalwf">
                                    </div>
                                </div>
                                <div class="add-others">
                                    <?php
                                    Session::put('carttotal', $gtotal);
//                                 $catererData = DB::table('users')
//                                        ->where('id', $restro_id)
//                                        ->first();
                                    //  print_r($catererData);
                                    ?>
                                    {{html_entity_decode(HTML::link('restaurants/menu/'.$catererData->slug, "Add more items", array('title' => "Add more items",'class'=>'btn btn-primary'))); }}
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="cart_img"><img src="{{ URL::asset('public/img/front') }}/cart_img.png" alt="img" /></div>
                            <div class="cart_txt">Your food basket is empty</div>
                            <div class="add-others centerother">

                                <a title="Add items" href="<?php echo HTTP_PATH; ?>restaurants/list">Add Items</a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
                echo Form::close();
                ?>
            </div>
        </div>

    </div>
</div>
<div class="clear"></div>
<script>

    $('body').on('click', '._cttvvv', function () {
        $('._cttvvv').removeClass('active');
        $(this).addClass('active');
        var datadiv = $(this).attr('data-div');
        var value = $(this).attr('data-value');
        if (datadiv == "creditcard") {
            $('#creditcard').show();
        } else {
            $('#creditcard').hide();
        }
        $('#payment_mode').val(value);
    });

    function checkCard() {

        var cvvCode = document.getElementById('card_cvv').value;

        var cardNumber = document.getElementById('card_number').value;

        var PaymentFullName = document.getElementById('full_name').value;



        if (cardNumber == "") {
            //$('#payloader').hide();
            //alert("<?php //echo __d("view", "Please enter card number", true);              ?>");
            //return false;
        } else {

            if (checkCreditCard(cardNumber, 'Visa')) {
                if (cvvCode == "") {
                    //$('#payloader').hide();
                    alert("<?php echo "Please enter correct CVV"; ?>");
                    return false;
                } else {
                    var result = validateCvvCode();

                    if (result) {
//                                       $('#cdiv').hide();
//                                       $('#loaderpayment').show();
                        if (PaymentFullName == "") {
                            // $('#payloader').hide();
                            alert("<?php echo "Please fill full name"; ?>");
                            return false;
                        }
                        //$('#cdiv').hide();     
                        //$('#payloader').show();     
                        return true;
                    } else {
                        //$('#payloader').hide();
                        alert('<?php echo "Please enter correct CVV"; ?>');
                        return false;
                    }
                }


            } else {
                //$('#payloader').hide();
                alert(ccErrors[ccErrorNo]);
                return false;
            }
        }
    }

    function validateCvvCode() {
        alert();
        //Get the text of the selected card type
        //var cardType = document.getElementById('PaymentCardType').options[document.getElementById('PaymentCardType').selectedIndex].text;
        var cardType = 'Visa';
        // Get the value of the CVV code
        var cvvCode = document.getElementById('card_cvv').value;


        var digits = 0;
        switch (cardType.toUpperCase()) {
            case 'MASTERCARD':
            case 'EUROCARD':
            case 'EUROCARD/MASTERCARD':
            case 'VISA':
            case 'DISCOVER':
                digits = 3;
                break;
            case 'AMEX':
            case 'AMERICANEXPRESS':
            case 'AMERICAN EXPRESS':
                digits = 4;
                break;
            default:
                return false;
        }

        var regExp = new RegExp('[0-9]{' + digits + '}');
        return (cvvCode.length == digits && regExp.test(cvvCode))
    }
</script>    
@stop