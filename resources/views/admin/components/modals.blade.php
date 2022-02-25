<!-- Modal -->
<div class="modal fade text-left" id="BankInfoModal" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="BankInfoModal"
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
<div class="modal fade text-left" id="ShippingInfoModal" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="ShippingInfoModal"
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
<div class="modal fade text-left" id="RatesModal" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="RatesModal"
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
<div class="modal fade text-left" id="ConfirmModal" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="ConfirmModal"
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
                    <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">
                        Yes
                    </button>
                    <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">
                        Cancel
                    </button>


                </form>
            </div>
        </div>
    </div>
</div>
<!--Confirm Modal -->
<div class="modal fade text-left" id="addCity" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="addCity"
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
<div class="modal fade text-left" id="addInternationalCity" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="addInternationalCity"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Add International City</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="addInternationalCityDiv">

            </div>
        </div>
    </div>
</div>

<!--Confirm Modal -->
<div class="modal fade text-left" id="editCity" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="editCity"
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


<div class="modal fade text-left" id="editFleet" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="editFleet"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Edit Fleet</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editFleetDiv">

            </div>
        </div>
    </div>
</div>


<div class="modal fade text-left" id="editRouteManagement" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="editRouteManagement"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Edit Route</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editRouteManagementDiv">

            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="editInternationalCity" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="editCity"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel8">Edit International City</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editInternationalCityDiv">

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
<div class="modal fade text-left" id="addRoute" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="addRoute"
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
<div class="modal fade text-left" id="editRoute" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="editRoute"
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
{{--<div class="modal fade text-left" id="ConfirmModalRoute" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ConfirmModalRoute"--}}
{{--aria-hidden="true">--}}
{{--<div class="modal-dialog modal-lg" role="document">--}}
{{--<div class="modal-content">--}}
{{--<div class="modal-header">--}}
{{--<h4 class="modal-title" id="myModalLabel8">Please Confirm</h4>--}}
{{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--<span aria-hidden="true">&times;</span>--}}
{{--</button>--}}
{{--</div>--}}
{{--<div class="modal-body routeConfirmation text-center">--}}
{{--<h3>Are you sure you want to perform this action?</h3>--}}
{{--<form action="{{route('admin.management.route.status')}}" method="post" class="mt-2">--}}
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
<div class="modal fade text-left" id="addRider" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="addRider"
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

<!--Profile Edit Model -->
<div class="modal fade text-left" id="editprofile" data-backdrop="static" data-keyboard="false" tabindex="-1"
     role="dialog" aria-labelledby="editprofile"
     aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="edit_profile_heading">Edit Profile<span></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editProfileDiv">

            </div>
        </div>
    </div>
</div>
<!--Profile Edit Model End -->

<!--Route Edit Model -->
<div class="modal fade text-left" id="editRider" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="editRider"
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
{{--<div class="modal fade text-left" id="ConfirmModalRider" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ConfirmModalRider"--}}
{{--aria-hidden="true">--}}
{{--<div class="modal-dialog modal-lg" role="document">--}}
{{--<div class="modal-content">--}}
{{--<div class="modal-header">--}}
{{--<h4 class="modal-title" id="myModalLabel8">Please Confirm</h4>--}}
{{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--<span aria-hidden="true">&times;</span>--}}
{{--</button>--}}
{{--</div>--}}
{{--<div class="modal-body riderConfirmation text-center">--}}
{{--<h3>Are you sure you want to perform this action?</h3>--}}
{{--<form action="{{route('admin.management.rider.status')}}" method="post" class="mt-2">--}}
{{--@csrf--}}
{{--@method('PUT')--}}
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
<!--Dispute Modal -->
<div class="modal fade text-left" id="UniversalDisputeModal" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="UniversalDisputeModal"
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
                            <select name="universal_city_select" id="universal_city_select" class="select2 form-control"
                                    style="width:100%;" data-rule-required="true"
                                    data-msg-required="This field is required">
                                <option></option>
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <select name="universal_dispute_type_select" id="universal_dispute_type_select"
                                    class="select2 form-control" style="width:100%;" data-rule-required="true"
                                    data-msg-required="This field is required">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2 justify-content-center" id="universal_tracking_div">
                        <div class="col-12 form-group">
                            <input name="universal_tracking_number" id="universal_tracking_number"
                                   class="tracking_number" data-tags-input-name="tracking_number"
                                   data-rule-required="true" data-msg-required="Tracking Number is required">
                        </div>
                    </div>
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 form-group">
                            <textarea name="universal_description" id="universal_description" class="form-control"
                                      cols="30" rows="3" placeholder="Enter Description" data-rule-required="true"
                                      data-msg-required="This field is required"></textarea>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="UniversalDisputeCreate" type="submit" class="btn btn-primary btn-block">Launch
                                Dispute
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Dispute Modal -->
<!--Shipment Charges Modal -->
<div class="modal fade text-left" id="ShipmentChargesModal" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="ShipmentChargesModal"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
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
<!--Password Change Modal -->
@if(Session::has('first_login') && session('first_login') != 1)
    <div class="modal fade text-left" id="FirstLoginPasswordChangeModal" data-backdrop="static" data-keyboard="false"
         tabindex="-1" role="dialog" aria-labelledby="FirstLoginPasswordChangeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-m" role="document">
            <div class="modal-content">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{session('error')}}
                    </div>
                @endif
                <div class="modal-header">
                    <h4 class="modal-title" id="shipment_charges_modal_heading">Change Your Password<span></span></h4>
                </div>
                <div class="modal-body password_change_body text-center" id="password_change_body">
                    <form id="password-form" class="form form-horizontal" method="post"
                          action="{{route('admin.update.profile.password.submit')}}">
                        @csrf
                        <div class="form-body">
                            <p>You have to change your password to make your sonic account more secure.</p>
                            <div class="form-group col">
                                <label for="password">Current Password:<span class="danger">*</span>
                                </label>
                                <div class="form-group position-relative">
                                    <input type="password" class="form-control required" id="current_password"
                                           placeholder="Minimum 6 Character" value="" name="current_password"
                                           data-rule-minlength="6"
                                           data-msg-minlength="Current Password needs to be at-least 6 Characters">
                                    <div class="form-control-position" id="first_peye">
                                        <i class="la la-eye success"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col">
                                <label for="password">Enter Password:<span class="danger">*</span>
                                </label>
                                <div class="form-group position-relative">
                                    <input type="password" class="form-control required" id="new_password"
                                           placeholder="Minimum 6 Character" value="" name="password"
                                           data-rule-minlength="6"
                                           data-msg-minlength="Password needs to be at-least 6 Characters">
                                    <div class="form-control-position" id="first_npeye">
                                        <i class="la la-eye success"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="password">Confirm Password:<span class="danger">*</span>
                                </label>
                                <div class="form-group position-relative">
                                    <input type="password" class="form-control required" id="confirm_password"
                                           placeholder="Minimum 6 Character" value="" name="confirm_password">
                                    <div class="form-control-position" id="first_cpeye">
                                        <i class="la la-eye success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions center">
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
<!--Password Change Modal -->


<div class="modal fade text-left" id="editCity" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="editCity"
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
{{-- edit territory --}}
<div class="modal fade" id="editterritory" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="editterritory"
     aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="add_remarks_title">Edit Territory</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body text-center" id="editterritoryDiv">

            </div>

        </div>
    </div>
</div>

{{-- edit designation --}}
<div class="modal fade" id="editdesignation" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="editdesignation"
     aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="add_remarks_title">Edit Designation</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body text-center" id="editdesignationDiv">

            </div>

        </div>
    </div>
</div>

{{-- edit retail user --}}
<div class="modal fade" id="editRetailUser" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="editRetailUser"
     aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="add_remarks_title">Edit User</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body text-center" id="editRetailUserDiv">

            </div>

        </div>
    </div>
</div>

<div class="modal fade text-left" id="LocationDeniedModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
     role="dialog" aria-labelledby="LocationDeniedModal"
     aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="shipment_charges_modal_heading">Location Denied!<span></span></h4>
            </div>
            <div class="modal-body password_change_body text-center" id="password_change_body">
                <form class="form form-horizontal">
                    <div class="form-body">
                        <p>You have to allow Location, cannot proceed without it.</p>
                    </div>
                    <div class="form-actions center">
                        <button type="button" class="btn btn-primary">Retry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="EditOneTimeProfileModal" data-backdrop="static" data-keyboard="false"
     tabindex="-1" role="dialog" aria-labelledby="EditOneTimeProfileModal"
     aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="edit_one_time_profile_modal_heading">Edit Your Profile<span></span></h4>
            </div>
            <div class="modal-body edit_one_time_profile_body text-center" id="edit_one_time_profile_body">
                <form class="form form-horizontal">
                    <div class="form-body">
                        <p>You have to update your profile, cannot proceed without it.</p>
                    </div>
                    <div class="form-actions center">
                        <button type="button" class="btn btn-primary">OK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="editIncidenceReport" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="editIncidenceReport"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title" id="myModalLabel8">Edit Report</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editIncidenceReportDiv">

            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="adminprofile" data-backdrop="static" data-keyboard="false" tabindex="-1"
     role="dialog" aria-labelledby="adminprofile"
     aria-hidden="true">

    <div class="modal-dialog modal-xl" role="document" style="margin-left: 35%!important;">
        <div class="modal-content" style="width: 60%!important;">
            <div class="modal-header">
                <h4 class="modal-title" id="admin_profile_heading">User Profile<span></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body admin_profile" id="admin_profile_body">
                <table class="table table-sm table-bordered border">
                    <thead>
{{--                    <tr>--}}
{{--                        <th>--}}
{{--                        <fieldset class="position-relative has-icon-left">--}}
{{--                            <i class="la la-user">--}}
{{--                            </i><label>Employ ID</label>--}}
{{--                        </fieldset>--}}
{{--                        </th>--}}
{{--                        <td id="full_name"></td>--}}
{{--                    </tr>--}}
                                        <tr>
                                            <th><span class="la la-user"></span><strong class="">Employee ID</strong></th>
                                            <td id="employee_id"></td>
                                        </tr>
                    <tr>
                        <th><span class="la la-user"></span><strong>Full Name</strong></th>
                        <td id="full_name"></td>
                    </tr>
                    <tr>
{{--                        <th>--}}
{{--                            <fieldset class="position-relative has-icon-left">--}}
{{--                                <i class="la la-envelope">--}}
{{--                                </i>$nbsp<label>Email</label>--}}
{{--                            </fieldset>--}}
{{--                        </th>--}}
                        <th><span class="la la-envelope"></span><strong>&nbsp;Email</strong></th>
                        <td id="email"></td>
                    </tr>
                    <tr>
                        <th><span class="la la-mobile-phone"></span><strong>Contact</strong></th>
                        <td id="contact"></td>
                    </tr>
                    <tr>
                        <th><span class="la la-th"></span><strong>&nbsp;Department</strong></th>
                        <td id="department"></td>
                    </tr>
                    <tr>
                        <th><span class="la la-user la la-align-left"></span><strong>Designation</strong></th>
                        <td id="designation"></td>
                    </tr>
                    <tr>
                        <th><span class="la la-tint"></span><strong>Blood Group</strong></th>
                        <td id="blood_group"></td>
                    </tr>
                    <tr>
                        <th><span class="la la-user"></span><strong>Emergency Contact Person</strong></th>
                        <td  id="emergency_contact_person"></td>
                    </tr>
                    <tr>
                        <th><span class="la la-mobile-phone"></span><strong>Emergency Contact Number</strong></th>
                        <td id="emergency_contact_no"></td>
                    </tr>


                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- new user Modal -->
{{--<div class="modal fade" id="adminprofile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">--}}
{{--    <div class="modal-dialog" role="document">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title" id="admin_profile_heading">Profile</h5>--}}
{{--                <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                    <span aria-hidden="true">&times;</span>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div class="modal-body admin_profile" id="admin_profile_body">--}}
{{--                <label id="employee_id"></label>--}}
{{--                <br>--}}
{{--                <label id="full_name"></label>--}}
{{--                <label id="email"></label>--}}
{{--            </div>--}}
{{--            <div class="modal-footer">--}}
{{--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
{{--                <button type="button" class="btn btn-primary">Save changes</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
