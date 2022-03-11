<!-- Main content -->

<section class="content">

    <div class="row">

        <div class="col-md-12">

            @component('components.widget', ['class' => 'box-primary', 'title' => __(

            'visitor::lang.district')])

            @slot('tool')

            <div class="box-tools">

                <button type="button" id="add_button_district" class="btn btn-primary btn-modal pull-right" 

                data-container=".default_districts_model"

                data-href="{{action('DefaultDistrictController@create')}}">

                <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>

            </div>

            @endslot



            <div class="row">

                <div class="col-md-12">

                    <table class="table table-bordered table-striped" id="other_district_table" style="width: 100%;">

                        <thead>

                            <tr>

                                <th>@lang( 'lang_v1.name' )</th>

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