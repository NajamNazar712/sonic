@extends('admin.layout.master')

@section('title', 'Team Lead Management')

@section('content')
    <h1 class="mb-1">
        Team Leads Management
    </h1>


    @include('admin.inc.messages')



    <input type="hidden" class="datepicker">
    <div class="modal fade" id="joiningDateModal" tabindex="-1" role="dialog" aria-labelledby="joiningDateModalLabel"
        aria-hidden="true">

    </div>

    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal"
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
    </div>

    <input type="hidden" id="number_of_available_agents_input">
    <div class="row justify-content-center">
        <div class="col-3" id="number_of_available_agents_div">
            <div class="card bg-gradient-directional-complaints_launched pull-up cursor-pointer">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="align-self-center">
                                <i class="icon-flag text-white font-large-2 float-left"></i>
                            </div>
                            <div class="media-body text-white text-right">
                                <h3 class="text-white">
                                    <p id="received_leads" class="d-inline">{{ count($number_of_available_agents) }}</p>
                                </h3>
                                <span>Online/ Available Agents </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
        <thead>
            <tr class="bg-primary white">
                {{--                                    <th class="border-primary border-darken-1"></th> --}}
                <th class="border-primary border-darken-1">S No.</th>
                <th class="border-primary border-darken-1">Employee ID</th>
                {{-- <th class="border-primary border-darken-1">Old Employee ID</th> --}}
                <th class="border-primary border-darken-1">Employee Name</th>

                <th class="border-primary border-darken-1">Hub</th>
                <th class="border-primary border-darken-1">City</th>
                <th class="border-primary border-darken-1">CNIC</th>
                <th class="border-primary border-darken-1">Phone Number</th>
                {{-- <th class="border-primary border-darken-1">Official Email</th> --}}
                <th class="border-primary border-darken-1">Employee Type</th>
                <th class="border-primary border-darken-1">Designation</th>
                <th class="border-primary border-darken-1">Department</th>
                {{-- <th class="border-primary border-darken-1">Zone</th> --}}
                {{-- <th class="border-primary border-darken-1">Replacement Trax ID</th>
                <th class="border-primary border-darken-1">Replacement Name</th>
                <th class="border-primary border-darken-1">Request/Document Status</th> --}}
                <th class="border-primary border-darken-1">Employee Status</th>
                <th class="border-primary border-darken-1">Requested At</th>
                {{-- <th class="border-primary border-darken-1">Joining Date</th> --}}
                <th class="border-primary border-darken-1">Last Working Date</th>
                {{-- <th class="border-primary border-darken-1">Remarks</th> --}}
                <th class="border-primary border-darken-1">Confirmation Status</th>
                <th class="border-primary border-darken-1">Available</th>
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
        .error-message {
            color: red
        }

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
                var Ids = [];
                var delId = $(this).data('del-id');
                $("#multiple_delete:checked").each(function() {
                    Ids.push($(this).val());
                });

                var deleteDaysContainer = $('#delete_days');
                var deleteIcon = $(this);
                $.ajax({
                        url: '{{ route('admin.team_lead.delete_additional_days') }}',
                        type: 'GET',
                        data: {
                            'ids': Ids,
                        }
                    })
                    .done(function(data) {

                        var object = data
                            .object; // Assuming 'data.object' contains the array or object you want to get the length of

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

                        window.location.href = "{{ route('admin.team_lead.index') }}";

                    })
                    .fail(function(xhr) {
                        toastr.error('Please Select',
                            'Error  !', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
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


            $('body').on('click', '#save_additional_days', function(e) {
                var id = $('#employee_id_d').val();
                var dates = $('#add_days_employee').val();
                e.preventDefault();
                $.ajax({
                        url: '{!! route('admin.team_lead.add_additional_days') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            employee_id: id,
                            add_additional_days: dates,
                        },
                        dataType: 'json',
                    })
                    .done(function(response) {
                        if (response.status == 0) {
                            window.location.href = "{{ route('admin.team_lead.index') }}";
                        } else if (response.status == 2) {
                            $('.error-message').text('Please Select Date');
                        }
                    })
                    .fail(function(xhr, status, error) {
                        // Handle the error response from the server
                        alert('Error: ' + error);
                        console.log(xhr.responseText); // You can inspect the error response here
                    });
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
                            // head.push('Employee Old Trax ID');
                            head.push('Employee Name');
                            head.push('Hub');
                            head.push('City');
                            head.push('CNIC');
                            head.push('Phone No.');
                            // head.push('Official Email');
                            head.push('Employee Type');
                            head.push('Rider Main Category');
                            head.push('Designation');
                            head.push('Department Name');
                            head.push('Line Manager');
                            // head.push('IBAN No.');
                            // head.push('Zone Name');
                            // head.push('Repalcement Tax ID');
                            // head.push('Repalcement Name');
                            // head.push('Request/Document Status');
                            head.push('Employee Status');
                            head.push('Requested At');
                            // head.push('Joining Date');
                            head.push('Last Working Date');
                            // head.push('Remarks');
                            head.push('Confirmation Status');
                            head.push('Available');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                // row.push(values.old_trax_id);
                                row.push(values.employee_name);
                                row.push(values.employee_hub);
                                row.push(values.city);
                                row.push(values.cnic);
                                row.push(values.phone_number);
                                // row.push(values.official_email);
                                row.push(values.employee_type);
                                row.push(values.rider_main_category);
                                row.push(values.employee_designation);
                                row.push(values.department_name);
                                row.push(values.line_manager);
                                // row.push(values.iban);
                                // row.push(values.zone_name);
                                // row.push(values.r_trax_id);
                                // row.push(values.r_name);
                                // row.push(values.request_status);
                                row.push(values.status);
                                row.push(values.requested_at);
                                // row.push(values.joining_date);
                                row.push(values.last_working_date);
                                // row.push(values.remarks);
                                row.push(values.confirmation_status);
                                row.push(values.attendance_date);

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
                        d.number_of_available_agents_input = $('#number_of_available_agents_input')
                            .val();

                    }
                },
                order: [
                    [2, 'desc']
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
                    // {
                    //     data: 'old_trax_id',
                    //     name: 'employees.old_trax_id',
                    //     class: 'align-middle old_trax_id'
                    // },


                    {
                        data: 'employee_name',
                        name: 'employees.name',
                        class: 'align-middle employee_name'
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
                    // {
                    //     data: 'official_email',
                    //     name: 'employees.official_email',
                    //     class: 'align-middle official_email'
                    // },
                    {
                        data: 'employee_type',
                        name: 'et.name',
                        class: 'align-middle employee_type'
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

                    // {
                    //     data: 'zone_name',
                    //     name: 'ez.id',
                    //     class: 'align-middle zone_name'
                    // },
                    // {
                    //     data: 'r_trax_id',
                    //     name: 'r_emp.trax_id',
                    //     class: 'align-middle r_trax_id'
                    // },
                    // {
                    //     data: 'r_name',
                    //     name: 'r_emp.name',
                    //     class: 'align-middle r_name'
                    // },
                    // {
                    //     data: 'request_status',
                    //     name: 'ers.name',
                    //     class: 'align-middle request_status'
                    // },
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
                    // {
                    //     data: 'joining_date',
                    //     name: 'employees.joining_date',
                    //     class: 'align-middle joining_date'
                    // },
                    {
                        data: 'last_working_date',
                        name: 'employees.last_working_date',
                        class: 'align-middle last_working_date'
                    },
                    // {
                    //     data: 'remarks',
                    //     name: 'employees.remarks',
                    //     class: 'align-middle remarks'
                    // },
                    {
                        data: 'confirmation_status',
                        name: 'employees.confirmation_status',
                        class: 'align-middle confirmation_status'
                    },

                    {
                        data: 'attendance_date',
                        name: 'ea.attendance_date',
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

                    this.api().table().columns.adjust();
                }
            });



            $("#filter_line_manager_btn").on('click', function() {
                $("#filter_line_manager").val(1);
                table.draw();
            });



            $('#number_of_available_agents_div').on('click', function() {
                $('#number_of_available_agents_input').val(2);


                table.draw();
            })


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

            var employeeId = null;


            $('body').on('click', '.add_additional_days', function() {
                var employeeId = $(this).attr('data-id');
                var employee = $(this).attr('data-ename');

                // Add the AJAX call here
                $.ajax({
                    url: '{!! route('admin.team_lead.get_updated_day') !!}',
                    type: 'GET', // Change to 'GET' or 'POST' depending on your server-side setup
                    data: {
                        employee_id: employeeId, // Sending the employeeId as data to Laravel
                        // Add any other data you want to send to Laravel here
                    },
                    dataType: 'json',
                }).done(function(response) {
                    // This function will be called when the AJAX request is successful
                    // Loop through the data and dynamically create the HTML content
                    var modalContent = `
        
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="joiningDateModalLabel">Add Days
                    <strong>(${employee})</strong>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="myForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" value="${employeeId}" name="employee_id" id="employee_id_d">
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <fieldset class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                    </span>
                                </div>
                                <input type="text" data-rule-required="true"
                                    data-msg-required="This Field is required"
                                    class="form-control bg-primary border-primary white rounded-right pickadate datepicker"
                                    id="add_days_employee" placeholder="Add Days" name="add_additional_days">
                                    </fieldset>
                                    <div class="error-message"></div>


                        </div>
                    </div>
                </div>
`;

                    if (response.employee_additional_days && response.employee_additional_days
                        .length > 0) {
                        modalContent += `
        <div class="card days_show">
            <div class="card-header">
                <strong>
                    <h5 class="card-title" style="font-weight: bold; text-decoration: underline;">Working Days</h5>
                </strong>
            </div>
            <div class="card-body" id="delete_days">
    `;
                        $.each(response.employee_additional_days, function(index,
                            employee_additional_day) {
                            var workingDay = new Date(employee_additional_day.working_days);
                            var formattedWorkingDay = workingDay.toLocaleDateString(
                                'en-US', {
                                    weekday: 'long'
                                });

                            modalContent += `
        <div class="parent-element">
            
           
                <div class="card-title" id="used_dates">
                    <input type="checkbox" value="${employee_additional_day.id}" id="multiple_delete">
                ${employee_additional_day.working_days} (${formattedWorkingDay})
            </div>
        </div>
        `;
                        });

                        modalContent += `
                        <a class="btn btn-sm btn-danger float-left delete-icon" title="Delete">
                Delete
                </a>
            </div>
        </div>
    `;
                    }

                    modalContent += `
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id='save_additional_days'>Save</button>
                </div>
            </form>
        </div>
    </div>
`;

                    // Set the dynamically created content to the modal body
                    $('#joiningDateModal').html(modalContent);
                    $('#joiningDateModal').modal('show');

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

                    for (var i = 0; i < response.employee_additional_days.length; i++) {
                        var workingDayObj = response.employee_additional_days[i];
                        var workingDay = workingDayObj.working_days;
                        var picker = replacement_last_working_day.pickadate('picker');
                        picker.set('disable', [new Date(workingDay)]);
                    }
                }).fail(function(xhr, status, error) {

                });
            });

            var route = '{!! route('admin.tracking.index') !!}';

            $('body').on('click', '.assigned_shipment', function() {

                $('#shipments_modal').modal('show');

                $('#shipments_modal .modal-body').html('');


                var shipment = $(this).attr('data-assigned')

                var shipmentArray = shipment.split(',');

                var anchorTagsWithUnderlines = '';

                for (var i = 0; i < shipmentArray.length; i++) {
                    var shipmentNumber = shipmentArray[i].trim();
                    anchorTagsWithUnderlines += '<p><a href=' + route + '?tracking_number=' +
                        shipmentNumber + ' style="text-decoration: underline;">' + shipmentNumber +
                        '</a></p>';
                    shipmentNumber + '</a>';

                    if (i < shipmentArray.length - 1) {
                        anchorTagsWithUnderlines += ', ';
                    }
                }

                $('#shipments_modal .modal-body').html(anchorTagsWithUnderlines);


            })

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


            $('#search_filter_btn').on('click', function() {
                table.draw(true);
            });


        });
    </script>
@endsection
