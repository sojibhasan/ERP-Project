<div class="pos-tab-content">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label>@lang('superadmin::lang.login_page_showing_type')</label> @if(!empty($help_explanations['login_page_showing_type'])) @show_tooltip($help_explanations['login_page_showing_type']) @endif
                {!! Form::select('background_showing_type', ['only_background_image'
                =>__('superadmin::lang.only_background_image'), 'background_image_and_logo' =>
                __('superadmin::lang.background_image_and_logo')],
                $business->background_showing_type , ['class' => 'form-control',
                'placeholder' => __('superadmin::lang.please_select')]) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('background_image', __( 'superadmin::lang.background_image' ) . ':') !!} @if(!empty($help_explanations['backgroud_image'])) @show_tooltip($help_explanations['backgroud_image']) @endif
                {!! Form::file('background_image', ['accept' => 'image/*']); !!}
            </div>
        </div>
    </div>
</div>