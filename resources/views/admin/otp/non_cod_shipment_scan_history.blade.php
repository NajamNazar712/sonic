
@extends('admin.layout.master')
@section('title','Non-COD Shipments OTP')

@section('content')
    <h1 class="mb-1">
        Non-COD Shipments Scanning History
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')


                <form id="tracking_number_search" class="mb-1" novalidate="novalidate">
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <div class="form-group">
                                <input type="text" name="tracking_number" id="tracking_number" class="form-control tracking_number" 
                                    placeholder="Tracking Number*" data-tags-input-name="tracking_number">
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </form>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">OTP</th>
                        <th class="border-primary border-darken-1">Employee Name</th>
                        <th class="border-primary border-darken-1">Employee Designation</th>
                        <th class="border-primary border-darken-1">Trax ID</th>
                        <th class="border-primary border-darken-1">Scanned At</th>
                    </tr>
                    </thead>
                </table>
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

    <script type="text/javascript">
        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
                body = [];
                var params = table.ajax.params();
                params.start = 0;
                params.length = -1;
                params.excel = true;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.shipment_otp.shipment_otp_scanning_history_list') }}',
                    data: params,
                    success: function (result) {
                        head = [];
                        head.push('S.No');
                        head.push('Tracking No.');
                        head.push('Trax ID');
                        head.push('Employee Name');
                        head.push('Employee Designation');
                        head.push('OTP');
                        head.push('Scanned At');

                        $.each(result.data, function(index, values) {
                            row = [];
                            row.push(index + 1);                            
                            row.push(values.tracking_number);               
                            row.push(values.trax_id);                       
                            row.push(values.employee_name);                 
                            row.push(values.employee_designation);          
                            row.push(values.shipment_otp);                  
                            row.push(values.created_at);                    
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
                    title: 'Non-COD Shipments Scanning History',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                'reset'
            ],
            scrollX: true, scrollY: '500px',
            lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            pageLength: 50,
            autoWidth: false,
            pagingType: 'full_numbers',
            processing: true,
            deferLoading: 0,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            ajax:{
                url: '{{ route('admin.shipment_otp.shipment_otp_scanning_history_list') }}',
                data: function (d) {
                    d.tracking_number = $('#tracking_number').val();
                }
            },
            rowId: 'shId',
            order: [[6, 'desc']],
            columns: [
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'tracking_number_link', name: 'tracking_number', class: 'align-middle name'},
                {data: 'shipment_otp', name: 'shipment_otp', class: 'align-middle otp'},
                {data: 'employee_name', name: 'employee_name', class: 'align-middle employee_name'},
                {data: 'employee_designation', name: 'employee_designation', class: 'align-middle employee_designation'},
                {data: 'trax_id', name: 'trax_id', class: 'align-middle trax_id'},
                {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
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
                    if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.destination_arrival') || $(header).is('.location')) {
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


        var select = $('#tracking_number').selectize({
            placeholder: 'Tracking Number*',
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

        $('#tracking_number_search').bind('submit',function (e) {
            var tracking_number = $('#tracking_number_search .tracking_number').val();
            if (tracking_number != '') {
                scan_sound(1);
                table.draw();
            } else {
                scan_sound(2);
                toastr.error('Please enter a tracking number.', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }
            e.preventDefault();
        });


    </script>
@endsection