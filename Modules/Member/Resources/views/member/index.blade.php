@extends('layouts.app')
@section('title', __('member::lang.members'))

@section('content')
<!-- Main content -->

<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('username', __('business.member_code') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('username', $usernames, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('town', __('business.town') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('town', $towns, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('district', __('business.district') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('district', $districts, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('gramasevaka_area', __('business.gramasevaka_area') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('gramasevaka_area', $gramasevaka_areas, null, ['class'
                        => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        ]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('bala_mandalaya_area', __('business.bala_mandalaya_area') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('bala_mandalaya_area', $bala_mandalaya_areas, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        ]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('member_group', __('business.member_group') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('member_group', $member_groups, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
            </div>


            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'member::lang.all_member')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_member_btn"
                    data-href="{{action('\Modules\Member\Http\Controllers\MemberController@create')}}"
                    data-container=".member_model">
                    <i class="fa fa-plus"></i> @lang( 'member::lang.add' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="member_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'messages.action' )</th>
                                    <th>@lang( 'member::lang.name' )</th>
                                    <th>@lang( 'member::lang.member_code' )</th>
                                    <th>@lang( 'member::lang.address' )</th>
                                    <th>@lang( 'member::lang.town' )</th>
                                    <th>@lang( 'member::lang.district' )</th>
                                    <th>@lang( 'member::lang.mobile_number_1' )</th>
                                    <th>@lang( 'member::lang.mobile_number_2' )</th>
                                    <th>@lang( 'member::lang.mobile_number_3' )</th>
                                    <th>@lang( 'member::lang.land_number' )</th>
                                    <th>@lang( 'member::lang.male_female' )</th>
                                    <th>@lang( 'member::lang.date_of_birth' )</th>
                                    <th>@lang( 'member::lang.gramaseva_area' )</th>
                                    <th>@lang( 'member::lang.balamandalaya_area' )</th>
                                    <th>@lang( 'member::lang.member_group' )</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade member_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->


@endsection
@section('javascript')
<script>
    $('.select2').select2();
    // member_table
        member_table = $('#member_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url : "{{action('\Modules\Member\Http\Controllers\MemberController@index')}}",
                data: function(d){
                    d.username = $('#username').val();
                    d.town = $('#town').val();
                    d.district = $('#district').val();
                    d.gramasevaka_area = $('#gramasevaka_area').val();
                    d.bala_mandalaya_area = $('#bala_mandalaya_area').val();
                    d.member_group = $('#member_group').val();
                }
            },
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'action', name: 'action'},
                {data: 'name', name: 'name'},
                {data: 'username', name: 'username'},
                {data: 'address', name: 'address'},
                {data: 'town', name: 'town'},
                {data: 'district', name: 'district'},
                {data: 'mobile_number_1', name: 'mobile_number_1'},
                {data: 'mobile_number_2', name: 'mobile_number_2'},
                {data: 'mobile_number_3', name: 'mobile_number_3'},
                {data: 'land_number', name: 'land_number'},
                {data: 'gender', name: 'gender'},
                {data: 'date_of_birth', name: 'date_of_birth'},
                {data: 'gramasevaka_area', name: 'gramaseva_vasamas.gramaseva_vasama'},
                {data: 'bala_mandalaya_area', name: 'balamandalayas.balamandalaya'},
                {data: 'member_group', name: 'member_group'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $('#username, #town, #district, #gramasevaka_area, #bala_mandalaya_area, #member_group').change(function(){
            member_table.ajax.reload();
        })

        $(document).on('click', 'button.note_group_delete', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            member_table.ajax.reload();
                        },
                    });
                }
            });
        });
</script>

@endsection