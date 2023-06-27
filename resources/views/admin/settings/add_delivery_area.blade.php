@extends('admin.layout.master')

@section('title', 'Add Delivery Area')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Add Delivery Area
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.delivery_area_keyword.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <input type="text" id="delivery_area_keyword" name="delivery_area_keyword" class="form-control delivery_area_keyword" placeholder="Delivery Area Keywords*" data-rule-required="true" data-msg-required="Delivery Area Keyword is required" data-tags-input-name="delivery_area_keyword">
                                        </div>

                                        
                                        <div class="form-group">
                                            <select class="form-control" name="city_id" onchange="getArea(this.value)" id="city_id" data-rule-required="true" data-msg-required="City Name is required">
                                                @foreach ($cities as $city)
                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group text-center" id="show_area" >
                                            <input type="text" id="area_name" name="area_name" class="form-control area_name text-center" placeholder="Write Area Name*" data-rule-required="true" data-msg-required="Area Name is required" data-tags-input-name="area_name">
                                        </div>

                                        <button type="submit" class="btn btn-primary">Add</button>
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

            $('#city_id').prepend('<option value="" selected="selected"></option>').select2({
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


        function getArea(city_id){

            $.ajax({
                url: '{!! route('admin.settings.delivery_area_keyword.get_city_area') !!}',
                method: 'GET',
                data: {
                    'city_id': city_id
                }
            }).done(function(data) {
                $('#show_area').html('');
                var area_input = `<input type="text" id="area_name" name="area_name" class="form-control area_name text-center" placeholder="Write Area Name*" data-rule-required="true" data-msg-required="Area Name is required" data-tags-input-name="area_name">`
                var select = `<input type="hidden" id='default_hub' name='default_hub' value="1"><select class="form-control" id="area_name" name="area_name" data-rule-required="true" data-msg-required="City Area Name is required"></select>
                                         `

                if(data.hub === 1){
                    $('#show_area').html(select);
                    if(data.data.length > 0) {

                        var push = "";
                        $.each(data.data, function(index, value) {
                            push+= `<option value="${value.id}">${value.name}</option>`
                        });
                        $('#area_name').append(push);
                    }
                    $('#area_name').prepend('<option value="" selected="selected"></option>').select2({
                        width: '100%',
                        placeholder: 'Select City Area'
                    });

                }else{
                    $('#show_area').html(area_input);
                }



             });
        }
    </script>
@endsection