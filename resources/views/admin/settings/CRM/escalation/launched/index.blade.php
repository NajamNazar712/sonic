@extends('admin.layout.master')

@section('title', 'Crm Escalation Launched Setting')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Crm Escalation Launched Setting
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Case Nature</th>
                                    <th class="border-primary border-darken-1">Case Nature Type</th>
                                    <th class="border-primary border-darken-1">Status(es)</th>
                                    <th class="border-primary border-darken-1">TAT</th>
                                    <th class="border-primary border-darken-1">Mark as</th>
                                    <th class="border-primary border-darken-1">Comment</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">Updated by</th>
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

    <div class="modal fade" id="statuses_modal" data-backdrop="static" role="dialog" aria-labelledby="statuses_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="statuses_modal_title">Status(es)</h4>

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
@endsection



@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    text: '<i class="la la-cogs"></i> Add',
                    className: 'btn btn-primary add',
                    action: function (e, dt, node, config) {
                        var url = '{!! route('admin.settings.escalation.launched.add.index') !!}';
                        window.location = url;
                    }
                },'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.settings.escalation.launched.list') }}',
                rowId: 'id',
                order: [[8, 'asc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'case_nature', name: 'crcn.name', class: 'align-middle case_nature'},
                    {data: 'case_nature_type', name: 'crcnt.type', class: 'align-middle case_nature_type'},
                    {data: 'view_statuses', class: 'align-middle text-center view_statuses', orderable: false, searchable: false},
                    {data: 'tat', name: 'crm_escalations.tat', class: 'align-middle added_at'},
                    {data: 'mark_as', name: 'crm_escalations.mark_as', class: 'align-middle mark_as'},
                    {data: 'comment', name: 'crm_escalations.comment', class: 'align-middle comment'},
                    {data: 'status', name: 'crm_escalations.status', class: 'align-middle status'},
                    {data: 'updated_at', name: 'crm_escalations.updated_at', class: 'align-middle updated_at'},
                    {data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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
                    var mark_as_select = '<select name="mark_as_select" id="mark_as_select" class="select2 form-control">' +
                        '<option value="0">Invalid</option>' +
                        '<option value="1">Valid</option>' +
                        '</select>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Disable</option>' +
                        '<option value="1">Enable</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.view_statuses')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.mark_as')){
                            $(mark_as_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
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
                    $("#mark_as_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Mark as",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('body').on('click','#datatable tbody tr td.view_statuses button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#statuses_modal .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.settings.escalation.view_statuses') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var statuses = '';
                            if (data.statuses) {
                                $.each(data.statuses, function(index, status) {
                                    statuses += status + '<br>';
                                });
                            }
                            $('#statuses_modal .modal-body').html(statuses);
                            $('#statuses_modal').modal('show');
                        }
                    });

            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                if ($(this).hasClass('edit')) {
                    var redirect = '{!! route('admin.settings.escalation.launched.edit.index', ':id') !!}';
                    var url = redirect.replace(':id', id);
                    window.location = url;
                }
                if ($(this).hasClass('enable')) {
                    $.ajax({
                        url: '{!! route('admin.settings.escalation.status') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            'status': 1,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function(data) {
                            if (data.status == 1) {
                                table.draw(false);

                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                }
                else if ($(this).hasClass('disable')) {
                    $.ajax({
                        url: '{!! route('admin.settings.escalation.status') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            'status': 0,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function(data) {
                            if (data.status == 1) {
                                table.draw(false);

                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                }

            });
        });
    </script>
@endsection