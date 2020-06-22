@extends('layout')
@section('content')
<script src="{{ URL::asset('public/js/listing.js') }}"></script>
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
                        <div class="tatils">Coupon Codes
                            <div class="link-button"> 
                                <?php
                                echo html_entity_decode(HTML::link('user/addcouponcode', '<i class="fa  fa-plus"></i> Add Coupon', array('title' => 'Add Coupon', 'class' => 'btn btn-primary', 'escape' => false)));
                                ?>
                            </div>
                        </div>
                        <div class="informetion_bx">

                            <div class="informetion_bxes">
                                <?php
                                if (!$records->isEmpty()) {
                                    ?>
                                    <div class="table_dcf">
                                        <div class="tr_tables">
                                            <div class="td_tables">Coupon Code </div>
                                            <div class="td_tables">Discount </div>
                                            <div class="td_tables">Start Date </div>
                                            <div class="td_tables">End Date  </div>
                                            <div class="td_tables">Status  </div>
                                            <div class="td_tables">Created   </div>
                                            <div class="td_tables">Action   </div>
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
                                                <div data-title="Name" class="td_tables2">
                                                    {{ ucwords($data->code); }}
                                                </div>
                                                <div data-title="Name" class="td_tables2">
                                                    {{ ($data->discount)."%"; }}
                                                </div>

                                                <div data-title="Name" class="td_tables2">
                                                    {{ ($data->start_time); }}
                                                </div>
                                                <div data-title="Name" class="td_tables2">
                                                    {{ ($data->end_time); }}
                                                </div>
                                                <div data-title="Name" class="td_tables2">
                                                    <?php if (date("Y-m-d") < $data->end_time) { ?>
                                                        {{ $data->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Not Active</span>' }} 
                                                    <?php } else {
                                                        echo '<span class="label label-danger">Expired</span>';
                                                    } ?>

                                                </div>

                                                <div data-title="Created" class="td_tables2">
                                                    {{  date("d M, Y h:i A", strtotime($data->created)) }}
                                                </div>
                                                <div data-title="Action" class="td_tables2">
                                                    <div class="actions">
                                                        <?php
                                                        if (!$data->status)
                                                            echo html_entity_decode(HTML::link('user/coupon_active/' . $data->slug, '<i class="fa fa-ban"></i>', array('class' => 'btn btn-danger btn-xs action-list', 'title' => "Active", 'onclick' => "return confirmAction('activate');")));
                                                        else
                                                            echo html_entity_decode(HTML::link('user/coupon_deactive/' . $data->slug, '<i class="fa fa-check"></i>', array('class' => 'btn btn-success btn-xs action-list', 'title' => "Deactive", 'onclick' => "return confirmAction('deactive');")));

                                                        echo html_entity_decode(HTML::link('user/coupon_delete/' . $data->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-danger btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirmAction('delete');")));
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
                                <div class="pagination pagination_css">
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


