<div class="modal-dialog modal-lg" role="document" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('account.add_journal')</h4>
        </div>
        <div class="modal-body">
            {!! Form::open(['url' => action('JournalController@store'), 'method' => 'post' ]) !!}
            <input type="hidden" id="index" name="index" value="1">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('journal_id', __('account.journal_no')) !!}
                            {!! Form::text('journal_id', $journal_id, ['class' => 'form-control
                            journal_id',
                            'requried', 'readonly'])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('date', __('account.date')) !!}
                            {!! Form::text('date', null, ['class' => 'form-control
                            journal_date',
                            'requried'])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('select_location', __('account.select_location')) !!}
                            {!! Form::select('location_id', $locations, $default_location_id, [ 'class' =>
                            'form-control select2','style' => 'width:100%', 'id' => 'location_id',
                            'requried' , 'placeholder'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('is_opening_balance', __('account.opening_balance')) !!}
                            {!! Form::select('is_opening_balance',['yes' => 'Yes', 'no' => 'No'], null, [ 'class' =>
                            'form-control select2','style' => 'width:100%', 'id' => 'is_opening_balance',
                            'requried' , 'placeholder', 'disabled'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('note', __('account.note')) !!}
                            {!! Form::textarea('note', null, ['class' => 'form-control', 'rows' => 2, 'cols' => 10,])
                            !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 journal_rows">
                <div class="row journal_row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('account_type_id', __('account.select_account_type')) !!}
                            {!! Form::select('journal[0][account_type_id]', $account_types, null, [ 'class' =>
                            'form-control select2 account_type_ids','style' => 'width:100%',
                            'requried' , 'placeholder'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('account_id', __('account.select_account')) !!}
                            {!! Form::select('journal[0][account_id]', [], null, [ 'class' =>
                            'form-control select2 account_ids','style' => 'width:100%',
                            'requried' , 'placeholder'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('amount', __('account.debit_amount')) !!}
                            {!! Form::text('journal[0][debit_amount]', null, [ 'class' => 'form-control debit_amount0
                            debit',
                            'requried' , 'placeholder' => __('account.amount')]) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('amount', __('account.credit_amount')) !!}
                            {!! Form::text('journal[0][credit_amount]', null, [ 'class' => 'form-control credit_amount0
                            credit',
                            'requried' , 'placeholder' => __('account.amount')]) !!}
                        </div>
                    </div>


                    <div class="col-md-2">
                        <button class="btn btn-xs btn-primary add_row" data-index="0"
                            style="margin-top: 27px;">+</button>
                        <button class="btn btn-xs btn-danger remove_row" data-index="0"
                            style="margin-top: 27px;">-</button>
                    </div>
                </div>
                <div class="row journal_row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::select('journal[1][account_type_id]', $account_types, null, [ 'class' =>
                            'form-control select2 account_type_ids','style' => 'width:100%',
                            'requried' , 'placeholder'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::select('journal[1][account_id]', [], null, [ 'class' =>
                            'form-control select2 account_ids','style' => 'width:100%',
                            'requried' , 'placeholder'
                            =>
                            'Please select']) !!}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::text('journal[1][debit_amount]', null, [ 'class' => 'form-control debit_amount1
                            debit',
                            'requried' , 'placeholder' => __('account.amount')]) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::text('journal[1][credit_amount]', null, [ 'class' => 'form-control credit_amount1
                            credit',
                            'requried' , 'placeholder' => __('account.amount')]) !!}
                        </div>
                    </div>


                    <div class="col-md-2">
                        <button class="btn btn-xs btn-primary add_row" data-index="1"
                            style="margin-top: 7px;">+</button>
                        <button class="btn btn-xs btn-danger remove_row" data-index="1"
                            style="margin-top: 7px;">-</button>
                    </div>
                </div>
            </div>


            <div class="modal-footer">
                <div class="col-md-6">
                    <h4>@lang('account.total')</h4>
                </div>
                <div class="col-md-2">
                    {!! Form::text('debit_total', null, [ 'class' => 'form-control debit_total', 'readonly', 'style' =>
                    'width:100%;',
                    'requried']) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::text('credit_total', null, [ 'class' => 'form-control credit_total', 'readonly', 'style'
                    => 'width:100%;',
                    'requried']) !!}
                </div>

                <button type="submit" class="btn btn-primary add_btn">@lang( 'messages.add' )</button>

                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
        $('.journal_date').datepicker("setDate", new Date());
        $('.account_ids').select2();
        $('.account_type_ids').select2();
        $('.add_btn').attr('disabled', true);
        
        $('body').on('click', '.add_row', function(e) {
            e.preventDefault();
            index = parseInt($('#index').val()) +1 ;
            debit_account_option = $('.debit_amount0').children();
            credit_account_option = $('.credit_amount0').children();
            $('#index').val(index);
            $.ajax({
                method: 'get',
                url: "{{action('JournalController@getRow')}}",
                data: { index:index },
                type : 'html',
                success: function(result) {
                    $('.journal_rows').append(result);
                },
            }).then(function(){
                $('body').find('.journal_date'+index).datepicker("setDate", new Date());
                $('body').find('.debit_account_option'+index).select2();
                $('body').find('.credit_account_option'+index).select2();
            });  
        });

        $('body').on('click', '.remove_row', function(e) {
            e.preventDefault();
            $(this).closest('div.row').remove();
            calculate_total();
        });


        $('.debit_amount0, .credit_amount0').change(function(){
            if($('.debit_amount0').val()){
                $('.credit_amount0').attr('disabled', 'disabled');
            }
            else if($('.credit_amount0').val()){
                $('.debit_amount0').attr('disabled', 'disabled');
            }else{
                $('.debit_amount0').attr('disabled', false);
                $('.credit_amount0').attr('disabled', false);
            }
            calculate_total();
        })
        $('.debit_amount1, .credit_amount1').change(function(){
            if($('.debit_amount1').val()){
                $('.credit_amount1').attr('disabled', 'disabled');
            }
            else if($('.credit_amount1').val()){
                $('.debit_amount1').attr('disabled', 'disabled');
            }else{
                $('.debit_amount1').attr('disabled', false);
                $('.credit_amount1').attr('disabled', false);
            }

            calculate_total();
        })

        function calculate_total() {
            let debit = 0;
            let credit = 0;
            $('.debit').each(function () {
                if($(this).val() != ''){
                    debit += parseFloat($(this).val());
                }
                
            });
            $('.credit').each(function () {
                if($(this).val() != ''){
                    credit += parseFloat($(this).val());
                }
            });

            $('.debit_total').val(debit);
            $('.credit_total').val(credit);

            if(debit == credit){
                $('.add_btn').attr('disabled', false);
            }else{
                $('.add_btn').attr('disabled', true);
            }
        }

        $(document).on('change', '.account_type_ids',function () {
            let account_type_id = $(this).val();
            var this_row = $(this).closest('.journal_row');
            $.ajax({
                method: 'get',
                url: '/accounting-module/journals/get-account-dropdown-by-type/'+account_type_id,
                contentType: 'html',
                data: {  },
                success: function(result) {
                    $(this_row).find('.account_ids').empty().append(result);
                },
            });
        })
        $(document).on('change', '#is_opening_balance',function () {
            if($(this).val() === 'yes'){
                $('#note').attr('required', true);
            }else{
                $('#note').attr('required', false);
            }
        });

    </script>