@extends('admin.layout.master')

@section('title', 'FTL Request')

@section('content')
    <h1>FTL Request</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Req ID</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Weight</th>
                                    <th class="border-primary border-darken-1">Required Vehicle</th>
                                    <th class="border-primary border-darken-1">Quantity</th>
                                    <th class="border-primary border-darken-1">Date Requested For</th>
                                    <th class="border-primary border-darken-1">Status Updated On</th>
                                    <th class="border-primary border-darken-1">Status Updated By</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Vendor</th>
                                    <th class="border-primary border-darken-1">Total Cost</th>
                                    <th class="border-primary border-darken-1">Charges</th>
                                    <th class="border-primary border-darken-1">GST</th>
                                    <th class="border-primary border-darken-1">Total Charges</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade text-left" id="addFTLRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addFTLRequestModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add FTL Request</h4>

                </div>
                <form id="ftl_request_form" method="post" action="{{route('admin.ftl.request.add')}}" class="justify-content-center" novalidate="novalidate">
                    <div class="modal-body text-center">
                        @csrf
                        <div class="form-group">
                            <select class="form-control select2" name="shipper" id="shipper" data-rule-required="true" data-msg-required="Shipper is required">
                                @foreach($shippers as $shipper)
                                    <option data-sale_person="{{$shipper->sale_person_id}}" value="{{$shipper->id}}">{{$shipper->name}}</option>
                                @endforeach
                            </select>
                            <input type="text" name="shipper_name" id="shipper_name" class="form-control mt-2 d-none" placeholder="Enter Shipper Name" data-rule-required="true" data-msg-required="Shipper Name is required">
                        </div>
                        <div class="form-group">
                            <select class="form-control select2" name="sale_person" id="sale_person" data-rule-required="true" data-msg-required="Sale Person is required">
                                @foreach($sale_persons as $sale_person)
                                    <option value="{{$sale_person->id}}">{{$sale_person->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control select2" name="origin" id="origin" data-rule-required="true" data-msg-required="Origin is required">
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control select2" name="destination" id="destination" data-rule-required="true" data-msg-required="Destination is required">
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="weight" id="weight" class="form-control" placeholder="Enter Weight" data-rule-required="true" data-msg-required="Weight is required">
                        </div>
                        <div class="form-group">
                            <select class="form-control select2" name="vehicle" id="vehicle" data-rule-required="true" data-msg-required="Vehicle is required">
                                @foreach($vehicles as $vehicle)
                                    <option value="{{$vehicle->id}}">{{$vehicle->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="quantity" id="quantity" class="form-control" placeholder="Enter Quantity" data-rule-required="true" data-msg-required="Quantity is required">
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>

                            <input type="text" name="date" value="{{now()->format('d F, Y')}}" class="form-control pickadate bg-primary border-primary white rounded-right deposit_date" id="date" placeholder="Date*" data-rule-required="true" data-msg-required="Date is required">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary width-100" id="add_special_rider_button">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#ftl_request_form #shipper').prepend('<option value="" selected="selected"></option><option value="0">Other</option>').select2({
                placeholder:'Select Shipper',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#addFTLRequestModal')
            }).bind('change', function() {
                var id = parseInt($(this).val());
                if(id === 0)
                {
                    $("#ftl_request_form #shipper_name").removeClass('d-none');
                }
                else {
                    $("#ftl_request_form #shipper_name").addClass('d-none');
                    var sale_person = $(this).find(':selected').attr('data-sale_person');
                    if (sale_person != undefined) {
                        $('#ftl_request_form #sale_person').val(sale_person).trigger('change');
                    } else {
                        $('#ftl_request_form #sale_person').val("").trigger('change');
                    }
                }
            });

            $('#ftl_request_form #sale_person').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Sale Person',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#addFTLRequestModal')
            });

            $('#ftl_request_form #origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Origin',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#addFTLRequestModal')
            });

            $('#ftl_request_form #destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#addFTLRequestModal')
            });

            $('#ftl_request_form #vehicle').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Vehicle',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#addFTLRequestModal')
            });

            $('#ftl_request_form #weight').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.1,
                'max': 100000
            });

            $('#ftl_request_form #quantity').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
            });

            $('#ftl_request_form #date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    // $('#mark_as_received input.deposit_date').valid();
                }
            });

            $('#ftl_request_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
            });

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.ftl.request.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Req ID');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Tracking Number');
                            head.push('Weight');
                            head.push('Required Vehicle');
                            head.push('Quantity');
                            head.push('Date Requested For');
                            head.push('Status Updated On');
                            head.push('Status Updated By');
                            head.push('Status');
                            head.push('Vendor');
                            head.push('Total Cost');
                            head.push('Charges');
                            head.push('GST');
                            head.push('Total Charges');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.req_id);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.tracking_number);
                                row.push(values.weight);
                                row.push(values.vehicle);
                                row.push(values.quantity);
                                row.push(values.date);
                                row.push(values.updated_on);
                                row.push(values.updated_by);
                                row.push(values.status);
                                row.push(values.vendor);
                                row.push(values.total_cost);
                                row.push(values.freight_charges);
                                row.push(values.gst);
                                row.push(values.total_charges);
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
                buttons: [
                    @if(session('role_id') == 1 || in_array(512,session('permissions')))
                    {
                        text: 'Add FTL Request',
                        className: 'btn btn-primary',
                        action: function (e, dt, node, config) {
                            $("#ftl_request_form").get(0).reset();
                            $("#ftl_request_form").validate().resetForm();;
                            $("#ftl_request_form .select2").val("").trigger('change');
                            $('#addFTLRequestModal').modal('show');
                        }
                    },
                    @endif
                    {
                        extend: 'excel',
                        title: 'FTL Requests',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
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

                ajax: {
                    url: '{{ route('admin.ftl.request.list') }}',
                },
                order: [[9, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return ''; }},
                    {data: 'req_id', name: 'ftl_requests.id', class: 'align-middle req_id'},
                    {data: 'origin', name: 'origin.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'destination.name', class: 'align-middle destination'},
                    {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'weight', name: 'ftl_requests.weight', class: 'align-middle weight'},
                    {data: 'vehicle', name: 'vt.name', class: 'align-middle vehicle'},
                    {data: 'quantity', name: 'ftl_requests.quantity', class: 'align-middle quantity'},
                    {data: 'date', name: 'ftl_requests.date', class: 'align-middle date'},
                    {data: 'updated_on', name: 'ftl_requests.updated_on', class: 'align-middle updated_on'},
                    {data: 'updated_by', name: 'updated_by.name', class: 'align-middle updated_by'},
                    {data: 'status', name: 'status.status', class: 'align-middle status'},
                    {data: 'vendor', name: 'tmv.name', class: 'align-middle vendor'},
                    {data: 'total_cost', name: '', class: 'align-middle total_cost', orderable: false, searchable: false,},
                    {data: 'freight_charges', name: 'ftl_requests.freight_charges', class: 'align-middle freight_charges'},
                    {data: 'gst', name: 'ftl_requests.gst', class: 'align-middle gst'},
                    {data: 'total_charges', name: 'ftl_requests.total_charges', class: 'align-middle total_charges'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action')  || $(header).is('.serial_number') || $(header).is('.total_cost')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
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
                    var status_select_data = $.map({!! $statuses !!}, function (obj) {
                        obj.id = obj.status;
                        obj.text = obj.status;
                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data: status_select_data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

        });
    </script>

@endsection