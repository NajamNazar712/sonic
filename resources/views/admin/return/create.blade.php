
@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Create Return Note
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row justify-content-center mb-2">
                    <div class="col-3">
                        <fieldset>
                            <input type="text" class="form-control" placeholder="Scan Tracking Number" id="scan_tracking">
                        </fieldset>
                    </div>
                </div>
                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="rider_name" id="rider_name" class="form-control select2" required >
                                <option value="">Select a rider</option>
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
                                <option value="">Select a route</option>
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
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
                <form id="create_return_note_form" class="" method="post" action="{{ route('admin.return.create.note.submit') }}">
                    <div class="row justify-content-center">
                        @csrf
                        <input type="hidden" name="hub_id" id="hub_id">
                        <input type="hidden" name="shipment_ids" id="shipment_ids">
                        <input type="hidden" name="rider_id" id="selected_rider_id">
                        <input type="hidden" name="route_id" id="selected_route_id">
                        <div class="col-3">
                            <button type="submit" name="submit_and_print" value="submit_and_print" class="btn btn-primary btn-block ">Submit &amp; Print</button>

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
                    url: '{!! route('admin.return.receive.rn.print') !!}',
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
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                pagingType: 'full_numbers',
                order: [[1, 'desc']],
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
                    {name: 'action', class: 'align-middle action'}
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


                }
            });


            $('.select2').select2();
            $('#rider_name').on('change',function () {
                var route = $(this).find(":selected").data("id");
                $('#route').val(route).trigger('change');
            });
            $('input#scan_tracking').focus();
            $('#scan_tracking').on('change',function () {
                var scan = $(this);
                var tracking = scan.val();
                var hub_id = $('#hub_id').val();

                if(table.row().count() == 0) {
                    $.ajax({
                        url: '{{route('admin.return.create.shipment_details')}}',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            'tracking': tracking
                        }
                    }).done(function (data) {

                        if (data.status == 1) {

                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                            scan.val('');
                            $('input#scan_tracking').focus();
                        } else {
                            var rowNo = table.rows().count();
                            var remove = '<a href="#" class="deliverynoterow">Delete</a>';
                            table.row.add([rowNo + 1, data.tracking_number, data.destination, data.consignee_name, data.phone, data.address, data.amount, data.service_type, data.status, remove]).node().id = data.shId;
                            table.draw(false);
                            shipment_ids.push(data.shId);
                            $('#hub_id').val(data.hub);

                            scan.val('');
                            $('input#scan_tracking').focus();
                        }
                    });
                }else {
                    if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking)) === -1) {

                        $.ajax({
                            url: '{{route('admin.return.create.shipment_details')}}',
                            type: 'GET',
                            dataType: 'JSON',
                            data: {
                                'tracking': tracking,
                                'hub_id':hub_id
                            }
                        }).done(function (data) {

                            if (data.status == 1) {

                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                                scan.val('');
                                $('input#scan_tracking').focus();
                            } else {
                                var rowNo = table.rows().count();
                                var remove = '<a href="#" class="deliverynoterow">Delete</a>';
                                table.row.add([rowNo + 1, data.tracking_number, data.destination, data.consignee_name, data.phone, data.address, data.amount, data.service_type, data.status, remove]).node().id = data.shId;
                                table.draw(false);
                                shipment_ids.push(data.shId);
                                scan.val('');
                                $('input#scan_tracking').focus();
                            }
                        });
                    } else {
                        var error = 'Tracking Number already scanned!';
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-bottom-center',
                            containerId: 'toast-bottom-center'
                        });
                        scan.val('');
                        $('input#scan_tracking').focus();
                    }
                }
            });
            $('body').on('click','a.deliverynoterow',function () {
                var rid = $(this).parents('tr').attr('id');

                table.row( $(this).parents('tr') ).remove().draw();
                shipment_ids.splice( $.inArray(rid, shipment_ids), 1 );
            });


            $('#create_return_note_form').bind('submit', function(event) {
                event.preventDefault();
                var count = table.rows().count();
                var errors = 0;
                var rider = $('#rider_name').val();
                var route = $('#route').val();
                if(rider != ''){

                    $('#rider_error').css('display','none');
                }else{

                    $('#rider_error').css('display','block');
                }
                if(route != ''){

                    $('#route_error').css('display','none');
                }else{

                    $('#route_error').css('display','block');
                }
                if(rider != '' && route != ''){
                    errors = 0;
                }else{
                    errors = 1;
                }

                if(count > 0) {
                    if (errors == 0) {
                        $('#create_return_note_form button[type="submit"]').attr('disabled', 'disabled');
                        swal({
                            title: 'Please Wait!',
                            text: 'Return Shipments are being submited!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        $('#create_return_note_form input#shipment_ids').val(shipment_ids);
                        $('#create_return_note_form input#selected_rider_id').val(rider);
                        $('#create_return_note_form input#selected_route_id').val(route);

                        this.submit();

                    }
                }else{
                    var error = "Select at-least one shipment!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                }


            });
        });
    </script>
@endsection