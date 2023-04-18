@extends('admin.layout.master')
<style>
    .text-right {
  text-align: right;
  
}

table {
    border-collapse: collapse;
}

.center {
      text-align: center;
    }
</style>
@section('title', 'Add Fintech Company Charges')

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
                Add Fintech Company Charges
                @endif  
                </h1>

                  @if(!empty($fintechvalues)) 

                  <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center">

                             

                                <form id="add_company_charges_update" class="form-horizontal text-center" method="POST"
                                    action="{{ route('admin.settings.fintech_company_charges.edit_save') }}"
                                    novalidate="novalidate" autocomplete="off">
                                    {{ csrf_field() }}
                                    <div class="row justify-content-center">
                                        <div class="form-group">
                                            <input type="text" value="{{$fintech_company_name->company_name}}"  class="form-control" readonly >
                                            <input type="hidden" name="company_id" value="{{$fintech_company_name->id}}"  class="form-control" readonly >
                                        </div>
                                    </div> <Br>
                          
                                <table  class="table table-responsive"> 
                                            <thead>      
                                                    <tr>
                                                        <th class="center"><label>Range Up</label></th>
                                                        <th class="center"><label>Range Down</label></th>
                                                        <th class="center"><label>Charges</label></th>
                                                        <th class="center"><label>Additional Charges</label></th>
                                                        <th class="center"><label>Federal Excise Duty Tax</label></th>
                                                      
                                                    </tr>
                                            </thead>
                                        <tbody>
                                            @foreach($fintechvalues as $key=> $charges)
                                                <input type="hidden" value ="@if(!empty($fintechvalues)) {{$charges->id}}  @endif" name="IndexID[]">
                                                <tr>
                                                    <td>
                                                        <div class="form-group">   
                                                            <input type="text"name="fintech_range_up_edit[]"
                                                            onkeydown="inputValidate()"
                                                            class="form-control input-filtered text-right"
                                                            value ="@if(!empty($fintechvalues)) {{$charges->range_up}}  @endif"
                                                            placeholder="Range Up*" required data-rule-required="true"data-msg-required="This field is required" min="1">
                                                        </div>
                                                    </td>  
                                                    <td>
                                                        <div class="form-group">   
                                                            <input type="text"name="fintech_range_down_edit[]"
                                                            onkeydown="inputValidate()"
                                                            class="form-control input-filtered text-right"
                                                            value ="@if(!empty($fintechvalues)) {{$charges->range_down}}  @endif"
                                                            placeholder="Range Down*"  data-rule-required="true" data-msg-required="This field is required" min="1">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">  
                                                            <input type="text" name="charges_edit[]"
                                                            onkeydown="inputValidate()"
                                                            class="form-control input-filtered text-right"
                                                            value ="@if(!empty($fintechvalues)) {{$charges->charges}}  @endif"
                                                            placeholder="Charges*" data-rule-required="true"data-msg-required="This field is required">
                                                        </div>    
                                                    </td>
                                                    <td>
                                                        <div class="form-group">  
                                                            <input type="text" name="additional_charges_edit[]"
                                                            onkeydown="inputValidate()"
                                                            class="form-control input-filtered text-right"
                                                            value ="@if(!empty($fintechvalues)) {{$charges->additional_charges}}  @endif"
                                                            placeholder="Additional Charges" data-rule-required="true"data-msg-required="This field is required">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group"> 
                                                            <input type="text" required name="fed_tax_edit[]"
                                                            onkeydown="inputValidate()" 
                                                            class="form-control input-filtered text-right"
                                                            value ="@if(!empty($fintechvalues)) {{$charges->fed_tax}}  @endif"
                                                            placeholder="Federal Excise Duty Tax*"data-rule-required="false"data-msg-required="This field is required">
                                                        </div>     
                                                    </td>  
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                           
                                <table class="table table-responsive">  
                                <tbody id="row_append_body_update">
                                </tbody>
                                </table>  

                                    <div class="col mb-1">
                                        <button type="button" class="btn btn-outline-success btm-sm add_row_update"
                                        id="add_row"><i class="la la-plus"></i></button>
                                    </div>

                                    <div class="col">
                                        <button type="submit" class="btn btn-primary width-100">Update</button>
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
                                <form id="add_company_charges"  class="form-horizontal text-center" method="POST"
                                    action="{{ route('admin.settings.fintech_company_charges.store') }}"
                                    novalidate="novalidate" autocomplete="off">
                                    {{ csrf_field() }}
                                    <div class="row justify-content-center">
                                        <div class="form-group">
                                            <input type="text" name="company_name" class="form-control " required data-rule-required="true"
                                            data-msg-required="Company Name is Required" placeholder="Company Name *" >
                                        </div>
                                    </div> <Br>

                                        <table  class="table table-responsive table-lg"> 

                                          <thead>      
                                            <tr>
                                                <th class="center"><label>Range Up</label></th>
                                                <th class="center"><label>Range Down</label></th>
                                                <th class="center"><label>Charges</label></th>
                                                <th class="center"><label>Additional Charges</label></th>
                                                <th class="center"><label>Federal Excise Duty Tax</label></th>
                                                <th class="center"> </th>
                                            </tr>
                                          </thead>
                                            
                                          <tbody id="row_append_body">
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                    <input type="text" 
                                                    name="fintech_range_up[0]"
                                                    onkeydown="inputValidate()"
                                                    class="form-control input-filtered"
                                                    placeholder="Range Up*" required data-rule-required="true"
                                                    data-msg-required="This field is required"
                                                  
                                                    >  
                                                    
                                                    </div>
                                                </td>

                                                <td> 
                                                    <div class="form-group">
                                                    <input type="text" 
                                                    name="fintech_range_down[0]"
                                                    onkeydown="inputValidate()"
                                                    class="form-control input-filtered "
                                                    placeholder="Range Down*" required data-rule-required="true"
                                                    data-msg-required="This field is required"   min="1">
                                                    </div>
                                                </td> 

                                                <td> 
                                                    <div class="form-group">
                                                    <input type="text" 
                                                    name="charges[0]"
                                                    onkeydown="inputValidate()"
                                                    class="form-control input-filtered "
                                                    placeholder="Charges*"
                                                    data-rule-required="true"
                                                    data-msg-required="This field is required"   min="1">
                                                    </div>
                                                </td> 

                                                <td> 
                                                  
                                                    <input type="text"
                                                    name="additional_charges[0]"
                                                    onkeydown="inputValidate()"
                                                    class="form-control input-filtered "
                                                    placeholder="Additional Charges"   min="1">
                                                    
                                                </td> 

                                                <td> 
                                                    <div class="form-group">
                                                    <input type="text"
                                                    name="fed_tax[]"
                                                    onkeydown="inputValidate()"
                                                    class="form-control input-filtered "
                                                    placeholder="FED Tax Charges*"
                                                    data-rule-required="true"
                                                    data-msg-required="This field is required"   min="1">
                                                    </div>
                                                </td> 
                                                <td></td>
                                            </tr>    

                                            </tbody>
                                            </table>       
                                            <td>
                                                <div class="col mb-1">
                                                    <button type="button" class="btn btn-outline-success btm-sm add_row"
                                                        id="add_row"><i class="la la-plus"></i></button>
                                                </div>
                                            </td>   
                                    <div class="col" >
                                        <button type="submit" class="btn btn-primary width-100"> submit</button>
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

