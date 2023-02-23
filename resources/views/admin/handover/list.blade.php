@extends('admin.layout.master')

@section('title', 'Handover List')

@section('content')
    <h1 class="mb-1">
        Handover List
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Search By Tracking Number" id="search_tracking">
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_from_admin" id="search_from_admin" class="form-control select2">
                                @foreach($handover_admins as $admin)
                                    <option value="{{$admin->id}}">{{$admin->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_to_admin" id="search_to_admin" class="form-control select2">
                                @foreach($handover_admins as $admin)
                                    <option value="{{$admin->id}}">{{$admin->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="width:100%;z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Created At</th>
                        <th class="border-primary border-darken-1">Created By</th>
                        <th class="border-primary border-darken-1">From</th>
                        <th class="border-primary border-darken-1">From Person Dept/Area/DES</th>
                        <th class="border-primary border-darken-1">To</th>
                        <th class="border-primary border-darken-1">To Person Dept/Area/DES</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Shipment(s)</th>
                        <th class="border-primary border-darken-1">Received Shipment(s)</th>
                        <th class="border-primary border-darken-1">Remaining Shipment(s)</th>
                        <th class="border-primary border-darken-1">Received By</th>
                        <th class="border-primary border-darken-1">Received At</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shipments" role="dialog" aria-labelledby="shipments_title" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_title">Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
        }
        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }
        

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_from_admin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select From Person',
                width:'100%',
                allowClear:true
            });
            $('#search_to_admin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select To Person',
                width:'100%',
                allowClear:true
            });
            function print(ids) {
				$.ajax({
					url: '{!! route('admin.handover.list.print') !!}',
					method: 'POST',
					data: {
						'ids': ids,
						'_token': '{{ csrf_token() }}'
					}
				})
				.done(function(data) {
					var tab = window.open('', '_blank');

					if(!tab) {
						swal({
							title: 'Popup Blocker Enabled!',
							text: 'Please add this site to your exception list.',
							icon: 'error',
							closeOnClickOutside: false,
							closeOnEsc: false
						});
					}
					else {
						tab.document.write(data);
						tab.document.close();
						tab.focus();
					}

					table.draw('false');
				});
			}
            $('#search_tracking').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
       
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.handover.list.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Created At');
                            head.push('Created By');
                            head.push('From');
                            head.push('From Person Dept/Area/DES');
                            head.push('To');
                            head.push('To Person Dept/Area/DES');
                            head.push('Hub');
                            head.push('Status');
                            head.push('Shipment(s)');
                            head.push('Received Shipment(s)');
                            head.push('Remaining Shipment(s)');
                            head.push('Received By');
                            head.push('Received At');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.created_at);
                                row.push(values.created_by);
                                row.push(values.from);
                                row.push(values.from_dept_area_desg);
                                row.push(values.to);
                                row.push(values.to_dept_area_desg);
                                row.push(values.hub);
                                row.push(values.status);
                                row.push(values.total_shipments);
                                row.push(values.received_shipments);
                                row.push(values.remaining);
                                row.push(values.received_by);
                                row.push(values.received_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );
            
            var selected_rows = [];
           
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                        buttons: [{
						text: '<i class="la la-print"></i> Print',
						className: 'btn btn-primary print',
						enabled: false,
						action: function (e, dt, node, config) {
							swal({
								text: 'Are you sure, you want to Dispatch these Handover Notes?',
								icon: 'warning',
								buttons: {
									cancel: {
										text: 'No',
										value: null,
										visible: true,
										closeModal: true,
									},
									confirm: {
										text: 'Yes',
										value: true,
										visible: true,
										closeModal: true
									}
								},
								closeOnClickOutside: false,
								closeOnEsc: false,
								dangerMode: true
							}).then(function(confirm) {
								if (confirm) {
									print(selected_rows);

									table.rows().deselect();

									selected_rows = [];

									table.button('.print').disable();

									table.draw('false');
								}
							});
						}
					},
                    {
                  extend: 'selectAll',
                  text: 'Select All',
                  className: 'select_all',
                  action : function(e) {
                    e.preventDefault();

                    table.rows().nodes().each(function(index) {
                      var row = table.row(index);

                      if ($(row.node().firstChild).hasClass('select-checkbox')) {
                        row.select();

                        id = parseInt(row.id());

                        var index = $.inArray(id, selected_rows);

                        if (index === -1) {
                          selected_rows.push(id);
                        }

                        table.button('.delivered').enable();
                        table.button('.print').enable();
                      }
                    });
                  }
                }, {
                  extend: 'selectNone',
                  text: 'Select None',
                  className: 'select_none',
                  action : function(e) {
                    e.preventDefault();

                    table.rows().nodes().each(function(index) {
                      var row = table.row(index);

                      if ($(row.node().firstChild).hasClass('select-checkbox')) {
                        row.deselect();

                        id = parseInt(row.id());

                        var index = $.inArray(id, selected_rows);

                        if (index !== -1) {
                            selected_rows.splice(index, 1);
                        }

                        if (selected_rows.length == 0) {
                            table.button('.delivered').disable();
                            table.button('.print').disable();
                        }
                      }
                    });
                  }
                },  
                {
                    extend: 'excelHtml5',
                    title: 'Handover List',
                    text:'<i class="la la-file-excel-o"></i> Excel',
                    },

                    'reset'

                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.handover.list.list') }}',
                    data: function (d) {
                        d.search_tracking = $('#search_tracking').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_from_admin = $('#search_from_admin').val();
                        d.search_to_admin = $('#search_to_admin').val();
                    }
                },
                rowId: 'handover_id',
                order: [[2, 'desc']],
                columns: [
                    {data: 'handover_id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'created_at', name: 'handovers.created_at', class: 'align-middle created_at'},
                    {data: 'created_by', name: 'a.name', class: 'align-middle created_by'},
                    {data: 'from', name: 'hr.name', class: 'align-middle from'},
                    {data: 'from_dept_area_desg', name: 'handovers.from_dept_area_desg', class: 'align-middle from_dept_area_desg'},
                    {data: 'to', name: 'hor.name', class: 'align-middle to'},
                    {data: 'to_dept_area_desg', name: 'handovers.to_dept_area_desg', class: 'align-middle to_dept_area_desg'},
                    {data: 'hub', name: 'c.name', class: 'align-middle text-center hub'},
                    {data: 'status', name: 'hs.name', class: 'align-middle status'},
                    {data: 'shipment_count', name: 'handovers.shipments', class: 'align-middle text-center shipment_count'},
                    {data: 'received_shipments', name: 'handovers.received', class: 'align-middle received_shipments'},
                    {data: 'remaining_shipment_count', name: 'remaining_shipment_count', class: 'align-middle text-center remaining_shipment_count'},
                    {data: 'received_by', name: 'a.name', class: 'align-middle received_by'},
                    {data: 'received_at', name: 'handovers.received_at', class: 'align-middle received_at'},
                ],
                rowCallback: function(row, data, index) {
                    // var info = table.page.info();
                    // $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.handover_id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
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
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            // $('#search_tracking').on('change',function () {
            //     table.draw();
            //
            // });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
				var id = parseInt($(this).parent('tr').attr('id'));

				var index = $.inArray(id, selected_rows);

				if (index === -1) {
					selected_rows.push(id);
				}
				else {
					selected_rows.splice(index, 1);
				}

				if (selected_rows.length > 0) {
					table.button('.print').enable();
					table.button('.delivered').enable();
				}
				else {
					table.button('.print').disable();
					table.button('.delivered').disable();
				}
			});

            var route = '{!! route('admin.tracking.index') !!}';
			$('#datatable tbody').on('click','tr td.shipment_count button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments .modal-body').html('');
                $('#shipments').modal('show');

                $.ajax({
                    url: '{!! route('admin.handover.list.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var shipments = '';
                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#shipments .modal-body').html(shipments);


                        }
                    });

            });

            $('#datatable tbody').on('click','tr td.remaining_shipment_count button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments .modal-body').html('');
                $('#shipments').modal('show');

                $.ajax({
                    url: '{!! route('admin.handover.list.remaining') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var shipments = '';
                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#shipments .modal-body').html(shipments);


                        }
                    });

            });
        });

    </script>
@endsection