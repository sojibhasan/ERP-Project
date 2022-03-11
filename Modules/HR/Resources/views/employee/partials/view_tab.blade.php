<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('hr::lang.employee_number'): </label> {{$employee->employee_number}}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('hr::lang.first_name'): </label> :{{ $employee->first_name}}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('hr::lang.last_name'): </label> {{$employee->last_name}}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group form-group-bottom">
            <label>@lang('hr::lang.date_of_birth'): </label> {{$employee->date_of_birth}}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('hr::lang.marital_status'): </label> {{$employee->marital_status}}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('hr::lang.country'): </label> {{$employee->country}}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('hr::lang.blood_group'): </label> {{$employee->blood_group}}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('hr::lang.id_number'): </label> {{$employee->id_number}}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('hr::lang.religious'): </label> {{$employee->religion_name}}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('hr::lang.gender'): </label> {{$employee->gender}}
        </div>
    </div>
</div>