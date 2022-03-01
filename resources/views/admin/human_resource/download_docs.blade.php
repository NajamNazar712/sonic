@extends('admin.layout.master')

@section('title', 'Documents')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    Download Documents
                </h1>
                    <div class="card">
                        <div class="card">
                            <div class="card-content" aria-expanded="true">
                                <div class="card-body">
                                    @include('admin.inc.messages')
                                    <div class="card text-white box-shadow-0 bg-gradient-directional-info">
                                        <div class="card-header">
                                            <h4 class="card-title text-white">Interview Evaluation Form</h4>
                                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                            <div class="heading-elements">
                                                <ul class="list-inline mb-0">
                                                    <li><a href="{{ asset('file/documents/interview_evaluation_form.pdf') }}" class="btn btn-secondary round btn-min-width mr-1 mb-1" download><i class="ft-download"></i> Download</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-content collapse show">
                                            <div class="card-body">
                                                <p class="card-text">This is the document for Interview Evaluation Form</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card text-white box-shadow-0 bg-gradient-y-success">
                                        <div class="card-header">
                                            <h4 class="card-title text-white">Employee Requisition Form</h4>
                                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                            <div class="heading-elements">
                                                <ul class="list-inline mb-0">
                                                    <li><a href="{{ asset('file/documents/employee_requisition_form.pdf') }}" class="btn btn-secondary round btn-min-width mr-1 mb-1" download><i class="ft-download"></i> Download</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-content collapse show">
                                            <div class="card-body">
                                                <p class="card-text">This is the document for Employee Requisition Form.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card text-white box-shadow-0 bg-gradient-y-primary">
                                        <div class="card-header">
                                            <h4 class="card-title text-white">Clearance Certificate Form</h4>
                                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                            <div class="heading-elements">
                                                <ul class="list-inline mb-0">
                                                    <li><a href="{{ asset('file/documents/clearance_certificate_form.pdf') }}" class="btn btn-secondary round btn-min-width mr-1 mb-1" download><i class="ft-download"></i> Download</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-content collapse show">
                                            <div class="card-body">
                                                <p class="card-text">This is the document for Clearance Certificate Form.</p> 
                                              </div>
                                        </div>
                                    </div>

                                    
                                    <div class="card text-white box-shadow-0 bg-gradient-y-warning">
                                        <div class="card-header">
                                            <h4 class="card-title text-white">Travel Form</h4>
                                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                            <div class="heading-elements">
                                                <ul class="list-inline mb-0">
                                                    <li><a href="{{ asset('file/documents/travel_form.pdf') }}" class="btn btn-secondary round btn-min-width mr-1 mb-1" download><i class="ft-download"></i> Download</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-content collapse show">
                                            <div class="card-body">
                                                <p class="card-text">This is the document for Travel Form.</p> 
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
            shopfiy_link="https://shopify-sonic.trax.pk?shop="+shopify_text+'.myshopify.com';
            window.open(shopfiy_link);
            document.getElementById('shopify_text').value="";
        }
        
    }



    </script>

@endsection