$("#add_company_charges_update" ).validate({   
  errorClass:"danger",
    errorPlacement: function(error, element) {
        error.addClass('w-100').appendTo(element.parent('.form-group'));
    },
    submitHandler: function(form) {
     
        swal({
            title: 'Are You Sure?',
            text: 'Select Yes to Update Fintech Company Charges',
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
                    form.submit();
            
                }
        });
    }
});     


$("#add_company_charges" ).validate({   
  errorClass:"danger",
    errorPlacement: function(error, element) {
        error.addClass('w-100').appendTo(element.parent('.form-group'));
    },
    submitHandler: function(form) {
     
        swal({
            title: 'Are You Sure?',
            text: 'Select Yes to Add Fintech Company Charges! ',
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
                    form.submit();
                }
        });
    }
});






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

                    var html = `<tr>
                                    <td>
                                        <div class="form-group">
                                                <input type="text" name="fintech_range_up[`+row+`]" 
                                                onkeydown="inputValidate()"
                                                class="form-control input-filtered "
                                                placeholder="Range Up*" required data-rule-required="true"
                                                data-msg-required="This field is required"  >
                                        </div>
                                    </td>
                                     <td> 
                                        <div class="form-group">
                                            <input type="text" required name="fintech_range_down[`+row+`]" 
                                            onkeydown="inputValidate()"
                                            class="form-control input-filtered "
                                            placeholder="Range Down*" required data-rule-required="true"
                                            data-msg-required="This field is required"   min="1">
                                        </div>
                                    </td> 
                                    <td> 
                                        <div class="form-group">
                                            <input type="text" name="charges[`+row+`]" 
                                            onkeydown="inputValidate()"
                                            class="form-control input-filtered"
                                            placeholder="Charges*" data-rule-required="true"
                                            data-msg-required="This field is required"   min="1">
                                        </div>
                                    </td> 
                                    <td> 
                                        <input type="text" name="additional_charges[`+row+`]"
                                        onkeydown="inputValidate()"
                                        class="form-control input-filtered "
                                        placeholder="Additional Charges"   min="1">
                                    </td> 
                                    <td> 
                                        <div class="form-group">
                                            <input type="text" name="fed_tax[`+row+`]"    
                                            onkeydown="inputValidate()"
                                            class="form-control input-filtered "
                                            placeholder="FED Tax Charges*" data-rule-required="true"
                                            data-msg-required="This field is required"   min="1">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="col mb-1">
                                            <button type="button" onclick="$(this).closest('tr').remove();" class="btn btn-outline-danger btm-sm"><i class="la la-trash"></i></button>
                                        </div>
                                    </td>            
                                </tr> `;
                    $('#row_append_body').append(html);

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

            var y = 0;
            $('body').on('click', '.add_row_update', function() {


flag = true;
if (flag == true) {
   
    var html = `<tr>
                    <td>
                        <div class="form-group">
                            <input type="text" required name="fintech_range_up[`+y+`]"
                            onkeydown="inputValidate()"
                            class="form-control input-filtered text-right"
                            placeholder="Range Up*" data-rule-required="true" data-msg-required="This field is required" min="1">
                        </div>
                    </td>
                    <td> 
                        <div class="form-group">
                            <input type="text" required name="fintech_range_down[`+y+`]"
                            onkeydown="inputValidate()" 
                            class="form-control input-filtered text-right"
                            placeholder="Range Down*" required data-rule-required="true"data-msg-required="This field is required">
                        </div>
                    </td> 
                    <td> 
                        <div class="form-group">
                            <input type="text" name="charges[`+y+`]"
                            onkeydown="inputValidate()"
                            class="form-control input-filtered text-right"
                            placeholder="Charges*" data-rule-required="true" data-msg-required="This field is required">
                        </div>
                    </td> 
                    <td> 
                        <input type="text" name="additional_charges[`+y+`]"
                        onkeydown="inputValidate()"
                        class="form-control input-filtered text-right"
                        placeholder="Additional Charges" data-rule-required="true" data-msg-required="This field is required">
                    </td>
                    <td> 
                        <div class="form-group">
                            <input type="text" name="fed_tax[`+y+`]"
                            onkeydown="inputValidate()"
                            class="form-control input-filtered text-right"
                            placeholder="Charges*" data-rule-required="true" data-msg-required="This field is required">
                        </div>
                    </td>           
                    <td>
                        <div class="col mb-1">
                            <button type="button" onclick="$(this).closest('tr').remove();" class="btn btn-outline-danger btm-sm"><i class="la la-trash"></i></button>
                        </div>
                    </td>   
                               
                </tr>`;
    $('#row_append_body_update').append(html);
    y++;
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
