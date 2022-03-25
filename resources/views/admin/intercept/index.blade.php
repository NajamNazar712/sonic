@extends('admin.layout.master')
@section('title','Intercept/Re-Book to New Destination')

@section('content')
    <h1 class="mb-1 mt-1">
        Intercept/Re-Book to New Destination
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="text-center mb-2">
                    <h4><b>Tracking Number: {{$shipment->tracking_number}}</b></h4>
                </div>

                <form id="intercept_form" class="form-horizontal" method="post" action="{{route('admin.intercept.update')}}" enctype="multipart/form-data">
                @csrf
                    <div class="form-group col-md-3  mb-2 text-center" style="margin: auto;">
                        <select name="consignee" class="select2" id="consignee" data-rule-required="true" data-msg-required="Consignee is required">
                            <option value="1" selected>Different Consignee</option>
                            <option value="2">Same Consignee</option>
                        </select>
                    </div>
                    <input type="hidden" name="shipment_id" value="{{$shipment['id']}}">
                    <div class="row justify-content-center">
                        <div class="col col_custom mr-5">
                            <h4 class="form-section mb-2 text-center">Consignee Information</h4>
                            <div class="form-group">
                                <select name="consignee_city" class="select2" id="consignee_city" data-rule-required="true" data-msg-required="City is required">
                                    @foreach($consignee_cities as $city)
                                        @if($city->id == $shipment['consignee_city_id'])
                                            <option value="{{ $city->id }}" selected>{{ $city->name }}</option>
                                        @else
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" name="consignee_name" id="consignee_name" class="form-control" value="{{$shipment['consignee_name']}}" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required" data-rule-maxlength="100" data-msg-maxlength="Name can be maximum 100 characters">
                            </div>

                            <div class="form-group">
                                <textarea id="consignee_address" name="consignee_address" class="form-control" rows="6" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required" data-rule-maxlength="255" data-msg-maxlength="Address can be maximum 255 characters">{{$shipment['consignee_address']}}</textarea>
                            </div>

                            <div class="form-group">
                                <input type="text" name="consignee_phone_number_1" class="form-control phone_number" value="{{$shipment['consignee_phone_number_1']}}" placeholder="Phone Number 1*" data-rule-required="true" data-msg-required="Phone Number is required">
                            </div>

                            <div class="form-group">
                                <input type="text" name="consignee_phone_number_2" class="form-control phone_number" value="{{$shipment['consignee_phone_number_2']}}" placeholder="Phone Number 2">
                            </div>
                                <input type="text" name="intercept_type" id="intercept_type" class="form-control hidden" placeholder="Phone Number 2">

                            <div class="form-group">
                                <input type="email" name="consignee_email" id="consignee_email" class="form-control" value="{{$shipment['consignee_email']}}" placeholder="Email Address" data-rule-maxlength="100" data-msg-maxlength="Email Address can be maximum 100 characters">
                            </div>

                            @if($shipment->booking_type_id == 2)
                                <div class="form-group d-none" id="replacement_parcel_image_div">
                                    <label class="d-block bold">Replacement Parcel Image</label>
                                    <input class="form-control form-control-sm" type="file" name="replacement_parcel_image"  id="replacement_parcel_image">
                                </div>
                            @endif
                        </div>
                        <div class="col col_custom">
                            <h4 class="form-section mb-2 text-center">Payment Information</h4>
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rs</span>
                                </div>

                                <input type="text" name="amount" class="form-control rounded-right amount" value="{{$shipment['amount']}}" placeholder="Collection Amount*" data-rule-required="true" data-msg-required="Collection Amount is required" id="amount">
                            </div>
                           
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <div class="form-group text-center">
                                <button type="submit" name="update" class="btn btn-primary width-10-per" value="Book">Update</button>
                            </div>
                        </div>
                    </div>
                </form>

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

    <script type="text/javascript">
        $(document).ready(function () {
            var consignee_name = @json($shipment['consignee_name']);
            var city = @json($shipment['consignee_city_id']);
            var email = @json($shipment['consignee_email']);
            var amount = @json($shipment['amount']);

            $('#consignee_city').select2({
                width: '100%',
                placeholder: 'City*'
            });
            $('#consignee').select2({
                width: '100%',
                placeholder: 'Consignee*'
            }).bind('change', function () {
              if(this.value == 2){
                  $('#consignee_city').val(city).trigger('change');
                  var hiddenInput = $('<input/>' , {type : 'hidden' , name: 'consignee_city' , value : $('#consignee_city').val(), id : 'new_city' });
                  $('#intercept_form').append( hiddenInput );  //append the hidden field with same name and value from the dropdown field
                  $('#intercept_type').val(2);
                  $('#consignee_city').addClass('disabled')  //disable class
                      .prop({'name' : 'new_consignee_city'  , disabled : true}); //change name and disbale
                  $( "#consignee_name" ).val(consignee_name);
                  $( "#consignee_name" ).prop('readonly', true);
                  $( "#consignee_email" ).val(email);
                  $( "#consignee_email" ).prop('readonly', true);
                  $( "#amount" ).val(amount);
                  $( "#amount" ).prop('readonly', true);
                  $("#replacement_parcel_image_div").removeClass("d-none");
              }
              else{
                  $( "#consignee_name" ).prop('readonly', false);
                  $( "#consignee_email" ).prop('readonly', false);
                  $('#intercept_type').val(1);
                  $( "#amount" ).prop('readonly', false);
                  $('#intercept_form').find('#new_city').remove(); // remove the hidden fields if any
                  $('#consignee_city').removeClass('disabled')  //remove disable class
                      .prop({name : 'consignee_city' , disabled : false}); //restore the name and enable
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
            $('#intercept_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                        // var form = this;
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to Update shipment!',
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
                        }).then(function (confirm) {
                            if (confirm) {
                                form.submit();
                            }
                        });
                }
            });
        });
        </script>
@endsection