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
                                        <th class="border-primary border-darken-1"></th>
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Account ID</th>
                                        <th class="border-primary border-darken-1">Company Name</th>
                                        <th class="border-primary border-darken-1">Contact Person</th>
                                        <th class="border-primary border-darken-1">City</th>
                                        <th class="border-primary border-darken-1">Sales Person Tagged</th>
                                        <th class="border-primary border-darken-1">POC Tagged</th>
                                        <th class="border-primary border-darken-1">KAM Tagged</th>
                                        <th class="border-primary border-darken-1">REF Tagged</th>
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
    <div class="modal fade text-left" id="SalesTierTypeTagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTierTypeTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Sales Tiers</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id1">
                    <div class="mb-2">
                        <select name="poc" id="poc" class="form-control select2">
                            @foreach($sale_tier_types as $poc)
                                <option value="{{ $poc->id }}" > {{ $poc->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <select name="kam" id="kam" class="form-control select2">
                            @foreach($sale_tier_types as $kam)
                                <option value="{{ $kam->id }}" > {{ $kam->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="ref" id="ref" class="form-control select2">
                            @foreach($sale_tier_types as $ref)
                                <option value="{{ $ref->id }}" > {{ $ref->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salesTierTypeTagSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

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
        $("#poc").prepend('<option value="" selected></option>').select2({
            placeholder: "Select POC",
            width:'100%',
            dropdownParent:$('#SalesTierTypeTagModal')
        });
        $("#kam").prepend('<option value="" selected></option>').select2({
            placeholder: "Select KAM",
            width:'100%',
            dropdownParent:$('#SalesTierTypeTagModal')
        });
        $("#ref").prepend('<option value="" selected></option>').select2({
            placeholder: "Select REFFERAL",
            width:'100%',
            dropdownParent:$('#SalesTierTypeTagModal')
        });

        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
                body = [];
                var params = table.ajax.params();
                if(params !== undefined){
                    params.start = 0;
                    params.length = -1;
                }
                var jsonResult = $.ajax({
                    url: '{{ route('admin.accounts.block.ajax') }}',
                    data: params,
                    success: function (result) {
                        head = [];

                        head.push('S.No');
                        head.push('Account ID');
                        head.push('Company Name');
                        head.push('Contact Person');
                        head.push('City');
                        head.push('Sales Person Tagged');
                        head.push('POC Tagged');
                        head.push('KAM Tagged');
                        head.push('REF Tagged');
                        head.push('Reason');
                        $.each(result.data, function(index, values) {
                            row = [];


                            row.push(index + 1);
                            row.push(values.id_padded);
                            row.push(values.name);
                            row.push(values.poc);
                            row.push(values.city);
                            row.push(values.admin_tag_id);
                            row.push(values.poc);
                            row.push(values.kam);
                            row.push(values.ref);
                            row.push(values.reason);

                            body.push(row);
                        });
                    },
                    async: false
                });

                return {body: body, header: head};
            }
        } );
        var selected_rows = [];
        var hub_ids = [];
        var table = $('.datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            scrollX: true, scrollY: '500px',
            buttons: [
                {
                    text: 'Sales Tier Tagging',
                    className: 'btn btn-primary tag',
                    enabled:false,
                    action: function (e, dt, node, config) {
                        if(selected_rows != ''){

                            $('#SalesTierTypeTagModal').modal('show');
                            $('#salesTierTypeTagSubmit').on('click',function () {
                                var poc = $('#poc').val();
                                var kam = $('#kam').val();
                                var ref = $('#ref').val();
                                swal({
                                    text: 'Are you sure, you want to Tag?',
                                    icon: 'info',
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
                                }).then(function(confirm) {
                                    if (confirm) {

                                        $.ajax({
                                            url: '{!! route('admin.accounts.kam_poc_ref_tag.submit') !!}',
                                            method: 'POST',
                                            data: {
                                                'poc': poc,
                                                'kam': kam,
                                                'ref': ref,
                                                'shipper_ids[]': selected_rows,
                                                '_token': '{{ csrf_token() }}'
                                            }
                                        })
                                            .done(function (data) {
                                                if (data.status === 0) {
                                                    toastr.error(data.error, 'Error!', {
                                                        positionClass: 'toast-top-center',
                                                        containerId: 'toast-top-center'
                                                    });
                                                } else {
                                                    $('#SalesTierTypeTagModal').modal('hide');
                                                    toastr.success(data.success, 'Success!', {
                                                        positionClass: 'toast-bottom-center',
                                                        containerId: 'toast-bottom-center'
                                                    });
                                                }
                                                selected_rows = [];

                                                table.rows().deselect();
                                                $('#poc').val('').trigger('change');
                                                $('#kam').val('').trigger('change');
                                                $('#ref').val('').trigger('change');
                                                $('#SalesTierTypeTagModal').modal('hide');
                                                table.draw(true);
                                                table.button('.tag').disable();
                                                table.button('.assign_rider').disable();


                                            });
                                    }
                                });
                            });

                        }else{
                            var error = "Atleast Select One Shipper";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }
                },
                {
                    extend: 'selectAll',
                    text: 'Select All',
                    className: 'select_all',
                    action : function(e) {
                        e.preventDefault();

                        table.rows().nodes().each(function(index) {
                            var row = table.row(index);

                            if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                id = parseInt(row.id());

                                hub_id = $(row.node()).data('id');

                                var allow = false;

                                if(hub_ids.length == 0) {
                                    hub_ids.push(hub_id);

                                    allow = true;
                                }
                                else if(hub_ids[0] == hub_id) {
                                    allow = true;
                                }

                                if (allow) {
                                    row.select();

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.assign_rider').enable();
                                    table.button('.tag').enable();

                                }
                            }
                        });
                    }
                }, {
                    extend: 'selectNone',
                    text: 'Select None',
                    className: 'select_none',
                    action : function(e) {
                        e.preventDefault();

                        table.rows().nodes().each(function(index) {
                            var row = table.row(index);

                            if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                row.deselect();

                                id = parseInt(row.id());

                                var index = $.inArray(id, selected_rows);

                                if (index !== -1) {
                                    selected_rows.splice(index, 1);
                                }

                                if (selected_rows.length == 0) {
                                    table.button('.assign_rider').disable();
                                    table.button('.tag').disable();

                                    hub_ids.splice(index, 1);
                                }
                            }
                        });
                    }
                },
                {
                    extend: 'excel',
                    title: 'Blocked Accounts',
                    className:'btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                'reset'
            ],
            select: {
                info: false,
                style: 'multi',
                selector: 'td.select-checkbox',
                className: 'selected bg-primary bg-lighten-5 primary'
            },
            lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
                language: {
                    processing: data_table_loader
                },
            serverSide: true,
            deferLoading: 0,
            rowId:'id',
            order: [[1, 'desc']],
            ajax: {
                url: '{{ route('admin.accounts.block.ajax') }}',
                data: function (d) {
                    d.sale_persons = $('#search_admins').val();
                }
            },
            columns: [
                {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'id_padded', name: 'users.id', class: 'account_id'},
                {data: 'name', name: 'users.name', class: 'company_name'},
                {data: 'poc', name: 'users.poc', class: 'contact_person'},
                {data: 'city', name: 'cities.name', class: 'align-middle city'},
                {data: 'admin_tag_id', name: 'ad.name', class: 'align-middle admin_tag_id'},
                {data: 'poc_tagged', name: 'a.name', class: 'align-middle poc_tagged'},
                {data: 'kam', name: 'd.name', class: 'align-middle kam'},
                {data: 'ref', name: 'h.name', class: 'align-middle ref'},
                {data: 'reason', name: 'users.blacklist_reason', class: 'reason'},
                {data: 'action', name: 'action', class: 'action', orderable: false, searchable: false}
            ],
            rowCallback: function(row, data, index) {
              /*  var info = table.page.info();
                $('td:eq(0)', row).html(index + 1 + info.page * info.length);*/
                var info = table.page.info();

                $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                if ($.inArray(data.id, selected_rows) !== -1) {
                    table.row(row).select();
                }

            },
            initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select-checkbox')) {
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
        $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
            var id = parseInt($(this).parent('tr').attr('id'));

            var index = $.inArray(id, selected_rows);

            if (index === -1) {
                selected_rows.push(id);
            }
            else {
                selected_rows.splice(index, 1);
            }

            if (selected_rows.length > 0) {
                table.button('.tag').enable();
            }
            else {
                table.button('.tag').disable();
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

