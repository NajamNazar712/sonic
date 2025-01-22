@extends('client.layout.master')

@section('title', 'Finga Dashboard')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    Wallet On-boarding
                </h1>

                <div class="card" style="height: 100vh; margin: 0; padding: 0; border: none;">
                    <div class="card-content" aria-expanded="true" style="height: 100%; padding: 0;">
                        <div class="card-body">
                            <div class="card-text">
                            </div>
                            <form id="main-form" class="form form-horizontal"  novalidate="novalidate">
                                @csrf
                                <div class="form-body">
                                    <h4 class="form-section">Profile Information (Please verify your profile information before sign up to wallet)</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <div class="form-group col-md-9">
                                                    <label>Name</label>
                                                    <span class="danger">*</span>
                                                    <input type="text" id="name" class="form-control border-primary" data-rule-maxlength="100" data-msg-maxlength="Name" data-rule-required="true" data-msg-required="Name is required" value="{{$user->name}}" name="name" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <div class="form-group col-md-9">
                                                    <label>Phone Number 1:</label>
                                                    <span class="danger">*</span>
                                                    <input type="text" id="phone" class="form-control border-primary" data-rule-required="true" data-msg-required="Phone Number is required" value="{{$user->phone}}" name="phone" required data-rule-remote="{{ route('cod.profile.shipper_phone_unique', ['id' => $user->id, 'type'=>'wallet']) }}" data-msg-remote="Phone must be unique">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <div class="form-group col-md-9">
                                                    <label>CNIC:</label>
                                                    <input type="text" id="cnic" class="form-control border-primary"  value="{{$user->cnic}}" name="cnic">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <div class="form-group col-md-9">
                                                    <label>Email Address:</label>
                                                    <span class="danger">*</span>
                                                    <input type="email" id="email"  class="form-control border-primary" data-rule-required="true" data-msg-required="Phone Number is required" value="{{$user->email}}" name="email" required>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-primary"  >Signup to Wallet</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function () {
            $( "#main-form" ).validate({
                errorClass: "danger",
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {

                    var formData = new FormData();

                    // Add CSRF token
                    formData.append('_token', '{{ csrf_token() }}');


                    var rowId = 0;

                    formData.append(`users[${rowId}][id]`, rowId);
                    formData.append(`users[${rowId}][name]`, $('#name').val());
                    formData.append(`users[${rowId}][phone]`, $('#phone').val());
                    formData.append(`users[${rowId}][cnic]`, $('#cnic').val());
                    formData.append(`users[${rowId}][email]`, $('#email').val());


                    $.ajax({
                        url: '{{ route('cod.update.bulk.profile_wallet') }}',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function (data) {

                            if (data.status === 0) {

                                if (data.error) {
                                    var summaryErrorMessages = '<ul style="color: #e56464;">';
                                    $.each(data.error, function (rowId, errors) {

                                        var errorMessages = '<ul>';
                                        summaryErrorMessages += `<li><strong>Row ${rowId}:</strong></li><ul>`;

                                        // Iterate over errors for the row
                                        $.each(errors, function (field, message) {
                                            // Append individual field errors to the row and summary
                                            errorMessages += `<li><strong>${field}:</strong> ${message}</li>`;
                                            summaryErrorMessages += `<li><strong>${field}:</strong> ${message}</li>`;
                                        });

                                        errorMessages += '</ul>';
                                        summaryErrorMessages += '</ul>';
                                    });
                                }
                                summaryErrorMessages += '</ul>';

                                swal({
                                    content: (() => {
                                        let content = document.createElement('div');
                                        content.innerHTML = summaryErrorMessages;
                                        return content;
                                    })(),
                                    title: 'Errors Found!',
                                    icon: 'warning',
                                    className: 'custom-swal-width' // Optional: Use custom class for wider modal
                                });


                                if (data.error_2) {
                                    let summaryErrorMessages = '<ul style="color: #e56464;">';

                                    // Iterate through the rows in error_2
                                    $.each(data.error_2, function (rowId, rowData) {


                                        summaryErrorMessages += `<li>Row ID: ${rowId}</li><ul>`;

                                        // Display the specific errors for each field in the row
                                        if (rowData.message) {
                                            $.each(rowData.message, function (field, messages) {
                                                $.each(messages, function (index, message) {
                                                    summaryErrorMessages += `<li>${field}: ${message}</li>`;
                                                });
                                            });
                                        }

                                        summaryErrorMessages += '</ul>';
                                    });

                                    let scrollableContent = document.createElement('div');
                                    scrollableContent.style.maxHeight = '400px'; // Adjust the height as needed
                                    scrollableContent.style.overflowY = 'auto';  // Add vertical scrolling
                                    scrollableContent.style.padding = '10px';   // Optional: Add padding for readability
                                    scrollableContent.innerHTML = summaryErrorMessages;

                                    swal({
                                        content: scrollableContent,
                                        title: 'Error!',
                                        className: 'custom-swal-width',
                                        text: 'Errors occurred in the following rows.',
                                        icon: 'warning',
                                    });
                                }




                            } else {
                                swal({
                                    title: 'Success',
                                    text: 'Success',
                                    icon: 'success',
                                });
                                var rdUrl = data.output.url ;
                                window.location.href = `{{ url('cod/wallet/finja_dashboard') }}?url=${rdUrl}`;
                            }
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });


        });
    </script>
@endsection
