@extends('admin.layout.master')

@section('title', 'Territory')

@section('content')
    <h1>Territory List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        @include('admin.inc.messages')
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
{{--                                    <th class="border-primary border-darken-1"></th>--}}
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Created By</th>
                                    <th class="border-primary border-darken-1">Created At</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">Action</th>

                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {


            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    if(params !== undefined){
                        params.start = 0;
                        params.length = -1;
                    }

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.territory.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Name');
                            head.push('City');

                            $.each(result.data, function(index, values) {
                            row = [];

                            row.push(index + 1);
                                row.push(values.name);
                                row.push(values.city);
                            body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

            var selected_rows = [];
            var table =  $('.datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',

                buttons: [{
                    text: 'Add Territory',
                    className: 'btn btn-primary',
                    enabled: true,
                    action: function (e, dt, node, config) {
                        window.location = '{{ route('admin.management.territory.add') }}';
                    }

                 },
                    {
                        extend: 'excel',
                        title: 'Territory List',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
                scrollX: false, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.management.territory.list') }}',
                rowId: 'id',
                order: [[4, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'territories.name', class: 'align-middle name'},
                    {data: 'city', name: 'c.name', class: 'align-middle city'},
                    {data: 'created_by', name: 'a.name', class: 'align-middle created_by'},
                    {data: 'created_at', name: 'territories.created_at', class: 'align-middle created_at'},
                    {data: 'updated_by', name: 'ad.name', class: 'align-middle updated_by'},
                    {data: 'updated_at', name: 'territories.updated_at', class: 'align-middle updated_at'},
                    {data: 'action', name: 'action', class: 'align-middle action'},
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

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select') ) {
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

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                console.log(id);

                if ($(this).hasClass('edit')) {
                    var link = '{{ route('admin.management.territory.edit', ["id" => 0]) }}';

                    window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
                }

            });


            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                console.log(id);
                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.assign_rider').enable();
                    table.button('.tag').enable();
                }
                else {
                    table.button('.assign_rider').disable();
                    table.button('.tag').disable();
                }
            });


        });

    </script>
    @endsection