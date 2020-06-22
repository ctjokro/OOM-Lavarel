@extends('layout')
@section('content')
<section>
    <?php 
    function humanTiming($time) {

    $time = time() - $time; // to get the time since that moment
    $time = ($time < 1) ? 1 : $time;
    $tokens = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit)
            continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . " ago";
    }
}
    ?>
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
                        <div class="tatils">My Reviews                         
                        </div>
                        <div class="informetion_bx">

                            <div class="informetion_bxes">
                                <?php
                                if (!$records->isEmpty()) {
                                    ?>
                                    <div class="table_dcf">
                                        <div class="tr_tables">
                                             <?php if ($userData->user_type == "Restaurant") { ?>
                                            <div class="td_tables">Customer Name</div>
                                             <?php }else{ ?>
                                            <div class="td_tables">Restaurant Name</div>
                                             <?php } ?>
                                            
                                            <div class="td_tables">Rating</div>
                                            <div class="td_tables">Comment</div>
                                            <div class="td_tables">Created</div>
                                            <!--<div class="td_tables">Action</div>-->
                                        </div>
                                        <?php
                                        $i = 1;
                                        foreach ($records as $data) {
//                                            echo "<pre>"; print_r($data); exit;
                                            if ($i % 2 == 0) {
                                                $class = 'colr1';
                                            } else {
                                                $class = '';
                                            }
                                            ?>
                                            <div class="tr_tables2">
                                                <div data-title="Name" class="td_tables2">
                                                    {{$data->first_name . " " . $data->last_name}}
                                                </div>
                                                <div data-title="Name" class="td_tables2 ">
                                                  <?php
                                                        $avg_ratng = round(($data->quality + $data->packaging) / 2);
                                                        for ($i = 0; $i < $avg_ratng; $i++) {
                                                            echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-on.png", '') . '</span>';
                                                        }
                                                        for ($j = 5; $j > $avg_ratng; $j--) {
                                                            echo '<span>' . HTML::image(HTTP_PATH . "public/lib/img/star-off.png", '') . '</span>';
                                                        }
                                                        ?>
                                                </div>
                                                <div data-title="Email Address" class="td_tables2">
                                                   {{ nl2br($data->comment)}}
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                     {{  date("d M, Y h:i A", strtotime($data->created)) }}
                                            <?php /* ?>   {{ humanTiming(strtotime($data->created))}} <?php */ ?>  
                                                </div>
<!--                                                <div data-title="Action" class="td_tables2">
                                                    <div class="actions">
                                                        
                                                    </div>
                                                </div>	-->
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


