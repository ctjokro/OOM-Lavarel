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
                    $area = Area::where('status', "=", "1")->where('city_id', "=", (isset($input['city']) ? $input['city'] : ""))->lists('name', 'id');
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
                            $pcount  = DB::table('menu_item')->where('menu_item.cuisines_id', '=', $id)
                            ->where('menu_item.status', '=', 1)
                            ->leftjoin('users', 'menu_item.user_id', '=', 'users.id')
                            ->groupBy('menu_item.user_id')
                             ->get();
                            
                            
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
                    {{ html_entity_decode(HTML::link(HTTP_PATH.'restaurants/list', "Clear Filter", array('class' => 'btn btn-default'), true)) }}
                    </div>
                </div>
            <?php } ?>
        </div></div>
        <div class="col-md-9 col-sm-8 col-xs-12">
        <div class="lis_right_site">
            <div class="filters_dv">
                <ul>
                    <?php
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
                       // echo "<pre>"; print_r($user); exit;
                        ?>
                        <div class="listing_bxs listin_new">
                            <div class="listing_top">
                            <div class="listing_bxs_left">
                                <?php
                                if($user->featured == 1){
                                                 if(strtotime($user->expiry_date) > time()){
                                                       echo "<span class='featured listing_span'>Featured</span>";
                                                }else{

                                                    $user_id = Session::get('user_id');
                                                    DB::table('users')
                                                     ->where('id', $user_id)
                                                     ->update(array('featured' => 0));
                                                 }
                                            
                                          }
                                if (file_exists(DISPLAY_FULL_PROFILE_IMAGE_PATH . $user->profile_image) and $user->profile_image) {
                                    ?>
                                    <a href="{{HTTP_PATH.'restaurants/menu/'.$user->slug}}"><img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.DISPLAY_FULL_PROFILE_IMAGE_PATH.$user->profile_image.'&w=177&h=171&zc=1&q=100') }}" alt="img" /></a>
                                    <?php
                                } else {
                                    ?>
                                    <a href="{{HTTP_PATH.'restaurants/menu/'.$user->slug}}"> <img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'public/img/front/default_restro.png&w=177&h=171&zc=1&q=100') }}" alt="img" /></a>
                                    <?php
                                }
                                ?>
                                     
                            </div>
                            <div class="listing_bxs_right">
                                
                                <h1>{{ html_entity_decode(HTML::link('restaurants/menu/'.$user->slug,ucwords($user->first_name." ".$user->last_name ), ['class'=>'link-menu_m'])); }}
                                
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
                                            ?><span class="nonb red-mark withborder" title="Non-veg"><i class="fa fa-circle"></i></span><?php
                                        }
                                        if($menu_itemveg){
                                              ?><span class="nonb green-mark withborder" title="Veg"><i class="fa fa-circle"></i></span><?php
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
                                                <span class="_tttb likenlike <?php echo (isset($class) && $class!="" && $class == "dislike")?"active":""; ?>" dataid ="<?php echo $user->id; ?>" access-mode="true" data-label="I dont like it." datatypec="dislike" title="I dont like it."><i class="fa fa-thumbs-down"></i> <?php echo count($allDisLikedata) ?></span>
                                            <?php
                                         }else{
                                             ?>  <span class="_tttb likenlike" dataid ="<?php echo $user->id; ?>" access-mode="false" data-label="I like it." datatypec="like" title="I like it."><i class="fa fa-thumbs-up"></i> <?php echo count($allLikedata) ?></span>
                                                <span class="_tttb likenlike" dataid ="<?php echo $user->id; ?>" access-mode="false" data-label="I dont like it." datatypec="dislike" title="I dont like it."><i class="fa fa-thumbs-down"></i> <?php echo count($allDisLikedata) ?></span>
                                            <?php
                                         }
                                        
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="open_img open_img_new">
                                    <?php
                                    // get carters open/close status
                                    if ($user->open_close) {
                                        $open_days = explode(",", $user->open_days);
                                        if (strtotime($user->start_time) <= time() && time() <= strtotime($user->end_time) and in_array(strtolower(date('D')), $open_days)) {
                                            ?>
                                    <span class="open same_style">open</span>
                                            <?php
                                        } else {
                                            ?>
                                    <span class="closee same_style">closed</span>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <span class="closee same_style">closed</span>
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
                            </div></div>
                            <div class="listing_bottom">
                                <div class="search-page-text">
                            <ul class="list_menus">
<!--                                        <li>
                                            <h3>Minimum Order</h3>
                                            <h2>{{$user->minimum_order? $user->minimum_order:" Not Available "}}</h2>
                                        </li>-->
                                        <li>
                                            <label class="lab_title">Opening Hours</label>
                                            <span class="filedd">
                                                <?php
                                                if ((!empty($user->start_time) && $user->start_time != "00:00:00") && (!empty($user->end_time) && $user->end_time != "00:00:00")) {
                                                    echo $user->start_time ? date("h:i a", strtotime($user->start_time)) . " - " . date("h:i a", strtotime($user->end_time)) : " - ";
                                                } else {
                                                    echo "Not Available";
                                                }
                                                ?>
                                            </span>
                                        </li>
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
                                                        ?>
                                                           {{ html_entity_decode(HTML::link(HTTP_PATH.'restaurants/list?city=&area=&cuisine[]='.$CsVal->id.'&keyword=',ucwords($CsVal->name), ['class'=>'link-menu_m'])); }}
                                                      
                                                        <?php
                                                      
                                                        
                                                        if($x < $max){
                                                            echo ',';
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
//        $alllocation[]  = $valc->first_name.' '.str_replace("'", '', $valc->address).' '.$valc->area_name.' '.$valc->city_name;
        $alllocation[]  = str_replace("'", '', $valc->address).' '.$valc->area_name.' '.$valc->city_name;
      
    }
}
if($alllocation){
?>

<style>
    #map_canvas {
  height: 300px;
  width: 100%;
}
</style>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false">
 </script>
 <script type="text/javascript">
  var delay = 100;
  var infowindow = new google.maps.InfoWindow();
  var latlng = new google.maps.LatLng(21.0000, 78.0000);
  var mapOptions = {
    zoom: 5,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  var geocoder = new google.maps.Geocoder(); 
  var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
  var bounds = new google.maps.LatLngBounds();

  function geocodeAddress(address, next) {
    geocoder.geocode({address:address}, function (results,status)
      { 
         if (status == google.maps.GeocoderStatus.OK) {
          var p = results[0].geometry.location;
          var lat=p.lat();
          var lng=p.lng();
          createMarker(address,lat,lng);
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
 function createMarker(add,lat,lng) {
   var contentString = add;
   var marker = new google.maps.Marker({
     position: new google.maps.LatLng(lat,lng),
     map: map,
           });

  google.maps.event.addListener(marker, 'click', function() {
     infowindow.setContent(contentString); 
     infowindow.open(map,marker);
   });

   bounds.extend(marker.position);

 }
  var locations = ["<?php echo implode('","',$alllocation) ?>"];
  var nextAddress = 0;
  function theNext() {
    if (nextAddress < locations.length) {
      setTimeout('geocodeAddress("'+locations[nextAddress]+'",theNext)', delay);
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
