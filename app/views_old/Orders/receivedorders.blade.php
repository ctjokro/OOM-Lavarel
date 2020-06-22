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
                         <div class="tatils">Received Orders</div>
                        <div class="informetion_bx">
                            <section class="panel serchh">
                    
                    <div class="search_pane">
                        {{ View::make('elements.actionMessage')->render() }}
                        {{ Form::open(array('url' => '/order/receivedorders', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>'form-inline')) }}
                        <div class="search_wrap">
                            <div class="wdew">
                        <div class="form-group align_box">
                            <label class="sr-only" for="search">Your Keyword</label>
                            {{ Form::text('search', $search_keyword, array('class' => 'required search_fields form-control','placeholder'=>"Your Keyword")) }}
                        </div>
                               
                        <div class="form-group align_box">
                           <?php
                                    $statusArray = array(
                                        '' => 'Please Select'
                                    );
                                    //$statusArray['Pending'] = 'Pending';
                                    $statusArray['Paid'] = 'Paid';
                                    global $adminStatus;
                                    if (!empty($adminStatus)) {
                                        foreach ($adminStatus as $key => $val)
                                            $statusArray[$key] = $val;
                                    }
                                    ?>
                                    {{ Form::select('status', $statusArray, $orderstatus, array('class' => 'form-control search_fields required', 'id'=>'status')) }}
                                    <span class="subb">{{ Form::submit('Search', array('class' => "btn btn-primary")) }}  </span>
                        </div>
                       </div>
                        <span class="hint" style="margin:5px 0">Search Order by typing their Order number</span>
                       </div>
                        {{ Form::close() }}
                    </div>
                </section>
                            <div class="informetion_bxes">
                                <?php
                                if (!$records->isEmpty()) {
                                    ?>
                                    <div class="table_dcf">
                                        <div class="tr_tables">
                                            <div class="td_tables">Order Number</div>
                                            <div class="td_tables">Status</div>
                                            <div class="td_tables">Placed Date/Time</div>
                                            <div class="td_tables">Order Type</div>
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
                                                <div data-title="Created" class="td_tables2">
                                                   <?php 
                                                  // print_r($data); exit;
                                                   if($data->pickup_ready == 1){
                                                       echo "Pick up";
                                                   }else{
                                                       echo "Home Deliver";
                                                   }
                                                   ?>
                                                </div>
                                                <div data-title="Action" class="td_tables2">
                                                    <div class="actions">
                                                        <?php
                                                        echo html_entity_decode(HTML::link('order/receivedview/' . $data->slug, '<i class="fa fa-search"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'View Order Details')));
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