@extends('admin.layout.master')

@section('title', 'Pending Pickups')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                     Pickups History
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="container">
                                <div class="row justify-content-center">

                                    <div class="col">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                   <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                            </div>
                                            <input type="text" name="requested_from_date"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   id="requested_from_date" placeholder="Requested Date From">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                     <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                            </div>
                                            <input type="text" name="requested_to_date"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   id="requested_to_date" placeholder="Requested Date To">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <button type="button" id="search_filter_btn"
                                                class="mb-1 btn btn-outline-primary btn-min-width"><i
                                                    class="la la-search" style="margin-right: 10px"></i> Search
                                        </button>
                                    </div>
                                </div>


                            </div>

                            {{-- <div class="container-fluid">
                                <div class="row justify-content-center">
                                    <input type="hidden" value="0" id="status_filter_input" name="pickup_status_id">
                                    @foreach($statuses as $id => $status)
                                        <div>
                                            <button type="button" class="btn btn-outline-secondary btn-min-width mr-1 mb-1 pickup_status_btn" rel="{{ $id }}">{{ $status['name'] }}
                                                ({{ $status['count'] }})
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div> --}}
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">ID</th>
                                    <th class="border-primary border-darken-1">Date & Time</th>
                                    <th class="border-primary border-darken-1">Ask Time</th>
                                    <th class="border-primary border-darken-1">Shipments/Pieces</th>
                                    <th class="border-primary border-darken-1">Weight (KG)</th>
                                    <th class="border-primary border-darken-1">Additional Services</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Route Code</th>
                                    <th class="border-primary border-darken-1">Rider ID</th>
                                    <th class="border-primary border-darken-1">Shipments Picked</th>
                                    <th class="border-primary border-darken-1">Product</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Station</th>
                                    <th class="border-primary border-darken-1">Assigned Courier</th>
                                    <th class="border-primary border-darken-1">Special Request</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>


                <!--Shipments popup -->
                <div class="modal fade" id="bookings_modal" data-backdrop="static" role="dialog"
                     aria-labelledby="bookings_modal" aria-hidden="true">
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
                <div class="modal fade" id="pending_bookings_modal" data-backdrop="static" role="dialog"
                     aria-labelledby="pending_bookings_modal" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="pending_bookings_modal_title">Received Shipment(s)</h4>

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


    <div class="modal fade" id="AllRemarksModal" data-backdrop="static" role="dialog" aria-labelledby="AllRemarksModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">All Remarks</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <table class="table table-bordered" id="all_remarks_tabel">
                        <tbody>
                        <tr>
                            <td>Rider Remarks:</td>
                            <td id="rider_remarks_td"></td>
                        </tr>
                        <tr>
                            <td>Shipper Remarks:</td>
                            <td id="shipper_remarks_td"></td>
                        </tr>
                        <tr>
                            <td>Trax Reason:</td>
                            <td id="trax_reason_td"></td>
                        </tr>
                        <tr>
                            <td>Trax Remarks:</td>
                            <td id="trax_remarks_td"></td>
                        </tr>
                        <tr>
                            <td>Reverse Pickup Remarks:</td>
                            <td id="remarks_td"></td>
                        </tr>
                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>


    <!---- start of show additional services modal---->
    <div class="modal fade text-left" id="AdditionalServiceModal" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="AddRequestModal"
    aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
       <div class="modal-content">
           <div class="modal-header bg-primary white">
               <h4 class="modal-title white">Additional Services</h4>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <div class="modal-body">
               <div class="container">
                   
                   <table class="table table-bordered">
                        <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Service</th>
                                    <th class="border-primary border-darken-1">Qty</th>
                                </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>
                   <!-- end additional services -->

                 
               </div>
           </div>
       </div>
   </div>
