@extends('admin.layout.master')

@section('title', 'POC and KAM Tagged Accounts')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    POC and KAM Tagged Accounts
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Account ID</th>
                                    <th class="border-primary border-darken-1 ">Shipper</th>
                                    <th class="border-primary border-darken-1 ">City</th>
                                    <th class="border-primary border-darken-1 ">POC</th>
                                    <th class="border-primary border-darken-1 ">Phone</th>
                                    <th class="border-primary border-darken-1 ">Address</th>
                                    <th class="border-primary border-darken-1 ">Email</th>
                                    <th class="border-primary border-darken-1 ">Product Type</th>
                                   {{-- <th class="border-primary border-darken-1 ">Status</th>--}}
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">


    <style>
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .selectize-control {
            width: 100%;
        }

        /* .selectize-control .selectize-input {
             vertical-align: middle;
         }

         .selectize-control .selectize-input .item {
             word-break: break-all;
         }*/
    </style>
@endsection
@section('js')

<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function () {

        jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
            if (this.context.length) {
                blockPagePermanently();
                body = [];
                var params = table.ajax.params();
                params.start = 0;
                params.length = -1;
                params.excel = true;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.shipment.poc_kam_tagged_accounts.list') }}',
                    data: params,
                    success: function (result) {
                        head = [];
                        head.push('S.No');
                        head.push('Account ID');
                        head.push('Shipper Name');
                        head.push('City');
                        head.push('POC');
                        head.push('Phone');
                        head.push('Address');
                        head.push('Email');
                        head.push('Product Type');
                       // head.push('Status');



                        $.each(result.data, function (index, values) {
                            row = [];
                            row.push(index + 1);
                            row.push(values.account_id);
                            row.push(values.shipper_name);
                            row.push(values.city);
                            row.push(values.poc);
                            row.push(values.phone);
                            row.push(values.address);
                            row.push(values.email);
                            row.push(values.product_type);
                           // row.push(values.status);
                            body.push(row);
                        });
                    },
                    async: false
                });
                UnblockPagePermanently();

                return {body: body, header: head};
            }
        });


        var table = $('#datatable').DataTable({
            scrollX: false, scrollY: '500px',
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            buttons: [
                {
                    extend: 'excel',
                    title: ' POC and KAM Tagged Accounts',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o "></i> Excel',
                },
            ],
            lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            ajax: {
                url: '{{ route('admin.shipment.poc_kam_tagged_accounts.list') }}',

            },
            rowId: 'id',
            order: [[1, 'asc']],
            columns: [
                {orderable: false,searchable: false,name: 'serial_number',class: 'align-middle serial_number',targets: 0, render: function (data, type, row) { return '';}},
                {data: 'account_id',name: 'u.id',class: 'align-middle text_center account_id '},
                {data: 'shipper_name',name: 'u.name',class: 'align-middle text_center shipper_name '},
                {data: 'city',name: 'c.name',class: 'align-middle text_center city '},
                {data: 'poc',name: 'u.poc',class: 'align-middle text_center poc '},
                {data: 'phone',name: 'u.phone',class: 'align-middle text_center phone '},
                {data: 'address',name: 'u.address',class: 'align-middle text_center address '},
                {data: 'email',name: 'u.email',class: 'align-middle text_center email '},
                {data: 'product_type',name: 'p.product_name',class: 'align-middle text_center product_type '},
               // {data: 'status',name: 'u.status',class: 'align-middle text_center status '},

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
                var drop_select = '<select name="status" id="status" class="select2 form-control">' +
                    '<option value="0">Request Received</option>' +
                    '<option value="1">Rates Added</option>' +
                    '<option value="2">Pending For Activation</option>' +
                    '<option value="5">Rates Rejected</option>' +
                    '</select>';

                var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                    '</select>';
                var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                this.api().columns().every(function (column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.action') || $(header).is('.serial_number')) {
                        $(td).appendTo($(search));
                    }
                    else if($(header).is('.status')) {
                        $(drop_select).appendTo($(search))
                            .on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td);
                    }else {
                        var current = $(input).appendTo($(search)).on('change', function () {
                            column.search($(this).val(), false, false, true).draw();
                        }).wrap(td).after(icon);

                        if (column.search()) {
                            current.val(column.search());
                        }
                    }
                });
                $("#status").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select a Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                this.api().table().columns.adjust();
            }
        });

    });
</script>
@endsection
