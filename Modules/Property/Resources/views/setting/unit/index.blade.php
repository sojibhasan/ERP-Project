<div class="pos-tab-content @if(empty(session('status.tab'))) active @endif">

  <!-- Main content -->
  <section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'unit.all_your_units' )])
    @can('property.settings.unit')
    @slot('tool')
    <input type="hidden" name="is_property" id="is_property" value="1">
    <div class="box-tools">
      <button type="button" class="btn btn-block btn-primary btn-modal" 
      data-href="{{action('UnitController@create')}}?is_property=true" 
      data-container=".unit_modal">
      <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
    </div>
    @endslot
    @endcan
    @can('property.settings.unit')
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="unit_table">
        <thead>
          <tr>
            <th>@lang( 'unit.name' )</th>
            <th>@lang( 'unit.short_name' )</th>
            <th>@lang( 'unit.allow_decimal' )  @if(!empty($help_explanations['allow_decimal'])) @show_tooltip($help_explanations['allow_decimal']) @endif</th>
            <th>@lang( 'unit.multiple_units' )</th>
            <th>@lang( 'unit.connected_units' )</th>
            <th class="notexport">@lang( 'messages.action' )</th>
          </tr>
        </thead>
      </table>
    </div>
    @endcan
    @endcomponent

</section>
</div>