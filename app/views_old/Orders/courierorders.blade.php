@extends('layout')
@section('content')
<section>
    <div class="top_menus">
        <div class="wrapper">
            @include('elements/left_menu')
            <div class="acc_bar">
                <div class="ad_right">
                    <h2>Welcome!</h2>
                    <h1><?php echo $userData->first_name . ' ' . $userData->last_name; ?></h1>
                </div>
                <div class="acc_setting">
                    @include('elements/top_menu')
                </div> 
                <div class="informetion">
                    {{ View::make('elements.actionMessage')->render() }}
                    <div class="informetion_top">

                        <div class="informetion_bx">
                            <div class="informetion_bxes">
                                <?php
                                if (!$records->isEmpty()) {
                                    ?>
                                    <div class="table_dcf">
                                        <div class="tr_tables">
                                            <div class="td_tables">Order Number</div>
                                            <div class="td_tables">Status</div>
                                            <div class="td_tables">Placed Date/Time</div>
                                            <div class="td_tables">Action</div>
                                        </div>
                                        <?php
                                        $i = 1;
                                        foreach ($records as $data) {
                                            if ($i % 2 == 0) {
                                                $class = 'colr1';
                                            } else {
                                                $class = '';
                                            }
                                            ?>
                                            <div class="tr_tables2">
                                                <div data-title="Address Title" class="td_tables2">
                                                    {{ $data->order_number }}
                                                </div>
                                                <div data-title="Address Title" class="td_tables2">
                                                    {{ ucwords($data->status); }}
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                    {{  date("d M Y h:i A", strtotime($data->created)) }}
                                                </div>
                                                <div data-title="Action" class="td_tables2">
                                                    <div class="actions">
                                                        <?php
                                                        echo html_entity_decode(HTML::link('order/courierview/' . $data->slug, '<i class="fa fa-search"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'View Order Details')));
                                                        ?>
                                                    </div>
                                                </div>	
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
                                <div class="dataTables_paginate paging_bootstrap pagination">
                                    {{ $records->appends(Request::only('search','from_date','to_date'))->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
@stop