@extends('client.layout.master')

@section('title', 'Packaging Material')

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
                                <div class="col-10">
                                    @include('client.inc.messages')
                                </div>
                                    <div class="col">
                                        <h1 class="mb-1 text-center">
                                            Material Request
                                        </h1>
                                        <div class="row text-center categories_row">

                                                    <div class="col-6">
                                                        <div class="col mb-1 text-center border border-3">
                                                            <img class="category_banner" alt="packaging" src="{{asset('img/packages_category.png')}}" >
                                                        </div>
                                                        <div class="col mb-1 text-center">
                                                            <a href="{{ route('cod.packaging.requests.category', ['id' => 1]) }}" class="btn btn-outline-primary select_category"><b>Packaging Materials</b></a>
                                                        </div>
                                                    </div>
                                                   
                                                    <div class="col-6">
                                                        <div class="col mb-1 text-center border border-3">
                                                            <img class="category_banner" alt="stationary" src="{{asset('img/stationary_category.png')}}" >
                                                        </div>
                                                        <div class="col mb-1 text-center">
                                                            <a href="{{ route('cod.packaging.requests.category', ['id' => 2]) }}" class="btn btn-outline-primary select_category"><b>Stationary Items</b></a>
                                                        </div>
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
                <input type="hidden" id="size_ids" name="size_ids" >
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
        .categories_row{
            margin: 120px 20px;
        }
        .category_banner{
            width: 500px;
            height: 230px;
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



    <script type="text/javascript">
        $('document').ready(function(){
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
            // $('.add_to_cart').on('click', function(){
            //     sizes.push(this.value);
            //     var count = sizes.length;
            //     $('#cart_count').text(count);
            //     $(this).addClass('d-none');
            // });

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

            
        });
    </script>

@endsection