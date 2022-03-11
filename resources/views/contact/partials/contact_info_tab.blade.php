<span id="view_contact_page"></span>
<div class="row">
    <div class="col-md-12">
        <div class="col-sm-3">
            @include('contact.contact_basic_info')
        </div>
        <div class="col-sm-3">
            @include('contact.contact_more_info')
        </div>
        @if( $contact->type != 'customer')
            <div class="col-sm-3">
                @include('contact.contact_tax_info')
            </div>
        @endif
        <div class="col-sm-3">
            @include('contact.contact_payment_info')
        </div>
        @if($reward_enabled)
            <div class="clearfix"></div>
            <div class="col-md-3">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-gift"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">{{session('business.rp_name')}}</span>
                      <span class="info-box-number">{{$contact->total_rp ?? 0}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
        @endif
        @if( $contact->type == 'supplier' || $contact->type == 'both')
            <div class="clearfix"></div>
            <div class="col-sm-12">
                @if(($contact->total_purchase - $contact->purchase_paid) > 0)
                    <a href="{{action('TransactionPaymentController@getPayContactDue', [$contact->id])}}?type=purchase" class="pay_purchase_due btn btn-primary btn-sm pull-right"><i class="fas fa-money-bill-alt" aria-hidden="true"></i> @lang("contact.pay_due_amount")</a>
                @endif
            </div>
        @endif
        <div class="col-sm-3">
            @include('contact.photos')
        </div>

    </div>
</div>