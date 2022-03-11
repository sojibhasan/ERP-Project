<style>
    .select2-search--dropdown.select2-search--hide{
        display: block;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.bootstrap4.css" />
<script src="{{ asset('js/selectize.min.js') }}"></script>

<div class="modal-dialog" role="document" style="width: 90%;">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang( 'property::lang.blocks_details' )</h4>
        </div>

        <div class="modal-body">
            <table class="table table-bordered table-striped" id="block_list_table">
                <thead>
                    <tr>

                        <th>Action</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Expense Ref No.</th>
                        <th>Expense Category</th>
                        <th>Expense for Contact</th>
                        <th>Total Amount</th>
                        <th>Expense Account </th>
                        <th width="10%">Expenses Note</th>
                        <th>Paid Amount</th>
            
                    </tr>
                </thead>
                <tbody>
                    @php
                        // dd($data->add_expense_data);
                        $item = json_decode($data->add_expense_data); 
                        // dd($dat);
                    @endphp
                    {{-- @foreach ($dat as $item) --}}
                    @if($item != null)
                        @php
                            $location = App\BusinessLocation::find($item->location_id); 
                            $cat = App\ExpenseCategory::find($item->expense_category_id);             
                            $con = App\Contact::find($item->contact_id);             
                            $acc = App\AccountType::find($item->expense_account);             
                        @endphp
            
                    <tr>
                        <td>
                            <a href="{{action('\Modules\Property\Http\Controllers\ExpenseController@edit', [$data->id])}}"><i class="fa fa-edit"></i></a>
                        </td>
                        <td>{{$item->transaction_date}}</td>
                        <td>{{$location->name}}</td>
                        <td>{{$item->ref_no}}</td>
                        <td>
                            @if($cat!= null) 
                                {{$cat->name}} 
                            @endif
                        </td>
                        <td>
                            @if($con!= null) 
                                {{$con->name}} 
                            @endif
                        </td>
                        <td>{{$item->final_total}}</td>

                        <td>
                            @if($acc!= null) 
                                {{$acc->name}} 
                            @endif
                        </td>
                        <td>{{$item->additional_notes}}</td>
                        <td>{{$item->final_total}}</td>
                    </tr>
                    @endif
            
                    {{-- @endforeach --}}
                </tbody>
            </table>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default add_block_btn" data-dismiss="modal">@lang( 'messages.close'
                )</button>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>

    $(document).ready(function(){

        $('#block_list_table').DataTable();
        // $('#block_customer_id, #block_block_id, #block_user_id').selectize();
    });
    
    $('.search-bloack-details').keyup(function(){
        get_block_list();
    })
    
    $('#block_list_table').DataTable();
</script>