<?php
if (!$users->isEmpty()) {
    ?>

    {{ Form::open(array('url' => 'admin/reviews/admin_index', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"form-inline form")) }}
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Reviews List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">
                        <table class="table table-bordered table-striped table-condensed cf">
                            <thead class="cf">
                                <tr>
                                    <th></th>
                                    <th>{{ SortableTrait::link_to_sorting_action('user_f_name', 'Posted By') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('caterer_f_name', 'Restaurant Name') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('comment', 'Review') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('rating', 'Rating') }}</th>
                                    <th>{{ SortableTrait::link_to_sorting_action('created', 'Created') }}</th>
                                    <th class="bjhuh">Action</th>
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
                                        <td data-title="Posted By">
                                            {{ ucwords($user->user_f_name." ".$user->user_l_name); }}
                                        </td>
                                        <td data-title="Restaurant Name">
                                            {{ ucwords($user->caterer_f_name." ".$user->caterer_l_name); }}
                                        </td>

                                        <td data-title="Review">
                                            {{ $user->comment }}
                                        </td>

                                        <td data-title="Rating">
                                            <label>Quality & Taste:</label><br/>
                                            <?php
                                            $avg_ratng = $user->quality;
                                            for ($i = 0; $i < $avg_ratng; $i++) {
                                                echo '<span>' . HTML::image(HTTP_PATH . "public/img/star-on.png", '') . '</span>';
                                            }
                                            for ($j = 5; $j > $avg_ratng; $j--) {
                                                echo '<span>' . HTML::image(HTTP_PATH . "public/img/star-off.png", '') . '</span>';
                                            }
                                            ?>
                                            <br/><br/>

                                            <label>Packaging:</label><br/>
                                            <?php
                                            $avg_ratng = $user->packaging;
                                            for ($i = 0; $i < $avg_ratng; $i++) {
                                                echo '<span>' . HTML::image(HTTP_PATH . "public/img/star-on.png", '') . '</span>';
                                            }
                                            for ($j = 5; $j > $avg_ratng; $j--) {
                                                echo '<span>' . HTML::image(HTTP_PATH . "public/img/star-off.png", '') . '</span>';
                                            }
                                            ?>
                                            <br/><br/>


                                            <label>Delivery:</label><br/>
                                            <?php
                                            $avg_ratng = $user->delivery;
                                            for ($i = 0; $i < $avg_ratng; $i++) {
                                                echo '<span>' . HTML::image(HTTP_PATH . "public/img/star-on.png", '') . '</span>';
                                            }
                                            for ($j = 5; $j > $avg_ratng; $j--) {
                                                echo '<span>' . HTML::image(HTTP_PATH . "public/img/star-off.png", '') . '</span>';
                                            }
                                            ?>
                                            <br/>

                                        </td>

                                        <td data-title="Created">
                                            {{  date("d M, Y h:i A", strtotime($user->created)) }}
                                        </td>
                                        <td data-title="Action">
                                            <?php
                                            if (!$user->status)
                                                echo html_entity_decode(HTML::link('admin/reviews/Admin_active/' . $user->slug, '<i class="fa fa-ban"></i>', array('class' => 'btn btn-danger btn-xs action-list', 'title' => "Active", 'onclick' => "return confirmAction('activate');")));
                                            else
                                                echo html_entity_decode(HTML::link('admin/reviews/Admin_deactive/' . $user->slug, '<i class="fa fa-check"></i>', array('class' => 'btn btn-success btn-xs action-list', 'title' => "Deactive", 'onclick' => "return confirmAction('deactive');")));

                                            echo html_entity_decode(HTML::link('admin/reviews/Admin_delete/' . $user->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-danger btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirmAction('delete');")));
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
                    Reviews List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">There are no Review posted on site yet.</section>
                </div>
            </section>
        </div>
    </div>  
<?php }
?>