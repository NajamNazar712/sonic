@extends('admin.layout.master')

@section('title', 'Add ERF')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Add ERF
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="col mb-5">
                                <form id="erf_form" class="form-horizontal" method="POST" novalidate="novalidate" action="{{route('admin.human_resource.erf.submit')}}">
                                    @method('POST')
                                    {{ csrf_field() }}
                                    <div class="row w-100 div_row">
                                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-2 mb-2">
                                            <h2 class="ml-2"> ERF Type</h2>
                                        </div>
                                        <div class="col-md-6 col-lg-6 mb-2">
                                            <div class="form-check form-check-inline pull-left">
                                                <input class="form-check-input" type="radio" name="erf_type" id="additional"  value="1" checked />
                                                <label class="form-check-label" for="inlineRadio1" >Additional</label>
                                            </div>

                                            <div class="form-check form-check-inline pull-left">
                                                <input class="form-check-input" type="radio" name="erf_type" id="replacement" value="2"/>
                                                <label class="form-check-label" for="inlineRadio2">Replacement</label>
                                            </div>
                                            <div class="form-check form-check-inline pull-left">
                                                <input class="form-check-input" type="radio" name="employee_status" id="inactive" value="1"/>
                                                <label class="form-check-label" for="inlineRadio1" >Inactive</label>
                                            </div>

                                            <div class="form-check form-check-inline pull-left">
                                                <input class="form-check-input" type="radio" name="employee_status" id="notice_period" value="2"/>
                                                <label class="form-check-label" for="inlineRadio2">Notice Period</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row div_row">
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                            <div class="form-group">
                                                <input type="hidden"  name="admin_email" id="admin_email">
                                                <select name="department" class="select2" id="department" data-rule-required="true" data-msg-required="Department is required">
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                            <div class="form-group">
                                                <select name="designation" class="select2" id="designation" data-rule-required="true" data-msg-required="Designation is required">
                                                    {{-- @foreach($designations as $designation)
                                                        <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                                    @endforeach --}}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                            <div class="form-group">
                                                <select name="hub" class="select2" id="hub" data-rule-required="true" data-msg-required="Hub is required">
                                                    @foreach($hubs as $hub)
                                                        <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                            <div class="form-group">
                                                <select name="city" class="select2" id="city" data-rule-required="true" data-msg-required="City is required">
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="departmrnt_div">
                                            <div class="form-group">
                                                <select name="department_head" class="select2" id="department_head" data-rule-required="true" data-msg-required="Department Head is required" disabled>
                                                     @foreach($department_heads as $head)
                                                        <option value="{{ $head->id }}">{{ $head->name }}</option>
                                                    @endforeach 
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="vacancies_div" >
                                            <div class="form-group">
                                                <select name="vacancies" class="select2" id="vacancies" data-rule-required="true" data-msg-required="Vacancy is required">
                                                    @for ($i=1; $i<=10; $i++)
                                                        <option value="{{$i}}">{{$i}}</option>
                                                    @endfor
                                                    }
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="position_div">
                                            <div class="form-group">
                                                <select name="position" class="select2" id="position" data-rule-required="true" data-msg-required="Position is required">
                                                    @foreach($admin_positions as $position)
                                                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="range_div">

                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" float: left;">
                                                <div class="form-group ">
                                                    <input type="text" class="form-control mr-1" id="from" name="salary_from" placeholder="Salary From" data-rule-required="true" data-msg-required="Salary From is required">
                                                </div>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" float: left;">
                                                <div class="form-group ">
                                                    <input type="text" class="form-control" id="to" name="salary_to" placeholder=" Salary To" data-rule-required="true" data-msg-required="Salary To is required">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="qualification_div">
                                            <div class="form-group ">
                                                <textarea class="form-control" rows="5" cols="100" id="qualification" name="qualification" placeholder="Qualifications*" data-rule-required="true" data-msg-required="Qualification is required"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="skills_div">
                                            <div class="form-group ">
                                                <textarea class="form-control" rows="5" cols="100" id="skills" name="skills" placeholder="Required Skills*" data-rule-required="true" data-msg-required="Skills are required"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="description_div">
                                            <div class="form-group ">
                                                <textarea class="form-control" rows="5" cols="100" id="job_description" name="job_description" placeholder="Job Description*" data-rule-required="true" data-msg-required="Job Description is required"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="allowance_div">
                                            <div class="form-group ">
                                                <select name="allowances[]" class="select2" id="allowance" multiple="multiple" data-rule-required="true" data-msg-required="Allowance is required">
                                                    @foreach($allowances as $allowance)
                                                        <option value="{{ $allowance->id }}">{{ $allowance->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 button_div">
                                        <div class="form-group text-center mt-2 mb-5">
                                            <button type="submit" class="btn btn-primary" id="form_btn">Submit</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="EmailModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EmailModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Send Email To</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="email_form" class="form-horizontal" method="POST" novalidate="novalidate">
                    <div class="modal-body mx-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="email" name="email" data-rule-required="true" data-msg-required="Email is required" placeholder="Enter Email">
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary" id="modal_submit_btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {
            $('#designation').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Designation*'
            });
 
            $('#department_head').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'HOD*'
            });
            
            $('#department').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Department*'
            }).bind('change',function(){
                var id = $(this).val();
                if(id) {
                    $.ajax({
                        url: '{!! route('admin.human_resource.erf.employee_data') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 1){
                            var data1 = $.map(data.emplyee_detail.designations, function (obj) {
                                obj.id = obj.id;
                                obj.text = obj.name;

                                return obj;
                            });
                            $('#designation').empty().trigger("change");
                            $('#designation').prepend('<option value="" selected="selected"></option>').select2({
                                width: '100%',
                                data:data1,
                                placeholder: 'Select Designation*'
                            });
                        $('#department_head').val(data.emplyee_detail.department_head.id).trigger('change');

                            department_head
                            console.log(data);
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });
            $('#hub').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Hub*'
            });
            $('#city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select City*'
            });
           
            $('#vacancies').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Number Of Vacancies*'
            });
            $('#position').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Position Type*'
            });
            $('#allowance').select2({
                width:'100%',
                placeholder:"Requirements/Allowances*",
                allowClear:true,
                dropdownParent:$('#erf_form')
            });
            $('#from').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
            });

            $('#to').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
            });

            $('#to').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
            });



            $('#additional').click(function () {
                 $("#department").val('').change();
                 $("#designation").val('').change();
                 $("#department_head").val('').change();
                 $("#city").val('').change();
                 $("#hub").val('').change();
                 $('#new_div').remove();
                 $('hr').remove();


                var vacancies = ' <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="vacancies_div">\n' +
                    '                                        <div class="form-group">\n' +
                    '                                            <select name="vacancies" class="select2" id="vacancies" data-rule-required="true" data-msg-required="Vacancy is required">\n' +
                    '                                                @for ($i=1; $i<=10; $i++)\n' +
                    '                                                    <option value="{{$i}}">{{$i}}</option>\n' +
                    '                                                 @endfor\n' +
                    '                                            </select>\n' +
                    '                                        </div>\n' +
                    '                                    </div>';

                $("#erf_form #departmrnt_div").after(vacancies);
                $('#vacancies').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Number Of Vacancies*'
                });


                var position = ' <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="position_div">\n' +
                    '                                        <div class="form-group">\n' +
                    '                                            <select name="position" class="select2" id="position" data-rule-required="true" data-msg-required="Position is required">\n' +
                    '                                                @foreach($admin_positions as $position)\n' +
                    '                                                    <option value="{{ $position->id }}">{{ $position->name }}</option>\n' +
                    '                                                @endforeach\n' +
                    '                                            </select>\n' +
                    '                                        </div>\n' +
                    '                                    </div>';

                $("#erf_form #vacancies_div").after(position);
                $('#position').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Position Type*'
                });


                var range = ' <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2 " id="range_div">\n' +

                    '                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" float: left;">\n' +
                    '                                            <div class="form-group ">\n' +
                    '                                                <input type="text" class="form-control mr-1" id="from" name="salary_from" placeholder="From*" data-rule-required="true" data-msg-required="Salary From is required">\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" float: left;">\n' +
                    '                                            <div class="form-group ">\n' +
                    '                                                <input type="text" class="form-control" id="to" name="salary_to" placeholder="To*" data-rule-required="true" data-msg-required="Salary To is required">\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '                                    </div>';

                $("#erf_form #position_div").after(range);

                $('#from').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                });

                $('#to').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                });

                var qualification = ' <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="qualification_div" >\n' +
                    '                                        <div class="form-group ">\n' +
                    '                                            <textarea class="form-control" rows="5" cols="100" id="qualification" name="qualification" placeholder="Qualifications*" data-rule-required="true" data-msg-required="Qualification is required"></textarea>\n' +
                    '                                        </div>\n' +
                    '                                    </div>';

                $("#erf_form #range_div").after(qualification);

                var skills = ' <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="skills_div">\n' +
                    '                                        <div class="form-group ">\n' +
                    '                                            <textarea class="form-control" rows="5" cols="100" id="skills" name="skills" placeholder="Required Skills*" data-rule-required="true" data-msg-required="Skills are required"></textarea>\n' +
                    '                                        </div>\n' +
                    '                                    </div>';

                $("#erf_form #qualification_div").after(skills);

                var description = ' <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="description_div">\n' +
                '                                        <div class="form-group ">\n' +
                '                                            <textarea class="form-control" rows="5" cols="100" id="job_description" name="job_description" placeholder="Job Description*" data-rule-required="true" data-msg-required="Job Description is required"></textarea>\n' +
                '                                        </div>\n' +
                '                                    </div>';

                $("#erf_form #skills_div").after(description);


                var allowance = ' <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2" id="allowance_div">\n' +
                    '                                        <div class="form-group ">\n' +
                    '                                            <select name="allowances[]" class="select2" id="allowance" multiple="multiple" data-rule-required="true" data-msg-required="Allowances are required">\n' +
                    '                                              @foreach($allowances as $allowance)\n' +
                    '                                                  <option value="{{ $allowance->id }}">{{ $allowance->name }}</option>\n' +
                    '                                              @endforeach\n' +
                    '                                          </select>\n' +
                    '                                        </div>\n' +
                    '                                    </div>';

                $("#erf_form #description_div").after(allowance);
                $('#allowance').select2({
                    width:'100%',
                    placeholder:"Allowances*",
                    allowClear:true,
                    dropdownParent:$('#erf_form')
                });

            });


            var today = '{{ $today }}';
            $('#replacement').click(function () {
                $("#department").val('').change();
                $("#designation").val('').change();
                $("#department_head").val('').change();
                $("#city").val('').change();
                $("#hub").val('').change();
                $("div").remove("#vacancies_div,#position_div,#allowance_div,#description_div,#skills_div,#qualification_div,#range_div");

                var new_row ='<hr>'+
                    '<div class="row w-100 justify-content-center mb-5" id="new_div">' +
                    '<table class="table table-bordered mb-5" id="dynamic_field">  \n' +
                    '                    <tr>  \n' +
                    '<td><div class="form-group"><select data-rule-required="true" data-msg-required="Trax Id is required" class="form-control select2" id="trax_id" name="addmore[0][trax_id]"><option value=""> Trax Id </option>@foreach($employee_trax_id as $employee)<option value="{{$employee->trax_id}}">{{$employee->trax_id}}</option>@endforeach</select></div></td> \n' +
                    '                        <td><div class="form-group"><input type="text" name="addmore[0][salary]" id="salary" placeholder="Last Gross Salary*" class="form-control name_list"  data-rule-required="true" data-msg-required="Salary is required" /></div></td>  \n' +
                    '                        <td><div class="form-group"> <input type="text" name="addmore[0][date]" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date*" data-rule-required="true" data-msg-required="Date is required"></div></td>  \n' +
                    '                        <td class="text-center"><button type="button" name="add" id="add" class="btn btn-success">Add </button></td> \n' +
                    '                    </tr>  \n' +
                    '                </table>'+

                    '</div>';
                $("#erf_form .button_div").prepend(new_row);
                $('#trax_id').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Trax Id*'
                });
                var to_date = $('#to_date').pickadate({
                    firstDay: 1,
                    clear: 'Clear',
                    max : new Date(today),
                    format:'dd mmmm, yyyy',
                    selectYears: true,
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd 23:59:59',
                    hiddenSuffix: '_formatted',
                    onOpen: function() {
                       // $('#to_date_root').css('top', '40px');
                        $('.picker').css('position','relative');
                    },
                    onSet: function(context) {

                    }
                });
                $('#salary').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                });


            });

            var select = $('#email').selectize({
                placeholder: 'Email(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function (dropdown) {
                    dropdown.remove();
                },
               /* onType: function (str) {
                    var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },*/
                create: function (input) {
                    var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

                    if (!regex.test(input)) {
                       return false;
                    }
                        return {
                            value: input,
                            text: input
                        }

                }
            });



             $('#email_form').validate({
                 ignore: [],
                 errorClass: 'danger',
                 successClass: 'success',
                 errorPlacement: function (error, element) {
                     error.addClass('w-100').appendTo(element.parents('.form-group'));
                 },
                 submitHandler: function (form) {
                     var email = $('#email').val();
                     $('#admin_email').val(email);
                     $('#erf_form').submit();
                 }
             });


            var i = 0;
            $('body').on('click','#add',function(){
                ++i;
                $("#dynamic_field").append('<tr><td><div class="form-group"><select data-rule-required="true" data-msg-required="Trax Id is required" class="form-control select2 trax_id" id="leavers_trax_id'+i+'" name="addmore['+i+'][trax_id]"><option value=""> Trax Id </option>@foreach($employee_trax_id as $employee)<option value="{{$employee->trax_id}}">{{$employee->trax_id}}</option>@endforeach</select></div></td><td><div class="form-group"><input type="text" name="addmore['+i+'][salary]" placeholder="Last Gross Salary*" class="form-control name_list"  data-rule-required="true" data-msg-required="Salary is required" id="gross_salary'+i+'" /></div></td> ' +
                    ' <td><div class="form-group"> <input type="text" name="addmore['+i+'][date]" class="form-control bg-primary border-primary white rounded-right " id="append_date'+i+'" placeholder="Date*" data-rule-required="true" data-msg-required="Date is required"></div></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-danger remove-tr">Remove</button></td></tr>');
                $('#leavers_trax_id'+i+'').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Trax Id*'
                });
                $('#gross_salary'+i+'').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                });

                var date = $('#append_date'+i+'').pickadate({
                    firstDay: 1,
                    clear: 'Clear',
                    max : new Date(today),
                    format:'dd mmmm, yyyy',
                    selectYears: true,
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd 23:59:59',
                    hiddenSuffix: '_formatted',
                    onOpen: function() {
                        $('.date_root').css('top', '40px');
                        $('.picker').css('position','relative');
                    },
                    onSet: function(context) {

                    }
                });
            });

            $(document).on('click', '.remove-tr', function(){
                $(this).parents('tr').remove();
            });
        });

        $('#erf_form').validate({
            ignore: [],
            errorClass: 'danger',
            successClass: 'success',
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            submitHandler: function(form) {
                $('#department_head').removeAttr('disabled');

                $('#EmailModal').modal('show');
                if($('#email').val() !== '' && $('#email').val() !== null ){
                    swal({
                        title: 'Please Wait!',
                        text: 'Your request is being processed!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();

                }
                
            }
        });


       /* $('#erf_form').on('keypress',function (e) {
            if(e.keyCode == 13) {
                e.preventDefault();
            }
        });*/

    </script>
@endsection