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
                                                    <input type="text" id="phone" class="form-control border-primary" data-rule-required="true" data-msg-required="Phone Number is required" value="{{$user->phone}}" name="phone" required data-rule-remote="{{ route('cod.profile.shipper_phone_unique', ['id' => $user->id]) }}" data-msg-remote="Phone must be unique">
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
                    $.ajax({
                        url: '{{ route('cod.update.profile_wallet') }}',
                        method: 'POST',
                        data: $(form).serialize(),  // Serialize form data properly
                        dataType: 'json', // Ensure proper response format
                        success: function(data) {
                            if(data.status == 0) {
                                $('.error-messages').remove();

                                if (data.status === 0 && data.error.length > 0) {
                                    let errorList = $('<ul class="error-messages text-danger mt-2"></ul>');

                                    $.each(data.error, function (index, message) {
                                        errorList.append('<li>' + message + '</li>');
                                    });
                                    $('.card-text').html(errorList); // Append errors after the `.card` element
                                }
                            // }else{
                            //     window.location.href = '{{ url('cod/wallet/login') }}'
                            // }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);

                        }
                    });
                }
            });


        });
    </script>
@endsection
