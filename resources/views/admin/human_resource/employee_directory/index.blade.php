@extends('admin.layout.master')

@section('title', 'Employee Directory')

@section('content')
    <h1>Employee Directory</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Trax ID</th>
                                    <th class="border-primary border-darken-1">Employee Name</th>
                                    <th class="border-primary border-darken-1">Gender</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">CNIC</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">Type</th>
                                    <th class="border-primary border-darken-1">Request Status</th>
                                    <th class="border-primary border-darken-1">Employee Status</th>
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
    <div class="modal fade text-left" id="approveRiderModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="approveRiderModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Approve Rider</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
{{--                <form action="{{route('admin.human_resource.employee_directory.approve')}}" method="post" class="mt-1"--}}
{{--                      id="approveRiderForm" novalidate="novalidate">--}}
{{--                    {{csrf_field()}}--}}
{{--                    <div class="modal-footer">--}}
{{--                        <button type="submit" class="btn btn-warning btn-min-width mr-1 mb-1" id="confirmAction">Add--}}
{{--                            Rider--}}
{{--                        </button>--}}
{{--                        <button type="button" class="btn btn-primary btn-min-width mr-1 mb-1" data-dismiss="modal">--}}
{{--                            Cancel--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </form>--}}
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
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.employee_directory.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Trax ID');
                            head.push('Employee Name');
                            head.push('Gender');
                            head.push('City');
                            head.push('CNIC');
                            head.push('Phone No.');
                            head.push('Type');
                            head.push('Request Status');
                            head.push('Employee Status');
                            head.push('Requested At');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.employee_name);
                                row.push(values.gender);
                                row.push(values.city);
                                row.push(values.cnic);
                                row.push(values.phone_number);
                                row.push(values.employee_type);
                                row.push(values.request_status);
                                row.push(values.status);
                                row.push(values.requested_at);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if (session('role_id') == 1 || session('role_id') == 6 || in_array(469, session('permissions')))
                    {
                        text: 'Approve',
                        className: 'btn btn-primary bulk_approve',
                        enabled: false,
                        action: function (e, dt, node, config) {swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes Approve Employee!',
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
                            if (confirm) {
                                swal({
                                    title: 'Please Wait!',
                                    text: 'Employee is being Approved',
                                    icon: 'info',
                                    buttons: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });

                                $.ajax({
                                    url: '{!! route('admin.human_resource.employee_directory.approve') !!}',
                                    method: 'POST',
                                    data: {
                                        'employee_ids[]': selected_rows,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                })
                                    .done(function (data) {
                                        if (data.status == 0) {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        } else {
                                            toastr.error(data.error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }
                                        swal.close();
                                        selected_rows = [];

                                        table.rows().deselect();
                                        table.draw('false');
                                    });
                            }
                        });
                        }
                    },
                    {
                        text: 'Reject',
                        className: 'btn btn-danger bulk_reject',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            swal({
                                title: 'Are You Sure?',
                                text: 'Select Yes Reject Employee!',
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
                                if (confirm) {
                                    swal({
                                        title: 'Please Wait!',
                                        text: 'Employee is being Rejected',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });

                                    $.ajax({
                                        url: '{!! route('admin.human_resource.employee_directory.reject') !!}',
                                        method: 'POST',
                                        data: {
                                            'employee_ids[]': selected_rows,
                                            '_token': '{{ csrf_token() }}'
                                        }
                                    })
                                        .done(function (data) {
                                            if (data.status == 0) {
                                                toastr.success(data.success, 'Success!', {
                                                    positionClass: 'toast-bottom-center',
                                                    containerId: 'toast-bottom-center'
                                                });
                                            } else {
                                                toastr.error(data.error, 'Error!', {
                                                    positionClass: 'toast-top-center',
                                                    containerId: 'toast-top-center'
                                                });
                                            }
                                            swal.close();
                                            selected_rows = [];

                                            table.rows().deselect();
                                            table.draw('false');
                                        });
                                }
                            });
                        }
                    },
                        @endif
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.bulk_approve').enable();
                                    table.button('.bulk_reject').enable();
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

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.bulk_approve').disable();
                                        table.button('.bulk_reject').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Employee Directory',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
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
                ajax: '{{ route('admin.human_resource.employee_directory.list') }}',
                order: [[11, 'desc']],
                rowId: 'employee_id',
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return''; }
                    },
                    {data: 'trax_id', name: 'employees.trax_id', class: 'align-middle trax_id'},
                    {data: 'employee_name', name: 'employees.name', class: 'align-middle employee_name'},
                    {data: 'gender', name: 'eg.name', class: 'align-middle gender'},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'cnic', name: 'employees.cnic', class: 'align-middle cnic'},
                    {data: 'phone_number', name: 'employees.phone_number', class: 'align-middle phone_number'},
                    {data: 'employee_type', name: 'et.name', class: 'align-middle employee_type'},
                    {data: 'request_status', name: 'ers.name', class: 'align-middle request_status'},
                    {data: 'status', name: 'es.name', class: 'align-middle status'},
                    {data: 'requested_at', name: 'employees.created_at', class: 'align-middle requested_at'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    $('td:eq(0)', row).addClass('select-checkbox');
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select')) {
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
                    table.button('.bulk_approve').enable();
                    table.button('.bulk_reject').enable();
                }
                else {
                    table.button('.bulk_approve').disable();
                    table.button('.bulk_reject').disable();
                }
            });

            $('body').on('click', '.approve', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes Approve Employee!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Employee is being Approved',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.approve') !!}',
                            method: 'POST',
                            data: {
                                'employee_ids[]': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
                // var id = $(this).data('target-id');
                // $('#employee_id').val(id);
                // $('#approveRiderModal').modal('show');
            });

            $('body').on('click', '.reject', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes Reject Employee!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Employee is being Rejected',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.reject') !!}',
                            method: 'POST',
                            data: {
                                'employee_ids[]': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $("#approveRiderForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    $('#riderInfoDiv input,#riderInfoDiv textarea,#riderInfoDiv select').removeAttr('disabled');
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'Rider is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
        });
    </script>

@endsection