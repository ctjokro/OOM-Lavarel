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
                        <div class="tatils">Additional Order Status 
                            <div class="link-button"> 
                                <?php
                                echo html_entity_decode(HTML::link('user/addstatus', '<i class="fa  fa-plus"></i> Add Status', array('title' => 'Add Status', 'class' => 'btn btn-primary', 'escape' => false)));
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
                                            <div class="td_tables">Status Name</div>
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
                                                <div data-title="Name" class="td_tables2">
                                                    {{ ucwords($data->status_name); }}
                                                </div>
                                              
                                                <div data-title="Created" class="td_tables2">
                                                    {{  date("d M, Y h:i A", strtotime($data->created)) }}
                                                </div>
                                                <div data-title="Action" class="td_tables2">
                                                    <div class="actions">
                                                        <?php
                                                        echo html_entity_decode(HTML::link('user/editstatus/' . $data->slug, '<i class="fa fa-pencil"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'Edit')));
                                                     //   echo html_entity_decode(HTML::link('user/deletemenu/' . $data->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-danger btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirm('Are you sure you want to delete?');")));
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
                                <div class="pagination pagination_css">
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


