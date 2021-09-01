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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
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
            $('#complaint_phone').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            $( "#add_request_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {

                        var nature_flag = true;
                        var case_nature_complaint_id = $('#case_nature_complaints').val();
                        var name = $('#complaint_name').val();
                        var phone = $('#complaint_phone').val();
                        var complaint_description = $('#complaint_description').val();
                        if (!case_nature_complaint_id) {
                            nature_flag = false;
                            var error = "Please select Complaint type!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                    if (!complaint_description) {
                        nature_flag = false;
                        var error = "Please select Description!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (!name) {
                        nature_flag = false;
                        var error = "Please Enter Your Name!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (!phone) {
                        nature_flag = false;
                        var error = "Please Enter Your Phone Number!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                        if (nature_flag) {
                            $('#AddNewRequest').attr('disabled', true);
                            swal({
                                title: 'Please Wait!',
                                text: 'Launching Request.',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            $.ajax({
                                url: '{!! route('tracking.add_request') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'shipment_id': $('#requested_shipment_id').val(),
                                    'case_nature_id': case_nature_id,
                                    'complaint_id': case_nature_complaint_id,
                                    'channel_id': case_nature_channel_id,
                                    'description': complaint_description
                                }
                            })
                                .done(function (data) {
                                    swal.close();
                                    if (data.status) {

                                        if (data.flag) {
                                            var html = '';

                                            $.each(data.already_existed_shipments, function (index, tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            if (!data.cannot_change) {
                                                html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                            }
                                            else {
                                                html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                            }

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Cannot Be Lodged!',
                                                content: content,
                                                icon: 'warning',
                                                buttons: {
                                                    cancel: {
                                                        text: 'Close',
                                                        value: null,
                                                        visible: true,
                                                        closeModal: true,
                                                    },
                                                },
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true
                                            });
                                        } else {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                        // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    } else {
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    }

                                    $('#AddRequestModal').modal('hide');
                                    $('#AddNewRequest').attr('disabled', false);
                                });
                        }


                }
            });
            $('.close').on('click',function (){
                $('#complaint_description').val('');
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
                  <form id="add_request_form" method="post" action="{{Route('tracking.add_request')}}">
                      @csrf
                      <div class="container">
                          <div class="row">
                              <h2 class="heading">Tracking Number</h2>
                          </div>

                          <input type="hidden" id="requested_shipment_id" name="shipment_id">
                          <div class="row old_scroll" id="requested_shipments">

                          </div>
                          <hr>
                          <div class="row justify-content-center">
                              <div class="col-8">
                                  <fieldset class="form-group">
                                      <h1 name="case_nature_select" class="form-control">Complaints</h1>

                                  </fieldset>
                              </div>
                          </div>
                          <div class="complaints" id="request_complaints">
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
                                          <input type="text" id="complaint_name" name="complaint_name" placeholder="Enter Your name" class="form-control">
                                      </fieldset>
                                  </div>
                                  <div class="col-6">
                                      <fieldset class="form-group">
                                          <input type="text" id="complaint_phone" name="complaint_phone" placeholder="Enter Your phone no." class="form-control">
                                      </fieldset>
                                  </div>
                                  <div class="col-6">
                                      <fieldset class="form-group">
                                          <textarea class="form-control" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                      </fieldset>
                                  </div>
                              </div>
                          </div>
                          <div class="row justify-content-center">
                              <div class="col-3">
                                  <button id="AddNewRequest" type="submit" class="btn btn-primary btn-block">Submit</button>
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

