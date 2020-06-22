
<?php
if (!$coupons->isEmpty()) {
    ?>

    {{ Form::open(array('url' => 'admin/coupon/admin_index', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"form-inline form")) }}
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Coupons List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">
                        <table class="table table-bordered table-striped table-condensed cf">
                            <thead class="cf">
                                <tr>
                                    <th></th>
                                    <th>{{ SortableTrait::link_to_sorting_action('code', 'Coupon Code') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('discount', 'Discount') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('start_time', 'Start Date') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('end_time', 'End Date') }}</th>
                                    <!--<th>Coupon Image</th>-->
                                    <th>{{ SortableTrait::link_to_sorting_action('status', 'Status') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('created', 'Created') }}</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($coupons as $coupon) {
                                    if ($i % 2 == 0) {
                                        $class = 'colr1';
                                    } else {
                                        $class = '';
                                    }
                                    ?>
                                    <tr>
                                        <td data-title="Select">
                                            {{ Form::checkbox('id', $coupon->id,null, array("onclick"=>"javascript:isAllSelect(this.form);",'name'=>"chkRecordId[]")) }}
                                        </td>
                                        <td data-title="Coupon Code">
                                            {{ ($coupon->code); }}
                                        </td>
                                        <td data-title="Discount">
                                            {{ ($coupon->discount)."%"; }}
                                        </td>
                                        
                                        
                                        <td data-title="Start Date">
                                            {{ ($coupon->start_time); }}
                                        </td>
                                        <td data-title="End Date">
                                            {{ ($coupon->end_time); }}
                                        </td>

<!--                                        <td data-title="Coupon Image">
                                            {{ $coupon->coupon_image?HTML::image(UPLOAD_FULL_COUPON_IMAGE_PATH.$coupon->coupon_image, '', array('width' => '100px')):"N/A" }}
                                        </td>-->

                                        <td data-title="Status">
                                            {{ $coupon->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Not Active</span>' }} 
                                        </td>

                                        <td data-title="Created">
                                            {{  date("d M, Y h:i A", strtotime($coupon->created)) }}</td>

                                        <td data-title="Action">
                                            <?php
                                            if (!$coupon->status)
                                                echo html_entity_decode(HTML::link('admin/coupon/Admin_active/' . $coupon->slug, '<i class="fa fa-ban"></i>', array('class' => 'btn btn-danger btn-xs action-list', 'title' => "Active", 'onclick' => "return confirmAction('activate');")));
                                            else
                                                echo html_entity_decode(HTML::link('admin/coupon/Admin_deactive/' . $coupon->slug, '<i class="fa fa-check"></i>', array('class' => 'btn btn-success btn-xs action-list', 'title' => "Deactive", 'onclick' => "return confirmAction('deactive');")));

                                            echo html_entity_decode(HTML::link('admin/coupon/Admin_delete/' . $coupon->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-danger btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirmAction('delete');")));
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
                        {{ $coupons->appends(Input::except('page'))->links() }}
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
                    {{ Form::hidden('search', $search, array('id' => '')) }}

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
                    Coupons List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">There are no Coupon added on site yet.</section>
                </div>
            </section>
        </div>
    </div>  
<?php }
?>