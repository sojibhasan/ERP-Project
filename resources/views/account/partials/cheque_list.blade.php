@if($cheque_lists->count() > 0)
@foreach ($cheque_lists as $item)
    <tr>
        <td>
            {!! Form::checkbox('select_cheques[]', $item->id, false, ['class' => 'input-icheck']) !!}
        </td>
        <td>
            {{$item->cheque_number}}
        </td>
        <td>
            @if(!empty($item->cheque_date) && $item->cheque_date != '0000-00-00')
            {{@format_date($item->cheque_date)}}
            @endif
        </td>
        <td>
            {{$item->bank_name}}
        </td>
        <td>
            {{@num_format($item->amount)}}
        </td>
    </tr>
@endforeach
@else
<tr>
    <td colspan="5" class="text-center">
        <p>@lang('account.no_item_found')</p>
    </td>

</tr>
@endif