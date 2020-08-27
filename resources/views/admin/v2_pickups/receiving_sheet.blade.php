@extends('admin.layout.master')

@section('title', 'Receiving Sheet')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Receiving Sheet
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center mb-2" id="search_form">
                                <div class="col-3">
                                    <fieldset class="form-group">
                                        <select name="search_rider" id="search_rider" class="form-control select2">
                                            @foreach($riders as $rider)
                                                <option value="{{$rider->id}}">{{$rider->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>


                                <div class="col-2">
                                    <button type="button" id="search_filter_btn"  class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Current Rider</th>
                                    <th class="border-primary border-darken-1">Assigned Date</th>
                                    <th class="border-primary border-darken-1">Pickup Note ID</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
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
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function () {

            var selected_rows = [];
            var table = $('#datatable').DataTable({
                "searching": false,
                rider:id=$('#search_rider').val(),
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.v2_pickups.receiving_sheet.list') }}',
                    data: function (d) {
                        d.rider_id = $('#search_rider').val();
                    }
                },
                rowId: 'id',
                deferLoading: 0,
                order: [[1, 'asc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'serial_number', orderable: false, searchable: false, name: 'pickup_requests.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'current_rider', name: 'cr.name', class: 'align-middle current_rider', orderable: false},
                    {data: 'assigned_date', name: 'vpa.created_at', class: 'align-middle attempted_date', orderable: false, searchable: false},
                    {data: 'pickup_note_no', name: 'vpn.pickup_note_id', class: 'align-middle pickup_note_no', orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
            });

            $('#datatable tbody').on('click', 'tr td.pickup_note_no button.print', function() {
                var pickup_note_id = parseInt($(this).attr('rel'));

                print(pickup_note_id);
            });
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.v2_pickups.receiving_sheet.print') !!}',
                    method: 'POST',
                    data: {
                        'ids': [id],
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
                    });
            }
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Rider*',
                width: '100%',
                allowClear: true
            });


            $('#search_filter_btn').on('click', function () {
                var rider = $('#search_rider').val();
                if(rider == null || rider == ''){
                    var error = "Rider not selected!"
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
              else{

                    table.draw();
                }

            });
        });
    </script>
@endsection