@extends('admin.layout.master')

@section('title', 'Edit Activities')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Edit Activities
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="activities_form" class="form-horizontal"  method="post" action="{{ route('admin.qa_evaluation.update_activities') }}" novalidate="novalidate">
                                @csrf
								<div class="row">
									
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <select name="handling_id" class="select2" id="handling_id" data-rule-required="true" data-msg-required="Evaluation Handling is required">
												@foreach($evaluation_handlings as $evaluation_handling)
													<option value="{{ $evaluation_handling->id }}">{{ $evaluation_handling->handling }}</option>
												@endforeach
											</select>
											
										</div>
									</div>
                                    <input type="hidden" id="activities_id" name="activities_id">
                                    <input type="hidden" id="activities_weightage" name="activities_weightage">
                                    <input type="hidden" id="activities_name" name="activities_name">
									
								</div>
                                <div class="col-12 ">
                                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                        <thead>
                                            <tr role="row" class="bg-primary white">
                                                <th class="border-primary border-darken-1">S. No.</th>
                                                <th class="border-primary border-darken-1">Activity</th>
                                                <th class="border-primary border-darken-1">Weightage</th>
                                                <th class="border-primary border-darken-1"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="col-12">
                                    <div class="form-group text-center">
                                        <a href="javascript:void(0);" id="add_activity" class="btn btn-primary add_activity">Add</a>
                                        <button id="submit_button" type="submit" class="btn btn-success">Update</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <style>
        a.disabled {
            pointer-events: none;
            cursor: default;
        }
    </style>


