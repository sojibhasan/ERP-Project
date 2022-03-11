<div class="row journal_row">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::select('journal['.$index.'][account_type_id]', $account_types, null, [ 'class' =>
            'form-control select2 account_type_ids','style' => 'width:100%',
            'requried' , 'placeholder'
            =>
            'Please select']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::select('journal['.$index.'][account_id]', [], null, [ 'class' =>
            'form-control select2 account_ids','style' => 'width:100%',
            'requried' , 'placeholder'
            =>
            'Please select']) !!}
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            {!! Form::text('journal['.$index.'][debit_amount]', null, [ 'class' => 'form-control debit debit_amount'.$index,
            'requried' , 'placeholder' => __('account.amount')]) !!}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::text('journal['.$index.'][credit_amount]', null, [ 'class' => 'form-control credit credit_amount'.$index,
            'requried' , 'placeholder' => __('account.amount')]) !!}
        </div>
    </div>

    <div class="col-md-2">
    <button class="btn btn-xs btn-primary add_row" data-index="{{$index}}"
            style="margin-top: 5px;">+</button>
    <button class="btn btn-xs btn-danger remove_row" data-index="{{$index}}"
            style="margin-top: 5px;">-</button>
    </div>
</div>

<script>
      $('.debit_amount{{$index}}, .credit_amount{{$index}}').change(function(){
            if($('.debit_amount{{$index}}').val()){
                $('.credit_amount{{$index}}').attr('disabled', 'disabled');
            }
            else if($('.credit_amount{{$index}}').val()){
                $('.debit_amount{{$index}}').attr('disabled', 'disabled');
            }else{
                $('.debit_amount{{$index}}').attr('disabled', false);
                $('.credit_amount{{$index}}').attr('disabled', false);
            }
            calculate_total();
        });
        $('.account_ids').select2();
        $('.account_type_ids').select2();

</script>