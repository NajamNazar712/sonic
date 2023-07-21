@extends('admin.layout.master')

@section('title', 'Leads Management')

@section('content')
    <h1 class="mb-1">
        Team Leads Management
    </h1>


    @include('admin.inc.messages')


    <div class="row justify-content-center">
        <div class="col-3" id="total_leads_div">
            <div class="card bg-gradient-directional-booked_shipments pull-up cursor-pointer">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="align-self-center">
                                <i class="icon-grid text-white font-large-2 float-left"></i>
                            </div>
                            <div class="media-body text-white text-right">
                                <h3 class="text-white">
                                    <p id="total_leads" class="d-inline">2</p> (100%)
                                </h3>
                                <span>Overall Number of Tickets (RCP count) </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3" id="received_leads_div">
            <div class="card bg-gradient-directional-complaints_launched pull-up cursor-pointer">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="align-self-center">
                                <i class="icon-flag text-white font-large-2 float-left"></i>
                            </div>
                            <div class="media-body text-white text-right">
                                <h3 class="text-white">
                                    <p id="received_leads" class="d-inline">1</p> (<p id="received_percentage"
                                        class="d-inline">3</p>%)
                                </h3>
                                <span>Online/ Available Agents </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3" id="in_process_div">
            <div class="card bg-gradient-directional-in_transit pull-up cursor-pointer">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="align-self-center">
                                <i class="icon-clock text-white font-large-2 float-left"></i>
                            </div>
                            <div class="media-body text-white text-right">
                                <h3 class="text-white">
                                    <p id="in_process" class="d-inline">3</p> (<p id="in_process_percentage"
                                        class="d-inline">3</p>%)
                                </h3>
                                <span>Pending Tickets</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-3" id="in_process_activation_div">
            <div class="card bg-gradient-directional-out_for_delivery pull-up cursor-pointer">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="align-self-center">
                                <i class="icon-clock text-white font-large-2 float-left"></i>
                            </div>
                            <div class="media-body text-white text-right">
                                <h3 class="text-white">
                                    <p id="in_process_activation" class="d-inline">4</p>
                                    (<p id="in_process_for_activation_percentage" class="d-inline">4</p>
                                    %)
                                </h3>
                                <span>Closed Tickets</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row justify-content-center">
        <div class="col-3" id="dead_leads_div">
            <div class="card bg-gradient-directional-pending_shipments pull-up cursor-pointer">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="align-self-center">
                                <i class="icon-close text-white font-large-2 float-left"></i>
                            </div>
                            <div class="media-body text-white text-right">
                                <h3 class="text-white">
                                    <p id="dead_leads" class="d-inline">5</p> (<p id="dead_percentage" class="d-inline">5
                                    </p>%)
                                </h3>
                                <span>No. of Connected Calls.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3" id="activated_leads_div">
            <div class="card bg-gradient-directional-return_delivered pull-up cursor-pointer">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="align-self-center">
                                <i class="icon-check text-white font-large-2 float-left"></i>
                            </div>
                            <div class="media-body text-white text-right">
                                <h3 class="text-white">
                                    <p id="active_leads" class="d-inline">6</p>
                                    (<p id="active_percentage" class="d-inline">6</p>%)
                                </h3>
                                <span>No. of Unresponsive Calls</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-3" id="lead_time_ratio_div">
            <div class="card bg-gradient-directional-return_confirm pull-up cursor-pointer">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="align-self-center">
                                <i class="la la-calculator text-white font-large-2 float-left"></i>
                            </div>
                            <div class="media-body text-white text-right">
                                <h3 class="text-white" id="dead_ratio">7</h3>
                                <span>No. of Re-attempt updated</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3" id="lead_time_ratio_div">
            <div class="card bg-gradient-directional-destination pull-up cursor-pointer">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="align-self-center">
                                <i class="la la-calculator text-white font-large-2 float-left"></i>
                            </div>
                            <div class="media-body text-white text-right">
                                <h3 class="text-white" id="active_ratio">8</h3>
                                <span>No. of Return Confirm updated</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="joiningDateModal" tabindex="-1" role="dialog" aria-labelledby="joiningDateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="joiningDateModalLabel">Add Days
                        {{-- <strong>({{ Auth::User()->name }})</strong> --}}
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="myForm" action="{{ route('admin.team_lead.add_additional_days') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" value="" id="employee_id" name="employee_id">
                        <div class="row">
                            <div class="col">
                                <fieldset class="form-group input-group">
                                    <div class="input-group-prepend">
                                        <span
                                            class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                    </div>
                                    <input type="text" data-rule-required="true"
                                        data-msg-required="This Field is required"
                                        class="form-control bg-primary border-primary white rounded-right pickadate datepicker"
                                        id="joining_date" placeholder="Add Days" name="add_additional_days">
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    @if ($employee_additional_days->isNotEmpty())
                        <div class="card days_show">
                            <div class="card-header">
                                <strong>
                                    <h5 class="card-title" style="font-weight: bold; text-decoration: underline;">Working
                                        Days</h5>
                                </strong>
                            </div>
                            <div class="card-body" id="delete_days">

                                @foreach ($employee_additional_days as $employee_additional_day)
                                    @php
                                        $workingDay = \Carbon\Carbon::parse($employee_additional_day->working_days)->format('l');
                                    @endphp

                                    <div class="parent-element">

                                        <a class="btn btn-sm btn-danger float-right delete-icon" title="Delete"
                                            data-del-id="{{ $employee_additional_day->id }}">
                                            <i class="fas fa-trash">Delete</i>
                                        </a>
                                        <div class="card-title" id="used_dates">
                                            {{ $employee_additional_day->working_days }}
                                            ({{ $workingDay }})
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endif

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-3" id="dormant_div">
            <div class="card bg-gradient-directional-pending_confirmation pull-up cursor-pointer">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="align-self-center">
                                <i class="la la-hourglass text-white font-large-2 float-left"></i>
                            </div>
                            <div class="media-body text-white text-right">
                                <h3 class="text-white">
                                    <p id="dormant" class="d-inline">9</p>
                                </h3>
                                <span>No. of Intercept updated.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3" id="updated_id">
            <div class="card bg-gradient-directional-pending_confirmation pull-up cursor-pointer">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="align-self-center">
                                <i class="la la-hourglass text-white font-large-2 float-left"></i>
                            </div>
                            <div class="media-body text-white text-right">
                                <h3 class="text-white">
                                    <p id="dormant" class="d-inline">9</p>
                                </h3>
                                <span>No. of Self-collection updated.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row justify-content-center">

    </div>

    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
        <thead>
            <tr class="bg-primary white">
                {{--                                    <th class="border-primary border-darken-1"></th> --}}
                <th class="border-primary border-darken-1">S No.</th>
                <th class="border-primary border-darken-1">Employee ID</th>
                <th class="border-primary border-darken-1">Old Employee ID</th>
                <th class="border-primary border-darken-1">Employee Name</th>
                <th class="border-primary border-darken-1">Father Name</th>
                <th class="border-primary border-darken-1">Gender</th>
                <th class="border-primary border-darken-1">Hub</th>
                <th class="border-primary border-darken-1">City</th>
                <th class="border-primary border-darken-1">CNIC</th>
                <th class="border-primary border-darken-1">Phone Number</th>
                <th class="border-primary border-darken-1">Official Email</th>
                <th class="border-primary border-darken-1">Employee Type</th>
                <th class="border-primary border-darken-1">Rider Main Category</th>
                <th class="border-primary border-darken-1">Incentive Amount</th>
                <th class="border-primary border-darken-1">Designation</th>
                <th class="border-primary border-darken-1">Department</th>
                <th class="border-primary border-darken-1">Line Manager</th>
                <th class="border-primary border-darken-1">IBAN No.</th>
                <th class="border-primary border-darken-1">Zone</th>
                <th class="border-primary border-darken-1">Replacement Trax ID</th>
                <th class="border-primary border-darken-1">Replacement Name</th>
                <th class="border-primary border-darken-1">Request/Document Status</th>
                <th class="border-primary border-darken-1">Employee Status</th>
                <th class="border-primary border-darken-1">Requested At</th>
                <th class="border-primary border-darken-1">Joining Date</th>
                <th class="border-primary border-darken-1">Last Working Date</th>
                <th class="border-primary border-darken-1">Remarks</th>
                <th class="border-primary border-darken-1">Confirmation Status</th>
                <th class="border-primary border-darken-1"></th>
            </tr>
        </thead>
    </table>

    <div class="modal fade" id="AssignHubModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="LastWorkingDayModal" aria-hidden="true">
        <div class="modal-dialog modal-lg justify-content-center" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Select Hub</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="assign_agent_hubs" novalidate="novalidate" method="post"
                    action="{{ route('admin.team_lead.assign_hub_agent') }}">
                    @csrf
                    <div class="modal-body mx-3 d-flex justify-content-center">
                        <div class="col-12 col-md-8 col-lg-6 mt-1">
                            <!-- Adjust the column width as per your preference -->
                            <select name="assign_hubs[]" id="search_origin" class="form-control select2" multiple
                                style="width: 100%;">
                                @foreach ($hubs as $hub)
                                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                @endforeach
                            </select>

                            <div class="text-danger" id="select_message_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary" id="working_day_btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/simple-line-icons/style.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/cryptocoins/cryptocoins.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
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
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/additional-methods.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            $('body').on('click', '.delete-icon', function() {
                var delId = $(this).data('del-id');
                var deleteDaysContainer = $('#delete_days');
                var deleteIcon = $(this);
                $.ajax({
                        url: '{{ route('admin.team_lead.delete_additional_days') }}',
                        type: 'GET',
                        data: {
                            'id': delId,
                        }
                    })
                    .done(function(data) {

                        var object = data.object; // Assuming 'data.object' contains the array or object you want to get the length of

                        console.log(object);

                        // Get the length of the 'object'
                        var objectLength = 0;
                        if (Array.isArray(object)) {
                            objectLength = object.length; // If 'object' is an array
                        } else if (typeof object === 'object' && object !== null) {
                            objectLength = Object.keys(object).length; // If 'object' is an object
                        }

                        if (objectLength == 1) {
                            $('.days_show').addClass('d-none')
                        }
                        if (data.status == 1) {
                            toastr.success(data.success,
                                'Success!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });

                            deleteIcon.closest('.parent-element').remove();

                        } else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    })
                    .fail(function(xhr) {
                        console.log(xhr.statusText);
                    });
            });


            var rv_city = null;

            var table = $('#datatable').DataTable();

            if ($.fn.DataTable.isDataTable('#datatable')) {
                table.destroy();
            }

            $('#datatable tbody').on('click', 'tr', function() {
                var rowData = table.row(this).data();
                rv_city = rowData['rv_city'];
                console.log(rowData);
            });


            $('#assign_agent_hubs').on('submit', function(event) {
                event.preventDefault();

                var selectedValue = $('#search_origin').val();

                if (selectedValue == '') {
                    $('#select_message_error').text('Please select at least one Hub');
                } else {
                    this.submit();
                }


            });



            $('body').on('click', '.assign_hub', function() {


                var employeeId = $(this).attr('data-id');
                var cities = $(this).attr('data-city');
                console.log(cities);
                $('#assign_agent_hubs').append('<input type="hidden" name="employee_id" value="' +
                    employeeId + '">');

                var selectedCities = cities.toString().split(',');

                if (rv_city != null) {
                    $('#search_origin option').each(function() {
                        var optionValue = $(this).val();

                        if (selectedCities.includes(optionValue)) {
                            $(this).prop('selected', true);
                        } else {
                            $(this).prop('selected', false);
                        }
                    });

                    $('#search_origin').trigger('change');


                } else if (rv_city == null) {
                    $("#search_origin option").prop("selected", false).trigger("change");

                }
            });

            var area = '';
            var territory = '';

            $("#search_origin").prepend('<option value=""></option>').select2({
                placeholder: "Select Hub",
                width: '100%'
            });

            $("#reference_id").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Reference*",
                width: '100%'
            });

            $("#edit_reference_id").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Reference*",
                width: '100%'
            });

            $("#search_sale_person").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Sale Person",
                width: '100%'
            });

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format: 'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $(
                            '#search_form #from_date').pickadate('picker').get('select'));
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
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $(
                            '#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.team_lead.list') }}',
                        data: params,
                        success: function(result) {
                            head = [];
                            head.push('S.No');
                            head.push('Employee ID');
                            head.push('Employee Old Trax ID');
                            head.push('Employee Name');
                            head.push('Father Name');
                            head.push('Gender');
                            head.push('Hub');
                            head.push('City');
                            head.push('CNIC');
                            head.push('Phone No.');
                            head.push('Official Email');
                            head.push('Employee Type');
                            head.push('Rider Main Category');
                            head.push('Incentive Amount');
                            head.push('Designation');
                            head.push('Department Name');
                            head.push('Line Manager');
                            head.push('IBAN No.');
                            head.push('Zone Name');
                            head.push('Repalcement Tax ID');
                            head.push('Repalcement Name');
                            head.push('Request/Document Status');
                            head.push('Employee Status');
                            head.push('Requested At');
                            head.push('Joining Date');
                            head.push('Last Working Date');
                            head.push('Remarks');
                            head.push('Confirmation Status');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.old_trax_id);
                                row.push(values.employee_name);
                                row.push(values.father_name);
                                row.push(values.gender);
                                row.push(values.employee_hub);
                                row.push(values.city);
                                row.push(values.cnic);
                                row.push(values.phone_number);
                                row.push(values.official_email);
                                row.push(values.employee_type);
                                row.push(values.rider_main_category);
                                row.push(values.incentive_amount);
                                row.push(values.employee_designation);
                                row.push(values.department_name);
                                row.push(values.line_manager);
                                row.push(values.iban);
                                row.push(values.zone_name);
                                row.push(values.r_trax_id);
                                row.push(values.r_name);
                                row.push(values.request_status);
                                row.push(values.status);
                                row.push(values.requested_at);
                                row.push(values.joining_date);
                                row.push(values.last_working_date);
                                row.push(values.remarks);
                                row.push(values.confirmation_status);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {
                        body: body,
                        header: head
                    };
                }
            });

            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    @if (session('role_id') == 1 || in_array(712, session('permissions')))
                        {
                            text: '<i class="la la-refresh"></i> Update Line Manager',
                            className: 'btn btn-primary',
                            action: function(e, dt, node, config) {
                                $("#updateLineManagerForm #line_manager_id").val('').trigger(
                                    'change');
                                $("#updateLineManagerModal").modal('show');
                            }
                        },
                    @endif {
                        extend: 'excel',
                        title: 'Employee Directory',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    @if (session('role_id') == 1 || session('role_id') == 6 || in_array(652, session('permissions')))
                        {
                            text: 'Approve',
                            className: 'btn btn-primary bulk_approve d-none',
                            enabled: false,
                            action: function(e, dt, node, config) {
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes Approve Employee!',
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
                                }).then(function(confirm) {
                                    if (confirm) {
                                        swal({
                                            title: 'Please Wait!',
                                            text: 'Employee is being Approved',
                                            icon: 'info',
                                            buttons: false,
                                            closeOnClickOutside: false,
                                            closeOnEsc: false
                                        });

                                        $.ajax({
                                                url: '{!! route('admin.human_resource.employee_directory.approve') !!}',
                                                method: 'POST',
                                                data: {
                                                    'employee_ids[]': selected_rows,
                                                    '_token': '{{ csrf_token() }}'
                                                }
                                            })
                                            .done(function(data) {
                                                if (data.status == 0) {
                                                    toastr.success(data.success,
                                                        'Success!', {
                                                            positionClass: 'toast-bottom-center',
                                                            containerId: 'toast-bottom-center'
                                                        });
                                                } else {
                                                    toastr.error(data.error, 'Error!', {
                                                        positionClass: 'toast-top-center',
                                                        containerId: 'toast-top-center'
                                                    });
                                                }
                                                swal.close();
                                                selected_rows = [];

                                                table.rows().deselect();
                                                table.draw('false');
                                            });
                                    }
                                });
                            }
                        }, {
                            text: 'Reject',
                            className: 'btn btn-danger bulk_reject d-none',
                            enabled: false,
                            action: function(e, dt, node, config) {
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes Reject Employee!',
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
                                }).then(function(confirm) {
                                    if (confirm) {
                                        swal({
                                            title: 'Please Wait!',
                                            text: 'Employee is being Rejected',
                                            icon: 'info',
                                            buttons: false,
                                            closeOnClickOutside: false,
                                            closeOnEsc: false
                                        });

                                        $.ajax({
                                                url: '{!! route('admin.human_resource.employee_directory.reject') !!}',
                                                method: 'POST',
                                                data: {
                                                    'employee_ids[]': selected_rows,
                                                    '_token': '{{ csrf_token() }}'
                                                }
                                            })
                                            .done(function(data) {
                                                if (data.status == 0) {
                                                    toastr.success(data.success,
                                                        'Success!', {
                                                            positionClass: 'toast-bottom-center',
                                                            containerId: 'toast-bottom-center'
                                                        });
                                                } else {
                                                    toastr.error(data.error, 'Error!', {
                                                        positionClass: 'toast-top-center',
                                                        containerId: 'toast-top-center'
                                                    });
                                                }
                                                swal.close();
                                                selected_rows = [];

                                                table.rows().deselect();
                                                table.draw('false');
                                            });
                                    }
                                });
                            }
                        },
                    @endif {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all d-none',
                        action: function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.bulk_approve').enable();
                                    table.button('.bulk_reject').enable();
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none d-none',
                        action: function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.bulk_approve').disable();
                                        table.button('.bulk_reject').disable();
                                    }
                                }
                            });
                        }
                    },
                    'reset'
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                scrollX: true,
                scrollY: '500px',
                lengthMenu: [
                    [50, 100, 500, 1000, -1],
                    [50, 100, 500, 1000, 'All']
                ],
                pageLength: 50,
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: false,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,


                ajax: {
                    url: '{{ route('admin.team_lead.list') }}',
                    data: function(d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.search_line_manager = $('#search_line_manager').val();
                        d.filter_line_manager = $('#filter_line_manager').val();
                        d.search_origin = $('#search_origin').val();
                    }
                },
                order: [
                    [22, 'desc']
                ],
                rowId: 'employee_id',
                columns: [
                    // {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function(data, type, row) {
                            return '';
                        }
                    },
                    {
                        data: 'trax_id',
                        name: 'employees.trax_id',
                        class: 'align-middle trax_id'
                    },
                    {
                        data: 'old_trax_id',
                        name: 'employees.old_trax_id',
                        class: 'align-middle old_trax_id'
                    },
                    {
                        data: 'employee_name',
                        name: 'employees.name',
                        class: 'align-middle employee_name'
                    },
                    {
                        data: 'father_name',
                        name: 'employees.father_name',
                        class: 'align-middle father_name'
                    },
                    {
                        data: 'gender',
                        name: 'eg.name',
                        class: 'align-middle gender'
                    },
                    {
                        data: 'employee_hub',
                        name: 'employee_hub',
                        class: 'align-middle employee_hub',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'city',
                        name: 'cities.name',
                        class: 'align-middle city'
                    },
                    {
                        data: 'cnic',
                        name: 'employees.cnic',
                        class: 'align-middle cnic'
                    },
                    {
                        data: 'phone_number',
                        name: 'employees.phone_number',
                        class: 'align-middle phone_number'
                    },
                    {
                        data: 'official_email',
                        name: 'employees.official_email',
                        class: 'align-middle official_email'
                    },
                    {
                        data: 'employee_type',
                        name: 'et.name',
                        class: 'align-middle employee_type'
                    },
                    {
                        data: 'rider_main_category',
                        name: 'rmc.name',
                        class: 'align-middle rider_main_category'
                    },
                    {
                        data: 'incentive_amount',
                        name: 'r.incentive_amount',
                        class: 'align-middle incentive_amount'
                    },
                    {
                        data: 'employee_designation',
                        name: 'ed.name',
                        class: 'align-middle employee_designation'
                    },
                    {
                        data: 'department_name',
                        name: 'ads.name',
                        class: 'align-middle department_name'
                    },
                    {
                        data: 'line_manager',
                        name: 'lm.name',
                        class: 'align-middle line_manager'
                    },
                    {
                        data: 'iban',
                        name: 'eb.iban',
                        class: 'align-middle iban'
                    },
                    {
                        data: 'zone_name',
                        name: 'ez.id',
                        class: 'align-middle zone_name'
                    },
                    {
                        data: 'r_trax_id',
                        name: 'r_emp.trax_id',
                        class: 'align-middle r_trax_id'
                    },
                    {
                        data: 'r_name',
                        name: 'r_emp.name',
                        class: 'align-middle r_name'
                    },
                    {
                        data: 'request_status',
                        name: 'ers.name',
                        class: 'align-middle request_status'
                    },
                    {
                        data: 'status',
                        name: 'es.id',
                        class: 'align-middle status'
                    },
                    {
                        data: 'requested_at',
                        name: 'employees.created_at',
                        class: 'align-middle requested_at'
                    },
                    {
                        data: 'joining_date',
                        name: 'employees.joining_date',
                        class: 'align-middle joining_date'
                    },
                    {
                        data: 'last_working_date',
                        name: 'employees.last_working_date',
                        class: 'align-middle last_working_date'
                    },
                    {
                        data: 'remarks',
                        name: 'employees.remarks',
                        class: 'align-middle remarks'
                    },
                    {
                        data: 'confirmation_status',
                        name: 'employees.confirmation_status',
                        class: 'align-middle confirmation_status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        class: 'align-middle text-center action',
                        orderable: false,
                        searchable: false
                    }
                ],
                rowCallback: function(row, data, index) {
                    // $('td:eq(0)', row).addClass('select-checkbox');
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>')
                        .appendTo(this.api().table().header());

                    var td =
                        '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input =
                        '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon =
                        '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var employee_type =
                        '<select name="employee_type_search" id="employee_type_search" class="select2 form-control">' +
                        '</select>';
                    // var department_type = '<select name="department_type_search" id="department_type_search" class="select2 form-control">' +
                    //     '</select>';
                    var employee_status =
                        '<select name="employee_status_search" id="employee_status_search" class="select2 form-control">' +
                        '</select>';


                    var employee_confirmation_status =
                        '<select name="employee_confirmation_status" id="employee_confirmation_status" class="select2 form-control">' +
                        '<option value="2">Probation</option>' +
                        '<option value="1">Permanent</option>' +
                        '</select>';
                    var rider_main_categories =
                        '<select name="rider_main_categories_search" id="rider_main_categories_search" class="select2 form-control">' +
                        '</select>';

                    var employee_zone =
                        '<select name="employee_zone_search" id="employee_zone_search" class="select2 form-control">' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(
                                header).is('.select') || $(header).is('.employee_hub')) {
                            $(td).appendTo($(search));
                        } else if ($(header).is('.employee_type')) {
                            $(employee_type).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.status')) {
                            $(employee_status).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.zone_name')) {
                            $(employee_zone).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.rider_main_category')) {
                            $(rider_main_categories).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.confirmation_status')) {
                            $(employee_confirmation_status).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        // else if($(header).is('.department_name'))
                        // {
                        //     $(department_type).appendTo($(search))
                        //         .on( 'change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         } ).wrap(td);
                        // }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    data = [{
                        'id': 1,
                        'text': 'Staff'
                    }, {
                        'id': 2,
                        'text': 'Rider - Permanent'
                    }, {
                        'id': 3,
                        'text': 'Rider - Incentive'
                    }, {
                        'id': 4,
                        'text': 'Intern'
                    }];

                    $("#employee_type_search").prepend('<option value="" selected></option>').select2({
                        data: data,
                        placeholder: "Select Employee Type",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });




                    $("#employee_confirmation_status").prepend('<option value="" selected></option>')
                        .select2({
                            placeholder: "Select Status",
                            width: '100%',
                            containerCssClass: 'select-xs',
                            dropdownCssClass: 'form-control-sm p-0'
                        });







                    {{-- var department_name_data = $.map({!! $employee_department !!}, function (obj) { --}}
                    {{--    obj.text = obj.name; --}}
                    {{--    return obj; --}}
                    {{-- }); --}}
                    {{-- $("#department_type_search").prepend('<option value="" selected></option>').select2({ --}}
                    {{--    data: department_name_data, --}}
                    {{--    placeholder: "Select Department Type", --}}
                    {{--    width: '100%', --}}
                    {{--    containerCssClass: 'select-xs', --}}
                    {{--    dropdownCssClass: 'form-control-sm p-0' --}}
                    {{-- }); --}}

                    this.api().table().columns.adjust();
                }
            });

            $("#filter_line_manager_btn").on('click', function() {
                $("#filter_line_manager").val(1);
                table.draw();
            });


            $('body').on('click', '.deactivate_staff', function() {
                var employeeId = $(this).attr('data-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes To De Activate Employee!',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Employee is being De Activate',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                                url: '{{ route('admin.team_lead.deactivate_staff') }}',

                                method: 'POST',
                                data: {
                                    'employee_id': employeeId,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {

                                swal.close();
                                table.draw();
                            });
                    }
                });
                // var id = $(this).data('target-id');
                // $('#employee_id').val(id);
                // $('#approveRiderModal').modal('show');
            });


            var used_date;

            var employee_working_days = @json($employee_additional_days); 

            console.log(employee_working_days)
            $('body').on('click', '.add_additional_days', function() {
                $('#joiningDateModal').modal('show');
                var employeeId = $(this).attr('data-id');
                var employee = $(this).attr('data-ename');
                $('#joiningDateModalLabel').html('Add Days <strong>(' + employee + ')</strong>');
                $('#employee_id').val(employeeId);

                for (var i = 0; i < employee_working_days.length; i++) {
                    var workingDayObj = employee_working_days[i];
                    var workingDay = workingDayObj.working_days;
                    var used_date = workingDay.replace(')', '').split('(')[0].trim();
                    var picker = replacement_last_working_day.pickadate('picker');
                    picker.set('disable', [new Date(used_date)]);
                }
            });

            var today = new Date();

            var replacement_last_working_day = $('.datepicker').pickadate({
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                min: today,
                disable: [],
                onOpen: function() {
                    var daysToDisable = [2, 3, 4, 5, 6, 7];
                    this.set('enable', [1]);
                    this.set('disable', daysToDisable);
                }
            });

            $('body').on('click', '.activate_staff', function() {
                var employeeId = $(this).attr('data-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes To Activate Employee!',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Employee is being Activate',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                                url: '{{ route('admin.team_lead.activate_staff') }}',

                                method: 'POST',
                                data: {
                                    'employee_id': employeeId,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {

                                swal.close();
                                table.draw();
                            });
                    }
                });
                // var id = $(this).data('target-id');
                // $('#employee_id').val(id);
                // $('#approveRiderModal').modal('show');
            });



            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                } else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.bulk_approve').enable();
                    table.button('.bulk_reject').enable();
                } else {
                    table.button('.bulk_approve').disable();
                    table.button('.bulk_reject').disable();
                }
            });
            $('body').on('click', '.approve', function(e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes Approve Employee!',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Employee is being Approved',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                                url: '{!! route('admin.human_resource.employee_directory.required_info') !!}',
                                method: 'POST',
                                data: {
                                    'employee_id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {
                                if (data.status == 2) {
                                    if (!data.joining_date) {
                                        $('#approveStaffForm #joining_date_group').removeClass(
                                            'd-none');
                                    } else {
                                        $('#approveStaffForm #joining_date_group').addClass(
                                            'd-none');
                                    }

                                    if (!data.employee_nature) {
                                        $('#approveStaffForm #employee_nature_list_group')
                                            .removeClass('d-none');
                                    } else {
                                        $('#approveStaffForm #employee_nature_list_group')
                                            .addClass('d-none');
                                    }

                                    if (!data.sub_department) {
                                        $('#approveStaffForm #sub_department_group')
                                            .removeClass('d-none');
                                    } else {
                                        $('#approveStaffForm #sub_department_group').addClass(
                                            'd-none');
                                    }

                                    $('#approveStaffForm #employee_id').val(data.employee_id);

                                    $('#employeeRequiredInfoModal').modal('show');
                                } else if (data.status == 3) {
                                    $('#approveRiderForm #employee_id').val(data.employee_id);
                                    $('#RiderRequiredInfoModal').modal('show');
                                } else if (data.status == 1) {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                } else if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
                // var id = $(this).data('target-id');
                // $('#employee_id').val(id);
                // $('#approveRiderModal').modal('show');
            });


            $('body').on('click', '.rejoin', function(e) {
                var id = $(this).data('target-id');
                var employee_type = table.row($(this).parents('tr')).data().employee_type_id;
                var trax_id = table.row($(this).parents('tr')).data().trax_id;

                if (employee_type == 1) {
                    $("#rejoinStaffForm #employee_id").val(id);
                    $("#rejoinStaffForm #old_trax_id").val(trax_id);
                    $("#rejoinStaffModal").modal("show");
                } else {
                    edit_Rider_function(this, true);
                }
            });

            $('body').on('click', '.reject', function(e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes Reject Employee!',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Employee is being Rejected',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                                url: '{!! route('admin.human_resource.employee_directory.reject') !!}',
                                method: 'POST',
                                data: {
                                    'employee_ids[]': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });
            var log_datatable = $('#designation_logs_table').DataTable({
                dom: 'ltipr',
                scrollX: false,
                autoWidth: false,
                paging: false,
                columns: [{
                        name: 'serial_number',
                        orderable: false,
                        searchable: false,
                        class: 'align-middle serial_number'
                    },
                    {
                        name: 'designation',
                        class: 'align-middle',
                        orderable: false,
                        searchable: false
                    },
                    {
                        name: 'updated_by',
                        class: 'align-middle',
                        orderable: false,
                        searchable: false
                    },
                    {
                        name: 'updated_at',
                        class: 'align-middle',
                        orderable: false,
                        searchable: false
                    },
                ],
                rowCallback: function(row, data, index) {
                    var info = log_datatable.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            $('body').on('click', '.designation_logs_1', function(e) {
                var id = $(this).data('target-id');
                $.ajax({
                    url: '{!! route('admin.human_resource.employee_directory.designation_logs') !!}',
                    type: 'POST',
                    data: {
                        'employee_id': id,
                        '_token': '{!! csrf_token() !!}'
                    }
                }).done(function(data) {
                    if (data.status == 1) {
                        var logs = data.logs;
                        $.each(logs, function(index, value) {
                            log_datatable.row.add([0, value.designation, value.updated_by,
                                value.updated_at
                            ]);
                            log_datatable.draw(true);
                        });
                        $('#designationChangeLogModal').modal('show');
                    } else {
                        toastr.error(data.error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });
            });

            $('body').on('hidden.bs.modal', '#designationChangeLogModal', function() {
                log_datatable.clear().draw();
            });

            $('body').on('click', '.employee_log', function(e) {
                var id = $(this).data('target-id');
                $.ajax({
                    url: '{!! route('admin.human_resource.employee_directory.employee_log') !!}',
                    type: 'POST',
                    data: {
                        'employee_id': id,
                        '_token': '{!! csrf_token() !!}'
                    }
                }).done(function(data) {
                    if (data.status == 1) {
                        var table_data = "";
                        $.each(data.logs, function(index, value) {
                            table_data += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${value.employee_type}</td>
                                    <td>${value.employee_status}</td>
                                    <td>${value.rejoin_employee}</td>
                                    <td>${value.pin_update}</td>
                                    <td>${value.blacklist}</td>
                                    <td>${value.updated_by}</td>
                                    <td>${value.updated_at}</td>

                                </tr>
                            `
                        });
                        $('#employee_log_header tbody').html(table_data);
                        $('#employee_log').modal('show');
                    } else {
                        toastr.error(data.error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });
            });


            function edit_Rider_function(elm, rejoin = false) {
                var id = $(elm).data('target-id');
                var rider_name = table.row($(elm).parents('tr')).data().employee_name;
                var cnic = table.row($(elm).parents('tr')).data().cnic;
                var phone_no = table.row($(elm).parents('tr')).data().phone_number;
                var pin = table.row($(elm).parents('tr')).data().pin;
                var address = table.row($(elm).parents('tr')).data().address;
                var city_id = table.row($(elm).parents('tr')).data().city_id;
                var shift_id = table.row($(elm).parents('tr')).data().shift_id;
                var check_bit = table.row($(elm).parents('tr')).data().check_if_rider_present_bit;
                var sub_category = table.row($(elm).parents('tr')).data().rider_sub_category;
                var main_category = table.row($(elm).parents('tr')).data().rider_main_category_id;
                var rider_type = table.row($(elm).parents('tr')).data().rider_type_id;
                $('#city_list').val(city_id).trigger('change');
                $('#shift_list').val(shift_id).trigger('change');
                if (check_bit != null) {
                    // var rider_type = table.row($(elm).parents('tr')).data().active_rider_type_id;
                    // $('#main_category_list').val(table.row($(elm).parents('tr')).data().category_id).trigger('change');
                    // $('#category_list').val(table.row($(elm).parents('tr')).data().category_id).trigger('change');
                    $('#category').val(table.row($(elm).parents('tr')).data().operation_id).trigger('change');
                    route_id = table.row($(elm).parents('tr')).data().route_id;
                    trax_id = table.row($(elm).parents('tr')).data().trax_id;
                    var ccd = table.row($(elm).parents('tr')).data().ccd;
                } else {
                    // var rider_type = table.row($(elm).parents('tr')).data().inactive_rider_type_id;
                    var ccd = false;
                    route_id = null;
                }
                ccd = Boolean(ccd)
                $('#editRiderModal #employee_id').val(id);
                $('#rider_name').val(rider_name);
                $('#rider_cnic').val(cnic);
                $('#rider_phone').val(phone_no);
                $('#rider_pin').val(pin);
                $('#address').val(address);
                $('#rider_type_list').val(rider_type).trigger('change');
                $('#main_category_list').val(main_category).trigger('change');
                $('#category_list').val(sub_category).trigger('change');
                if (rider_type == 1) {
                    $(".edit_ccd_rider_checkbox_div").show();
                    if (ccd != document.getElementById("edit_ccd_rider_checkbox").checked) {
                        switchery.setPosition(true);
                        switchery.handleOnchange(true);
                    }
                } else {
                    $(".edit_ccd_rider_checkbox_div").hide();
                }

                if (rejoin) {
                    $('#editRiderModal .modal-title').text("Rejoin Rider");
                    $('#editRiderModal .modal-footer #confirmAction').text("Rejoin Rider");
                    $('#editRiderModal #joining_date_group').removeClass("d-none");
                    $("#editRiderForm #rejoin_div_html").html(
                        "<input type='hidden' name='rejoin_rider_bit' value='1'>");
                    // $("#editRiderForm #rejoin_rider_div").removeClass("d-none");
                    // $('#editRiderModal #old_trax_id').val(trax_id);
                } else {
                    $('#editRiderModal .modal-title').text("Update Rider");
                    $('#editRiderModal .modal-footer #confirmAction').text("Update Rider");
                    $('#editRiderModal #joining_date_group').addClass("d-none");
                    $("#editRiderForm #rejoin_div_html").html("");
                }
                $('#editRiderModal').modal('show');
            }

            $('body').on('click', '.update_rider', function(e) {
                edit_Rider_function(this);

            });

            $("#UpdatePinModal #pin").inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'mask': "9999",
                'clearIncomplete': true,
            });

            $('body').on('click', '.update_pin_btn', function(e) {
                var employee_id = table.row($(this).parents('tr')).data().employee_id;
                var pin = table.row($(this).parents('tr')).data().pin;
                $('#UpdatePinModal #employee_id').val(employee_id);
                $('#UpdatePinModal #pin').val(pin);
                $('#UpdatePinModal').modal('show');
            });

            $('body').on('hidden.bs.modal', '#editRiderModal', function() {
                $('#editRiderModal #employee_id').val('');
                $('#rider_name').val('');
                $('#rider_cnic').val('');
                $('#rider_phone').val('');
                $('#rider_pin').val('');
                $('#address').val('');
                $('#city_list').val(null).trigger('change');
                $('#rider_type_list').val(null).trigger('change');
                $('#route_list').val(null).trigger('change');
                $('#main_category_list').val(null).trigger('change');
                $('#category_list').val(null).trigger('change');
                $('#category').val(null).trigger('change');


            });

            $('body').on('hidden.bs.modal', '#rejoinStaffModal', function() {
                $('#rejoinStaffForm #employee_id').val('');
                $('#rejoinStaffForm #joining_date').val('');
            });

            $("#rejoinStaffForm").validate({

                errorClass: "danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#rejoinStaffForm input,#rejoinStaffForm textarea,#rejoinStaffForm select')
                        .removeAttr('disabled');
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'Employee is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $('body').on('hidden.bs.modal', '#UpdatePinModal', function() {
                $('#UpdatePinModal #employee_id').val('');
                $('#pin').val('');
            });

            $('body').on('click', '.incentive', function(e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make Rider Incentive!',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Making Rider Incentive',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                                url: '{!! route('admin.human_resource.employee_directory.rider.incentive') !!}',
                                method: 'POST',
                                data: {
                                    'employee_id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('body').on('click', '.permanent', function(e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make Rider Permanent!',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Making Rider Permanent',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                                url: '{!! route('admin.human_resource.employee_directory.rider.permanent') !!}',
                                method: 'POST',
                                data: {
                                    'employee_id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {
                                if (data.status == 0) {
                                    // toastr.success(data.success, 'Success!', {
                                    //     positionClass: 'toast-bottom-center',
                                    //     containerId: 'toast-bottom-center'
                                    // });
                                    window.location.href = data.route + '?from=rider-incentive';

                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('body').on('click', '.blacklist', function(e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make Rider Blacklist!',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Making Rider Blaclist',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                                url: '{!! route('admin.human_resource.employee_directory.rider.blacklist') !!}',
                                method: 'POST',
                                data: {
                                    'employee_id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('body').on('click', '.deactivate', function(e) {
                var id = $(this).data('target-id');
                console.log(id);
                $('#LastWorkingDayModal .employee_id').val(id);
                $('#LastWorkingDayModal .employee_type').val(2);
                $('#LastWorkingDayModal').modal('show');

            });

            $('#search_origin').select2({
                placeholder: 'Search Hub (*)',
                width: '100%',
                allowClear: false,
                dropdownParent: $('#AssignHubModal'),
            }).on('change', function() {

                $('#select_message_error').text('');

            });



            $('body').on('click', '.assign_hub', function(e) {
                var id = $(this).data('target-id');
                $('#AssignHubModal .employee_type').val(1);
                $('#AssignHubModal .employee_id').val(id);
                $('#AssignHubModal').modal('show');
            });


            // $('body').on('click', '.deactivate_staff', function(e) {
            //     var id = $(this).data('target-id');
            //     $('#LastWorkingDayModal .employee_type').val(1);
            //     $('#LastWorkingDayModal .employee_id').val(id);
            //     $('#LastWorkingDayModal').modal('show');
            // });


            $('body').on('click', '.activate', function(e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make Rider Active!',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Making Rider Active',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        $.ajax({
                                url: '{!! route('admin.human_resource.employee_directory.rider.activate') !!}',
                                method: 'POST',
                                data: {
                                    'employee_id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {
                                if (data.status == 0) {
                                    // toastr.success(data.success, 'Success!', {
                                    //     positionClass: 'toast-bottom-center',
                                    //     containerId: 'toast-bottom-center'
                                    // });
                                    window.location.href = data.route + '?from=staff-profile';
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });



            $('body').on('click', '.convert_rider_to_staff', function(e) {
                var id = $(this).data('target-id');
                var employee_type = table.row($(this).parents('tr')).data().employee_type_id;
                if (employee_type == 2) {
                    $("#convertRiderForm #employee_id").val(id);
                    $("#convertRiderModal").modal('show');
                }
            });

            $('body').on('hidden.bs.modal', '#convertRiderModal', function() {
                $('#convertRiderForm #employee_id').val('');
                $('#convertRiderForm #department').val('').trigger('change');
                $('#convertRiderForm #designation').val('').trigger('change');
            });

            $('body').on('hidden.bs.modal', '#employeeRequiredInfoModal', function() {
                $('#approveStaffForm #employee_id').val('');
                $('#approveStaffForm #joining_date').val('');
                $('#approveStaffForm #replacement_last_working_day').val('');
                $('#approveStaffForm #replacement_employee_list').val('').trigger('change')
                $('#approveStaffForm #employee_nature_list').val('').trigger('change')
            });

            $('body').on('hidden.bs.modal', '#RiderRequiredInfoModal', function() {
                $('#approveRiderForm #employee_id').val('');
                $('#approveRiderForm #joining_date').val('');
            });

            $("#convertRiderForm").validate({
                errorClass: "danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes To Make Rider An Employee!',
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
                    }).then(function(confirm) {
                        if (confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'Converting Rider To Staff!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            form.submit();
                        }
                    });
                }
            });

            $("#approveStaffForm").validate({
                errorClass: "danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes To Approve Employee!',
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
                    }).then(function(confirm) {
                        if (confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'Employee is being Approved',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            form.submit();
                        }
                    });
                }
            });

            $("#approveRiderForm").validate({
                errorClass: "danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes To Approve Employee!',
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
                    }).then(function(confirm) {
                        if (confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'Employee is being Approved',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            form.submit();
                        }
                    });
                }
            });

            $('body').on('click', '.convert_intern_to_staff', function(e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes To Make Intern An Employee!',
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
                }).then(function(confirm) {
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Converting Intern To Staff!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                                url: '{!! route('admin.human_resource.employee_directory.staff.convert') !!}',
                                method: 'POST',
                                data: {
                                    'employee_id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('#search_filter_btn').on('click', function() {
                table.draw(true);
            });


        });
    </script>
@endsection
