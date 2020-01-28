@extends('admin.layout.master')
@section('title','Blocked Accounts List')
@section('content')
    <h1>Blocked Accounts List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        @include('admin.inc.messages')
                    </div>
                    @if (session('role_id') == 1 || in_array(276, session('permissions')))
                        <div id="search_form" class="row mb-2 justify-content-center">
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <select name="search_admins[]" id="search_admins" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($sale_name as $admin)
                                            <option value="{{$admin->id}}">{{$admin->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-2">
                                <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                            </div>
                        </div>
                    @endif

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Account ID</th>
                                        <th class="border-primary border-darken-1">Company Name</th>
                                        <th class="border-primary border-darken-1">City Name</th>
                                        <th class="border-primary border-darken-1">Contact Person</th>
                                        <th class="border-primary border-darken-1">Phone Number</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">Email Address</th>
                                        <th class="border-primary border-darken-1">Sales Person Tagged</th>
                                        <th class="border-primary border-darken-1">Reason</th>
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
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        $('#search_admins').select2({
            width:'100%',
            placeholder:"Select Sale Persons",
            allowClear:true,
            dropdownParent:$('#search_form')
        });
        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
                body = [];
                var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.accounts.block.ajax') }}',
                    data: params,
                    success: function (result) {
                        head = [];

                        head.push('S.No');
                        head.push('Account ID');
                        head.push('Company Name');
                        head.push('City Name');
                        head.push('Contact Person');
                        head.push('Phone No.');
                        head.push('Company Address');
                        head.push('Email Address');
                        head.push('Sales Person Tagged');
                        head.push('Reason');
                        $.each(result.data, function(index, values) {
                            row = [];


                            row.push(index + 1);
                            row.push(values.id_padded);
                            row.push(values.name);
                            row.push(values.city);
                            row.push(values.poc);
                            row.push(values.phone);
                            row.push(values.address);
                            row.push(values.email);
                            row.push(values.admin_tag_id);
                            row.push(values.reason);

                            body.push(row);
                        });
                    },
                    async: false
                });

                return {body: body, header: head};
            }
        } );
        var table = $('.datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            scrollX: true, scrollY: '500px',
            buttons: [
                {
                    extend: 'excel',
                    title: 'Blocked Accounts',
                    className:'btn-primary',
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
            rowId:'id',
            order: [[1, 'desc']],
            ajax: {
                url: '{{ route('admin.accounts.block.ajax') }}',
                data: function (d) {
                    d.sale_persons = $('#search_admins').val();
                }
            },
            columns: [
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'id_padded', name: 'users.id', class: 'account_id'},
                {data: 'name', name: 'users.name', class: 'company_name'},
                {data: 'city', name: 'cities.name', class: 'city'},
                {data: 'poc', name: 'users.poc', class: 'contact_person'},
                {data: 'phone', name: 'users.phone', class: 'phone'},
                {data: 'address', name: 'users.address', class: 'address'},
                {data: 'email', name: 'users.email', class: 'email'},
                {data: 'admin_tag_id', name: 'ad.name', class: 'align-middle admin_tag_id'},
                {data: 'reason', name: 'users.blacklist_reason', class: 'reason'},
                {data: 'action', name: 'action', class: 'action', orderable: false, searchable: false}
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

                    if ($(header).is('.action') || $(header).is('.serial_number')) {
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
            }
        });
        $('body').on('click','button.blacklist',function () {
            var id = $(this).parents('tr').attr('id');
            var status = $(this).attr('rel');
            console.log(status);
            swal({
                title: 'Are You Sure?',
                text: 'Select Yes to Unblock this account!',
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: 'No',
                        value: null,
                        visible: true,
                        closeModal: true,
                    },
                    confirm: {
                        text: 'Yes',
                        value: true,
                        visible: true,
                        closeModal: true
                    }
                },
                closeOnClickOutside: false,
                closeOnEsc: false,
                dangerMode: true
            }).then(function (confirm) {
                if(confirm){
                    if(id){
                        $.ajax({
                            url: '{!! route('admin.accounts.status.block') !!}',
                            method: 'POST',
                            data: {
                                'id':id,
                                'status':status,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status == 1){
                                table.draw('false');
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }

                        });
                    }
                }
            });

        });
        $('#search_filter_btn').on('click',function () {
            table.draw();
        });
    });
</script>

@endsection

