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
                        <div class="tatils">Manage Delivery Person
                            <div class="link-button">
                                <?php
                                echo html_entity_decode(HTML::link('user/adddeliveryperson', '<i class="fa  fa-plus"></i> Add New Delivery Person', array('title' => 'Add New Delivery Person', 'class' => 'btn btn-primary', 'escape' => false)));
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
                                            <div class="td_tables">Name</div>
                                            <div class="td_tables">Email Address</div>
                                            <div class="td_tables">Contact</div>
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
                                                    {{ ucwords($data->first_name . " " . $data->last_name); }}
                                                </div>
                                                <div data-title="Email Address" class="td_tables2">
                                                    {{ $data->email_address }}
                                                </div>
                                                <div data-title="Contact" class="td_tables2">
                                                    {{ $data->contact }}
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                    {{  date("d M, Y h:i A", strtotime($data->created)) }}
                                                </div>
                                                <div data-title="Action" class="td_tables2">
                                                    <div class="actions">
                                                        <?php
                                                        if (!$data->status)
                                                            echo html_entity_decode(HTML::link('user/activedeliveryperson/' . $data->slug, '<i class="fa fa-ban"></i>', array('class' => 'btn btn-danger btn-xs action-list', 'title' => "Active", 'onclick' => "return confirmAction('activate');")));
                                                        else
                                                            echo html_entity_decode(HTML::link('user/deactivedeliveryperson/' . $data->slug, '<i class="fa fa-check"></i>', array('class' => 'btn btn-success btn-xs action-list', 'title' => "Deactive", 'onclick' => "return confirmAction('deactive');")));


                                                        echo html_entity_decode(HTML::link('user/editdeliveryperson/' . $data->slug, '<i class="fa fa-pencil"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'Edit')));
                                                        echo html_entity_decode(HTML::link('user/deletedeliveryperson/' . $data->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-primary btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirm('Are you sure you want to delete?');")));
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

<script>
    function confirmAction(action) {
        var con = confirm("Are you sure want to "+action+"?");
        if(con){
            return true;    
        } else {
            return false;
        }
    }
</script>

@stop