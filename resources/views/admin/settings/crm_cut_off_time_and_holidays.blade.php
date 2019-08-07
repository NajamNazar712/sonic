@extends('admin.layout.master')

@section('title', 'CRM TAT Cut-Off Time & Holidays')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    CRM TAT Cut-Off Time & Holidays
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.crm_cut_off_time_and_holidays.update') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <div class="row justify-content-center">
                                    <div class="col-4">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                  <span class="">From*</span>
                                              </span>
                                            </div>
                                            <input type="text" name="cut_off_time_from" class="form-control bg-primary border-primary white rounded-right pickatime cut_off_time_from" value="{{$cut_off_time_from}}" id="cut_off_time_from" placeholder="Cut-Off Time From*" data-rule-required="true" data-msg-required="Cut-Off Time From is required">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="">To*</span>
                                    </span>
                                            </div>
                                            <input type="text" name="cut_off_time_to" class="form-control bg-primary border-primary white rounded-right pickatime cut_off_time_to" id="cut_off_time_to" value="{{$cut_off_time_to}}" placeholder="Cut-Off Time To*" data-rule-required="true" data-msg-required="Cut-Off Time To is required">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">

                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Date</th>
                                    <th class="border-primary border-darken-1">Reason</th>
                                    <th class="border-primary border-darken-1">Created At</th>
                                    <th class="border-primary border-darken-1">Created By</th>
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
    <div class="modal fade text-left" id="AddHoliday" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddHoliday"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Holiday</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="holiday_form" class="form-horizontal text-center" novalidate="novalidate">
                        {{ csrf_field() }}

                        <div class="row justify-content-center">
                            <div class="col-5">
                                <div class="form-group input-group ml-1">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                    </div>
                                    <input type="text" name="holiday_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="holiday_date" placeholder="Holiday Date*">
                                </div>
                            </div>
                            <div class="col-3 form-group">
                                <input type="text" class="form-control" name="reason" id="reason" placeholder="Reason*" data-rule-required="true" data-msg-required="Air Waybill Print Count is required">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary width-100" id="add_holiday_button">Add</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.cut_off_time_from').pickatime({
                clear: '',
                format: 'h:i A',
            });
            $('.cut_off_time_to').pickatime({
                clear: '',
                format: 'h:i A',
            });
            $('#holiday_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted'
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.crm_cut_off_time_and_holidays.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Date');
                            head.push('Reason');
                            head.push('Created At');
                            head.push('Created By');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.holiday);
                                row.push(values.reason);
                                row.push(values.created_at);
                                row.push(values.created_by);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );            var index_column = 0;
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '350px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        text: '<i class="la la-plus"></i> Add Holiday',
                        className: 'btn btn-primary add_holiday',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            // $('#AddHoliday .modal-body').html(html);
                            $('#AddHoliday').modal('show');
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'CRM TAT Cut-Off Time & Holidays',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                language: {
                    processing: data_table_loader
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.settings.crm_cut_off_time_and_holidays.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                },
                order: [[3, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'holiday', name: 'crm_tat_holidays.holiday', class: 'align-middle holiday'},
                    {data: 'reason', name: 'crm_tat_holidays.reason', class: 'align-middle reason'},
                    {data: 'created_at', name: 'crm_tat_holidays.created_at', class: 'align-middle created_at'},
                    {data: 'created_by', name: 'a.name', class: 'align-middle created_by'},

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


                        if ($(header).is('.serial_number')) {
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

            $('#add_holiday_button').on('click', function () {
                var holiday_date = $('input[name="holiday_date_formatted"]').val();
                var holiday_reason = $('#reason').val();
                var flag = true;
                if(!holiday_date){
                    flag = false;
                    var error = "Please select Holiday Date!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(!holiday_reason){
                    flag = false;
                    var error = "Please select Reason!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(flag){
                    $('#add_holiday_button').attr('disabled',true);
                    $.ajax({
                        url: '{!! route('admin.settings.crm_cut_off_time_and_holidays.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'holiday_date': holiday_date,
                            'holiday_reason' : holiday_reason
                        }
                    })
                        .done(function(data) {
                            if (data.status == 1) {
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                $('#AddHoliday').modal('hide');
                                table.draw();
                            }
                            else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            $('#add_holiday_button').attr('disabled',false);
                        });
                }
            });

            $('#AddHoliday').on('hide.bs.modal', function (e) {
                $('#holiday_date').val('');
                $('input[name="holiday_date_formatted"]').val('');
                $('#reason').val('');
            });


            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });

        });

    </script>
@endsection