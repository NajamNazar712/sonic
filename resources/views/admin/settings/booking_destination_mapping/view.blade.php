@extends('admin.layout.master')

@section('title', '  View Booking Destination Keywords')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    View Booking Destination Keywords
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.booking_destination_keyword.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <input type="hidden" name="id" value="{{$booking_destination_mapping->id}}">
                                        <div class="form-group">
                                            <input type="text" id="booking_destination_keywords" name="booking_destination_keywords" class="form-control booking_destination_keywords" placeholder="Booking Destination Keywords*" data-rule-required="true" value="{{$booking_destination_mapping_keywords}}" data-msg-required="Booking Destination Keyword is required" data-tags-input-name="booking_destination_keywords">
                                        </div>


                                        <div class="form-group">
                                            <select class="form-control" name="city_id" id="city_id" data-rule-required="true" data-msg-required="City Name is required" disabled>
                                                @foreach ($cities as $city)
                                                    @if ($booking_destination_mapping->city_id == $city->id)
                                                        <option value="{{$city->id}}" selected>{{$city->name}}</option>
                                                    @else
                                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#booking_destination_keywords').selectize({
                placeholder: 'Booking Destination Keyword(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                create: function(input) {
                    if ($.trim(input)) {
                        input = input.replace(/\s/g, '');

                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                }
            });

            $('#booking_destination_keywords')[0].selectize.disable();

            $('#city_id').select2({
                width: '100%',
                placeholder: 'Select City'
            });

            $('#settings_form').validate({

                errorClass: 'danger',
                successClass: 'success',
                ignore: ':hidden:not([class~=selectized]),:hidden > .selectized, .selectize-control .selectize-input input',

                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                }
            });
        });
    </script>
@endsection