@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Add Restaurant')
@extends('layouts/adminlayout')
@section('content')


<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    $.validator.addMethod("validname", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9_]+$/.test(value);
        }, "*Note: Special characters and spaces are not allowed.");
    $.validator.addMethod("pass", function (value, element) {
        return  this.optional(element) || (/.{8,}/.test(value) && /([0-9].*[a-z])|([a-z].*[0-9])/.test(value));
    }, "Password minimum length must be 8 charaters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.");
    $.validator.addMethod("contact", function (value, element) {
        return  this.optional(element) || (/^[0-9-]+$/.test(value));
    }, "Contact Number is not valid.");
    $("#adminAdd").validate();
});
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#city").change(function () {
            $("#area").load("<?php echo HTTP_PATH . "customer/loadarea/" ?>" + $(this).val() + "/0");
        })
    });
</script>
<?php $parts = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <ul id="breadcrumb" class="breadcrumb">
                    <li>
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/admindashboard', '<i class="fa fa-dashboard"></i> Dashboard', array('id' => ''), true)) }}
                    </li>
                    <li>
                        <i class="fa fa-user"></i> 
                        {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/user/admin_index', "Restaurants", array('id' => ''), true)) }}
                    </li>
                    <li class="active"> Config Timezone</li>
                </ul>

                <section class="panel">

                    <header class="panel-heading">
                        Config Timezone
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>
                        @if ($message = Session::get('message'))
                        <div class="alert alert-success alert-block">
                        	<button type="button" class="close" data-dismiss="alert">×</button>	
                                <strong>{{ $message }}</strong>
                        </div>
                        @endif
                        <form action="{{route('admin.timezone-currency')}}" method="post" class="cmxform form-horizontal tasi-form form">
                        <div class="form-group">
                            <label class="control-label col-lg-2">TimeZone<span class='require'>*</span></label>
                            <div class="col-lg-10">
                                <select class='form-control' name="timezone">
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
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-lg-2">Currency<span class='require'>*</span></label>
                            <div class="col-lg-10">
                                <select class='form-control' name="currency">
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
                        </div>
                        <input type="hidden" name="restaurant_id" value="<?php echo $parts[4]; ?>"/>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <input type="submit" name="save" class="btn btn-danger">
                                <input type="reset" name="reset" class="btn btn-default">
                            </div>
                        </div>
                        </form>
                        
                    </div>
                </section>
            </div>

        </div>
    </section>
</section>

{{ HTML::style('public/js/chosen/chosen.css'); }}
<!--scripts page-->
{{ HTML::script('public/js/chosen/chosen.jquery.js'); }}
<script type="text/javascript">
    $(".chzn-select").chosen();
</script>

<script>
    function in_array(needle, haystack) {
        for (var i = 0, j = haystack.length; i < j; i++) {
            if (needle == haystack[i])
                return true;
        }
        return false;
    }

    function getExt(filename) {
        var dot_pos = filename.lastIndexOf(".");
        if (dot_pos == -1)
            return "";
        return filename.substr(dot_pos + 1).toLowerCase();
    }



    

</script>


@stop