@extends('layout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#myform").validate();
    });</script>
<script type="text/javascript">

    function chkCancel() {
        var r = confirm("Are you sure want to cancel your order?");
        if (r == true) {
            window.location.href = "<?php echo HTTP_PATH . "order/cancelOrder/" . $orderData->slug; ?>";
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
                $('#commentstatus').hide();
                $('#confirmstatus').hide();
                $('#assignstatus').hide();
                
//            } else if (val != 'Cancel' && val != 'Delivered' && val != 'Confirm') {
//                $('#cancelstatus').hide();
//                $('#modifystatus').hide();
//                $('#commentstatus').show();
//                $('#confirmstatus').hide();
//                $('#assignstatus').hide();

            } else if (val == 'Modify') {
                $('#cancelstatus').hide();
                $('#modifystatus').show();
                $('#commentstatus').hide();
                $('#assignstatus').hide();

            }  else if (val == 'Confirm') {
                $('#cancelstatus').hide();
                $('#modifystatus').hide();
                $('#commentstatus').hide();
                $('#confirmstatus').show();
                $('#assignstatus').hide();

            }else if (val == 'Assign To Delivery') {
                $('#cancelstatus').hide();
                $('#modifystatus').hide();
                $('#commentstatus').hide();
                $('#confirmstatus').hide();
                $('#assignstatus').show();

            }else {
                $('#cancelstatus').hide();
                $('#modifystatus').hide();
                $('#commentstatus').hide();
                $('#confirmstatus').hide();
                $('#assignstatus').hide();

            }
        })
    });


</script>

<?php
$adminData = DB::table('admins')
        ->where('id', '1')
        ->first();

