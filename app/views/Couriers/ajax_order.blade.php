<?php
if (!$orders->isEmpty()) {
    ?>

    {{ Form::open(array('url' => 'admin/courier/admin_index', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"form-inline form")) }}
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Courier Service Orders List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">
                        <table class="table table-bordered table-striped table-condensed cf">
                            <thead class="cf">
                                <tr>
                                    <th>Order Number</th>
                                    <th>Courier Name</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th class="bjhuh">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                
                                foreach ($orders as $record) {
                                    if ($i % 2 == 0) {
                                        $class = 'colr1';
                                    } else {
                                        $class = '';
                                    }
                                    ?>
                                    <tr>
                                        <td data-title="Order Name">
                                            {{ ucwords($record->order_number); }}
                                        </td>
                                        <td data-title="Name">
                                            {{ ucwords($record->first_name.' '.$record->last_name); }}
                                        </td>
                                        <td data-title="Status">
                                            {{ $record->status }}
                                        </td>

                                        <td data-title="Created">
                                            {{  date("d M, Y h:i A", strtotime($record->created)) }}
                                        </td>
                                         <td data-title="Action">
                                            <?php
                                            echo html_entity_decode(HTML::link('admin/courier/Admin_deleteorder/' . $record->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-danger btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirmAction('delete');")));
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
    
    {{ Form::close() }} 

<?php } else {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Couriers List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">There are no Courier Service Order added on site yet.</section>
                </div>
            </section>
        </div>
    </div>  
<?php }
?>