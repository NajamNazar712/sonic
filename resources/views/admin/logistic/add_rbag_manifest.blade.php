@extends('admin.layout.master')

@section('title', 'R Bag Manifest')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">R Bag Manifest</h1>
            
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="logistic_booking_form" class="form-horizontal" method="POST" action="" novalidate="novalidate">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-2  pr-0">
                                        <div class="form-group">
                                            <label>Manifest#</label>
                                            <input type="text" name="cn_number" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-2  pr-0">
                                        <div class="form-group">
                                            <label>Origin</label>
                                            <input type="text" name="cn_number" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-2  pr-0">
                                        <div class="form-group">
                                            <label>Destination</label>
                                            <input type="text" name="cn_number" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-2  pr-0">
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" name="cn_number" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-2 pr-0">
                                        <div class="form-group">
                                            <label>RM Bag Barcode #</label>
                                            <input type="text" name="cn_number" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-2 ">
                                        <div class="form-group">
                                            <label>Manifest Type</label>
                                            <input type="text" name="cn_number" class="form-control" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    
                                    <div class="col-md-2 pr-0">
                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <input type="text" name="cn_number" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-0">
                                        <div class="form-group">
                                            <label>R-Bag Barcode#</label>
                                            <input type="text" name="cn_number" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Red-Bag Seal#</label>
                                            <input type="text" name="cn_number" class="form-control" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                  <div class="col-md-12">
                                    <h4 style="color: black"><b>Consignments</b></h4>
                                  </div>
                                    <div class="col-md-12">
                                        <table class="table col-md-12">
                                            <thead class="bg-primary white">
                                               <tr>
                                                    <th scope="col">Serial</th>
                                                    {{-- <th scope="col">Consignment#</th> --}}
                                                    <th scope="col">Origin</th>
                                                    <th scope="col">Destination</th>
                                                    <th scope="col">P-Bag#/Open Shipment</th>
                                                    <th scope="col">SRV#</th>
                                                    <th scope="col">Handling Instructions</th>
                                                    <th scope="col">Weight (KG)</th>
                                                    <th scope="col">Pcs</th>
                                                    <th scope="col">Pcs From Seq#</th>
                                                    <th scope="col">Pcs To Seq#</th>
                                                    <th scope="col">Actions</th>
                                               </tr>
                                            </thead>
                                            <tbody>
                                                    <tr scope="col">
                                                        <td>1</td>
                                                        <td>KHI-Karachi</td>
                                                        <td>KHI-Karachi</td>
                                                        <td>8856</td>
                                                        <td>88888</td>
                                                        <td>Fragile,Heavy</td>
                                                        <td>500</td>
                                                        <td>8</td>
                                                        <td>1</td>
                                                        <td>20</td>
                                                        <td class="text-center"><span class="danger" style="cursor: pointer"><i class="la la-trash m-0 "></i></span></td>
                                                    </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="item_box">
                                            <div class="item_detail">
                                                <div class="row">
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
                                                            <label>P-Bag#/Open Shipment</label>
                                                            <input type="text" name="length[]" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 pr-0">
                                                        <div class="form-group">
                                                            <label>SRV#</label>
                                                            <input type="text" name="weight[]" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 pr-0">
                                                        <div class="form-group">
                                                            <label>Handling Instructions</label>
                                                            <input type="text" name="weight[]" class="form-control"   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 pr-0">
                                                        <div class="form-group">
                                                            <label>Weight (KG)</label>
                                                            <input type="text" name="weight[]" class="form-control"   >
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
                                                            <label>Pcs From Seq#</label>
                                                            <input type="text" name="weight[]" class="form-control"   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 pr-0">
                                                        <div class="form-group">
                                                            <label>Pcs To Seq#</label>
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
       //add row in item btn
    $('.add_row_item_btn').click(function(){
        var row=' <div class="row"> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="height[]" class="form-control"> </div> </div> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="width[]" class="form-control"> </div> </div> <div class="col-md-2 pr-0"> <div class="form-group"> <input type="text" name="length[]" class="form-control"> </div> </div> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="weight[]" class="form-control"> </div> </div> <div class="col-md-2 pr-0"> <div class="form-group"> <input type="text" name="weight[]" class="form-control" > </div> </div> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="weight[]" class="form-control" > </div> </div> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="weight[]" class="form-control" > </div> </div> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="weight[]" class="form-control" > </div> </div> <div class="col-md-1 pr-0"> <div class="form-group"> <input type="text" name="weight[]" class="form-control" > </div> </div> <div class="col-md-1"> <div class="form-group"> <span class="btn btn-danger btn-sm remove_row_btn"><i class="la la-times m-0"></i></span> </div> </div> </div> ';
        $('.item_detail').append(row);
    });
    //remove row in item
    $('body').on('click','.remove_row_btn',function(){
        $(this).closest('.row').remove();
    });
</script>
@endsection