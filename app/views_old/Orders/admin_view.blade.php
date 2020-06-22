@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Order Details')
@extends('layouts/adminlayout')
@section('content')


<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    $.validator.addMethod("pass", function (value, element) {
        return  this.optional(element) || (/.{8,}/.test(value) && /([0-9].*[a-z])|([a-z].*[0-9])/.test(value));
    }, "Password minimum length must be 8 characters and contain at least 1 number.");
    $.validator.addMethod("contact", function (value, element) {
        return  this.optional(element) || (/^[0-9-]+$/.test(value));
    }, "Contact Number is not valid.");
    $("#adminAdd").validate();
});
</script>
<?php
$customerData = DB::table('users')
        ->select("users.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('areas', 'areas.id', '=', 'users.area')
        ->leftjoin('cities', 'cities.id', '=', 'users.city')
        ->where('users.id', $detail->user_id)
        ->first(); // get customer details




$RestaurantData = DB::table('users')
        ->select("users.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('areas', 'areas.id', '=', 'users.area')
        ->leftjoin('cities', 'cities.id', '=', 'users.city')
        ->where('users.id', $detail->caterer_id)
        ->first(); // get customer details

$deliveryAddress = DB::table('addresses')
        ->select("addresses.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('cities', 'cities.id', '=', 'addresses.city')
        ->leftjoin('areas', 'areas.id', '=', 'addresses.area')
        ->where('addresses.id', $detail->address_id)
        ->first(); // get cateter details

$cartItems = DB::table('order_item')
        ->whereIn('menu_id', explode(',', $detail->order_item_id))
        ->where('order_id', $detail->id)
        ->get(); // get cart menu of this order
?>
<script type="text/javascript">

    function chkCancel() {
        var r = confirm("Are you sure want to cancel your order?");
        if (r == true) {
            window.location.href = "<?php echo HTTP_PATH . "order/cancelOrder/" . $detail->slug; ?>";
        } else {
            return false;
        }
    }

    function submitchk() {
        var r = confirm("Are you sure want to change order status?");
        if (r == true) {
            return true;
        } else {
            return false;
        }
    }


    $(document).ready(function () {

        $('#selectop').change(function () {

            var val = $('#selectop').val();

            if (val == 'Cancel') {

                $('#modifystatus').hide();
                $('#cancelstatus').show();
            } else if (val == 'Modify') {
                $('#cancelstatus').hide();
                $('#modifystatus').show();
            } else {
                $('#cancelstatus').hide();
                $('#modifystatus').hide();
            }
        })
    });


</script>
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <ul id="breadcrumb" class="breadcrumb">
                    <li>
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/admindashboard', '<i class="fa fa-dashboard"></i> Dashboard', array('id' => ''), true)) }}
                    </li>
                    <li>
                        <i class="fa fa-tasks"></i> 
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/order/admin_index', "Orders", array('id' => ''), true)) }}
                    </li>
                    <li class="active">Order Details</li>
                </ul>

                <section class="panel">


                    <div class="panel-body panel-body_ful">

                        {{ View::make('elements.actionMessage')->render() }}
                        <div class="form-group">
                            <div class="user-sec">
                                <div class="user-title">Order Details</div> 


                                <div class="user-sec-in">
                                    <div class="user-sec-left">Order Number</div>
                                    <div class="user-sec-right">{{ $detail->order_number }}</div>
                                </div>
                                <div class="user-sec-in">
                                    <div class="user-sec-left">Order Status</div>
                                    <div class="user-sec-right">{{ $detail->status; }}</div>
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
                                        <div class="user-sec-left">First Name</div>
                                        <div class="user-sec-right">{{ $customerData->first_name ? $customerData->first_name:"N/A"; }}</div>
                                    </div>
                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Last Name</div>
                                        <div class="user-sec-right">{{ $customerData->last_name ? $customerData->last_name:"N/A"; }}</div>
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
                                        <div class="user-sec-left">Restaurant Name</div>
                                        <div class="user-sec-right">{{ $RestaurantData->first_name ? $RestaurantData->first_name:"N/A"; }}</div>
                                    </div>
                                    <!--                                    <div class="user-sec-in">
                                                                            <div class="user-sec-left">Last Name</div>
                                                                            <div class="user-sec-right">{{ $RestaurantData->last_name ? $RestaurantData->last_name:"N/A"; }}</div>
                                                                        </div>-->
                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Contact Number</div>
                                        <div class="user-sec-right">{{ $RestaurantData->contact ? $RestaurantData->contact:"N/A"; }}</div>
                                    </div>
                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Address</div>
                                        <div class="user-sec-right">{{ $RestaurantData->address ? $RestaurantData->address:"N/A"; }}</div>
                                    </div>
                                    <div class="user-sec-in">
                                        <div class="user-sec-left">City</div>
                                        <div class="user-sec-right">{{ $RestaurantData->city_name ? $RestaurantData->city_name:"N/A"; }}</div>
                                    </div>
                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Area</div>
                                        <div class="user-sec-right">{{ $RestaurantData->area_name ? $RestaurantData->area_name:"N/A"; }}</div>
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
                            <div class="form-group order-sec user-sec">
                                <div class="order-title user-title">Order Details</div>
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
//                                        $sub_total = $cartData->base_price * $cartData->quantity;
//                                        $total[] = $sub_total;
                                        ?>
                                        <div class="order-table-middel">
                                            <div class="order-table-middel-in">
                                                <?php echo $menuData->item_name; ?>
                                                <?php if (!empty($cartData->submenus)) { ?>
                                                    <div class="menucmt">
                                                        <span class="texlbl"> Sub Menu:</span> <span class="texval"><?php echo $cartData->submenus; ?></span>
                                                    </div>
                                                    <?php
                                                }

                                                // print_r($cartData);
                                                $addonprice = 0;
                                                if (isset($cartData->variant_id)) {
                                                    $explode = explode(',', $cartData->variant_id);
                                                    if ($explode) {
                                                        foreach ($explode as $explodeVal) {

                                                            $addonV = DB::table('variants')
                                                                    ->where('variants.id', $explodeVal)
                                                                    ->first();
                                                            if ($addonV) {
                                                                $addonprice = $addonprice + $addonV->price;
                                                                $addonTotal[] = $addonprice;
                                                                ?> <div class="menucmt"><span class="sumss"><i class="fa fa-tag"></i> Variant ({{$addonV->name}}) </span> <span class="pricev">{{CURR}} <strong>{{$addonV->price}} </strong></span></div><?php
                                                            }
                                                        }
                                                    }
                                                }
                                                if (isset($cartData->addon_id)) {
                                                    $explode = explode(',', $cartData->addon_id);
                                                    if ($explode) {
                                                        foreach ($explode as $explodeVal) {

                                                            $addonV = DB::table('addons')
                                                                    ->where('addons.id', $explodeVal)
                                                                    ->first();
                                                            if ($addonV) {
                                                                $addonprice = $addonprice + $addonV->addon_price;
                                                                $addonTotal[] = $addonprice;
                                                                ?> <div class="menucmt"><span class="sumss"><i class="fa fa-tag"></i> Add-on ({{$addonV->addon_name}}) </span> <span class="pricev">{{CURR}} <strong>{{$addonV->addon_price}} </strong></span></div><?php
                                                            }
                                                        }
                                                    }
                                                }
                                                $addonprice = $addonprice * $cartData->quantity;
                                                $total[] = $addonprice;
                                                ?>
                                                <?php if (!empty($cartData->comment)) { ?>
                                                    <div class="menucmt">
                                                        <span class="texlbl"> Comment:</span> <span class="texval"><?php echo $cartData->comment; ?></span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="order-table-middel-in"><?php echo App::make("HomeController")->numberformat($addonprice, 2); ?></div>
                                            <div class="order-table-middel-in"><?php echo $cartData->quantity; ?></div>
                                            <div class="order-table-middel-in"><?php echo App::make("HomeController")->numberformat($addonprice, 2); ?></div>
                                        </div>
                                        <?php
                                    }
                                    $gTotal = array_sum($total);
                                    ?>
                                    <div class="order-table-end">
                                        <div class="order-table-end-in" style="border-right:0px;">Total</div>
                                        <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                        <div class="order-table-end-in">&nbsp;</div>
                                        <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($gTotal, 2); ?></div>
                                    </div>
                                    <?php
                                    if (!empty($orderData->discount)) {
                                        //  $ordersc = explode(',',$orderData->order_id);
                                        $minus = $orderData->discount;
                                        $gTotal = $gTotal - $minus;
                                        ?>
                                        <div class="order-table-end">
                                            <div class="order-table-end-in" style="border-right:0px;">Discount (-)</div>
                                            <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                            <div class="order-table-end-in">&nbsp;</div>
                                            <div class="order-table-middel-in-g"> <?php echo App::make("HomeController")->numberformat($minus, 2); ?></div>
                                        </div>
                                    <?php } ?>
                                    <?php
                                    if (!empty($orderData->tax)) {
                                        //   $ordersc = explode(',',$orderData->order_id);
                                        $tax = $orderData->tax;
                                        $gTotal = $gTotal + $tax;
                                        ?>
                                        <div class="order-table-end">
                                            <div class="order-table-end-in" style="border-right:0px;">Tax</div>
                                            <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                            <div class="order-table-end-in">&nbsp;</div>
                                            <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($tax, 2); ?></div>
                                        </div>
                                    <?php } ?>


                                    <div class="order-table-end">
                                        <div class="order-table-end-in" style="border-right:0px;">Delivery Charge (<?php echo $detail->delivery_type; ?>) </div>
                                        <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                        <div class="order-table-end-in">&nbsp;</div>
                                        <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($detail->delivery_charge, 2); ?></div>
                                    </div>
                                    <?php $gTotal = $gTotal + $detail->delivery_charge; ?>
                                    <?php
                                    $adminData = DB::table('admins')
                                            ->where('id', '1')
                                            ->first();
                                    if ($adminData->is_commission == 1) {
                                        $comm_per = $adminData->commission;
                                        $tax_amount = $comm_per * $gTotal / 100;
                                        $gTotal = $gTotal - $tax_amount;
                                        ?>
                                        <div class="order-table-end">
                                            <div class="order-table-end-in" style="border-right:0px;">Admin Commission (-)</div>
                                            <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                            <div class="order-table-end-in">&nbsp;</div>
                                            <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($tax_amount, 2); ?></div>
                                        </div>
                                    <?php } ?>
                                    <div class="order-table-end">
                                        <div class="order-table-end-in" style="border-right:0px;">Grand Total</div>
                                        <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                        <div class="order-table-end-in">&nbsp;</div>
                                        <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($gTotal, 2); ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                        if ($detail->status != "Delivered") {
                            ?>
                            <div class="form-group">
                                <div class="user-sec">
                                    <div class="user-title">Status</div> 

                                    <div class="user-sec-in">
                                        {{ Form::model(array(), array('url' => '/admin/order/view/'.$detail->slug, 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}       	
                                        <div class="user-sec-left">Status</div>

                                        <div class="user-sec-right in_upt">
                                            <?php // if ($detail->status == "Modify" || $detail->status == "Pending" || $detail->status == "Confirm") {  ?>
                                            <?php
                                            //|| $detail->status == "Confirm"
                                            if ($detail->status == "Pending" ) { ?>
                                                <?php
                                                global $adminStatus;
                                                ?>
                                                {{ Form::select('status', $adminStatus, '', array('class' => 'required form-control','id'=>"selectop")) }}
                                                <div class="clear"></div><br />
                                                <div class="secstatus" id="modifystatus" style="display:none;">
                                                    <div class="order-sec user-sec fert" style="margin-bottom: 10px">
                                                        <div class="order-title user-title">Items Details</div>
                                                        <div class="order-table-sec djrw">
                                                            <div class="order-table-head djrw2">
                                                                <div class="order-table-head-in">Item</div>
                                                                <div class="order-table-head-in">Comments</div>
                                                            </div>
                                                            <?php
                                                            $i = 1;
                                                            foreach ($cartItems as $cartData) {
                                                                $menuData = DB::table('menu_item')
                                                                                ->where('id', $cartData->menu_id)->first();  // get menu data from menu table
                                                                ?>
                                                                <div class="order-table-middel djrw3">
                                                                    <div class="order-table-middel-in djrw_item"><?php echo $menuData->item_name; ?></div>
                                                                    <div class="order-table-middel-in djrw_comments">
                                                                        <textarea type="textarea" name="modfiy[<?php echo $i; ?>][comment]"></textarea>
                                                                        <input type="hidden" name="modfiy[<?php echo $i; ?>][id]" value="<?php echo $cartData->id; ?>">
                                                                    </div>
                                                                </div>
                                                                <?php
                                                                $i++;
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="secstatus" id="cancelstatus" style="display:none;">
                                                    <div class="form_group  col-lg-12 ">
                                                        {{ HTML::decode(Form::label('reason', "Reason <span class='require'>*</span>",array('class'=>" col-lg-1"))) }}
                                                        <div class="in_upt  col-lg-10">
                                                            {{ Form::textarea('reason', Input::old('reason'), array('class' => 'required form-control')) }}
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="in_upt in_upt_res" style="width:100% !important;">
                                                    {{ Form::submit('Submit', array('class' => "btn btn-danger",'onclick'=>"return submitchk(this.form);")) }}
                                                    {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/order/admin_index', "Cancel", array('class' => 'btn btn-default'), true)) }}
                                                </div>
                                            <?php } else { ?>
                                                <div class='stawe'>{{ $detail->status }}</div>
                                            <?php } ?>
                                        </div>

                                        <?php
                                        echo Form::close();
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } 
                        ?>
                    </div>
                </section>
            </div>

        </div>
    </section>
</section>
@stop