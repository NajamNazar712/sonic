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
                                    <div class="col-2">
                                        @include('client.inc.messages')
                                    </div>
                                    <div class="col">
                                        <div class="row">
                                            
                                        </div>

                                        <h1 class="mb-1 ">
                                            @if ($product->category==1)
                                                Packaging Materials
                                            @else
                                                Stationary Items
                                            @endif
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
                                                                    <option value="{{route('cod.packaging.requests.product',['id'=>$shipper_packaging_type->packaging_material->id])}}">{{ $shipper_packaging_type->packaging_material->type }}</option>
                                                                @endforeach
                                                            @endif
                                                        @endif
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="row products_row">
                                            <div class="container-fluid" style=" background-color: #fff; padding: 11px;">
                                                <form action="{{route('cod.packaging.requests.add_to_cart')}}" id="add_to_cart_form" method="post">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="type_id" value="{{$product->id}}">
                                                    <div class="row">
                                                        <div class="col-lg-2 order-lg-1 order-2">
                                                            <ul class="image_list">
                                                                <li class="other_images" data-image="{{asset($picture1)}}"><img src="{{asset($picture1)}}" alt=""></li>
                                                                <li class="other_images" data-image="{{asset($picture2)}}"><img src="{{asset($picture2)}}" alt=""></li>
                                                                <li class="other_images" data-image="{{asset($picture3)}}"><img src="{{asset($picture3)}}" alt=""></li>
                                                                <li class="other_images" data-image="{{asset($picture4)}}"><img src="{{asset($picture4)}}" alt=""></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-lg-4 order-lg-2 order-1">
                                                             
                                                            <div class="image_selected">
                                                                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel"  data-interval="false"  data-pause="hover">
                                                                    <ol class="carousel-indicators" style="top: 300px;">
                                                                      <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active bg-primary"></li>
                                                                      <li data-target="#carouselExampleIndicators" data-slide-to="1" class="bg-primary"></li>
                                                                      <li data-target="#carouselExampleIndicators" data-slide-to="2" class="bg-primary"></li>
                                                                      <li data-target="#carouselExampleIndicators" data-slide-to="3" class="bg-primary"></li>
                                                                      <li data-target="#carouselExampleIndicators" data-slide-to="4" class="bg-primary"></li>
                                                                    </ol>
                                                                    <div class="carousel-inner">
                                                                      <div class="carousel-item active">
                                                                        <img class="d-block w-100" src="{{asset($picture)}}" alt="First slide">
                                                                      </div>
                                                                      <div class="carousel-item">
                                                                        <img class="d-block w-100" src="{{asset($picture1)}}" alt="Second slide">
                                                                      </div>
                                                                      <div class="carousel-item">
                                                                        <img class="d-block w-100" src="{{asset($picture2)}}" alt="Third slide">
                                                                      </div>
                                                                      <div class="carousel-item">
                                                                        <img class="d-block w-100" src="{{asset($picture3)}}" alt="Fourth slide">
                                                                      </div>
                                                                      <div class="carousel-item">
                                                                        <img class="d-block w-100" src="{{asset($picture4)}}" alt="Fifth slide">
                                                                      </div>
                                                                    </div>
                                                                  </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 order-3">
                                                            <div class="product_description">
                                                               
                                                                <div class="product_name mt-5 mb-1">{{$product->type}}</div>
                                                                <hr class="singleline">

                                                                
                                                                
                                                                <div class="col-4 size_product">
                                                                        
                                                                        <div class="radio-toolbar">
                                                                            @foreach ($product->sizes as $size)
                                                                                @if($size->id != 1)
                                                                                <input type="radio" id="size_btn_{{$size->id}}" name="product_size" value="{{$size->id}}" checked>
                                                                                <label for="size_btn_{{$size->id}}">{{$size->size}}</label>
                                                                                @endif
                                                                            @endforeach
                                                                           
                                                                        </div>
                                                                        <p>&nbsp;</p>
                                                                    {{-- <select name="product_size" class="select2" id="product_size" data-rule-required="true" data-msg-required="Product Size is required">
                                                                        @foreach ($product->sizes as $size)

                                                                            <option value="{{$size->id}}">{{$size->size}}</option>
                                                                            
                                                                        @endforeach
                                                                    </select> --}}
                                                                </div>
                                                                <div class="col-4 size_product">
                                                                    <div class="form-group">
                                                                     <h4>Price In PKR: <span id="charges_show">{{$size_price}}</span></h4>
                                                                     <input type="hidden" class="form-control text-center " id="charges" name="charges" readonly data-rule-min="1"  data-rule-required="true" data-msg-required="Charges is required">

                                                                    </div>
                                                                </div>
                                                                {{-- <div class="col-4 size_product">
                                                                    <div class="product_quantity"> <span>Charges: </span> 
                                                                        <div class="form-group">
                                                                        <input type="hidden" class="form-control text-center " id="charges" name="charges" readonly data-rule-min="1"  data-rule-required="true" data-msg-required="Charges is required">
                                                                        </div>
                                                                    </div>
                                                                </div> --}}
                                                                <div class="col-4 size_product">
                                                                    <div class="product_quantity"> 
                                                                        
                                                                        <div class="form-group input-group item_quantity_div">
                                                                            <input type="text" class="form-control text-center number quantity" id="quantity" placeholder="Quantity*" name="quantity" data-rule-min="1" data-msg-min="Quantity can not be less than 1" data-rule-required="true" data-msg-required="Quantity is required">
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
                                                                        <button type="submit" name="checkout" class="btn btn-success checkout-button" value="Check Out">Check Out </button>
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
                <input type="hidden" id="size_ids" name="size_ids">
                <div class="display-inline-block">
                    <button type="submit" class="col btn btn-dark width" title="Checkout"><i class="la la-shopping-cart" style="font-size:24px"></i><span class='badge badge-warning' id='cart_count'></span></button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/spinner/jquery.bootstrap-touchspin.css')}}">
    
    
    <style>
        #proceed_cart {
            position: absolute;
            top: 46px;
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
    font-size: 34px;
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
    border: solid 1px #070707;
    border-radius: 15px;
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
.checkout-button{
    padding: 8px 62px;
}

