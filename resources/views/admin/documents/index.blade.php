@extends('admin.layout.master')

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
                                @include('admin.inc.messages')
                                <div class="card text-white box-shadow-0 bg-gradient-directional-info">
                                    <div class="card-header">
                                        <h4 class="card-title text-white">API Documentation</h4>
                                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                        <div class="heading-elements">
                                            <ul class="list-inline mb-0">
                                                <li><a href="{{ asset('file/documents/API Document - SONIC Version 2.0.pdf') }}" class="btn btn-secondary round btn-min-width mr-1 mb-1"> <i class=" ft-download"></i> Download</a></li>
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
                                                <li><a href="{{ asset('file/documents/SLGTRAX Claim Policy.pdf') }}" class="btn btn-secondary round btn-min-width mr-1 mb-1"> <i class=" ft-download"></i> Download</a></li>

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
                                                <li>
                                                    <a href="javascript:void(0);" class="btn btn-secondary round btn-min-width mr-1 mb-1 city_list_download" id="city_list_download"> 
                                                        <i class=" ft-download"></i>
                                                        Download
                                                    </a>
                                                </li>
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
                                                <li><a target="_blank" href="https://apps.shopify.com/sonic-trax" class="btn btn-secondary round btn-min-width mr-1 mb-1"> <i class=" ft-download"></i> Click here to Install</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <p class="card-text">With the help of this video you can learn how to install Shopify plugin</p>
                                                    <a class="white" href="https://www.youtube.com/watch?v=GtMJ1lGete4" target="_blank"><i class="la la-youtube-play align-text-bottom"></i> Shopify Plugin Tutorial</a>
                                                </div>
                                                <div class="col-4">
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
                                                <li><a href="{{ asset('file/documents/trax-plugin-wordpress-2.2.3.zip') }}?vv-2.2.3.1" class="btn btn-secondary round btn-min-width mr-1 mb-1"> <i class=" ft-download"></i> Download</a></li>
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

                {{-- <div class="modal fade" id="user_data_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel">User Information</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="select2Form">
                                    <div class="form-group">
                                        <label for="user_dropdown">Select Shippers</label>
                                        <select name="search_shipper[]" id="user_dropdown" class="form-control select2" multiple>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <a href="javascript:void(0);" class="btn btn-secondary round btn-min-width mr-1 user_city_list_download" id="user_city_list_download"> 
                                    <i class=" ft-download"></i>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div> --}}

            </div>
        </div>
    </div>

@endsection
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">

<style>
    #toast-container > .toast-error {
        font-size: 2rem;
        width: 46%;
    }

    #toast-container{
        width: 65%;
    }

    #user_data_modal{
        margin: 100px 0px 0px 0px;
    }
</style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('li a.city_list_download').on('click', function () {
                $(this).attr('disabled', true);
                window.open('{!! route('admin.resources.city_list') !!}', '_blank');
            });
        });


    </script>

@endsection
