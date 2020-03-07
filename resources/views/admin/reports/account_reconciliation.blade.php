@extends('admin.layout.master')

@section('title', 'Account Reconciliation Report')

@section('content')
    <h1 class="mb-1">
        Account Reconciliation Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">

                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
                            </div>

                            <input type="text" name="search_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date" placeholder="Search Date" data-value="{{Carbon\Carbon::now()}}">
                        </div>

                        <div class="form-group ml-1">
                            <button type="submit" name="search" class="btn btn-primary" value="Search">Search</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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
        #search_date_table{
            display:none;
        }
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var max = '{{ Carbon\Carbon::now()}}';

            var search_date = $('#search_form #search_date').pickadate({
                firstDay: 1,
                disable:[true],
                clear: '',
                today:'Select Current Month',
                max: max,
                format:'mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#search_date_root').css('top','40px');
                },
                onSet: function(context) {

                    var from_month = $('#search_date_root .picker__select--month').val();
                    var from_year = $('#search_date_root .picker__select--year').val();
                    var search_date_selected = new Date(from_year,from_month, 1);

                    search_date.pickadate('picker').set('select', search_date_selected,{muted:true});

                    $('#search_date_root button.picker__button--today').removeAttr('disabled');

                }

            });


            $('#search_form').on('submit',function (e) {
                e.preventDefault();
                // blockPagePermanently();
                var search_date = $('#search_form input[name="search_date_formatted"]').val();

                $.ajax({
                    url: '{!! route('admin.reports.account_reconciliation.export_to_excel') !!}',
                    method: 'post',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'date': search_date
                    }
                }).done(function (data) {
                    if(data.success == 1){
                        window.open("{!! route('admin.reports.account_reconciliation.download') !!}",'_black');
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                    }
                    // UnblockPagePermanently();
                });
            });
        });


    </script>
@endsection