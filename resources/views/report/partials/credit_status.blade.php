<div class="col-sm-12">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('report.credit_status')])
    {!! $sells_chart_1->container() !!}
    @endcomponent
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>
{!! $sells_chart_1->script() !!}