<?php
if (!$payments->isEmpty()) { //print_r($payments); exit;
    ?>

    {{ Form::open(array('url' => 'admin/payments', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"form-inline form")) }}
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Payment History
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">
                        <table class="table table-bordered table-striped table-condensed cf">
                            <thead class="cf">
                                <tr>
<!--                                    <th></th>-->
                                    <th>{{ SortableTrait::link_to_sorting_action('transaction_id', 'Transaction id') }}</th>
                                    <!--  <th>{{ SortableTrait::link_to_sorting_action('last_name', 'Last Name') }}</th>-->
                                      <th>Order Number</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('price', 'Amount') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('status', 'Status') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('type', 'Payment Type') }}</th>
                                  
                                    <th>{{ SortableTrait::link_to_sorting_action('created', 'Created') }}</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($payments as $user) {
                                    if ($i % 2 == 0) {
                                        $class = 'colr1';
                                    } else {
                                        $class = '';
                                    }
                                    ?>
                                    <tr>
<!--                                        <td data-title="Select">
                                            {{ Form::checkbox('id', $user->id,null, array("onclick"=>"javascript:isAllSelect(this.form);",'name'=>"chkRecordId[]")) }}
                                        </td>-->
                                        <td data-title="Name">
                                            {{ ucwords($user->transaction_id); }}
                                        </td>
                                        <td data-title="Name">
                                            <?php
                                           // $orders = explode(',',$user->order_id);
                                            $single = DB::table('orders')
                                            ->where('id', $user->order_id)
                                            ->first();
//                                            $number = $single->order_number;
//                                            $main_ordershopData = DB::table('main_order')
//                                            // ->whereIn('order_id', $orders)
//                                             ->whereRaw("FIND_IN_SET('$number',order_id)")
//
//                                             ->first();
                                            if($single){
                                                echo $single->order_number; 
                                            }else{
                                                echo "N/A";
                                            }
                                            ?>
                                            
                                        </td>

                                        <td data-title="Email Address">
                                            {{ App::make("HomeController")->numberformat($user->price,2) }}
                                        </td>

                                        <td data-title="Contact Number">
                                            {{ $user->status}} 
                                        </td>
                                       
                                        <td data-title="Contact Number">
                                            {{ $user->type}} 
                                        </td>
                                       

                                        <td data-title="Created">
                                            {{  date("d M, Y h:i A", strtotime($user->created)) }}</td>

                                        <td data-title="Action">
                                            <?php
                                          
                                                echo html_entity_decode(HTML::link('admin/payment/deletepayment/' . $user->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-danger btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirmAction('delete');")));
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
                        {{ $payments->appends(Input::except('page'))->links() }}
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
                    Payment History
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">There are no Payment History on site yet.</section>
                </div>
            </section>
        </div>
    </div>  
<?php }
?>