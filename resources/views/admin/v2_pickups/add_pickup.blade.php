@extends('admin.layout.master')
@section('title', 'Pickup Request')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Pickup Request
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form method="post" id="crm_agent_assign"
                                action="{{ route('admin.settings.auto_assigning.submit') }}">
                                @csrf

                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                                    <span
                                                        class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                        <span class="la la-calendar-o"></span>
                                                    </span>
                                                </div>

                                                <input type="text" name="search_date_from"
                                                    class="form-control pickadate bg-primary border-primary white rounded-right require_one search_date_from"
                                                    id="search_date_from" placeholder="Select Pickup Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="shippers" id="shippers" class="form-control select2" required
                                                data-rule-required="true" data-msg-required="Shippers is Required">
                                                <option value=""> Select Shippers </option>
                                                @foreach ($shippers as $shipper)
                                                    <option value="{{ $shipper->id }}"> {{ $shipper->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="cities" id="cities" class="form-control select2" required
                                                data-rule-required="true" data-msg-required="City is Required">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="search_area" id="search_area" class="form-control select2" required
                                                data-rule-required="true" data-msg-required="Pickup Area is required">
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="Preferred_time_range" id="Preferred_time_range" class="form-control select2" required
                                                data-rule-required="true" data-msg-required="Preferred Time Range is required">
                                                <option value="">Selected Preferred Time Range</option>
                                                <option value="9-10">9-10</option>
                                                <option value="10-11">10-11</option>
                                                <option value="11-12">11-12</option>
                                           </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <!-- Your content here -->
                                </div>
                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="admin_id" id="agent_id" class="form-control select2" required
                                                data-rule-required="true" data-msg-required="Agent is required">
                                                <option value="" class="text-center"> Shipment Type </option>
                                                {{-- @foreach ($agents as $agent)
                                                 <option value="{{ $agent->id }}" > {{ $agent->name }} </option>
                                             @endforeach --}}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group input-group">

                                            <div class="input-group-prepend">

                                                <span class="input-group-text text-dark border-primary  rounded-left">
                                                    <span> Estimated Weight </span>
                                                </span>
                                            </div>

                                            <input type="text" name="remarks" class="form-control text-center"
                                                placeholder="" required data-rule-required="true"
                                                data-msg-required="Agent is required">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">

                                                <span class="input-group-text text-dark border-primary  rounded-left">
                                                    <span> No of Shipments </span>
                                                </span>
                                            </div>
                                            <input type="text" name="remarks" class="form-control text-center" required
                                                data-rule-required="true" data-msg-required="Agent is required">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="text" name="remarks" class="form-control text-center"
                                                placeholder="Remarks" required data-rule-required="true"
                                                data-msg-required="Agent is required">
                                        </div>
                                    </div>
                                    {{-- Note: I get this Regular pickup Button Code From this route https://sonic.test/admin/shipment/book 
                                        for js code in future--}}
                                    <div class="col-3">
                                        <div id="pickup_div" class="form-group text-right p-1 ">
                                            <label class="d-block">Regular Pickup</label>
                                            <input type="checkbox" name="pickup" class="switch hidden" id="pickup">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" id="assign_agentSubmit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/simple-line-icons/style.min.css') }}">

@endsection
@section('js')
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/js/scripts/tables/datatables/datatable-basic.js') }}" type="text/javascript">
</script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
</script>
<script src="{{ asset('app-assets/vendors/js/pagination/moment.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#Preferred_time_range').select2({
            width: '100%',
            placeholder: 'Select Preferred Time Range',
            allowClear: true,
            containerCssClass: 'text-center'
        });
    $('#search_area').append('<option value="">Select Area</option>').select2({
            width: '100%',
            placeholder: 'Select Area',
            allowClear: true,
            containerCssClass: 'text-center'
        });
        $('#shippers').append('<option value="">Select Shippers</option>').select2({
            width: '100%',
            placeholder: 'Select Shippers',
            allowClear: true,
            containerCssClass: 'text-center'
        });
        $('#shippers').append('<option value="">Select Shippers</option>').select2({
            width: '100%',
            placeholder: 'Select Shippers',
            allowClear: true,
            containerCssClass: 'text-center'
        }).bind('change', function() {;
        var shipperId = $(this).val();
        // alert(shipperId);
        $('#cities').empty();
        $('#cities').append('<option value="">Select Cities</option>').select2({
            width: '100%',
            placeholder: 'Select Cities',
            allowClear: true,
            containerCssClass: 'text-center'
        });
        $('#cities').prop('disabled', false);
        if (shipperId) {
        $.ajax({
            url: '{{ route('admin.v2_pickups.pending.get_shipper_cities') }}',
            type: 'GET',
            data: {
                shipperId: shipperId
            },
            dataType: 'json',
            success: function(response) {
            $.each(response, function(index, cities) {
                $('#cities').append('<option value="' + cities.id + '">' + cities.name + '</option>');
            });
            },
            error: function(xhr, status, error) {
            console.error(error);
            }
        });
        }
        });
        $('#cities').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'Select Cities',
            allowClear: true,
            containerCssClass: 'text-center'
        }).bind('change', function() {
        var cityId = $(this).val();
        $('#search_area').empty();
        $('#search_area').append('<option value="">Select Pickup Address</option>').select2({
            width: '100%',
            placeholder: 'Select Pickup Address',
            allowClear: true,
            containerCssClass: 'text-center'
        });
        // Make the search_area select element enabled
        $('#search_area').prop('disabled', false);
        if (cityId) {
        $.ajax({
            url: '{{ route('admin.v2_pickups.pending.get_pickup_address') }}',
            type: 'GET',
            data: {
            city_id: cityId
            },
            dataType: 'json',
            success: function(response) {
            $.each(response, function(index, pickup_address) {
                $('#search_area').append('<option value="' + pickup_address.id + '">' + pickup_address.pickup_address + '</option>');
            });
            },
            error: function(xhr, status, error) {
            console.error(error);
            }
        });
        }
        });
        $('.select2-selection__placeholder').addClass('text-center');
        var from_date = $('#search_date_from').pickadate({
            firstDay: 1,
            // clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            onSet: function(context) {
                if (context.select) {
                    var old_date_formatted = $('input[name="search_date_from_formatted"]').val();
                    var currentDate = moment(old_date_formatted);

                    var to_date_formatted = $('input[name="search_date_to_formatted"]').val();
                    var toDate = moment(to_date_formatted);

                    if (currentDate.format('x') > toDate.format('x')) {
                        to_date.pickadate('picker').clear();
                    }

                    var afterDate = currentDate.add(31, 'days');
                    to_date.pickadate('picker').set({
                        'max': afterDate.toDate()
                    }, {
                        muted: true
                    });

                    from_date.valid();
                }
            }
        });
    });
</script>
@endsection
