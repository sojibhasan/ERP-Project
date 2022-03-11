<?php

namespace App\Http\Controllers;

use App\Business;
use Illuminate\Http\Request;
use Excel;
use App\BusinessLocation;
use Illuminate\Support\Facades\DB;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\Product;
use App\TaxRate;
use App\Transaction;
use App\Contact;
use App\Store;
use App\Utils\ModuleUtil;
use App\TypesOfService;
use App\Unit;
use Carbon\Carbon;
use PHPUnit\TextUI\ResultPrinter;

class ImportPurchasesController extends Controller
{
	/**
	 * All Utils instance.
	 *
	 */
	protected $productUtil;
	protected $businessUtil;
	protected $transactionUtil;
	protected $moduleUtil;

	/**
	 * Constructor
	 *
	 * @param ProductUtils $product
	 * @return void
	 */
	public function __construct(
		ProductUtil $productUtil,
		BusinessUtil $businessUtil,
		TransactionUtil $transactionUtil,
		ModuleUtil $moduleUtil
	) {
		$this->productUtil = $productUtil;
		$this->businessUtil = $businessUtil;
		$this->transactionUtil = $transactionUtil;
		$this->moduleUtil = $moduleUtil;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if (!auth()->user()->can('sell.create')) {
			abort(403, 'Unauthorized action.');
		}

		$business_id = request()->session()->get('user.business_id');

		$imported_sales = Transaction::where('business_id', $business_id)
			->where('type', 'sell')
			->whereNotNull('import_batch')
			->with(['sales_person'])
			->select('id', 'import_batch', 'import_time', 'invoice_no', 'created_by')
			->orderBy('import_batch', 'desc')
			->get();

		$imported_sales_array = [];
		foreach ($imported_sales as $sale) {
			$imported_sales_array[$sale->import_batch]['import_time'] = $sale->import_time;
			$imported_sales_array[$sale->import_batch]['created_by'] = $sale->sales_person->user_full_name;
			$imported_sales_array[$sale->import_batch]['invoices'][] = $sale->invoice_no;
		}

		$import_fields = $this->__importFields();

		return view('import_purchases.index')->with(compact('imported_sales_array', 'import_fields'));
	}

