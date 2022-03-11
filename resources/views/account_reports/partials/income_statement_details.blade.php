<div class="col-md-6  p-5">
</div>
<div class="col-md-2  p-5">
  <span class="first_date"><b>{{ \Carbon::parse($dates['first'])->format('M Y') }}</b></span>
</div>
<div class="col-md-2  p-5">
  <span class="second_date"><b>{{ \Carbon::parse($dates['second'])->format('M Y') }}</b></span>
</div>
<div class="col-md-2  p-5">
  <span class="third_date"><b>{{ \Carbon::parse($dates['third'])->format('M Y') }}</b></span>
</div>
<br>
<br>
@if($account_access)
<div class="">
  @php
  $first_income_total = 0;
  $second_income_total = 0;
  $third_income_total = 0;
  @endphp
  <div class="box-header with-border">
      <div class="col-md-6  p-5">
        <a data-toggle="collapse" data-parent="#accordion" href="#income" class="text-black"><i class="fa fa-plus-square-o text-red"></i>
         <b>@lang('account.income')</b>
        </a>
      </div>
      @foreach ($income_details as $key => $income_detail)
      @php
      $first_income_total += $income_detail['first'];
      $second_income_total += $income_detail['second'];
      $third_income_total += $income_detail['third'];
      @endphp
      @endforeach
      <div class="col-md-2  p-5">
        <span class="text-blue"><b>{{@number_format($first_income_total,2)}}</b></span>
      </div>
      <div class="col-md-2  p-5">
       <span class="text-blue"><b>{{@number_format($second_income_total,2)}}</b></span>
      </div>
      <div class="col-md-2  p-5">
        <span class="text-blue"><b>{{@number_format($third_income_total,2)}}</b></span>
      </div>
  </div>
  <div id="income" class="panel-collapse collapse">
    <div class="box-body">
        @foreach ($income_details as $key => $income_detail)
        <div class="col-md-6 p-5">
          {{$key}}
        </div>
        <div class="col-md-2 p-5">
          {{@number_format($income_detail['first'])}}
        </div>
        <div class="col-md-2 p-5">
          {{@number_format($income_detail['second'])}}
        </div>
        <div class="col-md-2 p-5">
          {{@number_format($income_detail['third'])}}
        </div>
        @endforeach
    </div>
  </div>
</div>

<div class="">
  @php
  $first_cost_total = 0;
  $second_cost_total = 0;
  $third_cost_total = 0;
  @endphp
  <div class="box-header with-border">
      <div class="col-md-6 p-5">
        <a data-toggle="collapse" data-parent="#accordion" href="#cost_of_sales" class="text-black"><i class="fa fa-plus-square-o text-red"></i>
         <b>@lang('account.cost_of_sales')</b>
        </a>
      </div>
      @foreach ($cost_details as $key => $cost_detail)
      @php
      $first_cost_total += $cost_detail['first'];
      $second_cost_total += $cost_detail['second'];
      $third_cost_total += $cost_detail['third'];
      @endphp
      @endforeach
      <div class="col-md-2 p-5">
        <span class="text-blue"><b>{{@number_format($first_cost_total,2)}}</b></span>
      </div>
      <div class="col-md-2 p-5">
       <span class="text-blue"><b>{{@number_format($second_cost_total,2)}}</b></span>
      </div>
      <div class="col-md-2 p-5">
        <span class="text-blue"><b>{{@number_format($third_cost_total,2)}}</b></span>
      </div>
  </div>
  <div id="cost_of_sales" class="panel-collapse collapse">
    <div class="box-body">
        @foreach ($cost_details as $key => $cost_detail)
        <div class="col-md-6 p-5">
          {{$key}}
        </div>
        <div class="col-md-2 p-5">
          {{@number_format($cost_detail['first'])}}
        </div>
        <div class="col-md-2 p-5">
          {{@number_format($cost_detail['second'])}}
        </div>
        <div class="col-md-2 p-5">
          {{@number_format($cost_detail['third'])}}
        </div>
        @endforeach
    </div>
  </div>
</div>

