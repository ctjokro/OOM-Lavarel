@extends('layout')
@section('content')

<section>
    <div class="top_menus">
     
        
        
        <div class="wrapper">
          <div class="acc_bar acc_bar_new">
                
                 

              <div class="informetion informetion_new" style="width: 100%">
                    {{ View::make('elements.actionMessage')->render() }}
                    <div class="informetion_top">
                        <div class="tatils"><span class="personal">Select a package to make your restaurant featured</span>
                           
                        </div>

                        <div class="informetion_bx informetion_bxes_new">
                            <div class="informetion_bxes informetion_bxes_new">
                               
<div class="order_pack">
 <?php
                                if($packages)
                                {
                                    foreach($packages as $packagesVal){
                                        ?>
                                        <div class="_cttvv">
                                             <div class="_cttvv_inner">
                                            <div class="pricecv_name">
                                                <span class="center"><?php  echo $packagesVal->name; ?></span>
                                            </div>
                                            
                                            <div class="pricecv_name_price">
                                                <?php  
                                                if($packagesVal->price == 0){
                                                    echo "Free";
                                                }else{
                                                echo  App::make("HomeController")->numberformat($packagesVal->price,2);
                                                }
                                                ?>
                                            </div>
                                            <div class="pricecv_name_price_dis">
                                                <?php  echo nl2br($packagesVal->description); ?>
                                            </div>
                                            <div class="pricecv_name dayss">
                                                Duration: <?php  echo $packagesVal->no_of_days.' days'; ?>
                                            </div>
                                            
                                            <div class="btndiv">
                                                <?php 
                                                 if($packagesVal->price == 0){
                                                        ?> {{ html_entity_decode(link_to(HTTP_PATH.'user/proceed/'.$packagesVal->slug, 'Try for free', array('escape' => false,'class'=>"btn btn-primary"))) }}<?php
                                                }else{
                                                    ?> {{ html_entity_decode(link_to(HTTP_PATH.'user/proceed/'.$packagesVal->slug, 'Procced to pay '. App::make("HomeController")->numberformat($packagesVal->price,2), array('escape' => false,'class'=>"btn btn-primary"))) }}<?php
                                                }
                                                ?>
                                                
                                               
                                            </div>
                                             </div>   
                                        </div>    
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            </div>
                           

                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</section>
@stop


