
@extends('admin.layout.master')
@section('title','Misrouted History')

@section('content')
    <h1 class="mb-1">
        Misrouted History
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
                        <th class="border-primary border-darken-1">Changed On</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Old Consignee City</th>
                        <th class="border-primary border-darken-1">Old Consignee Name</th>
                        <th class="border-primary border-darken-1">Old Consignee Address</th>
                        <th class="border-primary border-darken-1">Old Consignee Phone 1</th>
                        <th class="border-primary border-darken-1">Old Consignee Phone 2</th>
                        <th class="border-primary border-darken-1">Old Consignee Email</th>
                        <th class="border-primary border-darken-1">New Consignee City</th>
                        <th class="border-primary border-darken-1">New Consignee Name</th>
                        <th class="border-primary border-darken-1">New Consignee Address</th>
                        <th class="border-primary border-darken-1">New Consignee Phone 1</th>
                        <th class="border-primary border-darken-1">New Consignee Phone 2</th>
                        <th class="border-primary border-darken-1">New Consignee Email</th>
                        <th class="border-primary border-darken-1">Updated By</th>

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


            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.delivery.misroute.history.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking .No');
                            head.push('Changed On');
                            head.push('Origin');
                            head.push('Old Consignee City');
                            head.push('Old Consignee Name');
                            head.push('Old Consignee Address');
                            head.push('Old Consignee Phone 1');
                            head.push('Old Consignee Phone 2');
                            head.push('Old Consignee Email');
                            head.push('New Consignee City');
                            head.push('New Consignee Name');
                            head.push('New Consignee Address');
                            head.push('New Consignee Phone 1');
                            head.push('New Consignee Phone 2');
                            head.push('New Consignee Email');
                            head.push('Updated By');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.created_at);
                                row.push(values.origin);
                                row.push(values.old_consignee_city);
                                row.push(values.old_consignee_name);
                                row.push(values.old_consignee_address);
                                row.push(values.old_consignee_phone_number_1);
                                row.push(values.old_consignee_phone_number_2);
                                row.push(values.old_consignee_email);
                                row.push(values.new_consignee_city);
                                row.push(values.new_consignee_name);
                                row.push(values.new_consignee_address);
                                row.push(values.new_consignee_phone_number_1);
                                row.push(values.new_consignee_phone_number_2);
                                row.push(values.new_consignee_email);
                                row.push(values.updated_by);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Misrouted History',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                scrollX: true, scrollY: '350px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.delivery.misroute.history.list') }}',
                order: [[2, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'created_at', name: 'misrouted_history.created_at', class: 'align-middle created_at'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'old_consignee_city', name: 'odc.name', class: 'align-middle old_consignee_city'},
                    {data: 'old_consignee_name', name: 'misrouted_history.old_consignee_name', class: 'align-middle old_consignee_name'},
                    {data: 'old_consignee_address', name: 'misrouted_history.old_consignee_address', class: 'align-middle old_consignee_address'},
                    {data: 'old_consignee_phone_number_1', name: 'misrouted_history.old_consignee_phone_number_1', class: 'align-middle old_consignee_phone_number_1'},
                    {data: 'old_consignee_phone_number_2', name: 'misrouted_history.old_consignee_phone_number_2', class: 'align-middle old_consignee_phone_number_2'},
                    {data: 'old_consignee_email', name: 'misrouted_history.old_consignee_email', class: 'align-middle old_consignee_email'},
                    {data: 'new_consignee_city', name: 'odc.name', class: 'align-middle new_consignee_city'},
                    {data: 'new_consignee_name', name: 'misrouted_history.new_consignee_name', class: 'align-middle new_consignee_name'},
                    {data: 'new_consignee_address', name: 'misrouted_history.new_consignee_address', class: 'align-middle new_consignee_address'},
                    {data: 'new_consignee_phone_number_1', name: 'misrouted_history.new_consignee_phone_number_1', class: 'align-middle new_consignee_phone_number_1'},
                    {data: 'new_consignee_phone_number_2', name: 'misrouted_history.new_consignee_phone_number_2', class: 'align-middle new_consignee_phone_number_2'},
                    {data: 'new_consignee_email', name: 'misrouted_history.new_consignee_email', class: 'align-middle new_consignee_email'},
                    {data: 'updated_by', name: 'ad.name', class: 'align-middle updated_by'},

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


                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.status')) {
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