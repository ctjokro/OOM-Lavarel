@extends('layout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    $("#myform").validate();
});</script>
<script type="text/javascript">

    function submitchk() {
        var r = confirm("Are you sure want to change order status?");
        if (r == true) {
            return true;
        } else {
            return false;
        }
    }


</script>

<?php
$orderData = DB::table('orders')
        ->where('orders.id', $details->order_id)
        ->first(); // get order details

$customerData = DB::table('users')
        ->select("users.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('areas', 'areas.id', '=', 'users.area')
        ->leftjoin('cities', 'cities.id', '=', 'users.city')
        ->where('users.id', $orderData->user_id)
        ->first(); // get customer details

$catererData = DB::table('users')
        ->select("users.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('areas', 'areas.id', '=', 'users.area')
        ->leftjoin('cities', 'cities.id', '=', 'users.city')
        ->where('users.id', $orderData->caterer_id)
        ->first(); // get caterer details

$deliveryAddress = DB::table('addresses')
        ->select("addresses.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('cities', 'cities.id', '=', 'addresses.city')
        ->leftjoin('areas', 'areas.id', '=', 'addresses.area')
        ->where('addresses.id', $orderData->address_id)
        ->first(); // get cateter details

$cartItems = DB::table('order_item')
        ->whereIn('menu_id', explode(',', $orderData->order_item_id))
        ->where('order_id', $orderData->id)
        ->get(); // get cart menu of this order
?>

<section>
    <div class="top_menus">
        <div class="wrapper">
            @include('elements/left_menu')
            <div class="acc_bar">
                <div class="ad_right">
                    <h2>Welcome!</h2>
                    <h1><?php echo $userData->first_name . ' ' . $userData->last_name; ?></h1>
                </div>
                <div class="acc_setting">
                    @include('elements/top_menu')
                </div> 
                <div class="informetion">
                    <div class="informetion_top">
                        <div class="tatils">Order Details</div>
                        <div class="panel-body panel-body_ful">

                            <div class="form-group">
                                <div class="user-sec">
                                    <div class="user-title">Order Details</div> 

                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Order Number</div>
                                        <div class="user-sec-right"><?php echo $orderData->order_number; ?></div>
                                    </div>
                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Status</div>
                                        <div class="user-sec-right"><?php echo $details->status; ?></div>
                                    </div>
                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Placed Date/Time</div>
                                        <div class="user-sec-right"><?php echo date('d M Y h:i A', strtotime($orderData->created)); ?></div>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="user-sec">
                                    <div class="user-title">Customer Details</div>    
                                    <?php
                                    if (!empty($customerData)) {
                                        ?>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Name</div>
                                            <div class="user-sec-right">{{ $customerData->first_name ? $customerData->first_name.' '.$customerData->last_name:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Contact Number</div>
                                            <div class="user-sec-right">{{ $customerData->contact ? $customerData->contact:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Address</div>
                                            <div class="user-sec-right">{{ $customerData->address ? $customerData->address:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">City</div>
                                            <div class="user-sec-right">{{ $customerData->city_name ? $customerData->city_name:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Area</div>
                                            <div class="user-sec-right">{{ $customerData->area_name ? $customerData->area_name:"N/A"; }}</div>
                                        </div>
                                    <?php } else {
                                        ?>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">No Details Available</div>
                                        </div>
                                    <?php }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="user-sec">
                                    <div class="user-title">Restaurant Details</div>    
                                    <?php
                                    if (!empty($RestaurantData)) {
                                        ?>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Name</div>
                                            <div class="user-sec-right">{{ $catererData->first_name ? $catererData->first_name.' '.$catererData->last_name:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Contact Number</div>
                                            <div class="user-sec-right">{{ $catererData->contact ? $catererData->contact:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Address</div>
                                            <div class="user-sec-right">{{ $catererData->address ? $catererData->address:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">City</div>
                                            <div class="user-sec-right">{{ $catererData->city_name ? $catererData->city_name:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Area</div>
                                            <div class="user-sec-right">{{ $catererData->area_name ? $catererData->area_name:"N/A"; }}</div>
                                        </div>
                                    <?php } else {
                                        ?>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">No Details Available</div>
                                        </div>
                                    <?php }
                                    ?>
                                </div>
                            </div>

                            <?php if (!empty($deliveryAddress)) { ?>
                                <div class="form-group">
                                    <div class="user-sec">
                                        <div class="user-title">Delivery Address Details</div>    

                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Address Title</div>
                                            <div class="user-sec-right">{{ $deliveryAddress->address_title ? $deliveryAddress->address_title:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Address Type</div>
                                            <div class="user-sec-right">{{ $deliveryAddress->address_type ? $deliveryAddress->address_type:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Floor</div>
                                            <div class="user-sec-right">{{ $deliveryAddress->floor ? $deliveryAddress->floor:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Apartment</div>
                                            <div class="user-sec-right">{{ $deliveryAddress->apartment ? $deliveryAddress->apartment:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Building</div>
                                            <div class="user-sec-right">{{ $deliveryAddress->building ? $deliveryAddress->building:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Street Name</div>
                                            <div class="user-sec-right">{{ $deliveryAddress->street_name ? $deliveryAddress->street_name:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Area</div>
                                            <div class="user-sec-right">{{ $deliveryAddress->area_name ? $deliveryAddress->area_name:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">City</div>
                                            <div class="user-sec-right">{{ $deliveryAddress->city_name ? $deliveryAddress->city_name:"N/A"; }}</div>
                                        </div>

                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Phone Number</div>
                                            <div class="user-sec-right">{{ $deliveryAddress->phone_number ? $deliveryAddress->phone_number:"N/A"; }}</div>
                                        </div>



                                    </div>
                                </div>
                            <?php } ?>
                            <?php
                            if ($cartItems) {
                                $total = array();
                                ?>
                                <div class="form-group">
                                    <div class="order-sec user-sec fhth">
                                        <div class="order-title user-title">Items Details</div>
                                        <div class="order-table-sec">
                                            <div class="order-table-head">
                                                <div class="order-table-head-in">Item</div>
                                                <div class="order-table-head-in">Base Price</div>
                                                <div class="order-table-head-in">Quantity</div>
                                                <div class="order-table-head-in">Sub Total</div>
                                            </div>
                                            <?php
                                            foreach ($cartItems as $cartData) {

                                                $menuData = DB::table('menu_item')
                                                                ->where('id', $cartData->menu_id)->first();  // get menu data from menu table

                                                $sub_total = $cartData->base_price * $cartData->quantity;
                                                $total[] = $sub_total;
                                                ?>
                                                <div class="order-table-middel">
                                                    <div class="order-table-middel-in">
                                                        <div class="menucmtilet">
                                                            <?php echo $menuData->item_name; ?>
                                                        </div>
                                                        <?php if (!empty($cartData->submenus)) { ?>
                                                            <div class="menucmt">
                                                                <span class="texlbl"> Sub Menu:</span> <span class="texval"><?php echo $cartData->submenus; ?></span>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="order-table-middel-in"><?php echo App::make("HomeController")->numberformat($cartData->base_price, 2) ; ?></div>
                                                    <div class="order-table-middel-in"><?php echo $cartData->quantity; ?></div>
                                                    <div class="order-table-middel-in"><?php echo App::make("HomeController")->numberformat($sub_total, 2) ; ?></div>
                                                </div>
                                                <?php
                                            }
                                            $gTotal = array_sum($total);
                                            ?>
                                            <div class="order-table-end">
                                                <div class="order-table-end-in" style="border-right:0px;">Total</div>
                                                <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                <div class="order-table-end-in">&nbsp;</div>
                                                <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($gTotal, 2) ; ?></div>
                                            </div>
                                            <?php
                                            if (!empty($orderData->discount)) {
                                                $gTotal = $gTotal - $orderData->discount;
                                                ?>
                                                <div class="order-table-end">
                                                    <div class="order-table-end-in" style="border-right:0px;">Discount</div>
                                                    <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                    <div class="order-table-end-in">&nbsp;</div>
                                                    <div class="order-table-middel-in-g"> - <?php echo App::make("HomeController")->numberformat($orderData->discount, 2) ; ?></div>
                                                </div>
                                            <?php } ?>
                                            <?php
                                            if (!empty($orderData->tax)) {
                                                $gTotal = $gTotal + $orderData->tax;
                                                ?>
                                                <div class="order-table-end">
                                                    <div class="order-table-end-in" style="border-right:0px;">Tax</div>
                                                    <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                    <div class="order-table-end-in">&nbsp;</div>
                                                    <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($orderData->tax, 2) ; ?></div>
                                                </div>
                                            <?php } ?>
                                            <?php
                                            if (!empty($orderData->delivery_charge)) {
                                                $gTotal = $gTotal + $orderData->delivery_charge;
                                                ?>
                                                <div class="order-table-end">
                                                    <div class="order-table-end-in" style="border-right:0px;">Delivery Charge (<?php echo $orderData->delivery_type; ?>)</div>
                                                    <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                    <div class="order-table-end-in">&nbsp;</div>
                                                    <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($orderData->delivery_charge, 2) ; ?></div>
                                                </div>
                                            <?php } ?>
                                            <?php $gTotal = $gTotal; ?>
                                            <div class="order-table-end">
                                                <div class="order-table-end-in" style="border-right:0px;">Grand Total</div>
                                                <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                <div class="order-table-end-in">&nbsp;</div>
                                                <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($gTotal, 2) ; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php
                            if ($details->status == 'Pending') {
                                $adminuser = DB::table('admins')
                                        ->where('id', '1')
                                        ->first();
                                $getAdminTimeDiffMinutes = $adminuser->courier_time;
                                $orderCreatedTime = strtotime($orderData->created);
                                $currDatetime = strtotime(date('Y-m-d H:i:s'));
                                $adminDiffTime = strtotime(date("Y-m-d H:i:s", strtotime("+$getAdminTimeDiffMinutes minutes", $orderCreatedTime)));
                                if ($adminDiffTime >= $currDatetime) {
                                    ?>
                                    <div class="form-group">
                                        <div class="user-sec">
                                            <div class="user-title">Status</div> 

                                            <div class="user-sec-in">
                                                {{ View::make('elements.actionMessage')->render() }}
                                                {{ Form::model($userData, array('url' => '/order/courierview/'.$details->slug, 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}       	
                                                <div class="user-sec-left" style="padding-top:20px;">Status</div>
                                                <div class="user-sec-right in_upt">
                                                    <?php
                                                    global $courierStatus;
                                                    ?>
                                                    {{ Form::select('status',$courierStatus, '', array('class' => 'required form-control','id'=>"selectop")) }}
                                                    <div class="clear"></div><br />

                                                    <div class="in_upt in_upt_res" style="width:100% !important;">
                                                        {{ Form::submit('Submit', array('class' => "btn btn-danger",'onclick'=>"return submitchk(this.form);")) }}
                                                        {{ html_entity_decode(HTML::link(HTTP_PATH.'order/courierorders', "Cancel", array('class' => 'btn btn-default'), true)) }}
                                                    </div>
                                                </div>
                                                <?php
                                                echo Form::close();
                                                ?>


                                            </div>          

                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <div class="chan_pich" style="width: 20%">
                                <a title="Print This Order" class="icon-5 print" href="javascript:void(0)">Print Order</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{ URL::asset('public/js/front/jQuery.print.js') }}"></script>
<script type='text/javascript'>

    $(function () {

        $(".print").on('click', function () {
            //Print ele4 with custom options
            $(".informetion_top").print({
                //Use Global styles
                globalStyles: false,
                //Add link with attrbute media=print
                mediaPrint: false,
                //Custom stylesheet
                stylesheet: "<?php echo HTTP_PATH . "public/css/front/style.css" ?>",
                //Print in a hidden iframe
                iframe: false,
                //Don't print this
                noPrintSelector: ".avoid-this",
            });
        });
        // Fork https://github.com/sathvikp/jQuery.print for the full list of options
    });
</script>

@stop


