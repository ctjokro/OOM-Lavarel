@extends('layout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
   
      $.validator.addMethod("bsbno", function (value, element) {
        return  this.optional(element) || (/^[0-9-]+$/.test(value));
    }, "Contact Number is not valid.");
    
    $("#myform1").validate();
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
                    {{ View::make('elements.actionMessage')->render() }}
                   
                    <div class="informetion_top">
                        <div class="tatils">Bank Transfer Payments
                
                        </div>
                        <div class="informetion_bx">

                            <div class="informetion_bxes">
                                
                                        <div class="pery">
                                            <div id="formloader" class="formloader" style="display: none;">
                                                {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                                            </div>
                                            {{ View::make('elements.frontEndActionMessage')->render() }}
                                            {{ Form::open(array('url' => '/user/bankTransferPayment', 'method' => 'post', 'id' => 'myform1', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                                            <span class="require" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>
                                            <br><br>
                                            @if ($message = Session::get('message'))
                        <div class="alert alert-success alert-block">
                        	<button type="button" class="close" data-dismiss="alert">×</button>	
                                <strong>{{ $message }}</strong>
                        </div>
                        @endif
                                            
                
                
                                        
                                        <div class="form_group">
                                            {{ HTML::decode(Form::label('bsbno', "BSB No : <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                           <div class="in_upt">
                                                <?php if($bank_transfer) { ?>{{ Form::text('bsbno',Input::old('bsbno', $bank_transfer->bsbno), array('class' => 'required number form-control','maxlength'=>'16')) }} <?php } else { ?>{{ Form::text('bsbno',Input::old('bsbno', ''), array('class' => 'required number form-control','maxlength'=>'16')) }} <?php } ?>
                                            </div>
                                        </div>
                
                
                                        <div class="form_group">
                                            {{ HTML::decode(Form::label('account_no', "Account Number : <span class='require'>*</span>",array('class'=>"control-label col-lg-2 start_time"))) }}
                                           <div class="in_upt">
                                                <?php if($bank_transfer) { ?> {{ Form::text('account_no',Input::old('account_no', $bank_transfer->account_no), array('class' => 'required number form-control')) }} <?php } else { ?> {{ Form::text('account_no',Input::old('account_no', ''), array('class' => 'required number form-control')) }} <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form_group">
                                            {{ HTML::decode(Form::label('account_name', "Account Name : <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                           <div class="in_upt">
                                                <?php if($bank_transfer) { ?> {{ Form::text('account_name',Input::old('account_name', $bank_transfer->account_name), array('class' => 'required form-control account_name')) }} <?php } else { ?> {{ Form::text('account_name',Input::old('account_name',''), array('class' => 'required form-control account_name')) }} <?php } ?>
                                            </div>
                                        </div>
                
                                           
                                            <div class="form_group input_bxxs">
                                                <label>&nbsp;</label>
                                                <div class="in_upt in_upt_res">
                                                    {{ Form::submit('Submit', array('class' => "btn btn-primary")) }}
                                                    {{ Form::reset('Reset', array('class' => "btn btn-default")) }}
                                                </div>
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