	/**
	 * Preview imported data and map columns with sale fields
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function preview(Request $request)
	{
		if (!auth()->user()->can('sell.create')) {
			abort(403, 'Unauthorized action.');
		}

		$notAllowed = $this->businessUtil->notAllowedInDemo();
		if (!empty($notAllowed)) {
			return $notAllowed;
		}

		$business_id = request()->session()->get('user.business_id');

		if ($request->hasFile('purchases')) {

			$file_name = time() . '_' . $request->purchases->getClientOriginalName();
			$request->purchases->storeAs('temp', $file_name);

			$parsed_array = $this->__parseData($file_name);

			$import_fields = $this->__importFields();
			foreach ($import_fields as $key => $value) {
				$import_fields[$key] = $value['label'];
			}

			//Evaluate highest matching field with the header to pre select from dropdown
			$headers = $parsed_array[0];
			$match_array = [];
			foreach ($headers as $key => $value) {
				$match_percentage = [];
				foreach ($import_fields as $k => $v) {
					similar_text($value, $v, $percentage);
					$match_percentage[$k] = $percentage;
				}
				$max_key = array_keys($match_percentage, max($match_percentage))[0];

				//If match percentage is greater than 50% then pre select the value
				$match_array[$key] = $match_percentage[$max_key] >= 50 ? $max_key : null;
			}

			$business_locations = BusinessLocation::forDropdown($business_id);

			return view('import_purchases.preview')->with(compact('parsed_array', 'import_fields', 'file_name', 'business_locations', 'match_array'));
		}
	}

	public function __parseData($file_name)
	{
		$array = Excel::toArray([], public_path('uploads/temp/' . $file_name))[0];

		//remove blank columns from headers
		$headers = array_filter($array[0]);
		//Remove header row
		unset($array[0]);
		$parsed_array[] = $headers;
		foreach ($array as $row) {
			$temp = [];
			foreach ($row as $k => $v) {
				if (array_key_exists($k, $headers)) {
					$temp[] = $v;
				}
			}
			$parsed_array[] = $temp;
		}

		return $parsed_array;
	}

	/**
	 * Import sales to database
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function import(Request $request)
	{
		if (!auth()->user()->can('sell.create')) {
			abort(403, 'Unauthorized action.');
		}

		try {
			DB::beginTransaction();

			$file_name = $request->input('file_name');
			$import_fields = $request->input('import_fields');
			$group_by = $request->input('group_by');
			$location_id = $request->input('location_id');
			$business_id = $request->session()->get('user.business_id');

			$file_path = public_path('uploads/temp/' . $file_name);
			$parsed_array = $this->__parseData($file_name);
			//Remove header row
			unset($parsed_array[0]);
			$formatted_sales_data = $this->__formatPurchaseData($parsed_array, $import_fields, $group_by);
			//Set maximum php execution time
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', -1);

			$this->__importPurchases($formatted_sales_data, $business_id, $location_id);

			DB::commit();

			$output = [
				'success' => 1,
				'msg' => __('lang_v1.purchases_imported_successfully')
			];
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

			$output = [
				'success' => 0,
				'msg' => $e->getMessage()
			];

			@unlink($file_path);
			return redirect('import-purchases')->with('notification', $output);
		}

		@unlink($file_path);

		return redirect('import-purchases')->with('status', $output);
	}

	private function __importPurchases($formated_data, $business_id, $location_id)
	{
		$import_batch = Transaction::where('business_id', $business_id)->max('import_batch');
		$business_id = request()->session()->get('business.id');
		if (empty($import_batch)) {
			$import_batch = 1;
		} else {
			$import_batch = $import_batch + 1;
		}

		$now = \Carbon::now()->toDateTimeString();
		$row_index = 2;
		foreach ($formated_data as $data) {
			$order_total = 0;
			$purchase_lines = [];
			foreach ($data as $line_data) {
				$this_date = Carbon::parse($line_data['date'])->format('Y-m-d');
				$purchase_import_date = Business::where('id', $business_id)->first()->purchase_import_date;

				//transaction date is older then permmited date reurn false
				if ($this_date < $purchase_import_date) {
					$output = [
						'success' => false,
						'msg' => __('lang_v1.purchase_import_older_date_error')
					];

					throw new \Exception(__('lang_v1.sale_import_older_date_error', ['row' => $row_index, 'product_name' => $line_data['product'], 'sku' => $line_data['sku']]));
				}
				if (!empty($line_data['sku'])) {
					$variation = Variation::where('sub_sku', $line_data['sku'])->with(['product'])->first();

					$product = !empty($variation) ? $variation->product : null;
				} else {
					$product = Product::where('business_id', $business_id)
						->where('name', $line_data['product'])
						->with(['variations'])
						->first();
					$variation = !empty($product) ? $product->variations->first() : null;
				}

				if (empty($variation)) {
					throw new \Exception(__('lang_v1.import_sale_product_not_found', ['row' => $row_index, 'product_name' => $line_data['product'], 'sku' => $line_data['sku']]));
				}

				$tax_id = null;
				$item_tax = 0;
				$price_before_tax = $line_data['unit_price'];
				if (!empty($line_data['item_tax'])) {
					$tax = TaxRate::where('business_id', $business_id)
						->where('name', $line_data['item_tax'])
						->first();

					if (empty($tax)) {
						throw new \Exception(__('lang_v1.import_sale_tax_not_found', ['row' => $row_index, 'tax_name' => $line_data['item_tax']]));
					}
					$tax_id = $tax->id;
					$price_before_tax = $this->transactionUtil->calc_percentage_base($line_data['unit_price'], $tax->amount);
					$item_tax = $line_data['unit_price'] - $price_before_tax;
				}

				$line_discount = !empty($line_data['item_discount']) ? $line_data['item_discount'] : 0;
				$unit_price = $price_before_tax + $line_discount;

				$temp = [
					'product_id' => $variation->product_id,
					'variation_id' => $variation->id,
					'quantity' => $line_data['quantity'],
					'unit_price' => $unit_price,
					'pp_without_discount' => $this->productUtil->num_uf($line_data['unit_price']),
					'purchase_price' => $this->productUtil->num_uf($line_data['unit_price']),
					'purchase_price_inc_tax' => $this->productUtil->num_uf($line_data['unit_price']),
					'item_tax' => 0,
					'purchase_line_tax_id' => null,
					'line_discount_type' => 'fixed',
					'discount_percent' => 'fixed',
					'line_discount_amount' => $line_discount,
					'item_tax' => $item_tax,
					'tax_id' => $tax_id,
					'sell_line_note' =>  $line_data['item_description'],
					'product_unit_id' => $product->unit_id,
					'enable_stock' => $product->enable_stock,
					'type' => $product->type,
					'combo_variations' => $product->type == 'combo' ? $variation->combo_variations : []
				];

				$line_quantity = $line_data['quantity'];
				if (!empty($line_data['unit'])) {
					$unit_name = trim($line_data['unit']);
					$unit = Unit::where('actual_name', $unit_name)
						->orWhere('short_name', $unit_name)
						->first();

					if (empty($unit)) {
						throw new \Exception(__('lang_v1.import_sale_unit_not_found', ['row' => $row_index, 'unit_name' => $unit_name]));
					}

					//Check if sub unit
					if ($unit->id != $product->unit_id) {
						$temp['sub_unit_id'] = $unit->id;
						$temp['base_unit_multiplier'] = $unit->base_unit_multiplier;
						$line_quantity = ($line_quantity * $unit->base_unit_multiplier);
					}
				}
				$order_total += ($temp['pp_without_discount'] * $line_quantity);

				$purchase_lines[] = $temp;

				$row_index++;
			}

			$first_purchase_line = $data[0];
			//get contact
			if (!empty($first_purchase_line['supplier_phone_number'])) {
				$contact = Contact::where('business_id', $business_id)
					->where('mobile', $first_purchase_line['supplier_phone_number'])
					->first();
			} elseif (!empty($first_purchase_line['supplier_email'])) {
				$contact = Contact::where('business_id', $business_id)
					->where('email', $first_purchase_line['supplier_email'])
					->first();
			}
			if (empty($contact)) {
				$supplier_name = !empty($first_purchase_line['supplier_name']) ? $first_purchase_line['supplier_name'] : $first_purchase_line['supplier_phone_number'];
				$contact = Contact::create([
					'business_id' => $business_id,
					'type' => 'supplier',
					'name' => $supplier_name,
					'email' => $first_purchase_line['supplier_email'],
					'mobile' => $first_purchase_line['supplier_phone_number'],
					'created_by' => auth()->user()->id
				]);
			}
			$purchase_data = [
				'business_id' => $business_id,
				'invoice_no' => $first_purchase_line['invoice_no'],
				'location_id' => $location_id,
				'type' => 'purchase',
				'status' => 'final',
				'contact_id' => $contact->id,
				'final_total' => !empty($first_purchase_line['order_total']) ? $first_purchase_line['order_total'] : $order_total,
				'transaction_date' => !empty($first_purchase_line['date']) ? $first_purchase_line['date'] : $now,
				'discount_amount' => 0,
				'import_batch' => $import_batch,
				'import_time' => $now,
				'commission_agent' => null,
			];

			$is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');
			if ($is_types_service_enabled && !empty($first_purchase_line['types_of_service'])) {
				$types_of_service = TypesOfService::where('business_id', $business_id)
					->where('name', $first_purchase_line['types_of_service'])
					->first();

				if (empty($types_of_service)) {
					throw new \Exception(__('lang_v1.types_of_servicet_not_found', ['row' => $row_index, 'types_of_service_name' => $first_purchase_line['types_of_service']]));
				}

				$purchase_data['types_of_service_id'] = $types_of_service->id;
				$purchase_data['service_custom_field_1'] = !empty($first_purchase_line['service_custom_field1']) ? $first_purchase_line['service_custom_field1'] : null;
				$purchase_data['service_custom_field_2'] = !empty($first_purchase_line['service_custom_field2']) ? $first_purchase_line['service_custom_field2'] : null;
				$purchase_data['service_custom_field_3'] = !empty($first_purchase_line['service_custom_field3']) ? $first_purchase_line['service_custom_field3'] : null;
				$purchase_data['service_custom_field_4'] = !empty($first_purchase_line['service_custom_field4']) ? $first_purchase_line['service_custom_field4'] : null;
			}

			$invoice_total = [
				'total_before_tax' => !empty($first_purchase_line['order_total']) ? $first_purchase_line['order_total'] : $order_total,
				'tax' => 0
			];

			// $transaction = $this->transactionUtil->createSellTransaction($business_id, $purchase_data, $invoice_total, auth()->user()->id, false);
			$transaction = Transaction::create($purchase_data);
			$currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
			$store_id = Store::where('business_id', $business_id)->first()->id;
            $enable_product_editing = request()->session()->get('business.enable_editing_product_from_purchase');
			$this->productUtil->createOrUpdatePurchaseLines($transaction, $purchase_lines, $currency_details, $enable_product_editing, $store_id, null);

			foreach ($purchase_lines as $line) {
				if ($line['enable_stock']) {
					$this->productUtil->decreaseProductQuantity(
						$line['product_id'],
						$line['variation_id'],
						$location_id,
						$line['quantity']
					);
				}

				if ($line['type'] == 'combo') {
					$line_total_quantity = $line['quantity'];
					if (!empty($line['base_unit_multiplier'])) {
						$line_total_quantity = $line_total_quantity * $line['base_unit_multiplier'];
					}

					//Decrease quantity of combo as well.
					$combo_details = [];
					foreach ($line['combo_variations'] as $combo_variation) {
						$combo_variation_obj = Variation::find($combo_variation['variation_id']);

						//Multiply both subunit multiplier of child product and parent product to the quantity
						$combo_variation_quantity = $combo_variation['quantity'];
						if (!empty($combo_variation['unit_id'])) {
							$combo_variation_unit = Unit::find($combo_variation['unit_id']);
							if (!empty($combo_variation_unit->base_unit_multiplier)) {
								$combo_variation_quantity = $combo_variation_quantity * $combo_variation_unit->base_unit_multiplier;
							}
						}

						$combo_details[] = [
							'product_id' => $combo_variation_obj->product_id,
							'variation_id' => $combo_variation['variation_id'],
							'quantity' => $combo_variation_quantity * $line_total_quantity
						];
					}

					$this->productUtil
						->decreaseProductQuantityCombo(
							$combo_details,
							$location_id
						);
				}
			}

			//Update payment status
			$this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

			// $business_details = $this->businessUtil->getDetails($business_id);
			// $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

			// $business = [
			// 	'id' => $business_id,
			// 	'accounting_method' => request()->session()->get('business.accounting_method'),
			// 	'location_id' => $location_id,
			// 	'pos_settings' => $pos_settings
			// ];
			// $this->transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');
		}
	}

	private function __formatPurchaseData($imported_data, $import_fields, $group_by)
	{
		$formatted_array = [];
		$invoice_number_key = array_search("invoice_no", $import_fields);
		$supplier_name_key = array_search("supplier_name", $import_fields);
		$supplier_phone_key = array_search("supplier_phone_number", $import_fields);
		$supplier_email_key = array_search("supplier_email", $import_fields);
		$date_key = array_search("date", $import_fields);
		$product_key = array_search("product", $import_fields);
		$sku_key = array_search("sku", $import_fields);
		$quantity_key = array_search("quantity", $import_fields);
		$unit_price_key = array_search("unit_price", $import_fields);
		$item_tax_key = array_search("item_tax", $import_fields);
		$item_discount_key = array_search("item_discount", $import_fields);
		$item_description_key = array_search("item_description", $import_fields);
		$order_total_key = array_search("order_total", $import_fields);
		$unit_key = array_search("unit", $import_fields);
		$tos_key = array_search("types_of_service", $import_fields);
		$service_custom_field1_key = array_search("service_custom_field1", $import_fields);
		$service_custom_field2_key = array_search("service_custom_field2", $import_fields);
		$service_custom_field3_key = array_search("service_custom_field3", $import_fields);
		$service_custom_field4_key = array_search("service_custom_field4", $import_fields);

		$row_index = 2;
		foreach ($imported_data as $key => $value) {
			$formatted_array[$key]['invoice_no'] = $invoice_number_key !== false ? $value[$invoice_number_key] : null;
			$formatted_array[$key]['supplier_name'] = $supplier_name_key !== false ? $value[$supplier_name_key] : null;
			$formatted_array[$key]['supplier_phone_number'] = $supplier_phone_key !== false ? $value[$supplier_phone_key] : null;
			$formatted_array[$key]['supplier_email'] = $supplier_email_key !== false ? $value[$supplier_email_key] : null;
			$formatted_array[$key]['date'] = $date_key !== false ? $value[$date_key] : null;
			$formatted_array[$key]['product'] = $product_key !== false ? $value[$product_key] : null;
			$formatted_array[$key]['sku'] = $sku_key !== false ? $value[$sku_key] : null;
			$formatted_array[$key]['quantity'] = $quantity_key !== false ? $value[$quantity_key] : null;
			$formatted_array[$key]['unit_price'] = $unit_price_key !== false ? $value[$unit_price_key] : null;
			$formatted_array[$key]['item_tax'] = $item_tax_key !== false ? $value[$item_tax_key] : null;
			$formatted_array[$key]['item_discount'] = $item_discount_key !== false ? $value[$item_discount_key] : null;
			$formatted_array[$key]['item_description'] = $item_description_key !== false ? $value[$item_description_key] : null;
			$formatted_array[$key]['order_total'] = $order_total_key !== false ? $value[$order_total_key] : null;
			$formatted_array[$key]['unit'] = $unit_key !== false ? $value[$unit_key] : null;
			$formatted_array[$key]['types_of_service'] = $tos_key !== false ? $value[$tos_key] : null;
			$formatted_array[$key]['service_custom_field1'] = $service_custom_field1_key !== false ? $value[$service_custom_field1_key] : null;
			$formatted_array[$key]['service_custom_field2'] = $service_custom_field2_key !== false ? $value[$service_custom_field2_key] : null;
			$formatted_array[$key]['service_custom_field3'] = $service_custom_field3_key !== false ? $value[$service_custom_field3_key] : null;
			$formatted_array[$key]['service_custom_field4'] = $service_custom_field4_key !== false ? $value[$service_custom_field4_key] : null;
			$formatted_array[$key]['group_by'] = $value[$group_by];

			//check empty
			if (empty($formatted_array[$key]['supplier_phone_number']) && empty($formatted_array[$key]['supplier_email'])) {
				throw new \Exception(__('lang_v1.email_or_phone_cannot_be_empty_in_row', ['row' => $row_index]));
			}
			if (empty($formatted_array[$key]['product']) && empty($formatted_array[$key]['sku'])) {
				throw new \Exception(__('lang_v1.product_cannot_be_empty_in_row', ['row' => $row_index]));
			}
			if (empty($formatted_array[$key]['quantity'])) {
				throw new \Exception(__('lang_v1.quantity_cannot_be_empty_in_row', ['row' => $row_index]));
			}
			if (empty($formatted_array[$key]['unit_price'])) {
				throw new \Exception(__('lang_v1.unit_price_cannot_be_empty_in_row', ['row' => $row_index]));
			}

			$row_index++;
		}
		$group_by_key = $import_fields[$group_by];
		$formatted_data = [];
		foreach ($formatted_array as $array) {
			$formatted_data[$array['group_by']][] = $array;
		}

		return $formatted_data;
	}

	private function __importFields()
	{
		$fields =  [
			'invoice_no' => ['label' => __('sale.invoice_no')],
			'supplier_name' => ['label' => __('lang_v1.supplier_name')],
			'supplier_phone_number' => ['label' => __('lang_v1.supplier_phone_number'), 'instruction' => __('lang_v1.either_cust_email_or_phone_required')],
			'supplier_email' => ['label' => __('lang_v1.supplier_email'), 'instruction' => __('lang_v1.either_cust_email_or_phone_required')],
			'date' => ['label' => __('sale.sale_date'), 'instruction' => __('lang_v1.date_format_instruction')],
			'product' => ['label' => __('product.product_name'), 'instruction' => __('lang_v1.either_product_name_or_sku_required')],
			'sku' => ['label' => __('lang_v1.product_sku'), 'instruction' => __('lang_v1.either_product_name_or_sku_required')],
			'quantity' => ['label' => __('lang_v1.quantity'), 'instruction' => __('lang_v1.required')],
			'unit' => ['label' => __('lang_v1.product_unit')],
			'unit_price' => ['label' => __('sale.unit_price')],
			'item_tax' => ['label' => __('lang_v1.item_tax')],
			'item_discount' => ['label' => __('lang_v1.item_discount')],
			'item_description' => ['label' => __('lang_v1.item_description')],
			'order_total' => ['label' => __('lang_v1.order_total')],
		];

		$is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');

		if ($is_types_service_enabled) {
			$fields['types_of_service'] = ['label' => __('lang_v1.types_of_service')];
			$fields['service_custom_field1'] = ['label' => __('lang_v1.service_custom_field_1')];
			$fields['service_custom_field2'] = ['label' => __('lang_v1.service_custom_field_2')];
			$fields['service_custom_field3'] = ['label' => __('lang_v1.service_custom_field_3')];
			$fields['service_custom_field4'] = ['label' => __('lang_v1.service_custom_field_4')];
		}

		return $fields;
	}

	/**
	 * Deletes all sales from a batch
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function revertSaleImport($batch)
	{
		if (!auth()->user()->can('sell.delete')) {
			abort(403, 'Unauthorized action.');
		}

		try {
			$business_id = request()->session()->get('user.business_id');

			$sales = Transaction::where('business_id', $business_id)
				->where('type', 'sell')
				->where('import_batch', $batch)
				->get();
			//Begin transaction
			DB::beginTransaction();
			foreach ($sales as $sale) {
				$this->transactionUtil->deleteSale($business_id, $sale->id);
			}

			DB::commit();

			$output = ['success' => 1, 'msg' => __('lang_v1.import_reverted_successfully')];
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

			$output = [
				'success' => 0,
				'msg' => trans("messages.something_went_wrong")
			];
		}

		return redirect('import-purchases')->with('status', $output);
	}
}
