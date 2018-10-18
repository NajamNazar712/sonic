
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

                            <form action="#" id="delivery_note_form">
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
                                    <th class="border-primary border-darken-1">Tracking No.</th>
                                    <th class="border-primary border-darken-1">Order ID</th>
                                    <th class="border-primary border-darken-1">Service Type</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Pickup Address</th>
                                    <th class="border-primary border-darken-1">Product Type</th>
                                    <th class="border-primary border-darken-1">Item Description</th>
                                </tr>
                                </thead>
                            </table>
                            <form id="create_delivery_note_form" class="" method="post" action="{{ route('cod.shipment.receiving_sheet.create') }}">
                                <div class="row justify-content-center">
                                    @csrf
                                    <input type="hidden" name="hub_id" id="hub_id">
                                    <input type="hidden" name="shipment_ids" id="shipment_ids">
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
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
                    @if(session('print'))
            var pid = '{{ session('print') }}';
            print(pid);
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.print') !!}',
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
                    @endif

            var shipment_ids = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true, scrollY: '350px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'tracking_number', class: 'align-middle tracking_number'},
                    {name: 'destination', class: 'align-middle destination'},
                    {name: 'consignee_name', class: 'align-middle consignee_name'},
                    {name: 'phone', class: 'align-middle phone'},
                    {name: 'address', class: 'align-middle address'},
                    {name: 'amount', class: 'align-middle amount'},
                    {name: 'service_type', class: 'align-middle service_type'},
                    {name: 'status', class: 'align-middle status'},
                    {name: 'remarks', class: 'align-middle remarks'},
                    {name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
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
            $('#delivery_note_form').on('submit',function (e) {
                e.preventDefault();
                var scan = $('#scan_tracking');
                var tracking = scan.val();
                var hub_id = $('#hub_id').val();

                if (tracking != '') {
                    scan.attr('disabled', true);

                    if(table.row().count() == 0) {
                        $.ajax({
                            url:'{{route('admin.delivery.note.shipment.info')}}',
                            type:'GET',
                            dataType:'JSON',
                            data: {
                                'tracking':tracking
                            }
                        }).done(function (data) {

                            if(data.status == 1){
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }else{
                                var rowNo = table.rows().count();
                                var remove = '<a href="#" class="deliverynoterow">Delete</a>';
                                table.row.add([rowNo+1,data.tracking_number,data.destination,data.consignee_name,data.phone,data.address,data.amount,data.service_type,data.shipment_status,data.remarks,remove]).node().id = data.shId;
                                table.draw(false);
                                shipment_ids.push(data.shId);
                                $('#hub_id').val(data.hub);
                            }

                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                        });
                    } else {

                        if(table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking)) === -1){
                            // $('#hub_id').val('');
                            $.ajax({
                                url:'{{route('admin.delivery.note.shipment.info')}}',
                                type:'GET',
                                dataType:'JSON',
                                data: {
                                    'tracking':tracking,
                                    'hub_id':hub_id
                                }
                            }).done(function (data) {

                                if(data.status == 1){

                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }else{
                                    var rowNo = table.rows().count();
                                    var remove = '<a href="#" class="deliverynoterow">Delete</a>';
                                    table.row.add([rowNo+1,data.tracking_number,data.destination,data.consignee_name,data.phone,data.address,data.amount,data.service_type,data.shipment_status,data.remarks,remove]).node().id = data.shId;
                                    table.draw(false);
                                    shipment_ids.push(data.shId);
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
            $('body').on('click','a.deliverynoterow',function () {
                var rid = $(this).parents('tr').attr('id');

                table.row( $(this).parents('tr') ).remove().draw();
                shipment_ids.splice( $.inArray(rid, shipment_ids), 1 );
            });


            $('#create_delivery_note_form').bind('submit', function(event) {
                event.preventDefault();
                // riderFormValid();
                var count = 0;
                var this_form = this;
                count = table.rows().count();

                var errors = 0;
                var rider = $('#rider_name').val();
                var route = $('#route').val();


                if (route !== '' && route !== null) {

                    $('#route_error').css('display', 'none');
                } else {
                    var error = "Route not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#route_error').css('display', 'block');
                }


                if(count > 0) {
                    if (errors == 0) {

                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to create the Delivery Note!',
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
                                $('#create_delivery_note_form button[type="submit"]').attr('disabled', 'disabled');
                                $('#create_delivery_note_form input#shipment_ids').val(shipment_ids);
                                $('#create_delivery_note_form input#selected_rider_id').val(rider);
                                $('#create_delivery_note_form input#selected_route_id').val(route);

                                this_form.submit();
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