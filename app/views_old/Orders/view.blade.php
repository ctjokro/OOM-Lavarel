@extends('layout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    $("#myform").validate({
        submitHandler: function (form) {
            this.checkForm();

            if (this.valid()) { // checks form for validity
                $('#formloader').show();
                this.submit();
            } else {
                return false;
            }
        }
    });
});
</script>
<script language="javascript">

    function printdiv(printpage) {
        var divContents = $("#div_print").html();
        var printWindow = window.open('', '', 'height=400,width=800');
        printWindow.document.write('<html><head><title>DIV Contents</title>');
        printWindow.document.write('<link media="all" type="text/css" rel="stylesheet" href="<?php echo HTTP_PATH; ?>/public/css/front/style.css">');
        printWindow.document.write('</head><body >');
        printWindow.document.write(divContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }
</script>


<?php
$RestaurantData = DB::table('users')
        ->select("users.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('areas', 'areas.id', '=', 'users.area')
        ->leftjoin('cities', 'cities.id', '=', 'users.city')
        ->where('users.id', $orderData->caterer_id)
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
?>
<script type="text/javascript">
    function chkCancel() {
        var r = confirm("Are you sure want to cancel your order?");
        if (r == true) {
            window.location.href = "<?php echo HTTP_PATH . "order/cancelOrder/" . $orderData->slug; ?>";
        } else {
            return false;
        }
    }

    function chkmodifyorders() {
        var r = confirm("Are you sure want to modify your order?");
        if (r == true) {
            window.location.href = "<?php echo HTTP_PATH . "order/modifyorders/" . $RestaurantData->slug . '/' . $orderData->slug; ?>";
        } else {
            return false;
        }
    }
</script>

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
                                {{ View::make('elements.actionMessage')->render() }}
                    <div class="informetion_top">
                        <div class="tatils">Order Details</div>
<!--                        <input name="b_print" type="button" class="ipt"   onClick="printdiv('div_print');" value=" Print ">-->
                        <div class="panel-body panel-body_ful" id="div_print">

                            <div class="form-group">
                                <div class="user-sec">
                                    <div class="user-title">Order Details</div> 



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
                                    if($orderData->pickup_ready == 1){
                                        ?>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Delivery type</div>
                                            <div class="user-sec-right">Pickup
                                            
                                            </div>
                                        </div>    
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Delivery Mode</div>
                                            <div class="user-sec-right"><?php 
                                            if($orderData->pickup_now == 1){
                                                echo "Pickup Now";
                                            }else{
                                                 echo "Pickup Later (".$orderData->pickup_time.')';
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

                            <div class="form-group">
                                <div class="user-sec">
                                    <div class="user-title">Restaurants Details</div>    
                                    <?php
                                    if (!empty($RestaurantData)) {
                                        ?>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Name</div>
                                            <div class="user-sec-right">{{ $RestaurantData->first_name ? $RestaurantData->first_name.' '.$RestaurantData->last_name:"N/A"; }}</div>
                                        </div>
                                        <div class="user-sec-in">
                                            <div class="user-sec-left">Contact Number</div>
                                            <div class="user-sec-right">{{ $RestaurantData->contact ? $RestaurantData->contact:"N/A"; }}</div>
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
                                    <div class="order-sec user-sec">
                                        <div class="order-title user-title">Items Details</div>
                                        <div class="order-table-sec">
                                            <div class="order-table-head">
                                                <div class="order-table-head-in">Item</div>
                                                <div class="order-table-head-in">Base Price</div>
                                                <div class="order-table-head-in">Quantity</div>
                                                <div class="order-table-head-in">Sub Total</div>
                                                <?php if (!empty($cartItems['0']->is_modify)) { ?>
                                                    <div class="order-table-head-in">Modification</div>
                                                <?php } ?>
                                            </div>
                                            <?php
                                            foreach ($cartItems as $cartData) {

                                                $menuData = DB::table('menu_item')
                                                                ->where('id', $cartData->menu_id)->first();  // get menu data from menu table

//                                                $sub_total = $cartData->base_price * $cartData->quantity;
                                              
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
                                                        <?php }
                                                       // print_r($cartData);
                                                         $addonprice = 0;
                                                            if(isset($cartData->variant_id)){
                                                                $explode = explode(',',$cartData->variant_id);
                                                                if($explode){
                                                                    foreach($explode as $explodeVal){

                                                                         $addonV = DB::table('variants')
                                                                           ->where('variants.id', $explodeVal)
                                                                           ->first();
                                                                         if($addonV){
                                                                          $addonprice = $addonprice + $addonV->price;
                                                                          $addonTotal[] = $addonprice;
                                                                         ?> <div class="menucmt"><span class="sumss"><i class="fa fa-tag"></i> Variant ({{$addonV->name}}) </span> <span class="pricev">{{CURR}} <strong>{{$addonV->price}} </strong></span></div><?php
                                                                         }
                                                                    }
                                                                }
                                                            }
                                                            if(isset($cartData->addon_id)){
                                                                $explode = explode(',',$cartData->addon_id);
                                                                if($explode){
                                                                    foreach($explode as $explodeVal){

                                                                         $addonV = DB::table('addons')
                                                                           ->where('addons.id', $explodeVal)
                                                                           ->first();
                                                                         if($addonV){
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
                                                        
                                                    </div>
                                                    <div class="order-table-middel-in"><?php echo App::make("HomeController")->numberformat($addonprice, 2) ; ?></div>
                                                    <div class="order-table-middel-in"><?php echo $cartData->quantity; ?></div>
                                                    <div class="order-table-middel-in"><?php echo App::make("HomeController")->numberformat($addonprice, 2) ; ?></div>
                                                    <?php if (!empty($cartData->is_modify)) {
                                                        ?>
                                                        <div class="order-table-middel-in">
                                                            <?php
                                                            if (!empty($cartData->modification)) {
                                                                echo $cartData->modification;
                                                            } else {
                                                                echo "N/A";
                                                            }
                                                            ?>
                                                        </div>
                                                    <?php } ?>
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
                                              //$ordersc = explode(',',$orderData->order_id);
                                             $minus = $orderData->discount;
                                           //  if(count($ordersc) == 1){
                                                $gTotal = $gTotal - $minus;
                                                ?>
                                                <div class="order-table-end">
                                                    <div class="order-table-end-in" style="border-right:0px;">Discount (-)</div>
                                                    <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                    <div class="order-table-end-in">&nbsp;</div>
                                                    <div class="order-table-middel-in-g"> <?php echo App::make("HomeController")->numberformat($minus, 2) ; ?></div>
                                                </div>
                                         <?php  }  ?>
                                            <?php
                                           if (!empty($orderData->tax)) {
                                              // $ordersc = explode(',',$orderData->order_id);
                                                 $tax = $orderData->tax;
                                                $gTotal = $gTotal + $tax;
                                                
                                                ?>
                                                <div class="order-table-end">
                                                    <div class="order-table-end-in" style="border-right:0px;">Tax</div>
                                                    <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                    <div class="order-table-end-in">&nbsp;</div>
                                                    <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($tax, 2) ; ?></div>
                                                </div>
                                            <?php }  ?>

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
                                            <?php }  ?>
                                            <?php $gTotal = $gTotal; ?>
                                            <?php if($orderData->payby_wallet){?>
                                             <div class="order-table-end">
                                                <div class="order-table-end-in" style="border-right:0px;">Amount By wallet</div>
                                                <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                <div class="order-table-end-in">&nbsp;</div>
                                                <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($orderData->payby_wallet, 2) ; ?></div>
                                            </div>
                                            <?php } ?>
                                            
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
                            if (($orderData->paid == "1") && ($orderData->status == "Pending" || $orderData->status == "Modify" || $orderData->status == "Paid")) {
                                $adminuser = DB::table('admins')
                                        ->where('id', '1')
                                        ->first();
                                $getAdminTimeDiffMinutes = $adminuser->customer_time;
                                $orderCreatedTime = strtotime($orderData->created);
                                $currDatetime = strtotime(date('Y-m-d H:i:s'));
                                $adminDiffTime = strtotime(date("Y-m-d H:i:s", strtotime("+$getAdminTimeDiffMinutes minutes", $orderCreatedTime)));
                                ?>
                                <div class="form-group">
                                    <div class="user-sec">
                                        <div class="user-sec-in">
                                            <form>     

                                                <div class="user-sec-right in_upt">
                                                    <div class="in_upt in_upt_res" style="width:100% !important;">
                                                        <?php if ($adminDiffTime >= $currDatetime) { ?>
                                                            <!--<a href="javascript:void(0);" onclick="chkmodifyorders()" class="btn btn-default">Modify</a>-->
                                                            <a href="javascript:void(0);" onclick="chkCancel()" class="btn btn-default">Cancel</a>
                                                        <?php } else { ?>
                                                            <a href="javascript:void(0);" class="popup-box-seller-contact">Contact Restaurant</a>
                                                            <input type="hidden" name="order_id" id="order_id" value="<?php echo $orderData->id; ?>">
                                                        <?php } ?>
                                                            
                                                            <a title="Print This Order" class="icon-5 print btn btn-primary" href="javascript:void(0)">Print Order</a>
                                                    </div>
                                                </div>
                                            </form>              
                                        </div>          

                                    </div>
                                </div>
                                <?php
                            }
                           
                            if($orderData->status == "Delivered" ){
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
                            <?php 
                                
                            } ?>

                            <div class="chan_pich" style="width: 20%">
                                
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


