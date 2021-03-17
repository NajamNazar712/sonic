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
                <form id="documents_form" class="form form-horizontal" action="#">
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
                                            <td class="align-middle"><input class="form-control form-control-sm" type="file" name="image1"  id="image1"></td>
                                        @endif
                                    </tr>
                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Image # 2</b></h6></td>
                                        @if(isset($rider_ticker[1]))
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">Delete</button></a></td>
                                        @else
                                            <td class="align-middle"><input class="form-control form-control-sm" type="file" name="image2"  id="image2" value=""></td>
                                        @endif
                                    </tr>
                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Image # 3</b></h6></td>
                                        @if(isset($rider_ticker[2]))
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">Delete</button></a></td>
                                        @else
                                            <td class="align-middle"><input class="form-control form-control-sm" type="file" name="image3"  id="image3" value=""></td>
                                        @endif
                                    </tr>
                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Image # 4</b></h6></td>
                                        @if(isset($rider_ticker[3]))
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">Delete</button></a></td>
                                        @else
                                            <td class="align-middle"><input class="form-control form-control-sm" type="file" name="image4"  id="image4" value=""></td>
                                        @endif
                                    </tr>
                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Image # 5</b></h6></td>
                                        @if(isset($rider_ticker[4]))
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => 1,'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">Delete</button></a></td>
                                        @else
                                            <td class="align-middle"><input class="form-control form-control-sm" type="file" name="image5"  id="image5" value=""></td>
                                        @endif
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row justify-content-center mt-4">
                            <div class="mr-1">
                                <button type="button" class="btn btn-outline-primary mr-1 upload">Upload</button>
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
            $('#image1').val("");
            $('#image2').val("");
            $('#image3').val("");
            $('#image4').val("");
            $('#image5').val("");
            $('.upload').on('click', function() {
                var image_1 = $('#image1').val();
                var image_2 = $('#image2').val();
                var image_3 = $('#image3').val();
                var image_4 = $('#image4').val();
                var image_5 = $('#image5').val();
                console.log(image_2);
                console.log(image_3);
                if(image_1 != '' || image_2 != '' || image_3 != '' || image_4 != '' || image_5 != ''){
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to upload documents',
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
                            $.ajax({
                                url: '{!! route('admin.settings.rider_ticker.store') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'image_1': image_1,
                                    'image_2': image_2,
                                    'image_3': image_3,
                                    'image_4': image_4,
                                    'image_5': image_5
                                }
                            }).done(function(data){
                                if(data.status){
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    window.location.reload();
                                }else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                        }
                    });
                }
                else{
                    var error = 'No file Selected';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }


                $.ajax({
                    url: '{!! route('admin.accounts.documents.edit') !!}',
                    method: 'POST',
                    data: {
                        'user_id': user_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        // console.log(data);
                        // console.log(data.user_attachment.blank_cheque_image);
                        $('#old_filled_and_signed_pdf').text(data.user_attachment.filled_and_signed_pdf);
                        $('#old_signed_acknowledgement_pdf').text(data.user_attachment.signed_acknowledgement_pdf);
                        $('#old_cnic_front_image').text(data.user_attachment.cnic_front_image);
                        $('#old_cnic_back_image').text(data.user_attachment.cnic_back_image);
                        $('#old_blank_cheque_image').text(data.user_attachment.blank_cheque_image);
                        $('#upload_modal').modal('show');
                    }
                    else{
                        $('#old_filled_and_signed_pdf').text("");
                        $('#old_signed_acknowledgement_pdf').text("");
                        $('#old_cnic_front_image').text("");
                        $('#old_cnic_back_image').text("");
                        $('#old_blank_cheque_image').text("");
                        $('#upload_modal').modal('show');
                    }
                });

            });

            $('#upload_modal').on('hide.bs.modal', function (e) {
                $('#upload_documents_form')[0].reset();
                $('#filled_and_signed_pdf').val('');
                $('#signed_acknowledgement_pdf').val('');
                $('#cnic_front_image').val('');
                $('#cnic_back_image').val('');
                $('#blank_cheque_image').val('');

            });
            var user_id = {!! $id !!};
            $('button.confirm').on('click', function(){
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to confirm all documents uploaded!',
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
                        $.ajax({
                            url: '{!! route('admin.accounts.documents.confirm') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'user_id': user_id
                            }
                        }).done(function(data){
                            if(data.status){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                window.location.reload();
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                    }
                });
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
                    var filled_and_signed_pdf = $('#filled_and_signed_pdf').val();
                    var signed_acknowledgement_pdf = $('#signed_acknowledgement_pdf').val();
                    var cnic_front_image = $('#cnic_front_image').val();
                    var cnic_back_image = $('#cnic_back_image').val();
                    var blank_cheque_image = $('#blank_cheque_image').val();
                    if(filled_and_signed_pdf !== '' || signed_acknowledgement_pdf !== '' || cnic_front_image !== '' || cnic_back_image !== '' || blank_cheque_image !== ''){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to upload documents',
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