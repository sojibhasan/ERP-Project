@extends('layouts.app')
@section('title', __( 'unit.units' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'unit.units' )
    <small>@lang( 'unit.manage_your_units' )</small>
  </h1>
  
  </section>

  <!-- Main content -->
  <section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'unit.all_your_units' )])
    <input type="hidden" name="is_property" id="is_property" value="0">
    @can('unit.create')
    @slot('tool')
    <div class="box-tools">
      <button type="button" class="btn btn-block btn-primary btn-modal" 
      data-href="{{action('UnitController@create')}}" 
      data-container=".unit_modal">
      <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
    </div>
    @endslot
    @endcan
    @can('unit.view')
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

    <div class="modal fade unit_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
  </div>

</section>
@stop
@section('javascript')
<!-- /.content -->
<script type="text/javascript">
  
</script>
@endsection
