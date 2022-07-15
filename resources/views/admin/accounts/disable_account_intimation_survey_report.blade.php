@extends('admin.layout.master')

@section('title', 'Disable Account Intimation Survey Report')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Disable Account Intimation Survey Report
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')
							<table class="table table-bordered datatable" id="datatable" style="z-index: 3; width:100%;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Random ID</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Phone</th>
										<th class="border-primary border-darken-1">Email</th>
										<th class="border-primary border-darken-1">Send Via</th>
										<th class="border-primary border-darken-1">Send By</th>
										<th class="border-primary border-darken-1">Response Status</th>
										<th class="border-primary border-darken-1">Response Data</th>
										<th class="border-primary border-darken-1">Sended URL</th>
										<th class="border-primary border-darken-1">Created at</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    <div class="modal fade" id="show_answers_modal" role="dialog" aria-labelledby="show_answers_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="edit_title">Response Data</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover submitResponse" id="submitResponse">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Questions</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
               
            </div>
        </div>
    </div>

@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
	<script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

	<script type="text/javascript">
		$(document).ready(function() {

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
					var params = table.ajax.params();
					params.start = 0;
					params.length = -1;
					params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.accounts.disable.account.intimation.survey.report.list') }}',
						data: params,
                        success: function (result) {
                            head = [];
							
                            head.push('Random ID');
                            head.push('Shipper');
                            head.push('Phone');
                            head.push('Email');
                            head.push('Send Via');
                            head.push('Send By');
                            head.push('Response Status');
                            head.push('Sended URL');
                            head.push('Created at');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(values.random_id);
                                row.push(values.shipper_name);
                                row.push(values.phone);
                                row.push(values.email);
                                row.push(values.send_via);
                                row.push(values.send_by);
                                row.push(values.status);
                                row.push(values.url_excel);
                                row.push(values.created_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
			});

			var selected_rows = [];
			var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    extend: 'excel',
                    title: 'Disable Account Intimation Survey Report',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
				
	            scrollX: true, scrollY: '500px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
				},
				select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
				serverSide: true,
				ajax: {
					url: '{{ route('admin.accounts.disable.account.intimation.survey.report.list') }}',
					data: function (d) {
				}
				},
                rowId: 'random_id',
				order: [[10, 'Desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle text-center serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'random_id', name: 'disable_account_intimation_send_surveys.random_id', class: 'align-middle random_id'},
					{data: 'shipper_name', name: 'users.name', class: 'align-middle shipper_name'},
					{data: 'phone', name: 'users.phone', class: 'align-middle phone'},
					{data: 'email', name: 'users.email', class: 'align-middle email'},
					{data: 'send_via', name: 'disable_account_intimation_send_surveys.send_via', class: 'align-middle send_via'},
					{data: 'send_by', name: 'send_by.name', class: 'align-middle send_by'},
					{data: 'status', name: 'disable_account_intimation_send_surveys.status', class: 'align-middle status'},
					{data: 'answers', name: 'answers', class: 'align-middle text-center answers',sortable:false,orderable:false},
					{data: 'url', name: 'disable_account_intimation_send_surveys.url', class: 'align-middle url'},
					{data: 'created_at', name: 'disable_account_intimation_send_surveys.created_at', class: 'align-middle created_at'}
				],
				rowCallback: function(row, data, index) {

					var info = table.page.info();
					$('td:eq(0)', row).html(index + 1 + info.page * info.length);
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Not Collected</option>' +
                        '<option value="1">Collected</option>' +
                        '</select>';
                    var send_via = '<select name="send_via" id="send_via" class="select2 form-control">' +
                        '<option value="sms">SMS</option>' +
                        '<option value="email">Email</option>' +
                        '</select>';
					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.url') || $(header).is('.created_at') || $(header).is('.answers')) {
							$(td).appendTo($(search));
						}else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.send_via')){
                            $(send_via).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
						else {
							var current = $(input).appendTo($(search)).on('change', function() {
								column.search($(this).val(), false, false, true).draw();
							}).wrap(td).after(icon);

							if (column.search()) {
								current.val(column.search());
							}
						}
					});
					
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#send_via").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Send Via",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
					this.api().table().columns.adjust();
				}
			});


			$('#datatable tbody').on('click', 'tr td.answers button.show_answers', function() {
			
                var survey_id = $(this).parents('tr').attr('id');

                if(survey_id){
                    $.ajax({
                        url: '{!! route('admin.accounts.disable.account.intimation.survey.report.submitresponse') !!}',
                        data: {
                            'survey_id': survey_id,
                        }
                    })
                        .done(function(data) {
                            if(data.status == 1){
                                $('#show_answers_modal').modal('show');
                                var html = "";
                                $.each(data.submit_survey_data, function(index, values) {

                                    var opt1 = "", opt2 = "", opt3 = "", opt4 = "", noa = "";
                                    var style = 'style="font-weight:bold; background-color:yellow;"';

                                    if(values.option1 == values.selected_option)
                                    {
                                        opt1 = style;
                                    }
                                    else if(values.option2 == values.selected_option)
                                    {
                                        opt2 = style;
                                    }
                                    else if(values.option3 == values.selected_option)
                                    {
                                        opt3 = style;
                                    }
                                    else if(values.option4 == values.selected_option)
                                    {
                                        opt4 = style;
                                    }
                                    else{
                                        noa = style;
                                    }

                                    html+= `

                                            <tr>
                                                <td>${values.id}</td>
                                                <td>${values.questions}</td>
                                                <td>
                                                   <div class="options"> <span ${opt1}> ${values.option1} </span> </div>
                                                   <div class="options"> <span ${opt2}> ${values.option2} </span> </div>
                                                   <div class="options"> <span ${opt3}> ${values.option3} </span> </div>
                                                   <div class="options"> <span ${opt4}> ${values.option4} </span> </div>
                                                   <div class="options"> <span ${noa}> None of the Above </span> </div>
                                                </td>
                                            </tr>

                                `;
                                });
                                $('#submitResponse tbody').html(html);
                            }

                        });
                }

			});

		});
	</script>
@endsection