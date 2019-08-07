
@extends('admin.layout.master')
@section('title','Merged Accounts')

@section('content')
    <h1 class="mb-1">
        Merged Accounts
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Name.</th>
                        <th class="border-primary border-darken-1">Accounts</th>
                        <th class="border-primary border-darken-1">Created At</th>
                        <th class="border-primary border-darken-1">Created By</th>
                        <th class="border-primary border-darken-1">Updated At</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <!--Accounts popup -->
    <div class="modal fade" id="accounts_modal" data-backdrop="static" role="dialog" aria-labelledby="accounts_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Accounts(s)</h4>

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
    <!--Accounts popup -->

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
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

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.accounts.sister_account.merged_account.list') }}',
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Name');
                            head.push('Accounts');
                            head.push('Created At');
                            head.push('Created By');
                            head.push('Updated At');
                            head.push('Updated By');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.accounts);
                                row.push(values.created_at);
                                row.push(values.created_by);
                                row.push(values.updated_at);
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
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Merged Accounts',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
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
                    url: '{{ route('admin.accounts.sister_account.merged_account.list') }}',
                },
                rowId: 'id',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'name' ,name: 'merged_account_heads.id', class: 'align-middle name'},
                    { data:'accounts_button' ,name: 'accounts_button', class: 'align-middle accounts_button', orderable: false, searchable: false},
                    { data:'created_at' ,name: 'merged_account_heads.created_at', class: 'align-middle created_at'},
                    { data:'created_by' ,name: 'ac.name', class: 'align-middle created_by'},
                    { data:'updated_at' ,name: 'merged_account_heads.updated_at', class: 'align-middle updated_at'},
                    { data:'updated_by' ,name: 'au.name', class: 'align-middle updated_by'},
                    {data:'action' ,name: 'action', class: 'align-middle action',orderable: false, searchable: false}
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

                        if ($(header).is('.serial_number') || $(header).is('.accounts') || $(header).is('.action')) {
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

            $('#datatable tbody').on('click','tr td.accounts_button button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#accounts_modal .modal-body').html('');
                $('#accounts_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.accounts.sister_account.merged_account.info') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        // console.log(data);
                        if (data) {
                            var html = '';

                            if (data.accounts) {
                                var html = '';
                                html += '<table class="table table-sm datatable text-center">';
                                html += '<thead><tr><th><strong>Account No.</strong></th><th><strong>Company Name</strong><th><strong>Poc</strong></th><th><strong>Phone</strong></th><th><strong>City</strong></th><th><strong>Address</strong></th></tr></thead>';
                                html += '<tbody>';
                                $.each(data.accounts, function(index, account) {
                                    var account_id = ''+account['id']+'';
                                    var account_id_pad = account_id.padStart(6, '0');
                                    html += '<tr class=""><td>' + account_id_pad + '</td>';
                                    html += '<td>' + account['name'] + '</td>';
                                    html += '<td>' + account['poc'] + '</td>';
                                    html += '<td>' + account['phone'] + '</td>';
                                    html += '<td>' + account['city'] + '</td>';
                                    html += '<td>' + account['address'] + '</td></tr>';
                                });
                                html += '</tbody></table>';
                                // $.each(data.accounts, function(index, account) {
                                //     html += '<b>' + account['name']  + '</b><br>';
                                // });
                            }
                            $('#accounts_modal .modal-body').html(html);
                        }
                    });

            });
        })
    </script>
@endsection