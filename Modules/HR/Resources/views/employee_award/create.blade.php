<div class="modal-dialog" role="document">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">@lang('hr::lang.add_award')</h4>
        </div>
        <div class="modal-body">
            <form id="addSubordinate" action="{{action('\Modules\HR\Http\Controllers\EmployeeAwardController@store')}}"
                method="post">
                @csrf
                <div class="form-group">
                    <label>@lang('hr::lang.department')<span class="required">*</span></label>
                    <select class="form-control select2" name="department_id" id="department"
                        onchange="get_employee(this.value)">
                        <option value="">@lang('hr::lang.please_select')</option>
                        @foreach($department as $item)
                        <option value="{{$item->id}}"
                            @if(!empty($award->department)) {{$award->department == $item->id? 'selected':''}}  @endif>
                            {{$item->department}}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.employee')<span class="required">*</span></label>
                    <select class="form-control select2" name="employee_id" id="employee">
                        <option value="">@lang('hr::lang.please_select')</option>
                        @foreach($employee as $item)
                        <option value="{{$item->id}}"
                            @if(!empty($award->employee_id)) {{$award->employee_id == $item->id? 'selected':''}} @endif>
                            {{$item->first_name.' '.$item->last_name}}
                        </option>
                        @endforeach

                    </select>
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.award_name')<span class="required">*</span></label>
                    <input type="text" class="form-control" name="award_name"
                        value="@if(!empty($award->award_name)) {{$award->award_name}}  @endif">
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.gift_item')</label>
                    <input type="text" class="form-control" name="gift_item"
                        value="@if(!empty($award->gift_item)) {{$award->gift_item}}  @endif">
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.award_amount')</label>
                    <input type="text" class="form-control" name="award_amount"
                        value="@if(!empty($award->award_amount)) {{$award->award_amount}}  @endif">
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.month')</label>
                    <div class="input-group">
                        <input type="text" name="month" class="form-control monthyear" value="@if (!empty($award->award_month)) {{date('Y-n', strtotime($award->award_month))}} @endif">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="id"
                    value="@if(!empty($award->id)) {{str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encrypt->encode($award->id))}} @endif">
                <div class="modal-footer">
                    <button type="button" id="close" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-primary" id="btn">Save</button>
                </div>
            </form>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(".monthyear").datepicker( {
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months"
    });
    $(".select2").select2();
    $('.select2').css('width','100%');
    $('body').on('hidden.bs.modal', '.modal', function () {
        $(this).removeData('bs.modal');
    });
</script>