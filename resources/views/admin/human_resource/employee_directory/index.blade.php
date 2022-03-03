@extends('admin.layout.master')

@section('title', 'Employee Directory')

@section('content')
    <h1>Employee Directory</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12 ">
                                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                        <div class="col-5 mt-1">
                                            <div class="form-group input-group ">
                                                <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                                </div>
                                                <input type="text" name="search_date_from"
                                                       class="form-control pickadate bg-primary border-primary white rounded-right"
                                                       id="search_date_from" placeholder="Select From Date">
                                            </div>
                                        </div>
                                        <div class="col-5 mt-1">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                                </div>
                                                <input type="text" name="search_date_to"
                                                       class="form-control pickadate bg-primary border-primary white rounded-right"
                                                       id="search_date_to" placeholder="Select To Date">
                                            </div>
                                        </div>

                                        <div class="col-2 mt-1">
                                            <div class="form-group">
                                                <button type="button" id="search_filter_btn"
                                                        class="btn btn-outline-info btn-min-width"><i class="la la-search"></i>
                                                    Search
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Employee ID</th>
                                    <th class="border-primary border-darken-1">Employee Name</th>
                                    <th class="border-primary border-darken-1">Gender</th>
                                    <th class="border-primary border-darken-1">Hub</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">CNIC</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">Employee Type</th>
                                    <th class="border-primary border-darken-1">Rider Main Category</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">IBAN No.</th>
                                    <th class="border-primary border-darken-1">Zone</th>
                                    <th class="border-primary border-darken-1">Request/Document Status</th>
                                    <th class="border-primary border-darken-1">Employee Status</th>
                                    <th class="border-primary border-darken-1">Requested At</th>
                                    <th class="border-primary border-darken-1">Joining Date</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade text-left" id="editRiderModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="editRiderModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Edit Rider</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.human_resource.employee_directory.rider.update')}}" method="post" class="mt-1"
                      id="editRiderForm" novalidate="novalidate">
                    {{csrf_field()}}
                        <div id="rejoin_div_html"></div>
                        <div class="modal-body">
                            <div class="col text-center edit_ccd_rider_checkbox_div">
                                <label class="font-medium-2 font-weight-bold block">Credit Card on Delivery-CCD</label>
                                <div class="form-group">
                                    <label for="edit_ccd_rider_checkbox" class="font-medium-2 text-bold-600 mr-1">No</label>
                                    <input type="checkbox" name="edit_ccd_rider_checkbox" id="edit_ccd_rider_checkbox" class="edit_ccd_rider_checkbox">
                                    <label for="edit_ccd_rider_checkbox" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                                </div>
                            </div>
                        <div id="unEditableFields">
                            <div class="row">
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <select name="rider_type" id="rider_type_list" class="form-control select2"
                                                data-rule-required="true" data-msg-required="This field is required">
                                            @foreach($rider_types as $rider_type)
                                                <option value="{{$rider_type->id}}">{{$rider_type->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <select name="city_id" id="city_list" class="form-control select2"
                                                data-rule-required="true" data-msg-required="This field is required">
                                            @foreach($cities as $city)
                                                <option value="{{$city->id}}">{{$city->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <select name="shift_id" id="shift_list" class="form-control select2"
                                                data-rule-required="true" data-msg-required="This field is required">
                                            @foreach($employee_shifts as $shift)
                                                <option value="{{$shift->id}}">{{$shift->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>

                            </div>


                            <input type="hidden" name="employee_id" id="employee_id">
                            <div>
                                <div class="row mb-2">
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="rider_name" id="rider_name"
                                               placeholder="Rider Name" required data-rule-required="true"
                                               data-msg-required="This field is required">
                                    </fieldset>

                                </div>
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="phone" id="rider_phone"
                                               placeholder="Phone No." required data-rule-required="true"
                                               data-msg-required="This field is required">
                                    </fieldset>
                                </div>
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="cnic" id="rider_cnic"
                                               placeholder="CNIC" required data-rule-required="true"
                                               data-msg-required="This field is required">
                                    </fieldset>
                                </div>
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="pin" id="rider_pin"
                                               placeholder="PIN" required data-rule-required="true"
                                               data-msg-required="This field is required" data-rule-minlength="4"
                                               data-rule-maxlength="4">
                                    </fieldset>
                                </div>
                            </div>
                            </div>

                            <div class="row mb-2">
                            <div class="col">
                                <fieldset class="form-group">
                                    <textarea name="address" class="form-control" placeholder="Address" id="address"
                                              cols="30" rows="5" required data-rule-required="true"
                                              data-msg-required="This field is required"></textarea>
                                </fieldset>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <fieldset class="form-group">
                                    <select name="rider_main_category" id="main_category_list" class="form-control select2"
                                            data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($rider_main_categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col">
                                <fieldset class="form-group">
                                    <select name="rider_category" id="category_list" class="form-control select2"
                                            data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($rider_categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <fieldset class="form-group">
                                    <select name="category" id="category" class="form-control select2"
                                            data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($operation_rider_category as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col">
                                <fieldset class="form-group">
                                    <select name="route_id" id="route_list" class="form-control select2"
                                            data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($routes as $route)
                                            <option value="{{$route->id}}">{{$route->code}} ({{$route->start}}
                                                - {{$route->start}})
                                            </option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                        </div>
                        <div id="new_route_div" class="d-none">
                            <div class="row mb-2">
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="route_code" placeholder="Route Code" required data-rule-required="true" data-msg-required="This field is required">
                                    </fieldset>

                                </div>
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="start"  id="startSearchTextField" placeholder="Start Point" required data-rule-required="true" data-msg-required="This field is required">
                                    </fieldset>
                                </div>
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="end"  placeholder="End Point" required data-rule-required="true" data-msg-required="This field is required">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <fieldset class="form-group">
                                        <select name="route_type_id" id="route_type_id" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                                            <option value="" selected>Select Route Type</option>
                                            @foreach($route_types as $route_type)
                                                <option value="{{$route_type->id}}">{{$route_type->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <fieldset class="form-group">
                                        <textarea name="junction" class="form-control" placeholder="Add Junctions (comma seperated)" id="junction" cols="30" rows="5" required data-rule-required="true" data-msg-required="This field is required"></textarea>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning btn-min-width mr-1 mb-1" id="confirmAction">Update
                            Rider
                        </button>
                        <button type="button" class="btn btn-primary btn-min-width mr-1 mb-1" data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="UpdatePinModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="UpdatePinModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Edit Bolt & Sonic Pin</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.employee_directory.pin')}}" class="form-horizontal mb-1 justify-content-center" method="POST" id="UpdatePinForm" novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="employee_id" id="employee_id" value="">
                        <div class="form-group">
                            <div class="form-group position-relative">
                                <input type="password" name="pin" id="pin" class="form-control" placeholder="Bolt & Sonic Pin*" data-rule-required="true" data-msg-required="Bolt & Sonic Pin is required" data-rule-minlength="4" data-rule-maxlength="4">
                                <div class="form-control-position" id="peye">
                                    <i class="la la-eye success"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="edit" class="btn btn-primary" value="edit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="convertRiderModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="convertRiderModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Convert Rider</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.employee_directory.rider.convert')}}" class="form-horizontal mb-1 justify-content-center" method="POST" id="convertRiderForm" novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="employee_id" id="employee_id" value="">
                        <div class="form-group">
                            <select name="department_id" id="department" class="select2 form-control " data-rule-required="true" data-msg-required="Department is required" style="width: 100%">
                                @foreach($employee_department as $department)
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <select name="designation_id" id="designation" class="select2 form-control " data-rule-required="true" data-msg-required="Designation is required" style="width: 100%">
                            </select>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" class="btn btn-primary" value="edit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="designationChangeLogModal" data-backdrop="static" tabindex="-1" role="dialog" role="dialog" aria-labelledby="designationChangeLogModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="margin-left: 35%!important;">
            <div class="modal-content" style="width: 60%!important;">
                <div class="modal-header">
                    <h4 class="modal-title" id="designation_logs_heading">Designation Logs<span></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body designation_logs" id="designation_logs_body">
                    <table class="table table-sm table-bordered border" id="designation_logs_table">
                        <thead>
                            <tr class="bg-primary white">
                                <th class="border-primary border-darken-1">Designation</th>
                                <th class="border-primary border-darken-1">Updated By</th>
                                <th class="border-primary border-darken-1">Updated At</th>
                            </tr>
                        </thead>
                        <tbody id="designation_logs_table_body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            let route_id;
            var elm = document.getElementById("edit_ccd_rider_checkbox");
            var switchery = new Switchery(elm, { className: "switchery switchery-small", color: "#37BC9B" });
            $('#peye').on('mousedown',function(){$('#pin').attr('type','text')}).on('mouseup',function(){$('#pin').attr('type','password')});
            $('#editRiderForm #unEditableFields input,#editRiderForm #unEditableFields textarea,#editRiderForm #unEditableFields select').attr('disabled','disabled');
            $('#rider_type_list').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider Type',
                dropdownParent: $('#editRiderModal')
            });
            $('#category').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Functional Category',
                dropdownParent: $('#editRiderModal')
            });

            $('#city_list').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select City',
                dropdownParent: $('#editRiderModal')
            });

            $('#shift_list').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Shift',
                dropdownParent: $('#editRiderModal')
            });
            $('#route_list').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Route',
                dropdownParent: $('#editRiderModal')
            });
            $('#main_category_list').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider Main Category',
                dropdownParent: $('#editRiderModal')
            });

            $('#department').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Department',
                dropdownParent: $('#convertRiderModal')
            }).bind('change',function (){
                var id = $(this).val();
                if(id != "") {
                    $.ajax({
                        url: '{!! route('admin.human_resource.employee_directory.get.designation') !!}',
                        method: 'POST',
                        data: {
                            'department_id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            $("#designation").html("");
                            if (data.status == 1) {
                                let options = "";
                                $.each(data.designations, function (i, v) {
                                    options += "<option value='" + v.id + "'>" + v.name + "</option>";
                                });
                                $("#designation").html(options).val("").trigger('change');
                            } else {
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        });
                }
            });

            $('#designation').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Designation',
                dropdownParent: $('#convertRiderModal')
            });
            $('#category_list').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider Sub-Category',
                dropdownParent: $('#editRiderModal')
            });

            $('#city_list').on('change',function () {
                var routelist = $('#route_list');
                var id = $('#city_list').val();
                $.ajax({
                    url:'{!! route('admin.management.rider.category.ajax') !!}',
                    type:'GET',
                    dataType:'json',
                    data: {
                        'id':id,
                    },
                    success:function (data) {

                        routelist.empty();
                        $.each(data, function (key, value) {
                            var newOption = "<option value="+ value.id +">" + value.code + ' ('  + value.start + ' to ' + value.end +')' +"</option>";
                            routelist.append(newOption);
                        });
                        routelist.append('<option value="other">Other</option>').trigger('change');
                        routelist.val(route_id).trigger('change');
                    }
                });
            });

            $('#route_list').on('change', function () {
                var selection = $(this).val();
                if(selection == 'other'){
                    $('#new_route_div').removeClass('d-none');
                }else{
                    $('#new_route_div').addClass('d-none');
                }
            });

            var search_date_to = $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            var search_date_from = $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            $("#editRiderForm").validate({

                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    $('#editRiderForm #unEditableFields input,#editRiderForm #unEditableFields textarea,#editRiderForm #unEditableFields select').removeAttr('disabled');
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'Rider is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $("#UpdatePinForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.employee_directory.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Employee ID');
                            head.push('Employee Name');
                            head.push('Gender');
                            head.push('Hub');
                            head.push('City');
                            head.push('CNIC');
                            head.push('Phone No.');
                            head.push('Employee Type');
                            head.push('Rider Main Category');
                            head.push('Designation');
                            head.push('Department Name');
                            head.push('IBAN No.');
                            head.push('Zone Name');
                            head.push('Request/Document Status');
                            head.push('Employee Status');
                            head.push('Requested At');
                            head.push('Joining Date');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.employee_name);
                                row.push(values.gender);
                                row.push(values.employee_hub);
                                row.push(values.city);
                                row.push(values.cnic);
                                row.push(values.phone_number);
                                row.push(values.employee_type);
                                row.push(values.rider_main_category);
                                row.push(values.employee_designation);
                                row.push(values.department_name);
                                row.push(values.iban);
                                row.push(values.zone_name);
                                row.push(values.request_status);
                                row.push(values.status);
                                row.push(values.requested_at);
                                row.push(values.joining_date);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if (session('role_id') == 1 || session('role_id') == 6 || in_array(652, session('permissions')))
                    {
                        text: 'Approve',
                        className: 'btn btn-primary bulk_approve',
                        enabled: false,
                        action: function (e, dt, node, config) {swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes Approve Employee!',
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
                            if (confirm) {
                                swal({
                                    title: 'Please Wait!',
                                    text: 'Employee is being Approved',
                                    icon: 'info',
                                    buttons: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });

                                $.ajax({
                                    url: '{!! route('admin.human_resource.employee_directory.approve') !!}',
                                    method: 'POST',
                                    data: {
                                        'employee_ids[]': selected_rows,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                })
                                    .done(function (data) {
                                        if (data.status == 0) {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        } else {
                                            toastr.error(data.error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }
                                        swal.close();
                                        selected_rows = [];

                                        table.rows().deselect();
                                        table.draw('false');
                                    });
                            }
                        });
                        }
                    },
                    {
                        text: 'Reject',
                        className: 'btn btn-danger bulk_reject',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            swal({
                                title: 'Are You Sure?',
                                text: 'Select Yes Reject Employee!',
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
                                if (confirm) {
                                    swal({
                                        title: 'Please Wait!',
                                        text: 'Employee is being Rejected',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });

                                    $.ajax({
                                        url: '{!! route('admin.human_resource.employee_directory.reject') !!}',
                                        method: 'POST',
                                        data: {
                                            'employee_ids[]': selected_rows,
                                            '_token': '{{ csrf_token() }}'
                                        }
                                    })
                                        .done(function (data) {
                                            if (data.status == 0) {
                                                toastr.success(data.success, 'Success!', {
                                                    positionClass: 'toast-bottom-center',
                                                    containerId: 'toast-bottom-center'
                                                });
                                            } else {
                                                toastr.error(data.error, 'Error!', {
                                                    positionClass: 'toast-top-center',
                                                    containerId: 'toast-top-center'
                                                });
                                            }
                                            swal.close();
                                            selected_rows = [];

                                            table.rows().deselect();
                                            table.draw('false');
                                        });
                                }
                            });
                        }
                    },
                        @endif
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.bulk_approve').enable();
                                    table.button('.bulk_reject').enable();
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.bulk_approve').disable();
                                        table.button('.bulk_reject').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Employee Directory',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.human_resource.employee_directory.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[17, 'desc']],
                rowId: 'employee_id',
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return''; }
                    },
                    {data: 'trax_id', name: 'employees.trax_id', class: 'align-middle trax_id'},
                    {data: 'employee_name', name: 'employees.name', class: 'align-middle employee_name'},
                    {data: 'gender', name: 'eg.name', class: 'align-middle gender'},
                    {data: 'employee_hub', name: 'employee_hub', class: 'align-middle employee_hub', orderable: false, searchable: false},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'cnic', name: 'employees.cnic', class: 'align-middle cnic'},
                    {data: 'phone_number', name: 'employees.phone_number', class: 'align-middle phone_number'},
                    {data: 'employee_type', name: 'et.name', class: 'align-middle employee_type'},
                    {data: 'rider_main_category', name: 'rmc.name', class: 'align-middle rider_main_category'},
                    {data: 'employee_designation', name: 'ed.name', class: 'align-middle employee_designation'},
                    {data: 'department_name', name: 'ads.name', class: 'align-middle department_name'},
                    {data: 'iban', name: 'eb.iban', class: 'align-middle iban'},
                    {data: 'zone_name', name: 'ez.id', class: 'align-middle zone_name'},
                    {data: 'request_status', name: 'ers.name', class: 'align-middle request_status'},
                    {data: 'status', name: 'es.id', class: 'align-middle status'},
                    {data: 'requested_at', name: 'employees.created_at', class: 'align-middle requested_at'},
                    {data: 'joining_date', name: 'employees.joining_date', class: 'align-middle joining_date'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    $('td:eq(0)', row).addClass('select-checkbox');
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var employee_type = '<select name="employee_type_search" id="employee_type_search" class="select2 form-control">' +
                        '</select>';
                    // var department_type = '<select name="department_type_search" id="department_type_search" class="select2 form-control">' +
                    //     '</select>';
                    var employee_status = '<select name="employee_status_search" id="employee_status_search" class="select2 form-control">' +
                        '</select>';

                    var rider_main_categories = '<select name="rider_main_categories_search" id="rider_main_categories_search" class="select2 form-control">' +
                        '</select>';

                    var employee_zone = '<select name="employee_zone_search" id="employee_zone_search" class="select2 form-control">' +
                        '</select>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select')|| $(header).is('.employee_hub')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.employee_type'))
                        {
                            $(employee_type).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.status'))
                        {
                            $(employee_status).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.zone_name'))
                        {
                            $(employee_zone).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.rider_main_category'))
                        {
                            $(rider_main_categories).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        // else if($(header).is('.department_name'))
                        // {
                        //     $(department_type).appendTo($(search))
                        //         .on( 'change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         } ).wrap(td);
                        // }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    data = [{'id':1,'text':'Staff'},{'id':2,'text':'Rider - Permanent'},{'id':3,'text':'Rider - Incentive'},{'id':4,'text':'Intern'}];

                    $("#employee_type_search").prepend('<option value="" selected></option>').select2({
                        data: data,
                        placeholder: "Select Employee Type",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var status_data = $.map({!! $employee_statuses !!}, function (obj) {
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#employee_status_search").prepend('<option value="" selected></option>').select2({
                        data: status_data,
                        placeholder: "Select Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var zone_data = $.map({!! $employee_zones !!}, function (obj) {
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#employee_zone_search").prepend('<option value="" selected></option>').select2({
                        data: zone_data,
                        placeholder: "Select Zone",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var rider_main_categories_data = $.map({!! $rider_main_categories !!}, function (obj) {
                        obj.id = obj.name;
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#rider_main_categories_search").prepend('<option value="" selected></option>').select2({
                        data: rider_main_categories_data,
                        placeholder: "Select Rider Main Category",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    {{--var department_name_data = $.map({!! $employee_department !!}, function (obj) {--}}
                    {{--    obj.text = obj.name;--}}
                    {{--    return obj;--}}
                    {{--});--}}
                    {{--$("#department_type_search").prepend('<option value="" selected></option>').select2({--}}
                    {{--    data: department_name_data,--}}
                    {{--    placeholder: "Select Department Type",--}}
                    {{--    width: '100%',--}}
                    {{--    containerCssClass: 'select-xs',--}}
                    {{--    dropdownCssClass: 'form-control-sm p-0'--}}
                    {{--});--}}

                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.bulk_approve').enable();
                    table.button('.bulk_reject').enable();
                }
                else {
                    table.button('.bulk_approve').disable();
                    table.button('.bulk_reject').disable();
                }
            });

            $('body').on('click', '.approve', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes Approve Employee!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Employee is being Approved',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.approve') !!}',
                            method: 'POST',
                            data: {
                                'employee_ids[]': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
                // var id = $(this).data('target-id');
                // $('#employee_id').val(id);
                // $('#approveRiderModal').modal('show');
            });



            $('body').on('click', '.rejoin', function (e) {
                var id = $(this).data('target-id');
                var employee_type = table.row($(this).parents('tr')).data().employee_type_id;
                if(employee_type == 1) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes To Rejoin Employee!',
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
                        if (confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'Employee is being Rejoin',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });

                            $.ajax({
                                url: '{!! route('admin.human_resource.employee_directory.rejoin') !!}',
                                method: 'POST',
                                data: {
                                    'employee_id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                                .done(function (data) {
                                    if (data.status == 0) {
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    } else {
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    }
                                    swal.close();
                                    table.draw('false');
                                });
                        }
                    });
                }
                else{
                    edit_Rider_function(this,true);
                }
            });

            $('body').on('click', '.reject', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes Reject Employee!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Employee is being Rejected',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.reject') !!}',
                            method: 'POST',
                            data: {
                                'employee_ids[]': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });
            $('body').on('click', '.designation_logs', function (e) {
                var id = $(this).data('target-id');
                $.ajax({
                    url:'{!! route('admin.human_resource.employee_directory.designation_logs') !!}',
                    type:'POST',
                    data: {
                        'employee_id': id,
                        '_token':'{!! csrf_token() !!}'
                    }
                }).done(function (data) {
                    var table = document.getElementById('designation_logs_table_body');
                    if(data.status == 1){
                        data.logs.forEach(function(object) {
                            var tr = document.createElement('tr');
                            tr.innerHTML =
                                '<td>' + object.designation + '</td>' +
                                '<td>' + object.updated_by + '</td>' +
                                '<td>' + object.updated_at + '</td>';
                            table.appendChild(tr);
                        });
                        $('#designationChangeLogModal').modal('show');
                    }
                    else {
                        toastr.error(data.error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });
            });

            $('body').on('hidden.bs.modal', '#designationChangeLogModal', function () {
                $('#designationChangeLogModal #designation_logs_table #designation_logs_table_body').html('');
            });

            function edit_Rider_function(elm,rejoin=false)
            {
                var id = $(elm).data('target-id');
                var rider_name = table.row($(elm).parents('tr')).data().employee_name;
                var cnic = table.row($(elm).parents('tr')).data().cnic;
                var phone_no = table.row($(elm).parents('tr')).data().phone_number;
                var pin = table.row($(elm).parents('tr')).data().pin;
                var address = table.row($(elm).parents('tr')).data().address;
                var city_id = table.row($(elm).parents('tr')).data().city_id;
                var shift_id = table.row($(elm).parents('tr')).data().shift_id;
                var check_bit = table.row($(elm).parents('tr')).data().check_if_rider_present_bit;
                var sub_category = table.row($(elm).parents('tr')).data().rider_sub_category;
                var main_category = table.row($(elm).parents('tr')).data().rider_main_category_id;
                var rider_type = table.row($(elm).parents('tr')).data().rider_type_id;
                $('#city_list').val(city_id).trigger('change');
                $('#shift_list').val(shift_id).trigger('change');
                if(check_bit != null)
                {
                    // var rider_type = table.row($(elm).parents('tr')).data().active_rider_type_id;
                    // $('#main_category_list').val(table.row($(elm).parents('tr')).data().category_id).trigger('change');
                    // $('#category_list').val(table.row($(elm).parents('tr')).data().category_id).trigger('change');
                    $('#category').val(table.row($(elm).parents('tr')).data().operation_id).trigger('change');
                    route_id = table.row($(elm).parents('tr')).data().route_id;
                    var ccd = table.row($(elm).parents('tr')).data().ccd;
                }
                else{
                    // var rider_type = table.row($(elm).parents('tr')).data().inactive_rider_type_id;
                    var ccd = false;
                    route_id = null;
                }
                ccd = Boolean(ccd)
                $('#editRiderModal #employee_id').val(id);
                $('#rider_name').val(rider_name);
                $('#rider_cnic').val(cnic);
                $('#rider_phone').val(phone_no);
                $('#rider_pin').val(pin);
                $('#address').val(address);
                $('#rider_type_list').val(rider_type).trigger('change');
                $('#main_category_list').val(main_category).trigger('change');
                $('#category_list').val(sub_category).trigger('change');
                if(rider_type == 1)
                {
                    $(".edit_ccd_rider_checkbox_div").show();
                    if(ccd != document.getElementById("edit_ccd_rider_checkbox").checked) {
                        switchery.setPosition(true);
                        switchery.handleOnchange(true);
                    }
                }
                else{
                    $(".edit_ccd_rider_checkbox_div").hide();
                }

                if(rejoin)
                {
                    $('#editRiderModal .modal-title').text("Rejoin Rider");
                    $('#editRiderModal .modal-footer #confirmAction').text("Rejoin Rider");
                    $("#editRiderForm #rejoin_div_html").html("<input type='hidden' name='rejoin_rider_bit' value='1'>");
                }
                else{
                    $('#editRiderModal .modal-title').text("Update Rider");
                    $('#editRiderModal .modal-footer #confirmAction').text("Update Rider");
                    $("#editRiderForm #rejoin_div_html").html("");
                }
                $('#editRiderModal').modal('show');
            }

            $('body').on('click', '.update_rider', function (e) {
                edit_Rider_function(this);

            });

            $("#UpdatePinModal #pin").inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'mask':"9999",
                'clearIncomplete': true,
            });

            $('body').on('click', '.update_pin_btn', function (e) {
                var employee_id = table.row($(this).parents('tr')).data().employee_id;
                var pin = table.row($(this).parents('tr')).data().pin;
                $('#UpdatePinModal #employee_id').val(employee_id);
                $('#UpdatePinModal #pin').val(pin);
                $('#UpdatePinModal').modal('show');
            });

            $('body').on('hidden.bs.modal', '#editRiderModal', function () {
                $('#editRiderModal #employee_id').val('');
                $('#rider_name').val('');
                $('#rider_cnic').val('');
                $('#rider_phone').val('');
                $('#rider_pin').val('');
                $('#address').val('');
                $('#city_list').val(null).trigger('change');
                $('#rider_type_list').val(null).trigger('change');
                $('#route_list').val(null).trigger('change');
                $('#main_category_list').val(null).trigger('change');
                $('#category_list').val(null).trigger('change');
                $('#category').val(null).trigger('change');


            });

            $('body').on('hidden.bs.modal', '#UpdatePinModal', function () {
                $('#UpdatePinModal #employee_id').val('');
                $('#pin').val('');
            });

            $('body').on('click', '.incentive', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make Rider Incentive!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Making Rider Incentive',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.rider.incentive') !!}',
                            method: 'POST',
                            data: {
                                'employee_id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('body').on('click', '.permanent', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make Rider Permanent!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Making Rider Permanent',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.rider.permanent') !!}',
                            method: 'POST',
                            data: {
                                'employee_id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('body').on('click', '.blacklist', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make Rider Blacklist!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Making Rider Blaclist',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.rider.blacklist') !!}',
                            method: 'POST',
                            data: {
                                'employee_id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('body').on('click', '.deactivate', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make Rider Inactive!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Making Rider Inactive',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.rider.deactivate') !!}',
                            method: 'POST',
                            data: {
                                'employee_id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('body').on('click', '.deactivate_staff', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make Staff Inactive!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Making Staff Inactive',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.staff.deactivate') !!}',
                            method: 'POST',
                            data: {
                                'employee_id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('body').on('click', '.activate', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make Rider Active!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Making Rider Active',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.rider.activate') !!}',
                            method: 'POST',
                            data: {
                                'employee_id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('body').on('click', '.activate_staff', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Make Staff Active!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Making Staff Active',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.staff.activate') !!}',
                            method: 'POST',
                            data: {
                                'employee_id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });


            $('body').on('click', '.convert_rider_to_staff', function (e) {
                var id = $(this).data('target-id');
                var employee_type = table.row($(this).parents('tr')).data().employee_type_id;
                if(employee_type == 2) {
                    $("#convertRiderForm #employee_id").val(id);
                    $("#convertRiderModal").modal('show');
                }
            });

            $('body').on('hidden.bs.modal', '#convertRiderModal', function () {
                $('#convertRiderForm #employee_id').val('');
                $('#convertRiderForm #department').val('').trigger('change');
                $('#convertRiderForm #designation').val('').trigger('change');
            });

            $("#convertRiderForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes To Make Rider An Employee!',
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
                        if (confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'Converting Rider To Staff!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            form.submit();
                        }
                    });
                }
            });

            $('body').on('click', '.convert_intern_to_staff', function (e) {
                var id = $(this).data('target-id');
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes To Make Intern An Employee!',
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
                    if (confirm) {
                        swal({
                            title: 'Please Wait!',
                            text: 'Converting Intern To Staff!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: '{!! route('admin.human_resource.employee_directory.staff.convert') !!}',
                            method: 'POST',
                            data: {
                                'employee_id': id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                swal.close();
                                table.draw('false');
                            });
                    }
                });
            });

            $('#search_filter_btn').on('click',function () {
                table.draw(true);
            });




        });
    </script>

@endsection