@extends('admin.layout.master')

@section('title', 'Pickup Weight Threshold')

@section('css')
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">--}}



@endsection
@section('content')
    <h1>Pickup Weight Threshold</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <div class="">
                            @include('admin.inc.messages')
                        </div>
                    </div>


                    <div class="card-content">
                        <div class="card-body">
                            <div class="card">
                                <div class="card-body">

                                    <div class="card-content">
                                    <div class="row">
                                        @php
                                        $pickup_weight = '';
                                        if(!empty($settings)){
                                                $pickup_weight = $settings->setting_value;
                                            }else{
                                                $pickup_weight = '';
                                            }


                                        @endphp
                                        <div class="col-md-4">
                                            <form action="{{route('admin.settings.pickup.weight.add')}}" method="post" id="pickup_weight_threshold">
                                                @csrf
                                                @if($pickup_weight != '')
                                                    @method('PUT')
                                                @endif
                                            <fieldset class="form-group">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" value="{{$pickup_weight}}" name="pickup_weight" placeholder="Pickup Request Weight" required data-rule-required="true" data-msg-required="This field is required">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">KG</span>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary" type="submit">Add Threshold</button>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
           $('#pickup_weight_threshold').validate({

               errorClass: "danger",
               errorPlacement: function (error, element) {
                   error.addClass('w-100').appendTo(element.parents('.form-group'));
               },
               submitHandler: function (form) {

                   $(form).find('button[type=submit]').attr('disabled', 'disabled');
                   swal({
                       title: 'Please Wait!',
                       text: 'Pickup Request Weight is being added!',
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