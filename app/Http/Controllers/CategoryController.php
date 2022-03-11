<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountType;
use App\Category;
use App\Product;
use App\Transaction;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use CreateCategoriesTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Superadmin\Entities\HelpExplanation;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $businessUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, BusinessUtil $businessUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $enable_petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');

            $category = Category::leftjoin('accounts as cogs_account', 'categories.cogs_account_id', 'cogs_account.id')
                ->leftjoin('accounts as sale_account', 'categories.sales_income_account_id', 'sale_account.id')
                ->where('categories.business_id', $business_id)
                ->select('categories.name', 'short_code', 'categories.id', 'parent_id', 'cogs_account.name as cogs', 'sale_account.name as sales_accounts');
            if ($enable_petro_module == 0) {
                $category = $category->where('categories.id', '!=', 1)->where('categories.parent_id', '!=', 1);
            }
            $category = $category->get()->sortBy('parent_id');

            return Datatables::of($category)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        if ($row->name != "Fuel") {
                            if (auth()->user()->can('category.update')) {
                                $html .= '<button data-href="' . action("CategoryController@edit", [$row->id]) . '" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  ' . __("messages.edit") . '</button> &nbsp;';
                            }
                            if (auth()->user()->can('category.delete')) {
                                if ($this->canDeleteCategory($row->id)) {
                                    $html .= '<button data-href="' . action("CategoryController@destroy", [$row->id]) . '" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';
                                }
                            }
                        }
                        return $html;
                    }

                )
                ->addColumn('category_name', function ($row) {
                    if ($row->parent_id == 0) {
                        return $row->name;
                    } else {
                        return Category::where('id', $row->parent_id)->first()->name;
                    }
                })
                ->addColumn('category_short_code', function ($row) {
                    if ($row->parent_id == 0) {
                        return $row->short_code;
                    } else {
                        return Category::where('id', $row->parent_id)->first()->short_code;
                    }
                })
                ->addColumn('sub_category_name', function ($row) {
                    if ($row->parent_id != 0) {
                        return $row->name;
                    } else {
                        return '';
                    }
                })
                ->addColumn('sub_category_short_code', function ($row) {
                    if ($row->parent_id != 0) {
                        return $row->short_code;
                    } else {
                        return '';
                    }
                })
                ->removeColumn('id')
                ->removeColumn('parent_id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('category.index');
    }

    public function canDeleteCategory($category_id)
    {
        if (Category::where('parent_id', $category_id)->count() > 0) {
            return false;
        }
        if (Product::where('category_id', $category_id)->orWhere('sub_category_id', $category_id)->count() > 0) {
            return false;
        }

        if(Transaction::leftjoin('products', 'transactions.opening_stock_product_id', 'products.id')->where('category_id', $category_id)->orWhere('sub_category_id', $category_id)->count() > 0){
            return false;
        }
        if(Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.id')->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')->where('category_id', $category_id)->orWhere('sub_category_id', $category_id)->count() > 0){
            return false;
        }
        if(Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.id')->leftjoin('products', 'purchase_lines.product_id', 'products.id')->where('category_id', $category_id)->orWhere('sub_category_id', $category_id)->count() > 0){
            return false;
        }

        return true;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');

        $cogs_group_id = AccountGroup::getGroupByName('COGS Account Group');
        $sale_income_group_id = AccountGroup::getGroupByName('Sales Income Group');
        $expense_account_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id, true);
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id, true);
        $cogs_accounts = [];
        if (!empty($cogs_group_id)) {
            $cogs_accounts = Account::getAccountByAccountGroupId($cogs_group_id->id);
        }
        $sale_income_accounts = [];
        if (!empty($sale_income_group_id)) {
            $sale_income_accounts = Account::getAccountByAccountGroupId($sale_income_group_id->id);
        }
        $expense_accounts = [];
        if (!empty($expense_account_type_id)) {
            $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_account_type_id)->pluck('name', 'id');
        }
        $income_accounts = [];
        if (!empty($income_type_id)) {
            $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');
        }

        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->select(['name', 'short_code', 'id'])
            ->get();
        $parent_categories = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $parent_categories[$category->id] = $category->name;
            }
        }

        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        return view('category.create')
            ->with(compact('parent_categories', 'account_access', 'cogs_accounts', 'sale_income_accounts', 'expense_accounts', 'income_accounts', 'help_explanations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');

        if ($account_access) {
            if ($request->add_related_account == 'category_level') {
                $validator = Validator::make($request->all(), [
                    'cogs_account_id' => 'required',
                    'sales_income_account_id' => 'required',
                    'add_related_account' => 'required'
                ]);

                if ($validator->fails()) {
                    $output = [
                        'success' => 0,
                        'msg' => $validator->errors()->all()[0]
                    ];
                    return $output;
                }
            }
        }

        try {
            $input = $request->only(['name', 'short_code', 'add_related_account', 'cogs_account_id', 'sales_income_account_id']);
            if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                $input['parent_id'] = $request->input('parent_id');
            } else {
                $input['parent_id'] = 0;
            }
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['weight_excess_loss_applicable'] = !empty($request->weight_excess_loss_applicable) ? 1 : 0;
            $input['weight_loss_expense_account_id'] = $request->weight_loss_expense_account_id;
            $input['weight_excess_income_account_id'] = $request->weight_excess_income_account_id;
            $input['created_by'] = $request->session()->get('user.id');

            $category = Category::create($input);
            $output = [
                'success' => true,
                'data' => $category,
                'msg' => __("category.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('category.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
            $category = Category::where('business_id', $business_id)->find($id);

            $cogs_group_id = AccountGroup::getGroupByName('COGS Account Group');
            $sale_income_group_id = AccountGroup::getGroupByName('Sales Income Group');
            $expense_account_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id, true);
            $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id, true);
            $cogs_accounts = [];
            if (!empty($cogs_group_id)) {
                $cogs_accounts = Account::getAccountByAccountGroupId($cogs_group_id->id);
            }
            $sale_income_accounts = [];
            if (!empty($sale_income_group_id)) {
                $sale_income_accounts = Account::getAccountByAccountGroupId($sale_income_group_id->id);
            }
            $expense_accounts = [];
            if (!empty($expense_account_type_id)) {
                $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_account_type_id)->pluck('name', 'id');
            }
            $income_accounts = [];
            if (!empty($income_type_id)) {
                $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');
            }

            $parent_categories = Category::where('business_id', $business_id)
                ->where('parent_id', 0)
                ->where('id', '!=', $id)
                ->pluck('name', 'id');

            $is_parent = false;

            if ($category->parent_id == 0) {
                $is_parent = true;
                $selected_parent = null;
            } else {
                $selected_parent = $category->parent_id;
            }

            return view('category.edit')
                ->with(compact('category', 'parent_categories', 'is_parent', 'selected_parent', 'account_access', 'cogs_accounts', 'sale_income_accounts', 'expense_accounts', 'income_accounts'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('category.update')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');

        if ($account_access) {
            $validator = Validator::make($request->all(), [
                'cogs_account_id' => 'required',
                'sales_income_account_id' => 'required',
                'add_related_account' => 'required'
            ]);

            if ($validator->fails()) {
                $output = [
                    'success' => 0,
                    'msg' => $validator->errors()->all()[0]
                ];
                return $output;
            }
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'short_code', 'cogs_account_id', 'sales_income_account_id', 'add_related_account']);
                $business_id = $request->session()->get('user.business_id');

                $category = Category::where('business_id', $business_id)->findOrFail($id);
                $category->name = $input['name'];
                $category->short_code = $input['short_code'];
                $category->cogs_account_id = $input['cogs_account_id'];
                $category->sales_income_account_id = $input['sales_income_account_id'];
                $category->add_related_account = $input['add_related_account'];
                
                $category->weight_excess_loss_applicable = !empty($request->weight_excess_loss_applicable) ? 1 : 0;
                $category->weight_loss_expense_account_id = $request->weight_loss_expense_account_id;
                $category->weight_excess_income_account_id = $request->weight_excess_income_account_id;

                if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                    $category->parent_id = $request->input('parent_id');
                } else {
                    $category->parent_id = 0;
                }
                $category->save();

                $output = [
                    'success' => true,
                    'msg' => __("category.updated_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('category.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $category = Category::where('business_id', $business_id)->findOrFail($id);
                $category->delete();

                $output = [
                    'success' => true,
                    'msg' => __("category.deleted_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    public function getCategoriesApi()
    {
        try {
            $api_token = request()->header('API-TOKEN');

            $api_settings = $this->moduleUtil->getApiSettings($api_token);

            $categories = Category::catAndSubCategories($api_settings->business_id);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->respondWentWrong($e);
        }

        return $this->respond($categories);
    }
}
