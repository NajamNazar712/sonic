@extends('admin.layout.master')

@section('title', 'Employee Attendance (Horizontal)')

@section('content')
    <h1>Employee Attendance (Horizontal)</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12 ">
                                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                        <div class="col-5 mt-2">

                                            <div class="form-group input-group ml-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                        <span class="la la-calendar-o"></span>
                                                    </span>
                                                </div>

                                                <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Attendance (From)">
                                            </div>
                                        </div>
                                        <div class="col-5 mt-2">
                                            <div class="form-group input-group ml-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                        <span class="la la-calendar-o"></span>
                                                    </span>
                                                </div>

                                                <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Attendance (To)">
                                            </div>

                                        </div>
                                        <div class="col-5 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_admin" id="search_admin" class="form-control select2">
                                                    @foreach($admins as $admin)
                                                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-5 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_rider" id="search_rider" class="form-control select2">
                                                    @foreach($riders as $rider)
                                                        <option value="{{$rider->id}}">{{$rider->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-5 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_trax_id" id="search_trax_id" class="form-control select2">
                                                    @foreach($trax_ids as $trax_id)
                                                        <option value="{{$trax_id}}">{{$trax_id}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-5 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_department" id="search_department" class="form-control select2">
                                                    @foreach($departments as $department)
                                                        <option value="{{$department->id}}">{{$department->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group justify-content-center">
                                                <button type="button" id="search_filter_btn"
                                                        class="btn btn-outline-info btn-min-width"><i class="la la-search"></i>
                                                    Search
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div id="attendance_container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">

    <style>
       /* .picker__table{
            display: none;
        }
        .picker__button--close:before{
            content: '' !important;
            color: green !important;
        }*/
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    $('#search_date_to').pickadate('picker').clear({muted: true});
                    if (context.select) {
                        var selected_date = new Date(context.select);
                        var max_selected_date = moment(selected_date).add(31, 'days');
                        console.log(selected_date);
                        $('#search_date_to').attr('disabled', false);
                        $('#search_form #search_date_to').pickadate('picker').set({'min':selected_date},{'max':max_selected_date.toDate()},{muted: true});
                        $('#search_form #search_date_to').pickadate('picker').set({'max':max_selected_date.toDate()},{muted: true});
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        // $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            $('#search_admin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Staff',
                width:'100%',
                allowClear:true
            });
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Rider',
                width:'100%',
                allowClear:true
            });
            $('#search_department').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Department',
                width:'100%',
                allowClear:true
            });
            $('#search_trax_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Employee ID',
                width:'100%',
                allowClear:true
            });
           /* var search_month = $('#search_form #search_month').pickadate({
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'mmmm yyyy',
                format: 'mmmm yyyy',
                close: 'Select',
                hiddenSuffix: '_formatted',
                onClose: function() {
                    $(".picker__table .picker__day--infocus").first().trigger('click');
                },
            });*/

            $('#search_filter_btn').on('click',function () {
                var search_from = $('#search_date_from').val();
                var search_to = $('#search_date_to').val();

                if(search_from === '' || search_to === ''){
                    var error = "Date range is required";
                    toastr.error(error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                    return false;
                }
                else {
                    $.ajax({
                        url: '{!! route('admin.attendance.horizontal.table') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'search_from': search_from,
                            'search_to': search_to,
                        }
                    }).done(function (data) {
                        if (data.status == 1) {
                            var html = '<table class="table table-bordered datatable " id="datatable" style="z-index: 3;">' +
                                '                    <thead>' +
                                '                    <tr class="bg-primary white">' +
                                '                        <th class="border-primary border-darken-1">Employee Id</th>' +
                                '                        <th class="border-primary border-darken-1">Employee Name</th>' +
                                '                        <th class="border-primary border-darken-1">Department</th>' +
                                '                        <th class="border-primary border-darken-1">Designation</th>';

                            $.each(data.period, function (i, date) {
                                html += '                <th class="border-primary border-darken-1">' + date + '</th>';
                            });


                            html += '                    </tr>' +
                                '                    </thead></table>';

                            var columns = [];
                            columns.push({data: 'trax_id', name: 'a.trax_id', class: 'align-middle trax_id'});
                            columns.push({data: 'name', name: 'a.name', class: 'align-middle name'});
                            columns.push({data: 'department', name: 'ad.name', class: 'align-middle department'});
                            columns.push({
                                data: 'designation',
                                name: 'a.designation',
                                class: 'align-middle designation'
                            });
                            $.each(data.period, function (i, date) {
                                columns.push({
                                    data: '' + date + '',
                                    class: 'align-middle text-center dates',
                                    orderable: false,
                                    searchable: false
                                });
                            });

                            $('#attendance_container').html('');
                            $('#attendance_container').html(html);



                            var table = $('#datatable').DataTable({
                                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                                buttons: [
                                    {
                                        extend: 'excel',
                                        className: 'btn btn-primary',
                                        title: 'Employee Attendance (Horizontal)',
                                        text: '<i class="la la-file-excel-o"></i> Excel',
                                        action: function (e, dt, node, config) {
                                            var that = this;
                                            var params = table.ajax.params();
                                            params.start = 0;
                                            params.length = -1;
                                            params.excel = true;
                                            params._token = "{{csrf_token()}}";
                                            $.ajax({
                                                url: '{!! route('admin.attendance.horizontal.list') !!}',
                                                method: 'Post',
                                                data: params,
                                            }).done(function (data) {
                                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(that, e, dt, node, config);
                                            });
                                        },
                                    }],
                                scrollX: true, scrollY: '500px',
                                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                                pageLength: 50,
                                autoWidth: false,
                                pagingType: 'full_numbers',
                                // deferLoading: true,
                                processing: true,
                                language: {
                                    processing: data_table_loader
                                },
                                serverSide: true,
                                ajax: {
                                    url: '{{ route('admin.attendance.horizontal.list') }}',
                                    method: "Post",
                                    data: function (d) {
                                        d._token = "{{csrf_token()}}";
                                        d.search_admin = $('#search_admin').val();
                                        d.search_rider = $('#search_rider').val();
                                        d.search_department = $('#search_department').val();
                                        d.search_trax_id = $('#search_trax_id').val();
                                        d.search_from = search_from;
                                        d.search_to = search_to;
                                    }
                                },
                                order: [[0, 'desc']],
                                rowId: 'id',
                                columns: columns,
                                initComplete: function () {
                                    this.api().table().columns.adjust();
                                }

                            });
                        }
                    });
                }
            });

        });
    </script>

@endsection