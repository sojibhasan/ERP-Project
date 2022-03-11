<div class="modal-dialog" role="document" style="width: 55%">
  <div class="modal-content">
    <style>
      .select2 {
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' => action('CustomerSettingsController@update', $contact->id), 'method' =>
    'put', 'id' => 'sms_form', 'enctype' => 'multipart/form-data' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">{{$contact->name}}</h4>
    </div>

    <div class="modal-body">

      <div class="col-md-12">
        <div class="row">
          <div class="col-md-4">
            {!! Form::label('sell_over_limit', __( 'lang_v1.sell_over_limit' ).':', ['style' => 'margin-top: 5px;']) !!}
            &nbsp;
            <label class="radio-inline">
              {!! Form::radio('sell_over_limit', '0', $contact->sell_over_limit, ['class' => 'input-icheck']); !!}
              @lang('hr::lang.no')
            </label>
            <label class="radio-inline">
              {!! Form::radio('sell_over_limit', 1, $contact->sell_over_limit, ['class' => 'input-icheck']); !!}
              @lang('hr::lang.yes')
            </label>
          </div>
          <div class="col-md-4 approval_div @if(!$contact->sell_over_limit) hide @endif">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('sol_without_approval', 1, $contact->sol_without_approval, ['class' =>
                'input-icheck', 'id' => 'sol_woa']); !!}
                @lang('lang_v1.sol_woa')
              </label>
            </div>
            <div class="checkbox">
              <label>
                {!! Form::checkbox('sol_with_approval', 1, $contact->sol_with_approval, ['class' => 'input-icheck', 'id'
                => 'sol_na']); !!}
                @lang('lang_v1.sol_na')
              </label>
            </div>
          </div>
          <div class="col-md-4 over_limit_percentage  @if(!$contact->sol_without_approval) hide @endif">
            <label for="over_limit_percentage">@lang('lang_v1.over_limit_percentage')</label>
            <input type="text" name="over_limit_percentage" value="{{$contact->over_limit_percentage}}" class="form-control">
          </div>
          <br>
        </div>
      </div>


    </div>
    <div class="clearfix"></div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="save_sms_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('#sol_woa').change(function(){
    if($(this).prop('checked') == true){
      $('.over_limit_percentage').removeClass('hide');
    }else{
      $('.over_limit_percentage').addClass('hide');
    }
  })

  $('form input[type=radio]').change(function(){
    if($(this).val() == 1){
      $('.approval_div').removeClass('hide');
    }else{
      $('.approval_div').addClass('hide');

    }
  })
</script>