<?php

 require_once GMAILCLIENT;
        require_once GMAILOAUTH; 

        $google_client_id = GMAIL_CLIENT_ID;
        $google_client_secret = GMAIL_SECRET;
        $google_redirect_url = GMAILREDIRECT;
        $google_developer_key = GMAIL_DEVELOPER_KEY;

        $gClient = new Google_Client();
        $gClient->setApplicationName('Login to ');
        $gClient->setClientId($google_client_id);
        $gClient->setClientSecret($google_client_secret);
        $gClient->setRedirectUri($google_redirect_url);
        $gClient->setDeveloperKey($google_developer_key);

        $google_oauthV2 = new Google_Oauth2Service($gClient);

        //If user wish to log out, we just unset Session variable
        if (isset($_REQUEST['reset'])) {
            unset($_SESSION['token']);
            $gClient->revokeToken();
            header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
        }

        //Redirect user to google authentication page for code, if code is empty.
        //Code is required to aquire Access Token from google
        //Once we have access token, assign token to session variable
        //and we can redirect user back to page and login.
        if (isset($_GET['code'])) {
            $gClient->authenticate($_GET['code']);
            $_SESSION['token'] = $gClient->getAccessToken();
            header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
            return;
        }


        if (isset($_SESSION['token'])) {
            $gClient->setAccessToken($_SESSION['token']);
        }
        
        if ($gClient->getAccessToken()) {
            //Get user details if user is logged in
            $user = $google_oauthV2->userinfo->get();
            $user_id = $user['id'];
            $user_name = filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
            $profile_image_url = filter_var($user['picture'], FILTER_VALIDATE_URL);
            $personMarkup = "$email<div><img src='$profile_image_url?sz=50'></div>";
            $_SESSION['token'] = $gClient->getAccessToken();
        } else {
            //get google login url
            $authUrl = $gClient->createAuthUrl();
        }


?>


