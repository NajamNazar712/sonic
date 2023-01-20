@extends('admin.layout.master')

@section('title', 'One Link Charges')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    One Link Charges
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <form id="settings_form" class="form-horizontal text-center" method="POST"
                                    action="{{ route('admin.settings.onelink_payment_charges.store') }}"
                                    novalidate="novalidate">
                                    {{ csrf_field() }}
                                    {{-- <div class="row justify-content-center">
                                        <div class="form-group">
                                            <input type="text" name="default_time" class="form-control time" placeholder="Default Time*" data-rule-required="true" data-msg-required="Default Time is required" value="{{ $default_time }}" data-rule-min="0" data-msg-min="Default Time can not be less than 0" data-rule-max="23" data-msg-max="Default Time can not be more than 23">
                                        </div>
                                    </div> --}}
                                    <div class="charges_div">
                                        <div class="row justify-content-center">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Range Up</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Range Down</label>

                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Charges</label>

                                                </div>
                                            </div>
                                        </div>
                                        @if (count($one_linke_payment_charges_ranges))
                                            @foreach ($one_linke_payment_charges_ranges as $index => $range_charges)
                                                <div class="row justify_content_center mb-1">

                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text"
                                                                name="one_link_charges[{{ $index }}][rangeup]"
                                                                id="rangeup_{{ $index }}"
                                                                class="form-control rangeup"
                                                                value="{{ $range_charges->range_up }}"
                                                                placeholder="Range Up" data-rule-required="true"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text"
                                                                name="one_link_charges[{{ $index }}][rangedown]"
                                                                id="rangedown_{{ $index }}"
                                                                class="form-control rangedown"
                                                                value="{{ $range_charges->range_down }}"
                                                                placeholder="Range Down" data-rule-required="true"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text"
                                                                name="one_link_charges[{{ $index }}][charges]"
                                                                id="charges_{{ $index }}"
                                                                class="form-control charges"
                                                                value="{{ $range_charges->charges }}" placeholder="Charges"
                                                                data-rule-required="true"
                                                                data-msg-required="This field is required">
                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="col mb-1">
                                        <button type="button" class="btn btn-outline-success btm-sm add_row"
                                            id="add_row"><i class="la la-plus"></i></button>
                                    </div>

                                    <div class="col">
                                        <button type="submit" class="btn btn-primary width-250">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>

    <script>
        $(document).ready(function() {
            $('#settings_form input.rangeup').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#settings_form input.rangedown').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#settings_form input.charges').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            var row = 1;
            var one_linke_payment_charges_ranges = @json($one_linke_payment_charges_ranges);
            if (one_linke_payment_charges_ranges.length > 0) {
                row = one_linke_payment_charges_ranges.length;
            }
            $('body').on('click', '.add_row', function() {

                var old_row = row - 1;
                flag = true;
                if (old_row != 0) {
                    var old_range_up = $('#rangeup_' + old_row).val();
                    var old_range_down = $('#rangedown_' + old_row).val();
                    var old_charges = $('#charges_' + old_row).val();
                    if ((old_range_up == null || old_range_up == '') || ((old_range_down == null ||
                            old_range_down == '')) || ((old_charges == null || old_charges == ''))) {
                        flag = false;
                        console.log(old_range_down, old_range_up, old_charges);
                        if (old_range_up == null || old_range_up == '') {
                            var error = "Please enter Range Up Value!!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (old_range_down == null || old_range_down == '') {
                            var error = "Please enter Range Down Value!!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (old_charges == null || old_charges == '') {
                            var error = "Please enter Charges Value!!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    }
                }

                if (flag == true) {

                    var html = '<div class="row justify_content_center mb-1">\n' +
                        '                                        <div class="col">\n' +
                        '                                            <div class="form-group">\n' +
                        '                                               <input type="text" name="one_link_charges[' +
                        row + '][rangeup]" id="rangeup_' + row +
                        '" class="form-control rangeup" placeholder="Range Up" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                            </div>\n' +
                        '                                        </div>\n' +
                        '                                        <div class="col">\n' +
                        '                                            <div class="form-group">\n' +
                        '                                               <input type="text" name="one_link_charges[' +
                        row + '][rangedown]" id="rangedown_' + row +
                        '" class="form-control rangedown" placeholder="Range Down" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                            </div>\n' +
                        '                                        </div>\n' +

                        '                                        <div class="col">\n' +
                        '                                            <div class="form-group">\n' +
                        '                                               <input type="text" name="one_link_charges[' +
                        row + '][charges]" id="charges_' + row +
                        '" class="form-control charges" placeholder="Charges" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                            </div>\n' +
                        '                                        </div>\n' +
                        '</div>\n' +
                        '                                    </div>';
                    $('div.charges_div').append(html);



                    $('#settings_form input.rangeup').inputmask({
                        'alias': 'integer',
                        'allowMinus': false,
                        'allowPlus': false
                    });
                    $('#settings_form input.rangedown').inputmask({
                        'alias': 'integer',
                        'allowMinus': false,
                        'allowPlus': false
                    });
                    $('#settings_form input.charges').inputmask({
                        'alias': 'integer',
                        'allowMinus': false,
                        'allowPlus': false
                    });
                    row++;
                }

            });

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Settings are being updated!',
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
