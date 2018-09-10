<!-- Modal -->
<div class="modal fade text-left" id="BankInfoModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="BankInfoModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Bank Information</h4>
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
<div class="modal fade text-left" id="ShippingInfoModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ShippingInfoModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Shipping Information</h4>
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
<div class="modal fade text-left" id="RatesModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="RatesModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Shipper Rates</h4>
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
<div class="modal fade text-left" id="ConfirmModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ConfirmModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Please Confirm</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body confirmation text-center">
                <h3>Are you sure you want to perform this action?</h3>
                <form action="{{route('admin.accounts.status')}}" method="post" class="mt-2">
                    {{csrf_field()}}
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="shid" id="shid">
                    <input type="hidden" name="status" id="shstatus">
                    <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Yes</button>
                    <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>


                </form>
            </div>
        </div>
    </div>
</div>
<!--Confirm Modal -->
<div class="modal fade text-left" id="addCity" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addCity"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Add City</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="addCityDiv">

            </div>
        </div>
    </div>
</div>

<!--Confirm Modal -->
<div class="modal fade text-left" id="editCity" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editCity"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Edit City</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editCityDiv">

            </div>
        </div>
    </div>
</div>

<!--Confirm Modal City-->
{{--<div class="modal fade text-left" id="ConfirmModalCity" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ConfirmModalCity"--}}
     {{--aria-hidden="true">--}}
    {{--<div class="modal-dialog modal-lg" role="document">--}}
        {{--<div class="modal-content">--}}
            {{--<div class="modal-header">--}}
                {{--<h4 class="modal-title" id="myModalLabel8">Please Confirm</h4>--}}
                {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                    {{--<span aria-hidden="true">&times;</span>--}}
                {{--</button>--}}
            {{--</div>--}}
            {{--<div class="modal-body confirmation text-center">--}}
                {{--<h3>Are you sure you want to perform this action?</h3>--}}
                {{--<form action="{{route('admin.management.city.status')}}" method="post" class="mt-2">--}}
                    {{--{{csrf_field()}}--}}
                    {{--<input type="hidden" name="_method" value="PUT">--}}
                    {{--<input type="hidden" name="cid" id="cid">--}}
                    {{--<input type="hidden" name="status" id="cstatus">--}}
                    {{--<button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Yes</button>--}}
                    {{--<button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>--}}


                {{--</form>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
<!--Confirm Modal City-->
<!--Route Add Model -->
<div class="modal fade text-left" id="addRoute" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addRoute"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Add Route</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="addRouteDiv">

            </div>
        </div>
    </div>
</div>
<!--Route Add Model end-->
<!--Route edit Model -->
<div class="modal fade text-left" id="editRoute" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editRoute"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Edit Route</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editRouteDiv">

            </div>
        </div>
    </div>
</div>
<!--Route edit Model end-->
<!--Confirm Modal City-->
<div class="modal fade text-left" id="ConfirmModalRoute" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ConfirmModalRoute"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Please Confirm</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body routeConfirmation text-center">
                <h3>Are you sure you want to perform this action?</h3>
                <form action="{{route('admin.management.route.status')}}" method="post" class="mt-2">
                    {{csrf_field()}}
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="cid" id="cid">
                    <input type="hidden" name="status" id="cstatus">
                    <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Yes</button>
                    <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>


                </form>
            </div>
        </div>
    </div>
</div>
<!--Confirm Modal City-->
<!--Route Add Model -->
<div class="modal fade text-left" id="addRider" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addRider"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Add Rider</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="addRiderDiv">

            </div>
        </div>
    </div>
</div>
<!--Route Add Model end-->
<!--Route Edit Model -->
<div class="modal fade text-left" id="editRider" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editRider"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Edit Rider</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editRiderDiv">

            </div>
        </div>
    </div>
</div>
<!--Route Edit Model end-->
<!--Confirm Modal City-->
<div class="modal fade text-left" id="ConfirmModalRider" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ConfirmModalRider"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Please Confirm</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body riderConfirmation text-center">
                <h3>Are you sure you want to perform this action?</h3>
                <form action="{{route('admin.management.rider.status')}}" method="post" class="mt-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="cid" id="cid">
                    <input type="hidden" name="status" id="cstatus">
                    <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Yes</button>
                    <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>


                </form>
            </div>
        </div>
    </div>
</div>
<!--Confirm Modal City-->
<!--Dispute Modal -->
<div class="modal fade text-left" id="UniversalDisputeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="UniversalDisputeModal"
     aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Launch Dispute</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body  text-center">
                <form id="universal_dispute_form" action="" method="post">
                    <input type="hidden" id="universal_dispute_id" name="universal_dispute_id">
                    <div class="row mb-2">
                        <div class="col-12 form-group">
                            <select name="universal_city_select" id="universal_city_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                <option></option>
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <select name="universal_dispute_type_select" id="universal_dispute_type_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2 justify-content-center" id="universal_tracking_div">
                        <div class="col-12 form-group">
                            <input name="universal_tracking_number" id="universal_tracking_number" class="tracking_number" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                        </div>
                    </div>
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <textarea name="universal_description" id="universal_description" class="form-control" cols="30" rows="3" placeholder="Enter Description" data-rule-required="true" data-msg-required="This field is required"></textarea>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="UniversalDisputeCreate" type="submit" class="btn btn-primary btn-block">Launch Dispute</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Dispute Modal -->
<!--Shipment Charges Modal -->
<div class="modal fade text-left" id="ShipmentChargesModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ShipmentChargesModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="shipment_charges_modal_heading">Shipment Charges of #<span></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input type="hidden" id="shipment_charges_modal_id">
            <div class="modal-body shipment_charges_body text-center" id="shipment_charges_body">

            </div>
        </div>
    </div>
</div>
<!--Shipment Charges Modal -->