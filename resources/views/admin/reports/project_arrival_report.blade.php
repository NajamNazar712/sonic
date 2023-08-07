@extends('admin.layout.master')

@section('title', 'Project Arrival Report')

@section('content')
    <h1 class="mb-1">
        Project Arrival Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div  class="row mb-2 justify-content-center" id="track_form">
                    {{-- <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-5">
                        <fieldset class="form-group">
                            <select name="search_rider" id="search_rider" class="form-control select2">
                                @foreach($riders as $rider)
                                    <option value="{{$rider->id}}">{{$rider->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div> --}}
                    <div class="col-5">
                        <div class="form-group">
                            <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*"  id="tracking_numbers" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                        </div>
                    </div>
                    <div class="col-5">
                        <fieldset class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                @foreach($shippers as $shipper)
                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <div id="datatable_wrapper">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking Numbers</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Project Arrival By</th>
                            <th class="border-primary border-darken-1">Project Arrival At</th>
                            <th class="border-primary border-darken-1">Normal Arrival By</th>
                            <th class="border-primary border-darken-1">Normal Arrival At</th>
                            {{-- <th class="border-primary border-darken-1">No. of Scanned Shipments</th>
                            <th class="border-primary border-darken-1">No. of Arrived Shipments</th>
                            <th class="border-primary border-darken-1">Arrival Without Scan Shipments</th> --}}
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title"></h4>

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
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
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
      
        .tracking_numbers{
            width: 100% !important;
        }

    </style>
@endsection
@section('js')
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
{{-- <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script> --}}
<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // $('#datatable_wrapper').hide();



            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });

            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Rider',
                width:'100%',
                allowClear:true
            });

            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Shipper',
                width:'100%',
                allowClear:true
            });

            var max = '{{ Carbon\Carbon::now() }}';

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #to_date').pickadate('picker').set('min', $('#track_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #from_date').pickadate('picker').set('min', $('#track_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });
            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function (dropdown) {
                    dropdown.remove();
                },
                onType: function (str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function (input) {
                    if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.project_arrival.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking Numbers');
                            head.push('Shipper');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Project Arrival By');
                            head.push('Project Arrival At');
                            head.push('Normal Arrival By');
                            head.push('Normal Arrival At');
                             $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.project_arrival_by);
                                row.push(values.project_arrival_at);
                                row.push(values.normal_arrival_by);
                                row.push(values.normal_arrival_at);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Project Arrival Report',
                        className: 'btn btn-primary excel',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                        footer: true
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                autoWidth:false,
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.reports.project_arrival.list') }}',
                    data: function (d) {
                        // d.search_hub = $('#search_hub').val();
                        // d.search_rider = $('#search_rider').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                        d.tracking_numbers = $('#tracking_numbers').val();
                    }
                },
                order: [[6, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number', class: 'align-middle text-center shipper'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle text-center shipper'},
                    { data:'origin',name: 'uo.name', class: 'align-middle text-center origin'},
                    { data:'destination',name: 'des.name', class: 'align-middle text-center destination'},
                    { data:'project_arrival_by',name: 'apa.name', class: 'align-middle text-center project_arrival_by'},
                    { data:'project_arrival_at',name: 'sjpa.created_at', class: 'align-middle text-center project_arrival_at'},
                    { data:'normal_arrival_by',name: 'aa.name', class: 'align-middle text-center project_arrival_at'},
                    { data:'normal_arrival_at',name: 'sja.created_at', class: 'align-middle text-center project_arrival_at'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                },
            });

            $('#search_filter_btn').on('click',function () {
                $('#datatable_wrapper').show();
                table.draw();
            });

            $('#datatable tbody').on('click', 'tr td.pickup_note button.print', function() {

                var pickup_note_id = parseInt($(this).attr('rel'));
                print(pickup_note_id);
            });

           
        });
    </script>
@endsection