<div class="pos-tab-content @if(session('status.tab') == 'tax') active @endif">
<section class="content">
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\TaxController@store'), 'method' =>
            'post', 'id' => 'salary_component_form' ])!!}
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'hr::lang.all_tax')])
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="tax_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'hr::lang.tax_order' )</th>
                                <th>@lang( 'hr::lang.name' )</th>
                                <th>@lang( 'hr::lang.slab_amount' )</th>
                                <th>@lang( 'hr::lang.type' )</th>
                                <th>@lang( 'hr::lang.tax_rates' )</th>
                                <th>@lang( 'hr::lang.slab_wise_rates' )</th>
                                <th>@lang( 'hr::lang.selected_previous_slab' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($taxes->count() > 0)
                            @php
                                $i = 1;
                                $l =0;
                            @endphp
                            @foreach ($taxes as $tax)
                                
                            <tr data-index="{{$l}}" id="{{$l}}">
                                <td>{{$i}} {!! Form::hidden('tax['.$l.'][id]', $tax->id, ['class' => 'id']) !!}</td>
                                <td>{!! Form::text('tax['.$l.'][name]', $tax->name, ['class' => 'form-control name']) !!}</td>
                                <td>{!! Form::number('tax['.$l.'][slab_amount]', $tax->slab_amount, ['class' => 'form-control slab_amount']) !!}</td>
                                <td>{!! Form::select('tax['.$l.'][type]', ['fixed' => __('hr::lang.fixed'), 'percentage' => __('hr::lang.percentage')], $tax->type, ['class' => 'form-control type', 'placeholder'=> __('hr::lang.please_select')]) !!}</td>
                                <td>{!! Form::number('tax['.$l.'][tax_rate]', $tax->tax_rate, ['class' => 'form-control tax_rate']) !!}</td>
                                <td>{!! Form::select('tax['.$l.'][slab_wise_rates]', ['yes' => __('hr::lang.yes'), 'no' => __('hr::lang.no')], $tax->slab_wise_rates, ['class' => 'form-control slab_wise_rates', 'placeholder'=> __('hr::lang.please_select')]) !!}</td>
                                <td class="previous_slab_td">{!! Form::hidden('tax['.$l.'][previous_slab]', implode((array)$tax->slab_wise_rates), ['class' => 'form-control previous_slab']) !!}
                                @if($tax->slab_wise_rates == 'yes')
                                @for($k = 0; $k < $i-1; $k++)
                                <span class="btn btn-sm btn-flat btn-primary" style="margin-bottom:5px;">{{$k+1}}</span> &nbsp;
                                @endfor
                                @endif
                                </td>
                                <td><button type="button" class="btn btn-xs btn-primary add_row_btn"> + </button> &nbsp; <a data-href="{{action('\Modules\HR\Http\Controllers\TaxController@destroy', $tax->id)}}" class="btn btn-xs btn-danger delete-tax">x</a></td>
                            </tr>
                            @php
                                $i++;
                                $l++;
                                @endphp
                            @endforeach
                            {!! Form::hidden('index', $l, ['id' => 'index']) !!}
                            @else
                            <tr data-index="0" id="0">
                                <td>1 {!! Form::hidden('tax[0][id]', null, ['class' => 'id']) !!}</td>
                                <td>{!! Form::text('tax[0][name]', null, ['class' => 'form-control name']) !!}</td>
                                <td>{!! Form::number('tax[0][slab_amount]', null, ['class' => 'form-control slab_amount']) !!}</td>
                                <td>{!! Form::select('tax[0][type]', ['fixed' => __('hr::lang.fixed'), 'percentage' => __('hr::lang.percentage')], null, ['class' => 'form-control type', 'placeholder'=> __('hr::lang.please_select')]) !!}</td>
                                <td>{!! Form::number('tax[0][tax_rate]', null, ['class' => 'form-control tax_rate']) !!}</td>
                                <td>{!! Form::select('tax[0][slab_wise_rates]', ['yes' => __('hr::lang.yes'), 'no' => __('hr::lang.no')], null, ['class' => 'form-control slab_wise_rates', 'placeholder'=> __('hr::lang.please_select')]) !!}</td>
                                <td class="previous_slab_td">{!! Form::hidden('tax[0][previous_slab]', null, ['class' => 'form-control previous_slab']) !!}</td>
                                <td><button type="button" class="btn btn-xs btn-primary add_row_btn"> + </button> &nbsp; <button type="button" class="btn btn-xs btn-danger remove_row_tax">x</button></td>
                            </tr>
                            {!! Form::hidden('index', '0', ['id' => 'index']) !!}

                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix"></div>

            @endcomponent
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary pull-right " id="save_religion_btn">@lang( 'messages.save' )</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>
</div>