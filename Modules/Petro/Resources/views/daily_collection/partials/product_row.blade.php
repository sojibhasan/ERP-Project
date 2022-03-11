<tr>
  <td>
    {!! Form::select('daily_voucher['.$index.'][product_id]', $products, null, ['class' => 'form-control select2
    product_id', 'style' => 'width:100%;', 'required', 'placeholder' =>
    __( 'petro::lang.please_select')]) !!}
  </td>
  <td>
    {!! Form::text('daily_voucher['.$index.'][unit_price]', 0, ['class' => 'form-control unit_price', 'style' =>
    'width: 120px;', 'placeholder' => __('petro::lang.unit_price'), 'readonly']) !!}
  </td>
  <td>
    {!! Form::text('daily_voucher['.$index.'][qty]', 0, ['class' => 'form-control qty', 'style' => 'width:
    120px;', 'placeholder' => __('petro::lang.qty')]) !!}
  </td>
  <td>
    {!! Form::text('daily_voucher['.$index.'][sub_total]', 0, ['class' => 'form-control sub_total', 'style' =>
    'width: 120px;', 'placeholder' => __('petro::lang.sub_total')]) !!}
  </td>
  <td>
    <button type="button" class="btn btn-xs btn-primary add_row" style="margin-top: 6px;">+</button>
</td>
</tr>