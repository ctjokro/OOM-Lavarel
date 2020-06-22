@extends('layouts/adminlayout')
@section('content')
@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Dashboard')
{{ HTML::style('public/assets/morris.js-0.4.3/morris.css'); }}
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--state overview start-->
        <div class="row state-overview">
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol terques">
                        <i class="fa fa-briefcase"></i>
                    </div>
                    <div class="value">
                        <h1 class="count">
                            {{ $user = DB::table('users')->where('user_type', "=", "Restaurant")->count()  }}
                        </h1>
                        <p>{{ link_to('/admin/restaurants/admin_index', "Restaurant", array('escape' => false,'class'=>"")) }}</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol gray">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="value">
                        <h1 class="count4">
                            {{ $customers = DB::table('users')->where('user_type', "=", "Customer")->count()  }}
                        </h1>
                        <p>{{ link_to('/admin/customer/admin_index', "Customers", array('escape' => false,'class'=>"")) }}</p>
                    </div>
                </section>
            </div>

<!--            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol orange">
                        <i class="fa fa-truck"></i>
                    </div>
                    <div class="value">
                        <h1 class="count6">
                            {{ $courier = DB::table('users')->where('user_type', "=", "Courier")->count()  }}
                        </h1>
                        <p>{{ link_to('/admin/courier/admin_index', "Couriers", array('escape' => false,'class'=>"")) }}</p>
                    </div>
                </section>
            </div>-->

            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol red">
                        <i class="fa fa-cutlery"></i>
                    </div>
                    <div class="value">
                        <h1 class=" count1">
                            {{  $cuisines = DB::table('cuisines')->count()  }}
                        </h1>
                        <p>{{ link_to('/admin/cuisine/admin_index', "Cuisines", array('escape' => false,'class'=>"")) }}</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol yellow">
                        <i class="fa fa-building-o"></i>
                    </div>
                    <div class="value">
                        <h1 class=" count2">
                            {{ $cities = DB::table('cities')->count()  }}
                        </h1>
                        <p>{{ link_to('/admin/cities/admin_index', "Cities", array('escape' => false,'class'=>"")) }}</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol blue">
                        <i class="fa  fa-files-o"></i>
                    </div>
                    <div class="value">
                        <h1 class=" count3">
                            {{ $pages= DB::table('pages')->count()  }}
                        </h1>
                        <p>{{ link_to('/admin/page/admin_index', "Pages", array('escape' => false,'class'=>"")) }}</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol blue">
                        <i class="fa  fa-tasks"></i>
                    </div>
                    <div class="value">
                        <h1 class=" count14">
                            {{ $orders= DB::table('orders')->count()  }}
                        </h1>
                        <p>{{ link_to('/admin/order/admin_index', "Orders", array('escape' => false,'class'=>"")) }}</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol light-green">
                        <i class="fa  fa-comments"></i>
                    </div>
                    <div class="value">
                        <h1 class=" count15">
                            {{ $reviews= DB::table('reviews')->count()  }}
                        </h1>
                        <p>{{ link_to('/admin/reviews/admin_index', "Reviews", array('escape' => false,'class'=>"")) }}</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol light-green">
                        <i class="fa  fa-tags"></i>
                    </div>
                    <div class="value">
                        <h1 class=" count16">
                            {{ $coupons= DB::table('coupons')->count()  }}
                        </h1>
                        <p>{{ link_to('/admin/coupon/admin_index', "Coupons", array('escape' => false,'class'=>"")) }}</p>
                    </div>
                </section>
            </div>

        </div>

        <!--state overview end-->
        <div id="morris">
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Restaurants Registrations ( {{$last_seven_days}} new Restaurants registered in the last 7 days )
                        </header>
                        <div class="panel-body">
                            <div id="hero-bar" class="graph"></div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</section>
<!--main content end-->


{{ HTML::script('public/js/count.js'); }}
<!--<script>
    countUp({{$user}}, 'count');
            countUp({{$cuisines}}, 'count1');
            countUp({{$cities}}, 'count2');
            countUp({{$pages}}, 'count3');
            countUp({{$customers}}, 'count4');
            countUp({{$orders}}, 'count14');
            countUp({{$courier}}, 'count6');
            countUp({{$reviews}}, 'count15');
            countUp({{$coupons}}, 'count16');</script>-->

{{ HTML::script('public/assets/morris.js-0.4.3/morris.min.js'); }}
{{ HTML::script('public/assets/morris.js-0.4.3/raphael-min.js'); }}

<script>
            var Script = function() {

            //morris chart

            $(function() {
            // data stolen from http://howmanyleft.co.uk/vehicle/jaguar_'e'_type

            Morris.Bar({
            element: 'hero-bar',
                    data: [
                    {device: 'Jan', geekbench: <?php echo isset($dates[0]) ? $dates[0] : 0 ?>},
                    {device: 'Feb', geekbench: <?php echo isset($dates[1]) ? $dates[1] : 0 ?>},
                    {device: 'Mar', geekbench: <?php echo isset($dates[2]) ? $dates[2] : 0 ?>},
                    {device: 'Apr', geekbench: <?php echo isset($dates[3]) ? $dates[3] : 0 ?>},
                    {device: 'May', geekbench: <?php echo isset($dates[4]) ? $dates[4] : 0 ?>},
                    {device: 'Jun', geekbench: <?php echo isset($dates[5]) ? $dates[5] : 0 ?>},
                    {device: 'July', geekbench: <?php echo isset($dates[6]) ? $dates[6] : 0 ?>},
                    {device: 'Aug', geekbench: <?php echo isset($dates[7]) ? $dates[7] : 0 ?>},
                    {device: 'Sep', geekbench: <?php echo isset($dates[8]) ? $dates[8] : 0 ?>},
                    {device: 'Oct', geekbench: <?php echo isset($dates[9]) ? $dates[9] : 0 ?>},
                    {device: 'Nov', geekbench: <?php echo isset($dates[10]) ? $dates[10] : 0 ?>},
                    {device: 'Dec', geekbench: <?php echo isset($dates[11]) ? $dates[11] : 0 ?>}
                    ],
                    xkey: 'device',
                    ykeys: ['geekbench'],
                    labels: ['Restaurant'],
                    barRatio: 0.4,
                    xLabelAngle: 35,
                    hideHover: 'auto',
                    barColors: ['#6883a3']
            });
            });
            }();

</script>


@stop