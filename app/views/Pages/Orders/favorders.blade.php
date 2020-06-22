@extends('layout')
@section('content')
<?php
$data = DB::table('users')
        ->select("user_type")
        ->where('id', Session::get("user_id"))
        ->first();
 $type = $data->user_type;
?>

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

                        <div class="informetion_bx">
                            <div class="informetion_bxes">
                                <?php
                                if (!$records->isEmpty()) {
                                    ?>
                                    <div class="table_dcf">
                                        <div class="tr_tables">
                                            <div class="td_tables">Favorite</div>
                                            <div class="td_tables">Order Number</div>
                                            <?php if($type == "Customer") { ?>
                                                 <div class="td_tables">Restaurant Name</div>
                                            <?php } ?>
                                            <div class="td_tables">Status</div>
                                            <div class="td_tables">Placed Date/Time</div>
                                            <div class="td_tables">Action</div>
                                        </div>
                                        <?php
                                        $i = 1;
                                        foreach ($records as $data) {
                                        //    print_r($data);
                                            if ($i % 2 == 0) {
                                                $class = 'colr1';
                                            } else {
                                                $class = '';
                                            }
                                            ?>
                                            <div class="tr_tables2">
                                                <div data-title="Address Title" class="td_tables2">
                                                    <?php
                                                    if ($data->is_favorite == '1') {
                                                        echo html_entity_decode(HTML::link('user/removefav/' . $data->slug.'/'.$slug, '<i class="fa fa-rmfav-o"></i>', array('title' => 'Remove Favorite', 'class' => 'btn btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirm('Are you sure you want to remove from favorite orders?');")));
                                                    } else {
                                                        echo html_entity_decode(HTML::link('user/makefav/' . $data->slug.'/'.$slug, '<i class="fa fa-makefav-o"></i>', array('title' => 'Make Favorite', 'class' => 'btn btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirm('Are you sure you want to make favorite order?');")));
                                                    }
                                                    ?>
                                                </div>
                                                <div data-title="Address Title" class="td_tables2">
                                                    {{ $data->order_number }}
                                                </div>
                                                  <?php if($type == "Customer") { ?>
                                                 <div data-title="Address Title" class="td_tables2">
                                                    {{ ucwords($data->first_name); }}
                                                </div>
                                                <?php } ?>
                                                <div data-title="Address Title" class="td_tables2">
                                                    {{ ucwords($data->status); }}
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                    {{  date("d M Y h:i A", strtotime($data->created)) }}
                                                </div>
                                                <div data-title="Action" class="td_tables2">
                                                    <div class="actions">
                                                        <?php
                                                        //print_r($data); exit;
                                                        echo html_entity_decode(HTML::link('order/view/' . $data->slug, '<i class="fa fa-search"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'View Order Details')));
                                                        if($data->is_review == 0 && $data->status == 'Delivered'){
                                                            echo html_entity_decode(HTML::link('/restaurants/reviews/' . $data->restroslug, '<i class="fa fa-star"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'Place reviews')));
                                                        }
                                                           echo html_entity_decode(HTML::link('/restaurants/reorder/' . $data->slug, '<i class="fa fa-mail-forward"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'Reorder')));
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