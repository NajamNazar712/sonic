@extends('admin.layout.master')

@section('title', 'Un-Assigned Pickups')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Un-Assigned Pickups
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-end">
                                <div class="col-5">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="heading-elements">
                                                <ul class="list-inline mb-0">
                                                    <li class="primary border-primary round"><a data-action="collapse">Legend <i class="ft-minus"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-content collapse">
                                            <div class="card-body p-1">
                                                <h4 class=" info">Legend</h4>
                                                <table class="table mb-0">
                                                    <tbody>
                                                    @foreach($legends as $legend)
                                                        @if($legend->id == 7)
                                                            <tr style="background-color: {{$legend->color}}; color:#010a10;">
                                                                {{--                                                                <td><button type="button" class="btn btn-sm round btn-min-width text-white" style="background-color: {{$legend->color}}" disabled>{{$cut_off_time}}:00</button></td>--}}
                                                                <td class="align-middle">{{ $legend->name }} <b>({{$cut_off_time}}:00)</b></td>
                                                            </tr>
                                                        @else
                                                            <tr style="background-color: {{$legend->color}}; color:#010a10;">
                                                                {{--                                                                <td><button type="button" class="btn btn-sm round btn-min-width p-1" style="background-color: {{$legend->color}}" disabled> </button></td>--}}
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
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Contact Person</th>
                                    <th class="border-primary border-darken-1">Vendor</th>
                                    <th class="border-primary border-darken-1">Contact No(s).</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="assign_to_rider" data-backdrop="static" role="dialog" aria-labelledby="assign_to_rider_title" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <form class="form-horizontal" action="{{route('admin.v2_pickups.pending.assign')}}" method="post">
                                {{ csrf_field() }}
                                @method('put')
                                <div class="modal-header">
                                    <h4 class="modal-title" id="assign_to_rider_title">Assign to Rider</h4>
                                </div>
                                <input type="hidden" name="pickup_request_ids" id="assign_pickup_request_ids">
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
        .reverse_pickup_row{
            background-color: #bfefe2;
        }
        @foreach($legends as $legend)
    @if($legend->id == 1)
        .new_pickup{
            background-color: '{{$legend->color}}';
        }
        @elseif($legend->id == 2)
        .vendor_row{
            background-color: '{{$legend->color}}';
        }
        @elseif($legend->id == 3)
        .try_and_buy{
            background-color: '{{$legend->color}}';
        }
        @elseif($legend->id == 4)
        .first_attempt{
            background-color: '{{$legend->color}}';
        }
        @elseif($legend->id == 5)
        .second_attempt{
            background-color: '{{$legend->color}}';
        }
        @elseif($legend->id == 6)
        .multiple_attempt{
            background-color: '{{$legend->color}}';
        }
        @elseif($legend->id == 7)
        .after_cut_off_time{
            background-color: '{{$legend->color}}';
        }
        @endif
        @endforeach
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function () {


            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.v2_pickups.pending.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Pickup Request ID');
                            head.push('Requested Date');
                            head.push('Shipment(s) Booked');
                            head.push('Shipper');
                            head.push('Contact Person');
                            head.push('Vendor');
                            head.push('Contact No(s).');
                            head.push('Address');
                            head.push('City');
                            head.push('Status');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.pickup_request_id);
                                row.push(values.requested_date);
                                row.push(values.booked);
                                row.push(values.shipper);
                                row.push(values.contact_person);
                                row.push(values.vendor_name);
                                row.push(values.contact_number);
                                row.push(values.address);
                                row.push(values.city);
                                row.push(values.pickup_status);



                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );


            var selected_rows = [];
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
                    // {
                    //     text: 'Update',
                    //     className: 'btn btn-primary update',
                    //     enabled: false,
                    //     action: function (e, dt, node, config) {
                    //         $('#update_pickup_modal .reason').val(null).trigger('change');
                    //         $('#update_pickup_modal #trax_remarks').val('');
                    //
                    //         $('#update_pickup_modal').modal('show');
                    //     }
                    // },
                    {
                        extend: 'excel',
                        title: 'Pending Pickups',
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

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.assign').enable();
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
                ajax: '{{ route('admin.v2_pickups.un_assigned.list') }}',
                rowId: 'id',
                order: [[2, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'serial_number', orderable: false, searchable: false, name: 'pickup_requests.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'pickup_request_id', name: 'v2_pickup_requests.id', class: 'align-middle pickup_request_id'},
                    {data: 'requested_date', name: 'v2_pickup_requests.created_at', class: 'align-middle requested_date'},
                    {data: 'bookings_link', name: 'v2_pickup_requests.booked', class: 'align-middle text-center bookings_link'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'contact_person', name: 'usi.poc', class: 'align-middle contact_person'},
                    {data: 'vendor_name', name: 'usi.vendor', class: 'align-middle vendor_name'},
                    {data: 'contact_number', name: 'usi.phone', class: 'align-middle contact_number'},
                    {data: 'address', name: 'usi.pickup_address', class: 'align-middle address'},
                    {data: 'city', name: 'ci.name', class: 'align-middle city'},
                    {data: 'pickup_status', name: 'prs.id', class: 'align-middle pickup_status'},

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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var rider_status_select = '<select name="rider_status_select" id="rider_status_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.trax_reason') || $(header).is('.trax_remarks') || $(header).is('.shipper_remarks') || $(header).is('.attempted_date') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.pickup_status')){
                            $(drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.rider_status')){
                            $(rider_status_select).appendTo($(search))
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
                    var data = $.map({!! $pickup_statuses !!}, function (obj) {
                        obj.id = obj.id; // replace pk with your identifier
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Pickup Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
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
                    table.button('.assign').enable();
                }
                else {
                    table.button('.assign').disable();
                }
            });

            $('#assign_to_rider .rider').select2({
                width: '100%',
                placeholder: 'Rider*'
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });

            $('#assign_to_rider form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        text: 'Are you sure, you want to assign rider to the following pickup(s)?',
                        icon: 'info',
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
                        if(confirm){
                            $(form).find('button[type=submit]').attr('disabled', 'disabled');
                            $('#assign_pickup_request_ids').val(selected_rows);
                            form.submit();
                        }
                        $(form).find('button[type=submit]').attr('disabled', false);

                    });

                }
            });
            var route = '{!! route('admin.tracking.index') !!}';
            $('body').on('click','#datatable tbody tr td.bookings_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#bookings_modal .modal-body').html('');
                $('#bookings_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.v2_pickups.pending.bookings.all') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'pickup_request_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var shipments = '';
                            if (data.booked) {
                                $.each(data.booked, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#bookings_modal .modal-body').html(shipments);
                        }
                    });
            });
        });
    </script>
@endsection