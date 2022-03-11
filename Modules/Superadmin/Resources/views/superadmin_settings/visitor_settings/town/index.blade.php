<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'visitor::lang.town')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_button_towns"
                data-href="{{action('DefaultTownController@create')}}"
                data-container=".default_towns_model">
                <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                   <table class="table table-striped table-bordered" id="other_town_table"
                                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'lang_v1.name' )</th>
                                <th>@lang( 'lang_v1.district' )</th>
                                
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