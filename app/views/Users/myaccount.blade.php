@extends('layout')
@section('content')

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
                        <div class="tatils"><span class="personal">Personal info</span>
                            <div class="link-button edit_pro">
                                <?php echo html_entity_decode(HTML::link('user/editProfile', '<i class="fa fa-pencil-square-o"></i> Edit Profile', array('class' => 'icon-3', 'title' => 'Edit Profile'))); ?>
                            </div>
                            <?php if($userData->user_type == "Restaurant"){ ?>
                            
                            <div class="link-button dfdvvv">
                               <?php   if($userData->featured == 0){ ?>
                                {{ html_entity_decode(link_to(HTTP_PATH.'user/upgrade', 'Upgrade to featured', array('escape' => false,'class'=>"btn btn-primary",'target'=>'_blank'))) }}
                               <?php }else{
                                   if(strtotime($userData->expiry_date) > time()){
                                      // echo "Expire on : ".date('F d, Y',  strtotime($userData->expiry_date));
                                   }else{
                                       
                                       $user_id = Session::get('user_id');
                                       DB::table('users')
                                        ->where('id', $user_id)
                                        ->update(array('featured' => 0));
                                    }
                                   
                               }   ?>
                               
                            </div>
                            <?php } ?>
                        </div>

                        <div class="informetion_bx informetion_bxes_new">
                            <div class="informetion_bxes informetion_bxes_new">
                                <?php if($userData->user_type == "Restaurant"){ ?>
                                    <div class="informetion_bx_left">
                                        <label>Restaurant name</label>
                                        <div class="im_txt">
                                            <?php
                                            if ($userData->first_name) {
                                                echo $userData->first_name;
                                            } else {
                                                echo "N/A";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php }else { ?>
                                <div class="informetion_bx_left">
                                    <label>First name</label>
                                    <div class="im_txt">
                                        <?php
                                        if ($userData->first_name) {
                                            echo $userData->first_name;
                                        } else {
                                            echo "N/A";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="informetion_bx_left informetion_bx_right">
                                    <label>Last name</label>
                                    <div class="im_txt">
                                        <?php
                                        if ($userData->last_name) {
                                            echo $userData->last_name;
                                        } else {
                                            echo "N/A";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php } ?>
                                 <?php if($userData->user_type == "Restaurant"){ ?>
                                        <div class="informetion_bx_left informetion_bx_right">
                                            <label>Paypal email address</label>
                                            <div class="im_txt">
                                                <?php
                                                if ($userData->paypal_email_address) {
                                                    echo $userData->paypal_email_address;
                                                } else {
                                                    echo "N/A";
                                                }
                                                ?>
                                            </div>
                                        </div>
                            
                            <?php } ?>
                            </div>
                           

                            <div class="informetion_bxes">
                                <div class="informetion_bx_left">
                                    <label>Email</label>
                                    <div class="im_txt">
                                        <?php
                                        if ($userData->email_address) {
                                            echo $userData->email_address;
                                        } else {
                                            echo "N/A";
                                        }
                                        ?>

                                    </div>
                                </div>
                                <div class="informetion_bx_left informetion_bx_right">
                                    <label>Contact Number</label>
                                    <div class="im_txt">
                                        <?php
                                        if ($userData->contact) {
                                            echo $userData->contact;
                                        } else {
                                            echo "N/A";
                                        }
                                        ?>
                                    </div>
                                </div>
                                 
                                
                            </div>
                            <?php
                            if ($userData->user_type <> 'Courier') {
                                ?>
                                <div class="informetion_bxes">
                                    <div class="informetion_bx_left">
                                        <label>City</label>
                                        <div class="im_txt">
                                            <?php
                                            echo $userData->city_name ? ucfirst($userData->city_name) : "N/A"
                                            ?>

                                        </div>
                                    </div>
                                    <div class="informetion_bx_left informetion_bx_right">
                                        <label>Country</label>
                                        <div class="im_txt">
                                            <?php
                                            echo $userData->country ? ucfirst($userData->country) : "N/A"
                                            ?>
                                        </div>
                                    </div>
                                    <div class="informetion_bx_left informetion_bx_right">
                                        <label>Area</label>
                                        <div class="im_txt">
                                            <?php
                                            echo $userData->area_name ? ucfirst($userData->area_name) : "N/A"
                                            ?>
                                        </div>
                                    </div>
                                    <div class="informetion_bx_left informetion_bx_right">
                                        <label>Address</label>
                                        <div class="im_txt">
                                            <?php
                                        if ($userData->address) {
                                            echo $userData->address;
                                        } else {
                                            echo "N/A";
                                        }
                                        ?>
                                        </div>
                                    </div>
                                    <div class="informetion_bx_left informetion_bx_right">
                                        <label>Postal Code</label>
                                        <div class="im_txt">
                                            <?php
                                            echo $userData->postal_code ? $userData->postal_code : "N/A"
                                            ?>
                                        </div>
                                    </div>
                                    <?php  
                                 if($userData->featured == 1){ if(strtotime($userData->expiry_date) > time()){
                                      // echo "Expire on : ".date('F d, Y',  strtotime($userData->expiry_date));
                                     ?>
                                        <div class="informetion_bx_left informetion_bx_right">
                                            <label>Expire on</label>
                                            <div class="im_txt">
                                                <?php
                                                echo date('F d, Y',  strtotime($userData->expiry_date));
                                                ?>
                                            </div>
                                        </div> 
                                        <?php
                                   } }   ?>
                                </div>
                                <?php
                            }
                          /*  if ($userData->user_type == 'Restaurant') {
                                ?>
                                <div class="informetion_bxes">
                                    <div class="informetion_bx_left">
                                        <label>Deliver to</label>
                                        <div class="im_txt">
                                            <?php
                                            if ($userData->deliver_to) {
                                                $deliver_to = explode(",", $userData->deliver_to);
                                                $areas = array();
                                                foreach ($deliver_to as $area) {
                                                    $detail = DB::table('areas')
                                                            ->where('id', $area)
                                                            ->first();
                                                    if(!empty($detail))
                                                        $areas[] = $detail->name;
                                                        
                                                }
                                                echo implode(", ", $areas);
                                            } else {
                                                echo "N/A";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } */
                            if ($userData->user_type == 'Courier') {
                                ?>
                                <div class="informetion_bxes">
                                    <div class="informetion_bx_left">
                                        <label>Availability</label>
                                        <div class="im_txt">
                                            <?php
                                            echo $userData->availability ? "Available" : "Not Available"
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</section>
@stop


