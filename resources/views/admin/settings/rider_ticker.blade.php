@php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
@endphp
@extends('admin.layout.master')
@section('title','Documents')

@section('content')
    <h1 class="mb-1">
        Rider Ticker
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="documents_form" class="form form-horizontal" action="{{route('admin.settings.rider_ticker.store')}}" method="post">
                    @csrf
                    <div class="form-body">
                        <div class="row justify-content-center">
                            <div class="col-4">
                                <table class="table table-sm table-bordered text-center">
                                    <tbody>
                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Image # 1</b></h6></td>
                                        @if(isset($rider_ticker[0]))
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">Delete</button></a></td>
                                        @else
                                            <td class="align-middle"><input class="form-control form-control-sm" type="file" name="upload_image_1"  id="upload_image_1"></td>
                                        @endif
                                    </tr>
                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Image # 2</b></h6></td>
                                        @if(isset($rider_ticker[1]))
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">Delete</button></a></td>
                                        @else
                                            <td class="align-middle"><input class="form-control form-control-sm" type="file" name="upload_image_2"  id="upload_image_2"></td>
                                        @endif
                                    </tr>
                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Image # 3</b></h6></td>
                                        @if(isset($rider_ticker[2]))
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">Delete</button></a></td>
                                        @else
                                            <td class="align-middle"><input class="form-control form-control-sm" type="file" name="upload_image_3"  id="upload_image_3"></td>
                                        @endif
                                    </tr>
                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Image # 4</b></h6></td>
                                        @if(isset($rider_ticker[3]))
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">Delete</button></a></td>
                                        @else
                                            <td class="align-middle"><input class="form-control form-control-sm" type="file" name="upload_image_4"  id="upload_image_4"></td>
                                        @endif
                                    </tr>
                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Image # 5</b></h6></td>
                                        @if(isset($rider_ticker[4]))
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">Delete</button></a></td>
                                        @else
                                            <td class="align-middle"><input class="form-control form-control-sm" type="file" name="upload_image_5"  id="upload_image_5"></td>
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
                    {{--<div class="row justify-content-center">--}}
                    {{--<button type="submit" class="btn btn-primary col-2">--}}
                    {{--Update--}}
                    {{--</button>--}}
                    {{--</div>--}}
                </form>
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
            /*$('#upload_image_1').val("");
            $('#upload_image_2').val("");
            $('#upload_image_3').val("");
            $('#upload_image_4').val("");
            $('#upload_image_5').val("");*/

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
                    var image_2 = $('#upload_image_2').val();
                    var image_3 = $('#upload_image_3').val();
                    var image_4 = $('#upload_image_4').val();
                    var image_5 = $('#upload_image_5').val();

                    if((image_1 !== "" && image_1 != null) || (image_2 !== "" && image_2 != null) || (image_3 !== "" && image_3 != null) || (image_4 !== "" && image_4 != null) || (image_5 !== "" && image_5 != null)){
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

            $('.approve').on('click', function() {
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to approve documents',
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
                        var route = '{!!route('admin.accounts.documents.approve', ['id' => $id, 'approve' => 1, 'reject' => "null"])!!}';
                        window.location.href = route;
                    }
                });
            });
            $('.reject').on('click', function() {
                var reject_reason = $('#reason').val();
                if(reject_reason !== "" && reject_reason !== null){
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to reject documents',
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
                            var route = '{!!route('admin.accounts.documents.approve', ['id' => $id, 'approve' => 0, 'reject'])!!}';
                            route = route.replace("reject", reject_reason);
                            window.location.href = route;
                        }
                    });
                }
                else{
                    var error = 'Reason is required in case of rejection';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }

            });
        });
    </script>
@endsection