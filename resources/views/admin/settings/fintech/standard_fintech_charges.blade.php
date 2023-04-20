@extends('admin.layout.master')

@section('title', 'Standard Fintech Charges')

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
                                <form class="form-horizontal text-center" id="save_standard_fintech_charges"  novalidate="novalidate" autocomplete="off">
                                    {{ csrf_field() }}
                                    <div class="row justify-content-center">
                                        <div class="form-group">
                                            <label>Standard Fintech Charges*</label>
                                            <div class="input-group">
                                                <input type="text" name="standard_fintech_charges" id="standard_fintech_charges"  value="@if(!empty($value)){{$value->standard_fintech_charges}}@else{{''}}@endif" class="form-control input-filtered fuel_factor" placeholder=""  data-rule-required="true" data-msg-required="Fintech Charges is Required" min="1">   <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="form-group">
                                            <label>Standard FED Tax Charges*</label>
                                            <div class="input-group">
                                                <input type="text" value="@if(!empty($value)){{$value->standard_fed_charges}}@else{{''}}@endif"  name="standard_FED_Charges" id="standard_FED_Charges" placeholder="" class="form-control input-filtered fuel_factor" aria-invalid="false" data-rule-required="true" data-msg-required=" Fed Tax is Required" min="1">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary width-100">Update </button>
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


$("#save_standard_fintech_charges" ).validate({
    ignore: ":not(:visible),:disabled",
    errorClass: 'danger',
    successClass: 'success',
    errorPlacement: function(error, element) {
        error.addClass('w-100').appendTo(element.parents('.form-group'));
    },
    submitHandler: function(form) {


        swal({
            title: 'Are You Sure?',
            text: 'Select Yes to update Standard Fintech Charges!',
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
                var standard_fintech_charges = $("#standard_fintech_charges").val();
                var standard_FED_Charges = $("#standard_FED_Charges").val();
                $.ajax({
                type : 'POST',
url  : "{{route('admin.settings.standard_fintech_charges.store')}}",
data : {standard_fintech_charges:standard_fintech_charges,standard_FED_Charges:standard_FED_Charges,'_token': '{{ csrf_token() }}'},
                success:function(res){
                    if(res.status == '200'){
                        toastr.success('Standard Fintech Charges has been updated Successfully! ', 'Success!', {
                        positionClass: 'toast-bottom-center',
                        containerId: 'toast-bottom-center'
                    });
                        //  window.location.reload();
                    }
                    else{
                        toastr.error(res.error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    }
                }

                });
            }
        });
    }
});




        $('#save_standard_fintech_charges input.fuel_factor').inputmask({
            'alias': 'integer',
            'allowMinus': true,
            'allowPlus': false,
            'max':100
        });

        function inputValidate(){
        $("input.input-filtered").on("input", function() {
        this.value = this.value.replace(/^\D+/g, '').replace(/[^0-9..%]/g, '').replace(/(\..*)\./g, '$1').replace(/(\d+)(%.*)$/g, '$1%');
        });
        }
    </script>
@endsection
