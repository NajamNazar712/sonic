
@extends('admin.layout.master')
@section('title','Non-COD Shipments OTP')

@section('content')
    <h1 class="mb-1">
        Non-COD Shipments OTP
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                @if(session('role_id') == 1 || in_array(819,session('permissions')))
                    <form action="{{route('admin.shipment_otp.update')}}" method="post" novalidate="novalidate" id="delivery_otp_form">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="input-group col-3">
                                <label class="mr-2 font-small-3"><b>Shipment OTP: </b></label>
                                <div class="form-group">
                                    <input type="checkbox" name="otp_toggle" id="otp_toggle" class="switchery otp_toggle" data-size="sm" data-switchery="true" @if(isset($setting->setting_value) && $setting->setting_value == 1) checked @endif>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="input-group col-2">
                                <button class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                @endif

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">OTP</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">Location</th>
                        <th class="border-primary border-darken-1">Generated At</th>
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
                    url: '{{ route('admin.shipment_otp.list') }}',
                    data: params,
                    success: function (result) {
                        head = [];

                        head.push('S.No');
                        head.push('Tracking No.');
                        head.push('OTP');
                        head.push('Rider');
                        head.push('Generated At');
                        $.each(result.data, function(index, values) {
                            row = [];
                            row.push(index + 1);
                            row.push(values.tracking_number);
                            row.push(values.otp);
                            row.push(values.rider_name);
                            row.push(values.generated_at);
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
                    title: 'Non-COD Shipments OTP',
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
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            ajax:{
                url: '{{ route('admin.shipment_otp.list') }}',
            },
            rowId: 'shId',
            order: [[5, 'desc']],
            columns: [
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle name'},
                {data: 'otp', name: 'shipment_otps.otp', class: 'align-middle otp'},
                {data: 'rider_name', name: 'r.name', class: 'align-middle rider_name'},
                {data: 'location', name: 'location', class: 'align-middle location', orderable: false, searchable: false},
                {data: 'generated_at', name: 'shipment_otps.updated_at', class: 'align-middle generated_at'},

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

        $("#delivery_otp_form").validate({
            errorClass: 'danger',
            successClass: 'success',
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            submitHandler: function (form) {
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to toggle Shipment OTP!',
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
                    if(confirm){
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');
                        blockPagePermanently();
                        form.submit();
                    }
                });
            }
        });
    </script>
@endsection