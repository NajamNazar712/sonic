@extends('admin.layout.master')

@section('title', 'Human Resources')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Human Resources
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="hr_form" action="{{route('admin.human_resource.fnf.finance.submit')}}" method="post"  novalidate="novalidate">

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

                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">
                                                    Medical Re Imbursement (if any) :
                                                </label>
                                                <input type="text" id="medical" name="medical" class="form-control" placeholder="Amount">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipper_poc">
                                                    Notice Period :
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="notice_period" id="notice_period">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_address">Penalty (if any) :
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="penalty" id="penalty">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Van Deduction:
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="deduction" id="deduction">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
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
                                        <button type="submit" id="submit_hr_info" class="btn btn-primary">Submit</button>
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
            $('#submit_hr_info').on('click',function(){
                if($('#medical').val() == '' || $('#medical').val() == null  && $('#notice_period').val() == '' || $('#notice_period').val() == null && $('#penalty').val() == '' || $('#penalty').val() == null  &&  $('#deduction').val() == '' || $('#deduction').val() == null  &&  $('#comments').val() == '' || $('#comments').val() == null){

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