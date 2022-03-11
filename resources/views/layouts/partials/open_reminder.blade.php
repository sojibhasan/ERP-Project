<div class="modal" tabindex="-1" role="dialog" id="open_reminder_modal_{{$value->id}}" style="margin-top: 10%;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <i class="fa fa-bell-o fa-lg"
                    style="font-size: 50px; margin-top: 20px; border: 1px solid #333; padding:15px 10px 15px 10px; border-radius: 50%;"></i>
                <h3>{{$value->name}}</h3>
            </div>
            @php
            $crm_activity = App\CrmActivity::where('id',
            $value->crm_reminder_id)->with(['crm_activity_details'])->first();
            @endphp
            <div class="clearfix"></div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 20%">@lang('lang_v1.date')</th>
                                <th>@lang('lang_v1.note')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($crm_activity->crm_activity_details as $item)
                            <tr>
                                <td style="width: 20%">{{$item->date}}</td>
                                <td>{{$item->note}}</td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>