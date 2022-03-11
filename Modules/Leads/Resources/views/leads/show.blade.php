<div class="modal-dialog" role="document" style="width: 65%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'leads::lang.view_leads' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('date', __( 'leads::lang.date' )) !!}:
                    {{\Carbon::parse($leads->date)->format('m/d/Y')}}

                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('sector', __( 'leads::lang.sector' )) !!}: {{ucfirst($leads->sector)}}

                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('category_id', __( 'leads::lang.category' )) !!}: {{$leads->category}}

                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('main_organization', __( 'leads::lang.main_organization' )) !!}:
                    {{$leads->main_organization}}

                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('business', __( 'leads::lang.business' )) !!}: {{$leads->business}}

                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('address', __( 'leads::lang.address' )) !!}: {{$leads->address}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('town', __( 'leads::lang.town' )) !!}: {{$leads->town}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('district', __( 'leads::lang.district' )) !!}: {{ $leads->district}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('mobile_no_1', __( 'leads::lang.mobile_no_1' )) !!}: {{$leads->mobile_no_1}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('mobile_no_2', __( 'leads::lang.mobile_no_2' )) !!}: {{$leads->mobile_no_2}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('mobile_no_3', __( 'leads::lang.mobile_no_3' )) !!}: {{$leads->mobile_no_3}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('land_number', __( 'leads::lang.land_number' )) !!}: {{$leads->land_number}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('user', __( 'leads::lang.user' )) !!}: {{$leads->user}}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>

</script>