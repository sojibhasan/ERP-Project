<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        @can("manufacturing.add_recipe")
        @slot('tool')
            <div class="box-tools">
                <button class="btn btn-block btn-primary btn-modal" data-container="#recipe_modal" data-href="{{action('\Modules\Manufacturing\Http\Controllers\RecipeController@create')}}">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
        @endslot
        @endcan
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="recipe_table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all-row"></th>
                        <th>@lang( 'manufacturing::lang.recipe' )</th>
                        <th>@lang( 'product.category' )</th>
                        <th>@lang( 'product.sub_category' )</th>
                        <th>@lang( 'lang_v1.quantity' )</th>
                        <th>@lang( 'lang_v1.price' ) @show_tooltip(__('manufacturing::lang.price_updated_live'))</th>
                        <th>@lang( 'sale.unit_price' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="8">
                            {{-- <button type="button" class="btn btn-xs btn-danger" id="mass_update_product_price" >@lang('manufacturing::lang.update_product_price')</button> @show_tooltip(__('manufacturing::lang.update_product_price_help')) --}}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endcomponent
</section>
