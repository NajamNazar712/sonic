@extends('admin.layout.master')
@section('title','Outstanding Station Deposit Notes')

@section('content')
    <h1 class="mb-1">
        Outstanding Station Deposit Notes
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="track_form" class="justify-content-center m-2" novalidate="novalidate">
                    <div class="row mb-2 justify-content-center">

                        <div class="col-2">
{{--                            <fieldset class="position-relative has-icon-left">--}}

{{--                                <div class="form-control-position">--}}
{{--                                    <i class="ft-search"></i>--}}
{{--                                </div>--}}
{{--                            </fieldset>--}}
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search By SDN" name="scan_sdn"
                                       id="scan_sdn"  data-tags-input-name="scan_sdn">
                            </div>

                        </div>
                        <div class="col-2">
                            <fieldset class="position-relative">
                                <input type="text" class="form-control text-left" placeholder="Search By DNCC" name="scan_dncc"
                                       id="scan_dncc">
                                <div class="form-control-position">
                                    
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-2">
                            <fieldset class="position-relative">
                                <input type="text" class="form-control text-left" placeholder="Search By RNCC" name="scan_rncc"
                                       id="scan_rncc">
                                <div class="form-control-position">
                                   
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-3">
                            <fieldset class="position-relative">
                                <input type="text" class="form-control text-left" placeholder="Search By COD Tracking Number"
                                       name="search_tracking" id="search_tracking">
                                <div class="form-control-position">
                                    
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-3">
                            <fieldset class="position-relative">
                                <input type="text" class="form-control text-left" placeholder="Search By Retail Tracking Number"
                                       name="search_tracking_retail" id="search_tracking_retail">
                                <div class="form-control-position">
                                </div>
                            </fieldset>
                        </div>
                        <div class="mt-5">

                        </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                    </span>
                                </div>
                                <input type="text" name="search_date_from"
                                       class="form-control bg-primary border-primary white rounded-right pickadate"
                                       id="search_date_from" placeholder="Resolved Date From">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                    </span>
                                </div>
                                <input type="text" name="search_date_to"
                                       class="form-control bg-primary border-primary white rounded-right pickadate"
                                       id="search_date_to" placeholder="Resolved Date To">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                    </span>
                                </div>
                                <input type="text" name="search_date_from_deposited"
                                       class="form-control bg-primary border-primary white rounded-right pickadate"
                                       id="search_date_from_deposited" placeholder="Deposited Date From">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                    </span>
                                </div>
                                <input type="text" name="search_date_to_deposited"
                                       class="form-control bg-primary border-primary white rounded-right pickadate"
                                       id="search_date_to_deposited" placeholder="Deposited Date To">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <button type="submit" id="search_filter_btn"
                                        class="btn btn-outline-primary btn-min-width search"><i
                                            class="la la-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">SDN No.</th>
                        <th class="border-primary border-darken-1">SDN Type</th>
                        
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">No of DNCCs</th>
                        <th class="border-primary border-darken-1">Delivered Shipments</th>
                        <th class="border-primary border-darken-1">DNCC Amount</th>
                        <th class="border-primary border-darken-1">Deposited Amount</th>

                        <th class="border-primary border-darken-1">Deposited By</th>
                        <th class="border-primary border-darken-1">Deposited Date</th>
                        <th class="border-primary border-darken-1">Resolved By</th>
                        <th class="border-primary border-darken-1">Status</th>
                        {{-- <th class="border-primary border-darken-1">Zone</th> --}}
                        <th class="border-primary border-darken-1">Adjustment Date</th>
                        <th class="border-primary border-darken-1">Adjustment Amount</th>
                        <th class="border-primary border-darken-1">Adjustment Reference</th>
                        <th class="border-primary border-darken-1">Difference Amount</th>
                        <th class="border-primary border-darken-1">Deposit Slip</th>
                        <th class="border-primary border-darken-1">Aging</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>


    <!--Deposit Slip Modal -->
    <div class="modal fade text-left" id="uploadDepositSlip" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="uploadDepositSlip"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Deposit Slip Upload</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="sdn_upload_form" class="form" action="{{route('admin.delivery.sdn.slip')}}" method="post"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sdn_id" id="sdn_id"/>
                        <input type="hidden" name="deposit_rows" id="deposit_rows"/>
                        <table class="table table-bordered datatable" id="sdn_upload_table" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">

                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Date</th>
                                <th class="border-primary border-darken-1">Bank Name</th>
                                <th class="border-primary border-darken-1">Amount</th>
                                <th class="border-primary border-darken-1">Deposit Slip</th>
                                <th class="border-primary border-darken-1"></th>

                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <td colspan="3">Total</td>
                                <td id="upload_deposit_total"></td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="DepositSlipButton" type="submit" class="btn btn-primary btn-block" disabled>
                                    Upload
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Deposit Slip Modal -->
    <!--Shipments popup -->
    <div class="modal fade" id="dncc_modal" data-backdrop="static" role="dialog" aria-labelledby="dncc_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="dncc_modal_title">No. Of DNCC(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--Shipments popup -->
    <!--Shipments popup -->
    <div class="modal fade" id="pncc_modal" data-backdrop="static" role="dialog" aria-labelledby="pncc_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="pncc_modal_title">No. Of RNCC(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--Shipments popup -->
    <!--Shipments popup -->
    <div class="modal fade" id="delivered_shipments_modal" data-backdrop="static" role="dialog"
         aria-labelledby="delivered_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="delivered_shipments_modal_title">Delivered Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="adjustment_reference_modal" data-backdrop="static" role="dialog"
         aria-labelledby="adjustment_reference_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="adjustment_reference_modal_title">Adjustment Reference(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--Shipments popup -->

    <div class="modal fade text-left" id="ViewDepositSlip" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="ViewDepositSlip"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Deposit Slips View</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <table class="table table-bordered datatable" id="deposit_slip_table" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Slip Code</th>
                            <th class="border-primary border-darken-1">Date</th>
                            <th class="border-primary border-darken-1">Bank Name</th>
                            <th class="border-primary border-darken-1">Amount</th>
                            <th class="border-primary border-darken-1">Uploaded At</th>
                            <th class="border-primary border-darken-1">Uploaded By</th>
                            <th class="border-primary border-darken-1">Deposit Slip</th>

                        </tr>
                        </thead>
                    </table>

                    <hr>
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddAdjustmentModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="AddAdjustmentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Adjustment For SDN</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="sdn_adjustment_add" class="form" action="{{route('admin.delivery.sdn.adjustment.add')}}"
                          method="post">
                        @csrf
                        <input type="hidden" name="sdn_id" id="sdn_id_for_adjustment">
                        <input type="hidden" name="sdn_rows" id="sdn_rows_for_adjustment">
                        <table class="table table-bordered datatable" id="sdn_adjustment_table"
                               style="z-index: 3;width: 100%;">
                            <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1"></th>
                                <th class="border-primary border-darken-1">Statement #</th>
                                <th class="border-primary border-darken-1">Statement Creation Date</th>
                                <th class="border-primary border-darken-1">Amount</th>
                                <th class="border-primary border-darken-1">Action</th>

                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <td colspan="3">Total</td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>

                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="submit" class="btn btn-primary btn-block">Add Adjustment</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AddDNCCModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="AddDNCCModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add DNCC</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="sdn_add_dncc" class="form" action="{{route('admin.delivery.sdn.dncc.add')}}"
                          method="post">
                        @csrf
                        <input type="hidden" name="sdn_id" id="sdn_id_for_add_dncc">
                        <div class="form-group">
                            <select name="dncc_id" id="dncc_select" data-rule-required="true"
                                    data-msg-required="DNCC is Required" class="select2 form-control">

                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="remarks" id="dncc_remarks_input" placeholder="Remarks"
                                   class="form-control">
                        </div>

                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="submit" class="btn btn-primary btn-block">Add DNCC</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="RemoveDNCCModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="RemoveDNCCModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Remove DNCC</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="sdn_remove_dncc" class="form" action="{{route('admin.delivery.sdn.dncc.remove')}}"
                          method="post">
                        @csrf
                        <input type="hidden" name="sdn_id" id="sdn_id_for_remove_dncc">
                        <input type="hidden" name="dncc_id" id="dncc_id_for_remove_dncc">
                        <table class="table table-bordered datatable" id="remove_dncc_table"
                               style="z-index: 3;width: 100%;">
                            <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1"></th>
                                <th class="border-primary border-darken-1">S NO.</th>
                                <th class="border-primary border-darken-1">DNCC #</th>
                                <th class="border-primary border-darken-1">No. of Delivered Shipments</th>
                                <th class="border-primary border-darken-1">Amount</th>
                            </tr>
                            </thead>
                        </table>
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="submit" id="remove_dncc_btn" disabled class="btn btn-primary btn-block">
                                    Remove DNCC
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="AddPNCCModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="AddPNCCModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add pncc</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="sdn_add_pncc" class="form" action="{{route('admin.delivery.sdn.pncc.add')}}"
                          method="post">
                        @csrf
                        <input type="hidden" name="sdn_id" id="sdn_id_for_add_pncc">
                        <div class="form-group">
                            <select name="pncc_id" id="pncc_select" data-rule-required="true"
                                    data-msg-required="RNCC is Required" class="select2 form-control">

                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="remarks" id="pncc_remarks_input" placeholder="Remarks"
                                   class="form-control">
                        </div>

                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="submit" class="btn btn-primary btn-block">Add RNCC</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="RemovePNCCModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="RemovePNCCModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Remove RNCC</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="sdn_remove_pncc" class="form" action="{{route('admin.delivery.sdn.pncc.remove')}}"
                          method="post">
                        @csrf
                        <input type="hidden" name="sdn_id" id="sdn_id_for_remove_pncc">
                        <input type="hidden" name="pncc_id" id="pncc_id_for_remove_pncc">
                        <table class="table table-bordered datatable" id="remove_pncc_table"
                               style="z-index: 3;width: 100%;">
                            <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1"></th>
                                <th class="border-primary border-darken-1">S NO.</th>
                                <th class="border-primary border-darken-1">RNCC #</th>
                                <th class="border-primary border-darken-1">No. of Shipments</th>
                                <th class="border-primary border-darken-1">Amount</th>
                            </tr>
                            </thead>
                        </table>
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close
                                </button>
                            </div>
                            <div class="col-3">
                                <button type="submit" id="remove_pncc_btn" disabled class="btn btn-primary btn-block">
                                    Remove RNCC
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="status_logs_modal" data-backdrop="static" role="dialog"
         aria-labelledby="status_logs_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="status_logs_modal_title">SDN <span></span></h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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

        table.dataTable tbody tr.selected {
            background-color: #ebf5ff !important;
            color: #64a0d2 !important;
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    {{--    <script src="{{asset('app-assets/js/scripts/extensions/dropzone.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        function printStatement(id) {
            $.ajax({
                url: '{!! route('admin.petty_cash.statements.print') !!}',
                method: 'POST',
                data: {
                    'id': id,
                    '_token': '{{ csrf_token() }}'
                }
            })
                .done(function (data) {
                    var tab = window.open('', '_blank');

                    if (!tab) {
                        swal({
                            title: 'Popup Blocker Enabled!',
                            text: 'Please add this site to your exception list.',
                            icon: 'error',
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                    } else {
                        tab.document.write(data);
                        tab.document.close();
                        tab.focus();
                    }
                });
        }

        $(document).ready(function () {
            var search_date_from = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_date_to').pickadate('picker').set('min', $('#search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            var search_date_to = $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format: 'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_date_from').pickadate('picker').set('max', $('#search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            var search_date_from_deposited = $('#search_date_from_deposited').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_date_to_deposited').pickadate('picker').set('min', $('#search_date_from_deposited').pickadate('picker').get('select'));
                    }
                }
            });

            var search_date_to_deposited = $('#search_date_to_deposited').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format: 'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_date_from_deposited').pickadate('picker').set('max', $('#search_date_to_deposited').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    blockPagePermanently();

                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.delivery.sdn.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('SDN No.');
                            head.push('SDN Type');
                            
                            head.push('Hub');
                            head.push('No. of DNCCs');
                            head.push('Delivered Shipments');
                            head.push('DNCC Amount');
                            head.push('Deposited Amount');
                            head.push('Deposited By');
                            head.push('Deposited Date');
                            head.push('Resolved By');
                            head.push('Status');
                            // head.push('Zone');
                            head.push('Adjustment Date');
                            head.push('Adjustment Amount');
                            head.push('Adjustment Reference');
                            head.push('Difference Amount');
                            head.push('Aging');

                            $.each(result.data, function (index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.sdn_id_padded);
                                row.push(values.sdn_type);
                                
                                row.push(values.hub);
                                row.push(values.dncc_count);
                                row.push(values.sdn_delivered_shipments);
                                row.push(values.sdn_amount);
                                row.push(values.sdn_deposit_amount);
                                row.push(values.deposited_by);
                                row.push(values.created_at);
                                row.push(values.resolved_by);
                                row.push(values.status);
                                // row.push(values.zone);
                                row.push(values.adjustment_date);
                                row.push(values.adjusted_reference_count);
                                row.push(values.adjustment_ref);
                                row.push(values.difference_amount);
                                row.push(values.aging);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            });

        var selected_rowsx = [];

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        text: 'Mark Closed',
                        className: 'btn btn-primary closed',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to Mark as Closed!',
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
                                        $.ajax({
                                            url:"{{route('admin.delivery.sdn.bulk_closed')}}",
                                            method:'POST',
                                            data:{
                                                'sdn_ids':selected_rowsx,
                                                '_token':'{{ csrf_token() }}'
                                            }
                                        }).done(function (data) {
                                            if (data.status == 1) {
                                                            toastr.success(data.success, 'Success!', {
                                                                positionClass: 'toast-bottom-center',
                                                                containerId: 'toast-bottom-center'
                                                            });
                                                        } else {
                                                            toastr.error(data.error, 'Error!', {
                                                                positionClass: 'toast-top-center',
                                                                containerId: 'toast-top-center'
                                                            });
                                                        }
                                                        selected_rowsx = [];

                                                        table.rows().deselect();
                                                        table.draw(true);
                                                        table.button('.closed').disable();
                                        });
                                    }
                                });
                        }
                    },

                    //--------------------------------------

                    {
                        text: 'Resolved',
                        className: 'btn btn-primary resolved',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            swal({
                                title: 'Are You Sure?',
                                text: 'Select Yes to Mark as Resolved!',
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
                                    $.ajax({
                                        url:"{{route('admin.delivery.sdn.bulk_resolved')}}",
                                        method:'POST',
                                        data:{
                                            'sdn_ids':selected_rowsx,
                                            '_token':'{{ csrf_token() }}'
                                        }
                                    }).done(function (data) {
                                        if (data.status == 1) {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        } else {
                                            toastr.error(data.error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }
                                        selected_rowsx = [];

                                        table.rows().deselect();
                                        table.draw(true);
                                        table.button('.resolved').disable();
                                    });
                                }
                            });
                        }
                    },

                    //--------------------------------------

                    {
                        extend: 'excel',
                        title: 'Station Deposit Notes',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
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

                                var index = $.inArray(id, selected_rowsx);

                                if (index === -1) {
                                    selected_rowsx.push(id);
                                }

                                table.button('.closed').enable();
                            }
                        });
                    }
                }
                ,
                    {
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

                                var index = $.inArray(id, selected_rowsx);

                                if (index !== -1) {
                                    selected_rowsx.splice(index, 1);
                                }

                                if (selected_rowsx.length == 0) {
                                    table.button('.closed').disable();
                                }
                            }
                        });
                    }
                }
                , 'reset'],
                    select: {
                        info: false,
                        style: 'multi',
                        selector: 'td.select-checkbox',
                        className: 'selected bg-primary bg-lighten-5 primary'
                    },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.delivery.sdn.list') }}',
                    data: function (d) {
                        d.scan_sdn = $('#scan_sdn').val();
                        d.scan_dncc = $('#scan_dncc').val();
                        d.scan_rncc = $('#scan_rncc').val();
                        d.search_tracking = $('#search_tracking').val();
                        d.search_tracking_retail = $('#search_tracking_retail').val();
                        
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.search_date_from_deposited = $('input[name="search_date_from_deposited_formatted"]').val();
                        d.search_date_to_deposited = $('input[name="search_date_to_deposited_formatted"]').val();
                    }
                },
                rowId: 'sdn_id',
                order: [[2, 'desc']],
                columns: [
                    {data: 'id', orderable: false ,class:'text-center align-middle select p-1 serial_number',searchable: false, targets: 0, render: function (data, type, row) {
                            return '';
                        }},

                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'sdn', name: 'station_deposit_notes.id', class: 'align-middle text-center sdn'},
                    {data: 'sdn_type', name: 'station_deposit_notes.sdn_type', class: 'align-middle sdn_type', orderable: false},
                    
                    {data: 'hub', name: 'oc.name', class: 'align-middle hub'},
                    {
                        data: 'dncc_link',
                        name: 'station_deposit_notes.dncc_count',
                        class: 'align-middle dncc_link text-center'
                    },
                    {
                        data: 'delivered_shipments_link',
                        name: 'station_deposit_notes.sdn_delivered_shipments',
                        class: 'align-middle delivered_shipments_link text-center'
                    },
                    {data: 'sdn_amount', name: 'station_deposit_notes.sdn_amount', class: 'align-middle sdn_amount'},
                    {
                        data: 'sdn_deposit_amount',
                        name: 'station_deposit_notes.sdn_deposit_amount',
                        class: 'align-middle sdn_deposit_amount'
                    },
                    // { data:'sdn_expense' ,name: 'sdn_expense', class: 'align-middle sdn_expense'},
                    // { data:'sdn_net_amount' ,name: 'station_deposit_notes.sdn_net_amount', class: 'align-middle sdn_net_amount'},
                    {data: 'deposited_by', name: 'admins.name', class: 'align-middle deposited_by'},
                    {data: 'created_at', name: 'station_deposit_notes.created_at', class: 'align-middle created_at'},
                    {data: 'resolved_by', name: 'admins.name', class: 'align-middle resolved_by'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    // {data: 'zone', name: 'zones.id', class: 'align-middle zone'},
                    {data: 'adjustment_date', name: 'sdna.date', class: 'align-middle adjustment_date'},
                    {
                        data: 'sdn_adjustment_amount',
                        name: 'station_deposit_notes.adjustment_amount',
                        class: 'align-middle adjustment_amount'
                    },
                    {
                        data: 'adjusted_reference_link',
                        name: 'station_deposit_notes.adjustment_ref',
                        class: 'align-middle text-center adjustment_ref',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'difference_amount',
                        name: 'difference_amount',
                        class: 'align-middle difference_amount',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'deposit_slip',
                        name: 'deposit_slip',
                        class: 'align-middle deposit_slip',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'aging', name: 'aging', class: 'align-middle aging', orderable: false, searchable: false},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false},
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                    if (data.status != 'Resolved') {
                        $('td:eq(0)', row).addClass('select-checkbox');

                        if ($.inArray(data.id, selected_rows) !== -1) {
                            table.row(row).select();
                        }
                    }

                $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                if ($.inArray(data.id, selected_rowsx) !== -1) {
                    table.row(row).select();
                }
                },
                drawCallback: function (settings) {
                    var api = new $.fn.dataTable.Api(settings);
                    var data = api.rows({page: 'current'}).data();

                    if ($('#scan_dncc').val() != '') {
                        if (data.length > 0) {
                            scan_sound(1);
                        } else {
                            scan_sound(2);
                        }
                    }
                    if ($('#scan_rncc').val() != '') {
                        if (data.length > 0) {
                            scan_sound(1);
                        } else {
                            scan_sound(2);
                      
                        }
                    }
                    if ($('#scan_sdn').val() != '') {
                        if (data.length > 0) {
                            scan_sound(1);
                        } else {
                            scan_sound(2);
                        }
                    }
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Created</option>' +
                        '<option value="1">Deposited</option>' +
                        '<option value="2">Resolved</option>' +
                        '<option value="3">Closed</option>' +
                        '</select>';
                    var sdn_type_select = '<select name="sdn_type_select" id="sdn_type_select" class="select2 form-control">' +
                        '<option value="1">COD</option>' +
                        '<option value="2">Retail</option>' +
                        '</select>';
                    // var zones = '<select name="zones" id="zones" class="select2 form-control"></select>';
                    var bank_select = '<select name="bank_select" id="bank_select" class="select2 form-control"></select>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.deposit_slip') || $(header).is('.action') || $(header).is('.difference_amount') || $(header).is('.adjustment_ref')  || $(header).is('.aging') ) {
                            $(td).appendTo($(search));
                        } else if ($(header).is('.status')) {
                            $(drop_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }  else if ($(header).is('.sdn_type')) {
                            $(sdn_type_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } 
                        else if ($(header).is('.bank')) {
                            $(bank_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        //  else if ($(header).is('.zone')) {
                        //     $(zones).appendTo($(search))
                        //         .on('change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         }).wrap(td);
                        // }
                         else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select a Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#sdn_type_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Type",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    
                    
                    // var zone_data = $.map({!! $zones !!}, function (obj) {
                    //     obj.id = obj.id;
                    //     obj.text = obj.name;

                    //     return obj;
                    // });
                    // $("#zones").prepend('<option value="" selected></option>').select2({
                    //     data:zone_data,
                    //     placeholder: "Select zone",
                    //     width: '100%',
                    //     containerCssClass: 'select-xs',
                    //     dropdownCssClass: 'form-control-sm p-0'
                    // });

                    
                    var data = $.map({!! $banks !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data = $.map({!! $banks !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#bank_select").prepend('<option value="" selected></option>').select2({
                        data: data,
                        placeholder: "Select Bank",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            var select = $('#track_form #scan_sdn').selectize({
                placeholder: 'Search SDN(s)',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                create: function (input) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(input)) {
                        return false;
                    }
                    return {
                        value: input,
                        text: input
                    }
                }
            });


            $('#search_tracking').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function () {
                if (this.value.length == 0 || this.value.length >= 6) {
                    table.draw();
                }
            });

            $('#search_tracking_retail').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function () {
                if (this.value.length == 0 || this.value.length >= 6) {
                    table.draw();
                }
            });

            

            $('#scan_dncc').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function () {
                table.draw();
            });

            $('#scan_rncc').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function () {
                table.draw();
            });



            // $('#scan_sdn').inputmask({
            //     'alias': 'integer',
            //     'allowMinus': false,
            //     'allowPlus': false
            // }).bind('input', function () {
            //     table.draw();
            // });


            $("#sdn_add_dncc #dncc_select").prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select DNCC*',
                width: '100%',
                dropdownCssClass: 'form-control-sm p-0',
                dropdownParent: $("#sdn_add_dncc")
            });


            $("#sdn_add_pncc #pncc_select").prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select RNCC*',
                width: '100%',
                dropdownCssClass: 'form-control-sm p-0',
                dropdownParent: $("#sdn_add_pncc")
            });


            {{--$('#sdn_upload_form').bind('submit',function (e) {--}}
            {{--e.preventDefault();--}}

            {{--var url = '{!! route('admin.delivery.sdn.slip') !!}';--}}

            {{--var imagefile = $('#deposit_slip').val();--}}

            {{--if(!imagefile){--}}
            {{--error = "Please select a deposit slip first!";--}}
            {{--toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--}else{--}}
            {{--$.ajax({--}}
            {{--type:'post',--}}
            {{--url:url,--}}
            {{--enctype: 'multipart/form-data',--}}
            {{--processData: false,--}}
            {{--contentType: false,--}}
            {{--cache: false,--}}
            {{--data: new FormData($(this)[0])--}}
            {{--}).done(function (data) {--}}
            {{--if(data.status == 1){--}}

            {{--table.draw('false');--}}
            {{--$('#deposit_slip').val('');--}}
            {{--$('#uploadDepositSlip').modal('hide');--}}
            {{--toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}

            {{--}else{--}}
            {{--$('#deposit_slip').val('');--}}
            {{--$('#uploadDepositSlip').modal('hide');--}}
            {{--toastr.error(data.error.deposit_slip[0], 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}

            {{--}--}}
            {{--});--}}

            {{--}--}}
            {{--});--}}

            $('#sdn_upload_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    var pressed_button = $(this.submitButton);
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to upload Deposit Slip!',
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


                            // $(form).append('<input type="hidden" name="' + pressed_button.attr('name') + '" value="' + pressed_button.attr('value') + '">');
                            $('#deposit_rows').val(selected_rows);
                            // console.log($('#upload_image').val());
                            form.submit();
                        }
                    });

                }
            });

            function printSDN(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.sdn.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        var tab = window.open('', '_blank');

                        if (!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        } else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }

            $('body').on('click', '.printSDN', function () {
                var sdn = $(this).parents('tr').attr('id');
                // console.log(sdn);
                printSDN(sdn);
            });
            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                var index = $.inArray(id, selected_rowsx);

                if (index === -1) {
                    selected_rowsx.push(id);
                }
                else {
                    selected_rowsx.splice(index, 1);
                }
                console.log(selected_rowsx);


                if (selected_rowsx.length > 0) {
                    table.button('.closed').enable();
                    table.button('.resolved').enable();
                }
                else {
                    table.button('.closed').disable();
                    table.button('.resolved').disable();
                }
            });
            $('#datatable tbody').on('click', 'tr td.dncc_link button', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if (id) {
                    $.ajax({
                        url: '{!! route('admin.delivery.sdn.dn') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'sdn_id': id,
                        }
                    })
                        .done(function (data) {
                            if (data.status == 1) {
                                var notes = '<div>RNCC Number(s) :</div>';

                                if (data.pickup_notes) {
                                    $.each(data.pickup_notes, function (index, value) {
                                        notes += '<u><a href="javascript:void(0);" class="pncc_print" dnid="' + value + '">' + value + '</a></u><br>';
                                    });
                                }
                                $('#pncc_modal .modal-body').html('');
                                $('#pncc_modal').modal('show');
                                $('#pncc_modal .modal-body').html(notes);
                            } else if (data.status == 2) {
                                var notes = '<div>DNCC Number(s) :</div>';

                                if (data.delivery_notes) {
                                    $.each(data.delivery_notes, function (index, value) {
                                        notes += '<u><a href="javascript:void(0);" class="dncc_print" dnid="' + value + '">' + value + '</a></u><br>';
                                    });
                                }
                                $('#dncc_modal .modal-body').html('');
                                $('#dncc_modal').modal('show');
                                $('#dncc_modal .modal-body').html(notes);
                            } else if (data.status == 0) {
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            } else {
                                toastr.error('Something went wrong!', 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        });
                }


            });
            $('#datatable tbody').on('click', 'tr td.adjustment_ref button', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if (id) {
                    $.ajax({
                        url: '{!! route('admin.delivery.sdn.get.adjustment_reference') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'sdn_id': id,
                        }
                    })
                        .done(function (data) {
                            if (data.status == 1) {
                                var notes = '';

                                notes += data.html
                                $('#adjustment_reference_modal .modal-body').html('');
                                $('#adjustment_reference_modal').modal('show');
                                $('#adjustment_reference_modal .modal-body').html(notes);
                            } else {
                                toastr.error('Something went wrong!', 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        });
                }

            });


            $('#datatable tbody').on('click', 'tr td.delivered_shipments_link button', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#delivered_shipments_modal .modal-body').html('');
                $('#delivered_shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.sdn.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'sdn_id': id
                    }
                })
                    .done(function (data) {
                        if (data.status == 1 || data.status == 2) {
                            var html = '<div><b>Delivered Shipment(s) :</b></div>';
                            var dn_pn_title = '';
                            if (data.status == 1) {
                                dn_pn_title = 'RNCC Number ';
                            } else {
                                dn_pn_title = 'DNCC Number ';
                            }
                            if (data.shipments) {
                                $.each(data.shipments, function (index, value) {

                                    html += dn_pn_title + index + ': <br>';

                                    $.each(value, function (ind, tracking_number) {
                                        html += '<u><a href=' + route + '?tracking_number=' + tracking_number + ' target="_blank">' + tracking_number + '</a></u><br>';
                                    });
                                });
                            }
                            $('#delivered_shipments_modal .modal-body').html(html);

                        }
                    });

            });
            $('body').on('click', 'a.dncc_print', function () {
                var id = parseInt($(this).attr('dnid'));
                printDNCC(id);
            });

            function printDNCC(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.sdn.dncc.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        var tab = window.open('', '_blank');

                        if (!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        } else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }

            var banks_list = $.map({!! $banks !!}, function (obj) {
                obj.id = obj.id;
                obj.text = obj.name;
                return obj;
            });
            var deposit_table;
            var selected_rows = [];

            var rows_count = 0;
            $('#uploadDepositSlip').on('shown.bs.modal', function (event) {
                var id = event.relatedTarget;
                var sdn = $(id).data('target-id');
                $('#sdn_id').val($(id).data('target-id'));


                deposit_table = $('#sdn_upload_table').DataTable({
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons: [{
                        title: 'Add Row',
                        className: 'btn btn-primary mb-1',
                        text: '<i class="la la-plus"></i> Add Row',
                        action: function (e) {
                            add_row();
                        }
                    }],
                    ordering: false,
                    paging: false,
                    columns: [
                        {
                            orderable: false,
                            searchable: false,
                            name: 'serial_number',
                            class: 'align-middle serial_number',
                            targets: 0,
                            render: function (data, type, row) {
                                return '';
                            }
                        },
                        {name: 'date', class: 'align-middle date date-col-width form-group', width: '20%'},
                        {name: 'bank_name', class: 'align-middle bank_name form-group'},
                        {name: 'amount', class: 'align-middle expense_amount form-group'},
                        {name: 'deposit_slip', class: 'align-middle deposit_slip form-group'},
                        {name: 'action', class: 'align-middle action'},
                    ],

                    rowCallback: function (row, data, index) {
                        var info = deposit_table.page.info();

                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                    },
                    initComplete: function () {

                        // this.api().table().columns.adjust();
                    }
                });


                function add_row() {
                    rows_count++;
                    var date_input = '<div class="form-group input-group input-group-sm mb-0"><div class="input-group-prepend"><span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left"><span class="la la-calendar-o"></span></span></div><input type="text" name="date[' + rows_count + ']" id="deposit_date_' + rows_count + '" class="form-control pickadate-short-string bg-primary border-primary white rounded-right" placeholder="Date*" data-rule-required="true" data-msg-required="Date is required"></div>';
                    var bank_select = '<select class="form-control hub_select select2" name="bank[' + rows_count + ']" data-rule-required="true" data-msg-required="Bank is required"></select>';
                    var amount_input = '<input class="form-control form-control-sm amount" name="amount[' + rows_count + ']" placeholder="Amount*" data-rule-required="true" data-msg-required="Amount is required">';
                    var deposit_slip = '<input class="form-control form-control-sm" type="file" name="deposit_slip_' + rows_count + '" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Deposit Slip is required">';
                    if (rows_count == 1) {
                        var remove = '';
                    } else {
                        var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

                    }
                    // deposit_table.row.add(0,1,2,3,4,5);
                    deposit_table.row.add([0, date_input, bank_select, amount_input, deposit_slip, remove]).node().id = rows_count;
                    deposit_table.draw(true);
                    // $('#sdn_upload_table tbody').append(html);
                    $('#DepositSlipButton').attr('disabled', false);
                    selected_rows.push(rows_count);
                    $('select[name="bank[' + rows_count + ']"]').prepend('<option value="" selected="selected"></option>').select2({
                        data: banks_list,
                        placeholder: 'Select Bank',
                        allowClear: true,
                        width: '100%',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $('#deposit_date_' + rows_count).pickadate({
                        firstDay: 1,
                        today: '',
                        clear: '',
                        close: '',
                        weekdaysShort: ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'],
                        showMonthsShort: true,
                        formatSubmit: 'yyyy-mm-dd 00:00:00',
                        hiddenSuffix: '_formatted',
                        onOpen: function () {
                            // $('#deposit_date_' + rows_count+'_root').css('top', '-262px');
                        },
                    });
                    $('input.amount').inputmask({
                        'alias': 'decimal',
                        'allowMinus': false,
                        'allowPlus': false,
                        'rightAlign': false,
                        'digits': 2,
                        'min': 0.00,
                        'max': 10000000.00
                    }).bind('keyup', function () {
                        update_upload_slip_total();
                    });
                }
            });

            function update_upload_slip_total() {
                let upload_total = 0
                $('#sdn_upload_table .amount').each(function (v) {
                    if ($(this).val() != "") {
                        upload_total += parseFloat($(this).val());
                    }
                });
                $("#upload_deposit_total").html(upload_total);
            }

            $('body').on('click', 'a.remove_row', function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, selected_rows);

                if (index !== -1) {
                    selected_rows.splice(index, 1);
                }
                deposit_table.row($(this).parents('tr')).remove().draw();
                update_upload_slip_total();
            });
            var deposit_slip_table;
            $('body').on('click', '.deposit_slip_view', function () {
                var id = $(this).parents('tr').attr('id');
                if (id) {
                    $.ajax({
                        url: '{!! route('admin.delivery.sdn.slip_view') !!}',
                        type: 'POST',
                        data: {
                            'sdn_id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status) {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        } else {
                            $('#ViewDepositSlip').modal('show');
                            deposit_slip_table = $('#deposit_slip_table').DataTable({
                                dom: 'ltipr',
                                ordering: false,
                                paging: false,
                                columns: [
                                    {
                                        orderable: false,
                                        searchable: false,
                                        name: 'serial_number',
                                        class: 'align-middle serial_number',
                                        targets: 0,
                                        render: function (data, type, row) {
                                            return '';
                                        }
                                    },
                                    {name: 'code', class: 'align-middle code form-group'},
                                    {name: 'date', class: 'align-middle date date-col-width form-group'},
                                    {name: 'bank_name', class: 'align-middle bank_name form-group'},
                                    {name: 'amount', class: 'align-middle expense_amount form-group'},
                                    {name: 'created_at', class: 'align-middle expense_created_at form-group'},
                                    {name: 'uploaded_by', class: 'align-middle expense_uploaded_by form-group'},
                                    {name: 'deposit_slip', class: 'align-middle deposit_slip form-group'}
                                ],

                                rowCallback: function (row, data, index) {
                                    var info = deposit_slip_table.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                },
                                initComplete: function () {

                                }
                            });

                            $.each(data.slips, function (index, value) {
                                deposit_slip_table.row.add([0, value.code ,value.date, value.bank, value.amount, value.created_at, value.uploaded_by, value.image]);
                                deposit_slip_table.draw(true);
                            });
                        }
                    });
                }
            });
            $('body').on('click', '.update_status_deposit', function () {
                var id = $(this).parents('tr').attr('id');
                if (id) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to Mark SDN Deposited!',
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
                            $.ajax({
                                url: '{!! route('admin.delivery.sdn.back_to_deposit') !!}',
                                type: 'POST',
                                data: {
                                    'sdn_id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if (data.status == 1) {
                                    table.draw(false);
                                    toastr.success(data.message, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.message, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            });
                        }
                    });
                }
            });

            $('body').on('click', '.update_status_resolved', function () {
                var id = $(this).parents('tr').attr('id');
                if (id) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to Mark SDN Resolved!',
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
                            $.ajax({
                                url: '{!! route('admin.delivery.sdn.resolved') !!}',
                                type: 'POST',
                                data: {
                                    'sdn_id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if (data.status == 1) {
                                    table.draw(false);
                                    toastr.success(data.message, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.message, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            });
                        }
                    });
                }
            });

            $('body').on('click', '.update_status_closed', function () {
            var id = $(this).parents('tr').attr('id');
            if (id) {
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Mark SDN Closed!',
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
                        $.ajax({
                            url: '{!! route('admin.delivery.sdn.closed') !!}',
                            type: 'POST',
                            data: {
                                'sdn_id': id,
                                '_token': '{{ csrf_token() }}'
                          }
                          }).done(function (data) {
                              if (data.status == 1) {
                                 table.draw(false);
                                  toastr.success(data.message, 'Success!', {
                                     positionClass: 'toast-bottom-center',
                                     containerId: 'toast-bottom-center'
                                 });
                             } else {
                                 toastr.error(data.message, 'Error!', {
                                     positionClass: 'toast-top-center',
                                     containerId: 'toast-top-center'
                                 });
                             }
                         });
                     }
                 });
             }
         });
            $('body').on('click', '.add_dncc', function () {
                var id = $(this).parents('tr').attr('id');
                if (id) {
                    $.ajax({
                        url: '{!! route('admin.delivery.sdn.dncc.get.add') !!}',
                        type: 'POST',
                        data: {
                            'sdn_id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status == 1) {
                            html = "";
                            $.each(data.dn, function (i, v) {
                                let dn_id = v.id.toString();
                                html += '<option value="' + dn_id + '">' + dn_id.padStart(6, 0) + '</option>';
                            });

                            $("#sdn_add_dncc #dncc_select").html(html).val("").trigger('change');
                            $("#sdn_add_dncc #dncc_remarks_input").val("");
                            $("#sdn_add_dncc #sdn_id_for_add_dncc").val(id);
                            $("#AddDNCCModal").modal('show');
                        } else {
                            toastr.error(data.message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });

            $('body').on('click', '.add_pncc', function () {
                var id = $(this).parents('tr').attr('id');
                if (id) {
                    $.ajax({
                        url: '{!! route('admin.delivery.sdn.pncc.get.add') !!}',
                        type: 'POST',
                        data: {
                            'sdn_id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status == 1) {
                            html = "";
                            $.each(data.dn, function (i, v) {
                                let dn_id = v.id.toString();
                                html += '<option value="' + dn_id + '">' + dn_id + '</option>';
                            });

                            $("#sdn_add_pncc #pncc_select").html(html).val("").trigger('change');
                            $("#sdn_add_pncc #pncc_remarks_input").val("");
                            $("#sdn_add_pncc #sdn_id_for_add_pncc").val(id);
                            $("#AddPNCCModal").modal('show');
                        } else {
                            toastr.error(data.message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });


            var remove_dncc_table;
            var dncc_selected_rows = [];
            $('body').on('click', '.remove_dncc', function () {
                var id = $(this).parents('tr').attr('id');
                if (id) {
                    $.ajax({
                        url: '{!! route('admin.delivery.sdn.dncc.get.remove') !!}',
                        type: 'POST',
                        data: {
                            'sdn_id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status == 0) {
                            toastr.error(data.message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        } else {
                            $('#RemoveDNCCModal').modal('show');
                            $("#sdn_id_for_remove_dncc").val(id);
                            remove_dncc_table = $('#remove_dncc_table').DataTable({
                                dom: 'ltipr',
                                ordering: false,
                                paging: false,
                                columns: [
                                    {
                                        orderable: false,
                                        searchable: false,
                                        class: 'text-center align-middle select p-1',
                                        targets: 0,
                                        render: function (data, type, row) {
                                            return '';
                                        }
                                    },
                                    {
                                        orderable: false,
                                        searchable: false,
                                        name: 'serial_number',
                                        class: 'align-middle serial_number',
                                        targets: 0,
                                        render: function (data, type, row) {
                                            return '';
                                        }
                                    },
                                    {name: 'dncc', class: 'align-middle dncc form-group'},
                                    {name: 'delivered_shipments', class: 'align-middle delivered_shipments form-group'},
                                    {name: 'amount', class: 'align-middle expense_amount form-group'},
                                ],

                                rowCallback: function (row, d, index) {
                                    var info = remove_dncc_table.page.info();
                                    $('td:eq(0)', row).addClass('select-checkbox');

                                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                                    if ($.inArray(parseInt(d[0]), dncc_selected_rows) !== -1) {
                                        remove_dncc_table.row(row).select();
                                    } else {
                                        remove_dncc_table.row(row).deselect();
                                    }

                                },
                                initComplete: function () {

                                }
                            });

                            $.each(data.dncc, function (index, value) {
                                remove_dncc_table.row.add([value.id, , value.id.toString().padStart(6, 0), value.delivered_shipments, value.received_cod_amount]);
                                remove_dncc_table.draw(true);
                            });
                        }
                    });
                }
            });
            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.closed').enable();
                }
                else {
                    table.button('.closed').disable();
                }
            });

            $('#RemoveDNCCModal #remove_dncc_table').on('click', 'tbody tr td.select-checkbox', function () {

                var id = parseInt(remove_dncc_table.row($(this).parents('tr')).data()[0]);

                var index = $.inArray(id, dncc_selected_rows);


                if (index === -1) {
                    if (dncc_selected_rows.length + 1 != remove_dncc_table.rows().count()) {
                        dncc_selected_rows.push(id);
                    }
                } else {
                    dncc_selected_rows.splice(index, 1);
                }

                if (dncc_selected_rows.length > 0) {
                    $("#remove_dncc_btn").prop('disabled', false);
                } else {
                    $("#remove_dncc_btn").prop('disabled', true);
                }

                remove_dncc_table.draw(false);

            });

            $('#RemoveDNCCModal ').on('hidden.bs.modal', function () {
                remove_dncc_table.clear();
                remove_dncc_table.destroy();
                dncc_selected_rows = [];
            });

            var remove_pncc_table;
            var pncc_selected_rows = [];
            $('body').on('click', '.remove_pncc', function () {
                var id = $(this).parents('tr').attr('id');
                if (id) {
                    $.ajax({
                        url: '{!! route('admin.delivery.sdn.pncc.get.remove') !!}',
                        type: 'POST',
                        data: {
                            'sdn_id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status == 0) {
                            toastr.error(data.message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        } else {
                            $('#RemovePNCCModal').modal('show');
                            $("#sdn_id_for_remove_pncc").val(id);
                            remove_pncc_table = $('#remove_pncc_table').DataTable({
                                dom: 'ltipr',
                                ordering: false,
                                paging: false,
                                columns: [
                                    {
                                        orderable: false,
                                        searchable: false,
                                        class: 'text-center align-middle select p-1',
                                        targets: 0,
                                        render: function (data, type, row) {
                                            return '';
                                        }
                                    },
                                    {
                                        orderable: false,
                                        searchable: false,
                                        name: 'serial_number',
                                        class: 'align-middle serial_number',
                                        targets: 0,
                                        render: function (data, type, row) {
                                            return '';
                                        }
                                    },
                                    {name: 'pncc', class: 'align-middle pncc form-group'},
                                    {name: 'shipments', class: 'align-middle shipments form-group'},
                                    {name: 'amount', class: 'align-middle expense_amount form-group'},
                                ],

                                rowCallback: function (row, d, index) {
                                    var info = remove_pncc_table.page.info();
                                    $('td:eq(0)', row).addClass('select-checkbox');

                                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                                    if ($.inArray(parseInt(d[0]), pncc_selected_rows) !== -1) {
                                        remove_pncc_table.row(row).select();
                                    } else {
                                        remove_pncc_table.row(row).deselect();
                                    }

                                },
                                initComplete: function () {

                                }
                            });

                            $.each(data.pncc, function (index, value) {
                                remove_pncc_table.row.add([value.id, , value.id, value.shipments, value.amount]);
                                remove_pncc_table.draw(true);
                            });
                        }
                    });
                }
            });

            $('#RemovePNCCModal #remove_pncc_table').on('click', 'tbody tr td.select-checkbox', function () {

                var id = parseInt(remove_pncc_table.row($(this).parents('tr')).data()[0]);

                var index = $.inArray(id, pncc_selected_rows);


                if (index === -1) {
                    if (pncc_selected_rows.length + 1 != remove_pncc_table.rows().count()) {
                        pncc_selected_rows.push(id);
                    }
                } else {
                    pncc_selected_rows.splice(index, 1);
                }

                if (pncc_selected_rows.length > 0) {
                    $("#remove_pncc_btn").prop('disabled', false);
                } else {
                    $("#remove_pncc_btn").prop('disabled', true);
                }

                remove_pncc_table.draw(false);

            });

            $('#RemovePNCCModal ').on('hidden.bs.modal', function () {
                remove_pncc_table.clear();
                remove_pncc_table.destroy();
                pncc_selected_rows = [];
            });

            $('#ViewDepositSlip').on('hidden.bs.modal', function () {
                deposit_slip_table.clear();
                deposit_slip_table.destroy();
            });
            $('#uploadDepositSlip').on('hidden.bs.modal', function () {
                deposit_table.clear();
                deposit_table.destroy();
                $("#upload_deposit_total").html(0);
                selected_rows = [];
            });

            var sdn_adjustment_table;
            var selected_adjustment_rows = [];
            $('#datatable tbody').on('click', 'button.adjustment_add', function () {
                var sdn_id = $(this).parents('tr').attr('id');
                var options_html = "";
                if (sdn_id) {
                    var adjustment_rows_count = 0;
                    $.ajax({
                        url: '{!! route('admin.delivery.sdn.get.petty_cash_statements') !!}',
                        method: 'POST',
                        data: {
                            'id': sdn_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if (data.status == 1) {
                                $.each(data.data, function (key, value) {
                                    options_html += "<option value='" + value.id + "' data-date='" + value.date + "' data-amount='" + value.amount + "' >" + value.id + "</option>";
                                });
                                sdn_adjustment_table = $('#sdn_adjustment_table').DataTable({
                                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                                    buttons: [{
                                        title: 'Add Row',
                                        className: 'btn btn-primary mb-1',
                                        text: '<i class="la la-plus"></i> Add Row',
                                        action: function (e) {
                                            add_adjustment_row();
                                        }
                                    }],
                                    ordering: false,
                                    paging: false,
                                    columns: [
                                        {
                                            orderable: false,
                                            searchable: false,
                                            name: 'serial_number',
                                            class: 'align-middle serial_number',
                                            targets: 0,
                                            render: function (data, type, row) {
                                                return '';
                                            }
                                        },
                                        {name: 'statement', class: 'align-middle statement form-group', width: '20%'},
                                        {name: 'date', class: 'align-middle date form-group'},
                                        {name: 'amount', class: 'align-middle amount form-group'},
                                        {name: 'action', class: 'align-middle action'},
                                    ],

                                    rowCallback: function (row, data, index) {
                                        var info = sdn_adjustment_table.page.info();

                                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                    },
                                    footerCallback: function (row, data, start, end, display) {
                                        var api = this.api();
                                        api.columns('.statement', {
                                            page: 'current'
                                        }).every(function () {
                                            $(this.footer()).html('Total Amount');
                                        });
                                        api.columns('.amount', {
                                            page: 'current'
                                        }).every(function () {
                                            amount = this
                                                .data()
                                                .reduce(function (a, b) {
                                                    var x = parseFloat(a) || 0;
                                                    var y = parseFloat(b) || 0;
                                                    return x + y;
                                                }, 0);
                                            $(this.footer()).html(amount);
                                        });
                                    }
                                });


                                $('#sdn_id_for_adjustment').val(sdn_id);
                                $('#AddAdjustmentModal').modal('show');

                                function add_adjustment_row() {
                                    adjustment_rows_count++;
                                    var statement_select = '<select class="form-control statement_select select2 unique_statement" name="statement[' + adjustment_rows_count + ']" data-rule-required="true" data-msg-required="Statement is required"></select>';
                                    var amount_input = "<div class='amount_input'><div>";
                                    if (adjustment_rows_count == 1) {
                                        var remove = '';
                                    } else {
                                        var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger adjustment_remove_row"><i class="la la-close"></i></a>';

                                    }
                                    sdn_adjustment_table.row.add([0, statement_select, '', '', remove]).node().id = adjustment_rows_count;
                                    sdn_adjustment_table.draw(true);
                                    selected_adjustment_rows.push(adjustment_rows_count);
                                    $('select[name="statement[' + adjustment_rows_count + ']"]').prepend('<option value="" selected="selected" data-date="" data-amount=""></option>' + options_html).select2({
                                        placeholder: 'Select Statement',
                                        width: '100%',
                                        dropdownCssClass: 'form-control-sm p-0'
                                    }).bind('change', function () {
                                        var date = $(this).find("option:selected").attr("data-date");
                                        var amount = $(this).find("option:selected").attr("data-amount");
                                        sdn_adjustment_table.row($(this).closest("tr")).data()[2] = date;
                                        sdn_adjustment_table.row($(this).closest("tr")).data()[3] = amount;
                                        $(this).closest("td").next("td").html(date);
                                        $(this).closest("td").next("td").next("td").html(amount);
                                        sdn_adjustment_table.draw();
                                    });
                                }


                                add_adjustment_row();
                            } else {
                                toastr.error(data.message, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        });


                }
            });

            $('body').on('click', 'a.adjustment_remove_row', function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, selected_adjustment_rows);

                if (index !== -1) {
                    selected_adjustment_rows.splice(index, 1);
                }
                sdn_adjustment_table.row($(this).parents('tr')).remove().draw();
            });

            $('#AddAdjustmentModal').on('hidden.bs.modal', function () {
                sdn_adjustment_table.clear();
                sdn_adjustment_table.destroy();
                selected_adjustment_rows = [];
            });

            $.validator.addMethod("unique_statement", function (value, element) {
                var parentForm = $(element).closest('form');
                var timeRepeated = 0;
                if (value != '') {
                    $(parentForm.find('.unique_statement')).each(function () {
                        if ($(this).val() === value && value != 0) {
                            timeRepeated++;
                        }
                    });
                }
                return timeRepeated === 1 || timeRepeated === 0;

            }, "Statement Can Not Be Duplicate");

            var sdn_form;
            sdn_form = $('#sdn_adjustment_add').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to Adjust SDN!',
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
                            $('#sdn_rows_for_adjustment').val(selected_adjustment_rows);
                            form.submit();
                        }
                    });

                }
            });

            $("#sdn_add_dncc").validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to Add DNCC To SDN!',
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
                            form.submit();
                        }
                    });

                }
            });

            $("#sdn_add_pncc").validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to Add RNCC To SDN!',
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
                            form.submit();
                        }
                    });

                }
            });

            $("#sdn_remove_dncc").validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    if (dncc_selected_rows.length > 0) {
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to Remove DNCC From SDN!',
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
                                $("#sdn_remove_dncc #dncc_id_for_remove_dncc").val(dncc_selected_rows);
                                form.submit();
                            }
                        });
                    }
                }
            });

            $("#sdn_remove_pncc").validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    if (pncc_selected_rows.length > 0) {
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to Remove RNCC From SDN!',
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
                                $("#sdn_remove_pncc #pncc_id_for_remove_pncc").val(pncc_selected_rows);
                                form.submit();
                            }
                        });
                    }
                }
            });

            $('#track_form').bind('submit', function (e) {
                e.preventDefault();
                table.draw();
            });

            $('#datatable tbody').on('click', 'tr td button.view_logs', function () {
                var id = parseInt($(this).parents('tr').attr('id'));



                $.ajax({
                    url: '{!! route('admin.delivery.sdn.status_logs') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'sdn_id': id
                    }
                })
                    .done(function (data) {

                        if (data.status == 1) {
                            $('#status_logs_modal .modal-body').html('');
                            $('#status_logs_modal').modal('show');
                            $('#status_logs_modal_title span').text(data.sdn_id);
                            var html = '<div class="row"><div class="col-12"><table class="table table-sm table-bordered border"><thead><tr><th class="color primary text-center">Status</th><th class="color primary">Updated By</th><th class="color primary">Updated At</th></tr></thead><tbody>';


                            if (data.logs) {
                                $.each(data.logs, function (index, value) {
                                    html += '<tr><td>'+ value.status +'</td><td>'+ value.updated_by +'</td><td>'+ value.date +'</td></tr>';
                                });
                            }
                            html += '</tbody></table></div></div>';
                            $('#status_logs_modal .modal-body').html(html);
                        }
                        else{
                            toastr.error(data.message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });

            });
        });
    </script>
@endsection