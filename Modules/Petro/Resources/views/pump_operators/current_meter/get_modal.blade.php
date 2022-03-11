<div class="modal-dialog" role="document" style="width: 65%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.closing_meter' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    @foreach ($pumps as $pump)
                    @php
                    $day_entry = \Modules\Petro\Entities\PumperDayEntry::where('date', date('Y-m-d'))->where('pump_id',
                    $pump->id)->first();
                    @endphp
                    @if(!empty($day_entry))
                    <div class="col-md-3 text-center" style="padding: 0px;  margin-right: 10px;">
                        <button type="button" class="btn  btn-primary btn-flat"
                            style="height: 160px; width:100%; background: #800080; border: 0px; text-align: center !important;">
                            <span class="label label-danger" style="font-size: 17px;">@lang('petro::lang.closed')</span>
                            <h2 style="padding-top: 0px; margin-top: 10px">{{$pump->pump_no}} </h2>
                            <h4 style="padding-top: 0px; padding-bottom: 10px; margin-top; 0px">{{$pump->pumper_name}}
                            </h4>
                        </button>
                    </div>
                    @else
                    <div class="col-md-3 text-center" style="padding: 0px; margin-right: 10px;">
                        <a class="btn  btn-primary btn-flat"
                            style="height: 160px; width:100%; background: #F9A825; border: 0px;"
                            href="{{action('\Modules\Petro\Http\Controllers\CurrentMeterController@create', ['pump_id' => $pump->pump_id])}}">
                            <h2 style="padding-top: 0px; margin-top: 48px;">{{$pump->pump_no}}</h2>
                            <h4 style="padding-top: 0px; padding-bottom: 10px; margin-top; 0px">{{$pump->pumper_name}}
                            </h4>
                        </a>
                    </div>
                    @endif

                    @endforeach
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>



    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>

</script>