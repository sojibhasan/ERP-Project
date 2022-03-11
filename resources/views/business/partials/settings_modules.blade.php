<div class="pos-tab-content">
  <div class="row">
    @if(!empty($modules))
    <h4>@lang('lang_v1.enable_disable_modules')</h4>
   
    @foreach($modules as $k => $v)
    @if($enabled_moudle_by_subscription[$k] == 1)
    @if($k == 'enable_subscription')
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <br>
          <label>
            {!! Form::checkbox('enabled_modules[]', 'subscription', in_array('subscription', $enabled_modules) ,
            ['class' => 'input-icheck']); !!} {{$v['name']}}
          </label>
          @if(!empty($help_explanations['subscription'])) @show_tooltip($help_explanations['subscription']) @endif
        </div>
      </div>
    </div>
    @elseif($k == 'banking_module')
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <br>
          <label>
            {!! Form::checkbox('enabled_modules[]', 'banking_module', in_array('banking_module', $enabled_modules) ,
            ['class' => 'input-icheck']); !!} {{$v['name']}}
          </label>
          @if(!empty($help_explanations['banking_module'])) @show_tooltip($help_explanations['banking_module']) @endif
        </div>
      </div>
    </div>
    @else 
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <br>
          <label>
            {!! Form::checkbox('enabled_modules[]', $k, in_array($k, $enabled_modules) ,
            ['class' => 'input-icheck']); !!} {{$v['name']}}
          </label>
          @if(!empty($help_explanations[$k])) @show_tooltip($help_explanations[$k]) @endif
        </div>
      </div>
    </div>
    @endif
    @endif
    @endforeach
    @endif
  </div>
</div>