@extends('layouts.app')
@section('title', __('lang_v1.view_crm_contact'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ __('lang_v1.view_crm_contact') }}</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="hide print_table_part">
        <style type="text/css">
            .info_col {
                width: 25%;
                float: left;
                padding-left: 10px;
                padding-right: 10px;
            }
        </style>

    </div>
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="box-title">
                        <i class="fa fa-user margin-r-5"></i>
                        @lang( 'contact.contact_info', ['contact' => __('contact.contact') ])
                    </h3>
                </div>
                <div class="col-md-6">
                    <button type="button" class="pull-right btn-modal btn btn-xs btn-primary" data-toggle="modal"
                        data-target="#commentModal">{{__('lang_v1.add_comments')}}</button>
                    {{-- <button class="pull-right btn-modal btn btn-xs btn-primary" >{{__('lang_v1.add_comments')}}</button>
                    --}}

                </div>
            </div>
        </div>
        <div class="box-body">
            <span id="view_contact_page"></span>
            <div class="row">
                <div class="col-sm-3">
                    <div class="well well-sm">
                        @include('crm.partials.crm_basic_info')
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="well well-sm">
                        @include('crm.partials.crm_more_info')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- list purchases -->



    @component('components.widget', ['class' => 'box-primary', 'title' => session('business.rp_name')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="comments_table">
            <thead>
                <tr>
                    <th>@lang('messages.date')</th>
                    <th>@lang('lang_v1.comments')</th>
                    <th>@lang('lang_v1.next_follow_up')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent



    <!-- Comments  Modal -->
    <div id="commentModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{__('lang_v1.add_comments')}}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="comment_date">{{__('lang_v1.date')}}:</label>
                                <input class="form-control" type="text" name="comment_date" id="comment_date"
                                    value="{{date('Y-m-d')}}" readonly>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="comments">{{__('lang_v1.comments')}}:</label>
                                <textarea name="comments" id="comments" cols="15" rows="5"
                                    class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="next_follow_up">{{__('lang_v1.next_follow_up')}}:</label>
                                <input class="form-control" type="text" name="next_follow_up" id="next_follow_up"
                                    value="">
                            </div>
                        </div>
                        <input type="hidden" name="crm_id" id="crm_id" value="{{$crm->id}}">
                        <input type="hidden" name="user_id" id="user_id" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-primary" id="comment_submit"
                        data-dismiss="modal">Submit</button>
                    <button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>


</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        $('#next_follow_up').datepicker().datepicker("setDate", new Date());


        comments_table = $('#comments_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: "{{action('CRMController@show', $crm->id)}}", 
            columns: [
                { data: 'comment_date', name: 'comment_date'  },
                { data: 'comments', name: 'comments'  },
                { data: 'next_follow_up', name: 'next_follow_up'  },
            ],
            fnDrawCallback: function(oSettings) {
                
            },
        });


        $('#comment_submit').click(function(e){
            e.preventDefault();
            comment_date = $('#comment_date').val();
            comments = $('#comments').val();
            next_follow_up = $('#next_follow_up').val();
            crm_id = $('#crm_id').val();
            user_id = $('#user_id').val();

            if(comments == ''){
                toastr.error("{{__('lang_v1.comment_field_empty')}}");
            }else{
                $.ajax({
                    method: "GET",
                    url: '/crm/add_comments',
                    dataType: "json",
                    data: {
                        'comment_date' : comment_date,
                        'comments' : comments,
                        'next_follow_up' : next_follow_up,
                        'crm_id' : crm_id,
                        'user_id' : user_id
                    },
                    success: function(result){
                       if(result.success == false){
                        toastr.error(result.msg);
                       }else{
                        toastr.success(result.msg);
                        comments_table.ajax.reload();
                       }
                    }
                });
            }


        });



});




</script>

@endsection