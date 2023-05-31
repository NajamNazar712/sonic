@extends('admin.layout.master')

@section('title', 'SMS notification status delivered to shipper')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   SMS Notification Status Delivered To Shipper
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.sms_notification_return_delivered_to_shipper.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <div class="row mb-2 justify-content-center">
                                            <div class="col-12 form-group">
                                                <select name="users[]" id="roles_select" class="form-control select2" multiple="multiple" data-msg-required="Atleast One Role Is Required" data-rule-required="true" required="required">
                                                    @foreach($users as $user)
                                                        @if(isset($selected_roles))
                                                            <option value="{{$user->id}}" {{ in_array($user->id, $selected_roles) ? 'selected' : '' }}>{{$user->name}}</option>
                                                        @else
                                                            <option value="{{$user->id}}">{{$user->name}}</option>

                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            $('#roles_select').select2({
                placeholder: 'Users'
                , width: '100%'
                , allowClear: true
            }).bind('select2:select', function() {

                if ($(this).val().length != 0) {
                    $('#settings_form').find('button[type=submit]').prop('disabled', false);
                }
            });

            $('#roles_select').on('select2:unselect', function() {
                if ($(this).val().length == 0) {
                    $('#settings_form').find('button[type=submit]').prop('disabled', true);
                }
            });

            $('#settings_form').validate({
                // ignore: ":not(:visible),:disabled",
                errorClass: 'danger'
                , successClass: 'success'
                , errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
                , submitHandler: function(form) {
                    swal({
                        title: 'Are You Sure?'
                        , text: 'Select Yes to update users!'
                        , icon: 'warning'
                        , buttons: {
                            cancel: {
                                text: 'No'
                                , value: null
                                , visible: true
                                , closeModal: true
                                , }
                            , confirm: {
                                text: 'Yes'
                                , value: true
                                , visible: true
                                , closeModal: true
                            }
                        }
                        , closeOnClickOutside: false
                        , closeOnEsc: false
                        , dangerMode: true
                    }).then(function(confirm) {
                        if (confirm) {
                            $(form).find('button[type=submit]').attr('disabled', 'disabled');
                            blockPagePermanently();
                            form.submit();
                        }
                    });
                }
            });
        });

    </script>
@endsection
