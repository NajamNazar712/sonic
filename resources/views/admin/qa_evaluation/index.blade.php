@extends('admin.layout.master')

@section('title', 'QA Evaluation')

@section('content')
    <h1 class="mb-1">
        CX Evaluation
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-12 ">
                        <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Agent Name</th>
                                <th class="border-primary border-darken-1">Campaign</th>
                                <th class="border-primary border-darken-1">Evaluated By</th>
                                <th class="border-primary border-darken-1">Evaluation Date</th>
                                <th class="border-primary border-darken-1">Nature</th>
                                <th class="border-primary border-darken-1">Date and Time</th>
                                <th class="border-primary border-darken-1">Status</th>
                                <th class="border-primary border-darken-1">Score</th>
                                <th class="border-primary border-darken-1">Remarks</th>
                                <th class="border-primary border-darken-1"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <style>
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-return_intransit {
            background-image: linear-gradient(45deg, #ff39aed6, #bb82e7);
            background-repeat: repeat-x;
        }
        
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        span.font-13{
            font-size: 13px;
        }
        .fatal{
            background-color: #EF5753;
        }
        .accurate{
            background-color: springgreen;
            

        }
    </style>

@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.qa_evaluation.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Agent Name');
                            head.push('Campaign');
                            head.push('Evaluated By');
                            head.push('Evaluation Date');
                            head.push('Nature');
                            head.push('Date and Time');
                            head.push('Status');
                            head.push('Score');
                            head.push('Remarks');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.agent_name);
                                row.push(values.campaign);
                                row.push(values.evaluated_by);
                                row.push(values.evaluation_date);
                                row.push(values.nature);
                                row.push(values.date_time);
                                row.push(values.status);
                                row.push(values.score);
                                row.push(values.remarks);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header:head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                
                buttons: [
                    {
                    text: '<i class="ft-plus-circle"></i> Add',
                    className: 'btn btn-primary add',
                    action: function (e, dt, node, config) {
                        window.location = '{{ route('admin.qa_evaluation.add') }}';
                    }
                    },
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'QA Evaluation',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    }
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                rowId: 'id',
                ajax:{
                    url: '{{ route('admin.qa_evaluation.list') }}',
                    // data: function (d) {
                    //     d.search_origin = $('#origin').val();
                    //     d.search_destination = $('#destination').val();
                    //    /* d.search_shipper = $('#shipper').val();*/
                    //     d.search_shipper = $('#search_shipper').val();
                    //     d.cards_filter = $('#cards_filter_input').val();
                    //     d.search_shipping_mode = $('#search_shipping_mode').val();
                    //     d.search_date_from = $('input[name="from_date_formatted"]').val();
                    //     d.search_date_to = $('input[name="to_date_formatted"]').val();
                    // }
                },
                order: [[4, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'agent_name' ,name: 'ad.name', class: 'align-middle agent_name'},
                    { data:'campaign' ,name: 'ec.campaign_id', class: 'align-middle campaign'},
                    { data:'evaluated_by' ,name: 'ev.name', class: 'align-middle evaluated_by'},
                    { data:'evaluation_date' ,name: 'q_a_evaluations.evaluation_date', class: 'align-middle evaluation_date'},
                    { data:'nature' ,name: 'en.id', class: 'align-middle nature'},
                    { data:'date_time' ,name: 'q_a_evaluations.date_time', class: 'align-middle date_time'},
                    { data:'status' ,name: 'q_a_evaluations.status', class: 'align-middle status'},
                    { data:'score' ,name: 'q_a_evaluations.score', class: 'align-middle score'},
                    { data:'remarks' ,name: 'q_a_evaluations.remarks', class: 'align-middle remarks'},
                    { data:'action' ,name: 'action', class: 'align-middle action',orderable: false, searchable: false},

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
                    var nature_search = '<select name="nature_search" id="nature_search" class="select2 form-control"></select>';
                    var campaign_search = '<select name="campaign_search" id="campaign_search" class="select2 form-control"></select>';
                    var status_search = '<select name="status_search" id="status_search" class="select2 form-control"><option value="1">Non-Fatal</option><option value="0">Fatal</option><option value="2">Accurate</option></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.evaluation_date') || $(header).is('.date_time') || $(header).is('.score') || $(header).is('.remarks') ) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.campaign')){
                            $(campaign_search).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.nature')){
                            $(nature_search).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.status')){
                            $(status_search).appendTo($(search))
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
                    var data1 = $.map({!! $natures !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.nature;

                        return obj;
                    });

                    var data2 = $.map({!! $campaigns !!}, function (obj) {
                        obj.id = obj.campaign_id;
                        obj.text = obj.campaign;

                        return obj;
                    });

                    $("#nature_search").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Nature",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#campaign_search").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Campaign",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#status_search").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            
            $('body').on('click','button.qa_edit',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                var link = '{{ route('admin.qa_evaluation.edit', ["id" => 0]) }}';
                    window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
                
                

            });

            $('body').on('click','button.qa_view',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                var link = '{{ route('admin.qa_evaluation.view', ["id" => 0]) }}';
                    window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;

                

            });

        });

    </script>
@endsection