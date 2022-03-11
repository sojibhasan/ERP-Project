<div class="pos-tab-content  @if($get_permissions['property_module'] == 1) hide  @endif">
    <div class="row">
        @foreach($modules as $k => $v)
        @if($k == 'tables' || $k == 'modifiers' || $k == 'service_staff' || $k == 'kitchen')
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('enabled_modules[]', $k,  in_array($k, $enabled_modules) , 
                    ['class' => 'input-icheck']); !!} {{$v['name']}}
                  </label>
                  @if(!empty($help_explanations['res_'.$k])) @show_tooltip($help_explanations['res_'.$k]) @endif
                </div>
            </div>
        </div>
        @endif
    @endforeach
   </div>
</div>