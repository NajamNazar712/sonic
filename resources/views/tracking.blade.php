@section('title', 'Tracking')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        .selectize-control {
            width: 300px !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			var select = $('#track_form .tracking_numbers').selectize({
				placeholder: 'Tracking Number(s)*',
				delimiter: ',',
				createOnBlur: true,
				persist: false,
				plugins: ['remove_button'],
				onDropdownOpen: function(dropdown) {
					dropdown.remove();
				},
				onType: function(str) {
					var regex = /^[0-9,]+$/;

					if (!regex.test(str)) {
						select[0].selectize.setTextboxValue('');
					}
				},
				create: function(input) {
					if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
						return {
							value: input,
							text: input
						}
					}
					else {
						return false;
					}
				}
			});
            $('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Case Nature",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id === 1){
                    $('#request_service').addClass('d-none');
                    $('#request_complaints').removeClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }else if(id === 2){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').removeClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }
                else if(id === 3){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').removeClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }
                else if(id === 4){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#request_claims').removeClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                }else{
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#AddNewRequest').addClass('d-none');
                    $('#request_claims').addClass('d-none');
                }
            });

			@if (app('request')->has('tracking_number'))
                track({{ app('request')->input('tracking_number') }});
			@endif

			function track(tracking_numbers){
                $.ajax({
                    url: '{!! route('tracking.track') !!}',
                    method: 'POST',
                    data: {
                        'tracking_numbers': tracking_numbers,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        select[0].selectize.clear();

                        $('#tracking').html('');

                        if (data.invalid !== undefined) {
                            var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');

                            toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }

                        if (data.shipments != undefined) {
                            $.each(data.shipments, function(id, details) {
                                var shipment = '';

                                shipment += '<div class="mt-4 border-primary">';
                                shipment += '<div class="d-flex align-items-center bg-primary">';
                                shipment += '<div class="m-1 font-medium-3 white">' + details.tracking_number + '</div>';
                                shipment += '<button class="btn btn-secondary ml-auto mr-0 mr-sm-1 add_request" id=' + id + ' data-tracking=' + details.tracking_number + '>Add Request</button>';
                                shipment += '</div>';

                                shipment += '<div class="p-1">';
                                shipment += '<div class="row justify-content-between">';

                                shipment += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-5">';
                                shipment += '<h4><u>Shipper/Pickup Information</u></h4>';
                                shipment += '<div class="border table-responsive">';
                                shipment += '<table class="table table-sm table-borderless mb-0">';
                                shipment += '<tbody>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Shipper</strong></td>';
                                shipment += '<td>' + details.shipper.name + '</td>';
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Origin</strong></td>';
                                shipment += '<td>' + details.pickup.origin + '</td>';
                                shipment += '</tr>';
                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';

                                shipment += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-5 mt-2 mt-xs-2 mt-sm-2 mt-md-2 mt-lg-0">';
                                shipment += '<h4><u>Consignee Information</u></h4>';
                                shipment += '<div class="border table-responsive">';
                                shipment += '<table class="table table-sm table-borderless mb-0">';
                                shipment += '<tbody>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Consignee</strong></td>';
                                shipment += '<td>' + details.consignee.name + '</td>';
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Destination</strong></td>';
                                shipment += '<td>' + details.consignee.destination + '</td>';
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';

                                shipment += '<div class="col-12 mt-2">';
                                shipment += '<h4><u>Tracking History</u></h4>';
                                shipment += '<div class="border table-responsive">';

                                shipment += '<table class="table table-sm table-borderless datatable tracking_history">';
                                shipment += '<thead>';
                                shipment += '<tr role="row">';
                                shipment += '<th><strong>Date / Time</strong></th>';
                                shipment += '<th><strong>Status</strong></th>';
                                shipment += '</tr>';
                                shipment += '</thead>';
                                shipment += '<tbody>';

                                $.each(details.tracking_history, function(index, history) {
                                    shipment += '<tr>';
                                    shipment += '<td>' + history.date_time + '</td>';
                                    shipment += '<td>' + history.status + '</td>';
                                    shipment += '</tr>';
                                });

                                shipment += '</tbody>';
                                shipment += '</table>';

                                shipment += '</div>';
                                shipment += '</div>';

                                shipment += '</div>';
                                shipment += '</div>';


                                shipment += '</div>';

                                $('#tracking').append(shipment);
                            });

                            $('#tracking table.datatable.tracking_history').DataTable({
                                dom: 't',
                                paging: false,
                                order: [[0, 'desc']],
                                columns: [
                                    {name: 'date_time', class: 'align-middle date_time'},
                                    {name: 'status', class: 'align-middle status'}
                                ]
                            });
                        }
                    });
			}
            $('.close').on('click',function(){

                $('#request_complaints').addClass('d-none');
                $('#request_service').addClass('d-none');
                $('#request_feedback').addClass('d-none');
                $('#request_claims').addClass('d-none');
                $('#AddNewRequest').addClass('d-none');
                

            });
            $('#tracking').on('click', '.add_request', function () {
                id = $(this).attr('id');
                var tracking = $(this).attr('data-tracking');
                var tracking_rows = '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> '+ tracking +'</b></span></div>';
                $('#requested_shipment_id').val(id);
                $('#requested_shipments').html(tracking_rows);
                $('#AddRequestModal').modal('show');

            });

			$('#track_form').validate({
				ignore: [],
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
                    track($(form).find('.tracking_numbers').val());

					return false;
				}
			});

		});
	</script>
