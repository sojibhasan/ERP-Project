<div class="modal-dialog" role="document">
    <div class="modal-content">

        <div class="modal-body">


            <div class="row">
                <div class="col-sm-12">
        
                    <div class="row">
                        <div class="col-sm-12" data-offset="0">
        
                            <div class="wrap-fpanel">
                                <div class="box" data-collapsed="0">
                                    <div class="box-header bg-primary-dark">
                                        <h3 class="box-title">@lang('hr::lang.reimbursement') </h3>
                                    </div>
                                    <div class="panel-body">
        
        
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width:250px">@lang('hr::lang.date') : </th>
                                                <td><?php echo date('Y-m-d', strtotime($reimbursement->date)) ?></td>
                                            </tr>
                                            <tr>
                                                <th>@lang('hr::lang.amount') : </th>
                                                <td><?php echo $reimbursement->amount; ?></td>
                                            </tr>
                                            <tr>
                                                <th>@lang('hr::lang.description') : </th>
                                                <td><?php echo $reimbursement->desc ?></td>
                                            </tr>
                                            <tr>
                                                <th>@lang('hr::lang.approved_by_manager') : </th>
                                                <td><?php echo $reimbursement->approved_manager ?></td>
                                            </tr>
                                            <tr>
                                                <th>@lang('hr::lang.manager_comment') : </th>
                                                <td><?php echo $reimbursement->manager_comment ?></td>
                                            </tr>
                                            <tr>
                                                <th>@lang('hr::lang.approved_by_admin') : </th>
                                                <td><?php echo $reimbursement->approved_admin ?></td>
                                            </tr>
                                            <tr>
                                                <th>@lang('hr::lang.admin_comment') : </th>
                                                <td><?php echo $reimbursement->admin_comment ?></td>
                                            </tr>
        
                                        </table>
        
        
        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
            </div>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(".select2").select2();

    $('.select2').css('width','100%');


    $('body').on('hidden.bs.modal', '.modal', function () {
        $(this).removeData('bs.modal');
    });


</script>