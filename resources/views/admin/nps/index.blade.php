@extends('admin.layout.master')

@section('title', 'NPS SURVEY')

@section('content')
    <h1>NPS SURVEY</h1>

    <section>
        <div class="row">

            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="row justify-content-center">
                                <div class="col-3">
{{--                                    <select name="search_status" id="search_status" class="form-control select2">--}}
{{--                                        @foreach($erf_status as $status)--}}
{{--                                            <option value="{{$status->id}}">{{$status->name}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width: 100%">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Survey ID</th>
                                    <th class="border-primary border-darken-1">Survey Name</th>
                                    <th class="border-primary border-darken-1">Start Time</th>
                                    <th class="border-primary border-darken-1">End Time</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Assign Shippers</th>
                                    <th class="border-primary border-darken-1">Question</th>
                                    <th class="border-primary border-darken-1">Recommendation Box</th>
                                    <th class="border-primary border-darken-1">Admin</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div style="display: none;">
                        <form id="account_active_form" action="{{route('admin.nps.status')}}" method="post" class="mt-2">
                            {{csrf_field()}}
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="shid" id="shid">
                            <input type="hidden" name="status" id="shstatus">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="shippers_modal" data-backdrop="static" role="dialog" aria-labelledby="shippers_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bookings_modal_title">NPS Shippers</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <table class="table table-bordered" id="shipper_data_table">
                        <thead>
                            <tr>
                               <th>S.no</th>
                                <th>Shipper Name</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="question_modal" data-backdrop="static" role="dialog" aria-labelledby="question_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bookings_modal_title">NPS Question</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <table class="table table-bordered" id="question_data_table">
                        <thead>
                        <tr>
                            <th>S.no</th>
                            <th>Question</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
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
            var route = '<?php echo route('admin.nps.add'); ?>';
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    @if(session('role_id') == 1 ||  in_array(752, session('permissions')))
                    {
                        title: 'Add Survey',
                        className: 'btn btn-primary',
                        text: '<i class="la la-plus"></i> Add Survey',
                        action: function (e) {
                            window.location = route;

                        }
                    },
                    @endif

                    'reset'
                ],
                scrollX: true, scrollY: '500px',
                "autoWidth": true,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.nps.list') }}',
                },
                rowId: 'survey_id',
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

                    {data: 'survey_id', name: 'nps_survey.id', class: 'align-middle survey_id'},
                    {data: 'survey_name', name: 'nps_survey.survey_name', class: 'align-middle survey_name'},
                    {data: 'start_time', name: 'nps_survey.start_time', class: 'align-middle start_time'},
                    {data: 'end_time', name: 'nps_survey.end_time', class: 'align-middle end_time'},
                    {data: 'status', name: 'nps_survey.status', class: 'align-middle status'},
                    {data: 'shippers', name: 'shippers', class: 'align-middle shippers' , orderable: false, sortable: false},
                    {data: 'questions', name: 'questions', class: 'align-middle questions' , orderable: false, sortable: false},
                    {data: 'recommendation_box', name: 'nps_survey.recommendation_box', class: 'align-middle recommendation_box'},
                    {data: 'admin_name', name: 'a.name', class: 'align-middle admin'},
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

                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="1">Active</option>' +
                        '<option value="0">Deactivate</option>' +
                        '</select>';

                    var drop_select_recommendation = '<select name="recommendation_select" id="recommendation_select" class="select2 form-control">' +
                        '<option value="1">Yes</option>' +
                        '<option value="0">No</option>' +
                        '</select>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.aging')  || $(header).is('.shippers') || $(header).is('.questions')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.recommendation_box')){
                            $(drop_select_recommendation).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select a Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    $("#recommendation_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Recommendation Box",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

            $('body').on('click', 'button.shippers_modal',  function(){
                var id = $(this).parents('tr').attr('id');
                if(id){
                    $.ajax({
                        url: '{!! route('admin.nps.view_shippers') !!}',
                        data: {
                            'survey_id': id,
                        }
                    })
                    .done(function(data) {
                        if(data.status == 1){
                            $('#shippers_modal').modal('show');
                            var html = "";
                                $.each(data.shippers, function(index, values) {
                                     html+= `

                                            <tr>
                                             <td>${index+1}</td>
                                             <td>${values.name}</td>
                                            </tr>

                                `;
                            });
                            $('#shipper_data_table tbody').html(html);
                        }

                    });
                }
            });

            $('body').on('click', 'button.question_modal',  function(){
                var id = $(this).parents('tr').attr('id');
                if(id){
                    $.ajax({
                        url: '{!! route('admin.nps.view_questions') !!}',
                        data: {
                            'survey_id': id,
                        }
                    })
                        .done(function(data) {
                            if(data.status == 1){
                                $('#question_modal').modal('show');
                                var html = "";
                                $.each(data.question, function(index, values) {
                                    html+= `

                                            <tr>
                                             <td>${index+1}</td>
                                             <td>${values.question}</td>
                                            </tr>

                                `;
                                });
                                $('#question_data_table tbody').html(html);
                            }

                        });
                }
            });

            $('body').on('click','button.active_survey',function () {
                var id = $(this).parents('tr').attr('id');
                var status = $(this).attr('rel');
                var title = (status == 'activate') ? 'Are you sure to activate survey ?' : 'Are you sure ?';
                var text = (status == 'activate') ? 'Only one survey can be active at a time' : 'Select Yes to Deactivate Survey';
                swal({
                    title: title,
                    text: text,
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
                        $('#account_active_form #shid').val(id);
                        $('#account_active_form #shstatus').val(status);
                        $('#account_active_form').submit();
                    }
                });
            });

        });


    </script>
@endsection