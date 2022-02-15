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

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12">
                                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">

                                        <div class="col-4 mt-1">
                                            <fieldset class="form-group input-group">
                                                <input type="text" class="form-control" name="search_name" id="search_name" placeholder="Search Employee Name">
                                            </fieldset>
                                        </div>
                                        <div class="col-4 mt-1">
                                            <fieldset class="form-group input-group">
                                                <input type="text" class="form-control" name="search_phone_number" id="search_phone_number" placeholder="Search Phone Number">
                                            </fieldset>
                                        </div>

                                        <div class="col-4 mt-1">
                                            <fieldset class="form-group input-group">
                                                <input type="text" class="form-control" name="search_trax_id" id="search_trax_id" placeholder="Search Employee ID">
                                            </fieldset>
                                        </div>

                                        <div class="col-2 mt-1">
                                            <div class="form-group">
                                                <button type="button" id="search_filter_btn"
                                                        class="btn btn-outline-info btn-min-width"><i class="la la-search"></i>
                                                    Search
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Employee ID</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">Email</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Department</th>
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {
            $('#search_form #search_phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

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
                            head.push('Employee ID');
                            head.push('Name');
                            head.push('Phone Number');
                            head.push('Email');
                            head.push('City');
                            head.push('Designation');
                            head.push('Department');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.phone_number);
                                row.push(values.email);
                                row.push(values.city);
                                row.push(values.designation);
                                row.push(values.department_name);
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
                    text: '<i class="la la-file-excel-o"></i> Excel',
                    className: 'btn btn-primary datatable_excel_btn d-none',
                }],
                scrollX: true, scrollY: '500px',
                autoWidth: false,
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
                deferLoading: 0,
                ajax: {
                    url: '{{ route('admin.trax_directory.list')}}',
                    data: function (d) {

                        d.search_name = $('#search_name').val();
                        d.search_phone = $('#search_phone_number').val();
                        d.search_trax_id = $('#search_trax_id').val();

                    }
                },
                rowId: 'id',
                order: [[1, 'asc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'pickup_address_id', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'trax_id', name: 'e.trax_id', class: 'text-center align-middle trax_id',},
                    {data: 'name', name: 'e.name', class: 'align-middle name'},
                    {data: 'phone_number', name: 'e.phone_number', class: 'align-middle phone_number'},
                    {data: 'email', name: 'e.official_email', class: 'align-middle email'},
                    {data: 'city', name: 'c.name', class: 'align-middle city'},
                    {data: 'designation', name: 'd.name', class: 'align-middle designation'},
                    {data: 'department_name', name: 'ad.name', class: 'align-middle department_name'},
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

            $('#search_filter_btn').on('click',function () {
               var search_name = $('#search_name').val();
               var search_phone = $('#search_phone_number').val();
               var search_trax_id = $('#search_trax_id').val();
               if(search_name == '' && search_phone == '' && search_trax_id == ''){
                   toastr.error("Provide atleast one parameter", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
               }else{
                   $('.datatable_excel_btn').removeClass('d-none');
                   table.draw(true);
               }
            });
        });
    </script>
    @endsection
