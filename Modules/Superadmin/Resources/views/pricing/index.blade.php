@extends('layouts.auth')
@section('title', __('superadmin::lang.pricing'))

@section('content')
<style>
    .modal {
        text-align: center;
    }
</style>
<div class="container">
    @include('superadmin::layouts.partials.currency')
    @include('layouts.partials.logo')
    <div class="row">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title text-center">@lang('superadmin::lang.packages')</h3>
            </div>

            <div class="box-body">
                @include('superadmin::subscription.partials.packages', ['action_type' => 'register'])
            </div>
        </div>
    </div>
</div>
<div class="modal fade package_veriables_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        $('#change_lang').change( function(){
            window.location = "{{ route('pricing') }}?lang=" + $(this).val();
        });
    })

    $('.register_form_modal').click(function(){
        let this_package_id = $(this).attr('id');
        let is_visitor_pacakge = $(this).data('is_visitor_pacakge');
        console.log(is_visitor_pacakge);
        if(is_visitor_pacakge){
            $('.not_visitor_register_field').addClass('hide');
        }else{
            $('.not_visitor_register_field').removeClass('hide');
        }
        $('.package_id').val(this_package_id);
    })
</script>
@endsection