@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    


	<script>
		$(document).ready(function() {
            var activity_id_array = [];
            var activity_weightage_array = [];
            var activity_name_array = [];
            var activity_index_array = [];

            $('#submit_button').attr('disabled', true);
            $('a.add_activity').addClass('disabled');
            
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                paging: false,
                ordering:false,
                sorting:false,
                bInfo:false,
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {
                        
                        name: 'activity',
                        class: 'align-middle activity',
                       
                    },
                    {
                        
                        name: 'weightage',
                        class: 'align-middle weightage',
                        
                    },
                    {
                        
                        name: 'action',
                        class: 'align-middle action',
                        
                    }

                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
            });
            
            
            $('#handling_id').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Select Handling*'
			}).bind('change',function(){
                var handling_id = $(this).val();
                table.rows().remove();
                 activity_id_array = [];
                 activity_weightage_array = [];
                 activity_name_array = [];
                 activity_index_array = [];
                $.ajax({
                        url: '{!! route('admin.qa_evaluation.actvities_data') !!}',
                        type: 'POST',
                        data: {
                            'id': handling_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                            
                        var counter = 1
                        var rowNo = table.rows().count();
                
                    $.each(data.activities, function (i, v) {
                                                    
                        table.row.add([counter,'<div class="form-group input-group"><input type="text" id="activity_'+v.id+'" class="form-control activity_name" name="activity_name[]" value="'+v.activity+'"  data-rule-required="true" data-msg-required="Activity is required"></div>','<div class="form-group input-group"><input type="number" id="weightage_'+v.id+'" class="form-control activity_weightage" name="activity_weightage[]" value="'+v.weightage+'"  data-rule-required="true" data-msg-required="Weeightage is required"></div>','<div class="form-group input-group"><a href="javascript:void(0);" class="btn btn-sm btn-danger remove"><i class="ft-minus-circle"></i></a></div>']).node().id = rowNo;
                        counter++;
                                    activity_id_array.push(v.id);
                                    activity_weightage_array.push(v.weightage);
                                    activity_name_array.push(v.activity);
                                    activity_index_array.push(rowNo);
                                    rowNo++;
                                    
                    });
                    $('#submit_button').attr('disabled', false);
                    $('a.add_activity').removeClass('disabled')
                    
                    // console.log('activity_id_array');
                    //                 console.log(activity_id_array);
                    //                 console.log('activity_weightage_array');
                    //                 console.log(activity_weightage_array);
                    //                 console.log('activity_name_array');
                    //                 console.log(activity_name_array);
                    //                 console.log('activity_index_array');
                    //                 console.log(activity_index_array);
                    table.draw();
                });

            });

            

            $('#datatable').on('click', 'a.remove', function(){
                var rowId = parseInt($(this).parents('tr').attr('id'));

                var index = $.inArray(rowId, activity_index_array);

                console.log(index);
                if (index !== -1) {
                console.log('inside');

                    activity_id_array.splice(index, 1);
                    activity_weightage_array.splice(index, 1);
                    activity_name_array.splice(index, 1);
                    activity_index_array.splice(index, 1);
                }
                table.row( $(this).parents('tr') ).remove().draw();
                if(table.rows().count() == 0){
                    $('#submit_button').attr('disabled', true);
                    $('a.add_activity').addClass('disabled')
                    
                }
                                    console.log('activity_id_array');
                                    console.log(activity_id_array);
                                    console.log('activity_weightage_array');
                                    console.log(activity_weightage_array);
                                    console.log('activity_name_array');
                                    console.log(activity_name_array);
                                    console.log('activity_index_array');
                                    console.log(activity_index_array);
            });


            $('a.add_activity').click(function () {

                var rowNo = table.rows().count();
               
                table.row.add([rowNo,'<div class="form-group input-group"><input type="text" id="" class="form-control activity_name" name="activity_name['+rowNo+']" data-rule-required="true" data-msg-required="Activity is required"></div>','<div class="form-group input-group"><input type="number" id="" class="form-control activity_weightage" name="activity_weightage['+rowNo+']" data-rule-required="true" data-msg-required="Weeightage is required"></div>','<div class="form-group input-group"><a href="javascript:void(0);" class="btn btn-sm btn-danger remove"><i class="ft-minus-circle"></i></a></div>']).node().id = rowNo;
                                    activity_id_array.push(0);
                                    activity_index_array.push(rowNo);
                                    rowNo++;
                                    table.draw();
            });

            
           


            // $('#activities_form').validate({
            //     // ignore: ":not(:visible),:disabled",
            //     errorClass: 'danger',
            //     successClass: 'success',
            //     errorPlacement: function(error, element) {
            //         error.addClass('w-100').appendTo(element.parent('.form-group'));
            //     },
            //     submitHandler: function(form) {
            //         if(table.rows().count() > 0){
            //             $('#activities_id').val(activity_id_array);
            //             $('#activities_weightage').val(activity_weightage_array);
            //             $('#activities_name').val(activity_name_array);
            //                    Swal.fire({
            //                         type: 'info',
            //                         title: 'Please Wait!',
            //                         text: 'Your Request is being generated!',
            //                         showCancelButton: false,
            //                         showConfirmButton: false,
            //                         allowOutsideClick: false,
            //                     });
            //                     form.submit();

            //         }
            //     }
            // });

            $('#activities_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    // var msg = "";
                    // if($('#status').val() == 1){
                    //     msg = "Runner On Route is being marked as completed!"
                    // }else{
                    //     msg = 'Runner On Route is being updated!';
                    // }
                    swal({
                        type: 'info',
                        title: 'Please Wait!',
                        text: 'Your Request is being generated!',
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });

                    form.submit();
                }
            });

            
            // $('#submit_form').click(function () {

            //     $('#role_form').validate({
            //         errorClass: 'danger',
            //         successClass: 'success',
            //         normalizer: function(value) {
                        
            //             return $.trim(value);
            //         },
            //         errorPlacement: function(error, element) {
            //             error.addClass('w-100').appendTo(element.parent('.form-group'));
            //         },
            //         submitHandler: function(form) {
            //             $(form).find('button[type=submit]').attr('disabled', 'disabled');
            //                     swal({
            //                         title: 'Please Wait!',
            //                         text: 'Evaluation is being added!',
            //                         icon: 'info',
            //                         buttons: false,
            //                         closeOnClickOutside: false,
            //                         closeOnEsc: false
            //                     });
            //                     form.submit();
            //         }
			//     });
            // });
			
		});
	</script>
@endsection