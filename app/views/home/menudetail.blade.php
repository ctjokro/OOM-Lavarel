    
<?php
$open = 0;
// get carters open/close status
if ($caterer->open_close) {

    $open_days = explode(",", $caterer->open_days);
    if (strtotime($caterer->start_time) <= time() && time() <= strtotime($caterer->end_time) and in_array(strtolower(date('D')), $open_days)) {
        $open = 1;
    }
}
?>
<div class="_newcyyy">
    <div class="top_left">
    <div class="img_fream_img">
        <?php
        if (file_exists(UPLOAD_FULL_ITEM_IMAGE_PATH . $menuData->image) and $menuData->image) {
            ?>
            <img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.UPLOAD_FULL_ITEM_IMAGE_PATH.$menuData->image.'&w=150&h=150&zc=2&q=100') }}" alt="img" />
            <?php
        } else {
            ?>
            <img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=150&h=150&zc=2&q=100') }}" alt="img" />
            <?php
        }
        ?>
    </div>
        <div class="right_detail">
    <h1 >{{$menuData->item_name}}
    <?php 
     if($menuData->spicy == 1){
                ?><span class="nonb borderfi" title="This is spicy food."><img  class="" src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'/public/img/front/chilli.png'.'&w=20&h=20&zc=2&q=100') }}" alt="img" /></span><?php
            }
                       
             if ($menuData->non_veg == 0) {
                            ?><span class="new-veg-non" title="Veg">{{ HTML::image(URL::asset('public/img/front/veg-img.png'), '', array()) }}</span><?php
                        }
        if($menuData->non_veg == 1){
                    ?><span class="new-veg-non" title="Non-veg">{{ HTML::image(URL::asset('public/img/front/non-veg-img.png'), '', array()) }}</span><?php
                }
                                                    ?>
    
    </h1>
    <div class="like_n">{{$menuData->description}}</div></div></div> 
    <div class="full_row">
  
        <div class="left_main2" style="display: none">

            <!--<input type="button" value="-" class="but_1 counter_number"  id_val="{{$menuData->id}}"  alt="minus" />-->
            <input readonly="readonly" maxlength="3" type="hidden" value="{{isset($key_array[$menuData->id]) ? $key_array[$menuData->id] : 0}}" class='{{"preparation_time_".$menuData->id}}' />
            <input type="button" value="Add to cart" class="but_2 counter_numberpop " id_val="{{$menuData->id}}"  alt="plus" />

        </div></div>
    
    <div class="maindiv">
        
    <div class="platenumber">
        <?php 
        $parentData = DB::table('variants')
                ->where('menu_id', $menuData->id)
                ->where('parent', 1)
                ->first();
        ?>
        <input type="hidden" name="base_price" id="base_price" value="{{$menuData->price}}" />
        <input type="hidden" name="variant_type" id="variant_type" value="<?php //echo $parentData->id ?>" />
        <input type="hidden" name="addons" id="addons"  />
        <?php 
       
        
        $variantData = DB::table('variants')
                ->where('menu_id', $menuData->id)
                ->orderBy('parent','desc')
                ->get();
        
        if($variantData){
            ?>
        <div class="nctvv">
        <div class="_ttv">Variant</div>
        <div class="placr">
        <?php
          echo "<ul>";
       
        //  echo count($addonData);
          $x = 1;
        
          foreach($variantData as $addonDataVal){
              $idNCV = "";
              if($addonDataVal->parent == 1){
                  $idNCV = "newc";
                 
              }
              ?><li><label>{{$addonDataVal->name}}</label> <span class="pricev">{{number_format($addonDataVal->price,2)}}</span> <span data-name-variant="{{$addonDataVal->name}}" data-price-variant="{{$addonDataVal->price}}" data-id-variant="{{$addonDataVal->id}}" data-type="Variant" id="<?php echo $idNCV; ?>" class="btnv">Get it</span></li><?php
          }
          echo "</ul>";
          ?></div></div><?php
        }
        ?>
        <script> 
      
              $("#newc").trigger("click")
        // $(".btnv")[0].trigger('click');
            
        </script>
        <?php 
       
        
        $addonData = DB::table('addons')
                ->where('menu_id', $menuData->id)
                ->get();
        if($addonData){
            ?>
        <div class="nctvv">
        <div class="_ttv">Add-ons</div>
        <div class="placr">
        <?php
        //  echo count($addonData);
          $x = 1;
          echo "<ul>";
          foreach($addonData as $addonDataVal){
              ?><li><label>{{$addonDataVal->addon_name}}</label> <span class="pricev">{{$addonDataVal->addon_price}}</span> <span data-name="{{$addonDataVal->addon_name}}" data-price="{{$addonDataVal->addon_price}}" data-type="Add-on" data-id="{{$addonDataVal->id}}" class="btnv">Get it</span></li><?php
          }
          echo "</ul>";
          ?></div></div><?php
        }
        ?>
        
    </div>
     <div id="summary" class="summrydiv"></div>
    </div>
    
</div>
