<div class="pos-tab-content @if(session('status.tab') == 'taxes') active @endif">
  <!-- Main content -->
  <section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'property::lang.all_your_property_taxes' )])
    @can('property.settings.tax')
    @slot('tool')
    <div class="box-tools">
      <button type="button" class="btn btn-block btn-primary btn-modal" 
      data-href="{{action('\Modules\Property\Http\Controllers\PropertyTaxesController@create')}}" 
      data-container=".unit_modal">
      <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
    </div>
    @endslot
    @endcan
    @can('property.settings.tax')
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="property_taxes_table" style="width: 100%">
        <thead>
          <tr>
            <th>@lang( 'property::lang.business_location' )</th>
            <th>@lang( 'property::lang.property_tax_name' )</th>
            <th>@lang( 'property::lang.tax_type' )</th>
            <th>@lang( 'property::lang.amount/percentage' )</th>
            <th class="notexport">@lang( 'messages.action' )</th>
          </tr>
        </thead>
      </table>
    </div>
    @endcan
    @endcomponent

</section>
</div>