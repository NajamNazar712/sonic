@extends('client.layout.master')

@section('title', 'Resources')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    Resources
                </h1>
                    <div class="card">
                        <div class="card">
                            <div class="card-content" aria-expanded="true">
                                <div class="card-body">
                                    @include('client.inc.messages')
                                    <div class="card text-white box-shadow-0 bg-gradient-directional-info">
                                        <div class="card-header">
                                            <h4 class="card-title text-white">API Documentation</h4>
                                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                            <div class="heading-elements">
                                                <ul class="list-inline mb-0">
                                                    <li><a href="{{ asset('file/documents/API Document - SONIC Version 2.0.pdf') }}" class="btn btn-secondary round btn-min-width mr-1 mb-1" target="_blank"> <i class=" ft-download"></i> Download</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-content collapse show">
                                            <div class="card-body">
                                                <p class="card-text">This is the document which would help you to understand the process and the methods to integrate our APIs for booking a shipment, tracking a shipment, and finding information about destinations, rates, shipment charges etc.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card text-white box-shadow-0 bg-gradient-x2-pink">
                                        <div class="card-header">
                                            <h4 class="card-title text-white">Claim Policy Document</h4>
                                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                            <div class="heading-elements">
                                                <ul class="list-inline mb-0">
                                                    <li><a href="{{ asset('file/documents/Trax-Claim Policy Version 1.1.pdf') }}" class="btn btn-secondary round btn-min-width mr-1 mb-1"> <i class=" ft-download"></i> Download</a></li>

                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-content collapse show">
                                            <div class="card-body">
                                                <p class="card-text">This is the claim policy which would help you understand the terms and conditions for claiming damages or losses during the commencement of delivery services.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card text-white box-shadow-0 bg-gradient-y-success">
                                        <div class="card-header">
                                            <h4 class="card-title text-white">Network List</h4>
                                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                            <div class="heading-elements">
                                                <ul class="list-inline mb-0">
                                                    <li><a href="javascript:void(0);" class="btn btn-secondary round btn-min-width mr-1 mb-1 city_list_download"> <i class=" ft-download"></i> Download</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-content collapse show">
                                            <div class="card-body">
                                                <p class="card-text">This is the list of destinations where TRAX is currently operating for the delivery of shipments.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                                           <div class="card text-white box-shadow-0 bg-gradient-y-warning">
                                    <div class="card-header">
                                        <h4 class="card-title text-white">Shopfiy Plugin</h4>
                                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                        <div class="heading-elements">
                                            <ul class="list-unstyled mb-0 text-center">
                                                <li><a id="shopfiy_link" onClick="setShopifyLink();" class="btn btn-secondary round btn-min-width mr-1 mb-1"> <i class=" ft-download"></i> Install</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <p class="card-text">With the help of this video you can learn how to install Shopify plugin</p>
                                                    <a class="white" href="https://youtu.be/naeczv-jQYM" target="_blank"><i class="la la-youtube-play align-text-bottom"></i> Shopify Plugin Tutorial</a>
                                                </div>
                                                <div class="col-4">
                                                    <ul class="list-inline text-right">
                                                        <li><input id="shopify_text" class="form-control display-inline" type="text" placeholder="Shopify Store Name"></li>
                                                        <li>.myshopify.com</li>
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                 <div class="card text-white box-shadow-0 bg-gradient-y-primary">
                                    <div class="card-header">
                                        <h4 class="card-title text-white">Wordpress Plugin</h4>
                                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                        <div class="heading-elements">
                                            <ul class="list-inline mb-0">
                                                <li><a href="{{ asset('file/documents/trax-plugin-wordpress 1.8.zip') }}" class="btn btn-secondary round btn-min-width mr-1 mb-1"> <i class=" ft-download"></i> Download</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body">
                                            <p class="card-text">With the help of this video you can learn how to install Wordpress plugin</p> 
                                            <a class="white" href="https://www.youtube.com/watch?v=-r7j3VGHQRg" target="_blank"><i class="la la-youtube-play align-text-bottom"></i> Wordpress Plugin Tutorial</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card text-white box-shadow-0 bg-gradient-directional-info">
                                    <div class="card-header">
                                        <h4 class="card-title text-white">Magento Plugin</h4>
                                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                        <div class="heading-elements">
                                            <ul class="list-inline mb-0">
                                                <li><a href="{{ asset('file/documents/trax-sonic-magento-plugin-v5.zip') }}" class="btn btn-secondary round btn-min-width mr-1 mb-1 city_list_download"> <i class=" ft-download"></i> Download</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body">
                                            <p class="card-text">With the help of this video you can learn how to install Magento plugin</p> 
                                            <a class="white" href="https://www.youtube.com/watch?v=Tkz3eb_pZZw" target="_blank"><i class="la la-youtube-play align-text-bottom"></i> Magento Plugin Tutorial</a>
                                        </div>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $(document).ready(function () {
            $('li a.city_list_download').on('click', function () {
                $(this).attr('disabled', true);
                window.open('{!! route('cod.resources.city_list') !!}', '_blank');

            })
        });

        var shopify_text,shopfiy_link;

        function setShopifyLink(){
        shopify_text= document.getElementById('shopify_text').value;
        if(!shopify_text)
        {
            var error = 'Please enter Shopify Store Name';
            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
        }
        else
        {
            shopfiy_link="https://s-app-sonic.trax.pk?shop="+shopify_text+'.myshopify.com';
            window.open(shopfiy_link);
            document.getElementById('shopify_text').value="";
        }
        
    }



    </script>

@endsection
