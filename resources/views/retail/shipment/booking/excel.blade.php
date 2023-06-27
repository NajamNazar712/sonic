@extends('retail.layout.master')

@section('title', 'Book Excel Shipment(s)')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Book Excel Shipment(s)
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('retail.inc.messages')

                            <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('retail.shipment.book.excel_store') }}" novalidate="novalidate" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="row align-items-center justify-content-center">
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed">
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group text-left">
                                            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                        </div>
                                    </div>

                                    <div class="col ml-auto">
                                        <div class="form-group text-right">
                                            <a href="{{ asset('file/Trax Book Retail Shipment Template.xlsx?v=1') }}" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row mt-2">
                                <div class="col">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Shipments</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($products as $product)
                                            <tr role="row">
                                                <td class="text-center">{{ $product->id }}</td>
                                                <td>{{ $product->product_name }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Shipment Categories</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($business_categories as $business_category)
                                            <tr role="row">
                                                <td class="text-center">{{ $business_category->id }}</td>
                                                <td>{{ $business_category->name }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Product</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($shipping_modes as $shipping_mode)
                                            <tr role="row">
                                                <td class="text-center">{{ $shipping_mode->id }}</td>
                                                <td>{{ $shipping_mode->name }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role=   "row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Insurance Offered</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr role="row">
                                            <td class="text-center">No</td>
                                            <td class="text-center">Yes</td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Volumetric Weight</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr role="row">
                                            <td class="text-center">No</td>
                                            <td class="text-center">Yes</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Admin Discount Type</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr role="row">
                                                <td class="text-center">1</td>
                                                <td>Percentage Discount</td>
                                            </tr>
                                            <tr role="row">
                                                <td class="text-center">2</td>
                                                <td>Flat Discount</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Cities</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($domestic_cities as $domestic_city)
                                            <tr role="row">
                                                <td>{{ $domestic_city->name }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Payment Modes</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($payment_modes as $payment_mode)
                                            <tr role="row">
                                                <td class="text-center">{{ $payment_mode->id }}</td>
                                                <td>{{ $payment_mode->name }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Charges Modes</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($charges_modes as $charges_mode)
                                            <tr role="row">
                                                <td class="text-center">{{ $charges_mode->id }}</td>
                                                <td>{{ $charges_mode->charges_mode }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Trax Boxes</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($trax_boxes as $trax_box)
                                            <tr role="row">
                                                <td class="text-center">{{ $trax_box->id }}</td>
                                                <td>{{ $trax_box->name }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                    <table class="table table-bordered">
                                        <thead>
                                        <tr role="row" class="bg-primary white text-center">
                                            <th colspan="2" class="border-primary border-darken-1">Banks</th>
                                        </tr>
                                        <tr role="row" class="bg-primary bg-lighten-1 white">
                                            <th class="text-center border-primary border-lighten-2">ID</th>
                                            <th class="border-primary border-lighten-2">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($banks as $bank)
                                            <tr role="row">
                                                <td class="text-center">{{ $bank->id }}</td>
                                                <td>{{ $bank->name }}</td>
                                            </tr>
                                        @endforeach
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
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
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

            $('#booking_form').validate({
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
                        text: 'Your shipment(s) are being booked!',
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