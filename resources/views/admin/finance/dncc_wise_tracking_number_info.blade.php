@extends('admin.layout.master')
@section('title','DNCC wise Tracking number info')

@section('content')
    <h1 class="mb-1">
        DNCC wise Tracking number info
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="track_form" class="mb-1" novalidate="novalidate">
                    <div class="row justify-content-center">
                    <div class="col-3">
                        <div class="form-group input-group">
                            <input type="text" name="dncc_numbers" id="dncc_number" class="dt_search dncc_numbers form-control" 
                                placeholder="DNCC Number(s)" data-tags-input-name="dncc_number">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>
                                
                            <input type="text" name="completed_date" class="form-control" id="completed_date" placeholder="DNCC Date" title="DNCC Date"> <!-- data-value="{{ Carbon\Carbon::today() }}" -->
                        </div>
                    </div>
                    <div class="form-group ml-1">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
                </form>

                <table class="table table-bordered datatable" id="datatable">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">DNCC #</th>
                            <th class="border-primary border-darken-1">Shipment Arrived at</th>
                            <th class="border-primary border-darken-1">Shipment Status</th>                            
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Destination HUB</th>
                            <th class="border-primary border-darken-1">Shipment  Update Date</th>

                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
   
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

        .table > thead > tr > th {
            width: calc(1500px / 5);
            text-align: left;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
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
        
        .green-row {
            background-color: #90ee90;
        }

        .picker {
            z-index: 9999 !important;
        }

        .picker__holder {
            z-index: 9999 !important;
        }
    </style>
@endsection

@section('js')
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
    $(document).ready(function () {
        $('#completed_date').val('');
       var completed_date = $('#completed_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00', // ✔ keep as is
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#completed_date_root').css('top','40px');
                },
                onSet: function(context) {
                }
            });
      
        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
                let picker = $('#completed_date').pickadate('picker');
                let submitValue = picker.get('value', 'yyyy-mm-dd'); // e.g., "2026-04-02"
                body = [];
                var params = table.ajax.params();
                params.start = 0;
                params.length = -1;
                params.excel = true;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.finance.dncc_wise_tracking_number_info.list') }}',
                    data:function(d){
                        d.tracking_numbers = $('#dncc_number').val();
                        d.date = $('#completed_date').val();
                    },
                    data: params,
                    success: function (result) {
                        head = [];
                        head.push('S.No');
                        head.push('DNCC #');
                        head.push('Tracking .No');
                        head.push('Shipment Arrived at');
                        head.push('Shipment Status');
                        head.push('Origin');
                        head.push('Destination');
                        head.push('Destination Hub');
                        head.push('Shipment Update Date ');

                        $.each(result.data, function(index, values) {
                            row = [];
                            row.push(index + 1);
                            row.push(values.dncc_no);
                            row.push(values.tracking_number);
                            row.push(values.created_at);
                            row.push(values.shipment_status);
                            row.push(values.origin);
                            row.push(values.destination);
                            row.push(values.hub);
                            row.push(values.updated_date);

                            body.push(row);
                        });
                    },
                    async: false
                });

                return {body: body, header: head};
            }
        } );
       var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            buttons: [
                {
                    extend: 'excel',
                    title: 'DNCC Wise Tracking Number Info',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                'reset'
            ],
            scrollX: true, scrollY: '500px',
            lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            deferLoading: 0,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            ajax:{
                url: '{{ route('admin.finance.dncc_wise_tracking_number_info.list') }}',
                data: function (d) {
                    d.dncc_numbers = $('#dncc_number').val();
                    d.date = $('#completed_date').val();
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
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'tracking_number', name: 'tracking_number', class: 'align-middle', orderable: false },
                { data: 'dncc_no', name: 'dncc_no', class: 'align-middle dncc_no', orderable: false },
                { data: 'created_at', name: 'created_at', class: 'align-middle dncc_created_at', orderable: false },
                { data: 'shipment_status', name: 'shipment_status', class: 'align-middle shipment_status', orderable: false },
                { data: 'origin', name: 'origin', class: 'align-middle origin', orderable: false },
                { data: 'destination', name: 'destination', class: 'align-middle destination', orderable: false },
                { data: 'hub', name: 'hub', class: 'align-middle hub', orderable: false },
                { data: 'updated_date', name: 'updated_date', class: 'align-middle updated_date', orderable: false },
            ],
        });

        //Selectize
        var select = $('#dncc_number').selectize({
            placeholder: 'DNCC Number(s)',
            delimiter: ',',
            createOnBlur: true,
            persist: false,
            plugins: ['remove_button'],
            onDropdownOpen: function(dropdown) {
                dropdown.remove();
            },
            onType: function(str) {
                var regex = /^[0-9,]+$/;
                if (!regex.test(str)) {
                    select[0].selectize.setTextboxValue('');
                }
            },
            create: function(input) {
                if (input.length >= 1 && Math.floor(input) == input && $.isNumeric(input)) {
                    return {
                        value: input,
                        text: input
                    }
                } else {
                    return false;
                }
            },
        });

        $('#track_form').bind('submit',function (e) {
            var dncc_numbers = $('#track_form .dncc_numbers').val();
            var completed_date = $('#track_form .completed_date').val();
            if (dncc_numbers != '' || completed_date != '') {
                table.draw();
            }
            e.preventDefault();
        });
    });
    </script>
@endsection