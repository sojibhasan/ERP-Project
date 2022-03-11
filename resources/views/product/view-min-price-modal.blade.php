<div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">{{$product->name}}</h4>
            </div>
            <div class="modal-body">
                  <div class="row">
                      <div class="col-sm-12">
                          <div class="col-md-offset-3 col-sm-6 ">
                            <form id="min_sell_price" action="{{action('ProductController@minSellPriceUpdate')}}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="min_sell_price">Minimum Sell Price</label>
                                    <input class="form-control" type="number" name="min_sell_price" value="{{$product->min_sell_price}}">
                                    <input class="form-control" type="hidden" name="id" value="{{$product->id}}">
                                </div>
                            </form>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn-primary form_button">
                <i class="fa fa-save"></i> Save
              </button>
                  <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
        </div>
    </div>

<script>
    $('.form_button').click(function(){
        $('#min_sell_price').submit();
    });

</script>

    