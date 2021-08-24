@php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
@endphp
@extends('admin.layout.master')
@section('title','Documents')

@section('content')
    <h1 class="mb-1">
        Documents - {{$shipper}}
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
                                            <td class="align-middle"><h6><b>Pdf of filled and signed document:</b></h6></td>
                                            @if($documents && $documents->filled_and_signed_pdf != null)
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => $id, 'check' => 'filled_and_signed_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            @else
                                            <td class="align-middle">-</td>
                                            @endif
                                        </tr>
                                        <tr style="height: 50px">
                                            <td class="align-middle"><h6><b>Pdf of signed Acknowledgement form:</b></h6></td>
                                            @if($documents && $documents->signed_acknowledgement_pdf != null)
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => $id, 'check' => 'signed_acknowledgement_pdf', 'pdf' => 1])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            @else
                                                <td class="align-middle">-</td>
                                            @endif
                                        </tr>
                                        <tr style="height: 50px">
                                            <td class="align-middle"><h6><b>Picture of CNIC (Front):</b></h6></td>
                                            @if($documents && $documents->cnic_front_image != null)
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => $id, 'check' => 'cnic_front_image', 'pdf' => 0])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            @else
                                                <td class="align-middle">-</td>
                                            @endif
                                        </tr>
                                        <tr style="height: 50px">
                                            <td class="align-middle"><h6><b>Picture of CNIC (Back):</b></h6></td>
                                            @if($documents && $documents->cnic_back_image != null)
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => $id, 'check' => 'cnic_back_image', 'pdf' => 0])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            @else
                                                <td class="align-middle">-</td>
                                            @endif
                                        </tr>
                                        <tr style="height: 50px">
                                            <td class="align-middle"><h6><b>Picture of Blank cheque:</b></h6></td>
                                            @if($documents && $documents->blank_cheque_image != null)
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => $id, 'check' => 'blank_cheque_image', 'pdf' => 0])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            @else
                                                <td class="align-middle">-</td>
                                            @endif
                                        </tr>
                                        <tr style="height: 50px">
                                            <td class="align-middle"><h6><b>Picture of E Signature:</b></h6></td>
                                            @if($documents && $documents->e_sign_image != null)
                                            <td class="align-middle"><a class="white" href="{{route('admin.accounts.documents.view', ['id' => $id, 'check' => 'e_sign_image', 'pdf' => 0])}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                            @else
                                                <td class="align-middle">-</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if(session('role_id') == 1 || in_array(277, session('permissions')) || in_array(278, session('permissions')))
                            <div class="row justify-content-center mt-4">
                                @if($document_status == 0 || $document_status == 3)
                                    @if(session('role_id') == 1 || in_array(277, session('permissions')))
                                        <div class="mr-1">
                                            <button type="button" class="btn btn-outline-primary mr-1 edit">Edit</button>
                                        </div>
                                    @endif
                                    
                                        @if($documents && $documents->filled_and_signed_pdf != null && $documents->signed_acknowledgement_pdf != null  && $documents->cnic_front_image != null  && $documents->cnic_back_image != null  && $documents->blank_cheque_image != null)
                                        <div class="mr-1">
                                                <button type="button" class="btn btn-outline-success mr-1 confirm">Confirm</button>
                                        </div>
                                        @endif
                                @elseif($document_status == 2)
                                @if(session('role_id') == 1 || in_array(277, session('permissions')))
                                        <div class="mr-1">
                                            <button type="button" class="btn btn-outline-primary mr-1 edit">Edit</button>
                                        </div>
                                    @endif
                                @elseif($document_status == 1)
                                    @if(session('role_id') == 1 || in_array(278, session('permissions')))
                                        <div class="mr-1">
                                            <button type="button" class="btn btn-success approve">Approve</button>
                                        </div>
                                        <form id="reject_reason">
                                            <div class="form-group mr-1">
                                                <textarea class="form-control" id="reason" name="reason" placeholder="Reject reason"></textarea>
                                            </div>
                                        </form>
                                        <div class="">
                                            <button type="button" class="btn btn-danger reject">Reject</button>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
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


    <div class="modal fade" id="upload_modal" data-backdrop="static" role="dialog" aria-labelledby="upload_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <form id="upload_documents_form" class="form form-horizontal" method="post" action="{{route('admin.accounts.documents.upload')}}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" id="user_id" value="{{$id}}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="shipments_modal_title">Upload Document(s)</h4>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="col">
                            <div class="form-group">
                                <label for="filled_and_signed_image">
                                    Pdf of filled and signed document: 
                                </label><br>
                                <span id="old_filled_and_signed_pdf"> </span><input class="form-control form-control-sm" type="file" name="filled_and_signed_pdf" id="filled_and_signed_pdf">
                            </div>
                            <div class="form-group">
                                <label for="signed_acknowledgement_image">
                                    Pdf of signed Acknowledgement form:
                                </label>
                                <br>
                                <span id="old_signed_acknowledgement_pdf"> </span>
                                <input class="form-control form-control-sm" type="file" name="signed_acknowledgement_pdf"  id="signed_acknowledgement_pdf">
                            </div>
                            <div class="form-group">
                                <label for="cnic_front_image">
                                    Picture of CNIC (Front):
                                </label><br>
                                <span id="old_cnic_front_image"> </span>
                                <input class="form-control form-control-sm" type="file" name="cnic_front_image"  id="cnic_front_image">
                            </div>
                            <div class="form-group">
                                <label for="cnic_back_image">
                                    Picture of CNIC (Back):
                                </label><br>
                                <span id="old_cnic_back_image"> </span>
                                <input class="form-control form-control-sm" type="file" name="cnic_back_image"  id="cnic_back_image">
                            </div>
                            <div class="form-group">
                                <label for="blank_cheque_image">
                                    Picture of Blank cheque:
                                </label><br>
                                <span id="old_blank_cheque_image"> </span>
                                <input class="form-control form-control-sm" type="file" name="blank_cheque_image"  id="blank_cheque_image">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
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
            $('.edit').on('click', function() {
                
                var user_id = {!! $id !!};
                console.log(user_id);
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

            $( "#upload_documents_form" ).validate({
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