
@extends('admin.layout.master')
@section('title','Create Delivery Note')

@section('content')
    <h1 class="mb-1">
        Create Delivery Note
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form action="#" id="delivery_note_form">
                <div class="row justify-content-center mb-2">
                    <div class="col-3">
                        <fieldset>
                            <input type="text" class="form-control" placeholder="Scan Tracking Number" id="scan_tracking">
                        </fieldset>
                    </div>
                </div>
                </form>
                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="rider_name" id="rider_name" class="form-control select2" required >
                                @foreach($riders as $rider)
                                    <option value="{{$rider->id}}" data-id="{{$rider->route_id}}">{{$rider->name}}</option>
                                @endforeach
                            </select>
                            <div class="danger" id="rider_error" style="display:none;">This field is required</div>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="route" id="route" class="form-control select2" required>
                                @foreach($routes as $route)
                                    <option value="{{$route->id}}">{{$route->code}} ({{$route->start}} to {{$route->end}})</option>
                                @endforeach
                            </select>
                            <div class="danger" id="route_error" style="display:none;">This field is required</div>
                        </fieldset>
                    </div>

                </div>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Phone</th>
                        <th class="border-primary border-darken-1">Notification</th>
                        <th class="border-primary border-darken-1">Rider Information</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Rider Name</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>
                <form id="create_delivery_note_form" class="" method="post" action="{{ route('admin.delivery.note.create') }}">
                    <div class="row justify-content-center">
                        @csrf
                        <input type="hidden" name="hub_id" id="hub_id">
                        <input type="hidden" name="shipment_ids" id="shipment_ids">
                        <input type="hidden" name="notification_ids" id="notification_ids">
                        <input type="hidden" name="rider_info_ids" id="rider_info_ids">
                        <input type="hidden" name="selected_rider_id" id="selected_rider_id">
                        <input type="hidden" name="selected_route_id" id="selected_route_id">
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary btn-block ">Submit &amp; Print</button>

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
            var tracking_ids = [];
            var notification_ids = [];
            var rider_info_ids = [];
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true, scrollY: '350px',
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'tracking_number', class: 'align-middle tracking_number'},
                    {name: 'destination', class: 'align-middle destination'},
                    {name: 'consignee_name', class: 'align-middle consignee_name'},
                    {name: 'phone', class: 'align-middle phone'},
                    {name: 'notification', class: 'align-middle notification'},
                    {name: 'rider_information', class: 'align-middle rider_information'},
                    {name: 'address', class: 'align-middle address'},
                    {name: 'amount', class: 'align-middle amount'},
                    {name: 'service_type', class: 'align-middle service_type'},
                    {name: 'status', class: 'align-middle status'},
                    {name: 'rider_name', class: 'align-middle rider_name'},
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


            $('#rider_name').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Rider*',
            });
            $('#route').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Route*',
            });
            $('#rider_name').on('change',function () {
                var route = $(this).find(":selected").data("id");
                $('#route').val(route).trigger('change');
            });
            $('#scan_tracking').on('change',function() {
                $(this).val($(this).val().trim());
            });
            var rowsCount = 0;
            function  countRows() {
                rowsCount = table.row().count();
            }
            $('input#scan_tracking').focus();
            $('#delivery_note_form').on('submit',function (e) {
                e.preventDefault();
                var scan = $('#scan_tracking');
                var tracking = parseInt(scan.val());
                var hub_id = $('#hub_id').val();
                if (tracking !== '') {
                    scan.attr('disabled', true);
                    countRows();

                    if(rowsCount === 0) {
                        $.ajax({
                            url:'{{route('admin.delivery.note.shipment.info')}}',
                            type:'GET',
                            dataType:'JSON',
                            data: {
                                'tracking':tracking
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }else{
                                var rowNo = rowsCount;
                                var notification_check = '<input type="checkbox" class="form-control notification" name="notification['+data.shId+']" checked>';
                                var rider_information = '<input type="checkbox" class="form-control select select-checkbox rider_information" name="rider_information[]" checked>';
                                var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger deliverynoterow"><i class="la la-close"></i></a>';
                                table.row.add([rowNo+1,data.tracking_number,data.destination,data.consignee_name,data.phone,notification_check,rider_information,data.address,data.amount,data.service_type,data.shipment_status,data.rider_name,data.remarks,remove]).node().id = data.shId;
                                table.draw(false);
                                shipment_ids.push(data.shId);
                                tracking_ids.push(data.tracking_number);
                                notification_ids.push(1);
                                rider_info_ids.push(1);
                                $('#hub_id').val(data.hub);
                            }
                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();

                        });
                    } else {
                        var is_indexed = $.inArray(tracking, tracking_ids);
                        if(is_indexed === -1){
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
                                if(data.status === 1){

                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }else{
                                    var rowNo = rowsCount;
                                    var notification_check = '<input type="checkbox" class="form-control notification" name="notification['+data.shId+']" checked>';
                                    var rider_information = '<input type="checkbox" class="form-control rider_information" name="rider_information[]" checked>';
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger deliverynoterow"><i class="la la-close"></i></a>';
                                    table.row.add([rowNo+1,data.tracking_number,data.destination,data.consignee_name,data.phone,notification_check,rider_information,data.address,data.amount,data.service_type,data.shipment_status,data.rider_name,data.remarks,remove]).node().id = data.shId;
                                    table.draw(false);
                                    shipment_ids.push(data.shId);
                                    tracking_ids.push(data.tracking_number);
                                    notification_ids.push(1);
                                    rider_info_ids.push(1);
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
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, shipment_ids);
                // var notification = $.inArray(rid, notification_ids);
                // var rider_info = $.inArray(rid, rider_info_ids);
                if (index !== -1) {
                    shipment_ids.splice(index, 1);
                    tracking_ids.splice(index, 1);
                    notification_ids.splice(index, 1);
                    rider_info_ids.splice(index, 1);

                }
                // if (notification !== -1) {
                //     notification_ids.splice(notification, 1);
                // }
                // if (rider_info !== -1) {
                //     rider_info_ids.splice(rider_info_ids, 1);
                // }
                table.row( $(this).parents('tr') ).remove().draw();
            });

            $('.datatable tbody').on('click', 'tr td.notification input', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                // console.log(id)
                var index = $.inArray(id, shipment_ids);
                // console.log("Before: "+shipment_ids)
                // console.log("Before: "+notification_ids)
                if(notification_ids[index] === 0){
                    notification_ids[index] = 1;
                }else{
                    notification_ids[index] = 0;
                }
                // console.log("After: "+shipment_ids)
                // console.log("After: "+notification_ids)
                // if (index === -1) {
                //     notification_ids.push(id);
                // }
                // else {
                //     notification_ids.splice(index, 1);
                // }

            });
            $('.datatable tbody').on('click', 'tr td.rider_information input', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                var index = $.inArray(id, shipment_ids);

                if(rider_info_ids[index] === 0){
                    rider_info_ids[index] = 1;
                }else{
                    rider_info_ids[index] = 0;
                }

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


                if (rider !== '' && rider !== null) {

                    $('#rider_error').css('display', 'none');
                } else {
                    var error = "Rider not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#rider_error').css('display', 'block');
                }
                if (route !== '' && route !== null) {

                    $('#route_error').css('display', 'none');
                } else {
                    var error = "Route not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#route_error').css('display', 'block');
                }

                if(count > 0) {
                    if (errors === 0) {

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
                                $('#create_delivery_note_form input#notification_ids').val(notification_ids);
                                $('#create_delivery_note_form input#rider_info_ids').val(rider_info_ids);
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