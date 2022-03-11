<div class="pos-tab-content @if(session('status.tab') == 'taxes') active @endif">
  <!-- Main content -->
  <section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'property::lang.all_your_installment_cycle' )])
    @can('property.settings.tax')
    @slot('tool')
    <div class="box-tools">
      <button type="button" class="btn btn-block btn-primary btn-modal" 
      data-href="{{action('\Modules\Property\Http\Controllers\InstallmentCycleController@create')}}" 
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
    </div>
    @endslot
    @endcan
    @can('property.settings.tax')
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="installment_cycle_table" style="width: 100%">
        <thead>
          <tr>
            <th>@lang( 'property::lang.date' )</th>
            <th>@lang( 'property::lang.name' )</th>
            <th>@lang( 'property::lang.added_user' )</th>
            <th>@lang( 'property::lang.action' )</th>
          </tr>
        </thead>
      </table>
    </div>
    @endcan
    @endcomponent

</section>
</div>