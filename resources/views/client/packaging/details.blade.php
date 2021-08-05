@extends('client.layout.master')

@section('title', 'Packaging Material Products')

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
                                    <div class="col">
                                        <h1 class="mb-1 ">
                                            @if ($product->category==1)
                                                Packaging Materials
                                            @else
                                                Stationary Items
                                            @endif
                                        </h1>
                                        <div class="row products_row">
                                            <div class="container-fluid" style=" background-color: #fff; padding: 11px;">
                                                <form action="{{route('cod.packaging.requests.add_to_cart')}}" id="add_to_cart_form" method="post">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="type_id" value="{{$product->id}}">
                                                    <div class="row">
                                                        
                                                        <div class="col-lg-4 order-lg-2 order-1">
                                                             
                                                            <div class="image_selected"><img id="xzoom" src="{{asset($picture)}}"
                                                                data-zoom-image="{{asset($picture)}}"/></div>
                                                        </div>
                                                        <div class="col-lg-6 order-3">
                                                            <div class="product_description">
                                                               
                                                                <div class="product_name mt-5 mb-1">{{$product->type}}</div>
                                                                <hr class="singleline">

                                                                <div class="col-4 size_product">
                                                                    <div class="form-group">
                                                                    <select name="product_size" class="select2" id="product_size" data-rule-required="true" data-msg-required="Product Size is required">
                                                                        @foreach ($product->sizes as $size)
                                                                            <option value="{{$size->id}}">{{$size->size}}</option>
                                                                            
                                                                        @endforeach
                                                                    </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row mt-1">
                                                                    <div class="col-xs-6" style="margin-left: 13px;">
                                                                        <div class="product_quantity"> <span>Charges: </span> 
                                                                        <div class="form-group">
                                                                        <input type="text" class="form-control text-center " id="charges" name="charges" readonly data-rule-min="1"  data-rule-required="true" data-msg-required="Charges is required">
                                                                        </div>

                                                                            
                                                                    </div>
                                                                    </div>
                                                                   
                                                                </div>
                                                                
                                                               
                                                                <div class="row mt-1">
                                                                    <div class="col-xs-6" style="margin-left: 13px;">
                                                                        <div class="product_quantity"> <span>QTY: </span> 
                                                                            <div class="form-group">
                                                                                <input type="text" class="form-control text-center number" id="quantity" name="quantity" data-rule-min="1" data-msg-min="Quantity can not be less than 1" data-rule-required="true" data-msg-required="Quantity is required">
                                                                            </div>

                                                                            
                                                                        </div>
                                                                    </div>
                                                                   
                                                                </div>
                                                                <hr class="singleline">

                                                                <div class="mt-1">
                                                                    <div class="col-xs-6"> 
                                                                        <button type="submit" name="add_cart" class="btn btn-primary shop-button" value="Add to Cart">Add to Cart</button>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-1">
                                                                    <div class="col-xs-6"> 
                                                                        <button type="submit" name="checkout" class="btn btn-success shop-button" value="Check Out">Check Out </button>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                </div>
                                        </div>
                                    </div>
                                    <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="proceed_cart">
            <form action="{{route('cod.packaging.requests.checkout')}}" id="material_request_cart_form" method="get">
                <input type="hidden" id="size_ids" name="size_ids" value="{{$count}}">
                <div class="display-inline-block">
                    <button type="submit" class="col btn btn-dark width" title="Checkout"><i class="la la-shopping-cart" style="font-size:24px"></i><span class='badge badge-warning' id='cart_count'> {{$count}} </span></button>
                </div>
            </form>
        </div>
    </section>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
    
    
    <style>
        #proceed_cart {
            position: fixed;
            top: 120px;
            padding-right: 80px;
            left: 0;
            width: 100%;
            text-align: right;
        }
        .la {
            transform: scale(1.5,1.5);
        }
        .badge {
            padding-left: 9px;
            padding-right: 9px;
            -webkit-border-radius: 9px;
            -moz-border-radius: 9px;
            border-radius: 9px;
        }

        .label-warning[href],
        .badge-warning[href] {
            background-color: #c67605;
        }
        #cart_count {
            font-size: 12px;
            background: #ff0000;
            color: #fff;
            padding: 0 5px;
            vertical-align: top;
            margin-left: -10px;
        }
        .products_row{
            margin-top: 50px;
        }
    

.single_product {
    padding-top: 66px;
    padding-bottom: 140px;
    background-color: #e5e5e5;
    margin-top: 0px;
    padding: 17px
}

.product_name {
    font-size: 20px;
    font-weight: 400;
    margin-top: 0px
}


.product_price {
    display: inline-block;
    font-size: 30px;
    font-weight: 500;
    margin-top: 9px;
    clear: left
}



.image_selected {
    border: solid 1px #e8e8e8;
    box-shadow: 0px 0px 0px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    padding: 15px
}


@charset "utf-8";
@import url('https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,800,900|Rubik:300,400,500,700,900');


.single_product {
    padding-top: 16px;
    padding-bottom: 140px
}


.image_selected img {
    max-width: 100%
}


.order_info {
    margin-top: 16px
}


.size_product{
    padding-left: 0;
}
.shop-button{
    padding: 8px 58px;
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

    <script type="text/javascript" src="https://cdn.rawgit.com/igorlino/elevatezoom-plus/1.1.6/src/jquery.ez-plus.js"></script>


    <script type="text/javascript">
        $('document').ready(function(){
            
            var sizes = [];
            // $('.add_to_cart').on('click', function(){
            //     sizes.push(this.value);
            //     var count = sizes.length;
            //     $('#cart_count').text(count);
            //     $(this).addClass('d-none');
            // });
           
            $('#product_size').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Size*'
            }).bind('change', function() {

                var size_id = $(this).val();
                $.ajax({
                    url: '{!! route('cod.packaging.requests.get_charges') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'size_id': size_id
                    }})
                    .done(function(data) {
                        if (data.status == 0) {
                            $('#charges').val(data.charges);
                        }
                                      
                    });
            });

            $('.number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#material_request_cart_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    if($('#size_ids').val() > 0){
                        form.submit();
                    }
                    else{
                        var error = 'At least one size should be selected to proceed';
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
            });

            $('#add_to_cart_form').validate({
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

            $("#xzoom").ezPlus({
    zoomType: 'inner',
    cursor: 'crosshair',
    
    
});
        });
    </script>

@endsection