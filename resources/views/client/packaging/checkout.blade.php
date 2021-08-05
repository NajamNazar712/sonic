@extends('client.layout.master')

@section('title', 'Packaging Material Cart')

@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body">
                                @include('client.inc.messages')
                                <form action="{{route('cod.packaging.requests.submit')}}" id="material_request_form" method="post">
                                    @csrf
                                    <input type="hidden" value="{{$cart_count}}" id="cart_count">
                                    <div class="row">
                                        <div class="col">
                                            <div class="col">
                                                <div class="row">
                                                    <div class="col align-middle text-center">
                                                        <h4>Packaging Type</h4>
                                                    </div>
                                                    <div class="col align-middle text-center">
                                                        <h4>Size</h4>
                                                    </div>
                                                    <div class="col align-middle text-center">
                                                        <h4>Quantity</h4>
                                                    </div>
                                                    <div class="col align-middle text-center">
                                                        <h4>Charges</h4>
                                                    </div>
                                                    <div class="col align-middle text-center">

                                                    </div>
                                                </div>
                                            </div>
                                            
                                           
                                            @foreach($cart as $index => $item)
                                            @if ($item->type->status==1)
                                                
                                            
                                            <hr>
                                                <div class="col" id="packaging_{{$item->id}}">
                                                    <input type="hidden" id="size_{{$index}}" name="size[{{$index}}]" value="{{$item->size->id}}">
                                                    <div class="row">
                                                        <div class="col mb-1 align-middle text-center">
                                                            <img class="" alt="flyer" src="{{asset($pictures[$item->type_id])}}" width="100" height="100">
                                                        </div>
                                                        <div class="col mt-2 mb-1 align-middle text-center">
                                                            <p>{{$item->size->size}}</p>
                                                        </div>
                                                        <div class="col mt-2 mb-1 align-middle text-center">
                                                            <div class="row justify-content-center">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control text-center number" id="quantity_{{$index}}" name="quantity[{{$index}}]" value="{{$item->quantity}}" data-rule-min="1" data-msg-min="Quantity can not be less than 1" data-rule-required="true" data-msg-required="Quantity is required">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col mt-2 mb-1 align-middle text-center">
                                                            <div class="row justify-content-center">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control text-center number" readonly id="" name="" value="{{$item->size->standard_charges}}" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col mt-2 mb-1 align-middle text-center">
                                                            <button type="button" class="btn btn-icon btn-danger remove" value="{{$item->id}}"><i class="la la-close"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @endforeach
                                        </div>
                                        <div class="col-4">
                                            <div class="row justify-content-md-center">
                                                <div class="col-12">
                                                    <div class="form-body">
                                                        <div class="row justify-content-center">
                                                            <div class="col">
                                                                <div class="form-group">
                                                                    <select name="address_select" id="address_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="Pickup address is required">
                                                                        <option value="0">New</option>
                                                                        @foreach($address as $pickup)
                                                                            <option value="{{$pickup->id}}">{{$pickup->pickup_address}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-center">
                                                            <div class="col">
                                                                <div id="new_pickup_address" class="d-none">
                                                                    <div class="form-group">
                                                                        <textarea name="new_pickup_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required"></textarea>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <input type="text" name="new_pickup_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required">
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <input type="text" name="new_pickup_phone_number" id="new_pickup_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <select name="new_pickup_city" class="select2" id="new_pickup_city" data-rule-required="true" data-msg-required="City is required">
                                                                            @foreach($cities as $city)
                                                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" id="packaging_size_ids" name="packaging_size_ids">
                                                        <input type="hidden" id="packaging_quantities" name="packaging_quantities">
                                                        <div class="row justify-content-center">
                                                            <div class="col">
                                                                <div class="form-group">
                                                                    <select name="mode_of_payment" class="select2" id="mode_of_payment" data-rule-required="true" data-msg-required="Payment mode is required">
                                                                        <option></option>
                                                                        @foreach($payment_mode as $mode)
                                                                            <option value="{{$mode->id}}">{{$mode->mode}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row justify-content-center">
                                                            <div class="col-md-12 col-lg-6">
                                                                <button id="RequestMaterialBtn" type="submit" class="btn btn-primary btn-block">Request Material</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">

    <style>

    </style>

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $('document').ready(function(){
           
            var total_sizes = $('#cart_count').val();
            $('#address_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Send To*'
            }).bind('change', function() {
                $(this).valid();

                if (this.value == 0) {
                    $('#new_pickup_address').removeClass('d-none');
                }
                else {
                    $('#new_pickup_address').addClass('d-none');
                }
            });
            $('#mode_of_payment').select2({
                width: '100%',
                placeholder: 'Payment Mode'
            });
            $('#new_pickup_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            }).bind('change', function() {
                $(this).valid();
            });

            $('.phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });
            
            $('.number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#material_request_form').validate({
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
                        text: 'Your request is being submitted!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $('.remove').on('click', function (){
                if(total_sizes > 1){
                    total_sizes--;
                    var index = parseInt($(this).val());
                    $.ajax({
                    url: '{!! route('cod.packaging.requests.remove_product') !!}',
                    method: 'POST',
                        data: {
                            'id':index,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                    .done(function(data) {
                        if(data.status){
                            $('#packaging_' + index).remove();
                            var message = 'Packaging Type successfully removed';
                            toastr.success(message, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                        
                    });
                   
                    
                }
                else{
                    var message = 'At least one Packaging type is required';

                    toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                }
            })
        });
    </script>

@endsection