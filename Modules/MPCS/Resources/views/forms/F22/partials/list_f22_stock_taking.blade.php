<!-- Main content -->
<section class="content">
   
    <div class="row">
        <div class="col-md-12">
            {{-- @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('f22_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('f22_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('f22_product_id', __('mpcs::lang.product') . ':') !!}
                    {!! Form::select('f22_product_id', $products, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('mpcs::lang.form_no') . ':') !!}
                    {!! Form::text('F22_from_no', $F22_from_no, ['class' => 'form-control', 'readonly']) !!}
                </div>
            </div>


            @endcomponent --}}
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
  
            <div class="col-md-12">
                <div class="row" style="margin-top: 20px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="form_f22_list_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang('mpcs::lang.date_and_time')</th>
                                    <th>@lang('mpcs::lang.location')</th>
                                    <th>@lang('mpcs::lang.form_no')</th>
                                    <th>@lang('mpcs::lang.user')</th>
                                    <th>@lang('mpcs::lang.action')</th>
                                </tr>
                            </thead>
                            
                        </table>
                    </div>
                </div>
              
            </div>

            @endcomponent
        </div>
    </div>
  
</section>
<!-- /.content -->