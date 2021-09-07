@extends('client.layout.master')
@section('title','Receive Return Sheet')

@section('content')
    <h1 class="mb-1">
        Receive Return Sheet
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')

                <div id="camera_scan" class="d-none">
                    <div id="camera_view" class="camera_view"></div>
                </div>

                <form action="#" id="scan_tracking_form">
                    <div class="row justify-content-center mb-2">
                        <div class="col-3">
                            <fieldset>
                                <input type="text" class="form-control" placeholder="Scan Tracking Number" id="scan_tracking">
                            </fieldset>
                        </div>

{{--                        <div class="col-1">--}}
{{--                            <a href="#" id="camera_scan_initiate" class="d-block text-right" tabindex="-1">--}}
{{--                                <i class="ft-camera h1"></i>--}}
{{--                            </a>--}}
{{--                        </div>--}}
                    </div>
                </form>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>
                <form id="receive_shipments_From" class="" method="post" action="{{ route('cod.return.sheet.receive.submit') }}">
                    <div class="row justify-content-center">
                        @csrf
                        <input type="hidden" name="shipment_ids" id="shipment_ids">
                        <div class="col-3">
                            <button type="submit" name="receive" value="receive" class="btn btn-primary btn-block ">Receive</button>

                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">

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
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
{{--    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>--}}
{{--    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {
            var shipment_ids = [];
            var tracking_numbers = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                autoWidth : false,
                scrollX: true,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
                    {name: 'destination', class: 'align-middle destination', orderable: false},
                    {name: 'consignee_name', class: 'align-middle consignee_name', orderable: false},
                    {name: 'phone', class: 'align-middle phone', orderable: false},
                    {name: 'address', class: 'align-middle address', orderable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false},
                    {name: 'status', class: 'align-middle status', orderable: false},
                    {name: 'action', class: 'align-middle action',orderable: false,searchable:false}
                ],
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#scan_tracking_form').on('submit',function (e) {
                e.preventDefault();
                var scan = $('#scan_tracking');
                var tracking = scan.val();

                if (tracking != '') {
                    var index = $.inArray(tracking, tracking_numbers);
                    if (index === -1) {
                        scan.attr('disabled', true);
                        blockPagePermanently();
                        $.ajax({
                            url: '{{route('cod.return.sheet.receive.shipment_info')}}',
                            type: 'POST',
                            data: {
                                'tracking': tracking,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    UnblockPagePermanently();
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                } else {
                                    var rowNo = table.rows().count();
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger returnnoterow"><i class="la la-close"></i></a>';

                                    var row = table.row.add([rowNo + 1, data.tracking_number, data.destination, data.consignee_name, data.phone, data.address, data.amount, data.shipment_status, remove]).node().id = data.shId;
                                    table.draw(false);
                                    $('tr#' + row).attr('class', data.class);
                                    scan_sound(1);
                                    shipment_ids.push(data.shId);
                                    tracking_numbers.push(tracking);
                                    UnblockPagePermanently();
                                }

                                scan.val('');
                                scan.attr('disabled', false);
                                scan.focus();
                            });
                    }
                }
            });
            $('body').on('click','a.returnnoterow',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, shipment_ids);
                if (index !== -1) {
                    shipment_ids.splice(index, 1);
                }
                table.row( $(this).parents('tr') ).remove().draw();
            });


            $('#receive_shipments_From').bind('submit', function(event) {
                var this_form = this;
                event.preventDefault();
                var count = table.rows().count();

                if(count > 0) {
                    $('#receive_shipments_From button[type="submit"]').attr('disabled', 'disabled');
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to receive selected Shipments!',
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
                            blockPagePermanently();
                            $('#receive_shipments_From input#shipment_ids').val(shipment_ids);
                            this_form.submit();
                        }
                    });
                }else{
                    var error = "Select at-least one shipment!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                }
            });
            //
            // $('#camera_scan_initiate').bind('click', function() {
            //     if ($('#camera_scan').hasClass('d-none')) {
            //         $('#camera_scan').removeClass('d-none');
            //
            //         camera_scanning_start('#camera_view');
            //     }
            //     else {
            //         $('#camera_scan').addClass('d-none');
            //
            //         camera_scanning_stop();
            //     }
            // });
        });

        // function camera_scan_detected(tracking_number) {
        //     $('#scan_tracking').val(tracking_number);
        //
        //     $('#scan_tracking_form').trigger('submit');
        // }
    </script>
@endsection