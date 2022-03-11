{!! Form::open(['url' => action('AccountSettingController@store'), 'method' => 'post', 'id' =>
'business_location_add_form' ]) !!}
<div class="row">
    <div class="col-md-12">
        <h3>@lang('lang_v1.account_opening_balances')</h3>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date', __( 'lang_v1.date' ) . ':') !!}
                    {!! Form::text('date', null, ['class' => 'form-control',
                    'placeholder' => __(
                    'lang_v1.date' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('group_id', __( 'lang_v1.account_group' ) . ':') !!}
                    {!! Form::select('group_id', $account_groups, null, ['placeholder' =>
                    __('messages.please_select'), 'requied','style' => 'width: 100%', 'class' => 'form-control
                    select2']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('account_id', __( 'lang_v1.account' ) . ':') !!}
                    {!! Form::select('account_id', [], null, ['placeholder' =>
                    __('messages.please_select'), 'requied','style' => 'width: 100%', 'class' => 'form-control
                    select2']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('amount', __( 'lang_v1.amount' ) . ':') !!}
                    {!! Form::text('amount', null, ['class' => 'form-control',
                    'placeholder' => __(
                    'lang_v1.amount' ) ]); !!}
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary pull-right">@lang('lang_v1.save')</button>
    </div>
</div>

{!! Form::close() !!}
<br>
<div class="clearfix"></div>
<br>

<div class="row">
    <div class="col-md-12">
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="account_setting_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang( 'lang_v1.date' )</th>
                        <th>@lang( 'lang_v1.account_group' )</th>
                        <th>@lang( 'lang_v1.account' )</th>
                        <th>@lang( 'lang_v1.amount' )</th>
                        <th>@lang( 'lang_v1.added_by' )</th>
                        <th class="notexport">@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
        </div>
    
    </div>
</div>