<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyAhID-WwQiiAcVYSOowL84gIPYLybora7g"></script>
<script>
function initialize() {

var input = document.getElementById('address_');
var autocomplete = new google.maps.places.Autocomplete(input);

var location = document.getElementById('location_');
var autocomplete = new google.maps.places.Autocomplete(location);
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>

<div class="all_bg">
    <div class="all_bg_ldr">
        <img src="{{ URL::asset('public/img/front') }}/loader.gif" alt="Please Wait..." />
    </div>
</div>

<!--login popup box-->
<div class="popup fixed-width login-window">
    <div class="ligd">
        <span class="button b-close" popup="login-window">
            <span>X</span>        
        </span>
        <p>Login</p>
        <div class="messages-section"></div>
        {{ Form::open(array('url' => '/user/login', 'method' => 'post', 'id' => 'login-form', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
        <div class="inod">
            {{ Form::text('email_address', Session::get('email_address'), array('id'=>'email_login', 'class' => 'required email form-control','placeholder'=>"Email Address")) }}
        </div>
        <div class="inod">
            
            {{ Form::password('password' ,array( 'id'=>'password_login', 'type'=>'password','class' => 'required form-control','placeholder'=>"Password",'value'=>"sadas"))}}
        </div>
        <div class="modio">
            <div class="inod rigi">
                <a href="javascript:void(0)"  class="popup-box-login" alt="forgotpassword-window">Forgot password?</a>
                <div class="inpdf">
                    <?php echo Form::checkbox('remember_login', '1', (Session::get('email_address') ? TRUE : FALSE), ['class' => 'remem_checkbox', 'id' => 'remember_login']); ?> <label style="cursor: pointer;" for="remember_login">Remember Me</label>
                </div>
            </div>
            <div class="inod">
                {{ Form::submit('Log in', array('class' => "btn btn-primary")) }}
            </div>
        </div>
        <div class="already-buttons">
            Don’t have an account ? <a href="javascript:void(0)"  class="return-signup" alt="signup-customer-window">SIGN UP </a>
        </div>
        {{ Form::close() }}
    </div>
</div>
<!--end-popup box-->

<!--------------------------------------------------------------------------------------->

<!--signup popup box carters-->
<div class="popup fixed-width signup-window">
    <div class="ligd">
        <span class="button b-close" popup="signup-window">
            <span>X</span>        
        </span>
        <p>Restaurant Signup</p>
        {{ Form::open(array('url' => '/user/caterer_contact', 'method' => 'post', 'id' => 'signup-form', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
        <div class="messages-section"></div>
        <div class="inod">
            {{ Form::text('name', "", array('placeholder'=>'Name of the Restaurant *', 'id'=>'name', 'class' => 'required form-control','maxlength'=>'40')) }}
        </div>
        <div class="inod">
            {{ Form::text('email_address', "", array('id'=>'email', 'placeholder'=>'Email Address *', 'class' => 'required email form-control','maxlength'=>'254')) }}
        </div>
        
        <div class="inod">
            {{  Form::password('new_password',  array('placeholder'=>'Password *', 'type'=>'password','class' => 'required pass form-control','minlength' => 8, 'maxlength' => '40','id'=>"password"))}}
            <p class="help-block-popup"> Password minimum length must be 8 charaters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.</p>
        </div>
        <div class="inod">
            {{ Form::password('confirm_password',  array('placeholder'=>'Confirm Password *', 'type'=>'password','class' => 'required form-control','maxlength' => '40', 'equalTo' => '#password')) }}
        </div>
        <div class="inod">
            {{ Form::text('location', "", array('placeholder'=>'Address *','id'=>'location' , 'class' => 'required form-control','maxlength'=>'40')) }} 
        </div>
        <div class="inod">
            <?php
            $cities_array = array(
                '' => 'City'
            );

            // get Cairo city id
            $city_id = City::where('name', "like", "%Cairo%")->lists('id');
            $city_id = isset($city_id[0]) ? $city_id[0] : "";

            $cities = City::where('status', "=", "1")->orderBy('name', 'asc')->lists('name', 'id');
            if (!empty($cities)) {
                foreach ($cities as $key => $val)
                    $cities_array[$key] = ucfirst($val);
            }
            ?>
            {{ Form::select('city', $cities_array, $city_id, array('class' => 'required form-control city_caterer')) }}
        </div> 
        <div class="inod">
            <?php
            $area_array = array(
                '' => 'Area'
            );
            $area = Area::orderBy('name', 'asc')->where('status', "=", "1")->where('city_id', "=", $city_id)->lists('name', 'id');
            if (!empty($area)) {
                foreach ($area as $key => $val)
                    $area_array[$key] = ucfirst($val);
            }
            ?>
            {{ Form::select('area', $area_array, Input::old('area'), array('class' => 'required form-control area_caterer')) }}
        </div> 
<!--        <div class="inod">
            <?php
        /*    $area_array = array(
            );
            $area = Area::orderBy('name', 'asc')->where('status', "=", "1")->lists('name', 'id');
            if (!empty($area)) {
                foreach ($area as $key => $val)
                    $area_array[$key] = ucfirst($val);
            } */
            ?>
            {{ Form::select('deliver_to[]', $area_array, Input::old('deliver_to'), array('multiple' => true,'data-placeholder'=>'Deliver to *', 'class' => 'chzn-select required form-control deliver_to')) }}
        </div> -->
        <div class="inod">
            
            <select class='form-control' name="timezone" id="timezone">
                                    <option>Select TimeZone</option>
                                    <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
                                    <option value="America/Adak">(GMT-10:00) Hawaii-Aleutian</option>
                                    <option value="Etc/GMT+10">(GMT-10:00) Hawaii</option>
                                    <option value="Pacific/Marquesas">(GMT-09:30) Marquesas Islands</option>
                                    <option value="Pacific/Gambier">(GMT-09:00) Gambier Islands</option>
                                    <option value="America/Anchorage">(GMT-09:00) Alaska</option>
                                    <option value="America/Ensenada">(GMT-08:00) Tijuana, Baja California</option>
                                    <option value="Etc/GMT+8">(GMT-08:00) Pitcairn Islands</option>
                                    <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
                                    <option value="America/Denver">(GMT-07:00) Mountain Time (US & Canada)</option>
                                    <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                                    <option value="America/Dawson_Creek">(GMT-07:00) Arizona</option>
                                    <option value="America/Belize">(GMT-06:00) Saskatchewan, Central America</option>
                                    <option value="America/Cancun">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                                    <option value="Chile/EasterIsland">(GMT-06:00) Easter Island</option>
                                    <option value="America/Chicago">(GMT-06:00) Central Time (US & Canada)</option>
                                    <option value="America/New_York">(GMT-05:00) Eastern Time (US & Canada)</option>
                                    <option value="America/Havana">(GMT-05:00) Cuba</option>
                                    <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                                    <option value="America/Caracas">(GMT-04:30) Caracas</option>
                                    <option value="America/Santiago">(GMT-04:00) Santiago</option>
                                    <option value="America/La_Paz">(GMT-04:00) La Paz</option>
                                    <option value="Atlantic/Stanley">(GMT-04:00) Faukland Islands</option>
                                    <option value="America/Campo_Grande">(GMT-04:00) Brazil</option>
                                    <option value="America/Goose_Bay">(GMT-04:00) Atlantic Time (Goose Bay)</option>
                                    <option value="America/Glace_Bay">(GMT-04:00) Atlantic Time (Canada)</option>
                                    <option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
                                    <option value="America/Araguaina">(GMT-03:00) UTC-3</option>
                                    <option value="America/Montevideo">(GMT-03:00) Montevideo</option>
                                    <option value="America/Miquelon">(GMT-03:00) Miquelon, St. Pierre</option>
                                    <option value="America/Godthab">(GMT-03:00) Greenland</option>
                                    <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
                                    <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
                                    <option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
                                    <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
                                    <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
                                    <option value="Europe/Belfast">(GMT) Greenwich Mean Time : Belfast</option>
                                    <option value="Europe/Dublin">(GMT) Greenwich Mean Time : Dublin</option>
                                    <option value="Europe/Lisbon">(GMT) Greenwich Mean Time : Lisbon</option>
                                    <option value="Europe/London">(GMT) Greenwich Mean Time : London</option>
                                    <option value="Africa/Abidjan">(GMT) Monrovia, Reykjavik</option>
                                    <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                                    <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
                                    <option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                                    <option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
                                    <option value="Africa/Windhoek">(GMT+01:00) Windhoek</option>
                                    <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
                                    <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
                                    <option value="Asia/Gaza">(GMT+02:00) Gaza</option>
                                    <option value="Africa/Blantyre">(GMT+02:00) Harare, Pretoria</option>
                                    <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                                    <option value="Europe/Minsk">(GMT+02:00) Minsk</option>
                                    <option value="Asia/Damascus">(GMT+02:00) Syria</option>
                                    <option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
                                    <option value="Africa/Addis_Ababa">(GMT+03:00) Nairobi</option>
                                    <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
                                    <option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
                                    <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
                                    <option value="Asia/Kabul">(GMT+04:30) Kabul</option>
                                    <option value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg</option>
                                    <option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
                                    <option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                                    <option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
                                    <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
                                    <option value="Asia/Novosibirsk">(GMT+06:00) Novosibirsk</option>
                                    <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
                                    <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                                    <option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
                                    <option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                                    <option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
                                    <option value="Australia/Perth">(GMT+08:00) Australia,Perth</option>
                                    <option value="Australia/Eucla">(GMT+08:45) Australia,Eucla</option>
                                    <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                                    <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
                                    <option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
                                    <option value="Australia/Adelaide">(GMT+09:30) Australia,Adelaide</option>
                                    <option value="Australia/Darwin">(GMT+09:30) Australia,Darwin</option>
                                    <option value="Australia/Brisbane">(GMT+10:00) Australia,Brisbane</option>
                                    <option value="Australia/Hobart">(GMT+10:00) Australia,Hobart</option>
                                    <option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
                                    <option value="Australia/Lord_Howe">(GMT+10:30) Australia,Lord Howe Island</option>
                                    <option value="Etc/GMT-11">(GMT+11:00) Solomon Is., New Caledonia</option>
                                    <option value="Asia/Magadan">(GMT+11:00) Magadan</option>
                                    <option value="Pacific/Norfolk">(GMT+11:30) Norfolk Island</option>
                                    <option value="Asia/Anadyr">(GMT+12:00) Anadyr, Kamchatka</option>
                                    <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
                                    <option value="Etc/GMT-12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                                    <option value="Pacific/Chatham">(GMT+12:45) Chatham Islands</option>
                                    <option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
                                    <option value="Pacific/Kiritimati">(GMT+14:00) Kiritimati</option>
                                </select>
            
        </div>
        <div class="inod">
            
            <select class='form-control' name="currency" id="currency">
                                    <option>Select Currency</option>
                                    <option value="USD" >United States Dollars ($)</option>
                                    	<option value="EUR">Euro (€)</option>
                                    	<option value="GBP">United Kingdom Pounds (£)</option>
                                    	<option value="DZD">Algeria Dinars (دج)</option>
                                    	<option value="ARP">Argentina Pesos ($)</option>
                                    	<option value="AUD">Australia Dollars ($)</option>
                                    	<option value="ATS">Austria Schillings (öS)</option>
                                    	<option value="BSD">Bahamas Dollars (B$)</option>
                                    	<option value="BBD">Barbados Dollars (Bds$)</option>
                                    	<option value="BEF">Belgium Francs (fr.)</option>
                                    	<option value="BMD">Bermuda Dollars ($)</option>
                                    	<option value="BRR">Brazil Real (R$)</option>
                                    	<option value="BGL">Bulgaria Lev (Лв.)</option>
                                    	<option value="CAD">Canada Dollars (Can$)</option>
                                    	<option value="CLP">Chile Pesos ($)</option>
                                    	<option value="CNY">China Yuan Renmimbi (¥)</option>
                                    	<option value="CYP">Cyprus Pounds (£)</option>
                                    	<option value="CSK">Czech Republic Koruna (Kč)</option>
                                    	<option value="DKK">Denmark Kroner (Kr.)</option>
                                    	<option value="NLG">Dutch Guilders (ƒ)</option>
                                    	<option value="XCD">Eastern Caribbean Dollars ($)</option>
                                    	<option value="EGP">Egypt Pounds (E£)</option>
                                    	<option value="FJD">Fiji Dollars (FJ$)</option>
                                    	<option value="FIM">Finland Markka (mk)</option>
                                    	<option value="FRF">France Francs (₣)</option>
                                    	<option value="DEM">Germany Deutsche Marks (DM)</option>
                                    	<option value="XAU">Gold Ounces (XAU)</option>
                                    	<option value="GRD">Greece Drachmas</option>
                                    	<option value="HKD">Hong Kong Dollars (HK$)</option>
                                    	<option value="HUF">Hungary Forint (Ft)</option>
                                    	<option value="ISK">Iceland Krona (Íkr)</option>
                                    	<option value="INR">India Rupees (₹)</option>
                                    	<option value="IDR">Indonesia Rupiah (Rp)</option>
                                    	<option value="IEP">Ireland Punt (£)</option>
                                    	<option value="ILS">Israel New Shekels (₪)</option>
                                    	<option value="ITL">Italy Lira (₤)</option>
                                    	<option value="JMD">Jamaica Dollars ($)</option>
                                    	<option value="JPY">Japan Yen (¥)</option>
                                    	<option value="JOD">Jordan Dinar (د.ا)</option>
                                    	<option value="KRW">Korea (South) Won (₩)</option>
                                    	<option value="LBP">Lebanon Pounds (ل.ل)</option>
                                    	<option value="LUF">Luxembourg Francs (F)</option>
                                    	<option value="MYR">Malaysia Ringgit (RM)</option>
                                    	<option value="MXP">Mexico Pesos ($)</option>
                                    	<option value="NLG">Netherlands Guilders (ƒ)</option>
                                    	<option value="NZD">New Zealand Dollars ($)</option>
                                    	<option value="NOK">Norway Kroner (kr)</option>
                                    	<option value="PKR">Pakistan Rupees (₨)</option>
                                    	<option value="XPD">Palladium Ounces</option>
                                    	<option value="PHP">Philippines Pesos (₱)</option>
                                    	<option value="XPT">Platinum Ounces (₱)</option>
                                    	<option value="PLZ">Poland Zloty (zł)</option>
                                    	<option value="PTE">Portugal Escudo</option>
                                    	<option value="ROL">Romania Leu (lei)</option>
                                    	<option value="RUR">Russia Rubles (RUB)</option>
                                    	<option value="SAR">Saudi Arabia Riyal (ر.س)</option>
                                    	<option value="XAG">Silver Ounces (XAG)</option>
                                    	<option value="SGD">Singapore Dollars ($)</option>
                                    	<option value="SKK">Slovakia Koruna (Sk)</option>
                                    	<option value="ZAR">South Africa Rand (R)</option>
                                    	<option value="ESP">Spain Pesetas (Pts)</option>
                                    	<option value="XDR">Special Drawing Right (IMF)</option>
                                    	<option value="SDD">Sudan Dinar (£Sd)</option>
                                    	<option value="SEK">Sweden Krona (kr)</option>
                                    	<option value="CHF">Switzerland Francs (SFr.)</option>
                                    	<option value="TWD">Taiwan Dollars ($)</option>
                                    	<option value="THB">Thailand Baht</option>
                                    	<option value="TTD">Trinidad and Tobago Dollars (฿)</option>
                                    	<option value="TRL">Turkey Lira (₺)</option>
                                    	<option value="VEB">Venezuela Bolivar (Bs.F.)</option>
                                    	<option value="ZMK">Zambia Kwacha (ZK)</option>
                                    	<option value="EUR">Euro (€)</option>
                                    	<option value="XCD">Eastern Caribbean Dollars ($)</option>
                                    	<option value="XDR">Special Drawing Right (IMF)</option>
                                    	<option value="XAG">Silver Ounces (XAG)</option>
                                    	<option value="XAU">Gold Ounces (XAU)</option>
                                    	<option value="XPD">Palladium Ounces (XPD)</option>
                                    	<option value="XPT">Platinum Ounces (XPT)</option>
                                </select>
        </div>
        <div class="inod">
            {{ Form::text('contact_number', "", array('placeholder'=>'Contact Number *', 'id'=>'contact_number', 'class' => 'contact required number form-control','maxlength'=>'16')) }}
        </div> 
        <div class="inod">
            {{ Form::text('paypal_email_address', "", array('id'=>'paypal_email', 'placeholder'=>'Paypal Email Address *', 'class' => 'required email form-control','maxlength'=>'254')) }}
            <p class="help-block-popup"> This is use to transfer amount in your paypal account.</p>
        </div>
        <div class="inod">
            {{ Form::text('unique_name', "", array('id'=>'unique_name', 'placeholder'=>'Unique Name *', 'class' => 'required validname form-control','maxlength'=>'254')) }}
            <p class="help-block-popup"> This is use to redirect on restaurant url.</p>
        </div>
       <!-- <div class="inod">
            <input type="file" name="restaurant_logo" id="restaurant_logo" class="required form-control">
            <p class="help-block" style="float: left;color:red;">Supported logo Types: jpg, jpeg, png.</p>
        </div>-->
<!--        <div class="inod">
            {{ Form::textarea('message', "", array('id'=>'message', 'placeholder'=>'Message *', 'class' => 'required form-control','maxlength'=>'254')) }}
        </div>       -->
        <input type="hidden" value="" id="hiddenbox"/>
        <div class="inpdf_terms">
            <?php echo Form::checkbox('rest_signup_checked', '1', FALSE, ['class' => 'required remem_checkbox cccb', 'id' => 'rest_signup_cb']); ?> 
            <span>  By creating an account, you agree to our <a href="javascript:void(0)" class="term_window" title="Copyright Policy" >terms & conditions</a> </span> 
        </div>
        <div class="inod">
            {{ Form::submit('Submit', array('class' => "btn btn-primary")) }}
            {{ Form::reset('Reset', array('class'=>"btn btn-default")) }}
        </div>
        <div class="already-buttons">
            Already have an account ? <a href="javascript:void(0)"  class="return-login" alt="login-window">LOGIN </a>
        </div>
        {{ Form::close() }}
    </div>
</div>
<!--end-popup box-->


<!--signup popup box user-->
<div class="popup fixed-width signup-customer-window">
    <div class="ligd">
        <span class="button b-close" popup="signup-customer-window">
            <span>X</span>        
        </span>
        <p>Signup</p>
        {{ Form::open(array('url' => '/user/customersignup', 'method' => 'post', 'id' => 'signup-customer-form', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
        <div class="messages-section"></div>
        <div class="inod">
            {{ Form::text('first_name', "", array('placeholder'=>'First Name *', 'id'=>'first_name', 'class' => 'required form-control','maxlength'=>'40')) }}
        </div>
        <div class="inod">
            {{ Form::text('last_name', "", array('placeholder'=>'Last Name *','id'=>'last_name' , 'class' => 'required form-control','maxlength'=>'40')) }} 
        </div>
        <div class="inod">
            {{ Form::text('email_address', "", array('id'=>'email_customer', 'placeholder'=>'Email Address *', 'class' => 'required email form-control','maxlength'=>'254')) }}
        </div>
        <div class="inod">
            {{  Form::password('new_password',  array('placeholder'=>'Password *', 'type'=>'password','class' => 'required pass form-control','minlength' => 8, 'maxlength' => '40','id'=>"password_customer"))}}
            <p class="help-block-popup"> Password minimum length must be 8 charaters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.</p>
        </div>
        <div class="inod">
            {{ Form::password('confirm_password',  array('placeholder'=>'Confirm Password *', 'type'=>'password','class' => 'required form-control','maxlength' => '40', 'equalTo' => '#password_customer')) }}
        </div>
        <div class="inod">
            <?php
            $cities_array = array(
                '' => 'City'
            );

            // get Cairo city id
            $city_id = City::where('name', "like", "%Cairo%")->lists('id');
            $city_id = isset($city_id[0]) ? $city_id[0] : "";

            $cities = City::where('status', "=", "1")->orderBy('name', 'asc')->lists('name', 'id');
            if (!empty($cities)) {
                foreach ($cities as $key => $val)
                    $cities_array[$key] = ucfirst($val);
            }
            ?>
            {{ Form::select('city', $cities_array, $city_id, array('class' => 'required form-control city')) }}
        </div> 
        <div class="inod">
            <?php
            $area_array = array(
                '' => 'Area'
            );
            $area = Area::orderBy('name', 'asc')->where('city_id', "=", $city_id)->lists('name', 'id');
            if (!empty($area)) {
                foreach ($area as $key => $val)
                    $area_array[$key] = ucfirst($val);
            }
            ?>
            {{ Form::select('area', $area_array, Input::old('area'), array('class' => 'required form-control area')) }}
        </div>
        <div class="inod">
            {{ Form::text('postal_code', "", array('placeholder'=>'Postal Code *', 'id'=>'postal_code', 'class' => 'contact required number form-control')) }}
        </div>
        <div class="inod">
            {{ Form::text('address', "", array('id'=>'address', 'placeholder'=>'Address *', 'class' => 'required form-control','maxlength'=>'500')) }}
        </div> 
        <div class="inod">
            {{ Form::text('country', "", array('placeholder'=>'Country *', 'id'=>'country', 'class' => 'required  form-control')) }}
        </div>
        <div class="inod">
            {{ Form::text('contact_number', "", array('placeholder'=>'Mobile Number *', 'id'=>'mobile_number', 'class' => 'contact required number form-control','maxlength'=>'16')) }}
        </div> 
        <input type="hidden" value="" id="hiddenbox"/>
        <div class="inpdf_terms">
            <?php echo Form::checkbox('cust_signup_checked', '1', FALSE, ['class' => 'required remem_checkbox  cccb', 'id' => 'cust_signup_cb']); ?>
            <span> By creating an account, you agree to our <a href="javascript:void(0)" class="term_window" title="Copyright Policy" >terms & conditions</a> </span> 
        </div>
        <div class="inod">
            {{ Form::submit('Submit', array('class' => "btn btn-primary")) }}
            {{ Form::reset('Reset', array('class'=>"btn btn-default")) }}
        </div>
        {{ Form::close() }}
        
        <div class="already-buttons">
            Already have an account ? <a href="javascript:void(0)"  class="return-login" alt="login-window">LOGIN </a>
        </div>
        
        
<!--        <div class="connect-social">
            <h2>Connect with your Social Network</h2>
            <ul>
                <li><a href="javascript:void(0)" class="facebookbtn" onclick="login()"><i class="fa fa-facebook"></i></a></li>
                <li><a href="javascript:void(0)" class="facebookbtn" onclick="connect_with_gmail()"><i class="fa fa-google"></i></a></li>
            </ul>
        </div>-->
        
        
    </div>
</div>
<!--end-popup box-->

<!--------------------------------------------------------------------------------------->

<!--reset password popup box-->
<div class="popup fixed-width forgotpassword-window">
    <div class="ligd">
        <span class="button b-close"popup="forgotpassword-window">
            <span>X</span>        
        </span>
        <p>Forgot password</p>
        <div class="messages-section"></div>
        {{ Form::open(array('url' => '/user/forgotpassword', 'method' => 'post', 'id' => 'forgotpassword-form', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
        <div class="inod">
            {{ Form::text('email_address', "", array('id'=>'email_forgotpassword', 'class' => 'required email form-control','placeholder'=>"Email *")) }}
        </div>
        <span class="hint">  Enter your email and we will send you a link to reset your password. </span>
        <div class="inod">
            {{ Form::submit('Submit', array('class' => "btn btn-primary")) }}
        </div>
        <div class="inod">
            <a href="javascript:void(0)"  class="popup-box-login-password" alt="login-window"> <i class="fa fa-arrow-left"></i> Back to login</a>
        </div>
        {{ Form::close() }}
    </div>
</div>
<!--end-popup box-->

<!--contact seller popup box-->
<div class="popup fixed-width seller-contact-popup">
    <div class="ligd">
        <span class="button b-close" popup="seller-contact-popup">
            <span>X</span>        
        </span>
        <p>Contact Restaurant</p>
        <div class="messages-section"></div>
        {{ Form::open(array('url' => '/user/contactcaterer', 'method' => 'post', 'id' => 'contact-form', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
        <div class="inod">
            {{ Form::textarea('contact_message', "", array('id'=>'contact_message', 'class' => 'required form-control','placeholder'=>"Message *")) }}
        </div>
        <span class="hint"> Please write a message for issue regarding your order. </span>
        <div class="inod">
            {{ Form::submit('Contact', array('class' => "btn btn-primary")) }} 
        </div>
        {{ Form::close() }}
    </div>
</div>
<!--end-contact seller popup box-->

<!--------------------------------------------------------------------------------------->

<!--success message box popup box-->
<div class="popup fixed-width success-message-box">
    <div class="ligd">
        <ul class="message success no-margin">
            <li class="forgot-pass-message">

            </li>
        </ul>

    </div>
</div>
<!--end-popup box-->

<!--------------------------------------------------------------------------------------->

<!--error message box popup box-->
<div class="popup fixed-width error-message-box">
    <div class="ligd">
        <ul class="message error no-margin">
            <li>
                <?php
//                echo $this->session->userdata('message');
//                echo $this->session->flashdata('message');
//                $this->session->unset_userdata('message');
                ?>
            </li>
        </ul>
    </div>
</div>


<div class="popup terms-popup auto_width full-width-image">
    <div class="ligd">
        <span class="button b-close"popup="terms-popup">
            <span>X</span>        
        </span>   

        <div class="loader-image-box"> 
            <img src="{{ URL::asset('public/img/front') }}/loader.gif" alt="Please Wait..." />
        </div>   
        <div class="ajax-terms-content">   

        </div>
        <div class="clr"></div>
    </div>
</div>

{{ HTML::style('public/js/chosen/chosen.css'); }}
<!--scripts page-->
{{ HTML::script('public/js/chosen/chosen.jquery.js'); }}
<script type="text/javascript">
    $(".chzn-select").chosen();
</script>
<style>
    .chzn-container-multi .chzn-choices {
        min-height: 42px;
        width: 97%;
    }
    .chzn-container-multi .chzn-choices .search-field input {
        height: 32px;
    }
</style>

<script>
    $(document).ready(function () {
        $(".city").change(function () {
            $(".area").load("<?php echo HTTP_PATH . "customer/loadarea/" ?>" + $(this).val() + "/0");
        })

        $(".city_caterer").change(function () {
            $(".area_caterer").load("<?php echo HTTP_PATH . "customer/loadarea/" ?>" + $(this).val() + "/0");
        })

    });
    $(document).on("click", ".b-close", function () {
        var popup = $(this).attr("popup");
        $('.' + popup).bPopup().close();
    })
    $(document).ready(function ()
    {

<?php if (Request::is('/')) { ?>
    <?php
    if (Session::has('error_message')) {
        ?>

                swal({
                    title: "Sorry!",
                    text: "<?php echo Session::get('error_message'); ?>",
                    type: "error",
                    html: true
                });
        <?php
    }
    if (Session::has('success_message')) {
        ?>
                swal({
                    title: "",
                    text: "<?php echo Session::get('success_message') ?>",
                    type: "success",
                    html: true
                });
        <?php
    }
    Session::forget('error_message');
    Session::forget('success_message');
}
?>

$.validator.addMethod("validname", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9_]+$/.test(value);
        }, "*Note: Special characters and spaces are not allowed.");
        $.validator.addMethod("contact", function (value, element) {
            return  this.optional(element) || (/^[0-9-]+$/.test(value));
        }, "Contact Number is not valid.");

        $.validator.addMethod("pass", function (value, element) {
            return  this.optional(element) || (/.{8,}/.test(value) && /([0-9].*[a-z])|([a-z].*[0-9])/.test(value));
        }, "Password minimum length must be 8 charaters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.");

        // We'll catch form submission to do it in AJAX, but this works also with JS disabled    
        $('#signup-form').submit(function (event)
        {
            //var datastring = $("#signup-form").serialize();
            
            var form = $('#signup-form');
            // Stop full page load
            if (form.valid()) {
                event.preventDefault();
                // Check fields
                var submitBt = $(this).find('input[type=submit]');
                submitBt.disableBt();
                // Target url
                var target = $(this).attr('action');
                if (!target || target == '')
                {
                    // Page url without hash
                    target = document.location.href.match(/^([^#]+)/)[1];
                }

                // Request
                /*var data = {
                    name: $('#name').val(),
                    location: $('#location').val(),
                    contact_number: $('#contact_number').val(),
                    email_address: $('#email').val(),
                    message: $('#message').val(),
                    city: $('.city_caterer').val(),
                    area: $('.area_caterer').val(),
                    deliver_to: $('.deliver_to').val(),
                    paypal_email_address: $('#paypal_email').val(),
                    unique_name: $('#unique_name').val(),
                    password: $('#password').val(),
                    currency: $(this).find('input[name=currency]').val(),
                    timezone: $(this).find('input[name=timezone]').val(),
                },
                redirect = $('#redirect'),
                        sendTimer = new Date().getTime();
                if (redirect.length > 0)
                {
                    data.redirect = redirect.val();
                }*/

                // Send
                $.ajax({
                    url: target,
                    dataType: 'json',
                    type: 'POST',
                    data: $("#signup-form").serialize(),
                    success: function (data, textStatus, XMLHttpRequest)
                    {
                        if (data.valid)
                        {
                            // return success message in popup
                            swal({
                                title: "",
                                text: data.message,
                                redirect: data.redirect,
                                type: "success",
                                html: true,
                            },
                                    function () {
                                        window.location.href = data.redirect;
                                    });
                            form.trigger("reset");
                        }
                        else
                        {
                            swal({
                                title: "Sorry!",
                                text: data.message,
                                type: "error",
                                html: true
                            });
                        }
                        submitBt.enableBt();
                        $(".all_bg").hide();
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown)
                    {

                        // Message
                        swal({
                            title: "Sorry!",
                            text: "Error while contacting server, please try again",
                            type: "error",
                            html: true
                        });
                        submitBt.enableBt();
                        $(".all_bg").hide();
                    }
                });
                // Message
                $(".all_bg").show();
            } else {
                event.preventDefault();
            }
        });

        $('#signup-customer-form').submit(function (event)
        {
            var form = $('#signup-customer-form');
            // Stop full page load
            if (form.valid()) {
                event.preventDefault();
                // Check fields
                var submitBt = $(this).find('input[type=submit]');
                submitBt.disableBt();
                // Target url
                var target = $(this).attr('action');
                if (!target || target == '')
                {
                    // Page url without hash
                    target = document.location.href.match(/^([^#]+)/)[1];
                }

                // Request
                var data = {
                    first_name: $('#first_name').val(),
                    last_name: $('#last_name').val(),
                    location: $('#location').val(),
                    contact_number: $('#mobile_number').val(),
                    email_address: $('#email_customer').val(),
                    password: $('#password_customer').val(),
                    city: $('.city').val(),
                    area: $('.area').val(),
                    address: $('#address').val(),
                    postal_code: $('#postal_code').val(),
                    country: $('#country').val()
                },
                        redirect = $('#redirect'),
                        sendTimer = new Date().getTime();
                if (redirect.length > 0)
                {
                    data.redirect = redirect.val();
                }

                // Send
                $.ajax({
                    url: target,
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function (data, textStatus, XMLHttpRequest)
                    {
                        if (data.valid)
                        {
                            // return success message in popup
                            swal({
                                title: "",
                                text: data.message,
                                redirect: data.redirect,
                                type: "success",
                                html: true,
                            },
                                    function () {
                                        window.location.href = data.redirect;
                                    });
                            form.trigger("reset");
                        }
                        else
                        {
                            swal({
                                title: "Sorry!",
                                text: data.message,
                                type: "error",
                                html: true
                            });
                        }
                        submitBt.enableBt();
                        $(".all_bg").hide();
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown)
                    {

                        // Message
                        swal({
                            title: "Sorry!",
                            text: "Error while contacting server, please try again",
                            type: "error",
                            html: true
                        });
                        submitBt.enableBt();
                        $(".all_bg").hide();
                    }
                });
                // Message
                $(".all_bg").show();
            } else {
                event.preventDefault();
            }
        });

        $('#login-form').submit(function (event)
        {
            var form = $('#login-form');
            // Stop full page load
            if (form.valid()) {
                event.preventDefault();
                // Check fields 
                var submitBt = $(this).find('input[type=submit]');
                submitBt.disableBt();
                // Target url
                var target = $(this).attr('action');
                if (!target || target == '')
                {
                    // Page url without hash
                    target = document.location.href.match(/^([^#]+)/)[1];
                }

                // Request
                var data = {
                    email: $('#email_login').val(),
                    password: $('#password_login').val(),
                    remember: ($('#remember_login').val()) ? 1 : 0
                },
                redirect = $('#redirect'),
                        sendTimer = new Date().getTime();
                if (redirect.length > 0)
                {
                    //                    data.redirect = redirect.val();
                    location.reload();
                }

                // Send
                $.ajax({
                    url: target,
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function (data, textStatus, XMLHttpRequest)
                    {
                        
                        if (data.valid)
                        {
                            document.location.href = data.redirect;
                        }
                        else
                        {
                            // Message
                            swal({
                                title: "Sorry!",
                                text: data.message,
                                type: "error",
                                html: true
                            });
                            $(".all_bg").hide();
                            submitBt.enableBt();
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        // Message
                        swal({
                            title: "Sorry!",
                            text: "Error while contacting server, please try again",
                            type: "error",
                            html: true
                        });
                        $(".all_bg").hide();
                        submitBt.enableBt();
                    }
                }
                );
                // Message
                $(".all_bg").show();
            } else {
                event.preventDefault();
            }
        });
        $('#forgotpassword-form').submit(function (event)
        {
            var form = $('#forgotpassword-form');
            // Stop full page load
            if (form.valid()) {
                event.preventDefault();
                // Check fields
                var submitBt = $(this).find('input[type=submit]');
                submitBt.disableBt();
                // Target url
                var target = $(this).attr('action');
                if (!target || target == '')
                {
                    // Page url without hash
                    target = document.location.href.match(/^([^#]+)/)[1];
                }

                // Request
                var data = {
                    email: $('#email_forgotpassword').val()
                },
                redirect = $('#redirect'),
                        sendTimer = new Date().getTime();
                if (redirect.length > 0)
                {
                    data.redirect = redirect.val();
                }

                // Send
                $.ajax({
                    url: target,
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function (data, textStatus, XMLHttpRequest)
                    {
                        if (data.valid)
                        {
                            $(".all_bg").hide();
                            $('.forgotpassword-window').bPopup().close();
                            swal({
                                title: "Thank you",
                                text: "Link has been sent to your email address to reset your password.",
                                type: "success",
                                html: true
                            });
                            submitBt.enableBt();
                            $('#email_forgotpassword').val("");
                        }
                        else
                        {
                            // Message
                            swal({
                                title: "Sorry!",
                                text: data.message,
                                type: "error",
                                html: true
                            });
                            submitBt.enableBt();
                            $(".all_bg").hide();
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown)
                    {

                        // Message
                        swal({
                            title: "Sorry!",
                            text: "Error while contacting server, please try again",
                            type: "error",
                            html: true
                        });
                        submitBt.enableBt();
                        $(".all_bg").hide();
                    }
                });
                // Message
                $(".all_bg").show();
            } else {
                event.preventDefault();
            }
        });


        $('#contact-form').submit(function (event)
        {
            var form = $('#contact-form');
            // Stop full page load
            if (form.valid()) {
                event.preventDefault();
                // Check fields
                var submitBt = $(this).find('input[type=submit]');
                submitBt.disableBt();
                // Target url
                var target = $(this).attr('action');
                if (!target || target == '')
                {
                    // Page url without hash
                    target = document.location.href.match(/^([^#]+)/)[1];
                }

                // Request
                var data = {
                    message: $('#contact_message').val(),
                    order_id: $('#order_id').val()
                },
                redirect = $('#redirect'),
                        sendTimer = new Date().getTime();
                if (redirect.length > 0)
                {
                    data.redirect = redirect.val();
                }

                // Send
                $.ajax({
                    url: target,
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function (data, textStatus, XMLHttpRequest)
                    {
                        if (data.valid)
                        {
                            $(".all_bg").hide();
                            $('.seller-contact-popup').bPopup().close();
                            swal({
                                title: "Thank you",
                                text: data.message,
                                type: "success",
                                html: true
                            });
                            submitBt.enableBt();
                            $('#message').val("");
                        }
                        else
                        {
                            // Message
                            swal({
                                title: "Sorry!",
                                text: data.message,
                                type: "error",
                                html: true
                            });
                            submitBt.enableBt();
                            $(".all_bg").hide();
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown)
                    {

                        // Message
                        swal({
                            title: "Sorry!",
                            text: "Error while contacting server, please try again",
                            type: "error",
                            html: true
                        });
                        submitBt.enableBt();
                        $(".all_bg").hide();
                    }
                });
                // Message
                $(".all_bg").show();
            } else {
                event.preventDefault();
            }
        });
        $('#reset-form').submit(function (event)
        {
            var form = $('#reset-form');
            // Stop full page load
            if (form.valid()) {
                event.preventDefault();
                // Check fields
                var submitBt = $(this).find('input[type=submit]');
                submitBt.disableBt();
                // Target url
                var target = $(this).attr('action');
                if (!target || target == '')
                {
                    // Page url without hash
                    target = document.location.href.match(/^([^#]+)/)[1];
                }

                // Request
                var data = {
                    password: $('#password_reset').val(),
                    cnpassword: $('#reset_password').val()
                },
                redirect = $('#redirect'),
                        sendTimer = new Date().getTime();
                if (redirect.length > 0)
                {
                    data.redirect = redirect.val();
                }

                // Send
                $.ajax({
                    url: target,
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function (data, textStatus, XMLHttpRequest)
                    {
                        if (data.valid)
                        {
                            document.location.href = data.redirect;
                        }
                        else
                        {
                            // Message
                            swal({
                                title: "Sorry!",
                                text: data.message,
                                type: "error",
                                html: true
                            });
                            submitBt.enableBt();
                            $(".all_bg").hide();
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        // Message
                        swal({
                            title: "Sorry!",
                            text: "Error while contacting server, please try again",
                            type: "error",
                            html: true
                        });
                        submitBt.enableBt();
                        $(".all_bg").hide();
                    }
                });
                // Message
                $(".all_bg").show();
            } else {
                event.preventDefault();
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(".all_bg").hide();
        $(".popup-box-login-password").click(function () {
            $(".all_bg").hide();
            $('.forgotpassword-window').bPopup().close();
            var popup_box = $(this).attr('alt');
            $('.' + popup_box).bPopup({
                easing: 'easeOutBack', //uses jQuery easing plugin
                speed: 700,
                modalColor: "#7C7C7C",
                modalClose: true,
                transition: 'fadeIn',
                transitionClose: "slideIn",
                onClose: function () {
                    $("#email_login").val("");
                    $("#password_login").val("");
                    $("#remember_login").removeAttr("checked");
                    $(".messages-section").html("");
                    $("#first_name").val("");
                    $("#last_name").val("");
                    $("#email").val("");
                    $("#password").val("");
                    $("#remember").removeAttr("checked");
                    $("#email_forgotpassword").val("");
                    $("#password_reset").val("");
                    $("#reset_password").val("");
                },
                onOpen: function () {
                    $('.abs').hide();
                    $('.swap_image').attr('src', '<?php echo HTTP_PATH . "img/front/by_blur.png" ?>');
                }
            },
            function () {
                $('.abs').hide();
                $('.swap_image').attr('src', '<?php echo HTTP_PATH . "img/front/by_blur.png" ?>');
            });
        });
        $(".popup-box-reset").click(function () {
            $(".all_bg").hide();
            $('.success-message-box').bPopup().close();
            $('.error-message-box').bPopup().close();
            var popup_box = $(this).attr('alt');
            $('.' + popup_box).bPopup({
                easing: 'easeOutBack', //uses jQuery easing plugin
                speed: 700,
                modalClose: true,
                modalColor: "#7C7C7C",
                transition: 'fadeIn',
                transitionClose: "slideIn",
            });
        })
        $(".popup-box-login").click(function () {
            $(".all_bg").hide();
//            alert();
//            $('.buttonn').removeClass('menu-opened');
//             $('.open').hide('open');
//            $('.open').removeClass('open');
           
            
            $('.login-window').bPopup().close();
            var popup_box = "forgotpassword-window";
            $('.' + popup_box).bPopup({
                easing: 'easeOutBack', //uses jQuery easing plugin
                speed: 700,
                modalClose: true,
                transition: 'fadeIn',
                transitionClose: "slideIn",
                modalColor: "#7C7C7C",
                onClose: function () {
                    $("#email_login").val("");
                    $("#password_login").val("");
                    $("#remember_login").removeAttr("checked");
                    $("#first_name").val("");
                    $("#last_name").val("");
                    $("#email").val("");
                    $("#password").val("");
                    $("#remember").removeAttr("checked");
                    $("#email_forgotpassword").val("");
                    $("#email_login").val("");
                }
            }
            ,
                    function () {
                        var popup_box = $(this).attr('alt');
                        $('.' + popup_box).bPopup({
                            easing: 'easeOutBack', //uses jQuery easing plugin
                            speed: 700,
                            modalClose: false,
                            transition: 'fadeIn',
                            transitionClose: "slideIn",
                            modalColor: false
                        });
                    });
        });
        $(".popup-box").click(function () {
            
            $(".all_bg").hide();
            var popup_box = $(this).attr('alt');
            //initialize();
            $('.' + popup_box).bPopup({
                easing: 'easeOutBack', //uses jQuery easing plugin
                speed: 700,
                modalClose: false,
                transition: 'fadeIn',
                transitionClose: "slideIn",
                modalColor: "#7C7C7C",
                onClose: function () {
                    $("#email_login").val("");
                    $("#password_login").val("");
                    $("#remember_login").removeAttr("checked");
                    $("#first_name").val("");
                    $("#last_name").val("");
                    $("#email").val("");
                    $("#password").val("");
                    $("#remember").removeAttr("checked");
                    $("#email_login").val("");
                    $("#email_login").val("");
                }

            });
        })

        $(".return-login").click(function () {
            $('.signup-window').bPopup().close();
            $('.signup-customer-window').bPopup().close();
            $(".all_bg").hide();
            var popup_box = $(this).attr('alt');
            $('.' + popup_box).bPopup({
                easing: 'easeOutBack', //uses jQuery easing plugin
                speed: 700,
                modalClose: false,
                transition: 'fadeIn',
                transitionClose: "slideIn",
                modalColor: "#7C7C7C",
                onClose: function () {
                    $("#email_login").val("");
                    $("#password_login").val("");
                    $("#remember_login").removeAttr("checked");
                    $("#first_name").val("");
                    $("#last_name").val("");
                    $("#email").val("");
                    $("#password").val("");
                    $("#remember").removeAttr("checked");
                    $("#email_login").val("");
                    $("#email_login").val("");
                }

            });
        })


        $(".return-signup").click(function () {
            $('.login-window').bPopup().close();
            $(".all_bg").hide();
            var popup_box = $(this).attr('alt');
            $('.' + popup_box).bPopup({
                easing: 'easeOutBack', //uses jQuery easing plugin
                speed: 700,
                modalClose: false,
                transition: 'fadeIn',
                transitionClose: "slideIn",
                modalColor: "#7C7C7C",
                onClose: function () {
                    $("#email_login").val("");
                    $("#password_login").val("");
                    $("#remember_login").removeAttr("checked");
                    $("#first_name").val("");
                    $("#last_name").val("");
                    $("#email").val("");
                    $("#password").val("");
                    $("#remember").removeAttr("checked");
                    $("#email_login").val("");
                    $("#email_login").val("");
                }

            });
        })
    });
</script>
<script>
    $(document).ready(function () {
        $(".term_window").click(function () {
            $('.terms-popup').bPopup({
                easing: 'easeOutBack', //uses jQuery easing plugin
                speed: 700,
                modalClose: true,
                transition: 'fadeIn',
                transitionClose: "slideIn",
                modalColor: "#7C7C7C",
                content: 'ajax',
                contentContainer: '.ajax-terms-content',
                loadUrl: '<?php echo HTTP_PATH ?>data/terms_and_conditions',
                onClose: function () {
                    $('.ajax-terms-content').empty();
                    $(".loader-image-box").show();
                }
            })
        })
    })
</script>

<script>
    $(document).ready(function () {
        $(".popup-box-seller-contact").click(function () {
            $('.seller-contact-popup').bPopup({
                easing: 'easeOutBack', //uses jQuery easing plugin
                speed: 700,
                modalClose: true,
                transition: 'fadeIn',
                transitionClose: "slideIn",
                modalColor: "#7C7C7C",
                content: 'ajax',
                contentContainer: '.ajax-terms-content',
                loadUrl: '<?php echo HTTP_PATH ?>data/terms_and_conditions',
                onClose: function () {
                    $('.ajax-terms-content').empty();
                    $(".loader-image-box").show();
                }
            })
        })
    })
</script>
<style>
    .pac-container {
    z-index: 99999999 !important;
}
</style>

<script type="text/javascript">

    var newwindow;
    var intId;
    function login() {
        $('#facebookbtn').html('<div class="btm_loader"> <?php echo HTTP_PATH . 'public/img/loading.gif'; ?></div>');
        var screenX = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft,
        screenY = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop,
        outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth,
        outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22),
        width = 1000,
        height = 768,
        left = parseInt(screenX + ((outerWidth - width) / 2), 10),
        top = parseInt(screenY + ((outerHeight - height) / 2.5), 10),
        features = (
        'width=' + width +
            ',height=' + height +
            ',left=' + left +
            ',top=' + top
    );
                
        var URL = '<?php echo HTTP_PATH; ?>app/Social/facebook/fbconfig.php';
        var loginurl = "<?php echo HTTP_PATH; ?>app/Social/facebook/fbconfig.php";
        var loginurlarr = loginurl.split("&state");
        var updateloginurl = loginurlarr[0] + "%2F" + "&state" + loginurlarr[1];
        var updateloginurlarr = updateloginurl.split("&cancel_url");
        var newloginurl = updateloginurlarr[0] + "%2F" + "&cancel_url" + updateloginurlarr[1];
        newwindow = window.open(loginurl, 'Signup by facebook', features);

        if (window.focus) {
            newwindow.focus()
        }
        return false;
    }
</script>
<script type="text/javascript">
   
    var newwindow;
    var intId;
    function connect_with_gmail(){
        var  screenX    = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft,
        screenY    = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop,
        outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth,
        outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22),
        width    = 530,
        height   = 470,
        left     = parseInt(screenX + ((outerWidth - width) / 2), 10),
        top      = parseInt(screenY + ((outerHeight - height) / 2.5), 10),
        features = (
        'width=' + width +
            ',height=' + height +
            ',left=' + left +
            ',top=' + top
    );
        newwindow=window.open('<?php echo $authUrl ?>','Login_by_Gmail',features);
 
        if (window.focus) {newwindow.focus()}
        return false;
    }
     
</script>