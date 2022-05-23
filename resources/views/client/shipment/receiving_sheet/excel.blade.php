@extends('client.layout.master')

@section('title', 'Receiving Sheet Excel Shipment(s)')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Receiving Sheet Excel Shipment(s)
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('client.inc.messages')

                            @if(isset($shipment_errors) && count($shipment_errors) > 0)
                                @foreach($shipment_errors as $shipment_error)
                                    <div class="alert alert-danger">
                                        {{$shipment_error}}
                                    </div>
                                @endforeach
                            @endif
                            <form id="receiving_sheet_excel_form" class="form-horizontal" method="POST" action="{{ route('cod.shipment.receiving_sheet.excel.store') }}" novalidate="novalidate" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="row align-items-center justify-content-center mb-2">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="form-group">
                                            <select name="pickup_address" class="select2" id="pickup_address" data-rule-required="true" data-msg-required="Pickup Address is required">

                                                @php ($default_pickup_address = FALSE)

                                                @foreach($pickup_addresses as $pickup_address)
                                                    @if ($pickup_address['hidden'] == 0 && $pickup_address['status'] == 1)
                                                        @if ($pickup_address['default_address'] == 1)
                                                            @php ($default_pickup_address = TRUE)

                                                            <option value="{{ $pickup_address['id'] }}" selected >{{ $pickup_address['poc'] }}: {{ $pickup_address['pickup_address'] }}, {{ $pickup_address['city']['name'] }}</option>
                                                        @else
                                                            <option value="{{ $pickup_address['id'] }}">{{ $pickup_address['poc'] }}: {{ $pickup_address['pickup_address'] }}, {{ $pickup_address['city']['name'] }}</option>
                                                        @endif
                                                    @endif
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group text-left">
                                            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                        </div>
                                    </div>



                                    <div class="col-2">
                                        <div class="form-group text-right">

                                            <a href="{{ asset('file/Trax Receiving Sheet Excel Template.xlsx') }}?v=24_05_2022" class="btn btn-primary btn-lg"><i class="la la-download"></i> Template Download</a>

                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row">
                                <div class="col-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr role="row" class="bg-primary white text-center">
                                                <th colspan="2" class="border-primary border-darken-1">Pickup Addresses</th>
                                            </tr>
                                            <tr role="row" class="bg-primary bg-lighten-1 white">
                                                <th class="text-center border-primary border-lighten-2">ID</th>
                                                <th class="border-primary border-lighten-2">Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if ($pickup_addresses->count())
                                            @foreach ($pickup_addresses as $pickup_address)
                                                <tr role="row">
                                                    <td class="text-center">{{ $pickup_address->id }}</td>
                                                    <td>{{ $pickup_address->pickup_address }}, {{ $pickup_address->city->name }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr role="row">
                                                <td colspan="2" class="text-center">No Active Pickup Addresses</td>
                                            </tr>
                                        @endif
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#pickup_address').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select pickup address*'
            });

            $.validator.addMethod('maxsize', function(value, element, params) {
                if ($(element).attr('type') === 'file') {
                    if (element.files && element.files.length) {
                        console.log(element.files);
                        for (var c = 0; c < element.files.length; c++) {
                            if (element.files[c].size > params) {
                                return false;
                            }
                        }
                    }
                }

                return true;
            }, $.validator.format("File Size must not exceed {0} bytes."));

            $('#receiving_sheet_excel_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Your Receiving Sheet is being created!',
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