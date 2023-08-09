@extends('agent.layout.master')

@section('title', 'RV')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">


                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="border-primary">
                                <div class="d-flex flex-wrap align-items-center" style="background-color: #0EE290"></div>
                                <div class="d-flex flex-wrap align-items-center bg-primary">
                                    <div class="font-medium-3 white" style="margin:auto;";>Virtual RCP Agent Screen
                                    </div>
                                </div>


                                <form id="track_form" class="form-inline mb-1 justify-content-center"
                                    novalidate="novalidate">

                                    <div class="d-none">
                                        <div class="form-group">
                                            <input type="hidden" name="auth_id" class="auth_id"
                                                value="{{ Auth::id() }}">
                                        </div>
                                    </div>



                                    <div class="form-group mt-1">
                                        <button type="submit" name="track" class="btn btn-primary" value="Track"
                                            id="get_ticket_button">Get Ticket</button>
                                    </div>
                                </form>


                                <div class="p-1" id="horizontal_line">
                                    <div class="row justify-content-between">
                                        <div class="col-1">
                                            <h6>Total Tickets</h6>
                                            <div class="border table-responsive gray">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-center"><strong>{{count( $agent_total_tickets) }}</strong></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-11">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-4 p-0">
                                                        <div class="p-0">
                                                            <h6>Re - Attempt Count</h6>
                                                            <div class="border table-responsive gray">
                                                                <table class="table table-sm table-borderless mb-0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="text-center"><strong>{{ $reattempt_count }}</strong></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 p-0">
                                                        <div class="p-0">
                                                            <h6>Refused On Call</h6>
                                                            <div class="border table-responsive gray">
                                                                <table class="table table-sm table-borderless mb-0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="text-center"><strong>{{ $refused_on_call }}</strong></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 p-0">
                                                        <div class="p-0">
                                                            <h6>Unresponsive</h6>
                                                            <div class="border table-responsive gray">
                                                                <table class="table table-sm table-borderless mb-0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="text-center"><strong>{{ $unresponsive_count }}</strong></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="phone_number" value="{{ $user->phone_number }}">
                                    <div class="row justify-content-between">
                                        <div class="col-2">
                                            <h6 class="mt-2">Agent Employee ID</h6>
                                            <div class="border table-responsive gray">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>{{ $user->trax_id }}</strong></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-10 justify-content-center">
                                            <div class="container">
                                                <div class="row d-flex justify-content-center">
                                                    <div class="col-2 p-0">
                                                        <div class="p-0">
                                                            {{-- <h6 class="text-center mt-2">Agent</h6>
                                            <div class="border table-responsive gray">
                                              <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                  <tr>
                                                    <td class="text-center agent"><strong>harry</strong></td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                            </div> --}}
                                                        </div>
                                                    </div>
                                                    <div class="col-5 p-0">
                                                        <div class="p-0">
                                                            <h6 class="text-center mt-2">Agent</h6>
                                                            <div class="border table-responsive gray">
                                                                <table class="table table-sm table-borderless mb-0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="text-center agent">
                                                                                <strong>{{ $user->name }}</strong>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 p-0">
                                                        <div class="p-0">
                                                            {{-- <h6 class="text-center mt-2">Agent</h6>
                                              <div class="border table-responsive gray">
                                                <table class="table table-sm table-borderless mb-0">
                                                  <tbody>
                                                    <tr>
                                                      <td class="text-center agent"><strong>harry</strong></td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                              </div> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>




                                </div>


                                <div class="tracking" id="tracking">



                                </div>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" value="" id="shipment_id_val">

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <form id="intercept_form" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group col-md-3 mb-2 text-center" style="margin: auto; margin-top:10px;">
                            <select name="consignee" class="select2" id="consignee" data-rule-required="true"
                                data-msg-required="Consignee is required">
                                <option value="1">Different Consignee</option>
                                <option value="2">Same Consignee</option>
                            </select>
                        </div>
                        <input type="hidden" name="shipment_id">
                        <div class="row justify-content-center">
                            <div class="col-md-4 mr-5 pl-0">
                                <h4 class="form-section mb-2 text-center">Consignee Information</h4>
                                <div class="form-group">
                                    <select name="consignee_city" class="select2" value="" id="consignee_city"
                                        data-rule-required="true" data-msg-required="City is required">

                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="consignee_name" value="" id="consignee_name"
                                        class="form-control" placeholder="Name*" data-rule-required="true"
                                        data-msg-required="Name is required" data-rule-maxlength="100"
                                        data-msg-maxlength="Name can be maximum 100 characters">
                                </div>

                                <div class="form-group">
                                    <textarea id="consignee_address" name="consignee_address" class="form-control" rows="6"
                                        placeholder="Address*" data-rule-required="true" data-msg-required="Address is required"
                                        data-rule-maxlength="255" data-msg-maxlength="Address can be maximum 255 characters"></textarea>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="consignee_phone_number_1" id="consignee_phone_number_1"
                                        class="form-control phone_number" placeholder="Phone Number 1*"
                                        data-rule-required="true" data-msg-required="Phone Number is required">
                                </div>

                                <div class="form-group">
                                    <input type="text" name="consignee_phone_number_2" id="consignee_phone_number_2"
                                        class="form-control phone_number" placeholder="Phone Number 2">
                                </div>
                                <input type="text" name="intercept_type" id="intercept_type" value="1"
                                    class="form-control hidden">

                                <div class="form-group">
                                    <input type="email" name="consignee_email" value="" id="consignee_email"
                                        class="form-control" placeholder="Email Address" data-rule-maxlength="100"
                                        data-msg-maxlength="Email Address can be maximum 100 characters">
                                </div>

                                <div class="form-group d-none" id="replacement_parcel_image_div">
                                    <label class="d-block bold">Replacement Parcel Image</label>
                                    <input class="form-control form-control-sm" type="file"
                                        name="replacement_parcel_image" id="replacement_parcel_image"
                                        data-rule-extension="jpeg|jpg|png"
                                        data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                        data-rule-accept="image/*" data-msg-accept="Only Image file allowed"
                                        data-rule-maxsize="2097152"
                                        data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                </div>

                            </div>
                            <div class="col-md-4 mr-5 pl-0">
                                <h4 class="form-section mb-2 text-center">Payment Information</h4>
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rs</span>
                                    </div>

                                    <input type="text" name="amount" value="" id="amount"
                                        class="form-control rounded-right amount" placeholder="Collection Amount*"
                                        data-rule-required="true" data-msg-required="Collection Amount is required"
                                        id="amount">
                                </div>

                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col">
                                <div class="form-group text-center">
                                    <button type="submit" name="update" id="intercept_update"
                                        class="btn btn-primary width-10-per" value="Book">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @section('css')
        <link rel="stylesheet" type="text/css"
            href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">


        <style>
            /*.selectize-control {
                   width: 300px !important;
                  }*/

            #horizontal_line {
                border: none;
                border-bottom: 1px solid black;

            }

            .cell-padding {
                /* padding: 10px; */
                padding-left: 3px !important;
            }

            .tsize {
                font-size: 13px;
            }

            .switch {
                position: relative;
                display: inline-block;
                width: 60px;
                height: 20px;
            }

            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                -webkit-transition: .4s;
                transition: .4s;
                width: 48px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 14px;
                width: 14px;
                left: 4px;
                bottom: 3px;
                background-color: white;
                -webkit-transition: .4s;
                transition: .4s;
            }

            input:checked+.slider {
                background-color: #2196F3;
            }

            input:focus+.slider {
                box-shadow: 0 0 1px #2196F3;
            }

            input:checked+.slider:before {
                -webkit-transform: translateX(26px);
                -ms-transform: translateX(26px);
                transform: translateX(26px);
            }

            .slider.round {
                border-radius: 20px;
            }

            .slider.round:before {
                border-radius: 50%;
            }

            .tracking_numbers {
                width: 100% !important;
            }

            #shipment_reason {
                display: none;
            }

            #fake_status_id {
                width: 300px;
            }

            .gray {
                background-color: #E7E7E7
            }

            .error_message {
                color: red
            }
        </style>
    @endsection

    @section('js')
        <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('app-assets/vendors/js/forms/tags/tagging.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
        </script>
        <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
            type="text/javascript"></script>
        <script src="https://kit.fontawesome.com/e7bc565afe.js" crossorigin="anonymous"></script>
        <script type="text/javascript">
            $(document).ready(function() {

                $(document).on('change', '#tswitch', function() {
                    if ($("#tswitch").is(":checked")) {
                        $('#tmsg').removeClass('d-none');
                        $('#switch2').removeClass('d-none');
                        $('#scswitch').removeClass('d-none');
                        $('#scswitch1').removeClass('d-none');


                    } else {
                        $('#tmsg').addClass('d-none');
                        $('#switch2').addClass('d-none');
                        $('#scswitch').addClass('d-none');
                        $('#scswitch1').addClass('d-none')
                    }
                });


                $('#consignee_city').select2({
                    width: '100%',
                    placeholder: 'City*'
                });
                $('#consignee').select2({
                    width: '100%',
                    placeholder: 'Consignee*'
                }).bind('change', function() {
                    if (this.value == 2) {
                        console.log(this.value)
                        $('#consignee_city').val();
                        $('#consignee_city').trigger('change');
                        var hiddenInput = $('<input/>', {
                            type: 'hidden',
                            name: 'consignee_city',
                            value: $('#consignee_city').val(),
                            id: 'new_city'
                        });
                        $('#intercept_form').append(
                            hiddenInput
                        ); //append the hidden field with same name and value from the dropdown field
                        $('#intercept_type').val(2);
                        $('#consignee_city').addClass('disabled') //disable class
                            .prop({
                                'name': 'new_consignee_city',
                                disabled: true
                            }); //change name and disbale
                        $("#consignee_name").val();
                        $("#consignee_name").prop('readonly', true);
                        $("#consignee_email").val();
                        $("#consignee_email").prop('readonly', true);
                        $("#amount").val();
                        $("#amount").prop('readonly', true);
                        $("#replacement_parcel_image_div").removeClass("d-none");
                    } else {
                        console.log(this.value)
                        $("#consignee_name").prop('readonly', false);
                        $("#consignee_email").prop('readonly', false);
                        $('#intercept_type').val(1);
                        $("#amount").prop('readonly', false);
                        $('#intercept_form').find('#new_city').remove(); // remove the hidden fields if any
                        $('#consignee_city').removeClass('disabled') //remove disable class
                            .prop({
                                name: 'consignee_city',
                                disabled: false
                            }); //restore the name and enable
                        $("#replacement_parcel_image_div").addClass("d-none");
                    }
                });

                $('.amount').inputmask({
                    'alias': 'integer',
                    'allowMinus': false,
                    'allowPlus': false,
                    'groupSeparator': ',',
                    'autoGroup': true,
                    'max': 1000000
                });

                $('.phone_number').inputmask({
                    'mask': '9999-9999999',
                    'clearIncomplete': true
                });



                var auth_id = null;

                var auth_id = $('.auth_id').val();

                function track() {
                    $.ajax({
                            url: '{!! route('agent.dashboard.get_ticket') !!}',
                            method: 'POST',
                            data: {
                                'auth_id': auth_id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                        .done(function(data) {
                            if (data.shipment != undefined) {
                                $('#shipment_id_val').val(data.shipment.id)
                                var shipment = '';

                                shipment +=
                                    '<form id="get_submit" class="form-horizontal" method="post" enctype="multipart/form-data"> @csrf'
                                shipment += '<div class="row justify-content-between pl-1">'
                                shipment += '  <div class="col-2 pr-0 pl-1">'
                                shipment += '    <h6 class="mt-2"></h6>'
                                shipment += '    <div class="border table-responsive gray">'
                                shipment += '      <table class="table table-sm table-borderless mb-0">'
                                shipment += '        <tbody>'
                                shipment += '          <tr>'
                                shipment += '            <td><strong>Tracking Number</strong></td>'
                                shipment += '          </tr>'
                                shipment += '        </tbody>'
                                shipment += '      </table>'
                                shipment += '    </div>'
                                shipment += '  </div>'
                                shipment += '  <div class="col-10 pl-0">'
                                shipment += '    <div class="container">'
                                shipment += '      <div class="row justify-content-center">'
                                shipment += '        <div class="col-12 pl-0">'
                                shipment += '          <div class="p-0">'
                                shipment += '            <h6 class="text-center mt-2"></h6>'
                                shipment += '            <div class="border table-responsive gray">'
                                shipment += '              <table class="table table-sm table-borderless mb-0">'
                                shipment += '                <tbody>'
                                shipment += '                  <tr>'
                                shipment += '                    <td class="text-center agent"><strong>' + data
                                    .shipment.tracking_number + '</strong></td>'
                                shipment += '                  </tr>'
                                shipment += '                </tbody>'
                                shipment += '              </table>'
                                shipment += '            </div>'
                                shipment += '          </div>'
                                shipment += '        </div>'
                                shipment += '      </div>'
                                shipment += '    </div>'
                                shipment += '  </div>'
                                shipment += '</div>'

                                shipment += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mt-2">';
                                shipment += '<h4 class="text-center"><u>Shipper Information</u></h4>';
                                shipment += '<div class="border table-responsive gray">';
                                shipment += '<table class="table table-sm table-borderless mb-0">';
                                shipment += '<tbody>';
                                shipment += '<tr>';
                                shipment += '<td style="width: 15%;"><strong>Name</strong></td>';
                                shipment += '<td style="width: 45%;">' + ((data.shipper_info != null && data
                                    .shipper_info.name !=
                                    null) ? data.shipper_info.name : '----------------') + '</td>';
                                shipment += '<td><strong>Origin</strong></td>';
                                shipment += '<td>' + ((data.shipper_city != null && data.shipper_city.name !=
                                    null) ? data.shipper_city.name : '----------------') + '</td>';
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<tr>';
                                shipment += '<td colspan="2"></td>';
                                shipment += '</tr>';
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<td style="width: 15%;"><strong>Address</strong></td>';
                                shipment += '<td style="width: 45%;" colspan="1">' + ((data.shipper_info != null &&
                                        data.shipper_info
                                        .address != null) ? data.shipper_info.address : '----------------') +
                                    '</td>';
                                shipment += '<td><strong>Phone No(s).</strong></td>';
                                shipment += '<td>' + ((data.shipper_info != null && data.shipper_info.phone !=
                                    null) ? data.shipper_info.phone : '----------------') + '</td>';
                                shipment += '</tr>';
                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';
                                shipment += '</tbody>'; 
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';

                                shipment += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mt-2">';
                                shipment += '<h4 class="text-center"><u>Consignee Information</u></h4>';
                                shipment += '<div class="border table-responsive gray">';
                                shipment += '<table class="table table-sm table-borderless mb-0">';
                                shipment += '<tbody>';
                                shipment += '<tr>';
                                shipment += '<td style="width: 15%;"><strong>Name</strong></td>';
                                shipment += '<td style="width: 45%;">' + ((data.shipment.consignee_name != null &&
                                        data.shipment.consignee_name
                                         != null) ? data.shipment.consignee_name : '----------------') + '</td>';
                                shipment += '<td><strong>Origin</strong></td>';
                                shipment += '<td>' +  ((data.consignee_city.name != null &&
                                        data.consignee_city.name
                                         != null) ? data.consignee_city.name : '----------------') + '</td>';
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<tr>';
                                shipment += '<td colspan="2"></td>';
                                shipment += '</tr>';
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<td style="width: 15%;"><strong>Address</strong></td>';
                                shipment += '<td style="width: 45%;" colspan="1">' + data.shipment
                                    .consignee_address + '</td>';
                                shipment += '<td><strong>Phone No(s).</strong></td>';
                                shipment += '<td>' + data.shipment.consignee_phone_number_1 + '</td>';
                                shipment += '</tr>';
                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';
                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';


                                shipment += '<div class="col-12 mt-2">';
                                shipment += '<h4><u>Order Information</u></h4>';
                                shipment += '<div class="border table-responsive black">';
                                shipment += '<table class="table table-sm table-borderless mb-0">';

                                shipment += '<tr>';
                                shipment += '<table class="table table-sm table table-bordered mb-0">';
                                shipment += '<thead>';
                                shipment += '<tr>';

                                shipment += '<td class="cell-padding"><strong>Product Type</strong></td>';
                                shipment += '<td class="cell-padding"><strong>Description</strong></td>';
                                shipment += '<td class="cell-padding"><strong>Quantity</strong></td>';
                                shipment += '<td class="cell-padding"><strong>Order ID</strong></td>';
                                shipment += '</tr>';
                                shipment += '</thead>';
                                shipment += '<tbody>';
                                $.each(data.detail_product_infos, function(index, item) {
                                    shipment += '<tr class="cell-padding">';
                                    shipment += '<td>' + item.product_name + '</td>';
                                    shipment += '<td>' + ((item.description != null) ? item.description :
                                        '---------------') + '</td>';
                                    shipment += '<td>' + item.quantity + '</td>';
                                    shipment += '<td>' + ((item.order_id != null) ? item.order_id :
                                        '---------------') + '</td>';
                                    shipment += '</tr>';
                                });
                                shipment += '</tbody>';

                                shipment += '</table';
                                shipment += '</tr>';



                                shipment += '<tbody>';



                                shipment += '<tr>';

                                shipment += '<td><strong>Weight</strong></td>';
                                shipment += '<td>' + data.shipment.actual_weight + '</td>';
                                shipment += '<td><strong>Service Type</strong></td>';
                                shipment += '<td>' + data.service_type.booking_type + '</td>';
                                shipment += '<td><strong>Collection Amount</strong></td>';
                                shipment += '<td>Rs. ' + data.shipment.amount + '</td>';
                                shipment += '<td><strong>Piece(s)</strong></td>';
                                shipment += '<td>' + data.shipment.pieces + '</td>';
                                shipment += '</tr>';

                                shipment += '<tr>';
                                shipment += '<td><strong>Shipping Mode</strong></td>';
                                shipment += '<td>' + data.shipping_mode.mode + '</td>';
                                shipment += '<td><strong>Instructions</strong></td>';

                                shipment += '<td>' + ((data.shipment.special_intructions != null) ? data.shipment
                                    .special_intructions : 'No Instruction') + '</td>';
                                shipment += '<td><strong>Business Category</strong></td>';
                                shipment += '<td>' + data.business_category.name + '</td>';

                                shipment += '</tr>';


                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';


                                shipment += '<div class="col-12 mt-2">';
                                shipment += '<h4 class="text-center"><u>Tracking Information</u></h4>'
                                shipment += '<div class="border table-responsive gray">';
                                shipment +=
                                    '<table class="table table-sm table-borderless datatable tracking_history">';
                                shipment += '<thead>';
                                shipment += '<tr role="row">';

                                shipment += '<th style="padding-right:0px" class="col-1"><strong>Reason:</strong></th>';

                                shipment += '<td>' + ((data.rider_details.reason.name != null) ? data
                                    .rider_details.reason.name : '-----------') + '</td>';
                                shipment += '<th style="padding-right:0px" class="col-1"><strong>Attempted Time:</strong></th>';
                                shipment += '<td>' + ((data.rider_details.attempted_time != null) ? data
                                    .rider_details.attempted_time : '-----------') + '</td>';
                                shipment += '<th><strong>Remarks: </strong></th>';
                                shipment += '<td>' + ((data.rider_details.remarks != null) ? data
                                    .rider_details.remarks : '-----------') + '</td>';
                                shipment += '</tr>';
                                shipment += '</thead>';
                                shipment += '<tbody>';

                                shipment += '</tbody>';
                                shipment += '</table>';

                                shipment += '</div>';
                                shipment += '</div>';

                                if (data.image_location.image != null || data.image_location.audio != null || data
                                    .image_location.location != null) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<div class="border table-responsive">';
                                    shipment +=
                                        '<table class="table table-sm table-borderless datatable tracking_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row justify-content-center">';

                                    if (data.image_location.image != null) {
                                        shipment += '<a href="' + data.image_location.image +
                                            '" target="_blank" style="text-decoration: underline; margin-right: 50px;">View Image</a>';
                                    } else {
                                        shipment += '';

                                    }

                                    if (data.image_location.audio != null) {

                                        shipment += '<a href="' + data.image_location.audio +
                                            '" target="_blank" style="text-decoration: underline; margin-right: 50px;">Listen Audio</a>';
                                    } else {
                                        shipment += '';
                                    }

                                    if (data.image_location.location != null) {
                                        shipment += '<a href="https://www.google.com/maps/search/?api=1&query= ' +
                                            data
                                            .image_location.location +
                                            '" target="_blank" style="text-decoration: underline;">View Location</a>';

                                    } else {
                                        shipment += '';
                                    }

                                } else {
                                    shipment += '';

                                }
                                shipment += '</tr>';
                                shipment += '</thead>';
                                shipment += '<tbody>';
                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';
                                shipment += '<div class="col-12 mt-2">';
                                shipment += '<div class="border table-responsive">';
                                shipment +=
                                    '<table class="table table-sm table-borderless datatable tracking_history">';
                                shipment += '<thead>';
                                shipment += '<tr role="row">';



                                shipment +=
                                    '<th><strong><select class="form-control" id="shipment_status" name="shipment_status">';
                                shipment += '<option value="">Select Action (*)</option>';

                                @foreach ($shipment_statuses as $status)
                                    shipment +=
                                        '<option value="{{ $status->id }}" id="status_value">{{ $status->name }}</option>';
                                @endforeach
                                shipment +=
                                    '</select></strong><div id="rv_assign_agent_status_error" class="error_message_rv_assign_agent_status error_message"></div></th>';

                                shipment +=
                                    '<th><strong><select class="form-control d-none" id="call_to_id" name="call_to_id" disabled>';
                                shipment += '<option value="">Select Call To</option>';
                                shipment += '<option value="1" selected>Consignee</option>';
                                shipment += '<option value="2">Shipper</option>';


                                shipment += '</select></strong></th>';

                                shipment +=
                                    '<th><strong><select class="form-control" id="shipment_reason" name="shipment_reason">';
                                shipment +=
                                    '</select></strong><div id="rv_assign_agent_sub_status_error" class="error_message_rv_assign_agent_sub_status error_message"></div></th>';


                                shipment +=
                                    '<th><strong><textarea class="form-control form-control-sm" id="shipment_remarks" name="shipment_remarks "rows="2" placeholder="Remarks"></textarea></strong></th>';

                                shipment +=
                                    '</select></strong><div id="shipment_remarks_error" class="error_message_shipment_remarks error_message"></div></th>';




                                shipment += '<div style="margin:15px" class=d-none></div>'
                                shipment += '</tr>';
                                shipment += '</thead>';
                                shipment += '<tbody>';
                                // $.each(details.tracking_history, function (index, history) {
                                //         shipment += '<tr>';

                                //         shipment += '<td>' + ((history.status_reason) ? history.status_reason : '') + '</td>';
                                //         shipment += '<td>' + history.remarks + '</td>';

                                //         shipment += '</tr>';
                                // });

                                shipment += '</tbody>';
                                shipment += '</table>';



                                shipment += '</div>';
                                shipment += '</div>';



                                shipment += '<div class="col-12 mt-2">';
                                shipment += '<div class="border table-responsive">';
                                shipment +=
                                    '<table class="table table-sm table-borderless datatable tracking_history">';
                                shipment += '<thead>';
                                shipment += '<tr role="row">';
                                shipment += '<td class="col-1 pr-0">';
                                shipment += '<label class="mr-1" for="checkbox-id">Fake Status</label>';
                                shipment += '<input type="checkbox" class="checkbox-class" id="checkbox-id">';
                                shipment += '</td>';


                                shipment +=
                                    '<td class="col-5"><strong><select class="form-control" id="fake_status_id" name="fake_status_id" disabled>';
                                shipment += '<option value="">Select Fake Status</option>';

                                @foreach ($fake_status_remarks as $fsk)
                                    shipment +=
                                        '<option value="{{ $fsk->id }}" id="fsk">{{ $fsk->name }}</option>';
                                @endforeach
                                shipment +=
                                    '</select></strong><div id="rv_assign_agent_fake_status_id_error" class="error_message_rv_assign_agent_fake_status_id error_message"></div></td>';

                                shipment += '<td style="border-left: 1px solid black">';
                                shipment += '</td>';

                                shipment += '<td>';

                                shipment += '<div class="row">';
                                shipment += '<div class="col-12">';
                                shipment += '<div class="row">';

                                shipment += '<div class="col-3">';

                                shipment += '<h3><strong>SMS</strong></h3>';
                                shipment += '<div class="d-flex align-item-center" style="margin-right: 200px; ">';
                                shipment += '<h4 class="tsize align-self-center mr-1 mb-0">Off</h4>';

                                shipment += '<div class="" style="margin-top: 5px;">';
                                shipment += '<label class="switch align-self-center">';
                                shipment += '<input type="checkbox" id="tswitch">';
                                shipment += '<span class="slider round"></span>';
                                shipment += '</label>';
                                shipment += '</div>';
                                shipment += '<h4 class="tsize align-self-center mb-0">On</h4>';
                                shipment += '</div>';
                                shipment += '</div>';
                                shipment +=
                                    '<h4 class="tsize align-self-center mr-1 mb-0 d-none" style="margin-top: 30px"; id="scswitch1">Shipper</h4>';
                                shipment += '<div class="" style="margin-top: 37px;">';

                                shipment += '<label class="switch d-none" id="switch2">';
                                shipment += '<input type="checkbox" >';
                                shipment += '<span class="slider round"></span>';
                                shipment += '</label>';
                                shipment += '</div>';
                                shipment +=
                                    '<h4 class="tsize align-self-center mr-1 mb-0 d-none"  style="margin-top: 30px"; id="scswitch">Consignee</h4>';

                                shipment += '</div>';
                                shipment += '</div>';
                                shipment += '<div class="col-12">';

                                shipment +=
                                    '<br/><textarea class="form-control form-control-sm d-none" rows="2" id="tmsg" placeholder="Type Message Here ...."></textarea>';
                                shipment += '</div>';

                                shipment += '</div>';
                                shipment += '</thead>';
                                shipment += '<tbody>';
                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';
                                shipment += '<div class="col-12 mt-2">';
                                shipment += '<div class="border table-responsive">';
                                shipment +=
                                    '<h6 class="text-center" style="text-decoration: underline;" ><strong>Call History</strong></h6>';
                                shipment +=
                                    '<table class="table table-sm table-borderless datatable tracking_history">';
                                shipment += '<thead>';
                                shipment += '<tr role="row">';
                                shipment += '<th>S.no</th>';
                                shipment += '<th>Calling Date</th>';
                                shipment += '<th>Calling Time</th>';
                                shipment += '<th>Call Finding</th>';
                                shipment += '<th>Un-responsive Findings</th>';
                                shipment += '<th>Shipment Status</th>';
                                shipment += '<th>Remarks</th>';
                                shipment += '<th>Status</th>';
                                shipment += '<th>User</th>';
                                shipment += '</tr>';
                                shipment += '</thead>';
                                shipment += '<tbody>';
                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';
                                shipment += '</div>';
                                shipment += '</div>';
                                shipment += '</div>';
                                shipment +=
                                    '<button class="btn btn-primary float-right" style="margin: 10px;">Submit</button>';
                                shipment += '</form>'
                                $('#tracking').append(shipment);
                                scan_sound(1);
                            } else if (data.status == 1) {
                                $('#get_ticket_button').prop('disabled', false);
                                var error = "No Shipment Assigned";
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            } else if (data.status == 3) {
                                window.location.href = "{{ route('agent.login') }}";
                            } else if (data.status == 4) {
                                window.location.href = "{{ route('agent.login') }}";
                            } 
                            else if (data.status == 2) {
                                $('#get_ticket_button').prop('disabled', false);
                                var error = "No Shipment Assigned!";
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }

                            else if (data.status == 5) {
                                $('#get_ticket_button').prop('disabled', false);
                                var error = "No Shipment Found in Assigned Hub";
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        })
                }


                $('#track_form').validate({
                    ignore: [],
                    errorClass: 'danger',
                    successClass: 'success',
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parents('form'));
                    },
                    submitHandler: function(form) {
                        track($(form).find('.tracking_numbers').val());
                        $('#get_ticket_button').prop('disabled', true);
                        $('#horizontal_line').removeClass('d-none');
                        return false;
                    }
                });

                $(document).on('change', '#checkbox-id', function() {
                    if ($(this).is(':checked')) {
                        $('#fake_status_id').prop('disabled', false)
                    } else {
                        $('#fake_status_id').prop('disabled', true)
                        $('#rv_assign_agent_fake_status_id_error').text('');


                    }
                });


                $(document).on('change', '#shipment_reason', function() {
                    if ($("#shipment_reason").is(':empty') === false) {
                        $('.error_message_rv_assign_agent_sub_status').text('')
                    }
                })


                $(document).on('change', '#shipment_remarks', function() {
                    if ($("#shipment_remarks").is(':empty') === false) {
                        $('.error_message_shipment_remarks').text('')
                    }
                })


                $(document).on('change', '#fake_status_id', function() {
                    if ($("#shipment_reason").is(':empty') === false) {
                        $('.error_message_rv_assign_agent_fake_status_id').text('');
                    }
                })


                $(document).on('change', '#shipment_status', function() {
                    $('.error_message_rv_assign_agent_status').text('')
                    $('.error_message_rv_assign_agent_sub_status').text('');
                    // $("#shipment_remarks").removeAttr("style");


                })


                $(document).on('change', '#shipment_status', function() {
                    var id = $(this).val();
                    $.ajax({
                            url: '{!! route('agent.dashboard.get_shipment_reason') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'id': id
                            }
                        })
                        .done(function(data) {
                            if (data.status == 1 && (id == 1 || id == 5)) {
                                var options = '';
                                options += '<option value="">Select Reason (*)</option>';

                                $.each(data.reasons, function(index, reason) {
                                    options += '<option value="' + reason.id + '">' + reason
                                        .name + '</option>';
                                });
                                $('#shipment_reason').html(options);
                                $('#shipment_reason').show().click();


                                if ($("#shipment_status").is(':empty') === false) {
                                    $('.error_message_rv_assign_agent_status').text('')
                                }

                                $('#call_to_id').addClass('d-none');




                            } else if (data.status == 1 && (id == 6)) { //unresponsive
                                var options = '';
                                options += '<option value="">Select Reason</option>';

                                $.each(data.unresponsive_reasons, function(index, reason) {
                                    options += '<option value="' + reason.id + '">' + reason
                                        .remark + '</option>';
                                });
                                $('#shipment_reason').html(options);
                                $('#shipment_reason').show().click();

                                if (id == 6) {
                                    $('#call_to_id').removeClass('d-none');

                                } else {
                                    $('#call_to_id').addClass('d-none');
                                }

                            } else if (data.status == 1 && id == 3) {

                                var sid = $('#shipment_id_val').val();
                                $.ajax({
                                        url: '{!! route('agent.dashboard.get_intercepted_shipment') !!}',
                                        method: 'get',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            'shipment_id': sid
                                        }
                                    })
                                    .done(function(data) {
                                        if (data.status == 1) {

                                            $('input[name="shipment_id"]').val(data.shipment.id);
                                            $('input[name="consignee_name"]').val(data.shipment
                                                .consignee_name);
                                            $('input[name="consignee_phone_number_1"]').val(data
                                                .shipment.consignee_phone_number_1);
                                            $('input[name="consignee_phone_number_2"]').val(data
                                                .shipment.consignee_phone_number_2);
                                            $('input[name="consignee_email"]').val(data.shipment
                                                .consignee_email);
                                            $('input[name="amount"]').val(data.shipment.amount);
                                           

                                            var selectOptions = '';

                                            $.each(data.consignee_cities, function(index, city) {
                                                var option = '<option value="' + city.id + '"';

                                                if (city.id == data.shipment
                                                    .consignee_city_id) {
                                                    option += 'selected';
                                                }

                                                option += '>    ' + city.name + '</option>';

                                                selectOptions += option;
                                            });

                                            if ($("#shipment_status").is(':empty') === false) {
                                                $('.error_message_rv_assign_agent_status').text('')
                                            }



                                            if (data.shipment.booking_type_id === 2) {
                                                $('#replacement_parcel_image_div').addClass('d-none');
                                            }

                                            $('#consignee_city').html(selectOptions);
                                            $('#consignee_address').val(data.shipment
                                                .consignee_address);
                                            $('#shipment_reason').hide();

                                            $('#myModal').modal('show');
                                        }
                                    });

                            } else if (data.status == 1 && id == 3) {
                                $('.error_message_rv_assign_agent_sub_status').text('')
                                $('#shipment_reason').hide();
                                $('#call_to_id').addClass('d-none');

                            } else {
                                $('#shipment_reason').hide();
                                $('#call_to_id').addClass('d-none');

                            }
                        });
                });

                var consignee_city = null;
                var consignee_name = null;
                var consignee_address = null;
                var consignee_phone_number_1 = null;
                var consignee_phone_number_2 = null;
                var intercept_type = null;
                var consignee_email = null;
                var amount = null;
                var imageBase64 = null; 

                $(document).on('click', '#intercept_update', function(event) {
                    event.preventDefault();

                    consignee_city = $('#consignee_city').val();
                    consignee_name = $('#consignee_name').val();
                    consignee_address = $('#consignee_address').val();
                    consignee_phone_number_1 = $('#consignee_phone_number_1').val();
                    consignee_phone_number_2 = $('#consignee_phone_number_2').val();
                    intercept_type = $('#intercept_type').val();
                    consignee_email = $('#consignee_email').val();
                    amount = $('#amount').val();
                    
                    // Get the file input element
                    var inputFile = document.getElementById('replacement_parcel_image');

                    if (inputFile && inputFile.files && inputFile.files.length > 0) {
                        var file = inputFile.files[0];
                        var reader = new FileReader();

                        reader.onloadend = function() {
                            imageBase64 = reader.result;
                        }

                        reader.readAsDataURL(file);
                    }


                    $('#myModal').modal('toggle');
                    $('#shipment_status').prop('disabled', true);
                });


                var checkbox = null;

                $(document).on('submit', '#get_submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission
                    var shipment_status = $('#shipment_status').val();
                    var shipment_reason = $('#shipment_reason').val();
                    var shipment_remarks = $('#shipment_remarks').val();
                    var checkbox = $('#checkbox-id').is(':checked') ? '1' : '0';
                    var fake_status = $('#fake_status_id').val();
                    var call_to_id = $('#call_to_id').val();
                    var shipment_id_val = $("#shipment_id_val").val();
                    var phone_number = $('#phone_number').val();

                    console.log(phone_number)

                    $.ajax({
                            url: '{!! route('agent.dashboard.submit_ticket') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'shipment_id': shipment_id_val,
                                'rv_assign_agent_status_id': shipment_status,
                                'rv_assign_agent_sub_status_id': shipment_reason,
                                'rv_fake_status_id': fake_status,
                                'remarks': shipment_remarks,
                                'is_fake_status': checkbox,
                                'call_to_id': call_to_id,
                                'image':imageBase64,
                                'consignee_city':consignee_city,
                                'consignee_name':consignee_name,
                                'consignee_address':consignee_address,
                                'consignee_phone_number_1':consignee_phone_number_1,
                                'consignee_phone_number_2':consignee_phone_number_2,
                                'intercept_type':intercept_type,
                                'consignee_email':consignee_email,
                                'amount':amount,
                                'phone_number':phone_number

                            }

                        })
                        .done(function(data) {
                            if (data.status == 0) {
                                toastr.success(data.success, {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                                window.location.reload();
                            } else if(data.status == 2){
                                window.location.href = "{{ route('agent.login') }}";
                            }
                          
                            else {
                                var errors = data.errors;
                                $.each(errors, function(field, messages) {
                                    var errorMessage;
                                    console.log(field);
                                    if (field === 'rv_assign_agent_status_id' && $(
                                            "#shipment_status").val() === "") {
                                        errorMessage = '* Action is Required';
                                        $('#rv_assign_agent_status_error').text(errorMessage);
                                        $('#shipment_remarks').css('margin-bottom', '17px');
                                    }

                                    if (field === 'rv_assign_agent_sub_status_id' && $(
                                            "#shipment_reason").val() === "" && $(
                                            "#shipment_status").is(':empty') === false) {
                                        errorMessage = '* Reason is Required';
                                        $('#rv_assign_agent_sub_status_error').text(errorMessage);
                                    }

                                    if (field === 'rv_fake_status_id' && $("#fake_status_id")
                                    .val() === "") {
                                        if (checkbox == 1) {
                                            errorMessage = '* Fake Status is Required';
                                            $('#rv_assign_agent_fake_status_id_error').text(
                                                errorMessage);
                                        }
                                    }

                                    if (field === 'remarks' && $("#shipment_remarks").val() ===
                                        "") {
                                        errorMessage = '* Remarks is Required';
                                        $('#shipment_remarks_error').text(errorMessage);
                                    }

                                    // toastr.error(errorMessage, 'Error!', {
                                    //     positionClass: 'toast-top-center',
                                    //     containerId: 'toast-top-center'
                                    // });
                                });

                            }
                        })

                });
            });
        </script>
    @endsection
