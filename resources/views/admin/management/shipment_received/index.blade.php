@extends('admin.layout.master')

@section('title', 'Shipment Received Details')

@section('content')
    <h1>Shipment Received Details</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <form id="payment_form" class="form-horizontal" method="POST" action="{{ route('admin.management.shipment_received.excel_upload') }}" novalidate="novalidate" enctype="multipart/form-data">
                                        {{ csrf_field() }}
        
                                        <div class="row align-items-center justify-content-center">
                                            <div class="col">
                                                <div class="form-group">
                                                    <input type="file" name="receiver_detials" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                </div>
                                            </div>
        
                                            <div class="col">
                                                <div class="form-group text-left">
                                                    <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                                </div>
                                            </div>
        
                                            <div class="col ml-auto">
                                                <div class="form-group text-right">
                                                    <a href="{{ asset('file/shipment receive details.xlsx') }}" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                            

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Tracking No</th>
                                    <th class="border-primary border-darken-1">Receiver Name</th>
                                    <th class="border-primary border-darken-1">Receiver CNIC</th>
                                    <th class="border-primary border-darken-1">Receiver Relationship</th>
                                    <th class="border-primary border-darken-1">Created at</th>
                                    <th class="border-primary border-darken-1">Created by</th>
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

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>


    <script type="text/javascript">

        $(document).ready(function () {

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.shipment_received.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No');
                            head.push('Receiver Name');
                            head.push('Receiver CNIC');
                            head.push('Receiver Relationship');
                            head.push('Created at');
                            head.push('Created by');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_id);
                                row.push(values.receiverName);
                                row.push(values.receiverCnic);
                                row.push(values.relationship);
                                row.push(values.created_at);
                                row.push(values.created_by);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Department',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.management.shipment_received.list') }}',
                order: [[1, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return''; }
                    },
                    {data: 'tracking_id', name: 'shipment_receiver_details.tracking_number', class: 'align-middle tracking_id'},
                    {data: 'receiverName', name: 'shipment_receiver_details.receiver_name', class: 'align-middle receiverName'},
                    {data: 'receiverCnic', name: 'shipment_receiver_details.receiver_cnic', class: 'align-middle receiverCnic'},
                    {data: 'relationship', name: 'shipment_receiver_details.receiver_relationship', class: 'align-middle relationship'},
                    {data: 'created_at', name: 'shipment_receiver_details.created_at', class: 'align-middle text-center created_at'},
                    {data: 'created_by', name: 'admins.name', class: 'align-middle text-center created_by'}
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
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else {
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
            
        });
    </script>

@endsection