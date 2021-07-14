@extends('admin.layout.master')

@section('title', 'Multiple Sale Person Tagging')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Multiple Sale Person Tagging
                </h1>
                {{--{{dd($case_nature)}}--}}

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Lead</th>
                                    <th class="border-primary border-darken-1">Sale Persons</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--View Modal--}}
    <div class="modal fade text-left" id="AddLeadsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddLeadsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Lead</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.settings.multiple_sale_tagging.submit')}}" method="post" id="add_lead_form" class="form-horizontal text-center" novalidate="novalidate">
                    {{ csrf_field() }}
                    <div class="modal-body text-center">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col">
                                    <fieldset class="form-group">
                                        <select name="lead" id="lead_select" class="form-control select2" required data-rule-required="true" data-msg-required="This field is required">
                                            @foreach($admins as $admin)
                                                <option value="{{$admin->id}}">{{$admin->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary width-100" id="add_lead_button">Add</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="AssignAdminsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignAdminsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Assign Users</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.settings.multiple_sale_tagging.assign_admin.submit')}}" method="post" id="assign_user_form" class="form-horizontal text-center" novalidate="novalidate">
                    {{ csrf_field() }}
                    <div class="modal-body text-center">
                        <div class="container">
                            <div class="row justify-content-center">
                                <input type="hidden" name="lead_id" id="lead_id">
                                <div class="col" id="assing_div">
                                    <fieldset class="form-group">
                                        <select name="admins[]" id="assign_select" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary width-100" id="assign_admin_button">Assign</button>
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="ViewAssignedAdminsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewAssignedAdminsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Assigned Users</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col" id="view_assigned_div">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
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
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#lead_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Lead",
                allowClear:true,
                dropdownParent:$('#add_lead_form')
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.multiple_sale_tagging.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Lead');
                            head.push('Sale Persons');
                            head.push('Updated At');
                            head.push('Updated By');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.head_admin);
                                row.push(values.tagged_admins);
                                row.push(values.updated_at);
                                row.push(values.updated_by);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );            var index_column = 0;
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add Lead',
                        className: 'btn btn-primary add_type',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            // $('#AddHoliday .modal-body').html(html);
                            $('#AddLeadsModal').modal('show');
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'Multiple Sale Person Tagging',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                autoWidth: false,
                language: {
                    processing: data_table_loader
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.settings.multiple_sale_tagging.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                },
                rowid: 'id',
                order: [[3, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'head_admin', name: 'a.name', class: 'align-middle head_admin'},
                    {data: 'tagged_admins_count', class: 'align-middle tagged_admins', orderable: false, searchable: false},
                    {data: 'updated_at', name: 'multiple_sale_leads.updated_at', class: 'align-middle updated_at'},
                    {data: 'updated_by', name: 'ua.name', class: 'align-middle updated_by'},
                    {data: 'action', class: 'align-middle action', orderable: false, searchable: false}

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


                        if ($(header).is('.serial_number') || $(header).is('.tagged_admins') || $(header).is('.action')) {
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

            $('#add_lead_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    // blockPagePermanently();
                    swal({
                        text: 'Are you sure you want to add lead?',
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
                    }).then(function(confirm) {
                        if(confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'Adding Lead!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });

                            form.submit();
                        }
                        else {
                            $(form).find('button[type=submit]').prop('disabled', false);
                            // UnblockPagePermanently();
                        }
                    });
                }
            });

            {{--$('#add_lead_button').on('click', function () {--}}
                {{--var lead = $('#lead_select').val();--}}
                {{--var flag = true;--}}
                {{--if(!lead){--}}
                    {{--flag = false;--}}
                    {{--var error = "Please select Lead!";--}}
                    {{--toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
                {{--}--}}
                {{--if(flag){--}}
                    {{--$('#add_lead_button').attr('disabled',true);--}}
                    {{--$.ajax({--}}
                        {{--url: '{!! route('admin.settings.multiple_sale_tagging.submit') !!}',--}}
                        {{--method: 'POST',--}}
                        {{--data: {--}}
                            {{--'_token': '{{ csrf_token() }}',--}}
                            {{--'lead': lead--}}
                        {{--}--}}
                    {{--})--}}
                        {{--.done(function(data) {--}}
                            {{--if (data.status == 1) {--}}
                                {{--toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
                                {{--$('#AddLeadsModal').modal('hide');--}}
                                {{--table.draw();--}}
                            {{--}--}}
                            {{--else{--}}
                                {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
                            {{--}--}}
                            {{--$('#add_lead_button').attr('disabled',false);--}}
                        {{--});--}}
                {{--}--}}
            {{--});--}}

            $('#AddLeadsModal').on('hide.bs.modal', function (e) {
                $('#lead_select').val('').trigger('change');
            });

            $('body').on('click','.assign',function (e) {
                var id = $(this).data('target-id');
                var html = '';
                if(id) {
                    $.ajax({
                        url: '{!! route('admin.settings.multiple_sale_tagging.assign_admin.view') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'head_id': id
                        }
                    }).done(function (data) {
                        if (data.status) {
                            $('#assign_user_form #lead_id').val(id);
                            data.admins.forEach(function (admin) {
                                $('#assign_select').append('<option value="' + admin.id + '">' + admin.name + '</option>');
                            });
                            $('#assing_div').append(html);

                            $('#assign_select').select2({
                                width:'100%',
                                placeholder:"Select Users",
                                allowClear:true,
                                dropdownParent:$('#assign_user_form')
                            });
                            $('#AssignAdminsModal').modal('show');
                        }
                    });
                }
            });

            $('body').on('click','.tagged',function () {
                var id = $(this).data('target-id');
                if(id) {
                    $.ajax({
                        url: '{!! route('admin.settings.multiple_sale_tagging.assign_admin.view_assigned') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id
                        }
                    }).done(function (data) {
                        if (data.status) {
                            var serial = 1;
                            var html = '<div id="table_view"><table class="table table-sm table-bordered"><tbody>';
                            html += '<tr>';
                            html += '<td class="border-primary border-darken-1 align-middle text-center"><strong>S No.</strong></td>';
                            html += '<td class="border-primary border-darken-1 align-middle text-center"><strong>Users</strong></td>';
                            html += '</tr>';

                            data.tagged_users.forEach(function (users) {
                                html += '<tr>';
                                html += '<td class="align-middle text-center">' + serial + '</td>';
                                html += '<td class="align-middle text-center">' + users + '</td>';
                                html += '</tr>';
                                serial++;
                            });
                            html += '</tbody></table></div>';
                            $('#view_assigned_div').append(html);

                            $('#ViewAssignedAdminsModal').modal('show');

                            $('#ViewAssignedAdminsModal').on('hide.bs.modal', function (e) {
                                $('#table_view').remove();
                            });
                        }
                    });
                }
            });
            $('#AssignAdminsModal').on('hide.bs.modal', function (e) {
                $('#assign_select').find('option').remove();
            });
            $('#assign_user_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    // blockPagePermanently();
                    swal({
                        text: 'Are you sure you want to assign users?',
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
                    }).then(function(confirm) {
                        if(confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'Users are being assigned!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });

                            form.submit();
                        }
                        else {
                            $(form).find('button[type=submit]').prop('disabled', false);
                            // UnblockPagePermanently();
                        }
                    });
                }
            });
        });

    </script>
@endsection