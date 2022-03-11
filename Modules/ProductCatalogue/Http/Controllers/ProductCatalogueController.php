<?php

namespace Modules\ProductCatalogue\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Product;
use App\Business;
use App\Discount;
use App\SellingPriceGroup;
use App\Utils\ProductUtil;
use App\BusinessLocation;
use App\Category;
use App\Utils\ModuleUtil;
use App\VariationLocationDetails;

class ProductCatalogueController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($business_id, $location_id)
    {

        $business = Business::with(['currency'])->findOrFail($business_id);
        $business_location = BusinessLocation::where('business_id', $business_id)->findOrFail($location_id);
        $enable_petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');
        if (request()->ajax()) {
            $sub_category_id = request()->sub_category;
            $category_id = request()->category_id;
            $query = Product::leftjoin('categories as cat', 'products.category_id', 'cat.id')->where('products.business_id', $business_id)->where('show_in_catalogue_page', 1)
                ->whereHas('product_locations', function ($q) use ($location_id) {
                    $q->where('product_locations.location_id', $location_id);
                })
                ->ProductForSales()
                ->select('products.*')
                ->with(['variations', 'variations.product_variation', 'category']);
            if (!empty($sub_category_id)) {
                $query->where('sub_category_id', $sub_category_id);
            }
            if (!empty($category_id)) {
                $query->where('category_id', $category_id);
            }
            if (!empty($category_id)) {
                $query->where('category_id', $category_id);
            }
            if (!$enable_petro_module) {
                $query->where('cat.name', '!=', 'Fuel');
            }
            $products = $query->get();

            $now = \Carbon::now()->toDateTimeString();
            $discounts = Discount::where('business_id', $business_id)
                ->where('location_id', $location_id)
                ->where('is_active', 1)
                ->where('starts_at', '<=', $now)
                ->where('ends_at', '>=', $now)
                ->orderBy('priority', 'desc')
                ->get();
            return view('productcatalogue::catalogue.sub')->with(compact('products', 'business', 'discounts', 'business_location', 'location_id', 'business_id'));
        }

        $cats_query = Category::where('parent_id', 0)->where(
            'business_id',
            request()->session()->get('business.id')
        );
        if (!$enable_petro_module) {
            $cats = $cats_query->notPetro()->get();
        } else {
            $cats = $cats_query->get();
        }
        $sub_cats_query = Category::where('parent_id', '!=', 0)->where(
            'business_id',
            request()->session()->get('business.id')
        );
        if (!$enable_petro_module) {
            $sub_cats = $sub_cats_query->notPetro()->get();
        } else {
        }
        $sub_cats =  Category::subCategory($business_id, $enable_petro_module);

        return view('productcatalogue::catalogue.index')->with(compact('location_id', 'business', 'business_location', 'business_id', 'enable_petro_module', 'cats', 'sub_cats'));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($business_id, $id)
    {
        $product = Product::with(['brand', 'unit', 'category', 'sub_category', 'product_tax', 'variations', 'variations.product_variation', 'variations.group_prices', 'variations.media', 'product_locations', 'warranty'])->where('business_id', $business_id)
            ->findOrFail($id);

        $qty_available = VariationLocationDetails::where('product_id', $product->id)->where('location_id',  request()->input('location_id'))->select('qty_available')->first();

        $price_groups = SellingPriceGroup::where('business_id', $product->business_id)->where('active', 1)->pluck('name', 'id');

        $allowed_group_prices = [];
        foreach ($price_groups as $key => $value) {
            $allowed_group_prices[$key] = $value;
        }

        $group_price_details = [];
        $discounts = [];
        foreach ($product->variations as $variation) {
            foreach ($variation->group_prices as $group_price) {
                $group_price_details[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
            }

            $discounts[$variation->id] = $this->productUtil->getProductDiscount($product, $product->business_id, request()->input('location_id'), false, false, $variation->id);
        }

        $combo_variations = [];
        if ($product->type == 'combo') {
            $combo_variations = $this->productUtil->__getComboProductDetails($product['variations'][0]->combo_variations, $product->business_id);
        }

        return view('productcatalogue::catalogue.show')->with(compact(
            'product',
            'allowed_group_prices',
            'group_price_details',
            'combo_variations',
            'qty_available',
            'discounts'
        ));
    }

    public function generateQr()
    {
        $business_id = request()->session()->get('user.business_id');
        // if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'productcatalogue_module'))) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('productcatalogue::catalogue.generate_qr')
            ->with(compact('business_locations'));
    }
}
