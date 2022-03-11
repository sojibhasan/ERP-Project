<style>
    .btn-large {
        padding: 18px 28px;
        font-size: 22px; //change this to your desired size
        line-height: normal;
    }

    .active {
        background: #666 !important;
    }

    #key_pad input {
        border: none;
    }

    #key_pad button {
        height: 80px;
        width: 30%;
        font-size: 25px;
        margin: 2px 1px;
        border: none !important;
    }

    .payment_type_checkbox {
        display: none;
    }
</style>
<form name="calculator">
    <div class="clearfix"></div>
    <br />
    <div class="">
        <div class="col-md-8 col-lg-8">
            <div class="row">
                <div class="col-md-5">
                    <h2>@lang('petro::lang.payments')</h2>
                </div>
                <div class="col-md-6">
                    <input name="display" class="form-control input-lg amount input_number" style="margin-top: 10px; background: #fff; border: 2px solid #333;" id="amount" value="" />
                    <input type="hidden" name="payment_type" id="payment_type" value="" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="@if($pop_up)col-md-12 @else col-md-8 @endif">
            <div class="col-md-5 col-lg-5">
                <div class="row">
                    <div class="btn-group-vertical">
                        <label class="payment_type_btn btn btn-large btn-flat btn-block btn-primary">
                            <input class="payment_type_checkbox" type="checkbox" name="payment_type" value="cash" autocomplete="off" />
                            @lang('petro::lang.cash')
                        </label>
                        <label class="payment_type_btn btn btn-large btn-flat btn-block btn-info">
                            <input class="payment_type_checkbox" type="checkbox" name="payment_type" value="card" autocomplete="off" />
                            @lang('petro::lang.card')
                        </label>
                        <label class="payment_type_btn btn btn-large btn-flat btn-block btn-danger">
                            <input class="payment_type_checkbox" type="checkbox" name="payment_type" value="cheque" autocomplete="off" /> @lang('petro::lang.cheque')
                        </label>
                        <label class="payment_type_btn btn btn-large btn-flat btn-block btn-warning">
                            <input class="payment_type_checkbox" type="checkbox" name="payment_type" value="credit" autocomplete="off" /> @lang('petro::lang.credit')
                        </label>
                        <label class="payment_type_btn btn btn-large btn-flat btn-block btn-success">
                            <input class="payment_type_checkbox" type="checkbox" name="payment_type" value="multiple_credit" autocomplete="off" /> @lang('petro::lang.multiple_credit')
                        </label>
                    </div>
                </div>
            </div>
            <div id="key_pad" class="row col-md-6 text-center" style="margin-left: 7px;">
                <div class="row">
                    <button id="7" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">7</button>
                    <button id="8" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">8</button>
                    <button id="9" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">9</button>
                </div>
                <div class="row">
                    <button id="4" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">4</button>
                    <button id="5" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">5</button>
                    <button id="6" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">6</button>
                </div>
                <div class="row">
                    <button id="1" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">1</button>
                    <button id="2" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">2</button>
                    <button id="3" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">3</button>
                </div>
                <div class="row">
                    <button id="backspace" type="button" class="btn btn-danger" onclick="enterVal(this.id)">⌫</button>
                    <button id="0" type="button" class="btn btn-primary btn-sm" onclick="enterVal(this.id)">0</button>
                    <button id="precision" type="button" class="btn btn-success" onclick="enterVal(this.id)">.</button>
                </div>
            </div>
        </div>
        @if(!$pop_up)
        <div class="col-md-3">
            <div class="row">
                <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}"><input value="Dashboard" class="btn btn-flat btn-lg btn-block" style="color: #fff; background-color: #810040;" type="button" /> </a>
                <br />
                <br />
                <br />
                <button disabled value="save" id="payment_submit" name="submit" class="btn btn-flat btn-lg btn-block" style="color: #fff; background-color: #2874a6;" type="button">@lang('lang_v1.save')</button>
                <br />
                <br />
                <br />
                <span onclick="reset()">
                    <button type="button" class="btn btn-flat btn-lg btn-block" style="color: #fff; background-color: #cc0000;" type="button"><i class="fa fa-refresh" aria-hidden="true"></i> @lang('petro::lang.cancel')</button>
                </span>
                <br />
                <br />
                <a href="{{action('Auth\PumpOperatorLoginController@logout')}}" class="btn btn-flat btn-block btn-lg pull-right" style="background-color: orange; color: #fff;">@lang('petro::lang.logout')</a>
            </div>
        </div>
        @endif
    </div>
</form>
