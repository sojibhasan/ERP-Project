
<div class="col-md-12">
    @component('components.widget', ['class' => 'box'])
        @include('contact.partials.sell_list_filters')
    @endcomponent
</div>
<br>
<div class="col-md-12">
    @component('components.widget', ['class' => 'box'])
        @include('sale_pos.partials.sales_table')
    @endcomponent
    
</div>