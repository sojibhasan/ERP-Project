
@php
    $bus_id = request()->session()->get('business.id');
    $package = \Modules\Superadmin\Entities\Subscription::active_subscription($bus_id);
@endphp
<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'patient.all_your_test' )])
            @slot('tool')
                <div class="box-tools">
                    @if (!empty($package))
                    @if (session()->get('business.is_patient'))
                    <a  class="btn btn-block btn-primary" id='prescription_add_btn'
                        href="{{action('TestController@create',['patient_code' => Auth::user()->username])}}" >
                      
                        <i class="fa fa-upload"></i> @lang( 'patient.upload' )</a>
                    @endif
                    @endif
                  
                </div>
            @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="test_table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>@lang( 'patient.date' )</th>
                            <th>@lang( 'patient.laboratory_name' )</th>
                            <th>@lang( 'patient.test_name' )</th>
                            @if (request()->session()->get('business.is_patient'))
                            <th>@lang( 'patient.amount' )</th>
                            @endif
                            <th>@lang( 'patient.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
    @endcomponent

 

</section>
<!-- /.content -->

