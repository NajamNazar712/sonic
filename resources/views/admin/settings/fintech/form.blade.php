@extends('admin.layout.master')

@section('title', 'Fintech Company | Form')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                @if(!empty($fintechvalues))    
                Edit Fintech Company Charges
                
                @else
                Add Fintech Company 
                @endif  
                </h1>

                  @if(!empty($fintechvalues)) 

                  <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center">
                                <form id="settings_form" class="form-horizontal text-center" method="POST"
                                    action="{{ route('admin.settings.setup_fintech_charges.edit_save') }}"
                                    novalidate="novalidate">
                                    {{ csrf_field() }}
                               
                                    <div class="charges_div">
                                        <div class="row justify-content-center">    
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Range Up</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Range Down</label>

                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Charges</label>

                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Additional Charges</label>

                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Federal Excise Duty Tax</label>

                                                </div>
                                            </div>
                                        </div>
                                     
                                      

                                                @foreach($fintechvalues as $charges)            
                                                <input type="hidden" value ="@if(!empty($fintechvalues)) {{$charges->company_Id}}  @endif" name="company_id">
                                                <div class="row justify_content_center mb-1">

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text"
                                                                name="fintech_range_up_edit[]"
                                                                onkeydown="inputValidate()"
                                                                class="form-control input-filtered"
                                                                value ="@if(!empty($fintechvalues)) {{$charges->range_up}}  @endif"
                                                                placeholder="Range Up" required data-rule-required="true"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" value ="@if(!empty($fintechvalues)) {{$charges->id}}  @endif" name="IndexID[]">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text"
                                                                name="fintech_range_down_edit[]"
                                                                onkeydown="inputValidate()"
                                                                class="form-control input-filtered"
                                                                value ="@if(!empty($fintechvalues)) {{$charges->range_down}}  @endif"
                                                                placeholder="Range Down" required data-rule-required="true"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" required
                                                                name="charges_edit[]"
                                                                class="form-control input-filtered"
                                                                onkeydown="inputValidate()"
                                                                placeholder="Charges"
                                                                value ="@if(!empty($fintechvalues)) {{$charges->charges}}  @endif"
                                                                data-rule-required="true"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" required
                                                                name="additional_charges_edit[]"
                                                                class="form-control input-filtered"
                                                                onkeydown="inputValidate()"
                                                                value ="@if(!empty($fintechvalues)) {{$charges->additional_charges}}  @endif"
                                                                placeholder="Additional "
                                                                data-rule-required="true"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" required
                                                                name="fed_tax_edit[]"
                                                                onkeydown="inputValidate()" 
                                                                value ="@if(!empty($fintechvalues)) {{$charges->fed_tax}}  @endif"
                                                                class="form-control input-filtered"
                                                                 placeholder="Federal Excise Duty Tax"
                                                                data-rule-required="false"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>

                                                </div>
                                                @endforeach




                                    </div>
                                    <div class="col mb-1">
                                        <button type="button" class="btn btn-outline-success btm-sm add_row"
                                            id="add_row"><i class="la la-plus"></i></button>
                                    </div>

                                    <div class="col">
                                        <button type="submit" class="btn btn-primary width-250">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> 

                  @else 
                  <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center">
                                <form id="settings_form" class="form-horizontal text-center" method="POST"
                                    action="{{ route('admin.settings.setup_fintech_charges.store') }}"
                                    novalidate="novalidate">
                                    {{ csrf_field() }}
                                    <div class="row justify-content-center">
                                        <div class="form-group">
                                            <input type="text" name="company_name" class="form-control " placeholder="Company Name" >
                                        </div>
                                    </div> <Br>
                                    <div class="charges_div">
                                        <div class="row justify-content-center">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Range Up</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Range Down</label>

                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Charges</label>

                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Additional Charges</label>

                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Federal Excise Duty Tax</label>

                                                </div>
                                            </div>
                                        </div>
                                     
                                                <div class="row justify_content_center mb-1">

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text"
                                                                name="fintech_range_up[]"
                                                                onkeydown="inputValidate()"
                                                                class="form-control input-filtered"
                                                                placeholder="Range Up" required data-rule-required="true"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text"
                                                                name="fintech_range_down[]"
                                                                onkeydown="inputValidate()"
                                                                class="form-control input-filtered"
                                                                
                                                                placeholder="Range Down" required data-rule-required="true"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" required
                                                                name="charges[]"
                                                                onkeydown="inputValidate()"
                                                                class="form-control input-filtered"
                                                                placeholder="Charges"
                                                                data-rule-required="true"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" required
                                                                name="additional_charges[]"
                                                                onkeydown="inputValidate()"
                                                                class="form-control input-filtered"
                                                                placeholder="Additional "
                                                                data-rule-required="true"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" required
                                                                name="fed_tax[]"
                                                                onkeydown="inputValidate()"
                                                                class="form-control input-filtered"
                                                                 placeholder="Federal Excise Duty Tax"
                                                                 
                                                                data-rule-required="false"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>

                                                </div>

                                    </div>
                                    <div class="col mb-1">
                                        <button type="button" class="btn btn-outline-success btm-sm add_row"
                                            id="add_row"><i class="la la-plus"></i></button>
                                    </div>

                                    <div class="col">
                                        <button type="submit" class="btn btn-primary width-250">Save Company Details</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                  @endif
                




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


