@extends('admin.layout.master')

@section('title', 'Runners On Route')

@section('content')
    <h1 class="mb-1">
        Runners On Route
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Runner</th>
                        <th class="border-primary border-darken-1">Driver Name</th>
                        <th class="border-primary border-darken-1">Vehicle No.</th>
                        <th class="border-primary border-darken-1">Contact No.</th>
                        <th class="border-primary border-darken-1">Created At</th>
                        <th class="border-primary border-darken-1">Created By</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ViewDetailsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewDetailsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">View Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>

            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="SelectRunnerModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SelectRunnerModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Select Runner</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="runner_select_form" class="form-horizontal" action="{{ route('admin.runner.add') }}" method="GET" novalidate="novalidate">
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-7 form-group">
                                <select class="form-control runner" name="runner" id="runner" data-rule-required="true" data-msg-required="Runner is required">
                                    @foreach($runners as $runner)
                                        <option value="{{$runner->id}}">{{$runner->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="AddShipperBtn" type="submit" class="btn btn-info">Select</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <style type="text/css">
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
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $("#runner").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Runner",
                width:'100%',
                dropdownParent:$('#SelectRunnerModal')
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.runner.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Runner');
                            head.push('Driver Name');
                            head.push('Vehicle No.');
                            head.push('Contact No.');
                            head.push('Created At');
                            head.push('Created By');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.runner);
                                row.push(values.driver_name);
                                row.push(values.vehicle_no);
                                row.push(values.contact_no);
                                row.push(values.created_at);
                                row.push(values.created_by);
                                row.push(values.status);

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
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add Runner',
                        className: 'btn btn-primary add_runner',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#SelectRunnerModal').modal('show');
                        }
                    },{
                        extend: 'excel',
                        title: 'Runner On Route Report',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        className: 'btn btn-primary',
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
                autoWidth: false,
                ajax: '{{ route('admin.runner.list') }}',
                rowId: 'id',
                order: [[5, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle text-center serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'runner', name: 'r.name', class: 'align-middle text-center runner'},
                    {data: 'driver_name', name: 'runner_details.driver_name', class: 'align-middle text-center driver_name'},
                    {data: 'vehicle_no', name: 'runner_details.vehicle_no', class: 'align-middle text-center vehicle_no'},
                    {data: 'contact_no', name: 'runner_details.contact_no', class: 'align-middle text-center contact_no'},
                    {data: 'created_at', name: 'runner_details.created_at', class: 'align-middle text-center created_at'},
                    {data: 'created_by', name: 'a.name', class: 'align-middle text-center created_by'},
                    {data: 'status', name: 'runner_details.status', class: 'align-middle text-center status'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}

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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="1">Completed</option>' +
                        '<option value="0">Update</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.status')){
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            $('body').on('click','button.update',function () {
                var id = $(this).parents('tr').attr('id');
                var link = '{{ route('admin.runner.edit', ["id" => 0]) }}';

                window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

                var id = table.row( $(this).parents('tr') ).data().id;

                if ($(this).hasClass('details')) {
                    $.ajax({
                        url: '{!! route('admin.runner.view_details') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        console.log(id);
                        if (data.runner_detail_times.length != 0) {
                            var html = '';
                            html += '<table class="table table-sm datatable text-center">';
                            html += '<thead><tr><th>S No.</th><th>Origin</th><th>Destination</th><th>Departure Date</th><th>Departure Time</th><th>Arrival Date</th><th>Arrival Time</th><th>Stay Time</th><th>Comment</th></tr></thead>';
                            html += '<tbody>';
                            $.each(data.runner_detail_times, function (index, value) {
                                var ind = index + 1;
                                html += '<tr class=""><td>' + ind + '</td>';
                                html += '<td>' + value.origin + '</td>';
                                html += '<td>' + value.destination + '</td>';
                                html += '<td>' + value.departure_date + '</td>';
                                html += '<td>' + value.departure_time + '</td>';
                                html += '<td>' + value.arrival_date + '</td>';
                                html += '<td>' + value.arrival_time + '</td>';
                                html += '<td>' + value.stay_time + '</td>';
                                if(value.comment !== null){
                                    html += '<td>' + value.comment + '</td>';
                                }
                                else{
                                    html += '<td>-</td>';
                                }
                            });
                            html += '</tbody></table>';

                            $('#ViewDetailsModal .modal-body').html(html);
                            $('#ViewDetailsModal').modal('show');

                        }
                    })
                }
            });


            $('#runner_select_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Runner is being selected!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $('#SelectRunnerModal').on('hide.bs.modal', function (e) {
                $('#runner').val('').change();
            });
        });

    </script>
@endsection