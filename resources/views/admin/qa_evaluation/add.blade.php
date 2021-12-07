@extends('admin.layout.master')

@section('title', 'Add Evaluation')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Add Evaluation
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="role_form" class="form-horizontal" method="POST" action="{{ route('admin.qa_evaluation.submit') }}" novalidate="novalidate">
								{{ csrf_field() }}

								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <select name="agent_id" class="select2" id="agent_id" data-rule-required="true" data-msg-required="Agent is required">
												@foreach($agents as $agent)
													<option value="{{ $agent->id }}">{{ $agent->name }}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <select name="campaign_id" class="select2" id="campaign_id" data-rule-required="true" data-msg-required="Campaign is required">
												@foreach($campaigns as $campaign)
													<option value="{{ $campaign->campaign_id }}">{{ $campaign->campaign }}</option>
												@endforeach
											</select>
											
										</div>
									</div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <select name="evaluated_by" class="select2" id="evaluated_by" data-rule-required="true" data-msg-required="Evaluated By is required">
												@foreach($evaluated_by as $evaluated_admin)
													<option value="{{ $evaluated_admin->id }}">{{ $evaluated_admin->name }}</option>
												@endforeach
											</select>
											
										</div>
									</div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <select name="nature_id" class="select2" id="nature_id" data-rule-required="true" data-msg-required="Nature is required">
												@foreach($natures as $nature)
													<option value="{{ $nature->id }}">{{ $nature->nature }}</option>
												@endforeach
											</select>
											
										</div>
									</div>
                                    
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <input type="time" class="form-control" name="call_duaration" id="call_duaration">
                                          
											
										</div>
									</div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <input type="time" class="form-control" name="call_date_time" id="call_date_time">
										</div>
									</div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <select name="query_by" class="select2" id="query_by" data-rule-required="true" data-msg-required="Query By is required">
													<option value="Consignee">Consignee</option>
													<option value="Shipper">Shipper</option>
													<option value="Others">Others</option>
											</select>
											
										</div>
									</div>

                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <input type="text" class="form-control" name="contact_number" id="contact_number" placeholder="Contact #">
										</div>
									</div>

									<div class="col-12">
									</div>

									<div class="col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3">
										<h4 class="form-section mb-2">Handlings</h4>
                                        <div class="nav flex-column nav-pills border-info rounded-0" role="tablist" aria-orientation="vertical" id="handlings">
                                        </div>    
										
									</div>
									<div class="col-6 col-xs-6 col-sm-6 col-md-8 col-lg-9">
										<h4 class="form-section mb-2">Activity</h4>

                                        <div class="tab-content" id="activities">
                                        </div>
									</div>

									<div class="col-12">
										<div class="form-group text-center">
											<button type="submit" class="btn btn-primary">Add</button>
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
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#query_by').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Query By*'
			});

            $('#nature_id').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Nature*'
			});
            
            $('#evaluated_by').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Evaluated By*'
			});
            
            
            
            $('#agent_id').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Agent*'
			});

            $('#campaign_id').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Campaign*'
			}).bind('change',function(){
                var campaign_id = $(this).val();
                    $.ajax({
                        url: '{!! route('admin.qa_evaluation.handlings') !!}',
                        type: 'POST',
                        data: {
                            'id': campaign_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status) {
                            console.log(data);
                            var html = '';
                            $.each(data.evaluation_handlings, function (i, v) {
                                // console.log(v);
                                html +='<a class="nav-link rounded-0" id="handling_'+v.id+'_tab" data-toggle="pill" href="#module_'+v.id+'_tabpanel" role="tab" aria-controls="module_'+v.id+'_tabpanel" aria-selected="true">'+v.handling+'</a>';

                            });
                            var html1 = '';
                            $.each(data.evaluation_handlings, function (i, v) {
                                html1 +='<div class="tab-pane fade" id="module_'+v.id+'_tabpanel" role="tabpanel" aria-labelledby="module_'+v.id+'_tab">';
                                $.each(data.evaluated_activities, function (index, val) {
                                    if(v.id == val.evaluation_handling_id){
                                        html1 +='<fieldset class="d-inline-block m-1">';
                                        html1 +='<input type="checkbox" id="permission_'+val.id+'" class="permission" name="permission_ids[]" value="'+val.id+'">';
                                        html1 +='<label for="permission_'+val.id+'">'+val.activity+'</label>';
                                        html1 +='</fieldset>';
                                    }
                                });
                                html1 +='</div>';
                            });
                            

                            $("#handlings").html(html);
                            $("#activities").html(html1);
                            // $(".delivered_shipment_input").val('');
                            // $('.dncc_select').each(function (elm) {
                            //     $(this).empty().trigger('change');
                            //     $(this).html(dncc_data);
                            //     $(this).val('').trigger('change');
                            // });
                        }
                        $('#role_form .permission').each(function() {
                            var checkbox = $(this);
                            var label = checkbox.next();
                            var text = label.text();

                            label.remove();

                            checkbox.iCheck({
                                checkboxClass: 'icheckbox_line pt-1 pb-1',
                                checkedClass: 'checked bg-success',
                                uncheckedClass: 'bg-danger',
                                insert: '<div class="icheck_line-icon"></div>' + text
                            });
                        });
                    });
            });

			

			$('#role_form').validate({
                errorClass: 'danger',
				successClass: 'success',
                // $('.permission').val();
				normalizer: function(value) {
                    // if($('.permission').filter(':checked').length == 0){
                        
                    //     toastr.error('Please select an activity', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    // }
                    return $.trim(value);
				},
				errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
                    console.log($('.permission').filter(':checked').length)
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
	</script>
@endsection