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

							<form id="role_form" class="form-horizontal"  method="post">
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
                                    
									<div class="col-12">
										<div class="form-group text-center">
											<button id="submit_form" type="submit" class="btn btn-primary">Update</button>
										</div>
									</div>
								</div>
								</form>
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


			// var table = $('#datatable').DataTable({
            //     dom: 'ltipr',
            //     paging: false,
            //     ordering:false,
            //     sorting:false,
            //     bInfo:false,
            //     columns: [
            //         {
            //             orderable: false,
            //             searchable: false,
            //             name: 'serial_number',
            //             class: 'align-middle serial_number',
            //             targets: 1,
                        
            //         },
                   

            //     ],
            //     rowCallback: function (row, data, index) {
            //         var info = table.page.info();

            //         $('td:eq(0)', row).html(index + 1 + info.page * info.length);

            //     },
            // });
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
                        orderable: false,
                        searchable: false,
                        name: 'activity',
                        class: 'align-middle activity',
                       
                    },
                    {
                        orderable: false,
                        searchable: false,
                        name: 'weightage',
                        class: 'align-middle weightage',
                        
                    },
                    {
                        orderable: false,
                        searchable: false,
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

                $.ajax({
                        url: '{!! route('admin.qa_evaluation.actvities_data') !!}',
                        type: 'POST',
                        data: {
                            'id': handling_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        console.log(data.activities);
                            var html = '';
                            var html1 = '';
                            var html2 = '';
                        var counter = 1
                var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger remove"><i class="la la-close"></i></a>';
                        
                $.each(data.activities, function (i, v) {

                                        html +='<div class="form-group">';
                                            html +='<input type="text" id="activity_'+v.id+'" class="form-control activity_name" name="activity_name[]" value="'+v.activity+'">';
                                            html +='</div>';
                                            html1 +='<div class="form-group">';
                                            html1 +='<input type="number" id="weightage_'+v.id+'" class="form-control activity_weightage" name="activity_weightage[]" value="'+v.weightage+'">';
                                            html1 +='</div>';
                                            html2 +='<div class="form-group">';
                                                html2 +='<button id="'+v.id+'" class="btn btn-danger remove_activity" name="remove_activity_[]">x</button>';
                                                html2 +='</div>';
                                                
                    table.row.add([counter,'<input type="text" id="activity_'+v.id+'" class="form-control activity_name" name="activity_name[]" value="'+v.activity+'">','<input type="number" id="weightage_'+v.id+'" class="form-control activity_weightage" name="activity_weightage[]" value="'+v.weightage+'">','<button id="'+v.id+'" class="btn btn-danger remove_activity" name="remove_activity_[]">x</button>']).node().id = v.id;
                    
                    counter++;
                            });
                            table.draw();
                            // add_button ='<div class="form-group">';
                            //     add_button +='<button id="add_activity" class="btn btn-primary add_activity" name="add_activity_[]">+</button>';
                            //                     add_button +='</div>';
                            // $("#activity").html(html);
                            // $("#weightage").html(html1);
                            // $("#add_remove").html(html2);
                            // $("#activity").html($("#activity").html()+'');
                            // $("#weightage").html($("#weightage").html()+'');
                            // $("#add_remove").html($("#add_remove").html()+add_button);
                            
                    });

            });

            // $('#campaign_id').prepend('<option value="" selected="selected"></option>').select2({
			// 	width: '100%',
			// 	placeholder: 'Campaign*'
			// }).bind('change',function(){
            //     var campaign_id = $(this).val();
			// 		if(campaign_id == 1){
			// 			//call
			// 			$(".contact_number").css("display","block")
			// 			$(".complain_number").css("display","none")
			// 			$(".call_duaration").css("display","block")

			// 			$('#contact_number').attr('disabled',false);
			// 			$('#call_duaration').attr('disabled',false);
			// 			$('#complain_number').attr('disabled',true);

			// 		}else{
			// 			$(".contact_number").css("display","none")
			// 			$(".complain_number").css("display","block")
			// 			$(".call_duaration").css("display","none")

						
			// 			$('#contact_number').attr('disabled',true);
			// 			$('#call_duaration').attr('disabled',true);
			// 			$('#complain_number').attr('disabled',false);

			// 		}
            //         $.ajax({
            //             url: '{!! route('admin.qa_evaluation.handlings') !!}',
            //             type: 'POST',
            //             data: {
            //                 'id': campaign_id,
            //                 '_token': '{{ csrf_token() }}'
            //             }
            //         }).done(function (data) {
            //             if (data.status) {
            //                 console.log(data);
            //                 var html = '';
            //                 $.each(data.evaluation_handlings, function (i, v) {
            //                     html +='<a class="nav-link rounded-0" id="handling_'+v.id+'_tab" data-toggle="pill" href="#module_'+v.id+'_tabpanel" role="tab" aria-controls="module_'+v.id+'_tabpanel" aria-selected="true">'+v.handling+'</a>';

            //                 });
            //                 var html1 = '';
            //                 $.each(data.evaluation_handlings, function (i, v) {
            //                     html1 +='<div class="tab-pane fade" id="module_'+v.id+'_tabpanel" role="tabpanel" aria-labelledby="module_'+v.id+'_tab">';
            //                     $.each(data.evaluated_activities, function (index, val) {
            //                         if(v.id == val.evaluation_handling_id){
            //                             html1 +='<fieldset class="d-inline-block m-1">';
            //                             html1 +='<input type="checkbox" id="activity_'+val.id+'" class="activity" name="activity_ids[]" value="'+val.id+'">';
            //                             html1 +='<label for="activity_'+val.id+'">'+val.activity+'</label>';
            //                             html1 +='</fieldset>';
            //                         }
            //                     });
            //                     html1 +='</div>';
            //                 });
                            

            //                 $("#handlings").html(html);
            //                 $("#activities").html(html1);
                          
            //             }
            //             $('#role_form .activity').each(function() {
            //                 var checkbox = $(this);
            //                 var label = checkbox.next();
            //                 var text = label.text();

            //                 label.remove();

            //                 checkbox.iCheck({
            //                     checkboxClass: 'icheckbox_line pt-1 pb-1',
            //                     checkedClass: 'checked bg-success',
            //                     uncheckedClass: 'bg-danger',
            //                     insert: '<div class="icheck_line-icon"></div>' + text
            //                 });
            //             });
            //         });
            // });
			

            $('#remove_activity').click(function () {
            
            
            });
            
            $('#submit_form').click(function () {

                $('#role_form').validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    normalizer: function(value) {
                        
                        return $.trim(value);
                    },
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    submitHandler: function(form) {
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');
                                swal({
                                    title: 'Please Wait!',
                                    text: 'Evaluation is being added!',
                                    icon: 'info',
                                    buttons: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });
                                form.submit();
                    }
			    });
            });
			
		});
	</script>
@endsection