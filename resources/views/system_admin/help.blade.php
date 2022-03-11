@extends('layouts.app')
@section('title', __( 'lang_v1.system_administrations'))

@section('content')
<!-- Editor CSS file -->
<link rel="stylesheet" href="{{asset('public/css/editor.css')}}">
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang( 'site_settings.site_settings')
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'site_settings.settings')])

    {{-- @if(auth()->user()->can('direct_sell.access'))) --}}
    <form action="{{route('site_settings.help_update')}}" method="post" id="settingForm" enctype="multipart/form-data">
        @csrf
        @php
            $tour_toggle = DB::table('site_settings')->where('id', 1)->select('tour_toggle')->first()->tour_toggle;
        @endphp
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    <input class="input-icheck" type="checkbox" name="tour_toggle" id="tour_toggle" value="1" @if($tour_toggle ==1) checked @endif>{{ __('lang_v1.tour_toggle') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h3> Application Tour </h3>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="tour_step1_title" id="tour_step1_title" value=""
                                        class="form-control" autofocus autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea id="tour_step1_content" name="tour_step1_content"
                                        class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h3> Tuor Step 1</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="tour_step2_title" id="tour_step2_title" value=""
                                        class="form-control" autofocus autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea id="tour_step2_content" name="tour_step2_content"
                                        class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h3> Tuor Step 2</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="tour_step3_title" id="tour_step3_title" value=""
                                        class="form-control" autofocus autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea id="tour_step3_content" name="tour_step3_content"
                                        class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h3> Tuor Step 3</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="tour_step4_title" id="tour_step4_title" value=""
                                        class="form-control" autofocus autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea id="tour_step4_content" name="tour_step4_content"
                                        class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h3> Tuor Step 4</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="tour_step5_title" id="tour_step5_title" value=""
                                        class="form-control" autofocus autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea id="tour_step5_content" name="tour_step5_content"
                                        class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h3> Tuor Step 5</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="tour_step6_title" id="tour_step6_title" value=""
                                        class="form-control" autofocus autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea id="tour_step6_content" name="tour_step6_content"
                                        class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h3> Tuor Step 6</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="tour_step7_title" id="tour_step7_title" value=""
                                        class="form-control" autofocus autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea id="tour_step7_content" name="tour_step7_content"
                                        class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h3> Tuor Step 7</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="tour_step8_title" id="tour_step8_title" value=""
                                        class="form-control" autofocus autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea id="tour_step8_content" name="tour_step8_content"
                                        class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row pull-right" style="margin-top: 20px;">
            <div class="col-md-4">
                <div class="form-group">
                    <button class="btn btn-primary" id="settingForm_button">&nbsp;&nbsp;Update Help
                        Setting&nbsp;&nbsp;</button>
                </div>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4"></div>
        </div>
    </form>
    {{-- @endif --}}
    @endcomponent
</section>

<!-- This will be printed -->
<!-- <section class="invoice print_section" id="receipt_section">
</section> -->

@stop


@section('javascript')
<script>
    $('#tour_step1_title').val(LANG.tour_step1_title);
    $('#tour_step1_content').val(LANG.tour_step1_content);
    $('#tour_step2_title').val(LANG.tour_step2_title);
    $('#tour_step2_content').val(LANG.tour_step2_content);
    $('#tour_step3_title').val(LANG.tour_step3_title);
    $('#tour_step3_content').val(LANG.tour_step3_content);
    $('#tour_step4_title').val(LANG.tour_step4_title);
    $('#tour_step4_content').val(LANG.tour_step4_content);
    $('#tour_step5_title').val(LANG.tour_step5_title);
    $('#tour_step5_content').val(LANG.tour_step5_content);
    $('#tour_step6_title').val(LANG.tour_step6_title);
    $('#tour_step6_content').val(LANG.tour_step6_content);
    $('#tour_step7_title').val(LANG.tour_step7_title);
    $('#tour_step7_content').val(LANG.tour_step7_content);
    $('#tour_step8_title').val(LANG.tour_step8_title);
    $('#tour_step8_content').val(LANG.tour_step8_content);

//     const fs = require('{{asset('public/js/lang/en.js')}}');
// console.log(fs);

//     $('#settingForm_button').click(function(e){
//         e.preventDefault();
//         let ts_1_title = $('#ts_1_title').val();
//         LANG.tour_step1_title = ts_1_title;
//         console.log(ts_1_title);
        
//     });
</script>
@endsection