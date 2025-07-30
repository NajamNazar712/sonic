@extends('admin.layout.master')

@section('title', 'Shipment Manifest')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">Shipment Manifest</h1>
            
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="shipment_manifest_form" class="form-horizontal" method="POST" action="{{ route('admin.logistic.shipment_manifest.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-2  pr-0">
                                        <div class="form-group">
                                            <label>Manifest#</label>
                                            <input type="text" name="pbag_manifest_no" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-2  pr-0">
                                        <div class="form-group">
                                            <label>Origin</label>
                                            <select class="select select2 mb-1" name="origin" id="origin_select">
                                                @foreach ($cities as $city)
                                                     <option value="{{ $city->id }}">{{  $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2  pr-0">
                                        <div class="form-group">
                                            <label>Destination</label>
                                            <select class="select select2 mb-1" name="destination" id="destination_select">
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}">{{  $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2  pr-0">
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" name="pbag_date" class="form-control rounded-right pbag_date" id="pickup_datepicker" value="{{ Carbon\Carbon::today()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 pr-0">
                                        <div class="form-group">
                                            <label>Product</label>
                                            <select class="select select2 mb-1" id="product_select" name="product_id">
                                                @foreach ($products as $product)
                                                     <option value="{{ $product->id }}">{{  $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 ">
                                        <div class="form-group">
                                            <label>Type</label>
                                            <input type="text" name="pbag_type" class="form-control" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    
                                    <div class="col-md-2 pr-0">
                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <input type="text" name="quantity" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-2 pr-0">
                                        <div class="form-group">
                                            <label>Barcode Manifest#</label>
                                            <input type="text" name="barcode_mfst_no" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Sack Bag</label>
                                            <input type="text" name="sack_bag_no" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Consignment Test</label>
                                            <input type="text" name="consign_test" id="consign_test_id" class="form-control" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                  <div class="col-md-12">
                                    <h4 style="color: black"><b>Consignments</b></h4>
                                  </div>
                                    <div class="col-md-12">
                                        <table class="table col-md-12" id="consignment_details">
                                            <thead class="bg-primary white">
                                               <tr>
                                                    <th scope="col">Serial</th>
                                                    <th scope="col">Consignment#</th>
                                                    <th scope="col">Origin</th>
                                                    <th scope="col">Destination</th>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">Service</th>
                                                    <th scope="col">Handling Instruction</th>
                                                    <th scope="col">Pcs</th>
                                                    <th scope="col">Weight (KG)</th>
                                                    <th scope="col" colspan="2">Actions</th>
                                               </tr>
                                            </thead>
                                            <tbody>
                                                    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="item_box">
                                            <div class="item_detail">
                                                <div class="row">
                                                    <div class="col-md-2 pr-0">
                                                        <div class="form-group">
                                                            <label>Consignment#</label>
                                                            <input type="text" name="no_piece[]" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 pr-0">
                                                        <div class="form-group">
                                                            <label>Origin</label>
                                                            <input type="text" name="height[]" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 pr-0">
                                                        <div class="form-group">
                                                            <label>Destination</label>
                                                            <input type="text" name="width[]" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 pr-0">
                                                        <div class="form-group">
                                                            <label>Service</label>
                                                            <input type="text" name="length[]" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 pr-0">
                                                        <div class="form-group">
                                                            <label>Handling Instructions</label>
                                                            <input type="text" name="weight[]" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 pr-0">
                                                        <div class="form-group">
                                                            <label>Pcs</label>
                                                            <input type="text" name="weight[]" class="form-control"   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 pr-0">
                                                        <div class="form-group">
                                                            <label>Product</label>
                                                            <input type="text" name="weight[]" class="form-control"   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 pr-0">
                                                        <div class="form-group">
                                                            <label>Weight (KG)</label>
                                                            <input type="text" name="weight[]" class="form-control"   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-group">
                                                            <label>Action</label>
                                                        </div>
                                                    </div>
                                                </div>
                                              
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group" ><span class="btn btn-primary float-right add_row_item_btn ">Add Row</span></div>
                                                </div>
                                            </div>
                                        </div>
                                       
                                    </div>
                                </div> --}}
                                <div class="row mt-2">
                                    <div class="col-md-10"></div>
                                    <div class="col-md-2 ">
                                        <button type="submit" name="submit" id="add" class="btn btn-primary w-50 float-left mr-1" value="submit">Submit</button>
                                        <button type="submit" name="cancel" id="cancel" class="btn btn-danger " value="cancel">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/spinner/jquery.bootstrap-touchspin.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
<style>
    .item_box{
        border: 1px solid lightgrey;
        padding-top: 13px;
        padding-left: 10px;
        padding-right: 10px;
        padding-bottom: 9px;
    }
    #consignment_details td{
        padding: 0.75rem!important;
    }
    #consignment_details thead th{
        padding: 0.75rem 1rem!important;
    }
    .width-8{
        width: 8%;
    }
    .custmarginleft-20{
        margin-right: 20px;
    }
    .cursorpointer{
        cursor: pointer;
    }
    .padding-top10{
        padding-top: 10px;
    }
    .width15{
        width: 15%;
    }
    .width11{
        width: 11%;
    }
</style>
@endsection

@section('js')
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
<script>

    $(document).ready(function(){

        $('#origin_select').prepend('<option value="" selected="selected">Select Origin</option>').select2({
           width: '100%',
           placeholder: 'Select Origin'
        });
        $('#consign_origin_select').prepend('<option value="" selected="selected">Select Origin</option>').select2({
           width: '100%',
           placeholder: 'Select Origin'
        });

        
        $('#destination_select').prepend('<option value="" selected="selected">Select Destination</option>').select2({
           width: '100%',
           placeholder: 'Select Destination'
        });
        $('#consign_dest_select').prepend('<option value="" selected="selected">Select Destination</option>').select2({
           width: '100%',
           placeholder: 'Select Destination'
        });
        

        $('#product_select').prepend('<option value="" selected="selected">Select Product</option>').select2({
           width: '100%',
           placeholder: 'Select Product'
        });
        $('#consign_product_select').prepend('<option value="" selected="selected">Select Product</option>').select2({
           width: '100%',
           placeholder: 'Select Product'
        });

        $('#consign_service_select').prepend('<option value="" selected="selected">Select Product</option>').select2({
           width: '100%',
           placeholder: 'Select Service'
        });
        
        $('#pickup_datepicker').pickadate({
                firstDay: 1,
                clear: '',
                min: '{{ Carbon\Carbon::today() }}',
                // max: '{{ Carbon\Carbon::today() }}',
                // format: 'dd mmmm, yyyy',
                // value:'{{ Carbon\Carbon::today() }}',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
        });

    });
       //add row in item btn
    $('.add_row_item_btn').click(function(){
        var row='<div class="row"> <div class="col-md-2 pr-0"> <div class="form-group"> <input type="text" name="consig_no[]" class="form-control " > </div> </div> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="origin[]" class="form-control " > </div> </div> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="destination[]" class="form-control " > </div> </div> <div class="col-md-2 pr-0"> <div class="form-group"> <input type="text" name="service[]" class="form-control " > </div> </div> <div class="col-md-2 pr-0"> <div class="form-group"> <input type="text" name="handle_inst[]" class="form-control " > </div> </div> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="no_pcs[]" class="form-control " > </div> </div> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="product[]" class="form-control " > </div> </div> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="weight[]" class="form-control " > </div> </div> <div class="col-md-1"> <div class="form-group"> <div class="form-group"> <span class="btn btn-danger btn-sm remove_row_btn"><i class="la la-times m-0"></i></span> </div> </div> </div> </div>';
        $('.item_detail').append(row);
    });
    //remove row in item
    $('body').on('click','.remove_row_btn',function(){
        $(this).closest('.row').remove();
    });

    var cities=@json($cities);
    var products=@json($products);
    var services=@json($services);
    var shipment_ids=[];
    $("#consign_test_id").on('change',function(){
            var cn_number=$(this).val();
            if(cn_number!='' && cn_number>0)
            {
                    $.ajax({
                    url: '{{ route('admin.logistic.shipment', ['cn_number' => '']) }}/' + cn_number,
                    method: 'GET',
                    timeout: 10000,
                    error: function (data) {
                        toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    },
                    success: function (data) {
                        if(data.status == 0){
                            var logisticshipment=data.logisticshipment;
                            var index=$.inArray(logisticshipment.id,shipment_ids);
                            if(index === -1){
                                shipment_ids.push(logisticshipment.id);
                                $("#consignment_details").append('<tr scope="col"> <td class="text-center "><p class="padding-top10">1</p></td> <td><input type="text" name="consign_cn_number[]" id="consign_cn_number" value="'+logisticshipment.cn_number+'" class="form-control "></td> <td class="width15"> <input type="text" name="consign_origin" id="consign_origin_input" value="'+logisticshipment.origin_name+'" class="form-control"> </td> <td class="width15"> <input type="text" name="consign_destination" id="consign_destination_input" value="'+logisticshipment.destination_name+'"  class="form-control"> </td> <td class="width11"> <input type="text" name="consign_product" id="consign_product_input" value="'+logisticshipment.product_name+'" class="form-control"> </td> <td class="width11"> <input type="text" name="consign_service" id="consign_service_input" value="'+logisticshipment.service_name+'" class="form-control"> </td> <td><input type="text" name="consign_handling_inst" value="'+logisticshipment.handling_inst+'"  class="form-control"></td> <td><input type="text" name="consign_no_piece"  value="'+logisticshipment.total_pieces+'" class="form-control"></td> <td><input type="text" name="consign_weight" value="'+logisticshipment.booking_weight+'" class="form-control"></td> <td class="width-8"><span class="success cursorpointer custmarginleft-20" style="cursor: pointer"><i class="la la-check m-0 "></i></span><span class="danger cursorpointer" ><i class="la la-trash m-0 "></i></span></td> </tr>');
                                scan_sound(1);
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }else{
                                scan_sound(2);
                                toastr.error('Already Exist Shipment', 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                            }
                        }
                        else{
                            scan_sound(2);
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }
                });
            }
            
    });
</script>
@endsection