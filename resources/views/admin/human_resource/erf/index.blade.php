@extends('admin.layout.master')

@section('title', 'ERF Dashboard')

@section('content')
    <h1>ERF Dashboard</h1>

    <section>
        <div class="row">

            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <select name="search_status" id="search_status" class="form-control select2">
                                        @foreach($erf_status as $status)
                                            <option value="{{$status->id}}">{{$status->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">ERF ID</th>
                                    <th class="border-primary border-darken-1">Type</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Trax ID</th>
                                    <th class="border-primary border-darken-1">Leaver Name</th>
                                    <th class="border-primary border-darken-1">Requested By</th>
                                    <th class="border-primary border-darken-1">Hub</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Line Manager</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Employee Status</th>
                                    <th class="border-primary border-darken-1">Aging</th>
                                    <th class="border-primary border-darken-1">Requested At</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="file_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="file_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Input File</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <form method="post" id="file_upload" enctype="multipart/form-data" novalidate="novalidate" action="{{route('admin.human_resource.erf.file_upload')}}">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="erf_id" id="erf_id">
                            <input type="file" class="form-control" id="file" name="file" placeholder="Select File" data-rule-required="true" data-msg-required="File is required">
                        </div>
                        <div class="form-group text-center mt-2">
                            <button type="submit" class="btn btn-primary" id="form_btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reject_reason_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="reject_reason_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Enter Reason</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <form method="post" id="reject_reason_form"  novalidate="novalidate" action="{{route('admin.human_resource.erf.reject_reason')}}">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="erf_id" id="erf_id">
                            <input type="text" class="form-control" id="reason" name="reason" placeholder="Enter Reason" data-rule-required="true" data-msg-required="Reason is required">
                        </div>
                        <div class="form-group text-center mt-2">
                            <button type="submit" class="btn btn-primary" id="reason_submit_btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="documents_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="documents_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">View Documents</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">

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
        .selectize-control {
            width: 100%;
        }

        .selectize-control .selectize-input {
            vertical-align: middle;
        }

        .selectize-control .selectize-input .item {
            word-break: break-all;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>

        $(document).ready(function() {
            $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Search Status',
                allowClear: true
            }).bind('change', function () {
                table.draw();
            });
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.erf.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('ERF ID');
                            head.push('Type');
                            head.push('Department');
                            head.push('Designation');
                            head.push('trax_id');
                            head.push('leaver_name');
                            head.push('requested_by');
                            head.push('Hub');
                            head.push('City');
                            head.push('Line Manager');
                            head.push('Status');
                            head.push('Employee Status');
                            head.push('Aging');
                            head.push('requested_at');


                            $.each(result.data, function (index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.erf_id);
                                row.push(values.type);
                                row.push(values.department);
                                row.push(values.designation);
                                row.push(values.trax_id_for_excel);
                                row.push(values.leaver_name_for_excel);
                                row.push(values.requested_by);
                                row.push(values.hub);
                                row.push(values.city);
                                row.push(values.admin);
                                row.push(values.status);
                                row.push(values.es);
                                row.push(values.aging);
                                row.push(values.requested_at);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var route = '<?php echo route('admin.human_resource.erf.add'); ?>';
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if(session('role_id') == 1 ||  in_array(522, session('permissions')))
                    {
                        title: 'Add ERF',
                        className: 'btn btn-primary',
                        text: '<i class="la la-plus"></i> Add ERF',
                        action: function (e) {
                            window.location = route;

                        }
                    },
                        @endif

                    {
                        extend: 'excel',
                        title: 'ERF List',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                scrollX: false, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.human_resource.erf.list') }}',
                    data: function (d) {
                        d.search_status = $('#search_status').val();
                    }
                },
                rowId: 'shId',
                order: [[1, 'desc']],
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },

                    {data: 'erf_id', name: 'erf_id', class: 'align-middle erf_id'},
                    {data: 'type', name: 'employee_requisitions.type', class: 'align-middle type'},
                    {data: 'department', name: 'dp.name', class: 'align-middle department'},
                    {data: 'designation', name: 'd.name', class: 'align-middle designation'},
                    {data: 'trax_id', name: 'a.trax_id', class: 'align-middle trax_id', orderable: false, sortable: false},
                    {data: 'leaver_name', name: 'a.name', class: 'align-middle leaver_name', orderable: false, sortable: false},
                    {data: 'requested_by', name: 'a.name', class: 'align-middle requested_by'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'city', name: 'c.name', class: 'align-middle city'},
                    {data: 'admin', name: 'a.name', class: 'align-middle admin'},
                    {data: 'status', name: 's.name', class: 'align-middle status'},
                    {data: 'es', name: 'employee_requisitions.employee_status', class: 'align-middle es'},
                    {data: 'aging', name: 'aging', class: 'align-middle text-center aging', orderable: false, sortable: false},
                    {data: 'requested_date', name: 'employee_requisitions.created_at', class: 'align-middle requested_date'},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, sortable: false},


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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.aging') || $(header).is('.trax_id') || $(header).is('.leaver_name')) {
                            $(td).appendTo($(search));
                        } else {
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
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function () {
                var erf_id = table.row($(this).parents('tr')).data().erf_id;

                if ($(this).hasClass('admin_approve')) {
                    $('#erf_id').val(erf_id);
                    $('#file_modal').modal('show');
                }
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function () {
                var erf_id = table.row($(this).parents('tr')).data().erf_id;

                if ($(this).hasClass('admin_reject')) {
                    $('#reject_reason_modal #erf_id').val(erf_id);
                    $('#reject_reason_modal').modal('show');
                }
            });


            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function () {

                var erf_id = table.row($(this).parents('tr')).data().erf_id;

                if ($(this).hasClass('approve_request')) {
                    $.ajax({
                        url: '{!! route('admin.human_resource.erf.approve') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': erf_id,

                        }
                    }).done(function (data) {

                        if (data.status == 1) {
                            table.draw();
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        } else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                    });
                }
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function () {

                var erf_id = table.row($(this).parents('tr')).data().erf_id;

                if ($(this).hasClass('view_document')) {
                    $.ajax({
                        url: '{!! route('admin.human_resource.erf.documents') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': erf_id,

                        }
                    }).done(function (data) {

                        if (data.status == 1) {

                            var url = '{{ Storage::url('employee_requisition/') }}';
                            var html = '';
                            html += '<table class="table table-sm datatable text-center">';
                            html += '<thead><tr><th>S No.</th><th><strong>Admin</strong></th><th><strong>Document</strong></th></tr></thead>';
                            html += '<tbody>';
                            $.each(data.documents, function (index, value) {

                                var ind = index + 1;
                                html += '<tr class=""><td>' + ind + '</td>';
                                html += '<td>' + value.admin + '</td>';
                                html += '<td><a class="white" href=" ' + url + value.id + '/' + value.file + '" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>';

                            });
                            html += '</tbody></table>';

                            html += '<div class="form-group mt-4 text-center"><input type="hidden" name="id" class="er_id" value="' + erf_id + '" ><button type="button" class="btn btn-primary  document_view">View Request</button></div>';

                            $('#documents_modal .modal-body').html(html);
                            $('#documents_modal').modal('show');
                        } else {

                            var html = '';
                            html += '<div class="form-group  text-center"><input type="hidden" name="id" class="er_id" value="' + erf_id + '" ><button type="button" class="document_view btn btn-primary">View Request</button></div>';

                            $('#documents_modal .modal-body').html(html);
                            $('#documents_modal').modal('show');
                        }

                    });
                }
            });

            $('body').on('click','.document_view',function () {
                var id = $('.er_id').val();
                if (id) {
                    $.ajax({
                        url: '{!! route('admin.human_resource.erf.print') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id,
                        }
                    })
                        .done(function (data) {
                            var tab = window.open('', '_blank');

                            if (!tab) {
                                swal({
                                    title: 'Popup Blocker Enabled!',
                                    text: 'Please add this site to your exception list.',
                                    icon: 'error',
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });
                            } else {
                                tab.document.write(data);
                                tab.document.close();
                                tab.focus();
                            }
                        });
                }

            });

        });

        $("#file_upload").validate({

            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {

                swal({
                    title: 'Please Wait!',
                    text: 'File is being uploaded!',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                });

                form.submit();
            }
        });

        $("#reject_reason_form").validate({

            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {

                swal({
                    title: 'Please Wait!',
                    text: 'Reason is being updated!',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                });

                form.submit();
            }
        });



    </script>
@endsection