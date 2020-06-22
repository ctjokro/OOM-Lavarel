
{{ HTML::style('public/css/front/popup_list.css') }}
<script>
    function showpop(salonid)
    {
        document.getElementById('light' + salonid).style.display = 'block';
    }
    function closepop(salonid)
    {
        document.getElementById('light' + salonid).style.display = 'none';
    }
</script>
<?php
if (!$mainorders->isEmpty()) {
    ?>

    {{ Form::open(array('url' => 'admin/order/admin_index', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"form-inline form")) }}
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Orders List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">
                        <table class="table table-bordered table-striped table-condensed cf">
                            <thead class="cf">
                                <tr>
                                    <th>{{ SortableTrait::link_to_sorting_action('order_number', 'Order Number') }}</th>
                                    <th>{{ "Customer Name" }}</th>
<!--                                     <th class="td_tables">{{ SortableTrait::link_to_sorting_action('price', 'Price') }}</th>
                                            <th class="td_tables">{{ SortableTrait::link_to_sorting_action('tax', 'Tax') }}</th>
                                            <th class="td_tables">{{ SortableTrait::link_to_sorting_action('delivery_charge', 'Delivery charge') }}</th>
                                            <th class="td_tables">{{ SortableTrait::link_to_sorting_action('discount', 'Discount') }}</th>
                                            <th class="td_tables">Total</th>-->
<!--                                    <th>{{ "Status" }}</th>-->
<!--                                    <th>{{ "Courier Service" }}</th>-->
                                    <th>{{ SortableTrait::link_to_sorting_action('created', 'Created') }}</th>
                                    <th class="bjhuh">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($mainorders as $order) {
                                    if ($i % 2 == 0) {
                                        $class = 'colr1';
                                    } else {
                                        $class = '';
                                    }
                                    ?>
                                    <tr>
                                        <td data-title="Order Number">
                                            {{ $order->order_number; }}
                                        </td>
                                        <td data-title="Customer Name">
                                            <?php
                                            $userData = DB::table('users')
                                                    ->where('id', $order->user_id)
                                                    ->first();
                                            if (empty($userData)) {
                                                echo "Not Available";
                                            } else {
                                                echo ucwords($userData->first_name . ' ' . $userData->last_name);
                                            }
                                            ?>
                                        </td>
<!--                                        <td data-title="Customer Name">
                                                    {{ App::make("HomeController")->numberformat($order->price,2) }}
                                                </td>
                                               <td data-title="Customer Name">
                                                    {{ App::make("HomeController")->numberformat($order->tax,2) }}
                                                </td>
                                               <td data-title="Customer Name">
                                                    {{  App::make("HomeController")->numberformat($order->delivery_charge,2) }}
                                                </td>
                                               <td data-title="Customer Name">
                                                    {{ App::make("HomeController")->numberformat($order->discount,2) }}
                                                </td>
                                                <td data-title="Customer Name">
                                                    
                                                    {{ App::make("HomeController")->numberformat($order->price + $order->delivery_charge + $order->tax - $order->discount ,2) }}
                                                </td>-->
<!--                                        <td data-title="Status">
                                            {{ ucwords($order->status); }}
                                        </td>-->
<!--                                        <td data-title="Courier Name">
                                            <?php
                                            $courierData = DB::table('users')
                                                    ->select("users.*", "areas.name as area_name", "cities.name as city_name")
                                                    ->leftjoin('areas', 'areas.id', '=', 'users.area')
                                                    ->leftjoin('cities', 'cities.id', '=', 'users.city')
                                                    ->where('users.id', $order->courier_id)
                                                    ->first();
                                            if (!empty($courierData)) {
                                                ?>
                                                <div class="adminrecife"> 
                                                    <div class="btncourv" onclick = "showpop(<?php echo $order->id; ?>)" >
                                                        Courier Company
                                                    </div>
                                                    <div class="btncourvnotiy">
                                                        {{ html_entity_decode(HTML::link(HTTP_PATH.'order/notify_customer/'.$order->slug, "Notify Customer", array('class' => 'btn btn-defaults'), true)) }}
                                                    </div>
                                                </div>
                                                <div style="display: none;" id="light<?php echo $order->id; ?>" class="white_content">
                                                    <div class="white_content2">
                                                        <div class="white_notifications">
                                                            <div class="titlemsge">Courier Company Details</div>
                                                            <div class="considersignpoop">
                                                                <div class="contetd">
                                                                    <div class="contetdlr">
                                                                        <div class="contetdl">
                                                                            Name
                                                                        </div>
                                                                        <div class="contetdr">
                                                                            {{ $courierData->first_name ? $courierData->first_name.' '.$courierData->last_name:"N/A"; }} 
                                                                        </div>
                                                                    </div>
                                                                    <div class="contetdlr">
                                                                        <div class="contetdl">
                                                                            Contact Number
                                                                        </div>
                                                                        <div class="contetdr">
                                                                            {{ $courierData->contact ? $courierData->contact:"N/A"; }} 
                                                                        </div>
                                                                    </div>
                                                                    <div class="contetdlr">
                                                                        <div class="contetdl">
                                                                            Address
                                                                        </div>
                                                                        <div class="contetdr">
                                                                            {{ $courierData->address ? $courierData->address:"N/A"; }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="contetdlr">
                                                                        <div class="contetdl">
                                                                            City
                                                                        </div>
                                                                        <div class="contetdr">
                                                                            {{ $courierData->city_name ? $courierData->city_name:"N/A"; }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="contetdlr">
                                                                        <div class="contetdl">
                                                                            Area
                                                                        </div>
                                                                        <div class="contetdr">
                                                                            {{ $courierData->area_name ? $courierData->area_name:"N/A"; }}
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="close"> 
                                                                    <a href = "javascript:void(0)" class="btn-u btn-u-red" onclick = "closepop(<?php echo $order->id; ?>);">
            <?php echo 'Close'; ?>
                                                                    </a>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                    </div>
                                                </div>
            <?php
        } else {
            echo "Not Assinged Yet";
        }
        ?>
                                        </td>-->
                                        <td data-title="Created">
                                            {{  date("d M, Y h:i A", strtotime($order->created)) }}
                                        </td>
                                        <td data-title="Action">
        <?php
        echo html_entity_decode(HTML::link('admin/order/view/' . $order->slug, '<i class="fa fa-search"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'View Details')));
        ?>
                                        </td>	
                                    </tr>
        <?php
        $i++;
    }
    ?>
                            </tbody>
                        </table>
                    </section>
                </div>
            </section>
        </div>
    </div>
  <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <div class="panel-body border-bottom">
                    <div class="dataTables_paginate paging_bootstrap pagination">
                        {{ $mainorders->appends(Input::except('page'))->links() }}
                    </div>
                </div>
                
            </section>
        </div>
    </div>
    {{ Form::close() }} 

<?php } else {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Orders List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">There are no Order added on site yet.</section>
                </div>
            </section>
        </div>
    </div>  
<?php }
?>