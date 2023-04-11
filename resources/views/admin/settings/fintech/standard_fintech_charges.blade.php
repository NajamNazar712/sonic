@extends('admin.layout.master')

@section('title', 'Standard Fintech Changes')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                Standard Fintech Charges
                </h1> 
                  <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center">
                                <form id="settings_form" class="form-horizontal text-center" method="POST"
                                    action="{{ route('admin.settings.standard_fintech_charges.store') }}"
                                    novalidate="novalidate" autocomplete="off">
                                    {{ csrf_field() }}
                                    <div class="row justify-content-center">
                                    
                                        <div class="form-group text-left">
                                            <label>Standard Fintect Charges</label>
                                            <input type="text" name="standard_fintech_charges" onkeydown="inputValidate()"  value="@if(!empty($value)){{$value->standard_fintech_charges}}@else{{''}}@endif" class="form-control input-filtered" placeholder="" >
                                        </div>
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="form-group">
                                            <label>Standard FED Tex Charges</label>
                                            <input type="text" name="standard_FED_Charges"  onkeydown="inputValidate()" value="@if(!empty($value)){{$value->standard_fed_charges}}@else{{''}}@endif" class="form-control input-filtered" placeholder="" >
                                        </div>
                                    </div>
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary width-250">Update </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
               
                




            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>

    <script>
        function inputValidate(){
        $("input.input-filtered").on("input", function() {
        this.value = this.value.replace(/^\D+/g, '').replace(/[^0-9..%]/g, '').replace(/(\..*)\./g, '$1').replace(/(\d+)(%.*)$/g, '$1%');
        });
        }


        $(document).ready(function() {
            $('#settings_form input.rangeup').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#settings_form input.rangedown').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#settings_form input.charges').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'allowpercentage': true,
            });
            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'New Company Generate',
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
