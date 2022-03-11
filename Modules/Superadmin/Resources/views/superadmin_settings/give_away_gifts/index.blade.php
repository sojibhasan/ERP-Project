<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-4">
                    <div class="form-group">
                        {!! Form::label('name', __('superadmin::lang.gift_name') . ':') !!}
                        {!! Form::text('name', null, ['class' =>
                        'form-control', 'placeholder' => __('superadmin::lang.gift_name'), 'id' => 'gift_name']); !!}
                    </div>
                </div>
                <div class="col-xs-2">
                    <button class="btn btn-primary" type="button" style="margin-top: 22px;"
                        id="give_away_gift_add">@lang('messages.add')</button>
                </div>
                {!! Form::close() !!}
            </div>

            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'superadmin::lang.all_give_away_gifts' )])

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="give_away_gift_table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>@lang( 'superadmin::lang.gift_name' )</th>
                            <th>@lang('superadmin::lang.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
    @php
        $show_give_away_gift_in_register_page = json_decode($settings["show_give_away_gift_in_register_page"], true);
    @endphp
    <div class="row">
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('name', __( 'superadmin::lang.show_give_away_gift_in_register_page' ) . ':') !!}

                {!! Form::select('show_give_away_gift_in_register_page[]',['customer' => __('superadmin::lang.customer'), 'my_health' =>
                __('superadmin::lang.my_health'),
                'visitor' => __('superadmin::lang.visitor'), 'company' => __('superadmin::lang.company'), 'member'
                => __('superadmin::lang.member')],
                !empty($show_give_away_gift_in_register_page) ?
                $show_give_away_gift_in_register_page : null ,['class' =>
                'form-control select2', 'multiple', 'style' => 'width: 100%;' ]); !!}

            </div>
        </div>
    </div>
</div>