<script>
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
</script>
<?php
$adminData = DB::table('admins')
        ->where('id', '1')
        ->first();
?>
 <div class="_newcart">
<h2>Your Food Basket <span>{{$cart->totalItems()}}</span></h2>
<div class="cart_box">
    <div class="showcartloader" style="display:none;"><img src="{{ URL::asset('public/img/front') }}/loader.gif" alt="img" /></div>
    <?php
    // create cart content array
      $total = array();
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
            // get menu details
            $item = DB::table('menu_item')
                    ->leftjoin('users', 'users.id', '=', 'menu_item.user_id')
                    ->where('menu_item.id', $cat['id'])
                    ->select("item_name", "price", "users.first_name", "users.id", "users.last_name", "users.slug")
                    ->first();
            $cat['slug'] = $item->slug;
            $new_cat_array[$item->first_name . " " . $item->last_name][] = $cat;
            $restro_id = $cat['caterer_id'];
        }
        //echo $restro_id; exit;
        ?>
        <!-- ********** Delivery Charge Conditions Start *********** -->
        <?php
        $user_id = Session::get('user_id');

        $session_address_id = Session::get('session_address_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

//        $defaultDeliveryCarge = DB::table('admins')
//                ->where('id', 1)
//                ->first();
//        
//        $isDefaultDeliveryApplicable = $defaultDeliveryCarge->is_default_delivery;
//        $normalDefaultPrice = $defaultDeliveryCarge->normal;
//        $advanceDefaultPrice = $defaultDeliveryCarge->advance;
//        $deliveryChargeLimit = $defaultDeliveryCarge->delivery_charge_limit;
//
//        if (!empty($userData)) {
//            $city_id_session = Session::get('city_id');
//
//            $area_id_session = Session::get('area_id');
//
//            if (!empty($session_address_id) || $city_id_session != '') {
//
//                $addressData = DB::table('addresses')
//                        ->where('id', $session_address_id)
//                        ->first();
//                if (!empty($addressData)) {
//                    $user_city_id = $addressData->city;
//                    $user_area_id = $addressData->area;
//                } else {
//                    $user_city_id = $city_id_session;
//                    $user_area_id = $area_id_session;
//                }
//            } else {
//                $user_city_id = 0;
//                $user_area_id = 0;
//                $delivery = 0;
//            }
//
//                       
//            
//            $catererData = DB::table('users')
//                    ->where('id', $item->id)
//                    ->first();
//            $caterer_city_id = $catererData->city;
//            $caterer_area_id = $catererData->area;
//            if ($isDefaultDeliveryApplicable == 1) {
//                $basicDeliverycharge = $normalDefaultPrice;
//                $advanceDeliverycharge = $advanceDefaultPrice;
//                $delivery = 1;
//            } else {
//                if (!empty($user_city_id) && !empty($user_area_id) && !empty($caterer_city_id) && !empty($caterer_area_id)) {
//
//                    $deliveryCharge = DB::table('delivery_charges')
//                            ->where('from_city_id', $caterer_city_id)
//                            ->where('from_area_id', $caterer_area_id)
//                            ->where('to_city_id', $user_city_id)
//                            ->where('to_area_id', $user_area_id)
//                            ->first();
//
//                    if (!empty($deliveryCharge)) {
//
//                        $delivery = '1';
//                        $basicDeliverycharge = $deliveryCharge->basic_charge;
//                        $advanceDeliverycharge = $deliveryCharge->advance_charge;
//                        $deliveryChargeLimit = $deliveryCharge->delivery_charge_limit;
//                    } else {
//                        $delivery = 0;
//                    }
//                } else {
//                    $delivery = 0;
//                }
//            }
//        } else {
//            $delivery = 0;
//        }
        ?>
        <!-- ********** Delivery Charge Conditions End ************* -->
        <div class="crt_detal">
            <div class="scolc">
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
                                     if(isset($item_detail['variant_type'])){
                                         $explode = explode(',',$item_detail['variant_type']);
                                         if($explode){
                                             foreach($explode as $explodeVal){
                                                   
                                                  $addonV = DB::table('variants')
                                                    ->where('variants.id', $explodeVal)
                                                    ->first();
                                                  if($addonV){
                                                   $addonprice = $addonprice + $addonV->price;
                                                   $addonTotal[] = $addonprice;
                                                  ?><div class="top_p"> <span class="sumss_new"><i class="fa fa-tag"></i> Variant ({{$addonV->name}}) </span> <span class="pricev"><strong>{{$addonV->price}} </strong>{{CURR}}</span></div><?php
                                                  }
                                             }
                                         }
                                     }
                                    //print_r($item_detail); exit;
                                     if(isset($item_detail['addons'])){
                                         $explode = explode(',',$item_detail['addons']);
                                         if($explode){
                                             foreach($explode as $explodeVal){
                                                   
                                                  $addonV = DB::table('addons')
                                                    ->where('addons.id', $explodeVal)
                                                    ->first();
                                                  if($addonV){
                                                   $addonprice = $addonprice + $addonV->addon_price;
                                                   $addonTotal[] = $addonprice;
                                                  ?><div class="top_p"> <span class="sumss_new"><i class="fa fa-tag"></i> Add-on ({{$addonV->addon_name}}) </span> <span class="pricev"><strong>{{$addonV->addon_price}} </strong>{{CURR}}</span></div><?php
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

                                    <input type="button" value="-" data-addon="<?php echo $item_detail['addons'] ?>" data-vaiant="<?php echo $item_detail['variant_type'] ?>" class="but_1 counter_number"  id_val="{{$item_detail['id']}}"  alt="minus" />
                                    <input readonly="readonly" maxlength="3" type="text" value="{{isset($key_array[$item_detail['id']]) ? $key_array[$item_detail['id']] : 0}}" class='<?php echo "preparation_time_" . $item_detail['id']; ?>' />
                                    <input type="button" value="+" data-addon="<?php echo $item_detail['addons'] ?>" data-vaiant="<?php echo $item_detail['variant_type'] ?>" class="but_2 counter_number" id_val="{{$item_detail['id']}}"  alt="plus" />
                                </div>
                                <div class="maininfocontainer">

                                    <div class="right_main"><strong>{{$addonprice}} </strong>{{CURR}}</div>
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
            </div>
            <div class = "chus">

                <div class = "summary">
                    <strong>Total</strong>
                    <p ><strong data-total="{{array_sum($total)}}">{{ App::make("HomeController")->numberformat(array_sum($total) ,2)}}</strong>{{CURR}}</p>
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
                        <p><strong> - {{ App::make("HomeController")->numberformat($discount ,2)}}</strong></p>
                    </div>
                    <input type="hidden" name="discount" value="{{$discount}}" />
                    <?php
                }
                
                $deliveryInfo = DB::table('pickup_charges')
                        ->where('user_id', $restro_id)
                        ->first();
                if($deliveryInfo){
                                    $delivery = $deliveryInfo->is_default_delivery;
                                }else{
                                    $delivery = 0;
                                }
                $pickup = Session::get('pickup');
                if(isset($pickup) && $pickup == 1){
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
                        <p><strong><?php echo $delCharge; ?></strong>{{CURR}}</p>
                    </div>
                    <div class="summary devee" style="display:none;" id="del_desc">
                        <p class="del_contnet">If order amount is less then <b><?php echo $deliveryChargeLimit ; ?></b> so Vespa Delivery charge will be applicable or If order amount greater then <b><?php echo $deliveryChargeLimit ; ?></b> so car delivery price will be applicable.</p>
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
                        <p><strong>{{ App::make("HomeController")->numberformat($tax_amount ,2)}}</strong>{{CURR}}</p>
                    </div>
                    <input type="hidden" value="<?php echo $tax_amount; ?>" name="tax" id="taxwf">
                <?php } else { ?>
                    <input type="hidden" name="tax" value="0" id="taxwf">
                <?php } ?>
                <div class = "summary total">
                    <strong>Grand Total</strong>
                    <p><strong id="gtotal">{{ App::make("HomeController")->numberformat($gtotal ,2)}}</strong><?php echo CURR; ?></p>
                    <input type="hidden" value="<?php echo $gtotal; ?>" id="gtotalwf">
                </div>
            </div>
            <?php
            if ($order) {
                ?>
                <div class="add-others">
                    {{html_entity_decode(HTML::link('restaurants/menu/'.$catererData->slug, "Add more items", array('title' => "Add more items",'class'=>'btn btn-primary'))); }}
                </div>
            <?php } else {
                ?>

                <?php
            }
            ?>
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