@extends('admin.layout.master')

@section('title', 'Sale User Restriction Bypass')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Sale User Restriction Bypass
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.sales.user_restriction.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <div class="col-12 form-group">
                                            <select name="users[]" id="users_select" class="form-control select2" multiple="multiple">
                                                @foreach($sale_persons as $sale_person)
                                                    <option value="{{$sale_person->id}}">{{$sale_person->name}}</option>
                                                @endforeach
                                            </select>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>
        .select2-container--classic .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #64a0d2 !important;
            border-color: #5587b4 !important;
            color: #FFFFFF;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            $('#users_select').select2({
                placeholder:'Select Sales User',
                width:'100%',
                allowClear:true
            });

            @if(count($user_ids) > 0)
            var ids = @json($user_ids);
                $('#users_select').val(ids).trigger('change');
            @endif
            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                }
            });
        });
    </script>
@endsection