$RestaurantData = DB::table('users')
        ->select("users.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('areas', 'areas.id', '=', 'users.area')
        ->leftjoin('cities', 'cities.id', '=', 'users.city')
        ->where('users.id', $orderData->caterer_id)
        ->first(); // get cateter details

$customerData = DB::table('users')
        ->select("users.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('areas', 'areas.id', '=', 'users.area')
        ->leftjoin('cities', 'cities.id', '=', 'users.city')
        ->where('users.id', $orderData->user_id)
        ->first(); // get cateter details

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

$adminuser = DB::table('admins')
        ->where('id', '1')
        ->first();
$getAdminTimeDiffMinutes = $adminuser->caterer_time;
$orderCreatedTime = strtotime($orderData->created);
$currDatetime = strtotime(date('Y-m-d H:i:s'));
$adminDiffTime = strtotime(date("Y-m-d H:i:s", strtotime("+$getAdminTimeDiffMinutes minutes", $orderCreatedTime)));
?>

<section>
    <div class="top_menus">
        <div class="dash_toppart">
            <div class="wrapper"> 
                <div class="_cttv">
                    @include('elements/left_menu')


                </div></div></div>
        <div class="wrapper">

            <div class="acc_bar acc_bar_new">
                @include('elements/oderc_menu')
                <div class="informetion informetion_new">
                    <div class="informetion_top">
                        <div class="tatils">Order Details</div>
                        <div class="panel-body panel-body_ful">

                            <div class="form-group">
                                <div class="user-sec">
                                    <div class="user-title">Order Details</div> 

                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Customer Name</div>
                                        <div class="user-sec-right"><?php echo $customerData->first_name . ' ' . $customerData->last_name; ?></div>
                                    </div>
                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Contact Number</div>
                                        <div class="user-sec-right"><?php echo $customerData->contact; ?></div>
                                    </div>
                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Order Number</div>
                                        <div class="user-sec-right"><?php echo $orderData->order_number; ?></div>
                                    </div>
                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Status</div>
                                        <div class="user-sec-right"><?php echo $orderData->status; ?></div>
                                    </div>
                                    <div class="user-sec-in">
                                        <div class="user-sec-left">Placed Date/Time</div>
                                        <div class="user-sec-right"><?php echo date('d M Y h:i A', strtotime($orderData->created)); ?></div>
                                    </div>
                                    <?php
                                    if ($orderData->pickup_ready == 1) {
                                        ?>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Delivery type</div>
                                            <div class="user-sec-right">Pickup

                                            </div>
                                        </div>    
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Delivery Mode</div>
                                            <div class="user-sec-right"><?php
                                    if ($orderData->pickup_now == 1) {
                                        echo "Pickup Now";
                                    } else {
                                        echo "Pickup Later (" . $orderData->pickup_time . ')';
                                    }
                                    if ($adminDiffTime >= $currDatetime) {
                                        if ($orderData->status == "Confirm") {
                                                ?>{{ html_entity_decode(HTML::link(HTTP_PATH.'user/notify/'.$orderData->slug,' <span class="btn btn-primary">Notify to customer</span>', ['class'=>'link-menu_mcbbb'])); }}<?php //
                                        }
                                    }
                                        ?></div>
                                        </div>    
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Pickup address</div>
                                            <div class="user-sec-right">{{$RestaurantData->address}}, {{$RestaurantData->area_name}}</div>
                                        </div>    
                                        <?php
                                    }
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
                                    <div class="order-sec user-sec">
                                        <div class="order-title user-title saw">Items Details</div>
                                        <div class="ored_ur">
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
//                                                    $sub_total = $cartData->base_price * $cartData->quantity;
//                                                    $total[] = $sub_total;
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
                                                            <?php /* if (!empty($menuData->preparation_time)) { ?>
                                                              <div class="menucmtrpepar">
                                                              <span class="texlbl"> Preparation Time:</span> <span class="texval"><?php echo $menuData->preparation_time . ' Hours'; ?></span>
                                                              </div>
                                                              <?php } */ ?>

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
                                                    //$ordersc = explode(',',$orderData->order_id);
                                                    $minus = $orderData->discount;
                                                    // if(count($ordersc) == 1){
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
                                                    //  $ordersc = explode(',',$orderData->order_id);
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

                                                <?php
                                                if (!empty($orderData->delivery_charge)) {
                                                    $gTotal = $gTotal + $orderData->delivery_charge;
                                                    ?>
                                                    <div class="order-table-end">
                                                        <div class="order-table-end-in" style="border-right:0px;">Delivery Charge (<?php echo $orderData->delivery_type; ?>)</div>
                                                        <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                        <div class="order-table-end-in">&nbsp;</div>
                                                        <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($orderData->delivery_charge, 2); ?></div>
                                                    </div>
                                                <?php } ?>
                                                <?php /*
                                                  if ($adminData->is_commission == 1) {
                                                  $comm_per = $adminData->commission;
                                                  $tax_amount = $comm_per * $gTotal / 100;
                                                  $gTotal = $gTotal - $tax_amount;
                                                  ?>
                                                  <div class="order-table-end">
                                                  <div class="order-table-end-in" style="border-right:0px;">Admin Commission (-)</div>
                                                  <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                  <div class="order-table-end-in">&nbsp;</div>
                                                  <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($tax_amount, 2) ; ?></div>
                                                  </div>
                                                  <?php } */ ?>
                                                <?php $gTotal = $gTotal; ?>
                                                <div class="order-table-end">
                                                    <div class="order-table-end-in" style="border-right:0px;">Grand Total</div>
                                                    <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                    <div class="order-table-end-in">&nbsp;</div>
                                                    <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($gTotal, 2); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php
//  if ($orderData->status != "Confirm") {


                            if ($adminDiffTime >= $currDatetime) {
                                ?>
                                <div class="form-group">

                                    <div class="user-sec">
                                        <div class="user-title">Status</div> 
                                        <?php if (($orderData->status == "Pending" || $orderData->status == "Confirm" || $orderData->status == "Paid") || ($orderData->status != "Cancel" && $orderData->status != "Delivered")) { ?>
                                            <div class="user-sec-in">
                                                <div class="user-sec-left">Current Status</div>

                                                <div class="user-sec-right in_upt">
                                                    <?php echo $orderData->status; ?>
                                                </div>
                                                <?php
                                                if (!empty($orderData->kitchen_staff_id)) {
                                                    $kitchenStaffInfo = DB::table('users')
                                                            ->where('id', $orderData->kitchen_staff_id)
                                                            ->first();
                                                    //print_r($kitchenStaffInfo);
                                                    ?>
                                                    <div class="user-sec-left">Kitchen Staff</div>
                                                    <div class="user-sec-right in_upt">
                                                        <?php echo ucfirst($kitchenStaffInfo->first_name . " " . $kitchenStaffInfo->last_name); ?>
                                                    </div>
                                                <?php } ?>
                                                <?php
                                                if (!empty($orderData->delivery_person_id)) {
                                                    $deliveryPersonInfo = DB::table('users')
                                                            ->where('id', $orderData->delivery_person_id)
                                                            ->first();
                                                    //print_r($kitchenStaffInfo);
                                                    ?>
                                                    <div class="user-sec-left">Delivery Person</div>
                                                    <div class="user-sec-right in_upt">
                                                        <?php echo ucfirst($deliveryPersonInfo->first_name . " " . $deliveryPersonInfo->last_name); ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                        <!--                                            <div class="user-sec-in">
                                                                                        <div class="user-sec-left">Order status</div>
                                                                                        
                                                                                        <div class="user-sec-right in_upt">
                                        <?php //echo $orderData->paid?"Paid":"Payment is not successfull.";   ?>
                                                                                        </div>
                                                                                    </div>-->
                                        <div class="user-sec-in">




                                            {{ View::make('elements.actionMessage')->render() }}
                                            {{ Form::model($userData, array('url' => '/order/receivedview/'.$orderData->slug, 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}       	
                                            <div class="user-sec-left"><?php $orderData->status == "Delivered" ? "" : "Next Status";  ?></div>

                                            <div class="user-sec-right in_upt">
                                                <?php
                                                //print_r($orderData); exit;
                                                //if (($orderData->paid == 1) && (($orderData->status == "Pending" || $orderData->status == "Confirm" || $orderData->status == "Paid") || ($orderData->status != "Cancel" && $orderData->status != "Delivered"))) {
                                                if ((( $orderData->status == "Confirm" || $orderData->status == "Paid") || ($orderData->status != "Cancel" && $orderData->status != "Delivered"))) {
                                                    ?>

                                                    <?php
                                                    $kitchStaffArray = array(
                                                        '' => 'Please Select'
                                                    );
                                                    $kitchenStaffs = User::orderBy('first_name', 'asc')->where('status', "=", "1")->where('user_type', "=", 'KitchenStaff')->where('restaurant_id', "=", $userData->id)->get();
                                                    if (!empty($kitchenStaffs)) {
                                                        foreach ($kitchenStaffs as $kitchenStaff)
                                                            $kitchStaffArray[$kitchenStaff->id] = ucfirst($kitchenStaff->first_name . " " . $kitchenStaff->last_name);
                                                    }

                                                    //echo '<pre>';print_r($kitchenStaffs);
                                                    $totalKS = sizeof($kitchenStaffs);


                                                    $deliveryPersonArray = array(
                                                        '' => 'Please Select'
                                                    );
                                                    $deliveryPersons = User::orderBy('first_name', 'asc')->where('status', "=", "1")->where('is_busy', "=", "0")->where('user_type', "=", 'DeliveryPerson')->where('restaurant_id', "=", $userData->id)->get();
                                                    if (!empty($deliveryPersons)) {
                                                        foreach ($deliveryPersons as $deliveryPerson)
                                                            $deliveryPersonArray[$deliveryPerson->id] = ucfirst($deliveryPerson->first_name . " " . $deliveryPerson->last_name);
                                                    }

                                                   $totalDP = sizeof($deliveryPersons);
                                                    ?>

                                                    <?php
                                                    global $adminStatus;
                                                    $statusArray = array(
                                                        '' => 'Please Select'
                                                    );

                                                    $orderstatus = Orderstatus::orderBy('status_name', 'asc')->where('status', "=", "1")->where('user_id', "=", $userData->id)->lists('status_name', 'id');
                                                    //  echo '<pre>';print_r($orderstatus);die;
                                                    //$orderstatus = array_combine($orderstatus, $orderstatus);

                                                    if (!empty($orderstatus)) {
                                                        foreach ($orderstatus as $key => $val)
                                                            $statusArray[ucfirst($val)] = ucfirst($val);
                                                    }


                                                    if (!empty($adminStatus)) {
                                                        foreach ($adminStatus as $key => $val)
                                                            $statusArray[$key] = $val;
                                                    }

                                                    // echo '<pre>';print_r($statusArray);


                                                    if (in_array($orderData->status, $statusArray)) {
                                                        unset($statusArray[$orderData->status]);
                                                    }
                                                    if ($orderData->status != "Pending" && $orderData->status != "Paid") {
                                                        unset($statusArray['Confirm']);
                                                    }
                                                    
                                                    if($orderData->delivery_type == "Pickup"){
                                                        unset($statusArray['Assign To Delivery']);
                                                        unset($statusArray['On Delivery']); 
                                                    }
                                                        
                                                        
                                                        
                                                    ?>
                                                    {{ Form::select('status',$statusArray, $orderData->status, array('class' => 'required form-control','id'=>"selectop")) }}
                                                    <div class="clear"></div><br />



                                                    <?php //if (!empty($totalDP)) { ?>
                                                        <div class="secstatus" id="assignstatus" style="display:none;">
                                                            <div class="form_group col-lg-12">
                                                                {{ HTML::decode(Form::label('delivery_person_id', "Delivery Person <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                                                <div class="in_upt col-lg-12">
                                                                    {{ Form::select('delivery_person_id',$deliveryPersonArray, $orderData->delivery_person_id, array('class' => 'required form-control','id'=>"deliveryperson")) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php //} ?>

                                                    <?php if (!empty($totalKS)) { ?>
                                                        <div class="secstatus" id="confirmstatus" style="display:none;">
                                                            <div class="form_group col-lg-12">
                                                                {{ HTML::decode(Form::label('kitchen_staff_id', "Kitchen Staff <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                                                <div class="in_upt col-lg-12">
                                                                    {{ Form::select('kitchen_staff_id',$kitchStaffArray, $orderData->kitchen_staff_id, array('class' => 'required form-control','id'=>"kitchenstaff")) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

                                                    <div class="secstatus" id="modifystatus" style="display:none;">
                                                        <div class="order-sec user-sec fert">
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
                                                        <div class="form_group col-lg-12">
                                                            {{ HTML::decode(Form::label('reason', "Reason <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                                            <div class="in_upt col-lg-12">
                                                                {{ Form::textarea('reason', Input::old('reason'), array('class' => 'required form-control')) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="secstatus" id="commentstatus_" style="display:none;">
                                                        <div class="form_group  col-lg-12 ">
                                                            {{ HTML::decode(Form::label('reason', "Note <span class='require'>*</span>",array('class'=>" col-lg-1"))) }}
                                                            <div class="in_upt  col-lg-10">
                                                                {{ Form::textarea('comment', Input::old('comment'), array('class' => 'required form-control')) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="in_upt in_upt_res" style="width:100% !important;">
                                                        {{ Form::submit('Submit', array('class' => "btn btn-primary",'onclick'=>"return submitchk(this.form);")) }}
                                                        {{ html_entity_decode(HTML::link(HTTP_PATH.'order/receivedorders', "Cancel", array('class' => 'btn btn-default'), true)) }}
                                                    </div>
                                                <?php } else { ?>
                                                    <div class='stawe'>{{ $orderData->status }}</div>
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
                            //  }
                            ?>


                            <div class="form-group">
                                <div class="user-sec">
                                    <div class="user-sec-in">
                                        <form>     

                                            <div class="user-sec-right in_upt">
                                                <div class="in_upt in_upt_res" style="width:100% !important;">
                                                    <a title="Print This Order" class="icon-5 print btn btn-primary" href="javascript:void(0)">Print Order</a>
                                                </div>
                                            </div>
                                        </form>              
                                    </div>          

                                </div>
                            </div>
                            <div class="chan_pich" style="width: 20%">
                                <!--                                <a title="Print This Order" class="icon-5 print" href="javascript:void(0)">Print Order</a>-->
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


