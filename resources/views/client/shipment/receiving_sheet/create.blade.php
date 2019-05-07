
@extends('client.layout.master')
@section('title','Create Receiving Sheet')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    Create Receiving Sheet
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('client.inc.messages')

                            <form action="#" id="receiving_sheet_form">
                                <div class="row justify-content-center mb-2">
                                    <div class="col-3">
                                        <fieldset>
                                            <input type="text" class="form-control" placeholder="Scan Tracking Number" id="scan_tracking">
                                        </fieldset>
                                    </div>
                                </div>
                            </form>



                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Order ID</th>
                                    <th class="border-primary border-darken-1">Service Type</th>
                                    <th class="border-primary border-darken-1">Pickup Address</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Booking Date</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                            <form id="create_receiving_sheet_form" class="" method="post">
                                <div class="row justify-content-center">
                                    @csrf
                                    <input type="hidden" name="pickup_address_id" id="pickup_address_id">
                                    {{--<input type="hidden" name="shipment_ids" id="shipment_ids">--}}
                                    <div class="col-3">
                                        <button type="submit" class="btn btn-primary btn-block ">Submit &amp; Print</button>
                                    </div>

                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            function print(id) {
                $.ajax({
                    url: '{!! route('cod.shipment.receiving_sheet.print') !!}',
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
                    });
            }


            var shipment_ids = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
                    {name: 'order_id', class: 'align-middle order_id', orderable: false},
                    {name: 'service_type', class: 'align-middle service_type', orderable: false},
                    {name: 'origin', class: 'align-middle origin', orderable: false},
                    {name: 'pickup_address', class: 'align-middle pickup_address', orderable: false},
                    {name: 'product_type', class: 'align-middle product_type', orderable: false},
                    {name: 'item_description', class: 'align-middle item_description', orderable: false},
                    {name: 'action', class: 'align-middle action',orderable: false,searchable:false}
                ],
                rowCallback: function(row, data, index) {
                    // var info = table.page.info();
                    //
                    // $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().table().columns.adjust();
                }
            });

            $('#scan_tracking').on('change',function() {
                $(this).val($(this).val().trim());
            });
            $('input#scan_tracking').focus();
            $('#receiving_sheet_form').on('submit',function (e) {
                e.preventDefault();
                var scan = $('#scan_tracking');
                var tracking = scan.val();
                var pickup_address_id = $('#pickup_address_id').val();

                if (tracking != '') {
                    scan.attr('disabled', true);

                    if(table.row().count() == 0) {
                        $.ajax({
                            url:'{{route('cod.shipment.receiving_sheet.info')}}',
                            type:'POST',
                            data: {
                                'tracking':tracking,
                                '_token': '{!! csrf_token() !!}'
                            }
                        }).done(function (data) {

                            if(data.status == 1){
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }else{
                                var rowNo = table.rows().count();
                                var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger deleterow"><i class="la la-close"></i></a>';
                                table.row.add([rowNo+1,data.tracking_number,data.order_id,data.service_type,data.address,data.origin,data.destination,data.booking,remove]).node().id = data.shId;
                                table.draw(false);
                                shipment_ids.push(data.shId);
                                $('#pickup_address_id').val(data.pickup_address);
                            }

                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                        });
                    } else {

                        if(table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking)) === -1){
                            $.ajax({
                                url:'{{route('cod.shipment.receiving_sheet.info')}}',
                                type:'POST',
                                data: {
                                    'tracking':tracking,
                                    'pickup_address_id':pickup_address_id,
                                    '_token':'{!! csrf_token() !!}'
                                }
                            }).done(function (data) {
                                if(data.status == 1){
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }else{
                                    var rowNo = table.rows().count();
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger deleterow"><i class="la la-close"></i></a>';
                                    table.row.add([rowNo + 1,data.tracking_number,data.order_id,data.service_type,data.address,data.origin,data.destination,data.booking,remove]).node().id = data.shId;
                                    table.draw(false);
                                    shipment_ids.push(data.shId);
                                    table.order([0, 'desc']).draw();

                                }

                                scan.val('');
                                scan.attr('disabled', false);
                                scan.focus();
                            });
                        }else{
                            var error = 'Tracking Number already scanned!';
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                        }
                    }
                }
            });
            $('body').on('click','a.deleterow',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, shipment_ids);
                if (index !== -1) {
                    shipment_ids.splice(index, 1);
                }
                table.row( $(this).parents('tr') ).remove().draw();
            });


            $('#create_receiving_sheet_form').bind('submit', function(event) {
                event.preventDefault();
                // riderFormValid();
                var count = 0;
                var this_form = this;
                count = table.rows().count();

                var errors = 0;

                if(count > 0) {
                    if (errors == 0) {

                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to create the Receiving Sheet!',
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
                            if(confirm){
                                $('#create_receiving_sheet_form button[type="submit"]').attr('disabled', 'disabled');
                                $('#create_receiving_sheet_form input#shipment_ids').val(shipment_ids);
                                swal({
                                    title: 'Please Wait!',
                                    text: 'Your Receiving Sheet is being created!',
                                    icon: 'info',
                                    buttons: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });

                                $.ajax({
                                    url: '{!! route('cod.shipment.receiving_sheet.store') !!}',
                                    method: 'POST',
                                    data: {
                                        'shipment_ids[]': shipment_ids,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                })
                                    .done(function(data) {
                                        $('#create_receiving_sheet_form button[type="submit"]').attr('disabled', false);

                                        swal.close();

                                        if (data.status == 0) {
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                            $('#pickup_address_id').val('');

                                            table.clear().draw('false');

                                            print(data.receiving_sheet_id);
                                        }
                                        else {
                                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        }
                                    });
                            }
                        });


                    }
                }else{
                    var error = "Select at-least one shipment!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                }



            });
        });
    </script>
@endsection