<?php 


exit;

if (!$wallet->isEmpty()) {
    ?>

    {{ Form::open(array('url' => 'admin/customer/wallethistory/', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"form-inline form")) }}
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Customers List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">
                        <table class="table table-bordered table-striped table-condensed cf">
                            <thead class="cf">
                                <tr>
                                    <th></th>
                                    <th>{{ SortableTrait::link_to_sorting_action('type', 'Type') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('comment', 'Description') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('display_amount', 'Display Amount') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('status', 'Status') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('created', 'Created') }}</th>
                                    <th class="bjhuh">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($wallet as $wallets) {
                                    if ($i % 2 == 0) {
                                        $class = 'colr1';
                                    } else {
                                        $class = '';
                                    }
                                    ?>
                                    <tr>
                                        <td data-title="Select">
                                            {{ Form::checkbox('id', $wallets->id,null, array("onclick"=>"javascript:isAllSelect(this.form);",'name'=>"chkRecordId[]")) }}
                                        </td>
                                        <td data-title="Type">
                                            {{ ucwords($wallets->type); }}
                                        </td>
                                        <td data-title="Comment">
                                            {{ ucwords($wallets->comment); }}
                                        </td>

                                        <td data-title="Display Amount">
                                            {{ $wallets->display_amount }}
                                        </td>

                                        <td data-title="Status">
                                            {{ $wallets->status ? $wallets->status : 'N/A' }} 
                                        </td>

                                        <td data-title="Created">
                                            {{  date("d M, Y h:i A", strtotime($user->created)) }}
                                        </td>
                                        <td data-title="Action">
                                            <?php
                                            if (!$wallets->status)
                                                echo html_entity_decode(HTML::link('admin/customer/Admin_activeuser/' . $user->slug, '<i class="fa fa-ban"></i>', array('class' => 'btn btn-danger btn-xs action-list', 'title' => "Active", 'onclick' => "return confirmAction('activate');")));
                                            else
                                                echo html_entity_decode(HTML::link('admin/customer/Admin_deactiveuser/' . $user->slug, '<i class="fa fa-check"></i>', array('class' => 'btn btn-success btn-xs action-list', 'title' => "Deactive", 'onclick' => "return confirmAction('deactive');")));

                                            echo html_entity_decode(HTML::link('admin/customer/Admin_edituser/' . $user->slug, '<i class="fa fa-pencil"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'Edit')));
                                            echo html_entity_decode(HTML::link('admin/customer/Admin_deleteuser/' . $user->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-danger btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirmAction('delete');")));
                                            echo html_entity_decode(HTML::link('admin/customer/wallethistory/' . $user->slug, '<i class="fa fa-credit-card"></i>', array('title' => 'wallet history', 'class' => 'btn btn-success btn-xs action-list', 'escape' => false)));
                                            
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
                        {{ $wallets->appends(Input::except('page'))->links() }}
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
                    Customers List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">There are no Customer added on site yet.</section>
                </div>
            </section>
        </div>
    </div>  
<?php }
?>