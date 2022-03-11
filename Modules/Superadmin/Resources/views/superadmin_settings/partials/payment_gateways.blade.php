<div class="pos-tab-content">
    <div class="row">
    	<h4>Stripe:</h4>
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('STRIPE_PUB_KEY', __('superadmin::lang.stripe_pub_key') . ':') !!}
            	{!! Form::text('STRIPE_PUB_KEY', $default_values['STRIPE_PUB_KEY'], ['class' => 'form-control','placeholder' => __('superadmin::lang.stripe_pub_key')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('STRIPE_SECRET_KEY', __('superadmin::lang.stripe_secret_key') . ':') !!}
            	{!! Form::text('STRIPE_SECRET_KEY', $default_values['STRIPE_SECRET_KEY'], ['class' => 'form-control','placeholder' => __('superadmin::lang.stripe_secret_key')]); !!}
            </div>
        </div>

        <div class="clearfix"></div>
        
        <h4>Paypal:</h4>
        <div class="col-xs-6">
            <div class="form-group">
            	{!! Form::label('PAYPAL_MODE', __('superadmin::lang.paypal_mode') . ':') !!}
            	{!! Form::select('PAYPAL_MODE',['live' => 'Live', 'sandbox' => 'Sandbox'],  $default_values['PAYPAL_MODE'], ['class' => 'form-control','placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_SANDBOX_API_USERNAME', __('superadmin::lang.paypal_sandbox_api_username') . ':') !!}
            	{!! Form::text('PAYPAL_SANDBOX_API_USERNAME', $default_values['PAYPAL_SANDBOX_API_USERNAME'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_sandbox_api_username')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_SANDBOX_API_PASSWORD', __('superadmin::lang.paypal_sandbox_api_password') . ':') !!}
            	{!! Form::text('PAYPAL_SANDBOX_API_PASSWORD', $default_values['PAYPAL_SANDBOX_API_PASSWORD'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_sandbox_api_password')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_SANDBOX_API_SECRET', __('superadmin::lang.paypal_sandbox_api_secret') . ':') !!}
            	{!! Form::text('PAYPAL_SANDBOX_API_SECRET', $default_values['PAYPAL_SANDBOX_API_SECRET'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_sandbox_api_secret')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_LIVE_API_USERNAME', __('superadmin::lang.paypal_live_api_username') . ':') !!}
            	{!! Form::text('PAYPAL_LIVE_API_USERNAME', $default_values['PAYPAL_LIVE_API_USERNAME'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_live_api_username')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_LIVE_API_PASSWORD', __('superadmin::lang.paypal_live_api_password') . ':') !!}
            	{!! Form::text('PAYPAL_LIVE_API_PASSWORD', $default_values['PAYPAL_LIVE_API_PASSWORD'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_live_api_password')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_LIVE_API_SECRET', __('superadmin::lang.paypal_live_api_secret') . ':') !!}
            	{!! Form::text('PAYPAL_LIVE_API_SECRET', $default_values['PAYPAL_LIVE_API_SECRET'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_live_api_secret')]); !!}
            </div>
        </div>

        <div class="clearfix"></div>
        
        <h4>Razorpay: <small>(For INR India)</small></h4>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('RAZORPAY_KEY_ID', 'Key ID:') !!}
                {!! Form::text('RAZORPAY_KEY_ID', $default_values['RAZORPAY_KEY_ID'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('RAZORPAY_KEY_SECRET', 'Key Secret:') !!}
                {!! Form::text('RAZORPAY_KEY_SECRET', $default_values['RAZORPAY_KEY_SECRET'], ['class' => 'form-control']); !!}
            </div>
        </div>




        <div class="clearfix"></div>
        
        <h4>Pesapal: <small>(For KES currency)</small></h4>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PESAPAL_CONSUMER_KEY', 'Consumer Key:') !!}
                {!! Form::text('PESAPAL_CONSUMER_KEY', $default_values['PESAPAL_CONSUMER_KEY'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PESAPAL_CONSUMER_SECRET', 'Consumer Secret:') !!}
                {!! Form::text('PESAPAL_CONSUMER_SECRET', $default_values['PESAPAL_CONSUMER_SECRET'], ['class' => 'form-control']); !!}
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PESAPAL_LIVE', 'Is Live?') !!}
                {!! Form::select('PESAPAL_LIVE',['false' => 'False', 'true' => 'True'],  $default_values['PESAPAL_LIVE'], ['class' => 'form-control']); !!}
            </div>
        </div>


        <div class="clearfix"></div>
        
        <h4>Payhere:</h4>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PAYHERE_MERCHANT_ID', 'Merchant ID:') !!}
                {!! Form::text('PAYHERE_MERCHANT_ID', $default_values['PAYHERE_MERCHANT_ID'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PAYHERE_MERCHANT_SECRET', 'Merchant Secret:') !!}
                {!! Form::text('PAYHERE_MERCHANT_SECRET', $default_values['PAYHERE_MERCHANT_SECRET'], ['class' => 'form-control']); !!}
            </div>
        </div>
      
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PAYHERE_LIVE', 'Is Live?') !!}
                {!! Form::select('PAYHERE_LIVE',['false' => 'False', 'true' => 'True'],  !empty($default_values['PAYHERE_LIVE'])?'true':'flase', ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <h4>@lang('superadmin::lang.pay_online'):</h4>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PAY_ONLINE_CURRENCY_TYPE', 'Currency Type:') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-money"></i>
                    </span>
                    {!! Form::select('PAY_ONLINE_CURRENCY_TYPE[]', $currencies, !empty($settings['PAY_ONLINE_CURRENCY_TYPE']) ? json_decode($settings['PAY_ONLINE_CURRENCY_TYPE'], true) : null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'required', 'multiple']); !!}
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PAY_ONLINE_STARTING_NO', 'Starting No:') !!}
                {!! Form::text('PAY_ONLINE_STARTING_NO', $default_values['PAY_ONLINE_STARTING_NO'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <h4>@lang('superadmin::lang.pay_offline'):</h4>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PAY_ONLINE_BANK_NAME', 'Bank Name:') !!}
                {!! Form::text('PAY_ONLINE_BANK_NAME', $default_values['PAY_ONLINE_BANK_NAME'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PAY_ONLINE_BRANCH_NAME', 'Branch Name:') !!}
                {!! Form::text('PAY_ONLINE_BRANCH_NAME', $default_values['PAY_ONLINE_BRANCH_NAME'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PAY_ONLINE_ACCOUNT_NO', 'Account No:') !!}
                {!! Form::text('PAY_ONLINE_ACCOUNT_NO', $default_values['PAY_ONLINE_ACCOUNT_NO'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PAY_ONLINE_ACCOUNT_NAME', 'Account Name:') !!}
                {!! Form::text('PAY_ONLINE_ACCOUNT_NAME', $default_values['PAY_ONLINE_ACCOUNT_NAME'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PAY_ONLINE_SWIFT_CODE', 'Swift Code:') !!}
                {!! Form::text('PAY_ONLINE_SWIFT_CODE', $default_values['PAY_ONLINE_SWIFT_CODE'], ['class' => 'form-control']); !!}
            </div>
        </div>
      

        <div class="clearfix"></div>
        <div class="col-xs-12">
            <br/>
            <p class="help-block"><i>@lang('superadmin::lang.payment_gateway_help')</i></p>
        </div>
    </div>
</div>