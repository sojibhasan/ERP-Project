<style>
    .note_div{
        height: 500px;
        width: 100%;
        overflow-y: scroll;
        overflow-x: hidden;
    }
</style>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'account.notes' )</h4>
        </div>

        <div class="modal-body">
            <div class="note_div">
                @foreach ($account_notes as $note)
                <div class="row">
                    <div class="col-md-4">
                        <p><span style="font-weight: bold;">Date:</span> {{$note->operation_date}} </p>
                    </div>
                    <div class="col-md-8">
                        <p><span style="font-weight: bold;">Note:</span> {{$note->note}}</p>
                    </div>
                </div>
                @endforeach
                @if ($account_notes->count() == 0)
                <p class="text-center">No note found</p>
                @endif
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>



    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
    $(document).ready( function(){
      $('#od_datetimepicker').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format
      });
    });
</script>