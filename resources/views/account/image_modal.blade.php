<style>
    .modal-dialog {
        width: 65% !important;
    }

    #modal_image {
        max-height: 510px;
        max-width: 800px;
        min-height: 500px;
    }

    .zoom {
        padding: 50px;
        transition: transform .2s;
        /* Animation */
        margin: 0 auto;
    }

    .zoom:hover {
        transform: scale(1.3);
        /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
    }


    @media only screen and (max-width: 600px) and (min-width:300px) {
        .modal-dialog {
            width: 90% !important;
            margin-left: 5% !important;
        }

        #modal_image {
            max-height: 510px;
            max-width: 275px;
            min-height: 300px;
        }
    }
</style>
<div class="modal-dialog" role="document">
    <div class="modal-content print">
        <div class="modal-header">
            <h5 class="modal-title">{{$title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" style="text-align:center; height: 550px">
            <div class="col-md-12" style="text-align:center; ">
                <span id='ex4'>
                    <img class='zoom'  src="{{$url}}" alt="{{$title}}" id="modal_image">
                </span>

            </div>
        </div>
    </div>
</div>