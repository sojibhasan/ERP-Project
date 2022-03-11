<style>
    .select2-search--dropdown.select2-search--hide{
        display: block;
    }
</style>

<div class="modal-dialog" role="document" style="width: 45%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Property\Http\Controllers\PropertyBlocksController@updateBlocks'),
        'method'
        =>
        'post', 'id' => 'blocks_edit_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'property::lang.edit_blocks' )</h4>
        </div>

        <div class="modal-body">
            
            <div class="row">

                <input type="hidden" name="property_id" value="{{$property->id}}">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4 align-middle">
                            <div class="form-group">
                                <label>Block No:</label>
                            </div>
                        </div>
                        <div class="col-md-8 align-middle">
                            <div class="form-group">
                                <select class="select2 form-control" id="block_no" style="width: 100%">
                                    <option value="">All</option>
                                    <?php
                                        for($i=0; $i<count($property_blocks); $i++){
                                        ?>
                                            <option value="<?php echo $property_blocks[$i]['block_number'] ?>"><?php echo $property_blocks[$i]['block_number'] ?></option>
                                        <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="clearfix"></div>
                
            </div>
            
            <div class="row">
                <div class="col-md-12 repeat_field" style="margin-top: 10px;">
                    <table class="table table-bordered table-striped" id="block_list_table">
                        <thead>
                            <tr>
                                <th>@lang('property::lang.block_number')</th>
                                <th>@lang('property::lang.block_extent')</th>
                                <th>@lang('property::lang.units')</th>
                                <th>@lang('property::lang.block_sale_price')</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i=0;
                            @endphp
                            @foreach ($property_blocks as $item)
                            {!! Form::hidden('blocks['.$i.'][block_id]', $item->id, []) !!}
                            <tr data-block-no={{ $item->block_number }}>
                                <td>
                                    {!! Form::text('blocks['.$i.'][block_number]', $item->block_number, ['class' =>
                                    'form-control',
                                    'placeholder' =>
                                    __(
                                    'property::lang.block_number' ), 'required']); !!}

                                </td>
                                <td>
                                    {!! Form::text('blocks['.$i.'][block_extent]', @format_quantity($item->block_extent), ['class' =>
                                    'form-control',
                                    'placeholder' =>
                                    __(
                                    'property::lang.block_extent' ), 'required']); !!}

                                </td>
                                <td>
                                    {!! Form::select('blocks['.$i.'][unit_id]', $units, $item->unit_id, ['class' =>
                                    'form-control
                                    select2',
                                    'style' =>
                                    'width:100%',
                                    'placeholder' => __('lang_v1.please_select')]); !!}
                                </td>
                                <td>
                                    {!! Form::text('blocks['.$i.'][block_sale_price]', @num_format($item->block_sale_price), ['class' =>
                                    'form-control', 'placeholder'
                                    => __(
                                    'property::lang.block_sale_price' ), 'required']); !!}

                                </td>
                            </tr>
                            @php
                            $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default add_block_btn" data-dismiss="modal">@lang( 'messages.close'
                )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(document).ready(function(){
        $('#block_no').select2({
          placeholder: 'All',
          allowClear: true
        });
        
        $('#block_no').change(function(){
            let block_no = $(this).val();
            if(block_no.length){
                $('#block_list_table tr').hide();
                $('#block_list_table tr').each(function(){
                    $tr = $(this);
                    
                    if($tr.data('block-no') == block_no){
                        $tr.show();
                    }
                    
                })
            }
            else{
                $('#block_list_table tr').show();
            }
        })
        
    });
    
    
</script>