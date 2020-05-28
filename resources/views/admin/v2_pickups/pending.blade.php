@extends('admin.layout.master')

@section('title', 'Pending Pickups')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Pending Pickups
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-end">
                                <div class="col-5">
                                    <div class="card border border-lighten-5">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title info">Legend</h4>
                                                <table class="table mb-0">
                                                    <tbody>
                                                    @foreach($legends as $legend)
                                                        @if($legend->id == 7)
                                                            <tr>
                                                                <td><button type="button" class="btn btn-sm round btn-min-width text-white" style="background-color: {{$legend->color}}" disabled>{{$cut_off_time}}</button></td>
                                                                <td class="align-middle">{{ $legend->name }}</td>
                                                            </tr>
                                                            @else
                                                            <tr>
                                                                <td><button type="button" class="btn btn-sm round btn-min-width p-1" style="background-color: {{$legend->color}}" disabled> </button></td>
                                                                <td class="align-middle">{{ $legend->name }}</td>
                                                            </tr>
                                                            @endif

                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Pickup Request ID</th>
                                    <th class="border-primary border-darken-1">Requested Date</th>
                                    <th class="border-primary border-darken-1">Shipment(s) Booked</th>
                                    <th class="border-primary border-darken-1">Shipment(s) Rider Picked</th>
                                    <th class="border-primary border-darken-1">Shipment(s) Received</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Contact Person</th>
                                    <th class="border-primary border-darken-1">Vendor</th>
                                    <th class="border-primary border-darken-1">Contact No(s).</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Trax Reason</th>
                                    <th class="border-primary border-darken-1">Trax Remark(s)</th>
                                    <th class="border-primary border-darken-1">Shipper Remark(s)</th>
                                    <th class="border-primary border-darken-1">Rider Status</th>
                                    <th class="border-primary border-darken-1">Attempt Date/Time</th>
                                    <th class="border-primary border-darken-1">Attempt(s)</th>
                                    <th class="border-primary border-darken-1">Last Rider</th>
                                    <th class="border-primary border-darken-1">Current Rider</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="assign_to_rider" role="dialog" aria-labelledby="assign_to_rider_title" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <form class="form-horizontal">
                                {{ csrf_field() }}

                                <div class="modal-header">
                                    <h4 class="modal-title" id="assign_to_rider_title">Assign to Rider</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group m-0">
                                        <select name="rider" class="select2 rider" data-rule-required="true" data-msg-required="Rider is required">
                                            @foreach($riders as $rider)
                                                <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary ml-auto">Assign</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!--Shipments popup -->
                <div class="modal fade" id="bookings_modal" data-backdrop="static" role="dialog" aria-labelledby="bookings_modal" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="bookings_modal_title">Booking Shipment(s)</h4>

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
                <!--Shipments popup pending booking-->
                <div class="modal fade" id="pending_bookings_modal" data-backdrop="static" role="dialog" aria-labelledby="pending_bookings_modal" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="pending_bookings_modal_title">Pending Booking Shipment(s)</h4>

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

            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>
        .btn-min-width {
            min-width: 5.5rem;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            @if (session('role_id') == 1 || count(array_intersect([18, 19], session('permissions'))) !== 0)

            buttons: [
                    @if (session('role_id') == 1 || in_array(19, session('permissions')))
                {
                    text: 'Assign',
                    className: 'btn btn-primary assign',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        $('#assign_to_rider .rider').val(null).trigger('change');

                        $('#assign_to_rider').modal('show');
                    }
                },
                    @endif

                    @if (session('role_id') == 1 || in_array(18, session('permissions')))
                {
                    text: 'Cancel',
                    className: 'btn btn-danger cancel',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        swal({
                            text: 'Are you sure, you want to Cancel these Pickup(s)?',
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
                        }).then(function(confirm) {
                            if (confirm) {
                                $.ajax({
                                    url: '{!! route('admin.pickups.pending.multiple_cancel') !!}',
                                    method: 'PUT',
                                    data: {
                                        'pickup_request_ids': selected_rows,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                })
                                    .done(function(data) {
                                        if (data.status == 0) {
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                        }
                                        else {
                                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        }

                                        table.rows().deselect();

                                        selected_rows = [];

                                        table.button('.assign').disable();
                                        table.button('.cancel').disable();

                                        table.draw('false');
                                    });
                            }
                        });
                    }
                },
                    @endif
                {
                    extend: 'excel',
                    title: 'Pending Pickups',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                }, {
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

                                table.button('.assign').enable();
                                table.button('.cancel').enable();
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

                                if (selected_rows.length == 0) {
                                    table.button('.assign').disable();
                                    table.button('.cancel').disable();
                                }
                            }
                        });
                    }
                },
                'reset'
            ],
            @else
            buttons: [
                {
                    extend: 'excel',
                    title: 'Pending Pickups',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                'reset'
            ],
            @endif
            scrollX: true, scrollY: '500px',
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
            ajax: '{{ route('admin.v2_pickups.pending.list') }}',
            rowId: 'id',
            order: [[2, 'desc']],
            columns: [
                {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'serial_number', orderable: false, searchable: false, name: 'pickup_requests.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                {data: 'pickup_request_id', name: 'v2_pickup_requests.id', class: 'align-middle pickup_request_id'},
                {data: 'pickup_date', name: 'v2_pickup_requests.pickup_date', class: 'align-middle pickup_date'},
                {data: 'requested_date', name: 'v2_pickup_requests.requested_date', class: 'align-middle requested_date'},
                {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                {data: 'contact_person', name: 'usi.poc', class: 'align-middle contact_person'},
                {data: 'vendor', name: 'usi.vendor', class: 'align-middle vendor'},
                {data: 'contact_number', name: 'usi.phone', class: 'align-middle contact_number'},
                {data: 'address', name: 'usi.pickup_address', class: 'align-middle address'},
                {data: 'city', name: 'ci.name', class: 'align-middle city'},
                {data: 'rider', name: 'r.name', class: 'align-middle rider'},
                {data: 'bookings_link', name: 'v2_pickup_requests.booked', class: 'align-middle bookings_link text-center'},
                {data: 'pending_bookings_link', name: 'v2_pickup_requests.pending_bookings', class: 'align-middle pending_bookings_link'},
                {data: 'total_estimated_weight', name: 'v2_pickup_requests.total_estimated_weight', class: 'align-middle total_estimated_weight'},
                {data: 'pickup_type', name: 'pickup_type', class: 'align-middle pickup_type'},
                {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
            ],
            rowCallback: function(row, data, index) {
                var info = table.page.info();

                $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                if ($.inArray(data.id, selected_rows) !== -1) {
                    table.row(row).select();
                }
            },
            initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                    '<option value="0">Light</option>' +
                    '<option value="1">Heavy</option>' +
                    '</select>';
                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action')) {
                        $(td).appendTo($(search));
                    }else if($(header).is('.pickup_type')){
                        $(drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }
                    else {
                        var current = $(input).appendTo($(search)).on('change', function() {
                            column.search($(this).val(), false, false, true).draw();
                        }).wrap(td).after(icon);

                        if (column.search()) {
                            current.val(column.search());
                        }
                    }
                });
                $("#status_select").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Pickup Type",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                this.api().table().columns.adjust();
            }
        });
    </script>
@endsection