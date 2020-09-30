@extends('admin.layout.master')

@section('title', 'Booking Sms For Consignee')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Booking Sms For Consignee
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-5">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.booking_sms_shipper_wise.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <select name="shippers[]" id="shipper_select" class="form-control select2" multiple="multiple">
                                                @foreach($shippers as $person)
                                                    <option value="{{$person->id}}">{{$person->name}}</option>
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
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#shipper_select').select2({
                placeholder:'Shipper Select',
                width:'100%'
            });

            @if(count($existing_shippers) > 0)
                var ids = @json($existing_shippers);
                $('#shipper_select').val(ids).trigger('change');
            @endif

            $('#settings_form .class').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });
        });
    </script>
@endsection