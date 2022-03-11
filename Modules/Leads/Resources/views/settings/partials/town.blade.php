<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('district_fitler_town', __( 'leads::lang.district' )) !!}
                    {!! Form::select('district_fitler_town', $districts, null, ['class' => 'form-control select2', 'style' => 'width: 100%;',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'district_fitler_town']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('users_fitler_town', __( 'leads::lang.user' )) !!}
                    {!! Form::select('users_fitler_town', $users, null, ['class' => 'form-control select2', 'style' => 'width: 100%;',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'users_fitler_town']);
                    !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'leads::lang.all_towns')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_town_btn"
                    data-href="{{action('\Modules\Leads\Http\Controllers\TownController@create')}}"
                    data-container=".town_model">
                    <i class="fa fa-plus"></i> @lang( 'leads::lang.add_town' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="towns_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'leads::lang.date' )</th>
                                <th>@lang( 'leads::lang.district' )</th>
                                <th>@lang( 'leads::lang.town' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->