<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-12">
            <strong>@lang('lang_v1.payment_options'): @show_tooltip(__('lang_v1.payment_option_help'))</strong>
            <div class="form-group">
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">@lang('lang_v1.payment_method')</th>
                            <th class="text-center">@lang('lang_v1.enable')</th>
                            <th class="text-center @if(empty($default_accounts)) hide @endif">
                                @lang('lang_v1.default_accounts') @show_tooltip(__('lang_v1.default_account_help'))</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $default_payment_accounts = !empty($settings['default_payment_accounts']) ?
                        json_decode($settings['default_payment_accounts'], true) : [];
                        @endphp
                        @foreach($payment_types as $key => $value)
                        <tr>
                            <td class="text-center">{{$value}}</td>
                            <td class="text-center">{!! Form::checkbox('default_payment_accounts[' . $key .
                                '][is_enabled]', 1, !empty($default_payment_accounts[$key]['is_enabled'])); !!}</td>
                            <td class="text-center @if(empty($default_accounts)) hide @endif">
                                {!! Form::select('default_payment_accounts[' . $key . '][account]', $default_accounts,
                                !empty($default_payment_accounts[$key]['account']) ?
                                $default_payment_accounts[$key]['account'] : null, ['class' => 'form-control
                                input-sm']); !!}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>