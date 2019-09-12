
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

                <div id="camera_scan" class="d-none">
                    <div id="camera_view" class="camera_view"></div>
                </div>

                <form action="#" id="delivery_note_form">
                <div class="row justify-content-center align-items-center mb-2">
                    <div class="col-3">
                        <fieldset>
                            <input type="text" class="form-control" placeholder="Scan Tracking Number" id="scan_tracking">
                        </fieldset>
                    </div>

                    <div class="col-1">
                        <a href="#" id="camera_scan_initiate" class="d-block text-right" tabindex="-1">
                            <i class="ft-camera h1"></i>
                        </a>
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
                        <th class="border-primary border-darken-1">Last Rider Name</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Consolidation</th>
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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

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
            var consolidation_ids = [];
            var notification_ids = [];
            var rider_info_ids = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        text: 'Select All Notification(s)',
                        enabled: false,
                        className: 'select_all_notifications',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);
                                var notification_box = $(row.node()).find('td.notification input');
                                if (!notification_box.is(':checked')) {
                                    notification_box.prop('checked',true);
                                    var id = parseInt($(notification_box).parents('tr').attr('id'));

                                    var index = $.inArray(id, shipment_ids);
                                    if(notification_ids[index] === 0){
                                        notification_ids[index] = 1;
                                    }
                                }
                            });
                            table.button('.select_none_notifications').enable();
                        }
                    }, {
                        text: 'Unselect All Notification(s)',
                        className: 'select_none_notifications',
                        enabled: false,
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);
                                var notification_box = $(row.node()).find('td.notification input');
                                if (notification_box.is(':checked')) {
                                    notification_box.prop('checked',false);
                                    var id = parseInt($(notification_box).parents('tr').attr('id'));

                                    var index = $.inArray(id, shipment_ids);
                                    if(notification_ids[index] === 1){
                                        notification_ids[index] = 0;
                                    }
                                }

                            });
                            table.button('.select_all_notifications').enable();
                        }
                    },
                    {
                        text: 'Select All Rider Information(s)',
                        enabled: false,
                        className: 'select_all_rider_informations',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);
                                var rider_information_box = $(row.node()).find('td.rider_information input');
                                if (!rider_information_box.is(':checked')) {
                                    rider_information_box.prop('checked',true);
                                    var id = parseInt($(rider_information_box).parents('tr').attr('id'));

                                    var index = $.inArray(id, shipment_ids);
                                    if(rider_info_ids[index] === 0){
                                        rider_info_ids[index] = 1;
                                    }
                                }
                            });
                            table.button('.select_none_rider_informations').enable();
                        }
                    }, {
                        text: 'Unselect Rider Information(s)',
                        className: 'select_none_rider_informations',
                        enabled: false,
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);
                                var rider_information_box = $(row.node()).find('td.rider_information input');
                                if (rider_information_box.is(':checked')) {
                                    rider_information_box.prop('checked',false);
                                    var id = parseInt($(rider_information_box).parents('tr').attr('id'));

                                    var index = $.inArray(id, shipment_ids);
                                    if(rider_info_ids[index] === 1){
                                        rider_info_ids[index] = 0;
                                    }
                                }


                            });
                            table.button('.select_all_rider_informations').enable();
                        }
                    },
                    ],
                scrollX: true,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
                    {name: 'destination', class: 'align-middle destination', orderable: false},
                    {name: 'consignee_name', class: 'align-middle consignee_name', orderable: false},
                    {name: 'phone', class: 'align-middle phone', orderable: false},
                    {name: 'notification', class: 'align-middle notification', orderable: false},
                    {name: 'rider_information', class: 'align-middle rider_information', orderable: false},
                    {name: 'address', class: 'align-middle address', orderable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false},
                    {name: 'service_type', class: 'align-middle service_type', orderable: false},
                    {name: 'status', class: 'align-middle status', orderable: false},
                    {name: 'rider_name', class: 'align-middle rider_name', orderable: false},
                    {name: 'remarks', class: 'align-middle remarks', orderable: false},
                    {name: 'consolidation', class: 'align-middle consolidation', orderable: false},
                    {name: 'action', class: 'align-middle action', orderable: false, searchable: false}
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
            // function  countRows() {
            //     rowsCount = table.row().count();
            // }
            $('input#scan_tracking').focus();
            $('#delivery_note_form').on('submit',function (e) {
                e.preventDefault();
                var scan = $('#scan_tracking');
                var tracking = parseInt(scan.val());
                var hub_id = $('#hub_id').val();
                if (tracking !== '') {
                    scan.attr('disabled', true);
                    //countRows();

                    if(rowsCount === 0) {
                        blockPagePermanently();
                        $.ajax({
                            url:'{{route('admin.delivery.note.shipment.info')}}',
                            type:'POST',
                            data: {
                                'tracking':tracking,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                UnblockPagePermanently();
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                scan_sound(2);
                            }else{
                                rowsCount += 1;
                                var rowNo = rowsCount;
                                var notification_check = '<input type="checkbox" class="form-control notification" name="notification['+data.shId+']" checked>';
                                var rider_information = '<input type="checkbox" class="form-control select select-checkbox rider_information" name="rider_information[]" checked>';
                                var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger deliverynoterow"><i class="la la-close"></i></a>';

                                var consolidation = '';
                                if(data.consolidation_flag){
                                    consolidation = data.consolidation_details.order+'/'+data.consolidation_details.count;
                                }else{
                                    consolidation = '-';
                                }
                                var row = table.row.add([rowNo,data.tracking_number,data.destination,data.consignee_name,data.phone,notification_check,rider_information,data.address,data.amount,data.service_type,data.shipment_status,data.rider_name,data.remarks,consolidation,remove]).node().id = data.shId;
                                table.draw(false);
                                $('tr#'+row).attr('class',data.class);
                                if(data.consolidation_flag){
                                    $('tr#'+row).attr('consolidation_id',data.consolidation_details.consolidation_id);
                                }

                                // table.rows(row).nodes().attr("class", data.class);
                                scan_sound(1);
                                UnblockPagePermanently();
                                shipment_ids.push(data.shId);
                                tracking_ids.push(data.tracking_number);
                                notification_ids.push(1);
                                rider_info_ids.push(1);
                                $('#hub_id').val(data.hub);

                            }
                            scan.val('');
                            scan.attr('disabled', false);
                            scan.focus();
                            table.button('.select_all_notifications').enable();
                            table.button('.select_none_notifications').enable();
                            table.button('.select_all_rider_informations').enable();
                            table.button('.select_none_rider_informations').enable();

                        });
                    } else {
                        var is_indexed = $.inArray(tracking, tracking_ids);
                        if(is_indexed === -1){
                            blockPagePermanently();
                            // $('#hub_id').val('');
                            $.ajax({
                                url:'{{route('admin.delivery.note.shipment.info')}}',
                                type:'POST',
                                data: {
                                    'tracking':tracking,
                                    'hub_id':hub_id,
                                    '_token':'{!! csrf_token() !!}'
                                }
                            }).done(function (data) {
                                if(data.status === 1){
                                    UnblockPagePermanently();
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    scan_sound(2);
                                }else{
                                    // rowsCount += 1;
                                    // var rowNo = rowsCount;
                                    var rowNo = table.rows().count();
                                    var notification_check = '<input type="checkbox" class="form-control notification" name="notification['+data.shId+']" checked>';
                                    var rider_information = '<input type="checkbox" class="form-control rider_information" name="rider_information[]" checked>';
                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger deliverynoterow"><i class="la la-close"></i></a>';
                                    var consolidation = '';
                                    if(data.consolidation_flag){
                                        consolidation = data.consolidation_details.order+'/'+data.consolidation_details.count;
                                    }else{
                                        consolidation = '-';
                                    }
                                    var row = table.row.add([rowNo+1,data.tracking_number,data.destination,data.consignee_name,data.phone,notification_check,rider_information,data.address,data.amount,data.service_type,data.shipment_status,data.rider_name,data.remarks,consolidation,remove]).node().id = data.shId;
                                    table.draw(false);
                                    $('tr#'+row).attr('class',data.class);
                                    if(data.consolidation_flag){
                                        $('tr#'+row).attr('consolidation_id',data.consolidation_details.consolidation_id);
                                    }
                                    scan_sound(1);
                                    UnblockPagePermanently();
                                    shipment_ids.push(data.shId);
                                    tracking_ids.push(data.tracking_number);
                                    notification_ids.push(1);
                                    rider_info_ids.push(1);
                                    table.order([0, 'desc']).draw();

                                }
                                scan.val('');
                                scan.attr('disabled', false);
                                scan.focus();

                            });
                        }else{
                            var error = 'Tracking Number already scanned!';
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            scan_sound(2);
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

                if (index !== -1) {
                    shipment_ids.splice(index, 1);
                    tracking_ids.splice(index, 1);
                    notification_ids.splice(index, 1);
                    rider_info_ids.splice(index, 1);
                    rowsCount -= 1;
                }

                table.row( $(this).parents('tr') ).remove().draw();
            });

            $('.datatable tbody').on('click', 'tr td.notification input', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(id, shipment_ids);
                if(notification_ids[index] === 0){
                    notification_ids[index] = 1;
                }else{
                    notification_ids[index] = 0;
                }

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
                if(errors === 0) {
                    table.rows().nodes().each(function (index) {
                        var row = table.row(index);

                        if ($(row.node()).attr('consolidation_id')) {

                            var id = parseInt($(row.node()).attr('consolidation_id'));
                            //
                            var index = $.inArray(id, consolidation_ids);

                            if (index === -1) {
                                consolidation_ids.push(id);
                            }
                            if (consolidation_ids.length > 0) {

                                $.ajax({
                                    url: '{{route('admin.delivery.note.consolidation_check')}}',
                                    type: 'POST',
                                    data: {
                                        'consolidation_ids': consolidation_ids,
                                        'shipment_ids': shipment_ids,
                                        '_token': '{!! csrf_token() !!}'
                                    }
                                }).done(function (data) {
                                    if (data.missing_flag) {
                                        errors = 1;
                                        var html = '';

                                        html += 'The following Shipment(s) are missing from consolidation:<br/>';

                                        $.each(data.missing_shipments, function (index, tracking) {
                                            html += tracking + ', ';
                                        });

                                        html = html.slice(0, -2);

                                        content = document.createElement('div');
                                        content.innerHTML = html;
                                        swal({
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
                                    } else {

                                        if (count > 0) {
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
                                                    if (confirm) {
                                                        blockPagePermanently();
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
                                        } else {
                                            var error = "Select at-least one shipment!";
                                            toastr.error(error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });

                                        }
                                    }
                                });
                            }

                        }
                    });
                }


            });

            $('#camera_scan_initiate').bind('click', function() {
                if ($('#camera_scan').hasClass('d-none')) {
                    $('#camera_scan').removeClass('d-none');

                    camera_scanning_start('#camera_view');
                }
                else {
                    $('#camera_scan').addClass('d-none');

                    camera_scanning_stop();
                }
            });

        });

        function camera_scan_detected(tracking_number) {
            $('#scan_tracking').val(tracking_number);

            $('#delivery_note_form').trigger('submit');
        }
    </script>
@endsection