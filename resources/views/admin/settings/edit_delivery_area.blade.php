@extends('admin.layout.master')

@section('title', 'Edit Delivery Area')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Edit Delivery Area
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.delivery_area_keyword.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <input type="hidden" name="id" value="{{$delivery_location->id}}">
                                        <div class="form-group">
                                            <input type="text" id="delivery_area_keyword" name="delivery_area_keyword" class="form-control delivery_area_keyword" placeholder="Delivery Area Keywords*" data-rule-required="true" data-msg-required="Delivery Area Keyword is required" data-tags-input-name="delivery_area_keyword" value="{{$delivery_location_keywords}}">
                                        </div>

                                        
                                        <div class="form-group">
                                            <select class="form-control" name="city_id" id="city_id" data-rule-required="true" data-msg-required="City Name is required">
                                                @foreach ($cities as $city)
                                                    @if ($delivery_location->city_id == $city->id)
                                                        <option value="{{$city->id}}" selected>{{$city->name}}</option>
                                                    @else
                                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group text-center">
                                            <input type="text" id="area_name" name="area_name" class="form-control area_name text-center" placeholder="Write Area Name*" data-rule-required="true" data-msg-required="Area Name is required" data-tags-input-name="area_name" value="{{$delivery_location->area_name}}">
                                            
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#delivery_area_keyword').selectize({
                placeholder: 'Delivery Area Keyword(s)*',
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