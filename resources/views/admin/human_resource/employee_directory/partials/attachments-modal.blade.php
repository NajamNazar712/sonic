@php
    $authUser = \Illuminate\Support\Facades\Auth::user();

    $employee = \App\Http\Models\HR\Employee::where('id', $authUser->employee_id)->first();

    $documents = null;
    $document_status = null;
    $id = null;
    $employee_name = $authUser->name ?? 'Employee';

    if ($employee) {
        $documents = \App\Http\Models\HR\EmployeeAttachment::where('employee_id', $employee->id)->first();
        $document_status = $employee->status_id;
        $id = $employee->id;
    }

    $deadlinePassed = \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse('2026-03-31 23:59:59'));

    $cnics = ($documents && $documents->cnic)
        ? array_values(array_filter(array_map('trim', explode(',', $documents->cnic))))
        : [];

    $cvs = ($documents && $documents->cv)
        ? array_values(array_filter(array_map('trim', explode(',', $documents->cv))))
        : [];

    $photos = ($documents && $documents->photo)
        ? array_values(array_filter(array_map('trim', explode(',', $documents->photo))))
        : [];

    $cheques = ($documents && $documents->cheque)
        ? array_values(array_filter(array_map('trim', explode(',', $documents->cheque))))
        : [];

    $last_pay_slips = ($documents && $documents->last_pay_slip)
        ? array_values(array_filter(array_map('trim', explode(',', $documents->last_pay_slip))))
        : [];

//    $existingCnicFront = false;
//    $existingCnicBack = false;
//
//    foreach ($cnics as $cnicFile) {
//        if (strpos($cnicFile, 'cnic_1_') !== false) {
//            $existingCnicFront = true;
//        }
//
//        if (strpos($cnicFile, 'cnic_2_') !== false) {
//            $existingCnicBack = true;
//        }
//    }

    $attachmentErrorFields = [
        'cnic_1', 'cnic_2', 'cnic_3', 'cnic_4',
        'cv_1', 'cv_2', 'cv_3', 'cv_4',
        'photo_1', 'photo_2', 'photo_3', 'photo_4',
        'cheque_1', 'cheque_2', 'cheque_3', 'cheque_4',
        'last_pay_slip_1', 'last_pay_slip_2', 'last_pay_slip_3', 'last_pay_slip_4',
    ];

    $hasAttachmentErrors = false;
    $attachmentErrors = [];

    foreach ($attachmentErrorFields as $field) {
        if ($errors->has($field)) {
            $hasAttachmentErrors = true;
            foreach ($errors->get($field) as $message) {
                $attachmentErrors[] = $message;
            }
        }
    }

    $attachmentErrors = array_unique($attachmentErrors);
@endphp

