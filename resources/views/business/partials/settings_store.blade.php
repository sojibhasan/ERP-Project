<div class="pos-tab-content  @if($get_permissions['property_module'] == 1) hide  @endif">
    <div class="row">
        <div class="col-xs-3">
            <div class="form-group">
            	{!! Form::label(__('store.default_store')) !!} @if(!empty($help_explanations['default_store'])) @show_tooltip($help_explanations['default_store']) @endif
                {!! Form::select('default_store', $stores, $business->default_store, ['class' => 'form-control', 'placeholder' => 'Select store', 'id' => 'default_store']) !!}
            </div>
        </div>
    </div>
</div>