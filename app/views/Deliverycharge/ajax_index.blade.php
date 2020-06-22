<script>

</script>

<?php
if (!$delivery_charges->isEmpty()) {
    
    ?>

    {{ Form::open(array('url' => 'admin/deliverycharge/admin_index', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"form-inline form")) }}
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Delivery charges List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">
                        <table class="table table-bordered table-striped table-condensed cf">
                            <thead class="cf">
                                <tr>
                                    <th></th>
                                    <th class=" enable-sort" sort_type="">Form City</th>
                                    <th class=" enable-sort" sort_type="">From Area</th>
                                    <th class=" enable-sort" sort_type="">To City </th>
                                    <th class=" enable-sort" sort_type="">To Area</th>
                                    <th class=" enable-sort" sort_type="">{{ SortableTrait::link_to_sorting_action('basic_charge', 'Vespa Delivery Charge ('.CURR.')') }}</th>
                                    <th class=" enable-sort" sort_type="">{{ SortableTrait::link_to_sorting_action('advance_charge', 'Car Delivery Charge ('.CURR.')') }}</th>
                                    <th class=" enable-sort" sort_type="">{{ SortableTrait::link_to_sorting_action('delivery_charge_limit', 'Delivery Charge Limit ('.CURR.')') }}</th>
                                    <th class="enable-sort" sort_type="">{{ SortableTrait::link_to_sorting_action('created', 'Created') }}</th>
                                    <th class="bjhuh">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($delivery_charges as $record) {
                                    //echo "<pre>"; print_r($record); exit;
                                    if ($i % 2 == 0) {
                                        $class = 'colr1';
                                    } else {
                                        $class = '';
                                    }
                                    ?>
                                    <tr>
                                        <td data-title="Select">

                                            {{ Form::checkbox('id', $record->id,null, array("onclick"=>"javascript:isAllSelect(this.form);",'name'=>"chkRecordId[]")) }}
                                        </td>
                                        <td data-title="Name">
                                            <?php
                                            $fromcities = DB::table('cities')
                                                    ->where('cities.id', $record->from_city_id)
                                                    ->first();
                                            ?>
                                            {{ !empty($fromcities)? ucwords($fromcities->name):'N/A'; }}
                                        </td>
                                        <td data-title="Name">
                                            <?php
                                            $fromareas = DB::table('areas')
                                                    ->where('areas.id', $record->from_area_id)
                                                    ->first();
                                            
                                            ?>
                                            {{ !empty($fromareas)? ucwords($fromareas->name):'N/A'; }}
                                            
                                        </td>
                                        <td data-title="Name">
                                            <?php
                                            $tocities = DB::table('cities')
                                                    ->where('cities.id', $record->to_city_id)
                                                    ->first();
                                            ?>
                                            
                                            {{ !empty($tocities)? ucwords($tocities->name):'N/A'; }}
                                        </td>
                                        <td data-title="Name">
                                            <?php
                                            $toareas = DB::table('areas')
                                                    ->where('areas.id', $record->to_area_id)
                                                    ->first();
                                            
                                            ?>
                                            
                                            {{ !empty($toareas)? ucwords($toareas->name):'N/A'; }}
                                        </td>
                                       <td data-title="Name">
                                            {{ $record->basic_charge }}
                                        </td>
                                        <td data-title="Name">
                                            {{ $record->advance_charge }}
                                        </td>

                                        <td data-title="Name">
                                            {{ $record->delivery_charge_limit }}
                                        </td>

                                        <td data-title="Created">
                                            {{  date("d M, Y h:i A", strtotime($record->created)) }}</td>

                                        <td data-title="Action">
                                            <?php
                                            if (!$record->status)
                                                echo html_entity_decode(HTML::link('admin/deliverycharge/Admin_active/' . $record->slug, '<i class="fa fa-ban"></i>', array('class' => 'btn btn-danger btn-xs action-list', 'title' => "Active", 'onclick' => "return confirmAction('active');")));
                                            else
                                                echo html_entity_decode(HTML::link('admin/deliverycharge/Admin_deactive/' . $record->slug, '<i class="fa fa-check"></i>', array('class' => 'btn btn-success btn-xs action-list', 'title' => "Deactive", 'onclick' => "return confirmAction('deactive');")));

                                            echo html_entity_decode(HTML::link('admin/deliverycharge/Admin_edit/' . $record->slug, '<i class="fa fa-pencil"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'Edit')));
                                            echo html_entity_decode(HTML::link('admin/deliverycharge/Admin_delete/' . $record->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-danger btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirmAction('delete');")));
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

            <!--                    Number of Citys <span class="badge-gray"> </span> - <span class="badge-gray"> </span> out of <span class="badge-gray"></span>-->

                    <div class="dataTables_paginate paging_bootstrap pagination">
                        {{ $delivery_charges->appends(Request::only('search','from_date','to_date'))->links() }}
                    </div>
                </div>
                <div class="panel-body">
                    <button type="button" name="chkRecordId" onclick="checkAll(true);"  class="btn btn-success">Select All</button>
                    <button type="button" name="chkRecordId" onclick="checkAll(false);" class="btn btn-success">Unselect All</button>
                    <?php
                    $arr = array(
                        "" => "Action for selected...",
                        'Activate' => "Activate",
                        'Deactivate' => "Deactivate",
                        'Delete' => "Delete",
                    );
                    //  echo form_dropdown("action", $arr, '', "class='small form-control' id='table-action'");
                    ?>
                    {{ Form::select('action', $arr, null, array('class'=>"small form-control",'id'=>'action')) }}

                    <button type="submit" class="small btn btn-success btn-cons" onclick=" return isAnySelect();" id="submit_action">Ok</button>
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
                    Delivery Charges  List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">There are no Delivery Charge added on site yet.</section>
                </div>
            </section>
        </div>
    </div>  
<?php }
?>



