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
                            <form method="post" id="add_pickup_request" action="{{ route('cod.pickup.add') }}">
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

                                                <input type="text" name="pickup_date" class="form-control pickadate bg-primary border-primary white rounded-right require_one pickup_date" id="pickup_date" placeholder="Select Pickup Date" data-rule-required="true" data-msg-required="Pickup Date is Required">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="pickup_address_id" id="pickup_address_id" class="form-control select2" required
                                                data-rule-required="true" data-msg-required="Pickup Address is required">
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
                                                data-rule-required="true" data-msg-required="Preferred Time is required">
                                                @foreach($time_ranges as $time)
                                                    <option value="{{ $time->id }}">{{ $time->name }}</option>
                                                @endforeach
                                           </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="pickup_type_id" id="pickup_type_id" class="form-control select2" required
                                                data-rule-required="true" data-msg-required="Shipment Type is required">
                                                @foreach ($pickup_types as $pickup_type)
                                                    <option value="{{ $pickup_type->id }}" >{{ $pickup_type->name }}</option>
                                                @endforeach
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

                                            <input type="text" name="estimated_weight"  id="estimated_weight" class="form-control text-center">
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
                                            <input type="text" name="shipments_count" id="shipments_count" class="form-control text-center"
                                                data-rule-required="true" data-msg-required="No of Shipments is required">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <textarea name="remarks" class="form-control" placeholder="Remarks here.." rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end align-items-center">
                                    <div class="col-4">
                                        <div id="pickup_div" class="form-group text-right p-3">
                                            <label class="d-block"><strong>Regular Pickup</strong></label>
                                            <input type="checkbox" name="pickup" class="switch hidden" id="pickup">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="add">Add</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#estimated_weight').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
        });
        $('#shipments_count').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
        });
        $('#preferred_time_range').prepend('<option value="" selected="selected">Select Preferred Time Range</option>').select2({
            width: '100%',
            placeholder: 'Select Preferred Time Range',
        });

        $('#pickup_address_id').prepend('<option value="" selected="selected">Select Pickup Address</option>').select2({
            width: '100%',
            placeholder: 'Select Pickup Address',
        });
        $('#pickup_type_id').prepend('<option value="" selected="selected">Select Pickup Shipment Type</option>').select2({
            width: '100%',
            placeholder: 'Select Pickup Shipment Type',
        });
        $('#pickup').checkboxpicker();
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

        $('#add_pickup_request').validate({
            errorClass: 'danger',
            successClass: 'success',
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                $('#add_pickup_request button#add').prop('disabled', true);
                swal({
                    title: 'Please Wait!',
                    text: 'Pickup request is being added!',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                });
                form.submit();
            }
        });
    });
</script>
@endsection
