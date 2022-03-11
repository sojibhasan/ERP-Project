<div class="modal-dialog" role="document" style="width: 55%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\SalaryComponentController@update', $salary_component->id), 'method' =>
    'put', 'id' => 'salary_component_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'hr::lang.add_salary_component' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('component_name', __( 'hr::lang.component_name' ).':') !!}
          {!! Form::text('component_name', $salary_component->component_name, ['class' => 'form-control', 'required', 'placeholder' =>
          __( 'hr::lang.component_name')]);
          !!}
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('component_amount', __( 'hr::lang.component_amount' ).':') !!}
          {!! Form::text('component_amount', $salary_component->component_amount, ['class' => 'form-control', 'required', 'placeholder' =>
          __( 'hr::lang.component_amount')]);
          !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('override', __( 'hr::lang.override' ).':', ['style' => 'margin-top: 5px;']) !!} &nbsp;
          <label class="radio-inline">
            {!! Form::radio('override', 1, $salary_component->override == 1 ? true : false, ['class' => 'input-icheck']); !!} @lang('hr::lang.yes')
          </label>
          <label class="radio-inline">
            {!! Form::radio('override', '0', $salary_component->override == 0 ? true : false, ['class' => 'input-icheck']); !!} @lang('hr::lang.no')
          </label>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('statutory_fund', __( 'hr::lang.statutory_fund' ).':', ['style' => 'margin-top: 5px;']) !!}
          &nbsp;
          <label class="radio-inline">
            {!! Form::radio('statutory_fund', 1, $salary_component->statutory_fund == 1 ? true : false, ['class' => 'input-icheck']); !!} @lang('hr::lang.yes')
          </label>
          <label class="radio-inline">
            {!! Form::radio('statutory_fund', '0', $salary_component->statutory_fund == 0 ? true : false, ['class' => 'input-icheck']); !!} @lang('hr::lang.no')
          </label>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('type', __( 'hr::lang.type' ).':', ['style' => 'margin-top: 5px;']) !!} &nbsp;
          <label class="radio-inline">
            {!! Form::radio('type', 1, $salary_component->type == 1 ? true : false, ['class' => 'input-icheck']); !!} @lang('hr::lang.earning')
          </label>
          <label class="radio-inline">
            {!! Form::radio('type', '2', $salary_component->type == 2 ? true : false, ['class' => 'input-icheck']); !!} @lang('hr::lang.deduction')
          </label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <div class="row">
            <div class="col-md-3">
              {!! Form::label('add_to', __( 'hr::lang.add_to' ).':', ['style' => 'margin-top: 10px;']) !!}
            </div>
            <div class="col-md-4">
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('total_payable', 1, $salary_component->total_payable, ['class' => 'input-icheck', 'id' => 'total_payable']);
                  !!}
                  @lang('hr::lang.total_payable')
                </label>
              </div>

            </div>
            <div class="col-md-5">
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('cost_company', 1, $salary_component->cost_company, ['class' => 'input-icheck', 'id' => 'cost_company']); !!}
                  @lang('hr::lang.cost_to_company')
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('value_type', __( 'hr::lang.value_type' ).':', ['style' => 'margin-top: 10px;']) !!} &nbsp;
          <label class="radio-inline">
            {!! Form::radio('value_type', 1, $salary_component->value_type == 1 ? true : false, ['class' => 'input-icheck']); !!} @lang('hr::lang.amount')
          </label>
          <label class="radio-inline">
            {!! Form::radio('value_type', '2', $salary_component->value_type == 2 ? true : false, ['class' => 'input-icheck']); !!} @lang('hr::lang.percentage')
          </label>
        </div>
      </div>

      @php
          $statutory_payment = (array)json_decode($salary_component->statutory_payment);
      @endphp
      <div class="col-md-12">
        {!! Form::label('add_to', __( 'hr::lang.statutory_payment_aplicable' ).':', ['class' => 'text-red']) !!}
        <br>
        @foreach ($statutory_fund as $key => $item)
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('statutory_payment', $item->component_name.':', ['style' => 'margin-top: 5px;']) !!}
            &nbsp;
            <label class="radio-inline">
              {!! Form::radio('statutory_payment['.$item->id.']', 1, array_key_exists($item->id, $statutory_payment) && $statutory_payment[$item->id] == 1 ? true : false, ['class' => 'input-icheck emptycls']); !!} @lang('hr::lang.yes')
            </label>
            <label class="radio-inline">
              {!! Form::radio('statutory_payment['.$item->id.']', '0', array_key_exists($item->id, $statutory_payment) && $statutory_payment[$item->id] == 0 ? true : false, ['class' => 'input-icheck emptycls']); !!} @lang('hr::lang.no')
            </label>
          </div>
        </div>
        @endforeach
      </div>


      <div class="clearfix"></div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="save_salary_component_btn">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>

      {!! Form::close() !!}

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->

  <script>
   $('input:radio[name="statutory_fund"]').click(function(){
		if ($(this).is(':checked')){
			if($(this).val() == 1){

			}else{
				$('input.emptycls').prop('checked', false);
			}
		}
	});
  </script>