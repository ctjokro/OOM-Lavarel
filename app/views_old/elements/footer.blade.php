<div class="clear"></div>
<?php
$adminuser = DB::table('site_settings')->first();
// get main pages
$infoPage = DB::table('pages')
        ->where('status', 1)
        ->where('category', "Main")
        ->get();
?>
<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12 fbox one_line">
                <h4>Join Us On</h4>

                <ul class="list-inline big">
                    <li><a href="<?php echo $adminuser->facebook_link ?>"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="<?php echo $adminuser->twitter_link ?>"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="<?php echo $adminuser->instagram_link ?>"><i class="fa fa-instagram"></i></a></li>                        
                </ul>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 fbox">
                <h4>Info</h4>
                <?php if (!empty($infoPage)) {
                    ?>
                    <ul class="big">
                        <?php foreach ($infoPage as $iPage): ?>
                            <li>
                                <?php echo html_entity_decode(HTML::link('/' . $iPage->slug, $iPage->name, array('class' => '', 'title' => $iPage->name))); ?>
                            </li>    
                        <?php endforeach; ?>
                        <li><a href="<?php echo HTTP_PATH . "contactus" ?>">Contact</a></li>
                    </ul>
                <?php } ?>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 fbox">
                <h4>Popular Cities</h4>
                <?php
                //  $cities = City::where('status', "=", "1")->orderBy('name', 'asc')->lists('name', 'id')->take(1);
                $cities = DB::table('cities')
                        ->orderBy('name', 'asc')
                        ->where('status', "=", "1")
                        ->limit(5)
                        ->get();
//                    print_r($cities); exit;
                if (!empty($cities)) {
                    ?><ul class="big"><?php
                    foreach ($cities as $key => $val) {
                        // $cities_array[$key] = ucfirst($val);
                        //echo '<pre>'; print_r($val);
                        ?>
                            <li>
                                <?php echo html_entity_decode(HTML::link('/restaurants/list?city=' . $val->id, $val->name, array('class' => '', 'title' => ""))); ?>
                            </li>     
                            <?php
                        }
                        ?></ul><?php
                }
                ?>


            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 fbox">
                <h4>Contact Us</h4>

                <p><a href="tel:{{$adminuser->phone}}"><i class="fa fa-phone"></i> {{$adminuser->phone}}</a></p>
                <p><a href="mailto:{{$adminuser->mail_from}}"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> {{$adminuser->mail_from}}</a></p>
            </div>
        </div>
    </div>
    <div id="copyright" class="foot">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <p class="pull-left">&copy; <?php echo date('Y'); ?> {{$adminuser->title}}</p>
                </div>
                <div class="col-md-4">
                    <div style="text-align: center;color: #fff" class="_cttv">
                        <span class="_cvv">Powered by 
                        <a  href="https://cloud8s.com.au/" target="_blank">{{ HTML::image(URL::asset('public/img/front/powered-by-logo-home.png'), '', array()) }}</a>
                    </span>
<!--                        
                        <span class="_cvv">{{ html_entity_decode(link_to('https://cloud8s.com.au/', 'Powered by Cloud8s', array('escape' => false,'target'=>'_blank'))) }}</span></div>-->
                </div>
                </div>
                <div class="col-md-4">
                    <ul class="list-inline navbar-right">
                        <li>
                            <?php echo html_entity_decode(HTML::link('/terms_and_conditions', "Terms and Conditions", array('class' => '', 'title' => "Terms and Conditions"))); ?>
                        </li>
                        <li>
                            <?php echo html_entity_decode(HTML::link('/privacy_policy', "Privacy Policy", array('class' => '', 'title' => "Privacy Policy"))); ?>
                        </li>
                        <li>
                            <?php echo html_entity_decode(HTML::link('about', 'About Us', array('class' => (Request::is('about') ? "active" : ""), 'title' => 'About Us'))); ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>        
</footer>

 

<div class="_mbnbc">
    

</div>