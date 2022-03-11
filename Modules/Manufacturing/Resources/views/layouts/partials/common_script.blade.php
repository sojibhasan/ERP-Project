<script type="text/javascript">
	$(document).ready( function () {

		//Purchase table
	    productions_table = $('#productions_table').DataTable({
	        processing: true,
	        serverSide: true,
	        aaSorting: [[0, 'desc']],
	        ajax: {
	            url: '{{action("\Modules\Manufacturing\Http\Controllers\ProductionController@index")}}',
	            data: function(d) {
	                
	            },
	        },
	        columnDefs: [
	            {
	                targets: [6],
	                orderable: false,
	                searchable: false,
	            },
	        ],
	        columns: [
	            { data: 'transaction_date', name: 'transaction_date' },
	            { data: 'ref_no', name: 'ref_no' },
	            { data: 'location_name', name: 'bl.name' },
	            { data: 'product_name', name: 'product_name' },
	            { data: 'quantity', searchable: false },
	            { data: 'final_total', name: 'final_total' },
	            { data: 'action', name: 'action' },
	        ],
	        fnDrawCallback: function(oSettings) {
	            __currency_convert_recursively($('#productions_table'));
	        }
	    });

	    if ($('textarea#instructions').length > 0) {
	        CKEDITOR.config.height = 120;
	        CKEDITOR.replace('instructions');
	    }

		if ($('#search_product').length) {
			initialize_search($('#search_product'));
	    }
	    if ($('.search_product').length) {
	    	$('.search_product').each( function(){
	    		initialize_search($(this));
	    	});
	    }

		if ($('#search_product_by_product').length) {
		    $('#search_product_by_product').autocomplete({
	            source: function(request, response) {
	                $.getJSON(
	                    '/products/list',
	                    {
	                        term: request.term,
	                        product_types: ['single', 'variable']
	                    },
	                    response
	                );
	            },
	            minLength: 2,
	            response: function(event, ui) {
	                if (ui.content.length == 0) {
	                    toastr.error(LANG.no_products_found);
	                    $('input#search_product_by_product').select();
	                }
	            },
	            select: function(event, ui) {
	                addByProductRow(ui.item.variation_id);
	            },
	        }).autocomplete('instance')._renderItem = function(ul, item) {
		        var string = '<li>' + item.name;
	            if (item.type == 'variable') {
	                string += '-' + item.variation;
	            }
	            string +=
	                ' (' +
	                item.sub_sku +
	                ')' +
	                '</li>';
	            return $(string).appendTo(ul);
	        }
	    }

    	recipe_table = $('#recipe_table').DataTable({
	        processing: true,
	        serverSide: true,
	        ajax: '{{action("\Modules\Manufacturing\Http\Controllers\RecipeController@index")}}',
	        columnDefs: [
	            {
	                targets: [0, 5, 6, 7],
	                orderable: false,
	                searchable: false,
	            },
	        ],
	        "order": [[ 1, "desc" ]],
	        columns: [
	        	{ data: 'row_select' },
	            { data: 'recipe_name', name: 'recipe_name' },
	            { data: 'category', name: 'c.name' },
	            { data: 'sub_category', name: 'sc.name' },
	            { data: 'total_quantity', name: 'total_quantity' },
	            { data: 'recipe_total' },
	            { data: 'unit_cost' },
	            { data: 'action', name: 'action' },
	        ],
	        fnDrawCallback: function(oSettings) {
	            // __currency_convert_recursively($('#recipe_table'));
	        },
	    });
	});

	$(document).on('shown.bs.modal', '#recipe_modal', function(){
		initSelect2($(this).find('#variation_id'), $('#recipe_modal'));
        $(this).find('#copy_recipe_id').select2();
	});

	$(document).on('shown.bs.modal', '.view_modal', function(){
		__currency_convert_recursively($('.view_modal'));
	});

	$(document).on('change', '.quantity, .row_sub_unit_id, #total_quantity, #extra_cost, #sub_unit_id', function(){
		calculateRecipeTotal();
	});

    function addIngredientRow(variation_id, search_element) {
    	var row_index = parseInt($('#row_index').val());
    	var ingredient_group = search_element.closest('.box').find('.ingredient_group');
        var row_ig_index = ingredient_group.length ? ingredient_group.data('ig_index') : '';

    	$.ajax({
            url: "/manufacturing/get-ingredient-row/" + variation_id + '?row_index=' + row_index + '&row_ig_index=' + row_ig_index,
            dataType: 'html',
            success: function(result) {
                search_element.closest('.box').find('table.ingredients_table tbody').append(result);
                calculateRecipeTotal();
                row_index++;
                $('#row_index').val(row_index);
            },
        });
    }

	function addByProductRow(variation_id) {
		var count = 0;
    	$('table#by_product_table tbody tr').each( function(){
    		var el = $(this).find('input.ingredient_id');
    		if (el.val() == variation_id) {
    			count++;
    			var qty_el = $(this).find('input.quantity');
    			var quantity = __read_number(qty_el);
    			quantity++;
    			__write_number(qty_el, quantity);
    			// calculateRecipeTotal();
    		}
    	});

    	if (count == 0) {
	    	$.ajax({
	            url: "/manufacturing/get-by-product-row/" + variation_id,
	            dataType: 'html',
	            success: function(result) {
	                $('table#by_product_table tbody').append(result);
	                // calculateRecipeTotal();
	            },
	        });
	    }
    }

    function calculateRecipeTotal() {
    	var total = 0;
    	$('.ingredients_table tbody tr').each( function() {
    		var line_unit_price = $(this).find('.ingredient_price').val();
    		var quantity = __read_number($(this).find('.quantity'));
    		var multiplier = 1;
    		if ($(this).find('.row_sub_unit_id').length) {
    			multiplier = parseFloat(
		            $(this).find('.row_sub_unit_id')
		                .find(':selected')
		                .data('multiplier')
		        	);
    		}

    		var line_total = line_unit_price * quantity * multiplier;
    		$(this).find('span.ingredient_price').text(__currency_trans_from_en(line_total, true));
    		total += line_total;
    	});
    	$('span#ingredients_cost_text').text(__currency_trans_from_en(total, true));
    	$('#ingredients_cost').val(total);
    	var production_cost_percent = __read_number($('#extra_cost'));
    	var production_cost = __calculate_amount('percentage', production_cost_percent, total);
		total += production_cost;
    	__write_number($('#total'), total);
    }

	function initSelect2(element, dropdownParent = $('body')) {
		element.select2({
	        ajax: {
	            url: '/products/list',
	            dataType: 'json',
	            delay: 250,
	            data: function(params) {
	                return {
	                    term: params.term, // search term
	                };
	            },
	            processResults: function(data) {
	            	return {
			            results: $.map(data, function (value, key) {
			            	var name = value.type == 'variable' ? value.name + ' - ' + value.variation : value.name;
			            	name += ' (' + value.sub_sku + ')';
			                return {
			                    id: value.variation_id,
			                    text: name
			                }
			            })
			        };
	            },
	        },
	        minimumInputLength: 1,
	        escapeMarkup: function(markup) {
	            return markup;
	        },
	        dropdownParent: dropdownParent
	    });
	}

	$(document).on('click', 'button.remove_ingredient', function() {
		$(this).closest('tr').remove();
		calculateRecipeTotal();
	});
	$(document).on('submit', '#recipe_form', function (e) {
		var ingredients_length = $('.ingredients_table tbody .quantity').length;
		if (ingredients_length < 1) {
			toastr.error('@lang("manufacturing::lang.please_add_ingredients")');
			e.preventDefault();
			return false;
		}
	});

	$(document).on('click', 'button#add_ingredient_group', function() {
		var ig_index = parseInt($('#ig_index').val());
    	$.ajax({
            url: "/manufacturing/ingredient-group-form" + '?ig_index=' + ig_index,
            dataType: 'html',
            success: function(result) {
            	var el = $(result);
                $('#box_group').append(el);
                initialize_search(el.find('.search_product'));
                el.find('.ingredient_group').focus();
                ig_index++;
                $('#ig_index').val(ig_index);
            },
        });
	});

	function initialize_search(element) {
		element.autocomplete({
            source: function(request, response) {
                $.getJSON(
                    '/products/list',
                    {
                        term: request.term,
                        product_types: ['single', 'variable']
                    },
                    response
                );
            },
            minLength: 2,
            response: function(event, ui) {
                if (ui.content.length == 0) {
                    toastr.error(LANG.no_products_found);
                    $('input#search_product').select();
                }
            },
            select: function(event, ui) {
                addIngredientRow(ui.item.variation_id, $(this));
            },
        }).autocomplete('instance')._renderItem = function(ul, item) {
	        var string = '<li>' + item.name;
            if (item.type == 'variable') {
                string += '-' + item.variation;
            }
            string +=
                ' (' +
                item.sub_sku +
                ')' +
                '</li>';
            return $(string).appendTo(ul);
        }
	}
	$(document).on('click', 'button.remove_ingredient_group', function() {
	$(this).closest('.box').remove();
	calculateRecipeTotal();
});

