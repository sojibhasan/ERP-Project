<div class="row">
	<div class="col-md-12">
		<table class="table table-striped table-th-green text-center" id="by_product__production_table">
			<thead>
				<tr>
					<th><?php echo app('translator')->getFromJson('manufacturing::lang.by_products'); ?></th>
					<th><?php echo app('translator')->getFromJson('manufacturing::lang.calculate_qty'); ?></th>
					<th><?php echo app('translator')->getFromJson('manufacturing::lang.final_qty'); ?></th>
					<th><?php echo app('translator')->getFromJson('manufacturing::lang.production_cost'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				@if (!empty($by_products))
				@foreach ($by_products as $by_product)
				<tr>
					<td>
						{{$by_product->full_name}}

						{{-- <input type="hidden" class="ingredient_price" value="{{$by_product->dpp_inc_tax}}"> --}}
						<input type="hidden" class="by_product_id" value="{{$by_product->id}}">
						{!! Form::hidden('by_product[' . $by_product->id . '][variation_id]', $by_product->variation_id); !!}
					</td>
					<td>
						<div class="input-group">
							{{!empty($by_product->quantity) ? @num_format($by_product->quantity): 0}}
						</div>
					</td>
					<td>
						<div class="@if(empty($by_product->sub_units)) input-group @else input_inline @endif">
							{!! Form::text('by_product[' . $by_product->id . '][final_quantity]', '0', ['class' =>
							'form-control input_number final_quantity input-sm', 'placeholder' =>
							__('lang_v1.quantity'),
							'required']); !!}
							<span class="@if(empty($by_product->sub_units)) input-group-addon @endif">
								@if(!empty($by_product->sub_units))
								<select name="by_product[{{$by_product->id}}][sub_unit_id]"
									class="form-control input-sm row_sub_unit_id">
									@foreach($by_product->sub_units as $key => $value)
									<option value="{{$key}}" data-multiplier="{{$value['multiplier']}}"
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

					<td><div class="input-group">
						{!! Form::text('by_product[' . $by_product->id . '][production_cost]', !empty($by_product->production_cost) ? @num_format($by_product->production_cost) : 0, ['class' => 'form-control input_number waste_percent input-sm', 'placeholder' => __('lang_v1.production_cost')]); !!}
						<span class="input-group-addon"><i class="fa fa-percent"></i></span>
					</div></td>
				</tr>
				@endforeach
				@endif
			</tbody>
			<tfoot>

			</tfoot>
		</table>
	</div>
</div>