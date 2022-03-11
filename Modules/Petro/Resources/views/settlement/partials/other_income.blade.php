<br>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('other_income_product_id', __( 'petro::lang.service' ) ) !!}
                {!! Form::select('other_income_product_id', $services, null, ['class' => 'form-control other_income_fields check_pumper other_income_product', 'required',
                'placeholder' => __(
                'petro::lang.please_select' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('other_income_price', __( 'petro::lang.price' ) ) !!}
                {!! Form::text('other_income_price', null, ['class' => 'form-control other_income_fields check_pumper other_income_price input_number', 'readonly',
                'placeholder' => __(
                'petro::lang.price' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('other_income_qty', __( 'petro::lang.qty' ) ) !!}
                {!! Form::text('other_income_qty', null, ['class' => 'form-control other_income_fields check_pumper other_income_qty input_number', 'required',
                'placeholder' => __(
                'petro::lang.qty' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('other_income_reason', __( 'petro::lang.reason' ) ) !!}
                {!! Form::text('other_income_reason', null, ['class' => 'form-control other_income_fields check_pumper other_income_reason', 'required',
                'placeholder' => __(
                'petro::lang.reason' ) ]); !!}
            </div>
        </div>

        <div class="col-md-3">
            @can('edit_other_income_prices')
            <button type="submit" class="btn btn-warning edit_price_other_income" data-toggle="modal" data-target="#edit_price_other_income" style="margin-top: 23px; margin-right: 5px;">@lang('petro::lang.edit_price')</button>
            @endcan
            <button type="submit" class="btn btn-primary btn_other_income" style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
    </div>
</div>
<br>
<br>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="other_income_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.service' )</th>
                    <th>@lang('petro::lang.qty' )</th>
                    <th>@lang('petro::lang.reason' )</th>
                    <th>@lang('petro::lang.sub_total' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                $other_income_final_total = 0.00;
                @endphp
                @if (!empty($active_settlement))
                @foreach ($active_settlement->other_incomes as $other_income_item)
                @php
                $product = App\Product::where('id', $other_income_item->product_id)->first();
                $other_income_final_total = $other_income_final_total + $other_income_item->sub_total;
                @endphp
                <tr>
                    <td>{{$product->name}}</td>
                    <td>{{@num_format($other_income_item->qty)}}</td>
                    <td>{{$other_income_item->reason}}</td>
                    <td>{{@num_format($other_income_item->sub_total)}}</td>
                    <td><button class="btn btn-xs btn-danger delete_other_income" data-href="/petro/settlement/delete-other-income/{{$other_income_item->id}}"><i
                                class="fa fa-times"></i></td>
                </tr>
                @endforeach
                @endif
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">@lang('petro::lang.other_income_total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="other_income_total">
                        {{@num_format( $other_income_final_total)}}</td>
                    <td></td>
                </tr>
                <input type="hidden" value="{{$other_income_final_total}}" name="other_income_total" id="other_income_total">
            </tfoot>
        </table>
    </div>
</div>


<div class="modal" tabindex="-1" role="dialog" id="edit_price_other_income">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">@lang('petro::lang.edit_price')</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <label for="other_income_edit_price">@lang('petro::lang.price'): </label>
          <input type="text" value="0" name="other_income_edit_price" id="other_income_edit_price" placeholder="Price" class="form-control">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="save_edit_price_other_income_btn">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
