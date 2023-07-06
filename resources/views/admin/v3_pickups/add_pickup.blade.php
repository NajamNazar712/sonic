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
                            <form method="post" id="add_pickup_request" class="text-center" action="{{ route('admin.v3_pickups.add') }}">
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
                                                    id="pickup_date" placeholder="Select Pickup Date" data-rule-required="true" data-msg-required="Pickup Date is Required">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="shipper_id" id="shippers" class="form-control select2" data-rule-required="true" data-msg-required="Shippers is Required">
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
                                            <select name="pickup_address_id" id="pickup_address_id" class="form-control select2" data-rule-required="true" data-msg-required="Pickup Area is required">
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select name="preferred_time_range" id="preferred_time_range" class="form-control select2" data-rule-required="true" data-msg-required="Preferred Time Range is required">
                                                <option value="">Selected Preferred Time Range</option>
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
                                            <select name="pickup_type_id" id="pickup_type_id" class="form-control select2" data-rule-required="true" data-msg-required="Pickup Type is required">
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

                                            <input type="text" id="estimated_weight" name="estimated_weight" class="form-control text-center" placeholder="Estimated Weight">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">

                                                <span class="input-group-text text-dark border-primary rounded-left">
                                                    <span> No of Shipments </span>
                                                </span>
                                            </div>
                                            <input type="text" id="shipments_count" name="shipments_count" class="form-control" data-rule-required="true" data-msg-required="No. of Shipments is required">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end align-items-center vh-100">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <textarea type="text" name="remarks" class="form-control" placeholder="Remarks"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div id="pickup_div" class="form-group text-right p-1 ">
                                            <label class="d-block">Regular Pickup</label>
                                            <input type="checkbox" name="pickup" class="switch hidden" id="pickup">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary btn-lg" id="add">Add</button>
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript"></script>
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

        $('#pickup_type_id').prepend('<option value="" selected="selected">Select Pickup Shipment Type</option>').select2({
            width: '100%',
            placeholder: 'Select Pickup Shipment Type',
        });
        $('#pickup_address_id').prepend('<option value="" selected="selected">Select Pickup Address</option>').select2({
            width: '100%',
            placeholder: 'Select Pickup Address',
        });

        $('#shippers').prepend('<option value="" selected="selected">Select Shippers</option>').select2({
            width: '100%',
            placeholder: 'Select Shipper',
        }).
        bind('select2:select', function() {
            var shipperId = $(this).val();

            if (shipperId) {
                $('#pickup_address_id').empty();

                $('#pickup_address_id').prop('disabled', false);
                $.ajax({
                    url: '{{ route('admin.v3_pickups.get_pickup_address') }}',
                    method: 'POST',
                    data: {
                        'shipper_id': shipperId,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if (data.status == 0) {
                        $.each(data.pickup_addresses,function(key,value) {
                            var name = value.city.name + ' - ' + value.pickup_address;
                            var pickup = new Option(name, value.id, false, false);
                            $('#pickup_address_id').append(pickup).trigger('change');
                        });
                    } else {
                        toastr.error('No pickup address found!', 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });
            }
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
