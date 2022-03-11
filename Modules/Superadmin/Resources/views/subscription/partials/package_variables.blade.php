<style>
    .form-group select {
        border-bottom: 1px solid grey !important;
        border-top: 0px solid grey !important;
        border-left: 0px solid grey !important;
        border-right: 0px solid grey !important;
    }

    #option_vaialbe_modal .form-group label {
        color: black !important;
    }

    .modal-dialog {
        margin-top: 12%;
    }
</style>
<div class="modal-dialog" role="document" style="width: 70%" id="option_vaialbe_modal">
    <div class="modal-content print">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title">{{$package->name}}: {{!empty($currency_symbol)?$currency_symbol->symbol: ''}} <span
                    id="pacakge_price">{{number_format($package->price , 2)}}</span></h3>
            <input type="hidden" name="pprice" id="pprice" value="{{$package->price}}">
        </div>
        <div class="col-md-12">
            @if ($is_business_pacakge)
            <div class="col-xs-12">
                @foreach ($module_enable_price as $key => $item)
                @if(!empty($item))
                <div class="col-sm-3 ">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('module_enable_price', 1, false, ['class' => 'input-icheck module_price', 'data-module_name' => $key,
                            'data-price' => $item]); !!}
                            {{__('superadmin::lang.'.$key)}}
                        </label>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif

            @if(!request()->session()->get('business.is_patient'))
            @if (!empty($number_of_branches))
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('number_of_branches', __('superadmin::lang.number_of_branches') . ':') !!}
                    <select name="number_of_branches" id="number_of_branches" class="form-control selected-variable"
                        placeholder="Please select">
                        <option value="">Please select</option>
                        @foreach ($number_of_branches as $branch)
                        <option data-optvalue="{{$branch->option_value}}" data-incdec="{{$branch->increase_decrease}}" data-id="{{$branch->id}}"
                            data-type="{{$branch->variable_type}}" value="{{$branch->price_value}}">
                            {{$branch->option_value}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
            @endif

            @if (!empty($number_of_users))
            <div class="col-xs-6">
                <div class="form-group">
                    @if(!request()->session()->get('business.is_patient'))
                    {!! Form::label('number_of_users', __('superadmin::lang.number_of_users') . ':') !!}
                    @else
                    {!! Form::label('number_of_users', __('superadmin::lang.number_of_family_members') . ':') !!}
                    @endif
                    <select name="number_of_users" id="number_of_users" class="form-control selected-variable"
                        placeholder="Please select">
                        <option value="">Please select</option>
                        @foreach ($number_of_users as $user)
                        <option data-optvalue="{{$user->option_value}}" data-incdec="{{$user->increase_decrease}}" data-id="{{$user->id}}"
                            data-type="{{$user->variable_type}}" value="{{$user->price_value}}">
                            {{$user->option_value}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            @if(!request()->session()->get('business.is_patient'))
            @if (!empty($number_of_products))
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('number_of_products', __('superadmin::lang.number_of_products') . ':') !!}
                    <select name="number_of_products" id="number_of_products" class="form-control selected-variable"
                        placeholder="Please select">
                        <option value="">Please select</option>
                        @foreach ($number_of_products as $product)
                        <option data-optvalue="{{$product->option_value}}" data-incdec="{{$product->increase_decrease}}" data-id="{{$product->id}}"
                            data-type="{{$product->variable_type}}" value="{{$product->price_value}}">
                            {{$product->option_value}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
            @endif

            @if (!empty($number_of_periods))
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('number_of_periods', __('superadmin::lang.number_of_periods') . ':') !!}
                    <select name="number_of_periods" id="number_of_periods" class="form-control selected-variable"
                        placeholder="Please select">
                        <option value="">Please select</option>
                        @foreach ($number_of_periods as $period)
                        <option data-optvalue="{{$period->option_value}}" data-incdec="{{$period->increase_decrease}}" data-id="{{$period->id}}"
                            data-type="{{$period->variable_type}}" value="{{$period->price_value}}">
                            {{$period->option_value}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            @if (!empty($number_of_customer))
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('number_of_customer', __('superadmin::lang.number_of_customer') . ':') !!}
                    <select name="number_of_customer" id="number_of_customer" class="form-control selected-variable"
                        placeholder="Please select">
                        <option value="">Please select</option>
                        @foreach ($number_of_customer as $customer)
                        <option data-optvalue="{{$customer->option_value}}"
                            data-incdec="{{$customer->increase_decrease}}" data-type="{{$customer->variable_type}}" data-id="{{$customer->id}}"
                            value="{{$customer->price_value}}">
                            {{$customer->option_value}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
            @if (!empty($monthly_total_sales))
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('monthly_total_sales', __('superadmin::lang.monthly_total_sales') . ':') !!}
                    <select name="monthly_total_sales" id="monthly_total_sales" class="form-control selected-variable"
                        placeholder="Please select">
                        <option value="">Please select</option>
                        @foreach ($monthly_total_sales as $monthly_total_sale)
                        <option data-optvalue="{{$monthly_total_sale->option_value}}"
                            data-incdec="{{$monthly_total_sale->increase_decrease}}" data-type="{{$monthly_total_sale->variable_type}}" data-id="{{$monthly_total_sale->id}}"
                            value="{{$monthly_total_sale->price_value}}">
                            {{@num_format($monthly_total_sale->option_value)}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
            @if (!empty($no_of_vehicles))
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('no_of_vehicles', __('superadmin::lang.no_of_vehicles') . ':') !!}
                    <select name="no_of_vehicles" id="no_of_vehicles" class="form-control selected-variable"
                        placeholder="Please select">
                        <option value="">Please select</option>
                        @foreach ($no_of_vehicles as $no_of_vehicles)
                        <option data-optvalue="{{$no_of_vehicles->option_value}}"
                            data-incdec="{{$no_of_vehicles->increase_decrease}}" data-type="{{$no_of_vehicles->variable_type}}" data-id="{{$no_of_vehicles->id}}"
                            value="{{$no_of_vehicles->price_value}}">
                            {{@num_format($no_of_vehicles->option_value)}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
            @if (!empty($no_of_family_members))
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('no_of_family_members', __('superadmin::lang.no_of_family_members') . ':') !!}
                    <select name="no_of_family_members" id="no_of_family_members" class="form-control selected-variable"
                        placeholder="Please select">
                        <option value="">Please select</option>
                        @foreach ($no_of_family_members as $no_of_family_member)
                        <option data-optvalue="{{$no_of_family_member->option_value}}"
                            data-incdec="{{$no_of_family_member->increase_decrease}}" data-type="{{$no_of_family_member->variable_type}}" data-id="{{$no_of_family_member->id}}"
                            value="{{$no_of_family_member->price_value}}">
                            {{$no_of_family_member->option_value}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\SubscriptionController@pay',
            [$package->id]), 'method' => 'post']) !!}
            {!! Form::hidden('custom_price', null, ['id' => 'custom_price']) !!}
            {!! Form::hidden('option_variables_selected', null, ['id' => 'option_variables_selected']) !!}
            {!! Form::hidden('module_selected', null, ['id' => 'module_selected']) !!}
            @auth
            <button type="submit" class="btn btn-primary">Pay</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            @endauth
            @guest
            @if($package->hospital_system && in_array('patient' , json_decode($package->hospital_business_type)))

            <a href="" data-toggle="modal" data-target="#patient_register_modal"
                class="btn btn-sm btn-success regsiter_after_variable" id="{{$package->id}}">
                @if($package->price != 0)
                @lang('superadmin::lang.register_subscribe')
                @else
                @lang('superadmin::lang.register_free')
                @endif
            </a>
            @else
            <a href="" data-toggle="modal" data-target="@if($package->visitors_registration_module)#visitor_register_modal @else#register_modal  @endif"
                class="btn btn-sm btn-success regsiter_after_variable" id="{{$package->id}}">
                @if($package->price != 0)
                @lang('superadmin::lang.register_subscribe')
                @else
                @lang('superadmin::lang.register_free')
                @endif
            </a>
            @endif
            @endguest

            {!! Form::close() !!}
        </div>

    </div>
