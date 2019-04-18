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
                                                    <li><a href="{{ asset('file/documents/APIDocumentation-SONIC-v1.5.pdf') }}" class="btn btn-secondary round btn-min-width mr-1 mb-1"> <i class=" ft-download"></i> Download</a></li>
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

                                </div>
                            </div>
                        </div>
                    </div>

            </div>
        </div>
    </div>

@endsection
@section('css')

@endsection

@section('js')



    <script type="text/javascript">
        $(document).ready(function () {
            $('li a.city_list_download').on('click', function () {
                $(this).attr('disabled', true);
                $.ajax({
                    url:'{!! route("cod.resources.city_list") !!}',
                }).done(function (data) {
                    if(data.status){
                        function download(filename) {
                            var a = document.createElement("a");
                            a.href = filename;
                            // a.setAttribute("download", filename);
                            a.click();
                            return false;
                        }

                        download(data.file_name);
                    }

                })
            })
        });

    </script>

@endsection
