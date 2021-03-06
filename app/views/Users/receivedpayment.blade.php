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
                        <div class="tatils">Received Payments

                        </div>
                        <div class="informetion_bx">

                            <div class="informetion_bxes">
                                <?php
//                                echo '<prE>'; print_r($payments);die;
                                if (!$payments->isEmpty()) {
                                    ?>
                                    <div class="table_dcf">
                                        <div class="tr_tables">
                                            <div class="td_tables">Transaction id</div>
                                            <?php if ($paymentslug == "purchase") { ?>
                                                <div class="td_tables">Order Number</div>
                                            <?php } else { ?>
                                                <div class="td_tables">Plan</div>
                                            <?php } ?>

                                            <div class="td_tables">Amount</div>
                                            <div class="td_tables">Admin Commission</div>
                                            <div class="td_tables">Status</div>
                                            <div class="td_tables">Created</div>
                                            <?php if ($paymentslug == "purchase") { ?>
                                                <div class="td_tables">Action</div>
                                            <?php } ?>
                                        </div>
                                        <?php
                                        $i = 1;
                                        foreach ($payments as $data) {
                                            // echo "<pre>"; print_r($data); exit;
                                            if ($i % 2 == 0) {
                                                $class = 'colr1';
                                            } else {
                                                $class = '';
                                            }
                                            ?>
                                            <div class="tr_tables2">
                                                <div data-title="Name" class="td_tables2">
                                                    {{ ucwords($data->transaction_id); }}
                                                </div>
                                                <div data-title="Name" class="td_tables2 ">
                                                    <?php
                                                    if ($paymentslug == "purchase") {
                                                        $single = DB::table('orders')
                                                                ->where('id', $data->order_id)
                                                                ->first();

                                                        if ($single) {
                                                            echo $single->order_number;
                                                        } else {
                                                            echo "N/A";
                                                        }
                                                    } else {

                                                        $sponsorship = DB::table('sponsorship')
                                                                ->where('id', $data->package)
                                                                ->first();
                                                        if ($sponsorship) {
                                                            echo $sponsorship->name;
                                                        } else {
                                                            echo "N/A";
                                                        }
                                                    }
                                                    ?>


                                                </div>
                                                <div data-title="Amount" class="td_tables2 ttvb">
                                                    <?php 
                                                    foreach(Config::get('constant') as $key => $c)
                                                    {
                                                        if($key == $currency)
                                                        {
                                                            echo $c.' '.App::make("HomeController")->numberformat($data->price,2);
                                                        }
                                                    }
                                                    //{{ App::make("HomeController")->numberformat($data->price,2) }}
                                                    ?>
                                                    

                                                </div>
                                                <div data-title="Admin Commision" class="td_tables2 ttvb">
                                                    <?php
                                                    $adminuser = DB::table('admins')
                                                            ->where('id', '1')
                                                            ->first();

                                                    if ($adminuser->is_commission == 1) {

                                                        $comm_per = $adminuser->commission;
                                                        $tax_amount = $comm_per * $data->price / 100;
                                                    } else {
                                                        $tax_amount = 0.00;
                                                    }
                                                    ?>
                                                    <?php 
                                                    foreach(Config::get('constant') as $key => $c)
                                                    {
                                                        if($key == $currency)
                                                        {
                                                            echo $c.' '.App::make("HomeController")->numberformat($tax_amount,2);
                                                        }
                                                    }
                                                    //{{ App::make("HomeController")->numberformat($tax_amount,2) }}
                                                    ?>
                                                    

                                                </div>
                                                <div data-title="Email Address" class="td_tables2">
                                                    {{ $data->status}} 
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                    {{  date("d M, Y h:i A", strtotime($data->created)) }}
                                                </div>
                                                <?php if ($paymentslug == "purchase") { ?>
                                                    <div data-title="Action" class="td_tables2">
                                                        <div class="actions">
                                                            <?php
                                                            echo html_entity_decode(HTML::link('order/receivedview/' . $single->slug, '<i class="fa fa-search"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'View Order Details')));
                                                            ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <?php
                                            $i++;
                                        }
                                        ?>

                                    <?php } else {
                                        ?>
                                        <div class="no-record">
                                            No records available
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="pagination pagination_css">
                                    {{ $payments->appends(Request::only('search','from_date','to_date'))->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
@stop


