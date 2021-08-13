@extends('admin.layout.master')

@section('title', ' IT Support')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                  IT Support
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="it_support_form"  method="post" novalidate="novalidate">
                                @csrf
                                @method('post')
                                <input type="hidden" name="fnf_id" value="{{$fnf->id}}">
                                <input type="hidden" name="approval" id="approval">
                                <fieldset>
                                    @include('admin.human_resource.fnf.employee_data')
                                   <div class="row justify-content-center">
                                       <div class="col-md-6 text-center">
                                           <div class="form-group">
                                               <div>
                                                   <textarea cols="50"  class="form-control" rows="5" id="comments" name="comments" placeholder="Comments">{{$support->comments ?? ''}}</textarea>
                                               </div>
                                           </div>
                                       </div>
                                   </div>

                                </fieldset>
                                <div class="row justify-content-center">
                                    <div class="col-md-6 text-center">
                                        @if($support == Null )
                                            <button type="button" id="submit_support_info" class="btn btn-primary">Submit</button>
                                        @endif
                                        @if($support != Null && $support->status_id == 1 ||  $support->status_id == 3 )
                                            <button type="button" id="approve" class="btn btn-success" value="Approve">Approve</button>
                                        @endif
                                        @if($support != Null && $support->status_id == 1)
                                            <button type="button" id="reject" class="btn btn-danger" value="Reject">Reject</button>
                                        @endif
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
            $('#submit_support_info').on('click',function(){
                if($('#comments').val() == '' || $('#comments').val() == null  ){

                    var error = "Comments Section Cannot be Empty";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                    return false;
                }
                else{
                    var route = '{{route('admin.human_resource.fnf.it_support.submit')}}';
                    $('#rm_form').attr('action', route);
                    $('#rm_form').submit()
                }
            });

            $('#approve').on('click',function(){
                var route = '{{route("admin.human_resource.fnf.it_support_status_edit")}}';
                $('#it_support_form').attr('action', route);
                $('#approval').val('approved');
                $('#it_support_form').submit()
            });

            $('#reject').on('click',function(){
                var route = '{{route("admin.human_resource.fnf.it_support_status_edit")}}';
                $('#it_support_form').attr('action', route);
                $('#approval').val('rejected');
                $('#it_support_form').submit()
            });
        });
    </script>
@endsection