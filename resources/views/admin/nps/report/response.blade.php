@extends('admin.layout.master')

@section('title', 'NPS Response Report')

@section('content')
    <h1>NPS Response Report</h1>

    <section>
        <div class="row">

            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="row justify-content-center">
                                <div id="search_form" class="row p-1 mb-2" style="width: 100%">
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <select name="search_admins[]" id="search_shipper" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                                @foreach($shippers as $val)
                                                    <option value="{{$val->id}}">{{$val->name}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="search_survey" id="search_survey" class="form-control select2"  required data-rule-required="true" data-msg-required="This field is required">
                                                <option value="">Select</option>
                                                @foreach($nps_survey as $val)
                                                    <option value="{{$val->id}}">{{$val->survey_name}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                            </div>
                                            <input type="text" name="requested_from_date"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   id="requested_from_date" placeholder="Requested Date From">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                            </div>
                                            <input type="text" name="requested_to_date"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   id="requested_to_date" placeholder="Requested Date To">
                                        </div>
                                    </div>


                                    <div class="col-7">
                                        <button type="button" id="search_filter_btn" class="float-right mb-1 mt-2 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width: 100%">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Shipper Name</th>
                                    <th class="border-primary border-darken-1">Survey Name</th>
                                    <th class="border-primary border-darken-1">Promoters</th>
                                    <th class="border-primary border-darken-1">Passive</th>
                                    <th class="border-primary border-darken-1">Detractor</th>
                                    <th class="border-primary border-darken-1">Response Date</th>
                                    <th class="border-primary border-darken-1">Requested By</th>
                                    <th class="border-primary border-darken-1">Suggestions</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">


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
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    todo for datepicker--}}
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    {{--    todo for datepicker end--}}

    {{--    todo date filter field--}}
    <script>
        var booking_from_date = $('#requested_from_date').pickadate({
            firstDay: 1,
            clear: '',
            max: '{{ Carbon\Carbon::now() }}',
            // format: 'dd mmmm, yyyy',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            onSet: function (context) {
                // if (context.select) {
                //     $('#requested_to_date').pickadate('picker').set('min', $('#requested_from_date').pickadate('picker').get('select'));
                // }
            }
        });
        var booking_to_date = $('#requested_to_date').pickadate({
            firstDay: 1,
            clear: '',
            max: '{{ Carbon\Carbon::now()->addYear(1) }}',
            // format: 'dd mmmm, yyyy',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 23:59:59',
            hiddenSuffix: '_formatted',
            onSet: function (context) {
                // if (context.select) {
                //     $('#requested_from_date').pickadate('picker').set('max', $('#requested_to_date').pickadate('picker').get('select'));
                // }
            }
        });

    </script>
    <script>
        $(document).ready(function() {
            $('#search_shipper').select2({
                width:'100%',
                placeholder:"Select Shippers",
                allowClear:true,
                dropdownParent:$('#search_form')
            });

            $('#search_survey').select2({
                width:'100%',
                placeholder:"Select Survey",
                allowClear:true,
                dropdownParent:$('#search_form')
            });
            // $('#ratting_status').select2({
            //     width:'100%',
            //     placeholder:"Ratting Type",
            //     allowClear:true,
            //     dropdownParent:$('#search_form')
            // });




            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons:[],
                "autoWidth": true,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: 0,
                ajax: {
                    url: '{{ route('admin.nps.response_report_list') }}',
                    type: "get",
                    data: function (d) {
                        d['_token'] = "{{csrf_token()}}";
                        d.search_survey = $('#search_survey').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.requested_from_date = $('#requested_from_date').val();
                        d.requested_to_date = $('#requested_to_date').val();

                    }
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

                    {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name'},
                    {data: 'survey_name', name: 'ns.survey_name', class: 'align-middle survey_name'},
                    {data: 'promoters', name: 'promoters', class: 'align-middle promoters'},
                    {data: 'passive', name: 'passive', class: 'align-middle passive'},
                    {data: 'detractor', name: 'detractor', class: 'align-middle detractor'},
                    {data: 'response_date', name: 'nps_shipper_rattings.created_at', class: 'align-middle response_date'},
                    {data: 'requested_by', name: 'a.name', class: 'align-middle requested_by'},
                    {data: 'recommendations_box', name: 'nps_shipper_rattings.recommendations_box', class: 'align-middle recommendations_box'},


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


                        if ($(header).is('.action') || $(header).is('.serial_number')) {
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

            $('#search_filter_btn').on('click',function () {

                table.draw();
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