.image_list li {
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 120px;
    border: solid 1px #070707;
    border-radius: 15px;
    box-shadow: 0px 0px 0px rgba(0, 0, 0, 0.1) !important;
    margin-bottom: 15px;
    cursor: pointer;
    padding: 15px;
    -webkit-transition: all 200ms ease;
    -moz-transition: all 200ms ease;
    -ms-transition: all 200ms ease;
    -o-transition: all 200ms ease;
    transition: all 200ms ease;
    overflow: hidden
}


.single_product {
    padding-top: 66px;
    padding-bottom: 140px;
    background-color: #e5e5e5;
    margin-top: 0px;
    padding: 17px
}

.image_selected {
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: calc(100% + 15px);
    height: 525px;
    -webkit-transform: translateX(-15px);
    -moz-transform: translateX(-15px);
    -ms-transform: translateX(-15px);
    -o-transform: translateX(-15px);
    transform: translateX(-15px);
    border: solid 1px #070707;
    border-radius: 15px;
    box-shadow: 0px 0px 0px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    padding: 15px
}



ul {
    list-style: none;
    margin-bottom: 0px
}

.single_product {
    padding-top: 16px;
    padding-bottom: 140px
}

.image_list li {
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 120px;
    border: solid 1px #070707;
    border-radius: 15px;
    box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 15px;
    cursor: pointer;
    padding: 15px;
    -webkit-transition: all 200ms ease;
    -moz-transition: all 200ms ease;
    -ms-transition: all 200ms ease;
    -o-transition: all 200ms ease;
    transition: all 200ms ease;
    overflow: hidden
}

.image_list li:last-child {
    margin-bottom: 0
}

.image_list li:hover {
    box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.3)
}

.image_list li img {
    max-width: 100%
}


.image_selected {
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: calc(100% + 15px);
    height: 525px;
    -webkit-transform: translateX(-15px);
    -moz-transform: translateX(-15px);
    -ms-transform: translateX(-15px);
    -o-transform: translateX(-15px);
    transform: translateX(-15px);
    border: solid 1px #070707;
    border-radius: 15px;
    box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    padding: 15px
}

.image_selected img {
    max-width: 100%
}

/* radio button  */


.radio-toolbar {
  margin: 10px;
}

.radio-toolbar input[type="radio"] {
  opacity: 0;
  position: fixed;
  width: 0;
}

.radio-toolbar label {
    display: inline-block;
    background-color: #fefefe;
    padding: 3px 15px;
    font-family: sans-serif, Arial;
    font-size: 16px;
    border: 1px solid #444;
    border-radius: 4px;
}

.radio-toolbar label:hover {
  background-color: #64a0d2;
}

.radio-toolbar input[type="radio"]:focus + label {
    border: 2px dashed #444;
}

.radio-toolbar input[type="radio"]:checked + label {
    background-color: #5587b4 ;
    border-color: #3c5a78;
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

    <script type="text/javascript" src="https://cdn.rawgit.com/igorlino/elevatezoom-plus/1.1.6/src/jquery.ez-plus.js"></script>


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


            $(".other_images").click(function(param) {
                // var image = $(this).html();
                  $('#xzoom').attr('src',$(this).attr('data-image'));
                  $('#xzoom').attr('data-zoom-image',$(this).attr('data-image'));
                  
                //   var img = document.createElement($(this).html());

                //   console.log(img);
            });
            $.ajax({
                       url: '{!! route('cod.packaging.requests.get_cart_count') !!}',
                       method: 'POST',
                       data: {
                           '_token': '{{ csrf_token() }}'
                       }
                   }).done(function (data) {
                        if(data.status == 0){
                            $('#size_ids').val(data.count);
                            $('#cart_count').html(data.count);
                            
                        }
                   });
            var sizes = [];
            $('input:radio[name=product_size]').change(function() {
                var size_id = $(this).val();
                $('#charges_show').html(" ");

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
                            $('#charges_show').html(data.charges);
                            
                            
                        }
                                      
                    });
            });

            $('.number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
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

//             $("#xzoom").ezPlus({
//     zoomType: 'inner',
//     cursor: 'crosshair',
    
    
// });
        });
    </script>

@endsection