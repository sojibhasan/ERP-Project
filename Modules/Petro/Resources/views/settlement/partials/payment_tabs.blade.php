<div class="row">
    <div class="col-sm-12">
        <div class="settlement_tabs">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#cash_tab" class="tabs cash_tab" data-toggle="tab">
                        <i class="fa fa-money"></i> 
                        <strong>@lang('petro::lang.cash')</strong>
                    </a>
                </li>
           
                <li>
                    <a href="#cards_tab" class="tabs cards_tab" style="" data-toggle="tab">
                        <i class="fa fa-credit-card"></i> <strong>
                            @lang('petro::lang.cards') </strong>
                    </a>
                </li>

                <li>
                    <a href="#cheques_tab" class="tabs cheques_tab" style="" data-toggle="tab">
                        <i class="fa fa-pencil"></i> <strong>
                            @lang('petro::lang.cheques') </strong>
                    </a>
                </li>

                <li>
                    <a href="#expense_tab" class="tabs expense_tab" style="" data-toggle="tab">
                        <i class="fa fa-bell-o"></i> <strong>
                            @lang('petro::lang.expneses') </strong>
                    </a>
                </li>

                <li>
                    <a href="#shortage_tab" class="tabs shortage_tab" style="" data-toggle="tab">
                        <i class="fa fa-thermometer-O"></i> <strong>
                            @lang('petro::lang.shortage') </strong>
                    </a>
                </li>
            
                <li>
                    <a href="#excess_tab" class="tabs excess_tab" style="" data-toggle="tab">
                        <i class="fa fa-thermometer-full"></i> <strong>
                            @lang('petro::lang.excess') </strong>
                    </a>
                </li>

                <li>
                    <a href="#credit_sales_tab" class="tabs credit_sales_tab" style="" data-toggle="tab">
                        <i class="fa fa-credit-card-alt"></i> <strong>
                            @lang('petro::lang.credit_sales') </strong>
                    </a>
                </li>
            
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="cash_tab">
                    @include('petro::settlement.partials.payment_tabs.cash')
                </div>

                <div class="tab-pane" id="cards_tab">
                   @include('petro::settlement.partials.payment_tabs.cards')
                </div>

                <div class="tab-pane" id="cheques_tab">
                   @include('petro::settlement.partials.payment_tabs.cheques')
                </div>

                <div class="tab-pane" id="expense_tab">
                   @include('petro::settlement.partials.payment_tabs.expense')
                </div>

                <div class="tab-pane" id="shortage_tab">
                   @include('petro::settlement.partials.payment_tabs.shortage')
                </div>

                <div class="tab-pane" id="excess_tab">
                   @include('petro::settlement.partials.payment_tabs.excess')
                </div>

                <div class="tab-pane" id="credit_sales_tab">
                   @include('petro::settlement.partials.payment_tabs.credit_sales')
                </div>

            </div>
        </div>
    </div>
</div>