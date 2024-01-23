@extends('admin.layout.master')

@section('title', 'Ticker')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Ticker
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body" style="height: 750px">
							@include('admin.inc.messages')

							<div class="justify-content-center">
								{{-- <div class="col-12 col-sm-12 col-md-12 col-lg-12">  value="{{ $admin_ticker->start_date ?? ''}}"--}}
									<form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.ticker.store') }}" novalidate="novalidate">
										{{ csrf_field() }}
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<textarea class="form-control summernote admin_ticker" name="admin_ticker" id="admin_ticker">{{ $admin_ticker['description'] ?? '' }}</textarea>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													    <div class="form-group input-group">
															<div class="input-group-prepend">
																<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
																	<span class="la la-calendar-o"></span>
																</span>
															</div>
															<input type="text" name="admin_start_date" id="admin_start_date" placeholder="Start Date" class="form-control bg-primary border-primary white rounded-right" >
													    </div>
												</div>
											</div>
											<div class="col-md-3">
													<div class="form-group">
														<div class="form-group input-group">
															<div class="input-group-prepend">
																<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
																	<span class="la la-calendar-o"></span>
																</span>
															</div>
															<input type="text" name="admin_end_date" id="admin_end_date" class="form-control bg-primary border-primary white rounded-right" placeholder="End Date">
													</div>
											</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<select class="select2 form-control textleft" name="admin_start_time" id="admin_start_time">
													
													</select>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<select class="form-control" name="admin_end_time" id="admin_end_time">
													
													</select>
												</div>
											</div>
										</div>
										
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">													
													<textarea class="form-control summernote" name="shipper_ticker" id="shipper_ticker">{{ $shipper_ticker['description'] ?? '' }}</textarea>
													{{-- <input type="text" name="shipper_ticker" class="form-control shipper_ticker" placeholder="Shipper Ticker" value="{{ $shipper_ticker['description'] ?? '' }}" > --}}
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
														<div class="form-group input-group">
															<div class="input-group-prepend">
																<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
																	<span class="la la-calendar-o"></span>
																</span>
															</div>
															<input type="text" name="shipper_start_date" id="shipper_start_date" placeholder="Start Date" class="form-control bg-primary border-primary white rounded-right">
													    </div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<div class="form-group input-group">
														<div class="input-group-prepend">
															<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
																<span class="la la-calendar-o"></span>
															</span>
														</div>
														<input type="text" name="shipper_end_date" id="shipper_end_date" class="form-control bg-primary border-primary white rounded-right" placeholder="End Date">
													</div> 	
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<select class="form-control" name="shipper_start_time" id="shipper_start_time">
														
													</select>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<select class="form-control" name="shipper_end_time" id="shipper_end_time">
													
													</select>
												</div>
											</div>

										</div>
										
										<button type="submit" class="btn btn-primary" style="margin-left: 50%">Update</button>
									</form>
								{{-- </div> --}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/summernote/summernote.css')}}">

	<style>
		.text-center {
			text-align: left !important;
		}
	</style>

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/summernote/summernote.js')}}" type="text/javascript"></script>


	<script>
		$(document).ready(function(){

			var admin_start_date=@json($admin_ticker['start_date']);
			var admin_end_date=@json($admin_ticker['end_date']);

			 $("#admin_start_date").val(admin_start_date).trigger('change');
			 $("#admin_end_date").val(admin_end_date).trigger('change'); 
	
			var shipper_start_date=@json($shipper_ticker['start_date']);
			var shipper_end_date=@json($shipper_ticker['end_date'] );


			$("#shipper_start_date").val(shipper_start_date).trigger('change');
			$("#shipper_end_date").val(shipper_end_date).trigger('change');
		
			  	$('.summernote').summernote({
					toolbar: [
						['style', ['bold', 'italic', 'underline', 'clear']],
						['para', ['ul', 'ol', 'paragraph']],
					],
					styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'], // This is to include headings in the style dropdown
					defaultParagraphSeparator: 'p', // Set the default paragraph separator to 'p'
					tooltip: false, // Disable tooltips for the toolbar buttons
					disableDragAndDrop: true, // Disable drag and drop of files
					

            	});

			});
		
			$('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });		
	
		var admin_start_date = $('#admin_start_date').pickadate({
            firstDay: 1,
            clear: '',
            min: '{{ Carbon\Carbon::now() }}',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd',
            hiddenSuffix: '_formatted'
        }).bind('change',function(){
			if($(this).val()!='')
			{
				var selected_date = new Date($(this).val() + 'T00:00:00');
				var current_date = new Date();
				var admin_start_time=$("#admin_start_time");
				var current_start_time = @json($admin_ticker['start_time']);
				var current_start_time_formatted =  @json($admin_ticker['start_time_formatted']);
				if(selected_date.toDateString() === current_date.toDateString())
				{
					var currentHour = current_date.getHours();

					if (current_date.getMinutes() > 0) {
						currentHour++;
					}
					
				
					populateTimeOptions(currentHour, 24,admin_start_time,current_start_time);
					$("#admin_start_time").append('<option value='+current_start_time+' selected>'+current_start_time_formatted+'</option>');
				}
				else
				{
					populateTimeOptions(0, 24,admin_start_time,current_start_time);
					$("#admin_start_time").val(current_start_time).trigger('change');
					// append('<option value='+current_admin_start_time+' selected>'+current_admin_start_time+'</option>');

				}

				// $('#admin_start_time').prepend('<option value="" selected="selected">Start Time</option>').select2({
				// 	 width:'100%'
				// });

				

				$("#admin_start_time").attr('data-rule-required',true).attr('data-msg-required','Start Time is Required');
			}
			
			
		});

		
		
		
		var admin_start_time= $('#admin_start_time').prepend('<option value="" selected="selected">Start Time</option>').select2({
            width:'100%'
        });


		var admin_end_date = $('#admin_end_date').pickadate({
            firstDay: 1,
            clear: '',
            min: '{{ Carbon\Carbon::now() }}',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd',
            hiddenSuffix: '_formatted',
        }).bind('change',function(){
			if($(this).val()!='')
			{
					var selected_date = new Date($(this).val() + 'T00:00:00');
					var current_date = new Date();
					var admin_end_time=$("#admin_end_time");
					var current_end_time = @json($admin_ticker['start_time']);
					var current_end_time_formatted =  @json($admin_ticker['start_time_formatted']);
					if(selected_date.toDateString() === current_date.toDateString())
					{
						var currentHour = current_date.getHours();

						if (current_date.getMinutes() > 0) {
							currentHour++;
						}

						populateTimeOptions(currentHour, 24,admin_end_time,current_end_time);
						$("#admin_end_time").append('<option value='+current_end_time+' selected>'+current_end_time_formatted+'</option>');

					}
					else
					{
						populateTimeOptions(0, 24,admin_end_time,current_end_time);
						$("#admin_end_time").val(current_end_time).trigger('change');
					}

					// $('#admin_end_time').prepend('<option value="" selected="selected">Start Time</option>').select2({
					// 	 width:'100%'
					// });

					$("#admin_end_time").attr('data-rule-required',true).attr('data-msg-required','End Time is Required');
			}
		
			
		});;

		var admin_end_time= $('#admin_end_time').prepend('<option value="" selected="selected">End Time</option>').select2({
            width:'100%'
        });

		
		var shipper_start_date = $('#shipper_start_date').pickadate({
            firstDay: 1,
            clear: '',
            min: '{{ Carbon\Carbon::now() }}',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd',
            hiddenSuffix: '_formatted',
        }).bind('change',function(){
			if($(this).val()!='')
			{
					var selected_date = new Date($(this).val() + 'T00:00:00');
					var current_date = new Date();
					var shipper_start_time=$("#shipper_start_time");
					var current_start_time = @json($shipper_ticker['start_time']);
					var current_start_time_formatted =  @json($shipper_ticker['start_time_formatted']);
					if(selected_date.toDateString() === current_date.toDateString())
					{
						var currentHour = current_date.getHours();

						if (current_date.getMinutes() > 0) {
							currentHour++;
						}

						populateTimeOptions(currentHour, 24,shipper_start_time,current_start_time);
						$("#shipper_start_time").append('<option value='+current_start_time+' selected>'+current_start_time_formatted+'</option>');
					}
					else
					{
						populateTimeOptions(0, 24,shipper_start_time,current_start_time);
						$("#shipper_start_time").val(current_start_time).trigger('change');

					}

					// $('#shipper_start_time').prepend('<option value="" selected="selected">Start Time</option>').select2({
					// 	width:'100%'
					// });

					$("#shipper_start_time").attr('data-rule-required',true).attr('data-msg-required','Start Time is Required');
			}
	
			
		});
		
		var shipper_start_time= $('#shipper_start_time').prepend('<option value="" selected="selected">Start Time</option>').select2({
            width:'100%'
        });

		var shipper_end_date = $('#shipper_end_date').pickadate({
            firstDay: 1,
            clear: '',
            min: '{{ Carbon\Carbon::now() }}',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd',
            hiddenSuffix: '_formatted',
        }).bind('change',function(){
			if($(this).val()!='')
			{
				var selected_date = new Date($(this).val() + 'T00:00:00');
				var current_date = new Date();
				var shipper_end_time=$("#shipper_end_time");
				var current_end_time = @json($shipper_ticker['end_time']);
				var current_end_time_formatted =  @json($shipper_ticker['end_time_formatted']);
				if(selected_date.toDateString() === current_date.toDateString())
				{
					var currentHour = current_date.getHours();

					if (current_date.getMinutes() > 0) {
						currentHour++;
					}

					populateTimeOptions(currentHour, 24,shipper_end_time);
					$("#shipper_end_time").append('<option value='+current_end_time+' selected>'+current_end_time_formatted+'</option>');

				}
				else
				{
					populateTimeOptions(0, 24,shipper_end_time);
					$("#shipper_end_time").val(current_end_time).trigger('change');;

				}

				$('#shipper_end_time').prepend('<option value="" selected="selected">End Time</option>').select2({
					width:'100%'
				});

				$("#shipper_end_time").attr('data-rule-required',true).attr('data-msg-required','End Time is Required');
			}
			
			
		});

		var shipper_end_time= $('#shipper_end_time').prepend('<option value="" selected="selected">End Time</option>').select2({
            width:'100%'
        });


				function populateTimeOptions(startHour, endHour,select,current_time=null) {
					select.empty();
					for (var hour = startHour; hour < endHour; hour++) {
						var formattedHour = (hour % 12 === 0) ? 12 : hour % 12;
						var amPm = (hour < 12) ? 'AM' : 'PM';
						var optionValue = ('0' + hour).slice(-2) + ':00';
						var optionText = formattedHour + ':00 ' + amPm;
						if(optionText!=current_time)
						{
							var option = new Option(optionText, optionValue);
							select.append(option);
						}

						
					}

   				}
		
	</script>
@endsection