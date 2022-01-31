@extends('admin.layout.master')

@section('title', 'Trax Directory')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   Trax Directory
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">Outlook ID</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">Official Phone Number</th>
                                    <th class="border-primary border-darken-1">Emergency Contact No.</th>
                                    <th class="border-primary border-darken-1">Emergency Contact Person</th>
                                    <th class="border-primary border-darken-1">Created At</th>
                                   {{-- <th class="border-primary border-darken-1"></th>--}}
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <style>
        .selectize-control {
            width: 300px !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.trax_directory.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('City');
                            head.push('Designation');
                            head.push('Name');
                            head.push('Outlook ID');
                            head.push('Phone');
                            head.push('Official Phone');
                            head.push('Emergency Contact No.');
                            head.push('Emergency Contact Person');
                            head.push('Created Datetime');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.city);
                                row.push(values.designation);
                                row.push(values.name);
                                row.push(values.email);
                                row.push(values.phone);
                                row.push(values.official_phone);
                                row.push(values.emergency_contact);
                                row.push(values.emergency_contact_person);
                                row.push(values.date);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    return {body: body, header: head};
                }
            });
           /* var selected_rows = [];*/
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    extend: 'excel',
                    title: 'Trax Directory',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
               /* select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },*/
                serverSide: true,
                ajax: '{{ route('admin.trax_directory.list') }}',
                rowId: 'id',
                order: [[9, 'asc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'pickup_address_id', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'city', name: 'h.name', class: 'text-center align-middle city',},
                    {data: 'designation', name: 'ed.name', class: 'align-middle designation'},
                    {data: 'name', name: 'admins.name', class: 'align-middle name'},
                    {data: 'email', name: 'admins.email', class: 'align-middle email'},
                    {data: 'phone', name: 'admins.phone_number', class: 'align-middle phone'},
                    {data: 'official_phone', name: 'admins.official_phone_number', class: 'align-middle official_phone'},
                    {data: 'emergency_contact', name: 'emp.emergency_contact', class: 'align-middle emergency_contact'},
                    {data: 'emergency_contact_person', name: 'emp.emergency_contact_person', class: 'align-middle emergency_contact_person'},
                    {data: 'date', name: 'admins.created_at', class: 'align-middle date'},
                    //{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {

                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                  /*  $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }*/
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());
                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select-checkbox')) {
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
        });
    </script>
    @endsection
