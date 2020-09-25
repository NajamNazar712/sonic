@extends('admin.layout.master')

@section('title', 'Update ' . $runner->name . ' On Route')

@section('content')
    <h1 class="mb-1">
        Update {{$runner->name}} On Route
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row justify-content-center mb-1">
                    <div class="col-4">
                        <table class="table table-bordered datatable" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="">
                                <th class="align-middle text-center" style="width: 20%">S No.</th>
                                <th class="align-middle text-center">Junctions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($junctions as $index => $junction)
                                <tr>
                                    <td class="align-middle text-center">{{$junction->order}}</td>
                                    <td class="align-middle text-center">{{$junction->city_name}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <form id="runner_route_form" class="form-horizontal" action="{{ route('admin.runner.edit.submit') }}" method="POST" novalidate="novalidate">
                    @csrf
                    <input type="hidden" name="id" value="{{$runner_detail->id}}">
                    <input type="hidden" name="status" id="status" value="">
                    <div class="row justify-content-center">
                        <div class="col form-group">
                            <input type="text" class="form-control" name="driver_name" id="driver_name" placeholder="Driver Name*" value="{{$runner_detail->driver_name}}" readonly>
                        </div>
                        <div class="col form-group">
                            <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" placeholder="Vehicle Number*" value="{{$runner_detail->vehicle_no}}" readonly>
                        </div>
                        <div class="col form-group">
                            <input type="text" class="form-control phone" name="contact_number" id="contact_number" placeholder="Contact Number*" value="{{$runner_detail->contact_no}}" readonly>
                        </div>
                    </div>
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Hub</th>
                            <th class="border-primary border-darken-1">Departure Date Time</th>
                            <th class="border-primary border-darken-1">Arrival Date Time</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="row justify-content-center mt-1">
                        <button type="button" class="btn btn-outline-success mr-1" title="Add more junctions" id="add_row"><i class="la la-plus"></i></button>
                    </div>
                    <div class="row justify-content-center mt-2">
                        <div class="col-3 text-center">
                            <button id="update" type="submit" class="btn btn-outline-primary round btn-min-width mr-1 mb-1">Update</button>
                        </div>
                        <div class="col-3 text-center">
                            <button id="completed" type="submit" class="btn btn-primary round btn-min-width mr-1 mb-1" disabled>Completed</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            var old_date_limit = '{{ Carbon\Carbon::now()->toDateString() }}';
            $(".phone").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                paging: false,
                ordering:false,
                sorting:false,
                bInfo:false,
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    { name: 'origin', class: 'align-middle user_name'},
                    { name: 'hub', class: 'align-middle hub'},
                    { name: 'departure_time', class: 'align-middle departure_time'},
                    { name: 'arrival_time', class: 'align-middle arrival_time'}

                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
            });
            row = 1;
            var junctions = @json($junctions);
            var total_rows = junctions.length - 1;
            var cities = [];
            var last_junction_id = @json($last_junction);

            var runner_details_time = @json($runner_detail_time)

            $.each(runner_details_time,function (key,value) {
                var new_origin = '<div class="form-group input-group"><select name="origin[' + row + ']" id="origin_' + row + '" class="select2 form-control" data-rule-required="true" data-msg-required="Origin is required">' +
                    '<option value="'+ value.origin +'" selected>'+ value.origin_name +'</option></select></div>';
                var new_destination = '<div class="form-group input-group"><select name="destination[' + row + ']" id="destination_' + row + '" class="select2 form-control" data-rule-required="true" data-msg-required="Destination is required">' +
                    '<option value="'+ value.destination +'" selected>'+ value.destination_name +'</option></select></div>';
                var departure_time = '<div class="row"><div class="col form-group input-group"><input type="text" name="departure_date[' + row + ']" id="departure_date_' + row + '" class="form-control rounded-right pickadate date" data-value="'+ value.departure_date +'" placeholder="Departure Date*" data-rule-required="true" data-msg-required="Departure Date is required" readonly></div>' +
                    '<div class="col form-group input-group"><input type="text" name="departure_time[' + row + ']" id="departure_time_' + row + '" class="form-control rounded-right time" value="'+ value.departure_time +'" placeholder="Departure Time*" data-rule-required="true" data-msg-required="Departure Time is required" readonly></div></div>';
                var arrival_time = '<div class="row"><div class="col form-group input-group"><input type="text" name="arrival_date[' + row + ']" id="arrival_date_' + row + '" class="form-control rounded-right pickadate date" data-value="'+ value.arrival_date +'" placeholder="Arrival Date*" data-rule-required="true" data-msg-required="Arrival Date is required" readonly></div>' +
                    '<div class="col form-group input-group"><input type="text" name="arrival_time[' + row + ']" id="arrival_time_' + row + '" class="form-control rounded-right time" value="'+ value.arrival_time +'" placeholder="Arrival Time*" data-rule-required="true" data-msg-required="Arrival Time is required" readonly></div></div>';
                if(key == 0){
                    cities.push(value.origin);
                }
                cities.push(value.destination);
                table.row.add([1, new_origin, new_destination, departure_time, arrival_time]);
                table.draw();

                $('#origin_' + row).select2({
                    width: '100%',
                    placeholder: 'Select Origin',
                });
                $('#destination_' + row).select2({
                    width: '100%',
                    placeholder: 'Select Destination',
                });
                $('.date').pickadate({
                    firstDay: 1,
                    clear: '',
                    max: new Date(old_date_limit),
                    format:'dd mmmm, yyyy',
                    selectYears: true,
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd 00:00:00',
                    hiddenSuffix: '_formatted',
                });
                $('.time').inputmask("hh:mm:ss", {
                    placeholder: "--:--:--",
                    insertMode: false,
                    showMaskOnHover: false,
                    clearIncomplete : true,
                    hourFormat: "24"
                });
                if(row == total_rows){
                    $('#add_row').attr('disabled', true);
                }
                row++;
            });
            var old_row = row;
            $('#add_row').on('click', function(){
                old_row = row - 1;
                old_origin = $('#origin_' + old_row).val();
                old_destination = $('#destination_' + old_row).val();
                old_destination_name = $('#destination_' + old_row + ' option:selected').text();
                if(old_destination !== ''){
                    var new_origin = '<div class="form-group input-group"><select name="origin[' + row + ']" id="origin_' + row + '" class="select2 form-control" data-rule-required="true" data-msg-required="Origin is required">' +
                        '<option value="'+ old_destination +'">'+ old_destination_name +'</option></select></div>';
                    var new_destination = '<div class="form-group input-group"><select name="destination[' + row + ']" id="destination_' + row + '" class="select2 form-control" data-rule-required="true" data-msg-required="Destination is required"></select></div>';
                    var departure_time = '<div class="row"><div class="col form-group input-group"><input type="text" name="departure_date[' + row + ']" id="departure_date_' + row + '" class="form-control rounded-right pickadate date" value="" placeholder="Departure Date*" data-rule-required="true" data-msg-required="Departure Date is required"></div>' +
                        '<div class="col form-group input-group"><input type="text" name="departure_time[' + row + ']" id="departure_time_' + row + '" class="form-control rounded-right time" value="" placeholder="Departure Time*" data-rule-required="true" data-msg-required="Departure Time is required"></div></div>';
                    var arrival_time = '<div class="row"><div class="col form-group input-group"><input type="text" name="arrival_date[' + row + ']" id="arrival_date_' + row + '" class="form-control rounded-right pickadate date" value="" placeholder="Arrival Date*" data-rule-required="true" data-msg-required="Arrival Date is required"></div>' +
                        '<div class="col form-group input-group"><input type="text" name="arrival_time[' + row + ']" id="arrival_time_' + row + '" class="form-control rounded-right time" value="" placeholder="Arrival Time*" data-rule-required="true" data-msg-required="Arrival Time is required"></div></div>';
                    cities.push(parseInt(old_destination));
                    table.row.add([1, new_origin, new_destination, departure_time, arrival_time]);
                    table.draw();
                    $.each(junctions,function (key,value) {
                        var city_id = parseInt(value.city_id);

                        var index = $.inArray(city_id, cities);

                        if(index === -1){
                            var newOption =  "<option value="+city_id+">"+value.city_name+"</option>";
                            $('#destination_' + row).append(newOption).trigger('change');
                            $('#destination_' + row).val('').trigger('change');
                        }

                    });
                    $('#origin_' + row).select2({
                        width: '100%',
                        placeholder: 'Select Origin',
                    });
                    $('#destination_' + row).prepend('<option value="" selected="selected"></option>').select2({
                        width: '100%',
                        placeholder: 'Select Destination',
                    }).bind('change', function () {
                        var id = parseInt($(this).val());
                        var last_junc = parseInt(last_junction_id);
                        if(id == last_junc){
                            $('#add_row').attr('disabled', true);
                            $('#completed').attr('disabled', false);
                        }
                        else{
                            $('#add_row').attr('disabled', false);
                            $('#completed').attr('disabled', true);
                        }
                    });
                    $('.date').pickadate({
                        firstDay: 1,
                        clear: '',
                        max: new Date(old_date_limit),
                        format:'dd mmmm, yyyy',
                        selectYears: true,
                        selectMonths: true,
                        formatSubmit: 'yyyy-mm-dd 00:00:00',
                        hiddenSuffix: '_formatted',
                    });
                    $('.time').inputmask("hh:mm:ss", {
                        placeholder: "--:--:--",
                        insertMode: false,
                        showMaskOnHover: false,
                        clearIncomplete : true,
                        hourFormat: "24"
                    });
                    if(row == total_rows){
                        $('#add_row').attr('disabled', true);
                    }
                    row++;
                }
                else{
                    var error = "Previous Destination is required!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });
            $('#update').on('click',function(){
                $('#status').val(0);
            });
            $('#completed').on('click',function(){
                $('#status').val(1);
            });
            $('#runner_route_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    var msg = "";
                    if($('#status').val() == 1){
                        msg = "Runner On Route is being marked as completed!"
                    }else{
                        msg = 'Runner On Route is being updated!';
                    }
                    swal({
                        title: 'Please Wait!',
                        text: msg,
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    for(var i=1; i<old_row; i++){
                        console.log(i);
                        $('#origin_' + i).prop('disabled', false);
                        $('#destination_' + i).prop('disabled', false);
                        $('departure_time_' + i).attr('readonly', false);
                        $('arrival_time_' + i).attr('disabled', false);
                    }

                    form.submit();
                }
            });
        });

    </script>
@endsection