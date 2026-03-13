@extends('admin.layout.master')

@section('title', 'Expected Shipment Penalties List')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Expected Shipment Penalties List
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Shipper Name</th>
                                    <th class="border-primary border-darken-1">Expected Shipments</th>
                                    <th class="border-primary border-darken-1">Arrived Shipments</th>
                                    <th class="border-primary border-darken-1">Percentage Applied</th>
                                    <th class="border-primary border-darken-1">Amount</th>
                                    <th class="border-primary border-darken-1">Total Amount</th>
                                    <th class="border-primary border-darken-1">Adjustment Month</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Approved/Rejected By</th>
                                    <th class="border-primary border-darken-1">Approved/Rejected Date</th>
                                    <th class="border-primary border-darken-1">Created At</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
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
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function () {


            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                       
                ],
                scrollX: true, scrollY: '800px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                ajax: '{{ route('admin.finance.expected_shipment_penalty.list') }}',
                rowId: 'id',
                order: [[10, 'desc']],
                columns: [  
                    {
                        data: 'serial_number',
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'tracking_number', name: 's.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name'},
                    {data: 'average_shipments', name: 'esp.average_shipments', class: 'align-middle average_shipments'},
                    {data: 'recorded_shipments', name: 'esp.recorded_shipments', class: 'align-middle recorded_shipments'},
                    {data: 'percentage_applied', name: 'esp.percentage_applied', class: 'align-middle percentage_applied'},
                    {data: 'amount', name: 'esp.amount', class: 'align-middle amount'},
                    {data: 'total_charges', name: 'esp.total_charges', class: 'align-middle total_charges'},
                    {data: 'applied_month', name: 'esp.applied_month', class: 'align-middle applied_month'},
                    {data: 'status', name: 'esp.status', class: 'align-middle status'},
                    {data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
                    {data: 'status_updated_at', name: 'esp.status_updated_at', class: 'align-middle status_updated_at'},
                    {data: 'created_at', name: 'esp.created_at', class: 'align-middle created_at'},
                    {
                        data: 'action',
                        name: 'action',
                        class: 'text-center align-middle action p-1',
                        orderable: false,
                        searchable: false
                    }
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                  
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                    '<option value="1">Created</option>' +
                    '<option value="2">Approved</option>' +
                    '<option value="3">Rejected</option>' +
                    '</select>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.updated_by') || $(header).is('.status_updated_at') || $(header).is('.created_at') || $(header).is('.average_shipments')  || $(header).is('.recorded_shipments') || $(header).is('.amount')  || $(header).is('.percentage_applied') ) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                     $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select a Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });     


            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.reject', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route("admin.finance.expected_shipment_penalty.reject") !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    toastr.success(data.success, 'Success!', {
                        positionClass: 'toast-bottom-center',
                        containerId: 'toast-bottom-center'
                    });
                    table.draw();
                });
            });

            
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.approve', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route("admin.finance.expected_shipment_penalty.approve") !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    toastr.success(data.success, 'Success!', {
                        positionClass: 'toast-bottom-center',
                        containerId: 'toast-bottom-center'
                    });
                    table.draw();
                });
            });
        });
    </script>
@endsection