<div class="">
  @php
  $first_gross_profit = $first_income_total - $first_cost_total;
  $second_gross_profit = $second_income_total - $second_cost_total;
  $third_gross_profit = $third_income_total - $third_cost_total;
  @endphp
  <div class="box-header with-border">
      <div class="col-md-6 p-5">
        <a data-toggle="collapse" data-parent="#accordion" href="#gross_profit" class="text-red"><i class="fa fa-plus-square-o text-red"></i>
         <b>@lang('account.gross_profit')</b>
        </a>
      </div>
      <div class="col-md-2 p-5">
        <span class="text-blue"><b>{{@number_format($first_gross_profit,2)}}</b></span>
      </div>
      <div class="col-md-2 p-5">
       <span class="text-blue"><b>{{@number_format($second_gross_profit,2)}}</b></span>
      </div>
      <div class="col-md-2 p-5">
        <span class="text-blue"><b>{{@number_format($third_gross_profit,2)}}</b></span>
      </div>
  </div>
</div>

<div class="">
  @php
  $first_expense_total = 0;
  $second_expense_total = 0;
  $third_expense_total = 0;
  @endphp
  <div class="box-header with-border">
      <div class="col-md-6 p-5">
        <a data-toggle="collapse" data-parent="#accordion" href="#operating_expenses" class="text-black"><i class="fa fa-plus-square-o text-red"></i>
         <b>@lang('account.operating_expenses')</b>
        </a>
      </div>
      @foreach ($expense_details as $key => $expense_detail)
      @php
      $first_expense_total += $expense_detail['first'];
      $second_expense_total += $expense_detail['second'];
      $third_expense_total += $expense_detail['third'];
      @endphp
      @endforeach
      <div class="col-md-2 p-5">
        <span class="text-blue"><b>{{@number_format($first_expense_total,2)}}</b></span>
      </div>
      <div class="col-md-2 p-5">
       <span class="text-blue"><b>{{@number_format($second_expense_total,2)}}</b></span>
      </div>
      <div class="col-md-2 p-5">
        <span class="text-blue"><b>{{@number_format($third_expense_total,2)}}</b></span>
      </div>
  </div>
  <div id="operating_expenses" class="panel-collapse collapse">
    <div class="box-body">
        @foreach ($expense_details as $key => $expense_detail)
        <div class="col-md-6 p-5">
          {{$key}}
        </div>
        <div class="col-md-2 p-5">
          {{@number_format($expense_detail['first'])}}
        </div>
        <div class="col-md-2 p-5">
          {{@number_format($expense_detail['second'])}}
        </div>
        <div class="col-md-2 p-5">
          {{@number_format($expense_detail['third'])}}
        </div>
        @endforeach
    </div>
  </div>
</div>

<div class="">
  @php
  $first_net_profit_profit = $first_gross_profit - $first_expense_total;
  $second_net_profit_profit = $second_gross_profit - $second_expense_total;
  $third_net_profit_profit = $third_gross_profit - $third_expense_total;
  @endphp
  <div class="box-header with-border">
      <div class="col-md-6 p-5">
        <a data-toggle="collapse" data-parent="#net_profit_before_tax" href="#gross_profit" class="text-red"><i class="fa fa-plus-square-o text-red"></i>
         <b>@lang('account.net_profit_before_tax')</b>
        </a>
      </div>
      <div class="col-md-2 p-5">
        <span class="text-blue"><b>{{@number_format($first_net_profit_profit,2)}}</b></span>
      </div>
      <div class="col-md-2 p-5">
       <span class="text-blue"><b>{{@number_format($second_net_profit_profit,2)}}</b></span>
      </div>
      <div class="col-md-2 p-5">
        <span class="text-blue"><b>{{@number_format($third_net_profit_profit,2)}}</b></span>
      </div>
  </div>
</div>

@else
<div class="col-md-12 text-center" style="color: {{App\System::getProperty('not_enalbed_module_user_color')}}; font-size: {{App\System::getProperty('not_enalbed_module_user_font_size')}}px;">
    {{App\System::getProperty('not_enalbed_module_user_message')}}
</div>
@endif