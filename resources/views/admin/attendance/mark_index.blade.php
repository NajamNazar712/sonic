@extends('admin.layout.master')

@section('title', 'Mark Attendance')

@section('content')
    <h1>Mark Attendance</h1>
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')
                            <div class="row mb-2 height-400" style=" position: relative;">
                                <div class="col text-center">
                                     <div id="punch">
                                         <h2 class="centered text-white punch_msg"></h2>
                                     </div>
                                </div>
                            </div>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white text-center">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">Attendance Date</th>
                                    <th class="border-primary border-darken-1">Action Date</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                    <th class="border-primary border-darken-1">Location</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('css')

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        #punch {
            height: 250px;
            width: 250px;
            background-color: #DD3128;
            border-radius: 50%;
            display: inline-block;

            position: absolute;
            top: 50%;
            left: 50%;
            margin: -70px 0 0 -170px;
        }
        .centered ,.center{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>

@endsection

@section('js')

    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    
    <script type="text/javascript">
        $(document).ready(function () {

         var clock_in = @json($clock_in);
         var clock_out = @json($clock_out);
         var date = @json($date);
         var attendance_date = @json($attendance_date);


         if(clock_in == 1){
             $('.centered').html('');
             $('.centered').append('<strong>CLOCK IN</strong>');
             $('#msg_div').addClass('d-none');
         }
         else if(clock_out == 1){
             $('.centered').html('');
             $('.centered').append('<strong>CLOCK OUT</strong>');
             $('#msg_div').addClass('d-none');
         }

         var flag = true;

         $('#punch').on('click',function(){
             if(flag) {
                 $.ajax({
                     url: '{!! route('admin.attendance.mark.submit') !!}',
                     method: 'POST',
                     data: {
                         '_token': '{{ csrf_token() }}',
                         'clock_in': clock_in,
                         'clock_out': clock_out,
                         'attendance_date' : date
                     }
                 })
                     .done(function (data) {

                         if (data.status == 0) {
                             toastr.error(data.error, 'Error!', {
                                 positionClass: 'toast-top-center',
                                 containerId: 'toast-top-center'
                             });
                         } else {
                             table.draw();
                             if(data.error == 0){
                                 swal({
                                     title: data.success,
                                     text: data.date,
                                     icon: 'success',
                                     buttons: false,
                                     closeOnClickOutside: true,
                                     closeOnEsc: true
                                 });
                             }else{
                                 swal({
                                     title: "Already Marked",
                                     text: data.message,
                                     icon: 'error',
                                     buttons: false,
                                     closeOnClickOutside: true,
                                     closeOnEsc: true
                                 });
                             }

                             if (data.status == 1) {
                                 $('.centered').html('');
                                 $('.centered').append('<strong>CLOCK OUT</strong>');
                                 clock_in = 0;
                                 clock_out = 1;

                             } else if (data.status == 2) {
                                 $('.centered').html('');
                                 $('.centered').append('<strong>CLOCK IN</strong>');
                                 clock_in = 1;
                                 clock_out = 0;
                             }
                         }
                     });
                 
                 flag = false;
                 setTimeout(function () {
                    flag = true;
                 }, 30000);
             }
           });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                ],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,

                ajax: {
                    url: '{{ route('admin.attendance.mark.list') }}',
                    data: function (d) {
                        d.attendance_date = attendance_date;
                    }
                },
                order:['2','desc'],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'attendance_date', name: 'attendance_date', class: 'align-middle attendance_date text-center'},
                    {data: 'action_date', name: 'action_date', class: 'align-middle action_date text-center'},
                    {data: 'action_id', name: 'action_id', class: 'align-middle action_id text-center'},
                    {data: 'latitude', name: 'latitude', class: 'align-middle latitude text-center'},
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    this.api().table().columns.adjust();
                }

            });
        });
    </script>

@endsection