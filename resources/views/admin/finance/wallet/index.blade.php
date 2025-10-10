@extends('admin.layout.master')
@section('title', 'Wallet Customers')

@section('content')
    <h1 class="mb-1">
        Wallet Customers
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Wallet ID</th>
                            <th class="border-primary border-darken-1">Wallet Shipper name</th>
                            <th class="border-primary border-darken-1">Financing Product Type</th>
                            <th class="border-primary border-darken-1">Shipper Email</th>
                            <th class="border-primary border-darken-1">Shipper Phone</th>
                            <th class="border-primary border-darken-1">Shipper CNIC</th>
                            <th class="border-primary border-darken-1">Parent Shipper</th>
                            <th class="border-primary border-darken-1">Substitute User</th>
                            
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">

    <style>
        .table {
            width: 100% !important;
        }
    </style>

@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/tags/tagging.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
            if (this.context.length) {
                body = [];
                var params = table.ajax.params();
                params.start = 0;
                params.length = -1;
                params.excel = true;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.finance.wallet_users.list') }}',
                    data: params,
                    success: function(result) {
                        head = [];

                        head.push('S.No');
                        head.push('Wallet ID');
                        head.push('Wallet User Name');
                        head.push('Financing Product Type');
                        head.push('Shipper Email');
                        head.push('Shipper Phone');
                        head.push('Shipper CNIC');
                        head.push('Parent Shipper');
                        head.push('Substitute User');
                        

                        $.each(result.data, function(index, values) {
                            row = [];
                            row.push(index + 1);
                            row.push(values.wallet_id);
                            row.push(values.wallet_user_name);
                            row.push(values.finova_account_type);
                            row.push(values.wallet_user_email);
                            row.push(values.wallet_user_phone);
                            row.push(values.wallet_user_cnic);
                            row.push(values.parent_user_name);
                            row.push(values.substitute_name);
                            
                            body.push(row);
                        });
                    },
                    async: false
                });

                return {
                    body: body,
                    header: head
                };
            }
        });

        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            buttons: [{
                    extend: 'excel',
                    title: 'Wallet Users',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                'reset'
            ],
            scrollX: true,
            scrollY: '500px',
            lengthMenu: [
                [50, 100, 500, 1000, -1],
                [50, 100, 500, 1000, 'All']
            ],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            deferLoading: 0,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            ajax: {
                url: '{{ route('admin.finance.wallet_users.list') }}',
                data: function(d) {
                    d.tracking_numbers = $('#tracking_number').val();
                }
            },
            rowId: 'shId',
            order: [
                [1, 'desc']
            ],
            columns: [
                {
                    orderable: false,
                    searchable: false,
                    name: 'serial_number',
                    class: 'align-middle serial_number',
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },

                {
                    data: 'wallet_id',
                    name: 'wallet_id',
                    class: 'align-middle wallet_id',
                    orderable: false
                },

                {
                    data: 'wallet_user_name',
                    name: 'wallet_users.name',
                    class: 'align-middle wallet_user_name',
                    orderable: false
                },
                {
                    data: 'finova_account_type',
                    name: 'finova_account_type',
                    class: 'align-middle finova_account_type',
                    orderable: false
                },
                {
                    data: 'wallet_user_email',
                    name: 'wallet_users.email',
                    class: 'align-middle wallet_user_email',
                    orderable: false
                },

                {
                    data: 'wallet_user_phone',
                    name: 'wallet_users.phone',
                    class: 'align-middle wallet_user_phone',
                    orderable: false
                },

                {
                    data: 'wallet_user_cnic',
                    name: 'wallet_users.cnic',
                    class: 'align-middle wallet_user_cnic',
                    orderable: false
                },

                {
                    data: 'parent_user_name',
                    name: 'parent_user_name',
                    class: 'align-middle parent_user_name',
                    orderable: false
                },

                {
                    data: 'substitute_name',
                    name: 'substitute_name',
                    class: 'align-middle substitute_name',
                    orderable: false
                },
               
            ],
        });
        table.draw();
    </script>
@endsection
