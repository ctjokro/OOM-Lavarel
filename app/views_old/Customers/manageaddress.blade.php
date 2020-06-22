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
                        <div class="tatils">Manage Addresses
                            <div class="link-button">
                                <?php
                                echo html_entity_decode(HTML::link('user/addaddress', '<i class="fa  fa-plus"></i> Add New', array('title' => 'Add Menu', 'class' => 'btn btn-primary', 'escape' => false)));
                                ?>
                            </div>
                        </div>
                        <div class="informetion_bx">
                            <div class="informetion_bxes">
                                <?php
                                if (!$records->isEmpty()) {
                                    ?>
                                    <div class="table_dcf">
                                        <div class="tr_tables">
                                            <div class="td_tables">Address Title</div>
                                            <div class="td_tables">Type</div>
                                            <div class="td_tables">Created</div>
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
                                                    {{ ucwords($data->address_title); }}
                                                </div>
                                                <div data-title="Type" class="td_tables2">
                                                    {{ ucwords($data->address_type); }}
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                    {{  date("d M, Y h:i A", strtotime($data->created)) }}
                                                </div>
                                                <div data-title="Action" class="td_tables2">
                                                    <div class="actions">
                                                        <?php
                                                        echo html_entity_decode(HTML::link('user/editaddress/' . $data->slug, '<i class="fa fa-pencil"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'Edit')));
                                                        echo html_entity_decode(HTML::link('user/deleteaddress/' . $data->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-primary btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirm('Are you sure you want to delete?');")));
                                                        ?>
                                                    </div>
                                                </div>	
                                            </div>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                        <div class="pagination">
                                            {{ $records->appends(Request::only('search','from_date','to_date'))->links() }}
                                        </div>
                                    <?php } else {
                                        ?>
                                        <div class="no-record">
                                            No records available
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
@stop