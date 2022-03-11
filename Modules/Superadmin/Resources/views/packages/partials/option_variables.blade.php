<div class="modal-dialog" role="document" style="width: 65%">
    <div class="modal-content print">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title">Select variables</h3>

        </div>
        @php
        if(empty($selected_variables)){
        $selected_variables = [];
        }
        $all_variable_options = ['Number of Branches', 'Number of Users', 'Number of Products', 'Number of Periods', 'Number of Customers', 'Monthly Total Sales', 'No of Family Members', 'No of Vehicles'];
        $all_increase_decrease = ['Increase', 'Decrease'];
        $all_variable_type = ['Fixed', 'Percentage'];
        @endphp
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                    <th>@lang( 'superadmin::lang.action' )</th>
                    <th>@lang( 'superadmin::lang.variable_code' )</th>
                    <th>@lang( 'superadmin::lang.variable_options' )</th>
                    <th>@lang( 'superadmin::lang.option_value' )</th>
                    <th>@lang( 'superadmin::lang.increase_decrease' )</th>
                    <th>@lang( 'superadmin::lang.variable_type' )</th>
                    <th>@lang( 'superadmin::lang.price_value' )</th>
                </thead>
                <tbody>
                    @foreach ($option_variables as $option_variable)
                    <tr>
                        <td>
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('option_variables[]', $option_variable->id,
                                    in_array($option_variable->id, $selected_variables), ['class' => 'input-icheck']);
                                    !!}
                                    {{$option_variable->variable_code}}
                                </label>
                            </div>
                        </td>
                    <td>{{$option_variable->variable_code}}</td>
                    <td>{{$all_variable_options[$option_variable->variable_options]}}</td>
                    <td>{{$option_variable->option_value}}</td>
                    <td>{{$all_increase_decrease[$option_variable->increase_decrease]}}</td>
                    <td>{{ $all_variable_type[$option_variable->variable_type]}}</td>
                    <td>{{$option_variable->price_value}}</td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>