</div>

<script>
 
$('.regsiter_after_variable').hide();
$('#number_of_branches, #number_of_users, #number_of_products, #number_of_periods, #number_of_customer, #monthly_total_sales, #no_of_vehicles, #no_of_family_members, .module_price').change(function() {
    var pacakge_price = parseFloat($('#pprice').val());
    var pprice = parseFloat($('#pprice').val());
    let  select_variables = [];
    let  module_selected = [];
    
    var new_package_price = 0;
    @if(!request()->session()->get('business.is_patient'))
    if ($('#number_of_branches').val()) {
        new_package_price = calculate_price('number_of_branches', pacakge_price, pprice, new_package_price);
        pacakge_price = new_package_price;
    }
    @endif
    if ($('#number_of_users').val()) {
        new_package_price = calculate_price('number_of_users', pacakge_price, pprice, new_package_price);
        pacakge_price = new_package_price;
    }
    @if(!request()->session()->get('business.is_patient'))
    if ($('#number_of_products').val()) {
        new_package_price = calculate_price('number_of_products', pacakge_price, pprice, new_package_price);
        pacakge_price = new_package_price;
    }
    @endif
    if ($('#number_of_periods').val()) {
        new_package_price = calculate_price('number_of_periods', pacakge_price, pprice, new_package_price);
        pacakge_price = new_package_price;
    }
    if ($('#number_of_customer').val()) {
        new_package_price = calculate_price('number_of_customer', pacakge_price, pprice, new_package_price);
        pacakge_price = new_package_price;
    }
    if ($('#monthly_total_sales').val()) {
        new_package_price = calculate_price('monthly_total_sales', pacakge_price, pprice, new_package_price);
        pacakge_price = new_package_price;
    }
    if ($('#no_of_vehicles').val()) {
        new_package_price = calculate_price('no_of_vehicles', pacakge_price, pprice, new_package_price);
        pacakge_price = new_package_price;
    }
    if ($('#no_of_family_members').val()) {
        new_package_price = calculate_price('no_of_family_members', pacakge_price, pprice, new_package_price);
        pacakge_price = new_package_price;
    }
    @if(request()->session()->get('business.is_patient'))
    if ($('#number_of_periods').val() == ''  && $('#number_of_users').val() == '') {
        new_package_price = pprice;
        $('.regsiter_after_variable').hide();
    }else{
        $('.regsiter_after_variable').show();
    }
    @else
        var check_element_empty = false;
        $('.selected-variable').each(function(i, obj) {
            if($(this).val() != ''){
                check_element_empty = true;
                return false;
            }
        });
        
        if (!check_element_empty) {
            new_package_price = pprice;
            $('.regsiter_after_variable').hide();
        }else{
            $('.regsiter_after_variable').show();
        }
    @endif

    @if ($is_business_pacakge)
    $('.module_price').each(function(){
        if($(this).prop('checked') == true){
            new_package_price = parseFloat(new_package_price) + parseFloat($(this).data('price'));
            module_selected.push($(this).data('module_name'));
        }
    });
    @endif
    $('.selected-variable').each(function(i, obj) {
        if($(this).val() != ''){
            select_variables.push($(this).find(':selected').data('id'));  //seleted variables by user 
        }
    });

    
    $('#module_selected').val(JSON.stringify(module_selected));
    $('#option_variables_selected').val(JSON.stringify(select_variables));
    $('#custom_price').val(new_package_price);

    //register modal fields
    $('.rm_module_selected').val(JSON.stringify(module_selected));
    $('.rm_option_variables_selected').val(JSON.stringify(select_variables));
    $('.rm_custom_price').val(new_package_price);
     //register modal fields
     

    $('#pacakge_price').text(__number_f(new_package_price, false, false, 2));

});

function calculate_price(ele, pacakge_price, pprice, new_package_price) {
    var optval = $('#' + ele).find(':selected').data('optvalue');
    var incdec = $('#' + ele).find(':selected').data('incdec');
    var type = $('#' + ele).find(':selected').data('type');
    var value = ($('#' + ele).val());
    if(value){
        value = parseFloat(value);
        if (incdec === 0) {
            if (type === 0) {
                new_package_price = pacakge_price + value;
            }
            if (type === 1) {
                new_package_price = pacakge_price + (pprice * value / 100);
            }
        }
        if (incdec === 1) {
            if (type === 0) {
                new_package_price = pacakge_price - value;
            }
            if (type === 1) {
                new_package_price = pacakge_price - (pprice * value / 100);
            }
        }
    }
    
    return new_package_price;
}


</script>