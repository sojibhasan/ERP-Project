<tr>
	<td>
		{{$by_product->full_name}}
	
		{{-- <input type="hidden" class="ingredient_price" value="{{$by_product->dpp_inc_tax}}"> --}}
		<input type="hidden" class="by_product_id" value="{{$by_product->id}}">
	</td>
	<td>
		<div class="input-group">
			{!! Form::text('by_product[' . $by_product->id . '][production_cost]', !empty($by_product->production_cost) ? @num_format($by_product->production_cost) : 0, ['class' => 'form-control input_number waste_percent input-sm', 'placeholder' => __('lang_v1.production_cost')]); !!}
			<span class="input-group-addon"><i class="fa fa-percent"></i></span>
		</div>
	</td>
	<td>
		<div class="@if(empty($by_product->sub_units)) input-group @else input_inline @endif">
			{!! Form::text('by_product[' . $by_product->id . '][quantity]', !empty($by_product->quantity) ? @num_format($by_product->quantity) : 1, ['class' => 'form-control input_number quantity input-sm', 'placeholder' => __('lang_v1.quantity'), 'required']); !!}
			<span class="@if(empty($by_product->sub_units)) input-group-addon @endif">
				@if(!empty($by_product->sub_units))
					<select name="by_product[{{$by_product->id}}][sub_unit_id]" class="form-control input-sm row_sub_unit_id">
						@foreach($by_product->sub_units as $key => $value)
							<option 
								value="{{$key}}"
								data-multiplier="{{$value['multiplier']}}"
								@if(!empty($by_product->sub_unit_id) && $key == $by_product->sub_unit_id)
									selected
								@endif
								>{{$value['name']}}
							</option>
						@endforeach
					</select>
				@else
					{!! $by_product->unit !!}
				@endif
			</span>
		</div>
	</td>
	
	<td><button type="button" class="btn btn-danger btn-xs remove_by_product"><i class="fa fa-close"></i></button></td>
</tr>