@extends('admin.layout.master')

@section('title', 'Finance')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                  Finance
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="finance_form" action="{{route('admin.human_resource.fnf.finance.submit')}}" method="post"  novalidate="novalidate">

                                @csrf
                                @method('post')
                                <input type="hidden" name="fnf_id" value="{{$fnf->id}}">
                                <fieldset>
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">
                                                    Name:
                                                </label>
                                                <input type="text" id="name" name="overtime" class="form-control" placeholder="Name" value="{{$employee->name}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">
                                                    Designation:
                                                </label>
                                                <input type="text" id="designation" name="overtime" class="form-control" placeholder="Designation" value="{{$employee->designation->name}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">
                                                    Department:
                                                </label>
                                                <input type="text" id="department" name="department" class="form-control" placeholder="Department" value="{{$employee->department->name}}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="text-center mt-2 mb-2"><strong>Deductions</strong></h3>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">
                                                    Advance Salary :
                                                </label>
                                                <input type="text" id="salary" name="salary" class="form-control" placeholder="Amount">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipper_poc">
                                                    Loan Outstanding :
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="loan" id="loan">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_address">Short Cash:
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="cash" id="cash">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">COD Recovery:

                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="cod" id="cod">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Temporary Advance (IOU):

                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="iou" id="iou">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">I. Tax (if any):

                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="tax" id="tax">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <textarea cols="50"  class="form-control" rows="5" id="comments" name="comments" placeholder="Comments"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="row justify-content-center">
                                    <div class="col-md-6 text-center">
                                        <button type="submit" id="submit_finance_info" class="btn btn-primary">Submit</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {
            $('#submit_finance_info').on('click',function(){
                if($('#salary').val() == '' || $('#salary').val() == null  && $('#loan').val() == '' || $('#loan').val() == null && $('#cod').val() == '' || $('#cod').val() == null  &&  $('#cash').val() == '' || $('#cash').val() == null  &&  $('#iou').val() == '' || $('#iou').val() == null && $('#tax').val() == '' || $('#tax').val() == null &&  $('#comments').val() == '' || $('#comments').val() == null){

                    var error = "At-least fill one field";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                    return false;
                }
                else{
                    form.submit()
                }
            });
        });
    </script>
@endsection