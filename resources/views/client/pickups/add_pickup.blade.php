@extends('client.layout.master')
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
                                action="{{ route('cod.pickup.add') }}">
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

                                                <input type="text" name="pickup_date"
                                                    class="form-control pickadate bg-primary border-primary white rounded-right require_one pickup_date"
                                                    id="pickup_date" placeholder="Select Pickup Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="pickup_address_id" id="pickup_address_id" class="form-control select2" required
                                                data-rule-required="true" data-msg-required="Pickup Area is required">
                                                @foreach($pickup_addresses as $pickup_address)
                                                    <option value="{{ $pickup_address->id }}">{{ $pickup_address->city->name }} - {{ $pickup_address->pickup_address }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="preferred_time_range" id="preferred_time_range" class="form-control select2" required
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
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/simple-line-icons/style.min.css') }}">

@endsection
@section('js')
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#preferred_time_range').select2({
            width: '100%',
            placeholder: 'Select Preferred Time Range',
            allowClear: true,
            containerCssClass: 'text-center'
        });
        $('#pickup_address_id').append('<option value="">Select Pickup Address</option>').select2({
                width: '100%',
                placeholder: 'Select Pickup Address',
                allowClear: true,
                containerCssClass: 'text-center'
            });

        var from_date = $('#pickup_date').pickadate({
            firstDay: 1,
            // clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            min: '{{ Carbon\Carbon::today()}}',
            onSet: function(context) {
            }
        });
    });
</script>
@endsection
