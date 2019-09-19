@extends('admin.layout.master')

@section('title', 'CRM Case Nature Types')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    CRM Case Nature Types
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
                                    <th class="border-primary border-darken-1">Type</th>
                                    <th class="border-primary border-darken-1">Nature</th>
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
    <div class="modal fade text-left" id="AddCaseNatureTypes" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddCaseNatureTypes"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Case Nature Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="case_nature_type_form" class="form-horizontal text-center" novalidate="novalidate">
                        {{ csrf_field() }}
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <select name="case_nature_select" id="case_nature_select" class="form-control select2">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <input type="text" name="new_case_nature_type" class="form-control new_case_nature_type" id="new_case_nature_type" placeholder="Case Nature Type*">
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary width-100" id="add_type_button">Add</button>
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

    <script type="text/javascript">
        $(document).ready(function () {
            $('#case_nature_type_form').keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                $("#add_type_button").trigger('click');
            }
        });
            $('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Case Nature",
                allowClear:true,
                dropdownParent:$('#case_nature_type_form')
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.crm_case_nature_types.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Type');
                            head.push('Nature');
                            // head.push('Created At');
                            // head.push('Created By');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.case_nature_type);
                                row.push(values.case_nature);
                                // row.push(values.created_at);
                                // row.push(values.created_by);


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
                        text: '<i class="la la-plus"></i> Add Type',
                        className: 'btn btn-primary add_type',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            // $('#AddHoliday .modal-body').html(html);
                            $('#AddCaseNatureTypes').modal('show');
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'Case Nature Types',
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
                    url: '{{ route('admin.settings.crm_case_nature_types.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                },
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'case_nature_type', name: 'crm_request_case_nature_types.type', class: 'align-middle case_nature_type'},
                    {data: 'case_nature', name: 'crcs.name', class: 'align-middle case_nature'},
                    // {data: 'created_at', name: 'crm_tat_holidays.created_at', class: 'align-middle created_at'},
                    // {data: 'created_by', name: 'a.name', class: 'align-middle created_by'},

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

            $('#add_type_button').on('click', function () {
                var case_nature = $('#case_nature_select').val();
                var case_nature_type = $('#new_case_nature_type').val();
                var flag = true;
                if(!case_nature){
                    flag = false;
                    var error = "Please select Case Nature!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(!case_nature_type){
                    flag = false;
                    var error = "Please enter Case Nature Type!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(flag){
                    $('#add_type_button').attr('disabled',true);
                    $.ajax({
                        url: '{!! route('admin.settings.crm_case_nature_types.store') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'case_nature': case_nature,
                            'case_nature_type' : case_nature_type
                        }
                    })
                        .done(function(data) {
                            if (data.status == 1) {
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                $('#AddCaseNatureTypes').modal('hide');
                                table.draw();
                            }
                            else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            $('#add_type_button').attr('disabled',false);
                        });
                }
            });

            $('#AddCaseNatureTypes').on('hide.bs.modal', function (e) {
                $('#case_nature_select').val('').trigger('change');
                $('#new_case_nature_type').val('');
            });
        });

    </script>
@endsection