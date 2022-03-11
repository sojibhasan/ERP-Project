@extends('layouts.'.$layout)
@section('content')

<div class="container">
  @include('petro::pump_operators.partials.payment_section', ['pop_up' => false])
</div>

@endsection

@section('javascript')
<script src="{{url('Modules/Petro/Resources/assets/js/po_payment.js')}}"></script>
@endsection