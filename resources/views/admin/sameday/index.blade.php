@extends('admin.layout.master')

@section('title', 'Same-Day Delivery')

@section('content')
    <h1 class="mb-1">
        Same-Day Delivery
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Consignee Phone</th>
                        <th class="border-primary border-darken-1">Consignee Address</th>
                        <th class="border-primary border-darken-1">Product Type</th>
                        <th class="border-primary border-darken-1">Same-day Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Booked Date</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Dispatched Time</th>
                        <th class="border-primary border-darken-1">Delivered Time</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">TAT</th>
                        <th class="border-primary border-darken-1">Time Remining</th>
                        <th class="border-primary border-darken-1">Instructions</th>
                        <th class="border-primary border-darken-1">Action</th>
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
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '300px',
                dom: 'ltipr',
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.sameday.list') }}',
                rowId: 'shId',
                order: [[1, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_phone', name: 'shipments.consignee_phone_number_1', class: 'align-middle consignee_phone'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'product_name', name: 'p.product_name', class: 'align-middle product_name'},
                    {data: 'timing', name: 'sms.timing', class: 'align-middle timing'},
                    {data: 'current_status', name: 'sst.name', class: 'align-middle current_status'},
                    {data: 'booked_date', name: 'shipments.created_at', class: 'align-middle booked_date'},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'dispatched_time', name: 'dispatched.created_at', class: 'align-middle dispatched_time'},
                    {data: 'delivered_status', name: 'delivered_status', class: 'align-middle delivered_status',orderable: false, searchable: false},
                    {data: 'updated_by', name: 'updater.name', class: 'align-middle updated_by'},
                    {data: 'tat', name: 'tat', class: 'align-middle tat', orderable: false, searchable: false},
                    {data: 'remaining_time', name: 'remaining_time', class: 'align-middle remaining_time', orderable: false, searchable: false},
                    {data: 'instructions', name: 'shipments.special_instructions', class: 'align-middle instructions'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.delivered_status') || $(header).is('.tat') || $(header).is('.remaining_time')) {
                            $(td).appendTo($(search));
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

                    this.api().table().columns.adjust();
                }
            });
            function print(id) {
                $.ajax({
                    url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                    method: 'POST',
                    data: {
                        'ids[]': id,
                        'admin': '{!! Auth::id() !!}',
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
            $('body').on('click','.airwaybill',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                // console.log(id);
                print(id);
            });
            $('body').on('click','.view_charges',function () {
                var shipment_id = $(this).parents('tr').attr('id');
                $('#ShipmentChargesModal').modal('show');
                $('#shipment_charges_modal_id').val(shipment_id);
                $.ajax({
                    url:'{!! route("admin.orders.charges") !!}',
                    method: 'POST',
                    data: {
                        'shipment_id': shipment_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    $('#shipment_charges_body').html(data);
                    $('#shipment_charges_modal_heading span').text(shipment_id);
                })
            });

        });

    </script>
@endsection