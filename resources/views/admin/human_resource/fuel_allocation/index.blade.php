@extends('admin.layout.master')

@section('title', 'Rider Fuel Allocation')

@section('content')
    <h1 class="mb-1">
        Rider Fuel Allocation
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mt-2">
                    <div class="card col-12">
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <form id="search_form" class="card-body card-dashboard" novalidate="novalidate">
                                    <div class="row justify-content-center">
                                        <input type="hidden" name="search_statistics_div" id="search_statistics_div"
                                               value="">
                                        <div class="form-group col">
                                            <input type="text" name="from_date"
                                                   class="form-control graph_date bg-primary border-primary white rounded-right"
                                                   id="from_date" placeholder="Date From"
                                                   data-value="" data-rule-required="true"
                                                   data-msg-required="This field is required">
                                        </div>
                                        <div class="form-group col">
                                            <input type="text" name="to_date"
                                                   class="form-control graph_date bg-primary border-primary white rounded-right"
                                                   id="to_date" placeholder="Date To" data-value=""
                                                   data-rule-required="true" data-msg-required="This field is required">
                                        </div>
                                        <div class="form-group col">
                                            <select name="search_hub" id="search_hub"
                                                    class="select2 form-control">
                                                @foreach($hubs as $hub)
                                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col">
                                            <select name="search_rider" id="search_rider"
                                                    class="select2 form-control">
                                                @foreach($riders as $rider)
                                                    <option value="{{ $rider->id }}"> {{ $rider->name }} | {{ $rider->trax_id }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-1">
                                            <button type="submit" class="btn round btn-primary search_button">Search <i
                                                        class="ft-bar-chart"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Rider Name</th>
                        <th class="border-primary border-darken-1">Trax ID</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Date</th>
                        <th class="border-primary border-darken-1">Delivery Note(s)</th>
                        <th class="border-primary border-darken-1">DNCC Amount</th>
                        <th class="border-primary border-darken-1">Fuel Rate (Rs)</th>
                        <th class="border-primary border-darken-1">Fuel Allocated (Ltrs)</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Allocated At</th>
                        <th class="border-primary border-darken-1">Allocated By</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="allocate_fuel_modal" role="dialog" aria-labelledby="allocate_fuel_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="edit_lead_modal_title">Allocate Fuel</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">

                    <form id="allocate_fuel_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.human_resource.fuel_allocation.allocate') }}">
                        @method('POST')
                        @csrf
                        <input type="hidden" name="id" id="selected_id">
                        <input type="hidden" name="ids" id="selected_ids">
                        <div class="col">
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control decimal" name="fuel_rate" id="fuel_rate" placeholder="Fuel Rate(Rs)*" data-rule-required="true"  data-msg-required="Fuel Rate is required">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control decimal" name="fuel_allocated" id="fuel_allocated" placeholder="Fuel Allocated (Ltrs)*" data-rule-required="true"  data-msg-required="Fuel Allocated is required">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="fuel_amount" id="fuel_amount" placeholder="Fuel Amount (Rs)*" data-rule-required="true"  data-msg-required="Fuel Amount is required" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="form-group ml-1">
                                <button type="submit" class="btn btn-primary width-200" value="Add">Allocate</button>
                                <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="DetailsModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="DetailsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    Delivery Notes
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
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

        .small-calender-icon {
            font-size: 17px !important;
        }

        .bg-gradient-directional-booked_shipments {
            background-image: linear-gradient(45deg, #5e187b, #ed86ff);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-arrived_shipments {
            background-image: linear-gradient(45deg, #074077, #2fbef5);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-in_transit {
            background-image: linear-gradient(45deg, #535BE2, #9ea5ff);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-destination {
            background-image: linear-gradient(45deg, #027d8a, #01e4e4);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-out_for_delivery {
            background-image: linear-gradient(45deg, #ff9819, #fff824);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_shipments {
            background-image: linear-gradient(45deg, #39546d, #90929a);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_confirmation {
            background-image: linear-gradient(45deg, #6a1fa2, #ff4961);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-delivered {
            background-image: linear-gradient(45deg, #076500, #11f118);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-return_confirm {
            background-image: linear-gradient(45deg, #ff0c0c, #ff9191);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_return {
            background-image: linear-gradient(45deg, #7d491c, #e0b668de);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-return_delivered {
            background-image: linear-gradient(45deg, #02c123, #99ff12d1);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-cancelled_shipments {
            background-image: linear-gradient(45deg, #ff6a00, #ffb74c);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_launched {
            background-image: linear-gradient(45deg, #074077, #2FBEF5);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_in_process {
            background-image: linear-gradient(45deg, #6A1FA2, #FF4961);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_closed {
            background-image: linear-gradient(45deg, #076500, #11F118);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_rejected {
            background-image: linear-gradient(45deg, #FF0C0C, #FF9191);
            background-repeat: repeat-x;
        }

        .selectize-control {
            width: 300px !important;
        }

        .div_border {
            border-style: double;
        }

        .statusBooked {
            background-color: #5DADE2;
        }

        .statusOrigin {
            background-color: #E67E22;
        }

        .statusIntransit {
            background-color: #7F8C8D;
        }

        .statusDestination {
            background-color: #F1C40F;
        }

        .statusNotattempted {
            background-color: #1F618D;
        }

        .statusDeliveryunsuccessful {
            background-color: #28B463;
        }

        .statusOnhold {
            background-color: #154360;
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 100000.00
            });
            $('#fuel_rate').on('change').on('change',function (){
                calculate();
            });
            $('#fuel_allocated').on('change').on('change',function (){
                calculate();
            });

            function calculate(){
                if($('#fuel_rate').val() != '' && $('#fuel_allocated').val() != ''){
                    var fuel_amount = $('#fuel_rate').val() * $('#fuel_allocated').val();
                    $('#fuel_amount').val(parseFloat(fuel_amount.toFixed(2)));
                }
            }

            $("#search_hub").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Hub",
                width: '100%'
            });

            $("#search_rider").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Rider",
                width: '100%'
            });

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::yesterday() }}',
                format: 'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $('#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });

            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format: 'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.fuel_allocation.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Rider Name');
                            head.push('Trax ID');
                            head.push('Hub');
                            head.push('Date');
                            head.push('Delivery Note(s)');
                            head.push('DNCC Amount');
                            head.push('Fuel Rate (Rs)');
                            head.push('Fuel Allocated (Ltrs)');
                            head.push('Amount');
                            head.push('Allocated At');
                            head.push('Allocated By');

                            $.each(result.data, function (index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.rider_name);
                                row.push(values.trax_id);
                                row.push(values.hub);
                                row.push(values.date);
                                row.push(values.delivery_notes);
                                row.push(values.dncc_amount);
                                row.push(values.fuel_rate);
                                row.push(values.fuel_allocated);
                                row.push(values.amount);
                                row.push(values.allocated_at);
                                row.push(values.allocated_by);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [

                        @if (session('role_id') == 1 || in_array(832, session('permissions')))
                    {
                        text: '<i class="la la-plus"></i> Allocate Fuel',
                        className: 'btn btn-primary bulk_allocate_fuel',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            $("#allocate_fuel_modal #selected_ids").val(selected_rows);
                            $('#allocate_fuel_modal').modal('show');
                        }
                    },
                        @endif
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());
                                    row.select();

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.bulk_allocate_fuel').enable();
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.bulk_allocate_fuel').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Rider Fuel Allocation',
                        className: 'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.human_resource.fuel_allocation.list') }}',
                    data: function (d) {
                        d.search_hub = $('#search_hub').val();
                        d.search_rider = $('#search_rider').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[5, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return ''; }},
                    {data: 'rider_name', name: 'r.name', class: 'align-middle rider_name'},
                    {data: 'trax_id', name: 'r.trax_id', class: 'align-middle trax_id'},
                    {data: 'hub', name: 'c.name', class: 'align-middle hub'},
                    {data: 'date', name: 'rider_fuel_allocations.date', class: 'align-middle date'},
                    {data: 'delivery_notes_button', name: 'delivery_notes', class: 'align-middle delivery_notes', orderable: false, searchable: false},
                    {data: 'dncc_amount', name: 'rider_fuel_allocations.dncc_amount', class: 'align-middle dncc_amount'},
                    {data: 'fuel_rate', name: 'rider_fuel_allocations.fuel_rate', class: 'align-middle fuel_rate'},
                    {data: 'fuel_allocated', name: 'rider_fuel_allocations.fuel_allocated', class: 'align-middle fuel_allocated'},
                    {data: 'amount', name: 'rider_fuel_allocations.amount', class: 'align-middle amount'},
                    {data: 'allocated_at', name: 'rider_fuel_allocations.allocated_at', class: 'align-middle allocated_at'},
                    {data: 'allocated_by', name: 'a.name', class: 'align-middle allocated_by'},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search) || $(header).is('.serial_number'));
                        } else {
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

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function () {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                } else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.bulk_allocate_fuel').enable();

                } else {
                    table.button('.bulk_allocate_fuel').disable();
                }
            });

            $('body').on('click', '#datatable .allocate_fuel', function () {
                var selected_id = parseInt($(this).parents('tr').attr('id'));

                $('#selected_id').val(selected_id);

                $('#allocate_fuel_modal').modal('show');
            });

            $('body').on('click', '#datatable .delivery_notes_info', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.human_resource.fuel_allocation.delivery_notes') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function (data) {
                        var details = '<table class="table table-sm table-bordered"><tbody>';

                        details += '<tr>';

                        details += '<td class="border-primary border-darken-1 align-middle text-center"><strong>S No.</strong></td>';
                        details += '<td class="border-primary border-darken-1 align-middle text-center"><strong>Delivery Note</strong></td>';
                        details += '<td class="border-primary border-darken-1 align-middle text-center"><strong>DNCC Amount</strong></td>';

                        details += '</tr>';
                        console.log(data);
                        var serial = 0;
                        $.each(data.details, function(index, delivery_note) {
                            serial++;
                            details += '<tr>';
                            details += '<td class="align-middle text-center">' + serial + '</td>';
                            details += '<td class="align-middle text-center">' + delivery_note.delivery_note_id + '</td>';
                            details += '<td class="align-middle text-center">' + delivery_note.amount + '</td>';

                            details += '</tr>';
                        });

                        details += '</tbody></table>';

                        $('#DetailsModal .modal-body').html(details);

                        $('#DetailsModal').modal('show');
                    });
            });

            $('#allocate_fuel_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function (value) {
                    return $.trim(value);
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to Allocate Fuel!',
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
                            swal({
                                title: 'Please Wait!',
                                text: 'Allocating Fuel!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });

                            $.ajax({
                                url: '{!! route('admin.human_resource.fuel_allocation.allocate') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'fuel_rate': $('#fuel_rate').val(),
                                    'fuel_allocated': $('#fuel_allocated').val(),
                                    'fuel_amount': $('#fuel_amount').val(),
                                    'id': $('#selected_id').val(),
                                    'ids': $('#selected_ids').val(),
                                }
                            })
                                .done(function (data) {
                                    if(data.status == 1){
                                        $('#allocate_fuel_modal').modal('hide');
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    }
                                    else{
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    }
                                    swal.close();
                                });
                            table.draw();
                        }
                    });
                }
            });


            $('#allocate_fuel_modal').on('hide.bs.modal', function () {
                $('#fuel_rate').val('');
                $('#fuel_allocated').val('');
                $('#fuel_amount').val('');
                $('#selected_id').val('');
                $('#selected_ids').val('');
            });

            $("#search_form").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    table.draw();
                }
            });
        });

    </script>
@endsection