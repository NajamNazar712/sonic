@extends('admin.layout.master')

@section('title', 'Consignee City Update')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    {{$consignee->consignee_name}}({{$consignee->hub_name}}) City Update
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.petty_cash.consignee.city.update',['id'=>$consignee->id]) }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <div class="row mb-2 justify-content-center">
                                            <div class="col-12 form-group">
                                                <select name="cities[]" id="cities_select" class="form-control select2" multiple="multiple" required>
                                                    @foreach($cities as $city)
                                                        <option value="{{$city->id}}">{{$city->name}}</option>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            $('#cities_select').select2({
                placeholder:'Cities*',
                width:'100%',
                allowClear:true
            });

            @if(count($petty_cash_cities) > 0)
                var ids = @json($petty_cash_cities);
                $('#cities_select').val(ids).trigger('change');
            @endif

            $.validator.addMethod("cities[]",
                function(value, element) {
                console.log(value);

                        return result;
                },
                "Cities Must Be Unique!!"
            );
            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update Cities!',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if(confirm){
                            var data = $("#settings_form").serialize();
                            blockPagePermanently();
                            $.ajax({
                                type: "POST",
                                url: '{!!  route('admin.settings.petty_cash.consignee.city.check',['id'=> $consignee->id]) !!}', // script to validate in server side
                                data: data,
                                success: function (response) {
                                    if(response == 0)
                                    {
                                        form.submit();
                                    }
                                    else{
                                        toastr.error(response.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        UnblockPagePermanently();
                                    }
                                }
                            });

                        }
                    });
                }
            });
        });
    </script>
@endsection