<script type="text/javascript">
    $(document).ready(function() {
        $('.showhide').click(function() {
            $(".slidediv").slideToggle();
        });
    });
</script>
<?php
// get user type
$data = DB::table('users')
        ->select("user_type")
        ->where('id', Session::get("user_id"))
        ->first();
 $type = $data->user_type;

?>
<div class="ste_menu">
    <div class="my_menr">
        <a class="showhide" href="javascript:void(0)">
            <img src="{{ URL::asset('public/img/front') }}/menu_device_icon.png" alt="img" />
            <span>My Dashboard Menu</span>
        </a>
    </div>
    <ul class="slidediv">
        <li class="{{ (Request::is('user/myaccount') OR Request::is('user/editProfile')) ? 'active' : '' }}">
            <?php echo html_entity_decode(HTML::link('user/myaccount', '<i class="fa fa-user"></i> My Profile', array('class' => 'icon-1', 'title' => 'My Profile'))); ?>
        </li>
        <li  class="{{ (Request::is('user/myfavourite')) ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/myfavourite', '<i class="fa fa-heart"></i> My Favorite', array('class' => 'icon-2', 'title' => 'My Favorites'))); ?>
            </li>
        <?php
        if ($type == 'Customer') {
            ?>
            <li  class="{{ (Request::is('order/myorders') or Request::is('order/view/*')) ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('order/myorders', '<i class="fa fa-list-alt"></i> My Orders', array('class' => 'icon-2', 'title' => 'My Orders'))); ?>
            </li>
            <li  class="{{ (Request::is('order/favorders')) ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('order/favorders', '<i class="fa fa-heart"></i> Favourite Orders', array('class' => 'icon-2', 'title' => 'Favourite Orders'))); ?>
            </li>
            <?php
        } if ($type == 'Restaurant') {
            ?>
            <li  class="{{ (Request::is('order/receivedorders') or Request::is('order/receivedview/*')) ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('order/receivedorders', '<i class="fa fa-list-alt"></i> Received Orders', array('class' => 'icon-2', 'title' => 'My Orders'))); ?>
            </li>
            <li  class="{{ (Request::is('user/deliverycharges') or Request::is('user/deliverycharges/*')) ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/deliverycharges', '<i class="fa fa-truck"></i> Delivery charges', array('class' => 'icon-2', 'title' => 'Delivery charges'))); ?>
            </li>
            <?php
        } if ($type == 'Courier') {
            ?>
            <li  class="{{ (Request::is('order/courierorders') or Request::is('order/courierview/*')) ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('order/courierorders', '<i class="fa fa-list-alt"></i> Courier Orders', array('class' => 'icon-2', 'title' => 'Courier Orders'))); ?>
            </li>
                <?php
            }
            if ($type == 'Customer') {
                ?>
            <li  class="{{ (Request::is('user/manageaddresses') OR Request::is('user/addaddress') OR Request::is('user/editaddress*')) ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/manageaddresses', '<i class="fa fa-map-marker"></i> Manage Addresses', array('class' => 'icon-5', 'title' => 'Manage Addresses'))); ?>
            </li>
            <?php
        } else if ($type == 'Restaurant') {
            ?>
            <li class="{{ (Request::is('user/kitchenstaff') or Request::is('user/addkitchenstaff') or Request::is('user/editkitchenstaff/*')) ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/kitchenstaff', '<i class="fa fa-user"></i> Kitchen Staff', array('class' => 'icon-5', 'title' => 'Manage Kitchen Staff'))); ?>
            </li>
            <li class="{{ (Request::is('user/deliveryperson')  or Request::is('user/adddeliveryperson') or Request::is('user/editdeliveryperson/*')) ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/deliveryperson', '<i class="fa fa-motorcycle"></i> Delivery Person', array('class' => 'icon-5', 'title' => 'Manage Delivery Person'))); ?>
            </li>
            <li class="{{ Request::is('user/openinghours') ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/openinghours', '<i class="fa fa-clock-o"></i> Opening Hours', array('class' => 'icon-5', 'title' => 'Manage Opening Hours'))); ?>
            </li>
            <li  class="{{ (Request::is('user/managemenu') OR Request::is('user/addmenu') OR Request::is('user/editmenu*')) ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/managemenu', '<i class="fa fa-cutlery"></i> Manage Menu', array('class' => 'icon-5', 'title' => 'Manage Menu'))); ?>
            </li>
            <li  class="{{ (Request::is('user/orderstatus') OR Request::is('user/addstatus') OR Request::is('user/editstatus*')) ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/orderstatus', '<i class="fa fa-check"></i> Additional Order Status', array('class' => 'icon-5', 'title' => 'Additional Order Status'))); ?>
            </li>
<!--            <li  class="{{ (Request::is('user/creditcardsetup') OR Request::is('user/creditcardsetup') OR Request::is('user/creditcardsetup*')) ? 'active' : '' }}">
                <?php //echo html_entity_decode(HTML::link('user/creditcardsetup', '<i class="fa fa-paypal"></i> Credit card setup', array('class' => 'icon-5', 'title' => 'Credit card setup'))); ?>
            </li>-->
            <?php
        }
        ?>
        <li class="{{ Request::is('user/changePassword') ? 'active' : '' }}">
            <?php echo html_entity_decode(HTML::link('user/changePassword', '<i class="fa fa-key"></i> Change Password', array('style' => 'border-right:none', 'class' => 'icon-4', 'title' => 'Change Password'))); ?>
        </li>
        <?php 
        
        if ($type == 'Restaurant') {
            ?>
            <li class="{{ Request::is('user/couponcodes') ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/couponcodes', '<i class="fa fa-tag"></i> Coupon codes', array('style' => 'border-right:none', 'class' => 'icon-4', 'title' => 'Manage Coupan codes'))); ?>
            </li>    
            <li class="{{ Request::is('user/receivedpayment') ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/receivedpayment', '<i class="fa fa-credit-card"></i> Received Payment ', array('style' => 'border-right:none', 'class' => 'icon-4', 'title' => 'Received Payments'))); ?>
            </li> 
            <li class="{{ Request::is('user/paymenthistory/sponsorship') ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/paymenthistory/sponsorship', '<i class="fa fa-money"></i> Sponsorship History', array('style' => 'border-right:none', 'class' => 'icon-4', 'title' => 'Payment History'))); ?>
            </li> 
                
             
            <?php
            $user_id = Session::get('user_id');
             $ratings = DB::table('reviews')
                            ->select(DB::raw("avg(quality) as quality"), DB::raw("avg(packaging) as packaging"), DB::raw("avg(delivery) as delivery"), DB::raw("count(id) as counter"))
                            ->where('caterer_id', $user_id)
                            ->where('status', '1')
                            ->first();
             $userData = DB::table('users')
                ->where('id', Session::get("user_id"))
                ->first();
                    ?>
                    
                    <li class="{{ Request::is('user/myreviews') ? 'active' : '' }}">
                        <?php if($ratings->counter > 0){ ?>
                        <a href="{{HTTP_PATH.'user/myreviews'}}"> <i class="fa fa-comment-o"></i> Reviews ({{$ratings->counter}})</a>
                        <?php } ?>
                    </li>   
                    
 <?php
        }else{
            ?>
            <li class="{{ Request::is('user/paymenthistory/purchase') ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/paymenthistory/purchase', '<i class="fa fa-money"></i> Payment History', array('style' => 'border-right:none', 'class' => 'icon-4', 'title' => 'Payment History'))); ?>
            </li>    
            <li class="{{ Request::is('user/myreviews') ? 'active' : '' }}">
                <?php echo html_entity_decode(HTML::link('user/myreviews', '<i class="fa fa-comment"></i> My reviews', array('style' => 'border-right:none', 'class' => 'icon-4', 'title' => 'Payment History'))); ?>
            </li>    
<!--            <li class="{{ Request::is('user/wallet') ? 'active' : '' }}">
                <?php //echo html_entity_decode(HTML::link('user/wallet', '<i class="fa fa-comment"></i> Wallet', array('style' => 'border-right:none', 'class' => 'icon-4', 'title' => 'Wallet'))); ?>
            </li>    -->
            <?php
        }
        ?>
    </ul>
</div>