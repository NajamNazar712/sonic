@extends('retail.layout.master')

@section('title', 'Booking Form')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="card">
                <div class="card-content" aria-expanded="true">
                    <div class="card-body">
                        @include('retail.inc.messages')
                        <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('retail.shipment.book.store') }}" novalidate="novalidate">
                            {{ csrf_field() }}
                            <div class="row">
                                <div id="consignment_info" class="col-3">
                                    <h4 id="shipper_header_info" class="form-section mb-2 text-center">Consignment Info</h4>
                                    <div class="form-group">
                                        <select name="product" id="product" class="select2 form-control">
                                            @foreach($products as $product)
                                                <option value="{{$product->id}}">{{$product->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input name="shipper_account_no" class="form-control shipper_account_no" id="shipper_account_no" placeholder="Shipper Account No" value="">
                                    </div>
                                    <div class="form-group">
                                        <select name="business_category" id="business_category" class="select2 form-control">
                                            @foreach($business_categories as $business_category)
                                                <option value="{{$business_category->id}}">{{$business_category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select name="shipping_mode" id="shipping_mode" class="select2 form-control">
                                            @foreach($shipping_modes as $shipping_mode)
                                                <option value="{{$shipping_mode->id}}">{{$shipping_mode->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="consignee_shipper_info" class="col-6">
                                    <h4 id="shipper_header_info" class="form-section mb-2 text-center">Consignee & Shipper Info</h4>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/require.js/2.3.6/require.min.js" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/echarts/echarts.common.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/chartjs/chart.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#product').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Shipment*",
                allowClear:true
            });
            $('#business_category').select2({
                width:'100%',
                placeholder:"Select Shipment Category*"
            });
            $('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Product*",
                allowClear:true
            });
        });
    </script>
@endsection