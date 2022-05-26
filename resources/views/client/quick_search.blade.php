@extends('client.layout.master')

@section('title', 'Quick Search (Last 6 Months Data)')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <h1>Quick Search (Last 6 Months Data)</h1>
                                @include('client.inc.messages')
                                <div class="col mt-2">
                                    <form id="track_form" class="mb-1" novalidate="novalidate">
                                        <div class="row justify-content-center align-items-top">
                                            <div class="form-group col-auto">
                                                <label>Tracking Number (Full)</label>
                                                <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number">
                                            </div>

                                            <div class="form-group col-auto">
                                                <label>Phone Number (Full)</label>
                                                <input type="text" name="phone_number" class="form-control phone_number" placeholder="Phone Number">
                                            </div>

                                            <div class="form-group col-auto">
                                                <label>Order Id (Full)</label>
                                                <input type="text" name="order_id" class="form-control order_id" placeholder="Order ID">
                                            </div>

                                            <div class="form-group col-auto">
                                                <label class="d-block hidden">Search</label>
                                                <button type="submit" class="btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">Shipment ID</th>
                                        <th class="border-primary border-darken-1">Tracking No.</th>
                                        <th class="border-primary border-darken-1">Order ID</th>
                                        <th class="border-primary border-darken-1">Shipper</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Reason</th>
                                        <th class="border-primary border-darken-1">Origin</th>
                                        <th class="border-primary border-darken-1">Destination</th>
                                        <th class="border-primary border-darken-1">Consignee Name</th>
                                        <th class="border-primary border-darken-1">Consignee Contact</th>
                                        <th class="border-primary border-darken-1">Consignee Address</th>
                                        <th class="border-primary border-darken-1">Collection Amount</th>
                                        <th class="border-primary border-darken-1">Booking Date</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}?v=24052022" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#track_form .tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
            });

            $('#track_form .phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right">tipr',
                lengthMenu: [[10, 50, 100, 500, 1000], [10, 50, 100, 500, 1000]],
                pageLength: 10,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('cod.quick_search.list') }}',
                    data: function (d) {
                        d.tracking_number = $('#track_form .tracking_number').val();
                        d.order_id = $('#track_form .order_id').val();
                        d.phone_number = $('#track_form .phone_number').val();
                    }
                },
                order: [[0, 'desc']],
                columns: [
                    {data: 'shipment_id', name: 'shipments.id', visible: false},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'user_name', name: 'u.name', class: 'align-middle user_name'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'reason', name: 'ssr.name', class: 'align-middle reason'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'phone', name: 'phone', class: 'align-middle phone'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'}
                ]
            });

            $('#track_form').validate({
                errorClass: 'danger',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    var tracking_number = $('#track_form .tracking_number').val();
                    var phone_number = $('#track_form .phone_number').val();
                    var order_id = $('#track_form .order_id').val();

                    if (!(tracking_number.length >= 6 && Math.floor(tracking_number) == tracking_number && $.isNumeric(tracking_number))) {
                        tracking_number = '';
                    }

                    if (!(phone_number.length >= 10)) {
                        phone_number = '';
                    }

                    if (tracking_number != '' || phone_number != '' || order_id != '') {
                        table.draw();
                    }
                }
            });
        });
    </script>
@endsection