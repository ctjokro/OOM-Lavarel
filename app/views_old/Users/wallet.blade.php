@extends('layout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        

        $("#myform").validate({
            submitHandler: function(form) {
                this.checkForm();

                if (this.valid()) { // checks form for validity
                    //                    $('#formloader').show();
                    this.submit();
                } else {
                    return false;
                }
            }
        });
        
    });
    
   
    
</script>

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
                    <div class="informetion_top">
                        <div class="tatils">Wallet </div>
                        <div class="pery">
                            <div id="formloader" class="formloader" style="display: none;">
                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                            </div>
                            {{ View::make('elements.frontEndActionMessage')->render() }}
                            {{ Form::open(array('url' => '/user/wallet', 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                            <div class="panel__top">
								 <h6 class="panel__title">Add Money to Wallet</h6>
								 <p>Now add you money securely to account wallet using paypal payment gateway.</p>
							  </div>
                            
                            <div class="fill_data_left col-lg-12" style="margin-top: 15px;">
                            <div class="col-lg-6">
                            <div class="wrap wrap--yellow">
                                        
                                         {{ HTML::image('public/img/icon_wallet.png','', array()) }}
                                        <h2>
                                            <?php 
                                            
//                                            $amount = DB::table('wallets')
//                                                    ->where('wallets.user_id', Session::get('user_id'))
//                                                    ->where('wallets.paid', 1)
//                                                    ->sum('wallets.display_amount');
//                                            
                                            //New wallet  anand 
                                            $credit_amount = DB::table('wallets')
                                                    ->where('wallets.user_id', Session::get('user_id'))
                                                    ->where('wallets.type','Credit')
                                                    ->where('wallets.paid',1)
                                                    ->sum('wallets.display_amount');
                                            
                                            $debit_amount = DB::table('wallets')
                                                    ->where('wallets.user_id', Session::get('user_id'))
                                                    ->where('wallets.type', 'Debit')
                                                    ->where('wallets.paid', 1)
                                                    ->sum('wallets.display_amount');

                                            $amount = abs($credit_amount- $debit_amount);
                                            
                                          //  print_r($amount);
                                            ?>
                                            {{ App::make("HomeController")->numberformat($amount, 2) }}
                                        </h2>
                                        <p>Your Wallet Balance</p>
                                        <span class="-gap"></span><span class="-gap"></span>
                                        <p>Make Sure To Review Your Order Details Now.</p>
                                        <p>Once You Press 'place Your Order' You'll Be Directed To Paypal To Enter Your Payment Information And Process The Order</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="wrap wrap--gray">
                                    {{ HTML::image('public/img/front/paypal.png','', array()) }}
                                    <span class="-gap"></span>
                                    <span class="-gap"></span>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="field-set">
                                                <div class="caption-wraper">
                                                    <label class="field_label">Enter Amount <span class="require">*</span></label>
                                                </div>
                                                <div class="field-wraper">
                                                    <div class="field_cover">
                                                        {{  Form::text('amount', Input::old("amount"),  array('class' => 'required  form-control','id'=>"amount",'min'=>'0.0'))}}																	</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="field-set">
                                                <div class="caption-wraper">
                                                    <label class="field_label hide--mobile"></label>
                                                </div>
                                                <div class="field-wraper">
                                                    <div class="field_cover">
                                                        {{ Form::submit('Submit', array('class' => "btn btn-primary",'id'=>'bubbb')) }}																 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                               
                            </div>
                           

                        
                           
                            
                            </div>


                         <div class="informetion_bx">

                            <div class="informetion_bxes">
                                <?php
                                if (!$records->isEmpty()) {
                                    ?>
                                    <div class="table_dcf">
                                        <div class="tr_tables">
                                            <div class="td_tables">Sr. No.</div>
                                            <div class="td_tables">Description</div>
                                            <div class="td_tables">Date</div>
                                            <div class="td_tables">Debit</div>
                                            <div class="td_tables">Credit</div>
                                            <div class="td_tables">Balance</div>
                                            
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
                                                   <?php  echo $i; ?>
                                                </div>
                                                <div data-title="Name" class="td_tables2 ">
                                                  <?php 
                                                  if($data->paid == 1){
                                                  if($data->comment != ""){
                                                      echo $data->comment;
                                                  }else{
                                                      echo '-';
                                                  }}else{
                                                      echo "<span style='color:#f00;'>Payment could not be complete.</span>";
                                                  }
                                                  
                                                  
                                                  ?>
                                                   
                                                   
                                                </div>
                                                <div data-title="Name" class="td_tables2 ttvb">
                                                    
                                                     {{  date("d M, Y h:i A", strtotime($data->created)) }}
                                                   
                                                </div>
                                                <div data-title="Email Address" class="td_tables2">
                                                    <?php 
                                                  if($data->type == "Debit"){
                                                     ?>{{ App::make("HomeController")->numberformat($data->display_amount, 2) }}<?php
                                                  }else{
                                                      echo '-';
                                                  }
                                                  ?>
                                                    
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                    <?php 
                                                  if($data->type == "Credit"){
                                                     ?>{{ App::make("HomeController")->numberformat($data->display_amount, 2) }}<?php
                                                  }else{
                                                      echo '-';
                                                  }
                                                  ?>
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                   {{ App::make("HomeController")->numberformat($data->balance, 2) }}
                                                </div>
                                                
                                            </div>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                        
                                    <?php } else {
                                        ?>
<!--                                        <div class="no-record">
                                            No records available
                                        </div>-->
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
        </div>
    </div>
</section>
@stop

