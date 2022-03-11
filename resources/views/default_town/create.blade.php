<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('DefaultTownController@store'), 'method' => 'post', 'id' => 'town_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'visitors.add_town' )</h4>
      </div>
  
      <div class="modal-body">
              <div class="form-group">
                  {!! Form::label('name', __( 'lang_v1.name' ) .":*", ['style' => 'color: black !important']) !!}
                  {!! Form::text('name', null, ['class' => 'form-control', 'required','placeholder' => __( 'lang_v1.name' ) ]); !!}
              </div>
               <div class="form-group">
                  {!! Form::label('district_id', __( 'visitors.district' ) .":", ['style' => 'color: black !important']) !!}
                  <select name="district_id" class="form-control select2" id="district_id">\
                      <option>@lang('messages.please_select')</option>
                      @foreach($districts as $district)
                          
                              <option value="{{$district->id}}">{{$district->name}}</option>
                      @endforeach
                  </select>
              </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-submit">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
