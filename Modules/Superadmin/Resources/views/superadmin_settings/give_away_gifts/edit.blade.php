
<!-- Modal -->

<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">@lang('superadmin::lang.edit')</h4>
        </div>
        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\GiveAwayGiftsController@update', $gift->id), 'method' => 'put', 'id' => 'give_away_gift_edit_form']) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name">@lang('superadmin::lang.name'):</label>
                        <input type="text" class="form-control" name="name" value="{{$gift->name}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {!! Form::close() !!}
    </div>

</div>