// $(document).ready(function() {
//   $('.input-filtered').on('keypress', function(e) {
//     var keyCode = e.which;
//     // allow numbers (48-57) and percentage sign (37)
//     if ((keyCode < 48 || keyCode > 57) && keyCode != 37) {
//       e.preventDefault();
//     }
//   });
// });

// function inputFilter(element) {
//       element.on("input", function() {
//         this.value = this.value.replace(/^\D+/g, '').replace(/[^0-9.%]/g, '').replace(/(\..*)\./g, '$1').replace(/(\d+)(%.*)$/g, '$1%');
//       });
//     }

//     $(document).ready(function() {
//       // select all textboxes with the class 'input-filtered'
//       var textboxes = $(".input-filtered");
      
//       // apply the inputFilter function to each of the selected textboxes
//       textboxes.each(function() {
//         inputFilter($(this));
//       });
//     });



// function inputFilter(element) {
//       element.on("input", function() {
//         this.value = this.value.replace(/^\D+/g, '').replace(/[^0-9.%]/g, '').replace(/(\..*)\./g, '$1').replace(/(\d+)(%.*)$/g, '$1%');
//       });
//     }

//     $(document).ready(function() {
//       inputFilter($("input.input-filtered"));
//     });


function inputValidate(){

    $("input.input-filtered").on("input", function() {
        this.value = this.value.replace(/^\D+/g, '').replace(/[^0-9..%]/g, '').replace(/(\..*)\./g, '$1').replace(/(\d+)(%.*)$/g, '$1%');
      });
}

 


        $(document).ready(function() {
            $('#settings_form input.rangeup').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
               
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
            var row = 1;
        
            $('body').on('click', '.add_row', function() {

                var old_row = row - 1;
                flag = true;
                // if (old_row != 0) {
                //     var old_range_up = $('#rangeup_' + old_row).val();
                //     var old_range_down = $('#rangedown_' + old_row).val();
                //     var old_charges = $('#charges_' + old_row).val();
                //     if ((old_range_up == null || old_range_up == '') || ((old_range_down == null ||
                //             old_range_down == '')) || ((old_charges == null || old_charges == ''))) {
                //         flag = false;
                //         console.log(old_range_down, old_range_up, old_charges);
                //         if (old_range_up == null || old_range_up == '') {
                //             var error = "Please enter Range Up Value!!";
                //             toastr.error(error, 'Error!', {
                //                 positionClass: 'toast-top-center',
                //                 containerId: 'toast-top-center'
                //             });
                //         }
                //         if (old_range_down == null || old_range_down == '') {
                //             var error = "Please enter Range Down Value!!";
                //             toastr.error(error, 'Error!', {
                //                 positionClass: 'toast-top-center',
                //                 containerId: 'toast-top-center'
                //             });
                //         }
                //         if (old_charges == null || old_charges == '') {
                //             var error = "Please enter Charges Value!!";
                //             toastr.error(error, 'Error!', {
                //                 positionClass: 'toast-top-center',
                //                 containerId: 'toast-top-center'
                //             });
                //         }
                //     }
                // }

                if (flag == true) {

                    var html = '<div class="row justify_content_center mb-1">\n' +
                        '                                        <div class="col">\n' +
                        '                                            <div class="form-group">\n' +
                        '                                               <input type="text" required name="fintech_range_up[]" onkeydown="inputValidate()" class="form-control input-filtered" placeholder="Range Up" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                            </div>\n' +
                        '                                        </div>\n' +
                        '                                        <div class="col">\n' +
                        '                                            <div class="form-group">\n' +
                        '                                               <input type="text" required name=fintech_range_down[]" onkeydown="inputValidate()" class="form-control input-filtered" placeholder="Range Down" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                            </div>\n' +
                        '                                        </div>\n' +

                        '                                        <div class="col">\n' +
                        '                                            <div class="form-group">\n' +
                        '                                               <input type="text" required name="charges[]" onkeydown="inputValidate()" id="" class="form-control input-filtered" placeholder="Charges" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                            </div>\n' +
                        '                                        </div>\n' +
                        
                        '                                        <div class="col">\n' +
                        '                                            <div class="form-group">\n' +
                        '                                               <input type="text" required name="additional_charges[]" onkeydown="inputValidate()" class="form-control input-filtered" placeholder="Additional Charges" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                            </div>\n' +
                        '                                        </div>\n' +


                        '                                        <div class="col">\n' +
                        '                                            <div class="form-group">\n' +
                        '                                               <input type="text" required name="fed_tax[]" onkeydown="inputValidate()" class="form-control input-filtered" placeholder="Fedral Excise Duty Tax" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                            </div>\n' +
                        '                                        </div>\n' +
                        '                                    </div>';
                    $('div.charges_div').append(html);



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
                        'allowPlus': false
                    });
                    row++;
                }

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
