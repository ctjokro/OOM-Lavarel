
<?php
if (!$users->isEmpty()) {
    ?>

    {{ Form::open(array('url' => 'admin/restaurants/admin_index', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"form-inline form")) }}
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Restaurants List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">
                        <table class="table table-bordered table-striped table-condensed cf">
                            <thead class="cf">
                                <tr>
                                    <th></th>
                                    <th>{{ SortableTrait::link_to_sorting_action('first_name', 'Restaurant Name') }}</th>
                                    <!--  <th>{{ SortableTrait::link_to_sorting_action('last_name', 'Last Name') }}</th>-->
                                    <th>{{ SortableTrait::link_to_sorting_action('email_address', 'Email Address') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('contact', 'Contact Number') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('approve_status', 'Approval Status') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('activation_status', 'Email Verification') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('created', 'Created') }}</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($users as $user) {
                                    if ($i % 2 == 0) {
                                        $class = 'colr1';
                                    } else {
                                        $class = '';
                                    }
                                    ?>
                                    <tr>
                                        <td data-title="Select">
                                            {{ Form::checkbox('id', $user->id,null, array("onclick"=>"javascript:isAllSelect(this.form);",'name'=>"chkRecordId[]")) }}
                                        </td>
                                        <td data-title="Name">
                                            {{ ucwords($user->first_name); }}
                                        </td>
<!--                                        <td data-title="Name">
                                            {{ ucwords($user->last_name); }}
                                        </td>-->

                                        <td data-title="Email Address">
                                            {{ $user->email_address }}
                                        </td>

                                        <td data-title="Contact Number">
                                            {{ $user->contact ? $user->contact : 'N/A' }} 
                                        </td>
                                        <td data-title="Approval Status">
                                            {{ $user->approve_status ? '<span class="label label-success">Approved</span>' : '<span data-original-title="Click to approve account" data-toggle="Click to approve account" class="tooltips label label-danger">Pending</span>' }} 
                                        </td>

                                        <td data-title="Email Verification">
                                            {{ $user->activation_status ? '<span class="label label-success">Verified</span>' : '<span data-original-title="Email address is not verified" data-toggle="Email address is not verified" class="tooltips label label-danger">Not verified</span>' }} 
                                        </td>

                                        <td data-title="Created">
                                            {{  date("d M, Y h:i A", strtotime($user->created)) }}</td>

                                        <td data-title="Action">
                                            <?php
                                            if (!$user->status)
                                                echo html_entity_decode(HTML::link('admin/restaurants/Admin_activeuser/' . $user->slug, '<i class="fa fa-ban"></i>', array('class' => 'btn btn-danger btn-xs action-list', 'title' => "Active", 'onclick' => "return confirmAction('activate');")));
                                            else
                                                echo html_entity_decode(HTML::link('admin/restaurants/Admin_deactiveuser/' . $user->slug, '<i class="fa fa-check"></i>', array('class' => 'btn btn-success btn-xs action-list', 'title' => "Deactive", 'onclick' => "return confirmAction('deactive');")));

                                            echo html_entity_decode(HTML::link('admin/restaurants/Admin_edituser/' . $user->slug, '<i class="fa fa-pencil"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'Edit')));
                                            echo html_entity_decode(HTML::link('admin/restaurants/Admin_deleteuser/' . $user->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-danger btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirmAction('delete');")));
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
                        {{ $users->appends(Input::except('page'))->links() }}
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
                    Restaurants List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">There are no Restaurant added on site yet.</section>
                </div>
            </section>
        </div>
    </div>  
<?php }
?>