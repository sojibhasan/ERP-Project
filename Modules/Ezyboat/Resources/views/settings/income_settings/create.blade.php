<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Ezyboat\Http\Controllers\IncomeSettingController@store'), 'method' =>
    'post', 'id' => 'income_setting_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'ezyboat::lang.income_setting' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('income_name', __( 'ezyboat::lang.income_name' ) . ':*') !!}
          {!! Form::text('income_name', null, ['class' => 'form-control', 'required', 'placeholder' => __(
          'ezyboat::lang.income_name' )]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('owner_income', __( 'ezyboat::lang.owner_income' ) . ' % :*') !!}
          {!! Form::text('owner_income', null, ['class' => 'form-control input_number', 'placeholder' => __( 'ezyboat::lang.owner_income'), 'id'
          => 'owner_income']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('crew_income', __( 'ezyboat::lang.crew_income' ) . ' % :*') !!}
          {!! Form::text('crew_income', null, ['class' => 'form-control input_number', 'placeholder' => __( 'ezyboat::lang.crew_income'), 'id'
          => 'crew_income']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('deduct_expense_for_income', __( 'ezyboat::lang.deduct_expense_for_income' ) . ':*') !!}
          {!! Form::select('deduct_expense_for_income', ['yes' => 'Yes', 'no' => 'No'], null, ['class' => 'form-control', 'placeholder' => __( 'ezyboat::lang.please_select'), 'id'
          => 'deduct_expense_for_income']); !!}
        </div>
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
</script>