@if($employee && $document_status == 3)
    <div class="modal fade" id="upload_modal"
         @if($deadlinePassed)
             data-backdrop="static" data-keyboard="false"
         @endif
         role="dialog" aria-labelledby="upload_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="upload_documents_form"
                  class="form form-horizontal"
                  method="post"
                  action="{{ route('admin.human_resource.employee_directory.attachments.update', $id) }}"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="is_forced_document" value="1">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="shipments_modal_title">Documents - {{ $employee_name }}</h4>

                        @if(!$deadlinePassed)
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        @endif
                    </div>
                    @if($hasAttachmentErrors)
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($attachmentErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="modal-body">
                        <p>
                            This activity is being conducted for the submission of missing employee documents.
                            Employees with incomplete records are required to upload the requested documents by
                            <strong>31st March 2026.</strong>
                            Failure to comply by this deadline may result in restrictions until the required documents are submitted.
                        </p>

                        <div class="row justify-content-center">
                            <div class="col-12">
                                <table class="table table-sm table-bordered text-center">
                                    <tbody>
                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Scanned CNIC:</b></h6></td>
                                        @if(count($cnics))
                                            <td class="align-middle">
                                                @foreach($cnics as $cnic)
                                                    <a class="white mb-1 d-inline-block" href="{{ asset(Storage::url($cnic)) }}" target="_blank">
                                                        <button type="button" class="btn btn-primary btn-sm">View</button>
                                                    </a>
                                                @endforeach
                                            </td>
                                        @else
                                            <td class="align-middle">-</td>
                                        @endif
                                    </tr>

                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>CV/Resume:</b></h6></td>
                                        @if(count($cvs))
                                            <td class="align-middle">
                                                @foreach($cvs as $cv)
                                                    <a class="white mb-1 d-inline-block" href="{{ asset(Storage::url($cv)) }}" target="_blank">
                                                        <button type="button" class="btn btn-primary btn-sm">View</button>
                                                    </a>
                                                @endforeach
                                            </td>
                                        @else
                                            <td class="align-middle">-</td>
                                        @endif
                                    </tr>

                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Passport Size Photo:</b></h6></td>
                                        @if(count($photos))
                                            <td class="align-middle">
                                                @foreach($photos as $photo)
                                                    <a class="white mb-1 d-inline-block" href="{{ asset(Storage::url($photo)) }}" target="_blank">
                                                        <button type="button" class="btn btn-primary btn-sm">View</button>
                                                    </a>
                                                @endforeach
                                            </td>
                                        @else
                                            <td class="align-middle">-</td>
                                        @endif
                                    </tr>

                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Cheque:</b></h6></td>
                                        @if(count($cheques))
                                            <td class="align-middle">
                                                @foreach($cheques as $cheque)
                                                    <a class="white mb-1 d-inline-block" href="{{ asset(Storage::url($cheque)) }}" target="_blank">
                                                        <button type="button" class="btn btn-primary btn-sm">View</button>
                                                    </a>
                                                @endforeach
                                            </td>
                                        @else
                                            <td class="align-middle">-</td>
                                        @endif
                                    </tr>

                                    <tr style="height: 50px">
                                        <td class="align-middle"><h6><b>Last Pay Slip:</b></h6></td>
                                        @if(count($last_pay_slips))
                                            <td class="align-middle">
                                                @foreach($last_pay_slips as $last_pay_slip)
                                                    <a class="white mb-1 d-inline-block" href="{{ asset(Storage::url($last_pay_slip)) }}" target="_blank">
                                                        <button type="button" class="btn btn-primary btn-sm">View</button>
                                                    </a>
                                                @endforeach
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
{{--                            <div class="form-group">--}}
{{--                                <label>Scanned CNIC:</label>--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-md-6 mb-1">--}}
{{--                                        <label for="cnic_1">CNIC Front</label>--}}
{{--                                        <input class="form-control form-control-sm" type="file" name="cnic_1" id="cnic_1">--}}
{{--                                    </div>--}}

{{--                                    <div class="col-md-6 mb-1">--}}
{{--                                        <label for="cnic_2">CNIC Back</label>--}}
{{--                                        <input class="form-control form-control-sm" type="file" name="cnic_2" id="cnic_2">--}}
{{--                                    </div>--}}

{{--                                    <div class="col-md-6 mb-1">--}}
{{--                                        <label for="cnic_3">Additional Document 1</label>--}}
{{--                                        <input class="form-control form-control-sm" type="file" name="cnic_3" id="cnic_3">--}}
{{--                                    </div>--}}

{{--                                    <div class="col-md-6 mb-1">--}}
{{--                                        <label for="cnic_4">Additional Document 2</label>--}}
{{--                                        <input class="form-control form-control-sm" type="file" name="cnic_4" id="cnic_4">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="form-group">
                                <label>Scanned CNIC:</label>
                                <div class="row">
                                    @for($i = 1; $i <= 4; $i++)
                                        <div class="col-md-6 mb-1">
                                            <input class="form-control form-control-sm" type="file" name="cnic_{{ $i }}" id="cnic_{{ $i }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="form-group">
                                <label>CV/Resume:</label>
                                <div class="row">
                                    @for($i = 1; $i <= 4; $i++)
                                        <div class="col-md-6 mb-1">
                                            <input class="form-control form-control-sm" type="file" name="cv_{{ $i }}" id="cv_{{ $i }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Passport Size Photo:</label>
                                <div class="row">
                                    @for($i = 1; $i <= 4; $i++)
                                        <div class="col-md-6 mb-1">
                                            <input class="form-control form-control-sm" type="file" name="photo_{{ $i }}" id="photo_{{ $i }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Cheque:</label>
                                <div class="row">
                                    @for($i = 1; $i <= 4; $i++)
                                        <div class="col-md-6 mb-1">
                                            <input class="form-control form-control-sm" type="file" name="cheque_{{ $i }}" id="cheque_{{ $i }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Last Pay Slip:</label>
                                <div class="row">
                                    @for($i = 1; $i <= 4; $i++)
                                        <div class="col-md-6 mb-1">
                                            <input class="form-control form-control-sm" type="file" name="last_pay_slip_{{ $i }}" id="last_pay_slip_{{ $i }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="upload_documents_btn">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            @if($hasAttachmentErrors || ($employee && $document_status == 3))
            $('#upload_modal').modal('show');
            @endif
            {{--const existingCnicFront = @json($existingCnicFront);--}}
            {{--const existingCnicBack = @json($existingCnicBack);--}}

            const existingCounts = {
                cnic: @json(count($cnics)),
                cv: @json(count($cvs)),
                photo: @json(count($photos)),
                cheque: @json(count($cheques)),
                last_pay_slip: @json(count($last_pay_slips))
            };

            function hasAtLeastOneNew(prefix) {
                for (let i = 1; i <= 4; i++) {
                    if ($('#' + prefix + '_' + i).val() !== '') {
                        return true;
                    }
                }
                return false;
            }

            function hasRequirementSatisfied(prefix) {
                return existingCounts[prefix] > 0 || hasAtLeastOneNew(prefix);
            }

            $('#upload_modal').on('hide.bs.modal', function () {
                $('#upload_documents_form')[0].reset();

                for (let i = 1; i <= 4; i++) {
                    $('#cnic_' + i).val('');
                    $('#cv_' + i).val('');
                    $('#photo_' + i).val('');
                    $('#cheque_' + i).val('');
                    $('#last_pay_slip_' + i).val('');
                }
            });

            $('#upload_documents_btn').on('click', function(e) {
                e.preventDefault();

                // const cnicFrontOk = existingCnicFront || $('#cnic_1').val() !== '';
                // const cnicBackOk = existingCnicBack || $('#cnic_2').val() !== '';
                //
                // if (!cnicFrontOk) {
                //     toastr.error('The CNIC Front file is required.', 'Error!', {
                //         positionClass: 'toast-top-center',
                //         containerId: 'toast-top-center'
                //     });
                //
                //     return;
                // }
                //
                // if (!cnicBackOk) {
                //     toastr.error('The CNIC Back file is required.', 'Error!', {
                //         positionClass: 'toast-top-center',
                //         containerId: 'toast-top-center'
                //     });
                //     return;
                // }
                const totalCnicFiles = existingCounts.cnic + (
                    ($('#cnic_1').val() !== '' ? 1 : 0) +
                    ($('#cnic_2').val() !== '' ? 1 : 0) +
                    ($('#cnic_3').val() !== '' ? 1 : 0) +
                    ($('#cnic_4').val() !== '' ? 1 : 0)
                );

                if (totalCnicFiles < 2) {
                    toastr.error('At least two scanned CNIC files are required, including both front and back.', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    return;
                }

                if (!hasRequirementSatisfied('cv')) {
                    toastr.error('At least one CV/Resume file is required.', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    return;
                }

                if (!hasRequirementSatisfied('photo')) {
                    toastr.error('At least one Passport Size Photo is required.', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    return;
                }

                if (!hasRequirementSatisfied('cheque')) {
                    toastr.error('At least one Cheque file is required.', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    return;
                }

                if (!hasRequirementSatisfied('last_pay_slip')) {
                    toastr.error('At least one Last Pay Slip file is required.', 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    return;
                }

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
                }).then(function(confirm) {
                    if (confirm) {
                        $('#upload_documents_form')[0].submit();
                    }
                });
            });
        });
    </script>
@endif