</div>
    <!----end of show additional services modal --->

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style>
        .btn-min-width {
            min-width: 5.5rem;
        }

        .legends {
            cursor: pointer;
        }

        .custom-nav {
           margin-left: 4px;
        }

        .custom-nav li {

           border: 1px solid #CCCCCC;
           border-radius: 4px;
        }

        .custom-nav li:first-child {

           margin-right: 4px !important;
        }

        .custom-nav li:last-child {

           margin-left: 4px !important;
        }

        .custom-nav .nav-item a.nav-link {

            color: #CCCCCC;
            border: 1px solid #CCCCCC !important;
        }

        .custom-nav .nav-item p {

            line-height: 1.4;
        }

        .custom-nav .nav-item a.active {

            color: #64A0D2 !important;
            border: 1px solid #64A0D2 !important;
            background-color: #F7FAFC !important;
        }

        .custom-nav .nav-item a:hover {
            color: #64A0D2 !important;
            border: 1px solid #64A0D2 !important;
            background-color: #F7FAFC !important;
        }

        /* start scheduled days area */

        /* Hide checkboxes */
        .scheduled_days_area input[type="checkbox"] {
        display: none;
        }

        /* Style labels for checkboxes */
        .scheduled_days_area input + label {
        display: inline-block;
        border: 1px solid #CCCCCC;
        background: #fff;
        padding: 5px 10px;
        color: #A3A3A3;
        border-radius: 5px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s;
        }

        /* Style the checkbox's unchecked state */
        .scheduled_days_area input:checked + label {
            background: #5587b4;
            border-color: #64A0D2;
            color: #fff;
        }

        /* Style the checkbox's unchecked state icon */
        .scheduled_days_area input:checked + label:before {
        font-size: 17px;
        position: absolute;
        left: 24px;
        top: 6px;
        opacity: 1;
        }

        /* .scheduled_days_area input + label:hover {
            background: #fff;
            border-color: #CCCCCC;
            color: #000;
        } */

        .scheduled_days_area input:not(:checked) + label:hover {
            background: #F7FAFC;
            border-color: #64A0D2;
            color: #64A0D2;
        }

        .scheduled_days_area .item-column {
            margin-right: 10px;
        }

        /* end scheduled days area */

        /* start addition services */

        .service-item {

            border: 1px solid #CCCCCC;
            border-radius: 4px;
            padding-top: 12px;
            padding-bottom: 12px;

        }

        .btn-service {
            border-radius: 50%;
            padding: 4px;
            width: 30px;
            height: 30px;
            transition: all 0.4s;
        }

        .btn-service:hover {

            background: #6496BE !important;
        }

        .btn-service:active,
        .btn-service:focus {

            background: #6496BE !important;
        }

        .service-item .custom-input-number[type="number"]::-webkit-inner-spin-button,
        .service-item .custom-input-number[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            appearance: none;
            margin: 0;
        }

        /* end addition services */

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script>


        $(document).ready(function () {
           
            var booking_from_date = $('#requested_from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                // format: 'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#requested_to_date').pickadate('picker').set('min', $('#requested_from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var booking_to_date = $('#requested_to_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                // format: 'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#requested_from_date').pickadate('picker').set('max', $('#requested_to_date').pickadate('picker').get('select'));
                    }
                }
            });
           

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.v3_pickups.history.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Pickup Request ID');
                            head.push('Requested Date');
                            head.push('Current Rider');
                            head.push('Last Rider');
                            head.push('Pickup Note ID');
                            head.push('Shipment(s) Booked');
                            head.push('Shipment(s) Rider Picked');
                            // head.push('Shipment(s) Received');
                            head.push('Shipper');
                            head.push('Territory');
                            head.push('Contact Person');
                            head.push('Vendor');
                            head.push('Brand Name');
                            head.push('Contact No(s).');
                            head.push('Address');
                            head.push('City');
                            head.push('Status');
                            head.push('Trax Reason');
                            head.push('Trax Remark(s)');
                            head.push('Shipper Remark(s)');
                            head.push('Rider Remark(s)');
                            head.push('Assigned Date');
                            head.push('Attempt Date');
                            head.push('Aging');
                            head.push('Attempt(s)');


                            $.each(result.data, function (index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.pickup_request_id);
                                row.push(values.requested_date);
                                row.push(values.current_rider);
                                row.push(values.last_rider);
                                row.push(values.pickup_note_id);
                                row.push(values.booked);
                                row.push(values.shipments_rider_picked);
                                // row.push(values.received);
                                row.push(values.shipper);
                                row.push(values.territory);
                                row.push(values.contact_person);
                                row.push(values.vendor_name);
                                row.push(values.brand_name);
                                row.push(values.contact_number);
                                row.push(values.address);
                                row.push(values.city);
                                row.push(values.pickup_status);
                                row.push(values.trax_reason);
                                row.push(values.trax_remarks);
                                row.push(values.shipper_remarks);
                                row.push(values.rider_remarks);
                                row.push(values.assigned_date);
                                row.push(values.attempted_date);
                                row.push(values.aging);
                                row.push(values.attempts);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });


            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Pending Pickups Requests',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.v3_pickups.history.list') }}',
                    data: function (d) {
                        d.requested_from_date = $('#requested_from_date').val();
                        d.requested_to_date = $('#requested_to_date').val();
                        // d.pickup_status_id = $('#status_filter_input').val();
                    }
                },
                rowId: 'id',
                order: [[2, 'desc']],
                columns: [
                    {
                        data: 'serial_number',
                        orderable: false,
                        searchable: false,
                        name: 'pickup_requests.id',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'pickup_request_id', name: 'pickup_request_id', class: 'align-middle pickup_request_id'},
                    {data: 'pickup_date', name: 'pickup_date', class: 'align-middle pickup_date'},
                    {data: 'time_range', name: 'time_range', class: 'align-middle ask_time'},
                    {data: 'shipment_pieces', name: 'shipment_pieces', class: 'align-middle shipments', orderable: false},
                    {data: 'weight', name: 'weight', class: 'align-middle weight'},
                    {data: 'services_count_btn', name: 'services_count', class: 'align-middle text-center services_count'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'route_code', name: 'route_code', class: 'align-middle route_code'},
                    {data: 'rider_id', name: 'rider_id', class: 'align-middle rider_id'},
                    {data: 'shipments_picked', name: 'shipments_picked', class: 'align-middle shipments_picked'},
                    {data: 'product', name: 'product', class: 'align-middle product'},
                    {data: 'shipper', name: 'shipper', class: 'align-middle shipper'},
                    {data: 'hub', name: 'hub', class: 'align-middle station'},
                    {data: 'current_rider', name: 'current_rider', class: 'align-middle current_rider'},
                    {data: 'special_request', name: 'special_request', class: 'align-middle special_request'},
                    {
                        data: 'action',
                        name: 'action',
                        class: 'align-middle text-center action',
                        orderable: false,
                        searchable: false
                    }

                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.select') || $(header).is('.serial_number') || $(header).is('.shipments') || $(header).is('.status') || $(header).is('.trax_reason') || $(header).is('.trax_remarks') || $(header).is('.shipper_remarks') || $(header).is('.attempted_date') || $(header).is('.action') || $(header).is('.rider_remarks') || $(header).is('.brand_name') || $(header).is('.all_remarks')) {
                            $(td).appendTo($(search));
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    this.api().table().columns.adjust();
                }

            });

           
            var route = '{!! route('admin.tracking.index') !!}';
            $('body').on('click', '#datatable tbody tr td.bookings_link button', function () {
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
                    .done(function (data) {
                        if (data) {
                            var shipments = '';
                            if (data.booked) {
                                $.each(data.booked, function (index, tracking_numbers) {
                                    shipments += '<u><a href=' + route + '?tracking_number=' + tracking_numbers + ' target="_blank">' + tracking_numbers + '</a></u><br>';
                                });
                            }
                            $('#bookings_modal .modal-body').html(shipments);
                        }
                    });
            });

            $('#search_filter_btn').on('click', function () {
                table.draw(true);
            });


            $('#datatable tbody').on('click', 'tr td.all_remarks button.all_remarks_btn', function () {
                var pickup_req_id = parseInt($(this).attr('rel'));


                console.log(pickup_req_id);

                $.ajax({
                    url: '{!! route('admin.v2_pickups.pending.all_remarks') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'pickup_req_id': pickup_req_id
                    }
                })
                    .done(function (data) {
                        if (data) {
                            console.log(data.remarks);

                            $('#rider_remarks_td').html(data.remarks.rider_remarks);
                            $('#shipper_remarks_td').html(data.remarks.shipper_remarks);
                            $('#trax_reason_td').html(data.remarks.trax_reason);
                            $('#trax_remarks_td').html(data.remarks.trax_remarks);
                            $('#remarks_td').html(data.remarks.remarks);
                            $('#AllRemarksModal').modal('show');
                            if (!data.remarks.reverse_pickup) {

                                $("#remarks_td").parent().css({"display": "none"});
                            }
                            /* var shipments = '';
                            if (data.booked) {
                                $.each(data.booked, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#bookings_modal .modal-body').html(shipments); */
                        }
                    });
                /* print(pickup_note_id); */
            });

            $('body').on('click', 'button.pickup_status_btn', function(){
                var status_id = $(this).attr('rel');
               $('#status_filter_input').val(status_id);
               table.draw();
            });

        

            $('#add_pickup_request').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#add_pickup_request button#add').prop('disabled', true);
                    swal({
                        title: 'Please Wait!',
                        text: 'Pickup request is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });

            $('.apply-checked').change(function() {

                if ($(this).is(':checked')) {

                    $(this).attr('checked', 'checked');

                } else {

                    $(this).removeAttr('checked');
                }
            });

        });
        
        function showadditionalservices(event,pickup_request_id){
            $("#AdditionalServiceModal table tbody").empty();
            $.ajax({
                url:'{{ route('admin.v3_pickups.pending.pickup_request_services') }}',
                method:'POST',
                data:{
                    'pickup_request_id':pickup_request_id,
                    '_token':'{{ csrf_token() }}'
                }
            }).done(function(data){
                if (data.status == 0) {
                            $.each(data.pickup_request_services,function(key,value) {
                              $("#AdditionalServiceModal table tbody").append('<tr id="8" role="row" class="odd"><td class=" align-middle status">'+(key+1)+'</td><td class=" align-middle service_name">'+value.service_name+'</td><td class=" align-middle service_count">'+value.count+'</td></tr>')
                            });
                        } else {
                            toastr.error('No pickup address found!', 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                        });
                }
            })
           $("#AdditionalServiceModal").modal("show");
        }
    </script>
@endsection