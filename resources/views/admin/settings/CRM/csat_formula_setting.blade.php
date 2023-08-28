@extends('admin.layout.master')

@section('title', 'CSAT Formula Setting')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    CSAT Formula Setting
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form action="{{ route('admin.settings.csat_cases_setting.formula.store') }}" method="POST">
                                @csrf

                                <div class="row justify-content-center">
                                    <div id="csatFormulaValues" class="d-none">
                                        @foreach ($formulas as $formula)
                                            <div class="formula-value">{{ $formula }}</div>
                                        @endforeach
                                    </div>
                                    <div class="col-6">
                                        <div class="row mb-2 justify-content-center red-box-container">
                                            <div class="col-2 red-box" id="box-1" data-value="1">
                                                <h3 class="color">1</h3>
                                            </div>
                                            <div class="col-2 red-box" id="box-2" data-value="2">
                                                <h3 class="color">2</h3>
                                            </div>
                                            <div class="col-2 red-box" id="box-3" data-value="3">
                                                <h3 class="color">3</h3>
                                            </div>
                                            <div class="col-2 red-box" id="box-4" data-value="4">
                                                <h3 class="color">4</h3>
                                            </div>
                                            <div class="col-2 red-box" id="box-5" data-value="5">
                                                <h3 class="color">5</h3>
                                            </div>
                                        </div>
                                    </div>


                                    <textarea rows="2" cols="50" id="value" readonly>
                                </textarea>

                                <input type="hidden" name="submitted_values" id="submitted_values" value="">
                                </div>


                                <button type="submit" class="btn btn-info">Update</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .red-box-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .red-box {
            width: 50px;
            height: 50px;
            background-color: red;
            margin: 5px;
        }

        .btn-info{
            margin-left: 650px;
            margin-bottom: 50px;
        }
        .color{
            color: white;
        }
    </style>

@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>z
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            const greenBoxValues = [];

            $('.formula-value').each(function() {
                greenBoxValues.push(parseInt($(this).text()));
            });

            $('.red-box').each(function() {
                const boxValue = parseInt($(this).data('value'));
                if (greenBoxValues.includes(boxValue)) {
                    $(this).css('background-color', 'green');
                }
            });

            var newValue = null;
            newValue = 'Total ' + greenBoxValues + ' Star Rating Cases / Total Rated Cases * 100 ';
            $('#value').text(newValue);
            $('#submitted_values').val(greenBoxValues);

            $('.red-box').click(function() {
                const boxValue = $(this).data('value');
                const currentColor = $(this).css('background-color');

                if (currentColor === 'rgb(255, 0, 0)') {
                    $(this).css('background-color', 'green');
                    greenBoxValues.push(boxValue);
                } else {
                    $(this).css('background-color', 'red');
                    const index = greenBoxValues.indexOf(boxValue);
                    if (index > -1) {
                        greenBoxValues.splice(index, 1);
                    }
                }

                greenBoxValues.sort(function(a, b) {
                    return a - b;
                });
                console.log(typeof greenBoxValues);
                newValue = 'Total ' + greenBoxValues + ' Star Rating Cases / Total Rated Cases * 100 ';
                $('#value').text(newValue);

                $('#submitted_values').val(greenBoxValues);
            });

        });
    </script>
@endsection
