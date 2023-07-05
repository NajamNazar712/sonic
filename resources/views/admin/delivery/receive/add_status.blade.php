
@extends('admin.layout.master')
@section('title','Receive Deliveries')
@section('content')
    <h1 class="mb-1">
        Receive Deliveries(Delivery Note: {{str_pad($delivery_note_id, 6, '0', STR_PAD_LEFT)}})
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="status_update_form" action="{{route('admin.delivery.receive.add.status')}}" method="post">
                    @csrf
                    <input type="hidden" value="{{$delivery_note_id}}" id="delivery_note" name="delivery_note_id">
                    <input type="hidden" value="{{$shipments_count}}" id="shipments_count" name="shipments_count">
                    <input type="hidden" name="shipment_ids" id="shipment_ids">
                    <input type="hidden" name="open_box_ids" id="open_box_ids">
                    <input type="hidden" name="password" id="password">
                    <div class="row justify-content-center">
                        <div class="col-12 mb-2">
                            <h3>Delivery Ratio {{$percentage}}%</h3>
                        </div>
                        <div class="col-4">
                            <fieldset class="form-group">
                                <select name="select_all_status" id="select_all_status" class="form-control select2">
                                    @foreach($shipment_statuses as $status)
                                        <option value="{{$status->id}}">{{$status->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-4">
                            <fieldset class="form-group">
                                <select name="select_all_reason" id="select_all_reason" class="form-control select2">
                                </select>
                            </fieldset>
                        </div>
                        @if(!$delivery_note_status == 1)
                            <div class="col-4">
                                <button type="button" id="submit_selected_status" disabled class="mr-1 mb-1 btn btn-primary btn-min-width"><i class="la la-list-alt"></i> Bulk Update </button>
                            </div>
                        @endif
                    </div>

                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1"></th>
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Shipment ID</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Consignee</th>
                            <th class="border-primary border-darken-1">Collection Amount</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Reason</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">Received/Refused By</th>
                            <th class="border-primary border-darken-1" width="250">CNIC</th>
                            <th class="border-primary border-darken-1">Relation</th>
                            <th class="border-primary border-darken-1">Address</th>
                            <th class="border-primary border-darken-1">Attempts Count</th>
                            <th class="border-primary border-darken-1">Open Box</th>
                            <th class="border-primary border-darken-1">Rider Status</th>
                            <th class="border-primary border-darken-1">Rider Reason</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Current Status</th>
                            <th class="border-primary border-darken-1">Service Type</th>
							<th class="border-primary border-darken-1">Consolidation</th>
                            <th class="border-primary border-darken-1">Consolidated IDs</th>
                            <th class="border-primary border-darken-1">CCD Slip</th>
                            <th class="border-primary border-darken-1">Clear</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="row justify-content-center">
                        @if($delivery_note_status == 0)
                            <div class="mr-1">
                                <button id="statusSubmit" type="submit" disabled class="btn btn-primary btn-block">Update Status</button>
                            </div>
                        @endif
                        @if($delivery_note_status == 1)
                            <div class="mr-1">
                                <button id="printDNCC" type="button" class="btn btn-warning btn-block">Print DNCC</button>
                            </div>
                        @else
                            @if($undelivered_printed == 1)
                                <div class="mr-1">
                                    <button id="printTempDNCC" type="button" class="btn btn-warning btn-block">Print Temporary DNCC</button>
                                </div>
                            @endif
                        @endif
                        @if($shipment_update == 1)
                            <div class="mr-1 ml-1">
                                <button id="printUndeliveredDNCC" type="button" class="btn btn-warning btn-block">Print Undelivered Performa</button>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="upload_pod_modal" data-backdrop="static" role="dialog" aria-labelledby="upload_pod_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form id="upload_pod_form" method="post" action="{{route('admin.delivery.receive.upload_pod')}}" enctype="multipart/form-data">
                    @method('POST')
                        @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="bookings_modal_title">Upload POD</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                        
                        <input type="hidden" name="shipment_id" id="pod_shipment" >
                        <div class="form-group">
                            <label for="pod_file">
                                POD File: 
                            </label><br>
                            <input class="form-control form-control-sm" type="file" name="pod_file" id="pod_file" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                        </div>
                    

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!--Replacement Modal -->
    <div class="modal fade text-left" id="ReplacementModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ReplacementModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Replacement Shipment(s) Weight</h4>
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                    {{--<span aria-hidden="true">&times;</span>--}}
                    {{--</button>--}}
                </div>
                <div class="modal-body  text-center">
                    <form id="replacement_form" action="{{route('admin.delivery.receive.replacements.submit')}}" method="post">
                        <table class="table table-bordered datatable" id="replacementtable" style="z-index: 3;">
                            <thead>
                            @csrf
                            @method('PUT')
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Tracking No.</th>
                                <th class="border-primary border-darken-1">Service Type</th>
                                <th class="border-primary border-darken-1">Weight Of Shipment</th>

                            </tr>
                            </thead>
                        </table>

                        <input type="hidden" name="shipment_id_list" id="shipment_id_list">
                        <input type="hidden" name="delivery_note_id" value="{{$delivery_note_id}}">

                        <div class="row justify-content-center">
                            <div class="col-12">
                                <button id="ReplacementUpdate" type="submit" class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Replacement Modal -->
    <!--Try&Buy Modal -->
    <div class="modal fade text-left" id="TryBuyModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="TryBuyModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Try &amp; Buy Delivery</h4>
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                    {{--<span aria-hidden="true">&times;</span>--}}
                    {{--</button>--}}
                </div>
                <div class="modal-body  text-center">
                    <div class="row justify-content-center">
                        <div class="col-4">
                            <form id="items_scan_form" action="#">
                                <div class="form-group">
                                    <input type="text" name="item_number" class="form-control item_number" placeholder="Shipment Item Number Scan*" data-rule-required="true" data-msg-required="Item Number is required">
                                </div>

                            </form>
                        </div>
                    </div>

                    <form id="trybuy_form" action="{{route('admin.delivery.receive.trybuys.submit')}}" method="post">
                        <table class="table table-bordered datatable" id="trybuytable" style="z-index: 3;">
                            <thead>
                            @csrf
                            @method('PUT')
                            <tr role="row" class="bg-primary white">

                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Product Type</th>
                                <th class="border-primary border-darken-1">Product Description</th>
                                <th class="border-primary border-darken-1">Item Price</th>
                                <th class="border-primary border-darken-1">Receiving</th>

                            </tr>
                            </thead>
                        </table>
                        <div class="row justify-content-center mb-2">
                            <div class="col">
                                <h4><U>Total Collection Amount:</U> Rs: <span id="cod"></span></h4>
                            </div>
                        </div>
                        <input type="hidden" name="trybuy_id_list" id="trybuy_id_list">
                        <input type="hidden" name="trybuy_cod" id="trybuy_cod">
                        <input type="hidden" name="item_checked" id="item_checked">
                        <input type="hidden" name="item_unchecked" id="item_unchecked">
                        <input type="hidden" name="delivery_note_trybuy" id="delivery_note_trybuy">
                        <input type="hidden" name="trybuy_shipment_id" id="trybuy_shipment_id">
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <button id="TrybuyUpdate" type="submit" class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Try&Buy Modal -->
    <!--Non Service Modal -->
    <div class="modal fade text-left" id="NonServiceModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="NonServiceModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Out of Service Area Shipment(s) Charges</h4>
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                    {{--<span aria-hidden="true">&times;</span>--}}
                    {{--</button>--}}
                </div>
                <div class="modal-body  text-center">
                    <form id="nsa_form" action="{{route('admin.delivery.receive.nsa_shipments.submit')}}" method="post">
                        <table class="table table-bordered datatable" id="nsatable" style="z-index: 3;">
                            <thead>
                            @csrf
                            @method('PUT')
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Tracking No.</th>
                                <th class="border-primary border-darken-1">Consignee Address</th>
                                <th class="border-primary border-darken-1">Destination</th>
                                <th class="border-primary border-darken-1">Shipper Name</th>
                                <th class="border-primary border-darken-1">Estimated Charges</th>
                                <th class="border-primary border-darken-1">Remarks</th>

                            </tr>
                            </thead>
                        </table>

                        <input type="hidden" name="nsa_shipment_ids" id="nsa_shipment_ids">
                        <input type="hidden" name="delivery_note_id" value="{{$delivery_note_id}}">

                        <div class="row justify-content-center">
                            <div class="col-12">
                                <button id="NsaUpdate" type="submit" class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Non Service Modal -->
	<div class="modal fade text-left" id="DateModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DateModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Shipment(s) Receiving Date</h4>
                </div>
                <div class="modal-body  text-center">

                        <div class="row justify-content-center">
                            <div class="col-8" id="receiving_date_div">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                      </span>
                                    </div>
                                    <input type="text" name="receiving_date"
                                           class="form-control bg-primary border-primary white rounded-right"
                                           id="receiving_date" placeholder="Receiving Date">
                                </div>
                            </div>

                        </div>
                        <input type="hidden" name="date_shipment_id" id="date_shipment_id">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <button id="DateUpdate" type="button" class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>
    <!--Incomplete Address Modal -->
    <div class="modal fade text-left" id="IncompleteAddressModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="IncompleteAddressModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Incomplete Address Details</h4>

                </div>
                <div class="modal-body  text-center">

                    <input type="hidden" name="iad_shipment_id" id="iad_shipment_id">
                    <input type="hidden" name="iad_status" id="iad_status">
                    <div class="row justify-content-center mb-2">
                        <div class="col-9 text-left">
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input iad_radio" name="customRadio" id="customRadio1" status="Need House No.">
                                    <label class="custom-control-label" for="customRadio1">Need House No.</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input iad_radio" name="customRadio" id="customRadio2" status="Need Plot No.">
                                    <label class="custom-control-label" for="customRadio2">Need Plot No.</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input iad_radio" name="customRadio" id="customRadio3" status="Need Area Name">
                                    <label class="custom-control-label" for="customRadio3">Need Area Name</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input iad_radio" name="customRadio" id="customRadio4" status="Need Street No.">
                                    <label class="custom-control-label" for="customRadio4">Need Street No.</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input iad_radio" name="customRadio" id="customRadio5" status="Need Street Name">
                                    <label class="custom-control-label" for="customRadio5">Need Street Name</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input iad_radio" name="customRadio" id="customRadio6" status="Need Sector No.">
                                    <label class="custom-control-label" for="customRadio6">Need Sector No.</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input iad_radio" name="customRadio" id="customRadio7" status="Need Floor No.">
                                    <label class="custom-control-label" for="customRadio7">Need Floor No.</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input iad_radio" name="customRadio" id="customRadio8" status="Need Office No.">
                                    <label class="custom-control-label" for="customRadio8">Need Office No.</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input iad_radio" name="customRadio" id="customRadio9" status="Need Building No.">
                                    <label class="custom-control-label" for="customRadio9">Need Building No.</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input iad_radio" name="customRadio" id="customRadio10">
                                    <label class="custom-control-label" for="customRadio10">Other</label>
                                </div>
                            </fieldset>
                            <fieldset class="d-none">
                                <textarea name="other_description" class="form-control" id="other_description" cols="30" rows="10"></textarea>
                            </fieldset>
                        </div>

                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="AICUpdate" type="button" disabled class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!--Incomplete Address Modal -->
    {{--Password Modal--}}
    <div class="modal fade" id="PasswordModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="PasswordModal"
         aria-hidden="true" style="top:30%;">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white text-center">
                    <h4 class="modal-title white">Password</h4>

                </div>
                <div class="modal-body  text-center">

                    <div class="row justify-content-center">
                        <div class="form-group form-inline">
                            <input type="text" class="form-control password" autofocus id="password_input" placeholder="Enter Password"><button tabindex="-1" type="button" class="btn btn-primary ml-1" id="password_submit" disabled>Enter</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--Password Modal--}}

    {{--Consignee Refused--}}
    <div class="modal fade text-left" id="consignee_refused_modal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="consignee_refused_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Consignee Refused</h4>

                </div>
                <div class="modal-body  text-center">

                    <input type="hidden" name="iad_shipment_id" id="iad_shipment_id">
                    <input type="hidden" name="iad_status" id="iad_status">
                    <input type="hidden" name="iad_status_id" id="iad_status_id">
                    <div class="row justify-content-center mb-2">
                        <div class="col-9 text-left">
                            {{-- consignee_refused_reasons --}}
                            <fieldset class="form-group">
                                <select name="consignee_refused_reasons" id="consignee_refused_reasons" class="form-control select2">
                                    @foreach($consignee_refused_reasons as $reasons)
                                        <option value="{{$reasons->id}}">{{$reasons->reasons}}</option>
                                    @endforeach
                                </select>
                            </fieldset>

                            {{-- <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input cr_radio" name="customRadio" id="customRadio11" status="Consignee Wants To Open The Shipment.">
                                    <label class="custom-control-label" for="customRadio11">Consignee Wants To Open The Shipment</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input cr_radio" name="customRadio" id="customRadio12" status="Issue In The COD Amount/Product.">
                                    <label class="custom-control-label" for="customRadio12">Issue In The COD Amount/Product</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input cr_radio" name="customRadio" id="customRadio13" status="No Such Order From Consignee.">
                                    <label class="custom-control-label" for="customRadio13"> No Such Order From Consignee</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input cr_radio" name="customRadio" id="customRadio14" status="Refused After Opening The Shipment.">
                                    <label class="custom-control-label" for="customRadio14">Refused After Opening The Shipment</label>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input cr_radio" name="customRadio" id="customRadio15">
                                    <label class="custom-control-label" for="customRadio15">Other</label>
                                </div>
                            </fieldset>--}}
                            <fieldset class="d-none">
                                <textarea name="other_description" class="form-control" id="cr_other_description" cols="30" rows="10"></textarea>
                            </fieldset> 
                        </div>

                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button id="CRUpdate" type="button" disabled class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{--Consignee Refused--}}
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style>
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }
        table.dataTable tbody tr.statusUpdated {
            background-color:yellow;
            color: #000;
        }
        table.dataTable tbody tr.statusDelivered {
            background-color:springgreen;
            color: #000;
        }
        table.dataTable tbody tr.statusReturn {
            background-color: #ef5753;
            color: #000;
        }
        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        table.dataTable tbody tr td.status,
        table.dataTable tbody tr td.reason,
        table.dataTable tbody tr td.remarks {
            min-width: 110px !important;
            max-width: 150px !important;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }

        .checkbox_overlay {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            @if($require_password)
            $('#PasswordModal').modal('show');
            @endif
            $('.password').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'mask': '99999'
            });
            $('body').on('keypress change','#password_input',function() {
                if($(this).val().length == 5){
                    $('#password_submit').attr('disabled', false);
                }
            });

            $('#password_submit').on('click', function () {
               var pass = $('#password_input').val();
                var delivery_note = $('#delivery_note').val();

                if(pass){
                   $.ajax({
                       url: '{!! route('admin.delivery.receive.password.check') !!}',
                       type: 'POST',
                       data: {
                           'delivery_note_id': delivery_note,
                           'password': pass,
                           '_token': '{{ csrf_token() }}'
                       }
                   }).done(function (data) {
                        if(data.status){
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            $('#password_input').val('');
                            $('#password_submit').attr('disabled', true);
                        }else{
                            $('#PasswordModal').modal('hide');
                            $('#password').val(pass);
                        }
                   });
               }
            });
            $('#password_input').keypress(function (event) {
                if(event.keyCode == 13){
                    var pass = $('#password_input').val();
                    var delivery_note = $('#delivery_note').val();

                    if(pass){
                        $.ajax({
                            url: '{!! route('admin.delivery.receive.password.check') !!}',
                            type: 'POST',
                            data: {
                                'delivery_note_id': delivery_note,
                                'password': pass,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status){
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                $('#password_input').val('');
                                $('#password_submit').attr('disabled', true);
                            }else{
                                $('#PasswordModal').modal('hide');
                                $('#password').val(pass);
                            }
                        });
                    }
                }

            });
            $('#select_all_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Status',
                width:'100%',
                allowClear:true
            });
            $('#consignee_refused_reasons').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Reason',
                width:'100%',
                allowClear:true,
                dropdownParent: $('#consignee_refused_modal') 
            });
            $('#select_all_reason').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Reason',
                width:'100%'
            });

            $('.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 3,
                'min': 0.00,
                'max': 10000
            });
            var shipment_status = [];
            var shipment_reason = [];
            var shipment_remarks = [];
            var selected_rows = [];
            var open_box_ids = [];
            var note_id = $('#delivery_note').val();
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true,
                buttons: [
                        @if(!$delivery_note_status == 1)

                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    
                                    $('#submit_selected_status').attr('disabled', false);
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                }
                            });
                            
                            $('#submit_selected_status').attr('disabled', true);
                        }
                    },
                    @endif
                    'reset'
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: false,
                paging: false,
                ajax: '{{ route('admin.delivery.receive.add.list',['id'=>$delivery_note_id]) }}',
                rowId: 'shId',
                // order: [[2, 'desc']],
                ordering: false,
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'shipment_id_padded',name: 'shipments.id', class: 'align-middle shipment_id'},
                    {data:'tracking_number',name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data:'consignee_name',name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data:'collection_amount',name: 'shipments.amount', class: 'align-middle amount'},
                    {data:'status',name: 'status', class: 'align-middle status form-group statusOnChange',orderable: false, searchable: false},
                    {data:'reason',name: 'reason', class: 'align-middle reason form-group reasonSelect',orderable: false, searchable: false},
                    {data:'remarks',name: 'remarks', class: 'align-middle remarks',orderable: false, searchable: false},
                    {data:'received_or_refused_by',name: 'received_or_refused_by', class: 'align-middle received_or_refused_by',orderable: false, searchable: false},
                    {data:'cnic',name: 'cnic', class: 'align-middle cnic'},
                    {data:'relation',name: 'relation', class: 'align-middle relation'},
                    {data:'address',name: 'shipments.consignee_address', class: 'align-middle address'},
                    {data:'attempts' ,name: 'shipments.id', class: 'align-middle attempts'},
                    {data:'open_box' ,name: 'open_box', class: 'align-middle test-center open_box',orderable: false, searchable: false},
                    {data:'rider_status',name: 'rss.name', class: 'align-middle status form-group rider_status',orderable: false, searchable: false},
                    {data:'rider_reason',name: 'rssr.name', class: 'align-middle reason form-group rider_reason',orderable: false, searchable: false},
                    {data:'destination',name: 'oc.name', class: 'align-middle destination'},
                    {data:'shipper',name: 'shipper', class: 'align-middle shipper'},
                    {data:'current_status',name: 'current_status', class: 'align-middle current_status'},
                    {data:'service_type',name: 'service_type', class: 'align-middle service_type'},
					{data:'consolidation' ,name: 'consolidation', class: 'align-middle consolidation'},
                    {data:'consolidated_id' ,name: 'consolidations.consolidation_id', class: 'align-middle consolidated_id'},
                    {data:'ccd_image',name: 'ccd_image', class: 'align-middle ccd_image',orderable: false, searchable: false},
                    {data:'action',name: 'action', class: 'align-middle action',orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                drawCallback: function (settings) {
                    var api = new $.fn.dataTable.Api( settings );
                    var data = api.rows( {page:'current'} ).data();
                    var statuses = [7, 8, 9, 12, 15, 18, 56];
                    $.each(data,function (key,value) {
                        if(value.rider_status_id != null && value.latest_rider_status_id != null ){
                            if(statuses.includes(value.rider_status_id)){
                                $("#statusDrop_"+value.shId).select2({
                                    placeholder: "Select a Status",
                                    width:'100%'
                                });
                                $("#reasonDrop_"+value.shId).select2({
                                    placeholder: "Select a Reason",
                                    width:'100%'
                                });
                                $('#statusSubmit').removeAttr('disabled');
                            }
                            else{
                                $("#statusDrop_"+value.shId).prepend('<option value="" selected="selected"></option>').select2({
                                    placeholder: "Select a Status",
                                    width:'100%'
                                });

                                $("#reasonDrop_"+value.shId).prepend('<option value="" selected="selected"></option>').select2({
                                    placeholder: "Select a Reason",
                                    width:'100%'
                                });
                            }
                        }
                        else{
                            $("#statusDrop_"+value.shId).prepend('<option value="" selected="selected"></option>').select2({
                                placeholder: "Select a Status",
                                width:'100%'
                            });

                            $("#reasonDrop_"+value.shId).prepend('<option value="" selected="selected"></option>').select2({
                                placeholder: "Select a Reason",
                                width:'100%'
                            });
                        }
                        $("#relationDrop_"+value.shId).prepend('<option value="" selected="selected"></option>').select2({
                            placeholder: "Select a Relation",
                            width:'100%'
                        });
                        if(shipment_status.length !== 0){
                            $('select[name="status_drop['+value.shId+']"]').val(shipment_status[value.shId]).trigger('change');
                        }
                        if(shipment_reason.length !== 0){
                            $('select[name="reason_drop['+value.shId+']"]').val(shipment_reason[value.shId]).trigger('change');
                        }
                        if(shipment_remarks.length !== 0){
                            $('input[name="remarks['+value.shId+']"]').val(shipment_remarks[value.shId]);
                        }
                    });
                },
                initComplete: function() {

                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.status') || $(header).is('.reason') || $(header).is('.remarks') || $(header).is('.action') || $(header).is('.received_or_refused_by')|| $(header).is('.open_box') || $(header).is('.consolidation')|| $(header).is('.relation')|| $(header).is('.cnic')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change keypress', function() {
                                if ($(header).is('.amount')){
                                    var value = $(this).val().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                                    column.search(value, false, false, true).draw();
                                }
                                else {
                                    column.search($(this).val(), false, false, true).draw();
                                }
                            }).wrap(td).after(icon);
                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                var con_id = parseInt($(this).parent('tr').attr('consolidation_id'));

                if(con_id){
                    table.rows().nodes().each(function(index) {
                        var row = table.row(index);
                        if ($(row.node()).attr('consolidation_id') == con_id) {
                            var rid = parseInt($(row.node()).attr('id'));
                            var rindex = $.inArray(rid, selected_rows);

                            if (rindex === -1) {
                                selected_rows.push(rid);
                                if(id != rid){

                                    table.row(row).select();
                                }
                            }
                            else {
                                if(id != rid){

                                    row.deselect();
                                }
                                selected_rows.splice(rindex, 1);
                            }
                        }
                    });
                }else{
                    var index = $.inArray(id, selected_rows);

                    if (index === -1) {

                        selected_rows.push(id);
                    }
                    else {
                        selected_rows.splice(index, 1);
                    }
                }


                if (selected_rows.length > 0) {
                    table.button('.delivered').enable();
                    $('#submit_selected_status').attr('disabled', false);
                }
                else {
                    table.button('.delivered').disable();
                    $('#submit_selected_status').attr('disabled', true);
                }
                
            });



            $('body').on('select2:select','.statusOnChange .statusDrop',function (e) {
                var rowid = parseInt($(this).parents('tr').attr('id'));

                $('#statusSubmit').removeAttr('disabled');
                var statusSelection = $(this).find(':selected');
                var status = statusSelection.val();
                shipment_status[rowid] = status;
                var reason = statusSelection.closest('td').next('td').find('.reasonDrop');
                $.ajax({
                    url:'{!! route('admin.delivery.receive.reason') !!}',
                    type:'POST',
                    dataType:'json',
                    data: {
                        'status':status,
                        'shipment_id': rowid,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status == 0){
                        reason.empty().trigger('change');
                        $.each(data.reasons,function (key,value) {
                            var newOption = new Option(value.name, value.id, false, false);
                            reason.append(newOption).trigger('change');
                            reason.attr('data-rule-required', 'true');
                            reason.attr('data-msg-required', 'Reason is required');
                        });
                        reason.val('').trigger('change');
                    }else{
                        reason.empty().trigger('change');
                        toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });

            });

            if ($('.cnic').length > 0) {
                //do something
                $(this).attr('data-rule-required', 'true');
                $(this).attr('data-msg-required', 'CNIC is required');
                console.log('got yea');
            }

            $('body').on('click', 'input.open_box', function(){
                var open_box_id = $(this).parents('tr').attr('id');
                var ob_index = $.inArray(open_box_id, open_box_ids);

                if (ob_index === -1) {
                    open_box_ids.push(open_box_id);
                }
                else {
                    open_box_ids.splice(ob_index, 1);
                }
            });

            $('.iad_radio').on('click', function () {
                var id = $(this).attr('id');
                var status = $(this).attr('status');
                if(id == 'customRadio10'){
                    $('#other_description').parent('fieldset').removeClass('d-none');
                    $('#AICUpdate').attr('disabled', true);

                }else{
                    $('#other_description').parent('fieldset').addClass('d-none');
                    $('#AICUpdate').attr('disabled', false);
                    $('#iad_status').val(status);
                }

            });

            // $('.cr_radio').on('click', function () {
            //     var id = $(this).attr('id');
            //     var status = $(this).attr('status');
            //     if(id == 'customRadio15'){
            //         $('#cr_other_description').parent('fieldset').removeClass('d-none');
            //         $('#CRUpdate').attr('disabled', true);

            //     }else{
            //         $('#cr_other_description').parent('fieldset').addClass('d-none');
            //         $('#iad_status').val(status);
            //         $('#CRUpdate').attr('disabled', false);
            //     }
            // });

            $('#consignee_refused_reasons').on('change', function () {
                var selected_value = $(this).val();
                var selected_text = $(this).select2('data');
                var status = selected_text[0].text;
                if(selected_value == 12){
                    $('#cr_other_description').parent('fieldset').removeClass('d-none');
                    $('#CRUpdate').attr('disabled', true);

                }else{
                    $('#cr_other_description').parent('fieldset').addClass('d-none');
                    $('#iad_status_id').val(selected_value);
                    $('#iad_status').val(status);
                    $('#CRUpdate').attr('disabled', false);
                }
            });




            $('#other_description').on('input', function () {
               var description = $.trim($(this).val());
               if(description != ''){
                   $('#AICUpdate').attr('disabled', false);
                   $('#iad_status').val(description);
               }
               if(description == ''){
                   $('#AICUpdate').attr('disabled', true);
                   $('#iad_status').val('');
               }
            });

            $('#cr_other_description').on('input', function () {
                var description = $.trim($(this).val());
                if(description != ''){
                    $('#CRUpdate').attr('disabled', false);
                    $('#iad_status').val(description);
                }
                if(description == ''){
                    $('#CRUpdate').attr('disabled', true);
                    $('#iad_status').val('');
                }
            });
            var receiving_date_picker;
            $('body').on('select2:select','.reasonSelect .reasonDrop',function (e) {
                var rowid = parseInt($(this).parents('tr').attr('id'));
                var reasonSelection = $(this).find(':selected');
                var reason_status = reasonSelection.val();
                console.log(reason_status);

                shipment_reason[rowid] = reason_status;
				if(reason_status == 3){
                    $('#IncompleteAddressModal').modal('show');
                    $('#iad_shipment_id').val(rowid);
                }
                if(reason_status == 8){
                    $('#consignee_refused_modal').modal('show');
                    $('#iad_shipment_id').val(rowid);
                }
                if(reason_status == 5){
                    $('#DateModal').modal('show');
                    $('#date_shipment_id').val(rowid);
                    $('#receiving_date').pickadate({
                        firstDay: 1,
                        clear: '',
                        disable: [7],
                        min: new Date('{{$dayAfterTomorrow}}'),
                        max: new Date('{{$days15FromNow}}'),
                        format:'dd mmmm, yyyy',
                        selectYears: true,
                        selectMonths: true,
                        formatSubmit: 'yyyy-mm-dd',
                        hiddenSuffix: '_formatted',
                        onOpen: function() {
                            $('#receiving_date_root').css('top','40px');
                        },
                    });
                }
                if(reason_status == 16){
                    $('#DateModal').modal('show');
                    $('#date_shipment_id').val(rowid);
                    $('#receiving_date').pickadate({
                        firstDay: 1,
                        clear: '',
                        disable: [7],
                        min: new Date('{{$tomorrow}}'),
                        max: new Date('{{$next3days}}'),
                        format:'dd mmmm, yyyy',
                        selectYears: true,
                        selectMonths: true,
                        formatSubmit: 'yyyy-mm-dd',
                        hiddenSuffix: '_formatted',
                        onOpen: function() {
                            $('#receiving_date_root').css('top','40px');
                        },
                    });
                }
            });

            $('#DateUpdate').on('click', function () {
                var receiving_date = $('input[name="receiving_date_formatted"]').val();
                if(receiving_date === '' || receiving_date === null){
                    var error = '<p class="danger">Please select a date</p>';
                    $('#receiving_date_div').append(error);
                }else{
                    $('#receiving_date_div p.danger').remove();
                    var shipment_id = $('#date_shipment_id').val();
                    var remarks_input = $('tr#'+shipment_id).find('td.remarks input');
                    remarks = remarks_input.val();
                    remarks = remarks+ ' ' + receiving_date;
                    remarks_input.val(remarks);
                    $('#DateModal').modal('hide');
                    $('#receiving_date').pickadate('picker').set('clear');
                }
            });
            $('#DateModal').on('hidden.bs.modal', function () {
                $('#receiving_date').pickadate('picker').stop();
            });


            $('body').on('click','.clear',function () {
                var status = $(this).parents().closest('tr').find('.statusDrop');
                var reason = $(this).parents().closest('tr').find('.reasonDrop');
                var relation = $(this).parents().closest('tr').find('.relationDrop');
                status.val('').trigger("change");
                reason.val('').trigger("change");
                relation.val('').trigger("change");
                $('.remarks input').val('');
            });
            $('#status_update_form').on('keypress',function (e) {
                if(e.which == 13) {
                    e.preventDefault();
                }
            });


            $('body').on('select2:select','#select_all_status',function (e) {

                var statusSelection = $(this).find(':selected');
                var status = statusSelection.val();
                console.log(statusSelection,status,$('#delivery_note').val());

                    $('#select_all_status option[value="14"]').prop('disabled', true);

                var all_reason = $('#select_all_reason');
                $.ajax({
                    url:'{!! route('admin.delivery.receive.reason_all') !!}',
                    type:'POST',
                    dataType:'json',
                    data: {
                        'delivery_note_id':$('#delivery_note').val(),
                        'status':status,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status == 0){
                        all_reason.empty().trigger('change');
                        $.each(data.reasons,function (key,value) {
                            var newOption = new Option(value.name, value.id, false, false);
                            all_reason.append(newOption).trigger('change');
                            if(all_reason != 14){
                                all_reason.attr('data-rule-required', 'true');
                                all_reason.attr('data-msg-required', 'Reason is required');
                            }
                        });
                        all_reason.val('').trigger('change');
                    }
                    else if(data.status == 2)
                    {
                        $('#select_all_status').val('').trigger('change');
                        toastr.error(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                    else{
                        all_reason.empty().trigger('change');
                        toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });

            $('#AICUpdate').on('click', function () {
                var status = $('#iad_status').val();
                var id = $('#iad_shipment_id').val();
                var remark_input = $('#datatable tr#'+id).find('td.remarks input');
                var remark = remark_input.val();
                remark = remark+ ' ' + status;
                remark_input.val(remark);
                $('#IncompleteAddressModal').modal('hide');
            });


            $('#CRUpdate').on('click', function () {
                var remarks_status = $('#iad_status').val();
                var remarks_id = $('#iad_status_id').val();
                var id = $('#iad_shipment_id').val();
                var remark_id = $('#datatable tr#'+id).find('td.remarks input:nth-child(1)');
                var remark_input = $('#datatable tr#'+id).find('td.remarks input:nth-child(2)');

                console.log(remark_id);
                console.log(remark_input);

                var remarks_id = remarks_id;
                var remark = remarks_status;

                remark_id.val(remarks_id);
                remark_input.val(remark);
                $('#consignee_refused_modal').modal('hide');
            });

            $('#IncompleteAddressModal').on('hide.bs.modal', function () {
                $('#iad_status').val('');
                $('#iad_shipment_id').val('');
                $('.iad_radio').prop('checked', false);
                $('#AICUpdate').attr('disabled', true);
                $('#other_description').parent('fieldset').addClass('d-none');
                $('#other_description').val('');
            });

            $('#consignee_refused_modal').on('hide.bs.modal', function () {
                $('#iad_status').val('');
                $('#iad_shipment_id').val('');
                $('.cr_radio').prop('checked', false);
                $('#consignee_refused_reasons').val('').trigger('change');
                $('#CRUpdate').attr('disabled', true);
            });

            var shipments = [];
            // $('#status_update_form').bind('submit', function(event) {
            //     event.preventDefault();
            //     var this_form = this;
            //     swal({
            //         title: 'Are You Sure?',
            //         text: 'Select Yes to change the status of shipments!',
            //         icon: 'warning',
            //         buttons: {
            //             cancel: {
            //                 text: 'No',
            //                 value: null,
            //                 visible: true,
            //                 closeModal: true,
            //             },
            //             confirm: {
            //                 text: 'Yes',
            //                 value: true,
            //                 visible: true,
            //                 closeModal: true
            //             }
            //         },
            //         closeOnClickOutside: false,
            //         closeOnEsc: false,
            //         dangerMode: true
            //     }).then(function (confirm) {
            //         if (confirm) {
            //
            //             var shipment = $('#shipment_ids');
            //             event.preventDefault();
            //             var id = '';
            //             var count = table.data().count();
            //             for(var i = 0;i<count;i++){
            //                 id = table.row( i ).id();
            //                 shipments.push(id);
            //             }
            //             shipment.val(shipments);
            //             blockPagePermanently();
            //             this_form.submit();
            //         }
            //     });
            //
            // });
                $('#status_update_form').validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    var this_form = form;
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to change the status of shipments!',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if (confirm) {

                            var shipment = $('#shipment_ids');
                            var open_box_input = $('#open_box_ids');
                            event.preventDefault();
                            var id = '';
                            var count = table.data().count();
                            for(var i = 0;i<count;i++){
                                id = table.row( i ).id();
                                shipments.push(id);
                            }
                            shipment.val(shipments);
                            open_box_input.val(open_box_ids);
                            
                            
                            blockPagePermanently();
                            this_form.submit();
                        }
                    });


                }
            });

            $("#upload_pod_form" ).validate({
                errorClass:"danger",
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {

                    form.submit();

                }
            });


            //on page load ajax
            var trybuy_ids = [];
            var shipment_id_list = [];
            var shipments_count = $('#shipments_count').val();
            function checkShipmentStatuses(){
                var delivery_note = $('#delivery_note').val();
                $.ajax({
                    url: '{!! route('admin.delivery.receive.shipmentstatuscheck') !!}',
                    method: 'POST',
                    data: {
                        'delivery_note_id': delivery_note,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {


                    if(shipments_count>0) {
                        if (data.status == 1) {
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                            checkShipmentStatuses();
                        } else if (data.status == 2) {

                            $('#ReplacementModal').modal('show');

                            var repl = $('#replacementtable').DataTable({
                                dom: 'ltipr',
                                paging: false,
                                columns: [
                                    {
                                        orderable: false,
                                        searchable: false,
                                        name: 'serial_number',
                                        class: 'align-middle serial_number',
                                        targets: 1,
                                        render: function (data, type, row) {
                                            return '';
                                        }
                                    },
                                    {
                                        name: 'tracking_number',
                                        class: 'align-middle tracking_number',
                                        orderable: false,
                                        searchable: false
                                    },
                                    {
                                        name: 'service_type',
                                        class: 'align-middle service_type',
                                        orderable: false,
                                        searchable: false
                                    },
                                    {name: 'weight', class: 'align-middle weight', orderable: false, searchable: false},

                                ],
                                rowCallback: function (row, data, index) {
                                    var info = repl.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                },
                            });

                            $.ajax({
                                url: '{!! route('admin.delivery.receive.replacements') !!}',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    'delivery_note_id': delivery_note,
                                    'replacements': data.replacement,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if (data.status == 0) {
                                    var rowNo = repl.rows().count();
                                    $.each(data.data, function (key, value) {
                                        var inp = "<div class='form-group mb-0'><input class='form-control decimal' name='weight[" + value.id + "]' placeholder='Enter Weight'  data-rule-required='true' data-msg-required='Weight is required!'></div>";
                                        repl.row.add([rowNo + 1, value.tracking_number, value.booking_type_id, inp]).node().id = value.id;
                                        repl.draw(false);
                                        shipment_id_list.push(value.id);
                                        $('.decimal').inputmask({
                                            'alias': 'decimal',
                                            'allowMinus': false,
                                            'allowPlus': false,
                                            'rightAlign': false,
                                            'digits': 3,
                                            'min': 0.01,
                                            'max': 10000
                                        });
                                    });

                                } else {
                                    //toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            });


                        }else if (data.status == 9){

                            $('#NonServiceModal').modal('show');

                            var nsatable = $('#nsatable').DataTable({
                                dom: 'ltipr',
                                paging: false,
                                columns: [
                                    {
                                        orderable: false,
                                        searchable: false,
                                        name: 'serial_number',
                                        class: 'align-middle serial_number',
                                        targets: 1,
                                        render: function (data, type, row) {
                                            return '';
                                        }
                                    },
                                    { name: 'tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false  },
                                    { name: 'address', class: 'align-middle address', orderable: false, searchable: false },
                                    { name: 'destinatoin', class: 'align-middle destinatoin', orderable: false, searchable: false},
                                    { name: 'shipper', class: 'align-middle shipper', orderable: false, searchable: false},
                                    { name: 'charges', class: 'align-middle charges', orderable: false, searchable: false},
                                    { name: 'remarks', class: 'align-middle remarks', orderable: false, searchable: false},

                                ],
                                rowCallback: function (row, data, index) {
                                    var info = nsatable.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                },
                            });

                            $.ajax({
                                url: '{!! route('admin.delivery.receive.nsa_shipments_data') !!}',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    'delivery_note_id': delivery_note,
                                    'nsa_shipments': data.non_service_area_shipments,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if (data.status == 0) {
                                    var rowNo = nsatable.rows().count();
                                    $.each(data.shipments, function (key, value) {
                                        var charges = "<div class='form-group mb-0'><input class='form-control decimal' name='charges[" + value.id + "]' placeholder='Enter Estimated Charges'  data-rule-required='true' data-msg-required='Estimated Charges is required!'></div>";
                                        var remarks = "<div class='form-group mb-0'><input class='form-control' name='remarks[" + value.id + "]' placeholder='Enter Remarks'  data-rule-required='true' data-msg-required='Remark is required!' value='" + value.remarks + "'> </div>";
                                        // console.log(value.tracking_number)
                                        nsatable.row.add([rowNo + 1, value.tracking_number, value.consignee_address, value.consignee_city_id, value.user_id, charges, remarks]).node().id = value.id;
                                        nsatable.draw(false);
                                        shipment_id_list.push(value.id);
                                        $('.decimal').inputmask({
                                            'alias': 'integer',
                                            'allowMinus': false,
                                            'allowPlus': false,
                                            'rightAlign': false,
                                            'min': 0,
                                            'max': 100000
                                        });
                                    });

                                } else {
                                    //toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            });

                        } else if (data.status == 3) {
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });

                            $('#TryBuyModal').modal('show');
                            // checkShipmentStatuses();


                            trybuy = $('#trybuytable').DataTable({
                                dom: 'ltipr',
                                paging:false,

                                columns: [
                                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                                    {name: 'product_type', class: 'align-middle product_type'},
                                    {name: 'product_description', class: 'align-middle product_description'},
                                    {name: 'item_price', class: 'align-middle item_price'},
                                    {name: 'receiving', class: 'align-middle receiving position-relative'},

                                ],
                                rowCallback: function(row, data, index) {
                                    var info = trybuy.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                }
                            });

                            //trybuy shipment id for modal
                            $('#trybuy_shipment_id').val(data.try);
                            $.ajax({
                                url:'{!! route('admin.delivery.receive.trybuys') !!}',
                                type:'POST',
                                dataType:'json',
                                data: {
                                    'delivery_note_id': delivery_note,
                                    'trybuy':data.try,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {

                                if(data.status == 0){
                                    var rowNo = trybuy.rows().count();
                                    $.each(data.data,function (key,value) {
                                        trybuy_ids.push(value.pid);
                                        var inp = "<div class='checkbox_overlay'></div><input type='checkbox' checked class='form-control bought' name='bought["+value.pid+"]'>";
                                        trybuy.row.add([rowNo+1,value.type,value.description,value.price,inp]).node().id = value.pid;
                                        trybuy.draw(false);
                                        $('#cod').text(data.total_cod);
                                    });

                                }else{
                                    //toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            });


                        } else if (data.status == 0) {

                        }
                    }
                    shipments_count = shipments_count-1;
                });
            }
            checkShipmentStatuses();

            $('#items_scan_form input.item_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#items_scan_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {

                    var item_number = $(form).find('input.item_number').val();

                    form.reset();

                    if (trybuy.rows('[id='+ item_number +']').any()) {
                        var item = $('tr#'+item_number).find('.receiving input');
                        if(item.is(':checked')){
                            item.attr('checked', false);
                            item_scanned_cod_change(item);
                        }else{
                            toastr.error('Item has been scanned already!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }else{
                        toastr.error('Item not found in the list!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            function print(id,temp = null) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.dncc.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'temporary':temp,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
            function printUndelivered(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.undelivered.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                        location.reload();

                    });
            }

            $('#printDNCC').on('click',function () {
                var note_id = $('#delivery_note').val();
                print(note_id);
            });
            $('#printTempDNCC').on('click',function () {
                var note_id = $('#delivery_note').val();
                var temporary = 'temporary';
                print(note_id,temporary);
            });
            $('#printUndeliveredDNCC').on('click',function () {
                var note_id = $('#delivery_note').val();
                printUndelivered(note_id);
            });

            function item_scanned_cod_change(bought){
                console.log(trybuy_ids);

                var check = $(bought);
                var id = parseInt($(bought).parents('tr').attr('id'));
                var price = $(bought).parents('tr').find('td.item_price').text();
                var total_cod = $('#cod').text();
                var newcod = '';
                if($.isNumeric(price)){
                    if(check.is(':checked')){
                        newcod = parseInt(total_cod) + parseInt(price);
                        $('#cod').text(newcod);
                        trybuy_ids.push(id);

                    }else{
                        trybuy_ids.splice( $.inArray(id, trybuy_ids), 1 );
                        newcod = parseInt(total_cod) - parseInt(price);
                        $('#cod').text(newcod);
                    }
                }
            }
            /*// $('body').on('click','.receiving input:checkbox',function () {
            //     item_scanned_cod_change($(this));
            // });*/

            //replacement modal bind
            $('#replacement_form').bind('submit',function (e) {
                e.preventDefault();
            });
            $( "#replacement_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#shipment_id_list').val(shipment_id_list);
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    form.submit();

                }
            });
            //end replacement
            $('#trybuy_form').bind('submit',function (e) {
                // blockPagePermanently();
                var this_form = this;
                e.preventDefault();
                var total = $('#cod').text();
                total = parseInt(total);
                var deliverynote_id = $('#delivery_note').val();
                $('#trybuy_cod').val(total);
                $('#trybuy_id_list').val(trybuy_ids);
                var checkbox_count = $('.bought:checked').length;
                var uncheckbox_count = $('input:checkbox.bought').length;
                $('#item_checked').val(checkbox_count);
                $('#item_unchecked').val(uncheckbox_count);
                $('#delivery_note_trybuy').val(deliverynote_id);
                // if(checkbox_count > 0){
                    // UnblockPagePermanently();

                // }else{
                //     UnblockPagePermanently();
                //     var error = "Select at-least one item!";
                //     toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                // }
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to update Try & Buy Delivery!',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes',
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {
                        this_form.submit();
                    }
                });
            });

            var shipment_remarks_obj = {};
            var shipment_received_refused_obj = {};
            var submit_all_status_flag = true;
            $('#submit_selected_status').on('click', function () {

                var select_all_status = $('#select_all_status').val();

                var delivery_note = $('#delivery_note').val();
                var select_all_reason = $('#select_all_reason').val();
                var password = $('#password').val();
                if(selected_rows.length > 0 && (select_all_status != '')){

                        if ((select_all_reason != null) || (select_all_reason == null && select_all_status == 14)) {

                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to change the status of shipments!',
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function (confirm) {
                            if (confirm) {
                                var missing_input_issue = [];
                                var missing_input_temp = "";
                                var not_updated_shipments = [];
                                var shipment_remarks_obj = {};
                                var shipment_received_refused_obj = {};
                                var shipment_cnic_obj = {};
                                var shipment_relation_obj = {};
                                // blockPagePermanently();
                                submit_all_status_flag = true;
                                if (select_all_status == 14) {
                                    table.rows().nodes().each(function (index) {
                                        missing_input_temp = "";
                                        var row = table.row(index);

                                        if ($(row.node()).hasClass('selected')) {
                                            var id = parseInt(row.id());
                                            var amount = parseInt($(row.node()).attr('amount'));
                                            var remarks = $(row.node()).find('td.remarks input').val();
                                            shipment_remarks_obj[id] = remarks;
                                            if (amount == 0) {
                                                var receiver_name = $(row.node()).find('td.received_or_refused_by input').val();
                                                var cnic_input = $(row.node()).find('td.cnic input').val();
                                                var relation_input = $(row.node()).find('td.relation select').val();
                                                if ($.trim(receiver_name) == '' || $.trim(cnic_input) == '' || $.trim(relation_input) == '') {
                                                    not_updated_shipments.push($(row.node()).find('td.tracking_number').text());
                                                    submit_all_status_flag = false;

                                                    //Print Message

                                                    if ($.trim(receiver_name) == ''){
                                                        missing_input_temp+=" {Name} ";
                                                    }
                                                    if ($.trim(cnic_input) == ''){
                                                        missing_input_temp+=" {CNIC} ";
                                                    }
                                                    if ($.trim(relation_input) == ''){
                                                        missing_input_temp+=" {RELATION} ";
                                                    }
                                                    if(missing_input_temp) {
                                                        missing_input_issue.push(missing_input_temp);
                                                    }
                                                    //Print Message End

                                                } else {
                                                    shipment_received_refused_obj[id] = receiver_name;
                                                    shipment_cnic_obj[id] = cnic_input;
                                                    shipment_relation_obj[id] = relation_input;
                                                }
                                            }


                                        }
                                    });
                                    if (submit_all_status_flag == false) {

                                        var html = '';
                                        $.each(not_updated_shipments, function (index, tracking_number) {
                                            html += missing_input_issue[index]+" not found of "+ tracking_number + '<br/>';
                                        });

                                        html += '<br/>Update Received / Refused By , Cnic , Relation for all Shipment(s) of 0 (zero) amount !';

                                        content = document.createElement('div');
                                        content.innerHTML = html;
                                        swal({
                                            title: "Fill the empty fields",
                                            content: content,
                                            icon: 'warning',
                                            buttons: {
                                                cancel: {
                                                    text: 'Close',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                },
                                            },
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            dangerMode: true
                                        });
                                    }

                                } else {
                                    table.rows().nodes().each(function (index) {
                                        var row = table.row(index);
                                        if ($(row.node()).hasClass('selected')) {
                                            var id = parseInt(row.id());
                                            var remarks = $(row.node()).find('td.remarks input').val();
                                            shipment_remarks_obj[id] = remarks;

                                        }
                                    });
                                }

                                if (submit_all_status_flag) {
                                    swal({
                                        title: 'Please Wait!',
                                        text: 'Shipment(s) are being updated!',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                                    $.ajax({
                                        url: '{!! route('admin.delivery.receive.add.status.all') !!}',
                                        method: 'POST',
                                        data: {
                                            'shipment_ids': selected_rows,
                                            'open_box_ids': open_box_ids,
                                            'selected_status': select_all_status,
                                            'selected_reason': select_all_reason,
                                            'delivery_note_id': delivery_note,
                                            'remarks': shipment_remarks_obj,
                                            'received_or_refused_by': shipment_received_refused_obj,
                                            'cnic': shipment_cnic_obj,
                                            'relation': shipment_relation_obj,
                                            '_token': '{{ csrf_token() }}',
                                            'password': password,
                                        }
                                    }).done(function (data) {
                                        if (data.status === 1) {
                                            var tracking_numbers = '';
                                            var route = '{!! route('admin.tracking.index') !!}';
                                            UnblockPagePermanently();
                                            if(data.first_attempt_shipments.length > 0) {
                                                $.each(data.first_attempt_shipments, function (index, tracking_number) {
                                                    tracking_numbers += '<u><a href=' + route + '?tracking_number=' + tracking_number + ' target="_blank">' + tracking_number + '</a></u><br>';
                                                });
                                                var html = '<p>Return Confirmation Pending Cannot be mark on the following shipments due to First Delivery Attempt</p><br>';
                                                html += tracking_numbers;
                                                content = document.createElement('div');
                                                content.innerHTML = html;
                                                    swal({
                                                        title: 'RCP First Attempt',
                                                        content: content,
                                                        icon: 'warning',
                                                        buttons: {
                                                            confirm: {
                                                                text: 'OK',
                                                                value: null,
                                                                visible: true,
                                                                closeModal: true,
                                                            }
                                                        },
                                                        closeOnClickOutside: false,
                                                        closeOnEsc: false,
                                                        dangerMode: true
                                                    }).then(function (confirm) {
                                                        if (confirm) {
                                                            location.reload();
                                                        } else {
                                                            location.reload();
                                                        }
                                                    });

                                            }
                                            else{
                                                 toastr.success(data.success, 'Success!', {
                                                     positionClass: 'toast-bottom-center',
                                                     containerId: 'toast-bottom-center'
                                                 });
                                                 location.reload();
                                            }

                                        }
                                        else if(data.status === 2){
                                            var invalid_shipmet_flag = false;
                                            var not_replacement_shipment_flag = false;
                                            var html = '';
                                            var title = '';
                                            var route = '{!! route('admin.tracking.index') !!}';
                                            if(data.invalid_shipments){
                                                var tracking_numbers = '';
                                                $.each(data.invalid_shipments, function(index, tracking_number) {
                                                    tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                                });
                                                 html += '<p>Same consignee details found which are already marked as delivered of following Shipment(s):</p><br>';
                                                html += tracking_numbers;
                                                content = document.createElement('div');
                                                content.innerHTML = html;
                                                invalid_shipmet_flag = true;
                                                var title = "Same Consignee Info";
                                            }
                                            if(data.not_replacement_shipments){
                                                var replacement_tracking_numbers = '';
                                                $.each(data.not_replacement_shipments, function(index, tracking_number) {
                                                    replacement_tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                                });
                                                 html += '<p>Following Shipments Cannot be mark as Replacement - Not Collected due to Booking Type:</p><br>';
                                                html += replacement_tracking_numbers;
                                                content = document.createElement('div');
                                                content.innerHTML = html;
                                                not_replacement_shipment_flag = true;
                                                if(title != ''){
                                                    title += "/Booking Type Except Replacement";
                                                }else{
                                                    title = "Booking Type Except Replacement";
                                                }
                                            }
                                            if(data.first_attempt_shipments.length > 0) {
                                                var  fa_tracking_number = '';
                                                $.each(data.first_attempt_shipments, function (index, tracking_number) {
                                                    fa_tracking_number += '<u><a href=' + route + '?tracking_number=' + tracking_number + ' target="_blank">' + tracking_number + '</a></u><br>';
                                                });
                                                 html += '<p>Return Confirmation Pending Cannot be mark on the following shipments due to First Delivery Attempt</p><br>';
                                                html += fa_tracking_number;
                                                content = document.createElement('div');
                                                content.innerHTML = html;
                                                invalid_shipmet_flag = true;
                                                if(title != ''){
                                                    title += "/RCP First Attempt";
                                                }else{
                                                    title = "RCP First Attempt";
                                                }
                                            }
                                            if(invalid_shipmet_flag || not_replacement_shipment_flag) {
                                                swal({
                                                    title: title,
                                                    content: content,
                                                    icon: 'warning',
                                                    buttons: {
                                                        confirm: {
                                                            text: 'OK',
                                                            value: null,
                                                            visible: true,
                                                            closeModal: true,
                                                        }
                                                    },
                                                    closeOnClickOutside: false,
                                                    closeOnEsc: false,
                                                    dangerMode: true
                                                }).then(function (confirm) {
                                                    if (confirm) {
                                                        location.reload();
                                                    } else {
                                                        location.reload();
                                                    }
                                                });
                                            }
                                        }
                                        else {
                                            UnblockPagePermanently();
                                            toastr.error(data.error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                            location.reload();
                                        }
                                    });
                                }
                            }
                        });
                    }else{
                            var error = "Please Select A Reason!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                    }
                }
                else{
                    if(select_all_status == ''){
                        var error = "Please Select A Status!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                    if(selected_rows.length == 0){
                        var error = "Select at-least one shipment!";

                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }

            });

            $('#nsatable').on('change', 'td.remarks input', function () {
                $(this).val($(this).val().trim());
            });
            //nsa modal bind
            $('#nsa_form').bind('submit',function (e) {
                e.preventDefault();
            });
            $( "#nsa_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#nsa_shipment_ids').val(shipment_id_list);
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    form.submit();

                }
            });
            $('body').on('click', 'button.upload_pod',  function(){
                var id = $(this).parents('tr').attr('id');
                $('#pod_shipment').val(id);
                console.log(id)
                $('#upload_pod_modal').modal('show');
                // if(id){
                //     $('#corporate_rate_type_modal').modal('show');
                //     $('#corporate_rate_type_shipper_id').val(id);
                // }
            });

        });
    </script>
@endsection