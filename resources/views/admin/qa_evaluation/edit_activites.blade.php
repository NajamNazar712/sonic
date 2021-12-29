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
									
                                    <div class="col-6">
										<div class="form-group">
                                            <select name="campaign_id" class="select2" id="campaign_id" data-rule-required="true" data-msg-required="Evaluation Campaign is required">
													<option value="1">Incoming/RCP</option>
													<option value="2">Complains/Claim</option>
													<option value="3">Email Live Chat</option>
											</select>
											
										</div>
									</div>
                                    <div class="col-6 text-right">
                                        <h2 class="text-success font-weight-bold" id="score_heading">Total Weightage: <span id="score_text">100</span></h2>
                                </div>
                                    <input type="hidden" id="activities_id" name="activities_id">
                                    <input type="hidden" id="activities_weightage" name="activities_weightage">
                                    <input type="hidden" id="activities_name" name="activities_name">
                                    <input type="hidden" id="score" name="score">
									
								</div>
                                <div id="activities_data">
                                    
                                </div>
                                <div class="col-12">
                                    <div class="form-group text-center">
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
        .remove{
            margin: 0 auto;
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

            $('#submit_button').attr('disabled', true);
            $('#score_heading').css("display", "none");
            $('a.add_activity').addClass('disabled');
            
            $('#campaign_id').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Select Campaign*'
			}).bind('change',function(){
                var campaign_id = $(this).val();
              
                $.ajax({
                        url: '{!! route('admin.qa_evaluation.actvities_data') !!}',
                        type: 'POST',
                        data: {
                            'id': campaign_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                            
                        console.log(data);
                html='';
                        var score = 0;
                    $.each(data.handlings, function (index, value) {
                        console.log(value);
                        html+='<div class="row"><div class="col-12"><h2>'+value.handling+'</h2></div>';
                        $.each(data.activities, function (i, v) {
                        console.log(v);
                        
                  
                            if(v.evaluation_handling_id == value.id){
                                score += v.weightage ;
                                html+='<div class="row mt-1" style="width:100%">';
                                html+='<div class="col-5 ml-3"><div class="form-group input-group"><input type="text" id="activity_'+v.id+'" class="form-control activity_name" name="activity_name['+v.evaluation_handling_id+']['+v.id+']" value="'+v.activity+'"  data-rule-required="true" data-msg-required="Activity is required"></div></div>';
                                html+='<div class="col-4"><div class="form-group input-group"><input type="number" id="weightage_'+v.id+'" min="1" step="1" class="form-control activity_weightage" name="activity_weightage['+v.evaluation_handling_id+']['+v.id+']" value="'+v.weightage+'" data-value="'+v.weightage+'"  data-rule-required="true" data-msg-required="Weeightage is required"></div></div>';
                                html+='<div class="col-2"><div class="form-group input-group"><a href="javascript:void(0);" class="btn btn-sm btn-danger remove"><i class="ft-minus-circle"></i></a></div></div>';
                                html+='</div>';
                                
                            }

                        });
                        
                        html+='<div class="col-12 text-center"><a href="javascript:void(0);" id="'+value.id+'" class="btn btn-primary mb-1 add_activity">Add</a></div></div>';

                    });
                    console.log(score);
                    $('#score').val(score);
                    $('#activities_data').html(html);
                    $('#submit_button').attr('disabled', false);
                    $('a.add_activity').removeClass('disabled')
                    $('#score_heading').css("display", "block");
                    
                });

            });

            

            $('#activities_data').on('click', 'a.remove', function(){
                var parent = $(this).parents()[3];
                if(parent.childElementCount != 3){
                    $(this).parents()[2].remove();
                    
                }
                var ss = 0;
                $(".activity_weightage").each(function(){
                ss += parseInt($(this).val());
                });
                if(ss != 100){
                // $(this).val($(this).data("value"))
                $('#score_heading').removeClass("text-success");
                $('#score_heading').addClass("text-danger");

                $('#score_text').html(ss);
                $('#submit_button').attr('disabled', true);

                }else{
                $('#submit_button').attr('disabled', false);
                $('#score_text').html(ss);

                $('#score_heading').addClass("text-success");
                $('#score_heading').removeClass("text-danger");
                }
                
            });

            $('#activities_data').on('click', 'a.add_activity', function(){

                var handling_id = $(this).attr('id');
                console.log($(this).parents());
                var parent = $(this).parents()[1];
                console.log(parent.childElementCount)
                html1='<div class="row mt-1" style="width:100%">';
                                html1+='<div class="col-5 ml-3"><div class="form-group input-group"><input type="text" id="activity_0" class="form-control activity_name" name="activity_name['+handling_id+'][0]" value=""  data-rule-required="true" data-msg-required="Activity is required"></div></div>';
                                html1+='<div class="col-4"><div class="form-group input-group"><input type="number" id="weightage_0" class="form-control activity_weightage"  min="1" step="1" name="activity_weightage['+handling_id+'][0]" value=""  data-value="" data-rule-required="true" data-msg-required="Weeightage is required"></div></div>';
                                html1+='<div class="col-2"><div class="form-group input-group"><a href="javascript:void(0);" class="btn btn-sm btn-danger remove"><i class="ft-minus-circle"></i></a></div></div>';
                                html1+='</div>';
                                console.log(html1);
                                $(html1).insertBefore($(this).parents()[0]);
            });

            $('#activities_data').on('change', '.activity_weightage', function(){
                var ss = 0;
                $(".activity_weightage").each(function(){

                    ss += parseInt($(this).val());

                });
                if(ss != 100){
                    // $(this).val($(this).data("value"))
                    $('#score_heading').removeClass("text-success");
                    $('#score_heading').addClass("text-danger");

                    $('#score_text').html(ss);
                    $('#submit_button').attr('disabled', true);

                }else{
                    $('#submit_button').attr('disabled', false);
                    $('#score_text').html(ss);

                    $('#score_heading').addClass("text-success");
                    $('#score_heading').removeClass("text-danger");
                }
                console.log($(this).data("value"));
                console.log($(this).val());
                console.log(ss);
            });
          
            
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