$(document).on('click', '#mass_update_product_price', function(e){
    e.preventDefault();
    var selected_rows = [];
    var unit_prices = [];
    var i = 0;
    $('.row-select:checked').each(function () {
    	var recipe_id = $(this).val();
        selected_rows[i++] = recipe_id;
        unit_prices[recipe_id] = $(this).closest('tr').find('span.unit_cost').data('unit_cost');
    });
    
    if(selected_rows.length > 0){
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var data = {
                	recipe_ids: selected_rows,
                	unit_prices: unit_prices
                }
                $.ajax({
                    method: "post",
                    url: "/manufacturing/update-product-prices",
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            recipe_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    } else{
        swal('@lang("lang_v1.no_row_selected")');
    }    
});
$(document).on('click', 'button.delete_recipe', function() {
    swal({
        title: LANG.sure,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(willDelete => {
        if (willDelete) {
            var href = $(this).data('href');
            var data = $(this).serialize();
            $.ajax({
                method: 'DELETE',
                url: href,
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        recipe_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        }
    });
});

$(document).on('click', '.delete-production', function(e) {
	e.preventDefault();
    swal({
        title: LANG.sure,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(willDelete => {
        if (willDelete) {
            var href = $(this).data('href');
            var data = $(this).serialize();
            $.ajax({
                method: 'DELETE',
                url: href,
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        productions_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        }
    });
});

$(document).on('click', 'button.remove_by_product', function() {
		$(this).closest('tr').remove();
		calculateRecipeTotal();
	});
	

$(document).on('change', '#choose_product_form #variation_id', function() {
    var variation_id = $(this).val();
    if (variation_id) {
        $.ajax({
            method: 'get',
            url: "/manufacturing/is-recipe-exist/" + variation_id,
            dataType: 'json',
            success: function(result) {
                if (result == 1) {
                    $('#choose_product_form #recipe_selection').addClass('hide');
                } else {
                    $('#choose_product_form #recipe_selection').removeClass('hide');
					$('#choose_product_form').submit();
                }
            },
        });
    } else {
        $('#choose_product_form #recipe_selection').removeClass('hide');
    }
})
</script>