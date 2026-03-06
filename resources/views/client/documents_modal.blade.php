@php
    $authUser = Auth::user();
    $documents = \App\Http\Models\UserDocumentAttachment::where('user_id', $authUser->id)->first();
    $document_status = $authUser->documents_status;
    $reason = $authUser->documents_status_reason;
    $id = $authUser->id;
    $shipper = $authUser->name;
        
    $deadlinePassed = \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse('2026-03-08 23:59:59'));
@endphp

@if($document_status == 0 || $document_status == 3)
    <div class="modal fade" id="upload_modal" data-backdrop="static"
        @if($deadlinePassed)
            data-backdrop="static" data-keyboard="false"
        @endif
        role="dialog" aria-labelledby="upload_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="upload_documents_form" class="form form-horizontal" method="post" action="{{ route('cod.documents.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" id="user_id" value="{{ $id }}">

                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="shipments_modal_title">Documents - {{ $shipper }}</h4>

                        @if(!$deadlinePassed)
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        @endif
                    </div>
                
                    <div class="modal-body">
                        <p>
                            This activity is being conducted for the submission of revised and updated documents.
                            All shippers are required to upload the requested documents by <strong>31st March 2026.</strong>
                            Failure to comply by this deadline may result in restrictions on booking services until the required documents are submitted and approved.
                        </p>

                        @if($document_status == 3)
                            <h4 class="font-weight-bold">Rejected Reason:</h4>
                            <p class="text-danger">{{ $reason }}</p>
                        @endif

                        <div class="row justify-content-center">
                            <div class="col-12">
                                <table class="table table-sm table-bordered text-center">
                                    <tbody>
                                        <tr style="height: 50px">
                                            <td class="align-middle"><h6><b>Pdf of filled and signed document:</b></h6></td>
                                            @if($documents && $documents->filled_and_signed_pdf != null)
                                                <td class="align-middle">
                                                    <a class="white" href="{{ route('cod.documents.view', ['id' => $id, 'check' => 'filled_and_signed_pdf', 'pdf' => 1]) }}" target="_blank">
                                                        <button type="button" class="btn btn-primary btn-sm">View</button>
                                                    </a>
                                                </td>
                                            @else
                                                <td class="align-middle">-</td>
                                            @endif
                                        </tr>

                                        <tr style="height: 50px">
                                            <td class="align-middle"><h6><b>Pdf of signed Acknowledgement form:</b></h6></td>
                                            @if($documents && $documents->signed_acknowledgement_pdf != null)
                                                <td class="align-middle">
                                                    <a class="white" href="{{ route('cod.documents.view', ['id' => $id, 'check' => 'signed_acknowledgement_pdf', 'pdf' => 1]) }}" target="_blank">
                                                        <button type="button" class="btn btn-primary btn-sm">View</button>
                                                    </a>
                                                </td>
                                            @else
                                                <td class="align-middle">-</td>
                                            @endif
                                        </tr>

                                        <tr style="height: 50px">
                                            <td class="align-middle"><h6><b>Picture of CNIC (Front):</b></h6></td>
                                            @if($documents && $documents->cnic_front_image != null)
                                                <td class="align-middle">
                                                    <a class="white" href="{{ route('cod.documents.view', ['id' => $id, 'check' => 'cnic_front_image', 'pdf' => 0]) }}" target="_blank">
                                                        <button type="button" class="btn btn-primary btn-sm">View</button>
                                                    </a>
                                                </td>
                                            @else
                                                <td class="align-middle">-</td>
                                            @endif
                                        </tr>

                                        <tr style="height: 50px">
                                            <td class="align-middle"><h6><b>Picture of CNIC (Back):</b></h6></td>
                                            @if($documents && $documents->cnic_back_image != null)
                                                <td class="align-middle">
                                                    <a class="white" href="{{ route('cod.documents.view', ['id' => $id, 'check' => 'cnic_back_image', 'pdf' => 0]) }}" target="_blank">
                                                        <button type="button" class="btn btn-primary btn-sm">View</button>
                                                    </a>
                                                </td>
                                            @else
                                                <td class="align-middle">-</td>
                                            @endif
                                        </tr>

                                        <tr style="height: 50px">
                                            <td class="align-middle"><h6><b>Picture of Blank cheque:</b></h6></td>
                                            @if($documents && $documents->blank_cheque_image != null)
                                                <td class="align-middle">
                                                    <a class="white" href="{{ route('cod.documents.view', ['id' => $id, 'check' => 'blank_cheque_image', 'pdf' => 0]) }}" target="_blank">
                                                        <button type="button" class="btn btn-primary btn-sm">View</button>
                                                    </a>
                                                </td>
                                            @else
                                                <td class="align-middle">-</td>
                                            @endif
                                        </tr>

                                        <tr style="height: 50px">
                                            <td class="align-middle"><h6><b>Picture of E Signature:</b></h6></td>
                                            @if($documents && $documents->e_sign_image != null)
                                                <td class="align-middle">
                                                    <a class="white" href="{{ route('cod.documents.view', ['id' => $id, 'check' => 'e_sign_image', 'pdf' => 0]) }}" target="_blank">
                                                        <button type="button" class="btn btn-primary btn-sm">View</button>
                                                    </a>
                                                </td>
                                            @else
                                                <td class="align-middle">-</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <div class="col">
                            <div class="form-group">
                                <label for="filled_and_signed_pdf">Pdf of filled and signed document:</label><br>
                                <span id="old_filled_and_signed_pdf">{{ $documents->filled_and_signed_pdf ?? '' }}</span>
                                <input class="form-control form-control-sm" type="file" name="filled_and_signed_pdf" id="filled_and_signed_pdf">
                            </div>

                            <div class="form-group">
                                <label for="signed_acknowledgement_pdf">Pdf of signed Acknowledgement form:</label><br>
                                <span id="old_signed_acknowledgement_pdf">{{ $documents->signed_acknowledgement_pdf ?? '' }}</span>
                                <input class="form-control form-control-sm" type="file" name="signed_acknowledgement_pdf" id="signed_acknowledgement_pdf">
                            </div>

                            <div class="form-group">
                                <label for="cnic_front_image">Picture of CNIC (Front):</label><br>
                                <span id="old_cnic_front_image">{{ $documents->cnic_front_image ?? '' }}</span>
                                <input class="form-control form-control-sm" type="file" name="cnic_front_image" id="cnic_front_image">
                            </div>

                            <div class="form-group">
                                <label for="cnic_back_image">Picture of CNIC (Back):</label><br>
                                <span id="old_cnic_back_image">{{ $documents->cnic_back_image ?? '' }}</span>
                                <input class="form-control form-control-sm" type="file" name="cnic_back_image" id="cnic_back_image">
                            </div>

                            <div class="form-group">
                                <label for="blank_cheque_image">Picture of Blank cheque:</label><br>
                                <span id="old_blank_cheque_image">{{ $documents->blank_cheque_image ?? '' }}</span>
                                <input class="form-control form-control-sm" type="file" name="blank_cheque_image" id="blank_cheque_image">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#upload_modal').modal('show');

            $('#upload_modal').on('hide.bs.modal', function (e) {
                $('#upload_documents_form')[0].reset();
                $('#filled_and_signed_pdf').val('');
                $('#signed_acknowledgement_pdf').val('');
                $('#cnic_front_image').val('');
                $('#cnic_back_image').val('');
                $('#blank_cheque_image').val('');
            });

            $("#upload_documents_form").validate({
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

                    if (
                        filled_and_signed_pdf !== '' ||
                        signed_acknowledgement_pdf !== '' ||
                        cnic_front_image !== '' ||
                        cnic_back_image !== '' ||
                        blank_cheque_image !== ''
                    ) {
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
                            if (confirm) {
                                form.submit();
                            }
                        });
                    } else {
                        var error = 'No file Selected';
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
            });
        });
    </script>
@endif
