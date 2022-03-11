<div class="modal-dialog" role="document" style="width: 55%">
  <div class="modal-content">
    <style>
      .select2 {
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' => action('\Modules\SMS\Http\Controllers\SMSController@store'), 'method' =>
    'post', 'id' => 'sms_form', 'enctype' => 'multipart/form-data' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'sms::lang.send_sms' )</h4>
    </div>

    <div class="modal-body">

      <div class="col-md-12">
        <div class="form-group">
          <label for="numbers"> @lang( 'sms::lang.numbers' ) <small style="color: red;">
              @lang('sms::lang.numbers_instructions')</small></label>
          {!! Form::textarea('numbers', null, ['class' => 'form-control', 'placeholder' => __(
          'sms::lang.numbers' ), 'style' => 'width: 100%', 'rows' => 3,
          'id' => 'numbers']);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('group', __( 'sms::lang.group' )) !!}
          {!! Form::select('is_group', ['yes' => __('sms::lang.yes'), 'no' =>
          __('sms::lang.no')], null, ['class' => 'form-control select2',
          'required',
          'placeholder' => __(
          'sms::lang.please_select' ), 'id' => 'group']);
          !!}
        </div>
      </div>

      <div class="col-md-4 group_input hide">
        <div class="form-group">
          {!! Form::label('member_group', __( 'sms::lang.member_group' )) !!}
          {!! Form::select('member_group', $member_groups, null, ['class' => 'form-control select2',
          'placeholder' => __(
          'sms::lang.please_select' ), 'id' => 'member_group']);
          !!}
        </div>
      </div>

      <div class="col-md-4 group_input hide">
        <div class="form-group">
          {!! Form::label('balamandala', __( 'sms::lang.balamandala' )) !!}
          {!! Form::select('balamandala', $balamandalas, null, ['class' => 'form-control select2',
          'placeholder' => __(
          'sms::lang.please_select' ), 'id' => 'balamandala']);
          !!}
        </div>
      </div>
      <div class="col-md-4 group_input hide">
        <div class="form-group">
          {!! Form::label('gramseva_vasama', __( 'sms::lang.gramseva_vasama' )) !!}
          {!! Form::select('gramseva_vasama', $gramseva_vasamas, null, ['class' => 'form-control select2',
          'placeholder' => __(
          'sms::lang.please_select' ), 'id' => 'gramseva_vasama']);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="checkbox">
          <label style="margin-top: 18px;">
            {!! Form::checkbox('remove_duplicates', 1, false, ['class' => 'input-icheck', 'id' => 'remove_duplicates']);
            !!}
            {{__('sms::lang.remove_duplicates')}}
          </label>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('message_type', __( 'sms::lang.message_type' )) !!}
          {!! Form::select('message_type', ['text' => 'Text', 'unicode' => 'Unicode'], null, ['class' => 'form-control
          select2',
          'required',
          'placeholder' => __(
          'sms::lang.please_select' ), 'id' => 'message_type']);
          !!}
        </div>
      </div>


      <div class="col-md-12">
        <div class="form-group">
          <label for="message"> @lang( 'sms::lang.message' )</label>
          {!! Form::textarea('message', null, ['class' => 'form-control', 'placeholder' => __(
          'sms::lang.message' ), 'style' => 'width: 100%', 'rows' => 3,
          'id' => 'message']);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('characters', __( 'sms::lang.characters' )) !!}
          {!! Form::text('characters', null, ['class' => 'form-control', 'required', 'placeholder' => __(
          'sms::lang.characters' ), 'readonly',
          'id' => 'characters']);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('count_message', __( 'sms::lang.sms' )) !!}
          {!! Form::text('count_message', null, ['class' => 'form-control', 'required', 'placeholder' => __(
          'sms::lang.count_message' ), 'readonly',
          'id' => 'count_message']);
          !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('schedule', __( 'sms::lang.schedule' )) !!}
          {!! Form::checkbox('schedule', 1, false, ['class' => 'input-icheck', 'id' => 'schedule']);!!}
          {!! Form::select('timezone', ['Asia/Kolkata' => 'Asia/Kolkata'], 'Asia/Kolkata', ['class' => 'form-control',
          'required', 'readonly', 'id' => 'timezone']);
          !!}
        </div>
      </div>

      <div class="col-md-12">
        <div class="form-group">
          {!! Form::text('schedule_date_time', date('m/d/Y H:i:s'), ['class' => 'form-control', 'required',
          'placeholder' => __(
          'sms::lang.date' ), 'readonly',
          'id' => 'schedule_date_time']);
          !!}
        </div>
      </div>
      <input type="hidden" name="is_unicode" value="0" id="is_unicode">
     
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
  $('#sms_date').datepicker({
        format: 'mm/dd/yyyy'
    });
   $('.select2').select2();

$('#group').change(function(){
  if($(this).val() == 'yes'){
    $('.group_input').removeClass('hide');
  }else{
    $('.group_input').addClass('hide');
  }
});
$('#schedule_date_time').datetimepicker({
    format: moment_date_format + ' ' + moment_time_format,
    ignoreReadonly: true,
});
      

$('#numbers').keyup(function(e){
  if (e.keyCode == 13) {
    e.preventDefault();
    this.value = this.value.substring(0, this.selectionStart) + "" + "\n" + this.value.substring(this.selectionEnd, this.value.length);
  }
});

$('#message').keyup(function(e){
  let is_unicode = false;
  let normal_count = 0;
  let sepcial_count = 0;
  let count_message = 0;
  let characters = 0;
  if (e.keyCode == 13) {
    e.preventDefault();
    this.value = this.value.substring(0, this.selectionStart) + "" + "\n" + this.value.substring(this.selectionEnd, this.value.length);
  }

  var textarea  = document.getElementById('message').value;
  var specialCharacters = textarea.match(/[@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?\n]/g);

  is_unicode = isUnicode(textarea);
   
  normal_count = parseInt($(this).val().length);
  if(specialCharacters !== null) {
    sepcial_count = parseInt(specialCharacters.length);
  }

  if(is_unicode == true ){
    characters = parseInt(normal_count * 2);
    $('#characters').val(characters);
    $('#is_unicode').val('1');

    count_message = Math.floor(characters / 70) ;
  }else{
    characters = parseInt(sepcial_count + normal_count);
    $('#characters').val(characters);

    count_message = Math.floor(characters / 160) ;
  }

  $('#count_message').val(count_message +1)
  
});

function isUnicode(str) {
    for (var i = 0, n = str.length; i < n; i++) {
        if (str.charCodeAt( i ) > 255) { return true; }
    }
    return false;
}
</script>