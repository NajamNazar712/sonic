@php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
@endphp
@extends('admin.layout.master')
@section('title','Background Image')

@section('content')
   
    <h1 class="mb-1">Background Image</h1>
    @include('admin.inc.messages')

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-header"><h2 class="mb-1">Background Image</h2></div>
            <div class="card-body">
                <form id="documents_form" class="form form-horizontal" action="{{route('admin.settings.background_image.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <div class="row justify-content-center">
                            <div class="col-4">
                                <table class="table table-sm table-bordered text-center" id="Image_table">
                                    <tbody>
                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Image # 1</b></h6></td>
                                        <td class="align-middle"><input class="form-control form-control-sm" type="file" name="upload_image_1"  id="upload_image_1"></td>
                                        @if(isset($background_image[0]))
                                            <input type="hidden" class="form-control form-control-sm" name="background_image_id_1" id="background_image_id" value="{{$background_image[0]->id}}">
                                            <td class="align-middle view" ><a class="white" ><button type="button" class="btn btn-primary btn-sm" data-link="{{asset(Storage::url($background_image[0]->picture_path))}}">View</button></a></td>
                                        @endif

                                    </tr>
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row justify-content-center mt-4">
                            <div class="mr-1">
                                <button type="submit" class="btn btn-outline-primary mr-1 upload">Upload</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
  
    <div class="modal fade" id="picture_modal" data-backdrop="static" role="dialog" aria-labelledby="picture_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="picture_modal_title">Picture</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/selectize/selectize.css')}}">
@endsection


@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $('#Image_table tbody').on('click','tr td.view button',function () {
                var link = $(this).attr('data-link');

                var image = '<img src="' + link + '" style="width: 100%; max-width: 350px;" />';

                $('#picture_modal .modal-body').html(image);

                $('#picture_modal').modal('show');
            });

            $( "#documents_form" ).validate({
                errorClass:"danger",
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var image_1 = $('#upload_image_1').val();
                    if((image_1 !== "" && image_1 != null)){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to upload Image(s)',
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function (confirm) {
                            if(confirm){
                                form.submit();
                            }
                        });
                    }
                    else{
                        var error = 'No file Selected';
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
            });
        });
    </script>
@endsection