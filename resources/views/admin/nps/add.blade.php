@extends('admin.layout.master')

@section('title', 'Add Survey')

@section('content')
    <style>
        .center {
            margin-left: auto;
            margin-right: auto;
        }
    </style>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Add Survey
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="nps_form" class="form-horizontal" method="POST"
                                  action="{{ route('admin.nps.submit') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="form-group input-group ">
                                            <input type="text" class="form-control" name="survey_name"
                                                   id="survey_name" placeholder="Survey Name" data-rule-required="true"
                                                   data-msg-required="Survey Name is Required">
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="form-group input-group ">
                                            <div class="input-group-prepend">
												<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
													<span class="la la-calendar-o"></span>
												</span>
                                            </div>
                                            <input type="text"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   min="{{$min_date}}" max="{{$max_date}}" name="start_time"
                                                   id="survey_start" placeholder="Start Time" data-rule-required="true"
                                                   data-msg-min="Cannot select date less than one year from current date."
                                                   data-msg-max="Cannot be greater than one year."
                                                   data-msg-required="Date/Time is required">
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="form-group input-group ">
                                            <div class="input-group-prepend">
												<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
													<span class="la la-calendar-o"></span>
												</span>
                                            </div>
                                            <input type="text"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   min="{{$min_date}}" max="{{$max_date}}" name="end_time"
                                                   id="survey_end" placeholder="End Time" data-rule-required="true"
                                                   data-msg-min="Cannot select date less than one year from current date."
                                                   data-msg-max="Cannot be greater than one year."
                                                   data-msg-required="Date/Time is required">
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="form-group input-group ">
                                            <label class="font-medium-2 font-weight-bold block">Shippers</label>
                                            <select name="shipper_ids[]" id="shippers_select" class="form-control select2"
                                                    multiple="multiple" disabled
                                                    data-msg-required="Atleast one shipper is required"
                                                    data-rule-required="true" required="required">
                                                @foreach($shippers as $shipper)
                                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <label class="font-medium-2 font-weight-bold block">All Shippers</label>
                                        <div class="form-group">
                                            <label for="all_shippers_checkbox" class="font-medium-2 text-bold-600 mr-1">No</label>
                                            <input type="checkbox" name="all_shipper" id="all_shippers_checkbox" class="switchery all_shippers_checkbox" data-size="sm" data-switchery="true" checked>
                                            <label for="all_shippers_checkbox" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <label class="font-medium-2 font-weight-bold block">Recommendation Box</label>
                                        <div class="form-group">
                                            <label class="font-medium-2 text-bold-600 mr-1">No</label>
                                            <input type="checkbox" name="recommendation_box" id="recommendation_box" class="switchery recommendation_box" data-size="sm" data-switchery="true" checked>
                                            <label  class="font-medium-2 text-bold-600 ml-1">Yes</label>
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                            <table class="table table-bordered table-striped text-center"
                                                   id="survey_data">
                                                <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Question</th>
                                                    <th>
                                                        <button onclick="add_row()" type="button"
                                                                class="btn btn-info btn-sm"><span
                                                                    class="la la-plus"></span>
                                                        </button>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td> <div class="form-group" style="margin-bottom:0px"><input  data-rule-required="true" data-msg-required="Question is required" class="form-control question" name="question[0]"></div></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>



                                    <div class="col-12">
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function () {

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




            $("#survey_start").focus(function () {
                $(this).attr({type: 'datetime-local'});
            });
            $("#survey_end").focus(function () {
                $(this).attr({type: 'datetime-local'});
            });


        });


        var validator = $('#nps_form').validate({
            errorClass: 'danger',
            ignore: ":not(:visible),:disabled",
            successClass: 'success',
            errorPlacement: function (error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            normalizer: function (value) {
                return $.trim(value);
            },
            submitHandler: function (form) {
                $(form).find('button[type=submit]').attr('disabled', 'disabled');

                swal({
                    title: 'Please Wait!',
                    text: 'NPS SURVEY ADDED SUCCESSFULLY!',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                });


                if ($("#nps_form").valid()) {
                    form.submit();
                }

            }
        });


        var serial_no = 1;

        function add_row() {
            serial_no++;
            var add_row = `
                  <tr>
                    <td>${serial_no}</td>
                      <td> <div class="form-group" style="margin-bottom:0px"><input id=question${serial_no} data-rule-required="true" data-msg-required="Question is required" class="form-control question" name="question[${serial_no}]"></div>

                        </td>
                    <td><button onclick="$(this).closest('tr').remove();" type="button" class="btn btn-danger btn-sm"><span class="ft-x"></span></button></td>
                  </tr>
            `;
            $('#survey_data tbody').append(add_row);


            $(`#question${serial_no}`).each(function(){
                $( this ).rules( "add", {
                    required: true,
                });
            });
        }




    </script>
@endsection