@endsection

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
  @include('client.layout.header')
</head>
<body class="vertical-layout vertical-overlay-menu 2-columns menu-expanded fixed-navbar"
data-open="click" data-menu="vertical-overlay-menu" data-col="2-columns">
  <nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-dark bg-primary navbar-shadow navbar-brand-center">
    <div class="navbar-wrapper">
      <div class="navbar-header" style="top: 0;">
        <ul class="nav navbar-nav flex-row">
          <li class="nav-item">
            <a class="navbar-brand" href="{{route('cod.dashboard')}}">
                <img class="brand-logo sonic" alt="Sonic" src="{{ asset('img/sonic_logo_white_new.png') }}">
                <img class="brand-logo trax" alt="Trax" src="{{ asset('img/trax_logo_white_new.png') }}">
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="app-content content">
    <div class="content-wrapper">
      <div class="content-body">

        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    <h1 class="mb-1">
                        Tracking
                    </h1>

                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body">
                                @include('client.inc.messages')

                                <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                    <div class="form-group">
                                        <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                                    </div>

                                    <div class="form-group ml-1">
                                        <button type="submit" name="track" class="btn btn-primary" value="Track">Track</button>
                                    </div>
                                </form>

                                <div class="tracking" id="tracking">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      </div>
    </div>
  </div>
  <div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRequestModal"
       aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header bg-primary white">
                  <h4 class="modal-title white">Add Request</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body text-center">
                  <form id="add_request_form" method="post">
                      @csrf
                      <div class="container">
                          <div class="row">
                              <h2 class="heading">Tracking Number</h2>
                          </div>

                          <input type="hidden" id="requested_shipment_id">
                          <div class="row old_scroll" id="requested_shipments">

                          </div>
                          <hr>
                          <div class="row justify-content-center">
                              <div class="col-8">
                                  <fieldset class="form-group">
                                      <select name="case_nature_select" id="case_nature_select" class="form-control select2">
                                          @foreach($case_nature as $nature)
                                              <option value="{{$nature->id}}">{{$nature->name}}</option>
                                          @endforeach
                                      </select>
                                  </fieldset>
                              </div>
                          </div>
                          <div class="complaints d-none" id="request_complaints">
                              <div class="row justify-content-center">
                                  <div class="col-6">
                                      <fieldset class="form-group">
                                          <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2">
                                              @foreach($case_nature_complaints as $complaints)
                                                  <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                              @endforeach
                                          </select>
                                      </fieldset>
                                  </div>
                                  <div class="col-6">
                                      <fieldset class="form-group">
                                          <select name="complaint_channel" id="complaint_channels" class="form-control select2">
                                              @foreach($case_nature_channels as $channel1)
                                                  <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                              @endforeach
                                          </select>
                                      </fieldset>
                                  </div>
                                  <div class="col-6">
                                      <fieldset class="form-group">
                                          <textarea class="form-control" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                      </fieldset>
                                  </div>
                              </div>
                          </div>
                          <div class="service d-none" id="request_service">
                              <div class="row justify-content-center">
                                  <div class="col-6">
                                      <fieldset class="form-group">
                                          <select name="case_nature_request" id="case_nature_requests" class="form-control select2">
                                              @foreach($case_nature_service_requests as $service)
                                                  <option value="{{$service->id}}">{{$service->type}}</option>
                                              @endforeach
                                          </select>
                                      </fieldset>
                                  </div>
                                  <div class="col-6">
                                      <fieldset class="form-group">
                                          <select name="request_channel" id="request_channels" class="form-control select2">
                                              @foreach($case_nature_channels as $channel2)
                                                  <option value="{{$channel2->id}}">{{$channel2->channel}}</option>
                                              @endforeach
                                          </select>
                                      </fieldset>
                                  </div>
                                  <div class="col-6">
                                      <fieldset class="form-group">
                                          <textarea class="form-control" name="service_description" id="service_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                      </fieldset>
                                  </div>
                              </div>
                          </div>
                          <div class="feedback d-none" id="request_feedback">
                              <div class="row justify-content-center">
                                  <div class="col-8">
                                      <fieldset class="form-group">
                                          <select name="feedback_channel_request" id="feedback_channel_request" class="form-control select2">
                                              @foreach($case_nature_channels as $channel1)
                                                  <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                              @endforeach
                                          </select>
                                      </fieldset>
                                  </div>
                                  <div class="col-8">
                                      <fieldset class="form-group">
                                          <textarea class="form-control" name="feedback_description_request" id="feedback_description_request" rows="5" placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                      </fieldset>
                                  </div>
                              </div>
                          </div>
                          <div class="claims d-none" id="request_claims">
                              <input type="hidden" name="shipment_ids" id="shipment_ids">
                              <input type="hidden" name="case_nature_id" id="case_nature_id">
                              <input type="hidden" name="complaint_id" id="complaint_id">
                              <input type="hidden" name="channel_id" id="channel_id">
                              <div class="row justify-content-center">
                                  <div class="col-8">
                                      <fieldset class="form-group">
                                          <select name="case_nature_claim" id="case_nature_claim" class="form-control select2">
                                              @foreach($case_nature_type_claims as $claim)
                                                  <option value="{{$claim->id}}">{{$claim->type}}</option>
                                              @endforeach
                                          </select>
                                      </fieldset>
                                  </div>
                                  <div class="col-8">
                                      <fieldset class="form-group">
                                          <select name="claim_channel" id="claim_channel" class="form-control select2">
                                              @foreach($case_nature_channels as $channel1)
                                                  <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                              @endforeach
                                          </select>
                                      </fieldset>
                                  </div>
                                  <div class="col-8" id="claim_product_cost_div">
                                      <fieldset class="form-group">
                                          <input class="form-control" name="claim_product_cost" id="claim_product_cost" value="" placeholder="Enter Product Cost">
                                      </fieldset>
                                  </div>
                                  <div class="col-8 d-none" id="receiving_sheet_div">
                                      <fieldset class="form-group">
                                          <select name="receiving_sheet_id"  id="request_id" class="form-control select2" data-rule-required="true" data-msg-required="Please Select Receiving Sheet">
                                          <select name="receiving_sheet_id"  id="request_id" class="form-control select2"></select>
                                      </fieldset>
                                  </div>
                                  <div class="col-8 text-left" id="claim_product_picture_div">
                                      <fieldset class="form-group">
                                          <label for="product_picture"><b>Product Picture:</b></label>
                                          <input class="form-control form-control-sm" type="file" name="product_picture" id="product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                      </fieldset>
                                  </div>
                                  <div class="col-8 text-left" id="claim_invoice_picture_div">
                                      <fieldset class="form-group">
                                          <label for="invoice_picture"><b>Invoice Picture:</b></label>
                                          <input class="form-control form-control-sm" type="file" name="invoice_picture" id="invoice_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                      </fieldset>
                                  </div>

                                                                      <div class="col-8 text-left d-none" id="claim_shipment_damage_div">
                                                                          <fieldset class="form-group">
                                                                              <label for="damage_product_picture"><b>Damage Picture:</b></label>
                                                                              <input class="form-control form-control-sm" type="file" name="damage_product_picture" id="damage_product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                                                          </fieldset>
                                                                          <fieldset class="form-group">
                                                                              <label for="product_packaging_picture"><b>Product Packaging Picture:</b></label>
                                                                              <input class="form-control form-control-sm" type="file" name="product_packaging_picture" id="product_packaging_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                                                          </fieldset>
                                                                          <fieldset class="form-group">
                                                                              <label for="actual_product_picture"><b>Actual Product Picture:</b></label>
                                                                              <input class="form-control form-control-sm" type="file" name="actual_product_picture" id="actual_product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                                                          </fieldset>
                                                                          <fieldset class="form-group">
                                                                              <input class="form-control" name="damage_claim_product_cost" id="damage_claim_product_cost" value="" placeholder="Enter Actual Damaged Product Cost">
                                                                          </fieldset>

                                                                      </div>

                                                                      <div class="col-8 text-left d-none" id="claim_content_short_div">
                                                                          <fieldset class="form-group">
                                                                              <label for="missing_product_picture"><b>Missing Product Picture:</b></label>
                                                                              <input class="form-control form-control-sm" type="file" name="missing_product_picture" id="missing_product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                                                          </fieldset>
                                                                          <fieldset class="form-group">
                                                                              <label for="product_packaging_picture_content_short"><b>Product Packaging Picture:</b></label>
                                                                              <input class="form-control form-control-sm" type="file" name="product_packaging_picture_content_short" id="product_packaging_picture_content_short" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                                                          </fieldset>
                                                                          <fieldset class="form-group">
                                                                              <label for="actual_product_picture_content_short"><b>Actual Product Picture:</b></label>
                                                                              <input class="form-control form-control-sm" type="file" name="actual_product_picture_content_short" id="actual_product_picture_content_short" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                                                          </fieldset>
                                                                          <fieldset class="form-group">
                                                                              <input class="form-control" name="claim_content_product_cost" id="claim_content_product_cost" value="" placeholder="Enter Actual Missing Product Cost">
                                                                          </fieldset>
                                                                      </div>

                                  <div class="col-8">
                                      <fieldset class="form-group">
                                          <textarea class="form-control" name="description" id="claim_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                      </fieldset>
                                  </div>
                              </div>
                          </div>
                          <div class="row justify-content-center">
                              <div class="col-3">
                                  <button id="AddNewRequest" type="submit" class="btn btn-primary btn-block d-none">Submit</button>
                              </div>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
  @include('client.layout.footer')
</body>
</html>

