
@extends('admin.layout.master')
@section('title','Quick Receiving')

@section('content')
    <h1 class="mb-1">
        Quick Receiving
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" autofocus class="form-control" placeholder="Scan Delivery Note Number" name="scan_delivery_note" id="scan_delivery_note">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" readonly class="form-control" placeholder="Scan Tracking Number" name="scan_tracking" id="scan_tracking">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>


                </div>
                <div class="row mb-2 justify-content-center">
                    <div class="col-3 text-center border-right-black">
                        <b>Delivery Note #</b>
                        <b id="delivery_note_label"></b>
                    </div>

                    <div class="col-3 text-center">
                        <b>Scanned :</b>
                        <b id="remaining_scanned">0</b> / <b id="total_to_scan">0</b>
                    </div>

                </div>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Status Date</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Shipper Name</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: false,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false},
                    {name: 'status', class: 'align-middle status', orderable: false, searchable: false},
                    {name: 'reason', class: 'align-middle reason', orderable: false, searchable: false},
                    {name: 'remarks', class: 'align-middle remarks', orderable: false, searchable: false},
                    {name: 'status_date', class: 'align-middle status_date', orderable: false, searchable: false},
                    {name: 'origin', class: 'align-middle origin', orderable: false, searchable: false},
                    {name: 'destination', class: 'align-middle destination', sortable: false, orderable: false, searchable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false, searchable: false},
                    {name: 'shipper_name', class: 'align-middle shipper_name', sortable: false, orderable: false, searchable: false},
                ],
                initComplete: function() {

                }
            });

            $('#scan_tracking').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#scan_delivery_note').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
        });
    </script>
@endsection