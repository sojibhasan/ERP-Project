<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('AllergiesController@store'), 'method' => 'post', 'id' => 'add_allergie_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'patient.add_allergie_&_notes' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <input type="hidden" name="patient_code" id="patient_code" value="{{$patient_code}}">
                <div class="form-group col-sm-4">
                    {!! Form::label('date', __('patient.date'), []) !!}
                  {!! Form::text('date', date('m/d/Y'), ['class' => 'form-control', 'id' => 'allergy_date']) !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::label('allergy_name', __('patient.allergie_&_notes'), []) !!}
                  {!! Form::textarea('allergy_name', null, ['class' => 'form-control', 'id' => 'allergy_name', 'rows' => 4, 'cols' => 5, 'style' => 'width:100%;']) !!}
                </div>
               
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="add_allergie_btn">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#allergy_date').datepicker({ defaultDate: new Date() });

    $('#add_allergie_btn').click(function(e){
        e.preventDefault();
        url = "{{action('AllergiesController@store')}}";
        data = $('#add_allergie_form').serialize();
        
        $.ajax({
            method: 'POST',
            url: url,
            data: data,
            success: function(result) {
               if(result.success == 1){
                   toastr.success(result.msg)
               }else{
                   toastr.error(result.msg)
               }
               allergie_table.ajax.reload();
               $('.add_modal').empty().modal('hide');
                
            },
        });
    });
</script>