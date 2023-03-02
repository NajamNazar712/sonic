@extends('client.layout.master')

@section('title', 'Book an International Shipment')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="d-inline mb-1">
                    Book an International Shipment
                </h1>
                @if($credit_limit != null)
                    <h4 class="d-inline pull-right">Credit Limit Rs. {{number_format($credit_limit)}}</h4>
                @endif
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('client.inc.messages')

                            @if($credit_msg != '')
                                <div class="alert alert-warning">
                                    {{ $credit_msg }}
                                </div>
                            @endif
                            <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('cod.shipment.book.international.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div id="shipping_custom" class="col col_custom">
                                        <h4 id="shipper_header_info" class="form-section mb-2 text-center">Shipper Information</h4>

                                        <div class="form-group">
                                            <p class="border-bottom border-light text-center font-medium-1 text-bold-600">{{ $user->name }}</p>
                                        </div>

                                        <div class="form-group">
                                            <p class="border-bottom border-light text-center font-medium-1 text-bold-600">{{ $user->phone }}</p>
                                        </div>

                                        <div class="form-group">
                                            <select name="pickup_address" class="select2" id="pickup_address" data-rule-required="true" data-msg-required="Pickup Address is required">

                                                @php ($default_pickup_address = FALSE)

                                                @foreach($user->shipping as $shipping_information)
                                                    @if ($shipping_information['hidden'] == 0 && $shipping_information['status'] == 1)
                                                        @if ($shipping_information['default_address'] == 1)
                                                            @php ($default_pickup_address = TRUE)

                                                            <option value="{{ $shipping_information['id'] }}" data-city-id="{{ $shipping_information['city']['id'] }}" data-city-name="{{ $shipping_information['city']['name'] }}" selected >{{ $shipping_information['poc'] }}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['name'] }}</option>
                                                        @else
                                                            <option value="{{ $shipping_information['id'] }}" data-city-id="{{ $shipping_information['city']['id'] }}" data-city-name="{{ $shipping_information['city']['name'] }}">{{ $shipping_information['poc'] }}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['name'] }}</option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                                <option value="0">New</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <p class="border-bottom border-light text-center font-medium-1 text-bold-600" id="pickup_city_name"></p>
                                        </div>

                                        <div id="new_pickup_address" class="d-none">
                                            <div class="form-group">
                                                <textarea name="new_pickup_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required" data-rule-maxlength="190" data-msg-maxlength="Address can be maximum 190 characters"></textarea>
                                                <input type="checkbox" name="make_default_address" value="1">Make default address<br>
                                            </div>

                                            <div class="form-group">
                                                <input type="text" name="new_pickup_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required" data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters">
                                            </div>

                                            <div class="form-group">
                                                <input type="text" name="new_pickup_vendor" class="form-control" placeholder="Vendor" data-rule-maxlength="100" data-msg-maxlength="Vendor can be maximum 100 characters">
                                            </div>

                                            <div class="form-group">
                                                <input type="text" name="new_pickup_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                            </div>

                                            <div class="form-group">
                                                <input type="email" name="new_pickup_email_address" class="form-control" placeholder="Email Address*" data-rule-required="true" data-msg-required="Email Address is required">
                                            </div>

                                            <div class="form-group">
                                                <select name="new_pickup_city" class="select2" id="new_pickup_city" data-rule-required="true" data-msg-required="City is required">
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>

                                        @if($air_waybill != null)
                                            <div id="info_display" class="form-group text-center p-1 border border-light rounded">
                                                <label class="d-block">Show Information on Air Waybill</label>
                                                @if($air_waybill->information == 1)
                                                    <input type="checkbox" name="information_display" class="switch hidden" id="information_display" checked="checked">
                                                @else
                                                    <input type="checkbox" name="information_display" class="switch hidden" id="information_display">
                                                @endif
                                            </div>
                                        @else
                                            <div id="info_display" class="form-group text-center p-1 border border-light rounded">
                                                <label class="d-block">Show Information on Air Waybill</label>
                                                <input type="checkbox" name="information_display" class="switch hidden" id="information_display" checked="checked">
                                            </div>
                                        @endif

                                    </div>

                                    <div id="consignee_header_div" class="col col_custom">
                                        <h4 id="consignee_header_info" class="form-section mb-2 text-center">Consignee Information</h4>
                                        <label for="consignee_info">Search By Phone No.</label>
                                        <div class="form-group">
                                            <select name="consignee_info" class="select2" id="consignee_info">
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select name="consignee_city" class="select2" id="consignee_city" data-rule-required="true" data-msg-required="City is required">
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="postal_code" class="form-control postal_code" placeholder="Postal Code*" data-rule-required="true" data-msg-required="Postal Code is required" data-rule-maxlength="10" data-msg-maxlength="Postal Code can be maximum 10 digits">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="consignee_name" class="form-control" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required" data-rule-maxlength="100" data-msg-maxlength="Name can be maximum 100 characters">
                                        </div>

                                        <div class="form-group">
                                            <textarea id="consignee_address" name="consignee_address" class="form-control" rows="5" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required" data-rule-maxlength="255" data-msg-maxlength="Address can be maximum 255 characters"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="consignee_phone_number_1" class="form-control phone_number" placeholder="Phone Number 1*" data-rule-required="true" data-msg-required="Phone Number is required">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="consignee_phone_number_2" class="form-control phone_number" placeholder="Phone Number 2">
                                        </div>

                                        <div class="form-group">
                                            <input type="email" name="consignee_email_address" class="form-control" placeholder="Email Address" data-rule-maxlength="100" data-msg-maxlength="Email Address can be maximum 100 characters">
                                        </div>
                                    </div>

                                    <div id="order_information_header_div" class="col col_custom_middle">
                                        <h4 id="order_header_info" class="form-section mb-2 text-center">Order Information</h4>

                                        @if (Session::has('prefix'))
                                            <div class="form-group">
                                                <input name="order_id" class="form-control order_id" placeholder="Order ID" data-rule-maxlength="100" data-rule-required="true" data-msg-required="Order ID is required" data-msg-maxlength="Order ID can be maximum 100 characters" data-rule-remote="{{ route('cod.shipment.book.order_id') }}" data-msg-remote="Order ID must be unique">
                                            </div>
                                        @else
                                            @if (Session::has('restrict_order_id'))
                                                <div class="form-group">
                                                    <input name="order_id" class="form-control" placeholder="Order ID" data-rule-maxlength="100" data-msg-maxlength="Order ID can be maximum 100 characters" data-rule-remote="{{ route('cod.shipment.book.restrict_order_id') }}" data-msg-remote="Order ID must be unique">
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    <input name="order_id" class="form-control" placeholder="Order ID" data-rule-maxlength="100" data-msg-maxlength="Order ID can be maximum 100 characters">
                                                </div>
                                            @endif
                                        @endif

                                        <div class="form-group">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
													<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
														<span class="la la-calendar-o"></span>
													</span>
                                                </div>
                                                <input type="text" name="order_date" class="form-control bg-primary border-primary white rounded-right" id="order_date" placeholder="Order Date">
                                            </div>
                                        </div>

                                        <div id="regular">
                                            <div class="form-group">
                                                <select name="product_type" class="select2" id="product_type" data-rule-required="true" data-msg-required="Product Type is required">
                                                    @foreach($products as $product)
                                                        @if($user['product_id'] == $product->id)
                                                            <option value="{{ $product->id }}" selected>{{ $product->product_name }}</option>
                                                        @else
                                                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <textarea name="item_description" class="form-control" placeholder="Item Description*" data-rule-required="true" data-msg-required="Item Description is required" data-rule-maxlength="1000" data-msg-maxlength="Item Description can be maximum 1000 characters" rows="5"></textarea>
                                            </div>

                                            <div class="form-group input-group">
                                                <input type="text" name="item_quantity" class="form-control text-center quantity" placeholder="Item Quantity*" data-rule-required="true" data-msg-required="Item Quantity is required" data-toggle="tooltip" data-placement="top" title="" data-original-title="Here you enter the no. of items inside the flyer/box.">
                                            </div>

                                            <div class="form-group text-center p-1 border border-light rounded">
                                                <label class="d-block">Insurance</label>
                                                <input type="checkbox" name="insurance" class="switch hidden insurance">
                                            </div>

                                            <div class="form-group input-group d-none">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rs</span>
                                                </div>

                                                <input type="text" name="item_price" class="form-control rounded-right price" placeholder="Product Value*" data-rule-required="true" data-msg-required="Product Value is required">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <textarea name="special_instructions" class="form-control" placeholder="Special Instructions" data-rule-maxlength="190" data-msg-maxlength="Special Instructions can be maximum 190 characters" rows="5"></textarea>
                                        </div>
                                    </div>

                                    <div id="shipping_header_div" class="col col_custom">
                                        <h4 id="shipping_header_info" class="form-section mb-2 text-center">Shipping Information</h4>

                                        <div class="form-group input-group mb-0">
                                            <input type="text" name="estimated_weight" class="form-control weight" placeholder="Estimated Weight*" data-rule-required="true" data-msg-required="Estimated Weight is required">

                                            <div class="input-group-append">
                                                <span class="input-group-text">kg</span>
                                            </div>
                                        </div>

                                        <h6 class="form-text mb-1 text-justify text-muted text-italic">*Charges will be subjected to the Final Weight measured at the time of Shipment Arrival.</h6>

                                        <div id="pieces_quantity" class="form-group input-group">
                                            <input  type="text" name="pieces_quantity" class="form-control text-center pieces" placeholder="Pieces*" data-rule-required="true" data-msg-required="Pieces is required" data-toggle="tooltip" data-placement="top" title="" data-original-title="Here you enter the no. of individual flyers or boxes your shipment is separated into, so each can have it's own indentity slip and be accounted for.">
                                        </div>

                                        <div id="charges_mode_div" class="form-group">
                                            <select name="charges_mode" class="select2" id="charges_mode" data-rule-required="true" data-msg-required="Charges Mode is required">
                                                @foreach($charges_modes as $charges_mode)
                                                    @if ($charges_mode->id == 4)
                                                        <option value="{{ $charges_mode->id }}" selected="selected">{{ $charges_mode->charges_mode }}</option>
                                                    @else
                                                        <option value="{{ $charges_mode->id }}">{{ $charges_mode->charges_mode }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div id="payment_info" class="col col_custom">
                                        <h4 class="form-section mb-2 text-center">Payment Information</h4>
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rs</span>
                                            </div>

                                            <input type="text" name="amount" id="amount" class="form-control rounded-right amount" placeholder="Collection Amount*" data-rule-required="true" data-msg-required="Collection Amount is required">
                                        </div>

                                        <div class="form-group">
                                            <select name="payment_mode" class="select2" id="payment_mode" data-rule-required="true" data-msg-required="Mode of Payment is required">
                                                @foreach($payment_modes as $payment_mode)
                                                    <option value="{{ $payment_mode->id }}">{{ $payment_mode->mode }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="shipper_references" class="col">
                                    <h4 class="form-section mb-2 text-center">Shipper References (Optional)</h4>
                                    <div class="row">
                                        <div class="form-group col">
                                            <input name="shipper_reference_1" class="form-control shipper_reference" placeholder="Shipper Reference 1" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 1 can be maximum 190 characters">
                                        </div>
                                        <div class="form-group col">
                                            <input name="shipper_reference_2" class="form-control shipper_reference" placeholder="Shipper Reference 2" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 2 can be maximum 190 characters">
                                        </div>
                                        <div class="form-group col">
                                            <input name="shipper_reference_3" class="form-control shipper_reference" placeholder="Shipper Reference 3" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 3 can be maximum 190 characters">
                                        </div>
                                        <div class="form-group col">
                                            <input name="shipper_reference_4" class="form-control shipper_reference" placeholder="Shipper Reference 4" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 4 can be maximum 190 characters">
                                        </div>
                                        <div class="form-group col">
                                            <input name="shipper_reference_5" class="form-control shipper_reference" placeholder="Shipper Reference 5" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 5 can be maximum 190 characters">
                                        </div>
                                    </div>
                                </div>
                                @if($allow_booking)
                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="form-group text-center">
                                            <button type="submit" name="book" class="btn btn-primary submission" value="Book">Book</button>
                                            <button type="submit" name="book_and_print" class="btn btn-primary ml-1 submission" value="Book & Print">Book & Print</button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/spinner/jquery.bootstrap-touchspin.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tooltip/tooltip.js')}}" type="text/javascript"></script>

    <script>

        $(document).ready(function() {
            var cities = @json($cities);
            console.log(cities);
            var order_date = $('#order_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                }
            });

            $(this).find('.pieces').TouchSpin({
                min: 1,
                max: 10,
                buttondown_class: 'btn btn-primary rounded-left',
                buttonup_class: 'btn btn-primary rounded-right',
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            }).bind('input change', function() {
                $(this).tooltip('show');

                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });


            @if (session('print'))
            $.ajax({
                url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'ids[]': '{{ session('print') }}'
                }
            })
                .done(function(data) {
                    var tab = window.open('', '_blank');

                    if(!tab) {
                        swal({
                            title: 'Popup Blocker Enabled!',
                            text: 'Please add this site to your exception list.',
                            icon: 'error',
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                    }
                    else {
                        tab.document.write(data);
                        tab.document.close();
                        tab.focus();
                    }
                });
            @endif

            $('#charges_mode').select2({
                width: '100%',
                placeholder: 'Charges Mode*'
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });

            $('#pickup_address').select2({
                width: '100%',
                placeholder: 'Pickup Address*'
            }).bind('change', function () {
                $(this).valid();

                if (this.value == 0) {
                    $('#new_pickup_address').removeClass('d-none');
                } else {
                    $('#new_pickup_address').addClass('d-none');
                }
            });

            $('#new_pickup_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            }).bind('change', function() {
                $(this).valid();
            });

            $("#consignee_info").select2({
                width:'100%',
                placeholder: "Search Here...",
                minimumInputLength: 5,
                ajax: {
                    url: '{{ route('cod.shipment.book.get_consignee_infos') }}',
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                            'shipper': '{{session('user_id')}}'
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) { return markup; },
                templateResult: formatRepo,
                templateSelection: formatRepoSelection

            });
            function formatRepo (repo) {
                if (repo.loading) return repo.text;
                var markup = "<option value='" + repo.id + "'>"+ repo.full_name +"</option>";

                return markup;
            }
            function formatRepoSelection (repo) {
                return repo.full_name || repo.text;
            }

            var blacklist = false;
            var blacklist_message = '';
            var blacklist_color = '';
            function check_consignee_return_ratio(){
                var phone = $('input[name="consignee_phone_number_1"]').val();
                if(phone){
                    $.ajax({
                        url:'{!! route('cod.shipment.book.check_consignee_return_ratio') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'phone': phone,
                        }
                    }).done(function (data) {
                        if(data.status == 0){
                            blacklist = true;
                            blacklist_message = data.message;
                            blacklist_color = data.color;
                            return true;
                        }
                    });
                }
            }

            $('#consignee_info').on('select2:select', function () {
                var id = parseInt($(this).val());
                if(id){
                    $.ajax({
                        url:'{!! route('cod.shipment.book.get_consignee_info') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id,
                        }
                    }).done(function (data) {
                        if(data.status){
                            $('input[name="consignee_name"]').val(data.details.name);
                            $('#consignee_address').val(data.details.address);
                            $('input[name="consignee_phone_number_1"]').val(data.details.phone_number_1).change();
                            $('input[name="consignee_phone_number_2"]').val(data.details.phone_number_2);
                            $('input[name="consignee_email_address"]').val(data.details.email);
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
                }
            });

            $('input[name="consignee_phone_number_1"]').bind('change paste keyup', function () {
                if($(this).val().match(/\d/g) != null){
                    var length = $(this).val().match(/\d/g).length;
                }
                else{
                    var length = 0;
                }
                if(length == 11){
                    check_consignee_return_ratio();
                }


            });

            $('#information_display').checkboxpicker();
            $('#self_collection').checkboxpicker();


            $('#consignee_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Country*'
            }).bind('change', function() {
                $(this).valid();
            });

            $('#product_type').select2({
                width: '100%',
                placeholder: 'Product Type*'
            }).bind('change', function() {
                $(this).valid();
            });

            $('#regular .insurance').checkboxpicker().bind('change', function() {
                var parent = $(this).parent('.form-group').next('.form-group');

                if (this.checked) {
                    parent.removeClass('d-none');
                }
                else {
                    parent.addClass('d-none');

                    parent.children('#item_price-error').remove();
                }
            });

            // $('#amount').bind('keypress', function () {
            //     $('#booking_form .submission').attr('disabled', true);
            // });

            {{--$('#amount, #consignee_city, #pickup_address').change(function(){--}}
            {{--    $('#booking_form .submission').attr('disabled', true);--}}

            {{--    $('#span').remove();--}}
            {{--    if ($('#pickup_address').val() == 0) {--}}
            {{--        var pickup_city_id = $('#new_pickup_city').val();--}}
            {{--    }--}}
            {{--    else {--}}
            {{--        var pickup_city_id = $('#pickup_address').find(':selected').data('city-id');--}}
            {{--    }--}}

            {{--    $.ajax({--}}
            {{--        url:'{!! route('cod.shipment.book.check_cod_cap_zone_classes') !!}',--}}
            {{--        method: 'POST',--}}
            {{--        data: {--}}
            {{--            '_token': '{{ csrf_token() }}',--}}
            {{--            'amount': $('#amount').val(),--}}
            {{--            'pickup_city': pickup_city_id,--}}
            {{--            'consignee_city': $('#consignee_city').val(),--}}
            {{--        }--}}
            {{--    }).done(function (data) {--}}
            {{--        if(data.status === 1){--}}
            {{--            $('#span').remove();--}}
            {{--            var span = '<span id="span" style="color: red">'+data.error+'</span>';--}}
            {{--            $('#amount').val('');--}}
            {{--            $('#amount').parent('div').append(span);--}}

            {{--        }--}}
            {{--        if(data.status === 2) {--}}
            {{--            $('#span').remove();--}}

            {{--            $('#booking_form .submission').attr('disabled', false);--}}
            {{--        }--}}
            {{--        if(data.status === 0) {--}}
            {{--            $('#span').remove();--}}
            {{--            var span = '<span id="span" style="color: red">'+data.error+'</span>';--}}
            {{--            $('#amount').val('');--}}
            {{--            $('#amount').parent('div').append(span);--}}
            {{--        }--}}
            {{--        if(data.status === 3) {--}}
            {{--            $('#span').remove();--}}
            {{--            if ($('#pickup_address').val() == "") {--}}
            {{--                var span = '<span id="span" style="color: red">Pickup address is required</span>';--}}
            {{--                $('#pickup_address').parent('div').append(span);--}}
            {{--            }--}}
            {{--            else{--}}
            {{--                var span = '<span id="span" style="color: red">'+data.error+'</span>';--}}
            {{--                $('#new_pickup_city').parent('div').append(span);--}}
            {{--            }--}}
            {{--            $('#amount').val('');--}}
            {{--        }--}}
            {{--        if(data.status === 4) {--}}
            {{--            $('#span').remove();--}}
            {{--            var span = '<span id="span" style="color: red">'+data.error+'</span>';--}}
            {{--            $('#consignee_city').parent('div').append(span);--}}
            {{--            $('#amount').val('');--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}


            $('#payment_mode').select2({
                width: '100%',
                placeholder: 'Mode of Payment*'
            }).bind('change', function() {
                $(this).valid();
            });

            var check = @json($check);

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
                    check_consignee_return_ratio();
                    var pressed_button = $(this.submitButton);

                    $(form).append('<input type="hidden" name="' + pressed_button.attr('name') + '" value="' + pressed_button.attr('value') + '">');

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    var consignee_address = $('#consignee_address').val();
                    var strArray = consignee_address.split(/[ ,]+/);
                    var present = [];
                    for(k=0;k<strArray.length;k++) {
                        for (i = 0; i < check.length; i++) {
                            if(JSON.stringify(strArray[k]).toLowerCase()=== JSON.stringify(check[i]).toLowerCase()){
                                present.push(strArray[k]);
                            }
                        }
                    }
                    if(present.length > 0){
                        var url = '{{asset('img/nsa_osa.png')}}';
                        var html = '<div class="row justify-content-center"><img src="' + url + '"></div>';
                        html += '<div class="row justify-content-center"><h2><b>A Possible Address Anomaly: ' + present + ' Detected!</b></h2></div>';
                        html += '<div class="text-left">In case of,<br/>';
                        html += '<b>Out of Service Area:</b> Additional charges may apply.</br>';
                        html += '<b>Non Service Area:</b> Shipment may be returned.</br>';
                        html += '<b>For assistance, Call:</b> 021-38772222</br></div>';
                        content = document.createElement('div');
                        content.innerHTML = html;
                        swal({
                            content: content,
                            buttons: {
                                cancel: {
                                    text: 'Cancel',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Continue to Booking',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            // dangerMode: true
                        }).then(function(confirm) {
                            if(confirm) {

                                if(blacklist == true){
                                    var html = '<div class="row justify-content-center p-1" style="background-color: '+ blacklist_color +'; color:white;">'+ blacklist_message +'</div>';
                                    content = document.createElement('div');
                                    content.innerHTML = html;
                                    swal({
                                        content: content,
                                        buttons: {
                                            cancel: {
                                                text: 'Cancel',
                                                value: null,
                                                visible: true,
                                                closeModal: true,
                                            },
                                            confirm: {
                                                text: 'Book Anyway',
                                                value: true,
                                                visible: true,
                                                closeModal: true
                                            }
                                        },
                                        closeOnClickOutside: false,
                                        closeOnEsc: false,
                                        // dangerMode: true
                                    }).then(function(confirm) {
                                        if (confirm) {
                                            swal({
                                                title: 'Please Wait!',
                                                text: 'Your shipment is being booked!',
                                                icon: 'info',
                                                buttons: false,
                                                closeOnClickOutside: false,
                                                closeOnEsc: false
                                            });

                                            form.submit();
                                        }
                                        else{
                                            $(form).find('button[type=submit]').prop('disabled', false);
                                        }
                                    });
                                }else{
                                    swal({
                                        title: 'Please Wait!',
                                        text: 'Your shipment is being booked!',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });

                                    form.submit();
                                }

                            }
                            else{
                                $(form).find('button[type=submit]').prop('disabled', false);
                            }
                        });
                    }
                    else {
                        if(blacklist == true) {
                            var html = '<div class="row justify-content-center p-1" style="background-color: '+ blacklist_color +'; color:white;">' + blacklist_message + '</div>';
                            content = document.createElement('div');
                            content.innerHTML = html;
                            swal({
                                content: content,
                                buttons: {
                                    cancel: {
                                        text: 'Cancel',
                                        value: null,
                                        visible: true,
                                        closeModal: true,
                                    },
                                    confirm: {
                                        text: 'Book Anyway',
                                        value: true,
                                        visible: true,
                                        closeModal: true
                                    }
                                },
                                closeOnClickOutside: false,
                                closeOnEsc: false,
                                // dangerMode: true
                            }).then(function (confirm) {
                                if (confirm) {
                                    swal({
                                        title: 'Please Wait!',
                                        text: 'Your shipment is being booked!',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });

                                    form.submit();
                                }
                                else{
                                    $(form).find('button[type=submit]').prop('disabled', false);
                                }
                            });
                        }else{
                            swal({
                                title: 'Please Wait!',
                                text: 'Your shipment is being booked!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });

                            form.submit();
                        }

                    }
                }
            });

            $('.phone_number').inputmask("Regex", { regex: "[+|0][0-9]*"});

            $(this).find('.quantity').TouchSpin({
                min: 1,
                max: 10000,
                buttondown_class: 'btn btn-primary rounded-left',
                buttonup_class: 'btn btn-primary rounded-right',
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            }).bind('input change', function() {
                $(this).tooltip('show');
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });

            $('.bootstrap-touchspin-down, .bootstrap-touchspin-up').attr('tabindex', -1);

            $('.price').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'groupSeparator': ',',
                'autoGroup': true,
                'min': 1,
                'max': 100000
            });

            /*$('.postal_code').inputmask({
                'alias': 'numeric',
                'allowMinus': false,
                'allowPlus': false
            });*/

            $('.weight').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2,
                'min': 0.1,
                'max': 100000
            });

            $('.amount').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('.order_id').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'min': 0,
                'max': 1000000000000
            });
        });
    </script>
@endsection