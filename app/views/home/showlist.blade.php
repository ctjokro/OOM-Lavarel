@section('content')
<script type="text/javascript">
    $(document).ready(function () {
        $("#city").change(function () {
            $("#area").load("<?php echo HTTP_PATH . "customer/loadarea/" ?>" + $(this).val() + "/0");
        })
    });
    
    $('body').on('click','.tytpon',function(e){
        $('#searchform').submit();
    });
    $('body').on('change','.selectbox',function(e){
        $('#searchform').submit();
    });
</script>

<div class="clear"></div>
<div class="listing_banner" id="map_canvas"><h2>Select Your Restaurant</h2></div> 
<div class="wrapper">
    <div class="list_bx">
        <div class="liste_top">
            <h1>Top Restaurants </h1>
        </div>  
        {{ Form::open(array('url' => '/restaurants/list', 'method' => 'get','class'=>"form",'id'=>'searchform')) }}
        <div class="col-md-3 col-sm-4 col-xs-12">
        <div class="lis_left_menu">
            <h1>Filter by City/Area</h1>
            <div class="lis_input lis_inputse">
                <span>
                    <?php
                    $input = Input::all();
                    $cities_array = array(
                        '' => "Select City"
                    );

                    $cities = City::where('status', "=", "1")->orderBy('cities.name','asc')->lists('name', 'id');
                    if (!empty($cities)) {
                        foreach ($cities as $key => $val)
                            $cities_array[$key] = ucfirst($val);
                    }
                    ?>
                    {{ Form::select('city', $cities_array, (isset($input['city'])?$input['city']:""), array('id'=>'city','class'=>'selectbox')) }}
                </span>
            </div>

            <div class="lis_input lis_inputse">
                <span>
                    <?php
                    $area_array = array(
                        '' => 'Select Area'
                    );
                    $area = Area::where('status', "=", "1")->where('city_id', "=", (isset($input['city']) ? $input['city'] : ""))->orderBy('name', 'asc')->lists('name', 'id');
                    if (!empty($area)) {
                        foreach ($area as $key => $val)
                            $area_array[$key] = ucfirst($val);
                    }
                    ?>
                    {{ Form::select('area', $area_array, (isset($input['area'])?$input['area']:""), array('id'=>'area','class'=>'selectbox')) }}
                </span>
            </div>
            <?php
            if (!empty($cuisines)) {
                $get_cuisine = isset($input['cuisine']) ? $input['cuisine'] : array();
                $catering_type = isset($input['catering_type']) ? $input['catering_type'] : array();
                ?>
                <div class="cat_menus">
                    <h2>Filter By Meal Type</h2>
                    <ul>
                        <?php
                        $mealtype = Mealtype::orderBy('name', 'asc')->where('status', "1")->lists('name', 'id');
//                                    if (!empty($mealtype)) {
//                                        foreach ($mealtype as $key => $val)
//                                            $mealtype[$key] = ucfirst($val);
//                                    }
                        foreach ($mealtype as $id => $val) {
                            ?>
                            <li>
                                <div class="checkbox">
                                    <input type="checkbox" {{in_array($id, $catering_type)?"checked='checked'":""}} value="<?php echo $id; ?>" id="<?php echo $id ?>" class="tytpon" name="catering_type[]">
                                    <label for="<?php echo $id ?>"><?php echo $val ?></label>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>

                    <h2>Filter By Cuisine</h2>
                    <ul>
                        <?php
                        foreach ($cuisines as $id => $val) {
                            
                            if(isset($_REQUEST['city']) && $_REQUEST['city'] > 0){
                                if((isset($_REQUEST['city']) && $_REQUEST['city'] > 0) && (isset($_REQUEST['area']) && $_REQUEST['area'] > 0)){
                                     
                                      $pcount  = DB::table('menu_item')->where('menu_item.cuisines_id', '=', $id)
                                    ->where('menu_item.status', '=', 1)
                                    ->where('users.city', '=', $_REQUEST['city'])
                                    ->where('users.area', '=', $_REQUEST['area'])
                                    ->leftjoin('users', 'menu_item.user_id', '=', 'users.id')
                                    ->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                                    ->groupBy('menu_item.user_id')
                                     ->get();
                                }else{
                                   
                                                                        
                                      $pcount  = DB::table('menu_item')->where('menu_item.cuisines_id', '=', $id)
                                    ->where('menu_item.status', '=', 1)
                                    ->where('users.city', '=', $_REQUEST['city'])
                                    ->where('users.status', '=', 1)
                                    ->where('opening_hours.open_close', "=", "1")
                                    ->leftjoin('users', 'menu_item.user_id', '=', 'users.id')
                                    ->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                                    ->groupBy('menu_item.user_id')
                                     ->get();
                                }
                                
                                  
                            }else{ 
                                  $pcount  = DB::table('menu_item')->where('menu_item.cuisines_id', '=', $id)
                                ->where('menu_item.status', '=', 1)
                                          ->where('opening_hours.open_close', "=", "1")
                                ->leftjoin('users', 'menu_item.user_id', '=', 'users.id')
                                          ->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                                ->groupBy('menu_item.user_id')
                                 ->get();
                            }
                           //echo '<pre>';print_r($pcount);
                            ?>
                            <li>
                                <div class="checkbox">
                                    <input type="checkbox" {{in_array($id, $get_cuisine)?"checked='checked'":""}} value="<?php echo $id; ?>" id="<?php echo $id ?>" class="tytpon" name="cuisine[]">
                                    <label for="<?php echo $id ?>"><?php echo $val ?> (<?php echo count($pcount); ?>)</label>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                    <div class="search_btn">
                        <!--{{ Form::submit('Filter Results', array('class' => "btn btn-primary ")) }}-->
                    <?php
                     if(isset($_REQUEST['city']) && $_REQUEST['city'] > 0){
                                if((isset($_REQUEST['city']) && $_REQUEST['city'] > 0) && (isset($_REQUEST['area']) && $_REQUEST['area'] > 0)){
                               ?>{{ html_entity_decode(HTML::link(HTTP_PATH.'restaurants/list?city='.$_REQUEST['city'].'&area='.$_REQUEST['area'], "Clear Filter", array('class' => 'btn btn-default'), true)) }}<?php
                          }else{
                               ?>{{ html_entity_decode(HTML::link(HTTP_PATH.'restaurants/list?city='.$_REQUEST['city'], "Clear Filter", array('class' => 'btn btn-default'), true)) }}<?php
                          }
                            
                        }else{
                            
                             ?>{{ html_entity_decode(HTML::link(HTTP_PATH.'restaurants/list', "Clear Filter", array('class' => 'btn btn-default'), true)) }}<?php
                        }
                    ?>
                        
                    </div>
                </div>
            <?php } ?>
        </div></div>
        <div class="col-md-9 col-sm-8 col-xs-12">
        <div class="lis_right_site">
            <div class="filters_dv">
                <ul>
                    <?php
                   // echo "<pre>";  print_r($restro);  echo "</pre>";
                    if (!$restro->isEmpty()) {
                        
                        ?>
                        <li><span>Sort By <i class="fa fa-caret-right"></i></span></li>
                        <li <?php echo (isset($input['s']) and $input['s'] == 'first_name') ? 'class="active"' : ""; ?>>
                            {{ SortableTrait::link_to_sorting_action('first_name', 'A-Z') }}
                        </li>
<!--                        <li><a href="#">Popularity</a></li>-->
                        <li <?php echo (isset($input['s']) and $input['s'] == 'rating') ? 'class="active"' : ""; ?>>
                            {{ SortableTrait::link_to_sorting_action('rating', 'Top Rated') }}
                        </li>
                    <?php } else {
                        ?>
                        <li><span>Sort By <i class="fa fa-caret-right"></i></span></li>
                        <li>
                            <a href="javascript:void(0)">A-Z</a>
                        </li>
<!--                        <li><a href="javascript:void(0)">Popularity</a></li>-->
                        <li class="most_loved"><a href="javascript:void(0)">Top Rated</a></li>
                    <?php }
                    ?>
                    <li class="search-filter">
                        {{ Form::text('keyword', isset($input['keyword'])?$input['keyword']:"", array("placeholder"=>'Search by restaurants name, cuisine name, food items', 'class' => 'form-control')) }}
                        {{ Form::submit('Find', array('class' => "btn-primary")) }}
                    </li>
                </ul>
            </div>

            {{ Form::close() }}
            <div class="lis_bx listing_new_design">
                <?php
                if (!$restro->isEmpty()) {
                   // var_dump($restro);
                    foreach ($restro as $user) {
                        //echo "<pre>"; print_r($user); 
//                        if($user->open_close == 0){
//                            continue;
//                        }
                        ?>
                <?php
                                                if ($user->unique_name) {
                                                    $subdomainVal = $user->unique_name;
                                                } else {
                                                    $subdomainVal = $user->slug;
                                                }
                                                ?>
                        <div class="listing_bxs listin_new">
                            <div class="listing_top">
                            <div class="listing_bxs_left">
                                <?php
                                if($user->featured == 1){
                                                 if(strtotime($user->expiry_date) >= time()){
                                                       echo "<span class='featured listing_span'>Featured</span>";
                                                }else{

                                                    $user_id = $user->id;
                                                    DB::table('users')
                                                     ->where('id', $user_id)
                                                     ->update(array('featured' => 0));
                                                 }
                                            
                                          }
                                if (file_exists(DISPLAY_FULL_PROFILE_IMAGE_PATH . $user->profile_image) and $user->profile_image) {
                                    ?>
                                    <a href="{{'http://'.$subdomainVal.'.'.SERVER_PATH.'welcome'}}"><img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.DISPLAY_FULL_PROFILE_IMAGE_PATH.$user->profile_image.'&w=177&h=171&zc=1&q=100') }}" alt="img" /></a>
                                    <?php
                                } else {
                                    ?>
                                    <a href="{{'http://'.$subdomainVal.'.'.SERVER_PATH.'welcome'}}"> <img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=177&h=171&zc=1&q=100') }}" alt="img" /></a>
                                    <?php
                                }
                                ?>
                                     
                            </div>
                            <div class="listing_bxs_right">
                                
                                <h1>{{ html_entity_decode(HTML::link('http://'.$subdomainVal.'.'.SERVER_PATH.'welcome',ucwords($user->first_name." ".$user->last_name ), ['class'=>'link-menu_m'])); }}
                                
                                <?php
                                        $menu_itemNonveg = DB::table('menu_item')
                                                           ->where('user_id', $user->id)
                                                           ->where('non_veg', '1')
                                                           ->first();
                                        $menu_itemveg = DB::table('menu_item')
                                                           ->where('user_id', $user->id)
                                                           ->where('non_veg', '0')
                                                           ->first();
                                        if($menu_itemNonveg){
                                            ?><span class="new-veg-non" title="Non-veg">{{ HTML::image(URL::asset('public/img/front/non-veg-img.png'), '', array()) }}</span><?php
                                        }
                                        if($menu_itemveg){
                                              ?><span class="new-veg-non" title="Veg">{{ HTML::image(URL::asset('public/img/front/veg-img.png'), '', array()) }}</span><?php
                                        }

                                       ?>
                                </h1>
                                <div class="address">
                                    <span class="area_bold">{{$user->area_name }},  {{$user->city_name }}</span>
                                    <span >{{$user->address }}, {{$user->area_name }}</span>
                                    <div class="faviv">
                                        <?php 
                                        
                                        $user_id = Session::get('user_id');
                                        $allLikedata = DB::table('thumbs')
                                                ->where('restro_id', $user->id)
                                                ->where('type', 'like')
                                                ->get();

                                        $allDisLikedata = DB::table('thumbs')
                                               ->where('restro_id', $user->id)
                                                ->where('type', 'dislike')
                                                ->get();
                                        
                                         if($user_id > 0){
                                             
                                             $ifexut = DB::table('thumbs')
                                               ->where('restro_id', $user->id)
                                               ->where('user_id', $user_id)
                                                ->first();
                                             
                                                $class = "";
                                               
                                                if($ifexut){
                                                    if($ifexut->type == "like"){
                                                        $class = "like";
                                                    }else{
                                                        $class = "dislike";
                                                    }
                                                }
                                              ?> <span class="_tttb likenlike <?php echo (isset($class) && $class!="" && $class == "like")?"active":""; ?>" dataid ="<?php echo $user->id; ?>" access-mode="true" data-label="I like it." datatypec="like" title="I like it."><i class="fa fa-thumbs-up "></i> <?php echo count($allLikedata) ?></span>
                                                <span class="_tttb likenlike <?php echo (isset($class) && $class!="" && $class == "dislike")?"active":""; ?>" dataid ="<?php echo $user->id; ?>" access-mode="true" data-label="I don't like it." datatypec="dislike" title="I don't like it."><i class="fa fa-thumbs-down"></i> <?php echo count($allDisLikedata) ?></span>
                                            <?php
                                         }else{
                                             ?>  <span class="_tttb likenlike" dataid ="<?php echo $user->id; ?>" access-mode="false" data-label="I like it." datatypec="like" title="I like it."><i class="fa fa-thumbs-up"></i> <?php echo count($allLikedata) ?></span>
                                                <span class="_tttb likenlike" dataid ="<?php echo $user->id; ?>" access-mode="false" data-label="I don't like it." datatypec="dislike" title="I don't like it."><i class="fa fa-thumbs-down"></i> <?php echo count($allDisLikedata) ?></span>
                                            <?php
                                         }
                                        
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="open_img open_img_new">
                                    <?php
                                    // get carters open/close status
                                    if ($user->open_close) {
                                         $day = date('D');
                                        $open_days = explode(",", $user->open_days);

                                        $vrrcy = explode(',',$user->start_time);
                                        $endty = explode(',',$user->end_time);
                                      //  $endbr = explode(',', $user->breakfast_time);
                                       // echo "<pre>"; print_r($endbr); echo "</pre>";
                                        $startime = array_combine($open_days, $vrrcy);
                                        $endtime = array_combine($open_days, $endty);
                                      //  $brtime = array_combine($open_days, $endbr);
                                        $start = "";
                                        if(isset($startime[strtolower(date('D'))])){

                                            $start = $startime[strtolower(date('D'))];
                                        }
                                        $end ="";
                                        if(isset($endtime[strtolower(date('D'))])){
                                              $end = $endtime[strtolower(date('D'))];
                                        }
                                    /*    $brstart ="";
                                        if (isset($brtime[strtolower(date('D'))])) {
                                          $brstart = $brtime[strtolower(date('D'))];
                                        }  */
                                        
                                        if($end < $start){
                            $end = date('Y-m-d h:i',strtotime(date('Y-m-d '.$end) . ' +1 day'));
                            
                        }
                        /*
                        $exlunch = explode('-',$start);
                        $exdinner = explode('-',$end);
                        $exbreak = explode('-',$brstart);
                        
                        if((isset($exbreak) && count($exbreak == '2') && strtotime($exbreak[0]) <= time()) && (isset($exbreak[1]) && time() <= strtotime($exbreak[1])) and in_array(strtolower(date('D')), $open_days)){
                          // $open = 1;
                            ?>
                            <span class="open same_style">open</span>
                            <?php 
                       }
                       elseif((isset($exlunch) && count($exlunch == '2') && strtotime($exlunch[0]) <= time()) && (isset($exlunch[1]) && time() <= strtotime($exlunch[1])) && in_array(strtolower(date('D')), $open_days)){
                         //  $open = 1;
                            ?>
                            <span class="open same_style">open</span>
                            <?php
                       }
                       elseif((isset($exdinner) && count($exdinner == '2') && strtotime($exdinner[0]) <= time()) && (isset($exdinner[1]) && time() <= strtotime($exdinner[1])) and in_array(strtolower(date('D')), $open_days)){
                         //  $open = 1;
                            ?>
                            <span class="open same_style">open</span>
                            <?php
                       }
                       
                       else {
                             //echo date('Y-m-d H:i:s'); 
                            ?>
                            <span class="closee same_style">closed</span>
                            <?php
                            
                        }  */
                            
                            
                            // here previous open close resturant code comment
                                        if ((isset($start) && strtotime($start) <= time()) && (isset($end) && time() <= strtotime($end)) and in_array(strtolower(date('D')), $open_days)) {
                                                          
                                            ?>
                                    <!--<span class="open same_style">open</span>-->
                                            <?php
                                        } else {
                                            ?>
                                    <!--<span class="closee same_style">closed</span>-->
                                            <?php
                                        }   
                                        
                                        
                                    } else {
                                        ?>
                                        <!--<span class="closee same_style">closed</span>-->
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="rating_bx rating_bx_new">
                                    <?php
                                    $avg_ratng = round($user->rating);
                                    for ($i = 0; $i < $avg_ratng; $i++) {
                                        echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                                    }
                                    for ($j = 5; $j > $avg_ratng; $j--) {
                                        echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                                    }
                                    ?>
                                    <span class="rating_number">({{$user->counter}})</span> 
                                    
                                </div>
                            </div>
                                <div class="sdsvvv">
                                    <?php 
                                    $pickc = DB::table('pickup_charges')->where('user_id', $user->id)->first();

                            //print_r($pickc); exit;
                            if ($pickc) {
                                if ($pickc->pick_up == 1) {
                                    echo '<div title="Pickup facility available" class="asd_yy_vyyt">' . HTML::image(HTTP_PATH . "public/img/front/pick_up.png", '') . '</div>';
                                }
                            }
                                    ?>
                                </div>
                            </div>
                            <div class="listing_bottom">
                                <div class="search-page-text">
                            <ul class="list_menus">
<!--                                        <li>
                                            <h3>Minimum Order</h3>
                                            <h2>{{$user->minimum_order? $user->minimum_order:" Not Available "}}</h2>
                                        </li>-->
<!--                                        <li>
                                            <label class="lab_title">Opening Hours</label>
                                            <span class="filedd">
                                                <?php
                                               // echo $user->id;
                                              /*  if ((!empty($user->start_time) && $user->start_time != "00:00:00") && (!empty($user->end_time) && $user->end_time != "00:00:00")) {
                                                    echo $user->start_time ? date("h:i a", strtotime($user->start_time)) . " - " . date("h:i a", strtotime($user->end_time)) : " - ";
                                                } else {
                                                    echo "Not Available";
                                                } */
                                                ?>
                                            </span>
                                        </li>-->
                                        <li>
                                            <label class="lab_title">Cuisine</label>
                                            <span class="filedd">
                                                <?php
                                                $cusrinesId =  Menu::where('user_id', $user->id)->lists('cuisines_id');
                                              
                                                
                                                if ($cusrinesId) {
                                                    $cuisinename = DB::table('cuisines')->whereIn('id', $cusrinesId)->select("cuisines.id", "cuisines.name")->get();
                                                    // print_r($cuisinename); exit;
                                                    $max = count($cuisinename);
                                                    $x = 1;
                                                    foreach ($cuisinename as $CsVal) {
                                                        
                                                        //secho $CsVal->name;
                                                        // print_r($mealsVal); exit;
                                                        ?>{{ html_entity_decode(HTML::link(HTTP_PATH.'restaurants/list?city=&area=&cuisine[]='.$CsVal->id.'&keyword=',ucwords($CsVal->name), ['class'=>'link-menu_m'])); }}<?php
                                                      
                                                        
                                                        if($x < $max){
                                                            echo ', ';
                                                        }
                                                          $x++;
                                                    }
                                                } else {
                                                    echo "Not available";
                                                }
                                                ?>
                                            </span>
                                        </li>
                                        <li>
                                            <label class="lab_title">Meal Type</label>
                                            <span class="filedd">
                                                <?php
                                                 if ($user->catering_type != "") {
                                                       $meals = explode(',', $user->catering_type);
                                                       $mealsTypes = DB::table('mealtypes')
                                                               ->select("mealtypes.id", "mealtypes.name")
                                                               ->whereIn('id', $meals)
                                                               ->get();
                                                        $max = count($mealsTypes);
                                                        $Y = 1;
                                                       foreach ($mealsTypes as $mealsVal) {
                                                           // print_r($mealsVal); exit;
                                                           ?>{{ html_entity_decode(HTML::link(HTTP_PATH.'restaurants/list?city=&area=&catering_type[]='.$mealsVal->id,ucwords($mealsVal->name), ['class'=>'link-menu_m'])); }}
                                                           <?php
                                                           if($Y < $max){
                                                            echo ', ';
                                                        }
                                                          $Y++;
                                                       }
                                                   }else{
                                                        echo "Not available";
                                                   }
                                                ?>
                                            </span>
                                        </li>
                                        
                            </ul></div></div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="dataTables_paginate paging_bootstrap pagination">
                        {{ $restro->appends(Input::except('page'))->links() }}
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="no-record-list">
                        No Restaurant available for your search criteria
                    </div>
                    <?php
                }
                ?>
            </div>
        </div></div>
    </div>
</div>
<div class="clear"></div>
<?php 


$alllocation = "";
if($restro){
    foreach($restro as $valc){
        $img = "";
        
    if (file_exists(DISPLAY_FULL_PROFILE_IMAGE_PATH . $valc->profile_image) and $valc->profile_image) {
        $img = "<img src='".HTTP_PATH."public/assets/timthumb.php?src=".HTTP_PATH.DISPLAY_FULL_PROFILE_IMAGE_PATH.$valc->profile_image."&w=220&h=115&zc=3&q=100'  />";
        
    } 
       // $alllocation[]  = $valc->first_name.' '.$valc->address.' '.$valc->area_name.' '.$valc->city_name;
//        $alllocation[]  = '["'.preg_replace("/[^a-zA-Z0-9\s]/", "", preg_replace('/(?:\r\n|\n)/',"", $valc->address)).' '.$valc->area_name.' '.$valc->city_name.'","<div id=iw-container>'.$img.'<div class=iw-title><a href='.HTTP_PATH.'restaurants/menu/'.$valc->slug.'>'. $valc->first_name.'</a></div></div>","'.$valc->first_name.'"]';'http://'.$subdomainVal.'.'.SERVER_PATH
        $alllocation[]  = '["'.preg_replace("/[^a-zA-Z0-9\s]/", "", preg_replace('/(?:\r\n|\n)/',"", $valc->address)).' '.$valc->area_name.' '.$valc->city_name.'","<div id=iw-container><div class=iw-content><div class=iw-subTitle>'.$img.'</div><div class=iw-title><a href=http://'.$subdomainVal.'.'.SERVER_PATH.'welcome'.'>'. $valc->first_name.'</a><div></div>'.preg_replace("/[^a-zA-Z0-9\s]/", "", preg_replace('/(?:\r\n|\n)/',"", $valc->address)).' '.$valc->area_name.' '.$valc->city_name.'</div></div>","'.$valc->first_name.'"]';
      
    }
}

if($alllocation){
    
?>

<style>
 
#map_canvas {
  height: 400px;
  width: 100%;
}
</style>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyCeb4b0y0CStJboZR9Iu4GCW0Ive4JfHIw&sensor=false">
 </script>
 <script type="text/javascript">
  var delay = 100;
  var infowindow = new google.maps.InfoWindow();
  var latlng = new google.maps.LatLng(-33.870453,151.208755);
  var mapOptions = {
    zoom: 6,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  var geocoder = new google.maps.Geocoder(); 
  var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
  var bounds = new google.maps.LatLngBounds();

  function geocodeAddress(address,link,name, next) {
    geocoder.geocode({address:address}, function (results,status)
      { 
          //alert(results);
         if (status == google.maps.GeocoderStatus.OK) {
          var p = results[0].geometry.location;
          var lat=p.lat();
          var lng=p.lng();
          createMarker(address,link,name,lat,lng);
        }
        else {
           if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
            nextAddress--;
            delay++;
          } else {
                        }   
        }
        next();
      }
    );
  }
    
    function createMarker(add,link,name,lat,lng) {
      var contentString = link;
    var pinImage = new google.maps.MarkerImage("http://www.googlemapsmarkers.com/v1/"+name.substr(0, 1)+"/3A8ACF/FFFFFF/FFFFFF/");
      var marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat,lng),
        map: map,
        icon: pinImage,
        });
        
        var infowindow = new google.maps.InfoWindow({
                content: contentString,
                maxWidth: 350
        });
        
    google.maps.event.addListener(marker, 'mouseover', function() {
      
        
  
        infowindow.open(map, this);

         

   });
   

    marker.addListener('mouseout', function() {
    
          infowindow.close();
   });
   bounds.extend(marker.position);
 }
 
 
 
 google.maps.event.addListener(infowindow, 'domready', function() {

    // Reference to the DIV that wraps the bottom of infowindow
    var iwOuter = $('.gm-style-iw');

    /* Since this div is in a position prior to .gm-div style-iw.
     * We use jQuery and create a iwBackground variable,
     * and took advantage of the existing reference .gm-style-iw for the previous div with .prev().
    */
    var iwBackground = iwOuter.prev();

    // Removes background shadow DIV
    iwBackground.children(':nth-child(2)').css({'display' : 'none'});

    // Removes white background DIV
    iwBackground.children(':nth-child(4)').css({'display' : 'none'});

    // Moves the infowindow 115px to the right.
    iwOuter.parent().parent().css({left: '115px'});

    // Moves the shadow of the arrow 76px to the left margin.
    iwBackground.children(':nth-child(1)').attr('style', function(i,s){ return s + 'left: 76px !important;'});

    // Moves the arrow 76px to the left margin.
    iwBackground.children(':nth-child(3)').attr('style', function(i,s){ return s + 'left: 76px !important;'});

    // Changes the desired tail shadow color.
 //   iwBackground.children(':nth-child(3)').find('div').children().css({'box-shadow': 'rgba(72, 181, 233, 0.6) 0px 1px 6px', 'z-index' : '1'});

    // Reference to the div that groups the close button elements.
    var iwCloseBtn = iwOuter.next();

    // Apply the desired effect to the close button
    iwCloseBtn.css({opacity: '1', right: '38px', top: '3px', border: '7px solid #48b5e9', 'border-radius': '13px'});

    // If the content of infowindow not exceed the set maximum height, then the gradient is removed.
    if($('.iw-content').height() < 140){
      $('.iw-bottom-gradient').css({display: 'none'});
    }

    // The API automatically applies 0.7 opacity to the button after the mouseout event. This function reverses this event to the desired value.
    iwCloseBtn.mouseout(function(){
      $(this).css({opacity: '1'});
    });
  });
  
  
  var locations = [<?php echo implode(',',$alllocation); ?>];
  var nextAddress = 0;
  function theNext() {
    if (nextAddress < locations.length) {
      setTimeout('geocodeAddress("'+locations[nextAddress][0]+'","'+locations[nextAddress][1]+'","'+locations[nextAddress][2]+'",theNext)', delay);
      nextAddress++;
    } else {
      map.fitBounds(bounds);
    }
  }
  theNext();

</script>
<?php } ?>
<!--<script>
$(document).ready(function () {
    var map;
    var elevator;
    var myOptions = {
        zoom: 3,
        center: new google.maps.LatLng(0, 0),
        mapTypeId: 'terrain'
    };
    map = new google.maps.Map($('#map_canvas')[0], myOptions);

    var addresses = ['Norway', 'Africa'];

    for (var x = 0; x < addresses.length; x++) {
        $.getJSON('http://maps.googleapis.com/maps/api/geocode/json?address='+addresses[x]+'&sensor=false', null, function (data) {
            var p = data.results[0].geometry.location
            var latlng = new google.maps.LatLng(p.lat, p.lng);
            new google.maps.Marker({
                position: latlng,
                map: map
            });

        });
    }

});
</script>-->
@stop
