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
                        <div class="tatils">Manage Menu
                            <div class="link-button">
                                <?php
                                echo html_entity_decode(HTML::link('user/addmenu', '<i class="fa  fa-plus"></i> Add Menu', array('title' => 'Add Menu', 'class' => 'btn btn-primary ', 'escape' => false)));
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
                                            <div class="td_tables">Cuisine</div>
                                            <div class="td_tables">Item Name</div>
                                            <div class="td_tables">Food Type</div>
                                            <div class="td_tables">Spicy</div>
                                            <div class="td_tables">Deal</div>
                                            <div class="td_tables">Price</div>
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
                                                    {{ ucwords($data->name); }}
                                                </div>
                                                <div data-title="Name" class="td_tables2 ">
                                                    {{ ucwords($data->item_name); }}


                                                </div>
                                                <div data-title="Name" class="td_tables2 ttvb">
                                                    <?php
                                                    if ($data->non_veg == 0) {
                                                        ?><span class="nonb green-mark withborder withpad" title="Veg"><i class="fa fa-circle"></i></span><?php
                                        }
                                        if ($data->non_veg == 1) {
                                                        ?><span class="nonb red-mark withborder withpad" title="Non-veg"><i class="fa fa-circle"></i></span><?php
                                        }
                                                    ?>


                                                </div>
                                                <div data-title="Name" class="td_tables2 ttvb">
                                                    <?php
                                                    if ($data->spicy == 1) {
                                                        ?><span class="nonb borderfi" title="This is spicy food."><img src="{{ URL::asset('public/assets/timthumb.php?src='.HTTP_PATH.'/public/img/front/chilli.png'.'&w=20&h=20&zc=2&q=100') }}" alt="img" /></span><?php
                                        } else {
                                            echo "N/A";
                                        }
                                                    ?>

                                                </div>
                                                <div data-title="Name" class="td_tables2 ttvb">

                                                    <?php
                                                    if ($data->deal == 1) {
                                                        ?> <span class="ttvb">   <img src="{{ URL::asset('public/img/front') }}/deal.png" alt="Deal" /> </span><?php
                                        } else {
                                            echo "N/A";
                                        }
                                                    ?>

                                                </div>
                                                <div data-title="Email Address" class="td_tables2">
                                                    {{ App::make("HomeController")->numberformat($data->price, 2) }}
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                    {{  date("d M, Y h:i A", strtotime($data->created)) }}
                                                </div>
                                                <div data-title="Action" class="td_tables2">
                                                    <div class="actions">
                                                        <?php
                                                        echo html_entity_decode(HTML::link('user/editmenu/' . $data->slug, '<i class="fa fa-pencil"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'Edit')));
                                                        echo html_entity_decode(HTML::link('user/deletemenu/' . $data->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-danger btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirm('Are you sure you want to delete?');")));
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


