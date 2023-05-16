@extends('admin.layout.master')
@section('title','Rider Remarks')

@section('content')
    <h1 class="mb-1">
        Rider Remarks    
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')


                <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="col-3 mt-1">
                        <div class="form-group input-group ">
                            <div class="input-group-prepend">
                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                            <span class="la la-calendar-o"></span>
                        </span>
                            </div>
                            <input type="text" name="search_date_from"
                                   class="form-control pickadate bg-primary border-primary white rounded-right"
                                   id="search_date_from" placeholder="Select From Date">
                        </div>
                    </div>
                    <div class="col-3 mt-1">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                            <span class="la la-calendar-o"></span>
                        </span>
                            </div>
                            <input type="text" name="search_date_to"
                                   class="form-control pickadate bg-primary border-primary white rounded-right"
                                   id="search_date_to" placeholder="Select To Date">
                        </div>
                    </div>

                    <div class="col-3 mt-1">

                        <select name="search_city" id="search_city" class="form-control select2 col-4">
                            @foreach($cities as $city)
                                <option value="{{$city->id}}">{{$city->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    
                    <div class="col-4 mt-1">
                        <div class="form-group">
                            <button type="button" id="search_filter_btn"
                                    class="btn btn-block btn-outline-info btn-min-width"><i class="la la-search"></i>
                                Search
                            </button>
                        </div>
                    </div>

                </form>
                    
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Rider Remarks ID</th>
                        <th class="border-primary border-darken-1">Rider Name</th>
                        <th class="border-primary border-darken-1">Trax ID</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">Rider Remarks</th>
                        <th class="border-primary border-darken-1">Response</th>
                        <th class="border-primary border-darken-1">Created At</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Updated At</th>
                        <th class="border-primary border-darken-1">Action</th>


                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <!--Shipments popup -->
    {{-- <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Shipment(s)</h4>

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
    </div> --}}
    <!--Shipments popup -->

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>

       <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var search_date_to = $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            var search_date_from = $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            $('#search_city').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'City',
            allowClear:true
        })

            $('#search_filter_btn').on('click',function () {
                table.draw(true);
            });



            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Rider Remarks ID');
                            head.push('Rider Name');
                            head.push('Trax ID');
                            head.push('City');
                            head.push('Hub');
                            head.push('Zone');
                            head.push('Rider Remarks');
                            head.push('Response');
                            head.push('Created At');
                            head.push('Status');
                            head.push('Updated By');
                            head.push('Updated At');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.id);
                                row.push(values.rider_name);
                                row.push(values.traxID);
                                row.push(values.city_name);
                                row.push(values.hub);
                                row.push(values.zone_name);
                                row.push(values.rider_remarks);
                                row.push(values.response);
                                row.push(values.created_at);
                                row.push(values.status);
                                row.push(values.date);
                                row.push(values.updated_at);
                                row.push(values.updated_by);
                                body.push(row);
                            });
                        },
                        url: '{{ route('admin.management.riders.rider_remarks.list') }}',
                        data: params,
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: false, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Rider Remarks',
                        text: '<i class="la la-file-excel-o"></i> Excel',
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
                ajax: {
                    url: '{{ route('admin.management.riders.rider_remarks.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.search_city = $('#search_city').val();
                    }
                },
                rowId: 'rider_remarks.id',
                order: [[9, 'desc']],
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
                    {data: 'id', name: 'rider_remarks.id', class: 'align-middle hub'},
                    {data: 'rider_name', name: 'r.name', class: 'align-middle rider_name'},
                    {data: 'traxID', name: 'r.trax_id', class: 'align-middle traxID'},
                    {data: 'city_name', name: 'city.name', class: 'align-middle city_name'},
                    {data: 'hub', name: 'c.name', class: 'align-middle hub', orderable: false},
                    {data: 'zone_name', name: 'z.name', class: 'align-middle zone_name'},
                    {data: 'rider_remarks', name: 'rider_remarks.rider_remarks', class: 'align-middle rider_remarks'},
                    {data: 'response', name: 'rider_remarks.response', class: 'align-middle response'},
                    {data: 'created_at',name: 'rider_remarks.created_at',class: 'align-middle created_at text-center',orderable: false, searchable: false},
                    {data: 'status', name: 'rider_remarks.rider_remarks_status_id', class: 'align-middle status'},
                    {data: 'updated_by', name: 'rider_remarks.updated_by', class: 'align-middle updated_by'},
                    {data: 'updated_at', name: 'rider_remarks.updated_at', class: 'align-middle updated_at'},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                drawCallback: function (settings) {
                    var api = new $.fn.dataTable.Api(settings);
                    var data = api.rows({page: 'current'}).data();
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());
                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }  else {
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

       

        });
    </script>
@endsection