@extends('client.layout.master')

@section('title', 'Finga Dashboard')

@section('content')
    <style>
        .right {
            float: right;
        }
        .custom-swal-width .swal-modal {
            width: 80%; /* Adjust width as needed */
            max-width: 90%; /* Prevent exceeding viewport */
        }

    </style>

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    Wallet On-boarding Substitute Accounts
                </h1>

                <div class="card" style="margin: 0; padding: 0; border: none;overflow-y: scroll">
                    <div class="card-content" aria-expanded="true" style="height: 100%; padding: 0;">
                        <div class="card-body">
                            <div class="card-text">
                            </div>
                            <h4 class="form-section">Profile Information (Please verify your profile information before sign up to wallet)</h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button"   id="bulkSubmitBtn" class="btn btn-primary" style="float: right">Bulk Signup</button>
                                </div>
                            </div>

                            <!-- Bulk Submit Button (Initially hidden) -->


                            <table id="subUsersTable" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAllCheckbox"></th>
                                    <th>Substitute User#</th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>CNIC</th>
                                    <th>Email Address</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($user->sub_users as $key => $value)
                                        <?php
                                        $check_box = false;
                                        if ($value->sub_wallet) {
                                            $name = $value->sub_wallet->name;
                                            $email = $value->sub_wallet->email;
                                            $phone = $value->sub_wallet->phone;
                                            $cnic = $value->sub_wallet->cnic;
                                        } else {
                                            $check_box = true;
                                            $name = $value->name;
                                            $email = $value->email;
                                            $phone = $value->phone_number;
                                            $cnic = $value->cnic;
                                        }
                                        ?>
                                    <tr id="row-{{ $value->id }}">
                                        <td>
                                        @if(($check_box))
                                            <input  type="checkbox" value="{{$value->id}}" class="selectRowCheckbox">
                                        @endif
                                        </td>

                                        <td>{{ $value->id}}</td>
                                        <td><input type="text" id="name-{{ $value->id }}" class="form-control" value="{{ $name }}" name="name" required></td>
                                        <td><input type="text" id="phone-{{ $value->id }}" class="form-control" value="{{ $phone }}" name="phone" required></td>
                                        <td><input type="text" id="cnic-{{ $value->id }}" class="form-control" value="{{ $cnic }}" name="cnic"></td>
                                        <td><input type="email" id="email-{{ $value->id }}" class="form-control" value="{{ $email }}" name="email" required></td>
                                        <td>
                                            @csrf
                                            <input type="hidden" name="substitute_user_id" value="{{ $value->id }}">
                                            @if(isset($value->sub_wallet))
                                                <input type="hidden" name="login_request" value="{{ $value->id }}">
                                                <button type="button" class="btn btn-success submit-btn">Sign In</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

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
            // Initialize DataTable (optional, for customization)
            var table = $('#subUsersTable').DataTable({
                paging: false,  // Disable pagination for simplicity
                searching: false, // Disable searching for simplicity
                order: false, // Disable searching for simplicity
                info: false // Disable information display
            });

            // Toggle Bulk Submit button visibility based on checkbox selection
            $('#subUsersTable').on('change', '.selectRowCheckbox', function () {
                var anyChecked = $('.selectRowCheckbox:checked').length > 0;
                $('#bulkSubmitBtn').toggle(anyChecked);
            });

            // Select all checkboxes when the "Select All" checkbox is clicked
            $('#selectAllCheckbox').on('change', function () {
                var isChecked = $(this).prop('checked');
                $('.selectRowCheckbox').prop('checked', isChecked);
                $('#bulkSubmitBtn').toggle(isChecked); // Show bulk submit button if "Select All" is checked
            });

            // Handle the Bulk Submit
            $('#bulkSubmitBtn').on('click', function () {
                var formData = new FormData();

                // Add CSRF token
                formData.append('_token', '{{ csrf_token() }}');

                // Collect data from all selected rows
                $('.selectRowCheckbox:checked').each(function () {
                    var row = $(this).closest('tr');
                    var rowId = row.attr('id') ? row.attr('id').split('-')[1] : row.index();

                    formData.append(`users[${rowId}][id]`, rowId);
                    formData.append(`users[${rowId}][name]`, row.find('input[name="name"]').val());
                    formData.append(`users[${rowId}][phone]`, row.find('input[name="phone"]').val());
                    formData.append(`users[${rowId}][cnic]`, row.find('input[name="cnic"]').val());
                    formData.append(`users[${rowId}][email]`, row.find('input[name="email"]').val());
                    formData.append(`users[${rowId}][substitute_user_id]`, row.find('input[name="substitute_user_id"]').val());
                });

                if ($('.selectRowCheckbox:checked').length > 0) {
                    $.ajax({
                        url: '{{ route('cod.update.bulk.profile_wallet') }}',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function (data) {
                            $('.row-error').remove();
                            $('tr').removeClass('error-row');

                            if (data.status === 0) {

                                if (data.error) {
                                    var summaryErrorMessages = '<ul style="color: #e56464;">';
                                    $.each(data.error, function (rowId, errors) {
                                        // Find the row element by ID
                                        var row = $(`#row-${rowId}`);

                                        // Highlight the row with an error
                                        row.css('background-color', '#ffebeb');
                                        row.css('border', '1px solid #e56464');

                                        // Create error messages for the specific row
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

                                        // Optionally display the error messages directly in the row (e.g., in a dedicated error column)
                                        row.find('.error-cell').html(errorMessages); // Ensure `.error-cell` exists in your row structure
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

                                        var row = $(`#row-${rowId}`);

                                        // Highlight the row with an error
                                        row.css('background-color', '#ffebeb');
                                        row.css('border', '1px solid #e56464');

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
                } else {
                    swal({
                        title: 'No Rows Selected!',
                        text: 'Please select at least one row to submit.',
                        icon: 'warning',
                    });
                }
            });


            // Handle individual row validation and submission
            $('#subUsersTable').on('click', '.submit-btn', function () {
                var row = $(this).closest('tr');
                var rowId = row.attr('id');
                var formData = row.find('input, select').serialize();  // Serialize data of the row

                var isValid = true;
                row.find('input').each(function () {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (isValid) {
                    $.ajax({
                        url: '{{ route('cod.SignInWalletUser') }}',
                        method: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function (data) {
                            if (data.status === 1) {
                                window.location.href = data.redirect_url;
                            }else{
                                swal({
                                    title: 'Error!',
                                    text: 'Not Found',
                                    icon: 'warning',
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });


    </script>
@endsection
