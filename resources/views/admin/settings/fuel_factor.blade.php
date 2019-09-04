@extends('admin.layout.master')

@section('title', 'Fuel Factor')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Fuel Factor
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.fuel_factor.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Fuel Factor</span>
                                                </div>
                                                <input type="text" name="fuel_factor" class="form-control fuel_factor" placeholder="Fuel Factor*" data-rule-required="true" data-msg-required="Fuel Factor is required" value="">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-2 justify-content-center">
                                            <div class="col-12 form-group">
                                                <select name="shippers[]" id="shippers_select" class="form-control select2" multiple="multiple" data-msg-required="Atleast one shipper is required" data-rule-require="true" disabled="disabled">
                                                    @foreach($shippers as $shipper)
                                                        <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 form-group">
                                                <label class="font-medium-2 font-weight-bold block">All Shippers</label>
                                                <div class="form-group">
                                                    <label for="all_shippers_checkbox" class="font-medium-2 text-bold-600 mr-1">NO</label>
                                                    <input type="checkbox" name="all_shippers_checkbox" id="all_shippers_checkbox" class="switchery all_shippers_checkbox" data-size="md" data-switchery="true" checked>
                                                    <label for="all_shippers_checkbox" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-lg">Update</button>
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
            $('#settings_form input.fuel_factor').inputmask({
                'alias': 'integer',
                'allowMinus': true,
                'allowPlus': false,
                'max':100
            });
            var all_switch = document.querySelector('.switchery.all_shippers_checkbox');
            $('#all_shippers_checkbox').on('change',function(){

                var all_switch_change = document.querySelector('.switchery.all_shippers_checkbox');

                if (all_switch_change.checked === true) {
                    $('#shippers_select').val(null).trigger('change');
                    $('#shippers_select').attr('disabled', true);

                }else if (all_switch_change.checked === false) {
                    $('#shippers_select').attr('data-rule-required', true);
                    $('#shippers_select').attr('disabled', false);
                }
            });


            $('#shippers_select').select2({
                placeholder:'Shippers',
                width:'100%',
                allowClear:true
            }).bind('select2:select', function () {

                if($(this).val() != null){
                    if($("#all_shippers_checkbox").is(":checked")){
                        $("#all_shippers_checkbox").trigger('click');
                    }
                }
            });

            $('#shippers_select').on('select2:unselect', function () {
               if($(this).val().length == 0){
                   var all_switch_check = document.querySelector('.switchery.all_shippers_checkbox');

                   if(all_switch_check.checked === false){
                       $("#all_shippers_checkbox").trigger('click');
                   }
               }
            });

            $('#settings_form').validate({
                ignore: ":not(:visible),:disabled",
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update fuel factor!',
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