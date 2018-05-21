<!-- Modal -->
<div class="modal fade text-left" id="BankInfoModal" tabindex="-1" role="dialog" aria-labelledby="BankInfoModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white" id="myModalLabel8">Bank Information</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade text-left" id="ShippingInfoModal" tabindex="-1" role="dialog" aria-labelledby="ShippingInfoModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white" id="myModalLabel8">Shipping Information</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!--Rates Modal -->
<div class="modal fade text-left" id="RatesModal" tabindex="-1" role="dialog" aria-labelledby="RatesModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white" id="myModalLabel8">Shipper Rates</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!--Confirm Modal -->
<div class="modal fade text-left" id="ConfirmModal" tabindex="-1" role="dialog" aria-labelledby="ConfirmModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white" id="myModalLabel8">Please Confirm</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body confirmation text-center">
                <h3>Are you sure you want to perform this action?</h3>
                <form action="{{route('admin.account.status')}}" method="post" class="mt-2">
                    {{csrf_field()}}
                    <input type="hidden" name="shid" id="shid">
                    <input type="hidden" name="status" id="shstatus">
                    <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Yes</button>
                    <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>


                </form>
            </div>
        </div>
    </div>
</div>