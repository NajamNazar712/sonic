@extends('admin.layout.master')

@section('title', 'View Evaluation')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					View Evaluation
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

						

								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <select name="agent_id" class="select2" id="agent_id" data-rule-required="true" data-msg-required="Agent is required" readonly disabled>
												@foreach($agents as $agent)
                                                    @if ($agent->id == $qa_evaluations->agent_id)
                                                        
													<option value="{{ $agent->id }}" selected>{{ $agent->name }}</option>
                                                    @endif
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <select name="campaign_id" class="select2" id="campaign_id" data-rule-required="true" data-msg-required="Campaign is required" readonly disabled>
												@foreach($campaigns as $campaign)
                                                @if ($campaign->id == $qa_evaluations->campaign_id)
                                                        
													<option value="{{ $campaign->campaign_id }}" selected>{{ $campaign->campaign }}</option>
                                                    @endif
												@endforeach
											</select>
											
										</div>
									</div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <select name="nature_id" class="select2" id="nature_id" data-rule-required="true" data-msg-required="Nature is required" readonly disabled>
												@foreach($natures as $nature)
                                                @if ($nature->id == $qa_evaluations->nature_id)
                                                        
                                                <option value="{{ $nature->id }}" selected>{{ $nature->nature }}</option>
                                                @endif
												@endforeach
											</select>
											
										</div>
									</div>
                                    
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 call_duaration">
										<div class="form-group input-group ">
											<div class="input-group-prepend">
												<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
													<span class="la la-clock-o"></span>
												</span>
											</div>
                                            <input type="text" class="form-control bg-primary border-primary white rounded-right" name="call_duaration" id="call_duaration" value="{{$qa_evaluations->call_duaration}}" placeholder="Call Duration" data-rule-required="true" data-msg-required="Date/Time is required" readonly disabled>
										</div>
									</div>

                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group input-group ">
											<div class="input-group-prepend">
												<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
													<span class="la la-calendar-o"></span>
												</span>
															</div>
                                            <input type="text" class="form-control bg-primary border-primary white rounded-right" name="call_date_time" id="call_date_time" value="{{$qa_evaluations->call_date_time}}" placeholder="Date/Time" data-rule-required="true" data-msg-required="Date/Time is required" readonly disabled>
										</div>
									</div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
                                            <select name="query_by" class="select2" id="query_by" data-rule-required="true" data-msg-required="Query By is required" readonly disabled>
													<option value="Consignee">Consignee</option>
													<option value="Shipper">Shipper</option>
													<option value="Others">Others</option>
											</select>
											
										</div>
									</div>
									

                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 contact_number">
										<div class="form-group">
                                            <input type="text" class="form-control" name="contact_number" value={{$qa_evaluations->contact_number}} id="contact_number" placeholder="Caller's Contact #"  readonly disabled>
										</div>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 complain_number">
										<div class="form-group">
                                            <input type="text" class="form-control text-left" name="complain_number" value={{$qa_evaluations->complain_number}} id="complain_number" placeholder="Request/Complain #" data-rule-required="true" data-msg-required="Request/Complain # is required" readonly disabled>
										</div>
									</div>

									
									<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<div class="form-group">
											<textarea name="remarks" id="remarks_input" class="form-control" cols="30" rows="3" placeholder="Remarks*" data-rule-required="true" data-msg-required="Remarks is required" readonly disabled>{{$qa_evaluations->remarks}}</textarea>
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
            var campaign_id = $('#campaign_id').val();
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
                                        html1 +='<input type="checkbox" id="activity_'+val.id+'" class="activity" name="activity_ids[]" value="'+val.id+'">';
                                        html1 +='<label for="activity_'+val.id+'">'+val.activity+'</label>';
                                        html1 +='</fieldset>';
                                    }
                                });
                                html1 +='</div>';
                            });
                            

                            $("#handlings").html(html);
                            $("#activities").html(html1);
                           
                        }
                        $('#role_form .activity').each(function() {
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

			$('#contact_number').attr('disabled',true);
						$('#complain_number').attr('disabled',true);
						$('#call_duaration').attr('disabled',true);
						$(".contact_number").css("display","none");
						$(".complain_number").css("display","none");
						$(".call_duaration").css("display","none");

		
			$("#call_date_time").focus( function() {
				$(this).attr({type: 'datetime-local'});
			});

			
			
			$('#call_duaration').inputmask({
                'mask': '99:99:99',
                'clearIncomplete': true
            });

			$('#contact_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });
			$('#complain_number').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
            });
			
			

			$('#query_by').select2({
				width: '100%',
				placeholder: 'Query By*'
			});

            $('#nature_id').select2({
				width: '100%',
				placeholder: 'Nature*'
			});
            
            $('#evaluated_by').select2({
				width: '100%',
				placeholder: 'Evaluated By*'
			});
            
            
            
            $('#agent_id').select2({
				width: '100%',
				placeholder: 'Agent*'
			});
		

            $('#campaign_id').select2({
				width: '100%',
				placeholder: 'Campaign*'
			}).bind('change',function(){
                var campaign_id = $(this).val();
					if(campaign_id == 1){
						//call
						$(".contact_number").css("display","block")
						$(".complain_number").css("display","none")
						$(".call_duaration").css("display","block")

						$('#contact_number').attr('disabled',false);
						$('#call_duaration').attr('disabled',false);
						$('#complain_number').attr('disabled',true);

					}else{
						$(".contact_number").css("display","none")
						$(".complain_number").css("display","block")
						$(".call_duaration").css("display","none")

						
						$('#contact_number').attr('disabled',true);
						$('#call_duaration').attr('disabled',true);
						$('#complain_number').attr('disabled',false);

					}
                    
            });
			

			

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
	</script>
@endsection