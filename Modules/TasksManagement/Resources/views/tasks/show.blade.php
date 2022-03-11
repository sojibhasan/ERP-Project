<style>
    .tox-menubar{
        display: none !important;
    }
    .tox-toolbar-overlord{
        display: none !important;
    }
    .tox-statusbar{
        display: none !important;
    }
</style>
<div class="modal-dialog" role="document" style="width: 60%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h3 class="modal-title">{{$note->note_heading}}</h3>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-3">
                    <b>@lang('tasksmanagement::lang.date_and_time'):</b> {{$note->date_and_time}}
                </div>
                <div class="col-md-3">
                    <b>@lang('tasksmanagement::lang.note_group'):</b> {{$note->note_group}}
                </div>
                <div class="col-md-3">
                    <b>@lang('tasksmanagement::lang.created_by'):</b> {{$note->created_by}}
                </div>
                <div class="col-md-2">
                    <b style="float: left;">@lang('tasksmanagement::lang.color'):</b>
                    <div style="float: right;height: 25px; width: 25px; background:{{$note->color}}"></div>
                </div>
            </div>
            <br>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <b>@lang('tasksmanagement::lang.note_details'):</b> <br>
                    <textarea name="note_details" id="note_details">{{$note->note_details}}</textarea>
                </div>
            </div>
            <br>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <b>@lang('tasksmanagement::lang.note_footer'):</b> <br>
                    {{strip_tags($note->note_footer)}}
                </div>
            </div>

            <br>
            <br>
            <div class="row">
                <div class="col-md-4">
                    <b>@lang('tasksmanagement::lang.show_on_the_top'):</b>
                    {{ucfirst($note->show_on_top_section)}}
                </div>
                <div class="col-md-4">
                    <b>@lang('tasksmanagement::lang.shared_with'):</b>
                    {{($share_with)}}
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
     tinymce.init({
          selector: 'textarea#note_details'
      });

</script>