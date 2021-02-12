@extends('admin.layout.master')

@section('title', 'Edit Territory')

@section('content')
    <h1>Edit Territory</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        @include('admin.inc.messages')
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <form id="territory_form" class="form-horizontal" method="POST" action="{{ route('admin.management.territory.update', ['id' => $territory->id]) }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <div class="row justify-content-center">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>City</label>
                                            <select name="city_id" class="select2" id="city_id" data-rule-required="true" data-msg-required="City is required">
                                                @foreach($cities as $city)
                                                    @if ($city->id == $territory->city_id)
                                                        <option value="{{ $city->id }}" selected="selected">{{ $city->name }}</option>
                                                    @else
                                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Territory</label>
                                            <input type="text" name="territory" id="territory" class="form-control" placeholder="Territory Name*" data-rule-required="true" data-msg-required="Territory Name is required" value="{{ $territory->name }}">
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="form-group text-center mt-2">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </div>
                            </form>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {

            @if ($territory->city_id === null)
            $('#territory_form #city_id').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            });
            @else
            $('#territory_form #city_id').select2({
                width: '100%',
                placeholder: 'City'
            });
            @endif

            $('#territory_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function (value) {
                    return $.trim(value);
                },
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'User is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
        });

    </script>
@endsection