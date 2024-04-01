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
										<h3>Admin Ticker</h3>
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
											<div class="col-md-2">
												<div class="form-group">
													<select class="select2 form-control textleft" name="admin_start_time" id="admin_start_time">
													
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<select class="form-control" name="admin_end_time" id="admin_end_time">
													
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<span class="sm btn btn-primary" id="admin_reset"> Reset</span>
												</div>
											</div>
										</div>
										<br>		
										<h3>Shipper Ticker</h3>
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
											<div class="col-md-2">
												<div class="form-group">
													<select class="form-control" name="shipper_start_time" id="shipper_start_time">
														
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<select class="form-control" name="shipper_end_time" id="shipper_end_time">
													
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<span class="sm btn btn-primary" id="shipper_reset"> Reset</span>
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
					 colors: [
						['white', 'black', 'gray', 'red', 'green', 'blue', 'yellow', 'purple', 'cyan'],
						['#c4b540', '#1dd381', '#ba1cd2', '#ff5733', '#33ff57', '#3344ff', '#ffff33', '#cc33ff', '#33ffff']
					],
					toolbar: [
						['style', ['bold', 'italic', 'underline', 'clear']],
						['font', ['strikethrough', 'superscript', 'subscript']],
						['fontsize', ['fontsize']],
						['color', ['forecolor', 'backcolor']],
						['para', ['ul', 'ol', 'paragraph']],
					],
					styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'], // This is to include headings in the style dropdown
					fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Merriweather'], // Add the desired font names
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
	
		var ad_current_start_time = @json($admin_ticker['start_time']);
		var ad_current_start_time_formatted =  @json($admin_ticker['start_time_formatted']);
		var ad_current_end_time = @json($admin_ticker['end_time']);
		var ad_current_end_time_formatted =  @json($admin_ticker['end_time_formatted']);
	
		
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
			
				if(selected_date.toDateString() === current_date.toDateString())
				{
					var currentHour = current_date.getHours();

					if (current_date.getMinutes() > 0) {
						currentHour++;
					}
				
					populateTimeOptions(currentHour, 24,admin_start_time,ad_current_start_time);
					if(ad_current_start_time!=null)
					{
						$("#admin_start_time").append('<option value='+ad_current_start_time+' selected>'+ad_current_start_time_formatted+'</option>');
						ad_current_start_time=null;	
					}else{
						initializ_admin_start_time();
					}
				}
				else
				{
					populateTimeOptions(0, 24,admin_start_time);
					if(ad_current_start_time!=null)
					{
						$("#admin_start_time").val(ad_current_start_time).trigger('change');
						ad_current_start_time=null;
					}else
					{
						initializ_admin_start_time();
					}
					
					
				}

				 $("#admin_start_time").attr('data-rule-required',true).attr('data-msg-required','Start Time is Required');
	

			}
			
			
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
				
					if(selected_date.toDateString() === current_date.toDateString())
					{
						var currentHour = current_date.getHours();

						if (current_date.getMinutes() > 0) {
							currentHour++;
						}

						populateTimeOptions(currentHour, 24,admin_end_time,ad_current_end_time);
						if(ad_current_end_time!=null)
						{
							$("#admin_end_time").append('<option value='+ad_current_end_time+' selected>'+ad_current_end_time_formatted+'</option>');
							ad_current_end_time=null;
						}else{
							initializ_admin_end_time();
						}

					}
					else
					{
						populateTimeOptions(0, 24,admin_end_time);
						if(ad_current_end_time!=null)
						{
							$("#admin_end_time").val(ad_current_end_time).trigger('change');
							ad_current_end_time=null;
						}else{
							initializ_admin_end_time();
						}
					}

				

				$("#admin_end_time").attr('data-rule-required',true).attr('data-msg-required','End Time is Required');
			}
		
			
		});
		

		var sh_current_start_time = @json($shipper_ticker['start_time']);
		var sh_current_start_time_formatted =  @json($shipper_ticker['start_time_formatted']);
		var sh_current_end_time = @json($shipper_ticker['end_time']);
		var sh_current_end_time_formatted =  @json($shipper_ticker['end_time_formatted']);	

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
				
					if(selected_date.toDateString() === current_date.toDateString())
					{
						var currentHour = current_date.getHours();

						if (current_date.getMinutes() > 0) {
							currentHour++;
						}

						populateTimeOptions(currentHour, 24,shipper_start_time,sh_current_start_time);
						if(sh_current_start_time!=null)
						{
							$("#shipper_start_time").append('<option value='+sh_current_start_time+' selected>'+sh_current_start_time_formatted+'</option>');
							sh_current_start_time=null;

						}else{
							initializ_shipper_start_time();
						}
					}
					else
					{
						populateTimeOptions(0, 24,shipper_start_time);
						if(sh_current_start_time!=null)
						{
							$("#shipper_start_time").val(sh_current_start_time).trigger('change');
							sh_current_start_time=null;
						}else{
							initializ_shipper_start_time();

						}

					}

				
					$("#shipper_start_time").attr('data-rule-required',true).attr('data-msg-required','Start Time is Required');
			}
	
			
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
	
				if(selected_date.toDateString() === current_date.toDateString())
				{
					var currentHour = current_date.getHours();

					if (current_date.getMinutes() > 0) {
						currentHour++;
					}

					populateTimeOptions(currentHour, 24,shipper_end_time,sh_current_end_time);
					if(sh_current_end_time!=null)
					{
						$("#shipper_end_time").append('<option value='+sh_current_end_time+' selected>'+sh_current_end_time_formatted+'</option>');
						sh_current_end_time=null;
					}else{
						initializ_shipper_end_time();
					}

				}
				else
				{
					populateTimeOptions(0, 24,shipper_end_time);
					if(sh_current_end_time!=null)
					{
						$("#shipper_end_time").val(sh_current_end_time).trigger('change');
						sh_current_end_time=null;
					}else{
						initializ_shipper_end_time();
					}

				}
				$("#shipper_end_time").attr('data-rule-required',true).attr('data-msg-required','End Time is Required');
			}
			
			
		});

		
		initializ_admin_start_time();
		initializ_admin_end_time();
		initializ_shipper_start_time();
		initializ_shipper_end_time();


		$("#admin_reset").on('click',function()
		{
			$("#admin_start_date").val('').trigger('change');
			$("#admin_end_date").val('').trigger('change');
			$("#admin_start_time").empty();
			$("#admin_end_time").empty();
			$("#admin_start_time").removeAttr('data-rule-required data-msg-required');
			$("#admin_end_time").removeAttr('data-rule-required data-msg-required');
			initializ_admin_start_time();
			initializ_admin_end_time();

		});

		$("#shipper_reset").on('click',function()
		{
			$("#shipper_start_date").val('').trigger('change');
			$("#shipper_end_date").val('').trigger('change');
			$("#shipper_start_time").empty();
			$("#shipper_end_time").empty();
			$("#shipper_start_time").removeAttr('data-rule-required data-msg-required');
			$("#shipper_end_time").removeAttr('data-rule-required data-msg-required');
			initializ_shipper_start_time();
			initializ_shipper_end_time();
		});

	
		
	
		function populateTimeOptions(startHour, endHour,select,current_time=null) {
			select.empty();
			for (var hour = startHour; hour < endHour; hour++) {
				var formattedHour = (hour % 12 === 0) ? 12 : hour % 12;
				var amPm = (hour < 12) ? 'AM' : 'PM';
				var optionValue = ('0' + hour).slice(-2) + ':00';
				var optionText = formattedHour + ':00 ' + amPm;
				if(optionValue!=current_time)
				{
					var option = new Option(optionText, optionValue);
					select.append(option);
				}	
			}	
			
   		}
		function initializ_shipper_start_time(){
			var shipper_start_time= $('#shipper_start_time').prepend('<option value="" selected="selected">Start Time</option>').select2({
					width:'100%'
			});
		}

		function initializ_shipper_end_time()
		{
			var shipper_end_time= $('#shipper_end_time').prepend('<option value="" selected="selected">End Time</option>').select2({
				width:'100%'
			});
		
		}
		function initializ_admin_start_time()
		{
			var admin_start_time= $('#admin_start_time').prepend('<option value="" selected="selected">Start Time</option>').select2({
            	width:'100%'
       	 	});

		}
		function initializ_admin_end_time()
		{
			var admin_end_time= $('#admin_end_time').prepend('<option value="" selected="selected">End Time</option>').select2({
				width:'100%'
			});
		}
		
	</script>
@endsection