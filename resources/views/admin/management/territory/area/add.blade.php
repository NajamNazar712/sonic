@extends('admin.layout.master')

@section('title', 'Add Area')

@section('content')
    <h1>Add Area</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        @include('admin.inc.messages')
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <form id="territory_form" class="form-horizontal" method="POST" action="{{ route('admin.management.area.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <div class="row justify-content-center">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select name="territory" class="select2" id="territory" data-rule-required="true" data-msg-required="Territory is required">
                                                @foreach($territories as $territory)
                                                    <option value="{{ $territory->id }}">{{ $territory->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="text" name="area" id="area" class="form-control" placeholder="Area Name*" data-rule-required="true" data-msg-required="Area Name is required">
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="form-group text-center mt-2">
                                            <button type="submit" class="btn btn-primary">Add</button>
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
            $('#territory_form #territory').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Territory*'
            });


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