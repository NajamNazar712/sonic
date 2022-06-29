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
                                <div class="col-6">
                                    @include('client.inc.messages')
                                </div>
                                <h1 class="mb-1">
                                    Material Request
                                </h1>
                                <div class="col-6 align-middle text-center search_style">
                                    <form id="search_package_type_from">
                                        <div class="form-group mb-0">
                                            <select name="search_package_type" id="search_package_type" class="form-control select2">
                                                @if(isset($search_packaging_types))
                                                @foreach($search_packaging_types as $search_packaging_type)
                                                    <option value="{{route('cod.packaging.requests.product',['id'=>$search_packaging_type->id])}}">{{ $search_packaging_type->type }}</option>
                                                @endforeach
                                                @if (count($shipper_packaging_types)>0)
                                                    @foreach($shipper_packaging_types as $shipper_packaging_type)
                                                        @if ($shipper_packaging_type->packaging_material->status != 0)
                                                            <option value="{{route('cod.packaging.requests.product',['id'=>$shipper_packaging_type->packaging_material->id])}}">{{ $shipper_packaging_type->packaging_material->type }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endif
                                            </select>
                                        </div>
                                    </form>
                                </div>
                                <br>
                                <form action="{{route('cod.packaging.requests.submit')}}" id="material_request_form" method="post">
                                    @csrf
                                    <input type="hidden" value="{{$cart_count}}" id="cart_count">
                                    <div class="row">
                                        <div class="col">
                                            <div class="col">
                                                <div class="row">
                                                    <div class="col align-middle text-left">
                                                        <h4>Products</h4>
                                                    </div>
                                                    
                                                    <div class="col align-middle text-center">

                                                    </div>
                                                </div>
                                            </div>
                                            
                                           
                                            @foreach($cart as $index => $item)
                                            @if ($item->type->status==1)
                                                
                                            <div id="packaging_{{$item->id}}">

                                            
                                            <hr>
                                                <div class="col">
                                                    <input type="hidden" id="size_{{$index}}" name="size[{{$index}}]" value="{{$item->size->id}}">
                                                    <input type="hidden" id="types_{{$index}}" name="types[{{$index}}]" value="{{$item->type->id}}">
                                                    <div class="row">
                                                        <div class="col mb-1 align-middle text-center border border-2 box_padding">
                                                            <img class="" alt="flyer" src="{{asset($pictures[$item->type_id])}}" width="120" height="120">
                                                        </div>
                                                        <div class="col mt-2 mb-1 align-middle text-left pad_left">
                                                            <h4 class="font-weight-bold">{{ucfirst($item->type->type)}}</h4>
                                                            <h5>Size: {{$item->size->size}}</h5>
                                                            <h5>Rs. {{$item->size->standard_charges}}</h5>
                                                            <h5>Quantity:</h5>
                                                            <div class="form-group input-group item_quantity_div">
                                                                <input type="text" class="form-control text-center number quantity" id="quantity_{{$item->id}}" placeholder="Quantity*" name="quantity[{{$index}}]" value="{{$item->quantity}}" data-rule-min="1" data-msg-min="Quantity can not be less than 1" data-rule-required="true" data-msg-required="Quantity is required">
                                                            </div>
                                                            {{-- <p>{{$item->size->size}}</p> --}}
                                                        </div>
                                                        <div class="col mt-2 mb-1 align-middle text-center">
                                                            <div class="row justify-content-center">
                                                                <div class="col-10">

                                                                    <div class="form-group">
                                                                        {{-- <input type="text" class="form-control text-center number" id="quantity_{{$index}}" name="quantity[{{$index}}]" value="{{$item->quantity}}" data-rule-min="1" data-msg-min="Quantity can not be less than 1" data-rule-required="true" data-msg-required="Quantity is required"> --}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col mt-2 mb-1 align-middle text-center">
                                                            <button type="button" class="btn btn-icon btn-danger remove" value="{{$item->id}}"><i class="la la-close"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                        <div class="col-4">
                                            <div class="row">
                                                <div class="col align-middle text-center">
                                                    <h4>Consignee Details</h4>
                                                </div>
                                                
                                                <div class="col align-middle text-center">

                                                </div>
                                            </div>
                                            <hr>
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
                                                                        <input type="text" name="new_pickup_reference_id" id="new_pickup_reference_id" class="form-control" placeholder="Reference ID">
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/spinner/jquery.bootstrap-touchspin.css')}}">

    <style>
        .box_padding{
            padding-top: 15px;
            padding-bottom: 15px;
            border-radius: 15px;
        }
        .pad_left{
            padding-left: 50px;
        }
        .search_style{
            margin:0 auto;
        }
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
    <script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $('document').ready(function(){
            $('#search_package_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder:'Search',
                dropdownParent:$('#search_package_type_from')
            }).bind('select2:select',function () {
                var url = $(this).val();
                window.location.href = url;
            });
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
            });
            $('.quantity').TouchSpin({
                min: 1,
                max: 10000,
                buttondown_class: 'btn btn-primary rounded-left',
                buttonup_class: 'btn btn-primary rounded-right',
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            }).bind('input change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });
        });
    </script>

@endsection