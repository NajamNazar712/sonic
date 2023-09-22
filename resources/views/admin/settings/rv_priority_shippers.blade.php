@extends('admin.layout.master')

@section('title', 'RV Priority Shipper')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    RV Priority Shipper
                </h1>

                {{-- {{ dd($rv_shipper_priorities) }} --}}
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-6">

                                    <form id="settings_form" class="form-horizontal text-center" method="POST"
                                        action="{{ route('admin.settings.rv_shipper_priority.store') }}"
                                        novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <div class="row mb-2 justify-content-center">
                                            <div class="col-12 form-group">
                                                <input type="hidden" class="unsorted_zones" name="unsorted_zones">

                                                <select name="shippers[]" class="unsorted_zones" id="shippers_select"
                                                    class="form-control select2" multiple="multiple"
                                                    data-msg-required="Atleast one shipper is required"
                                                    data-rule-required="true" required="required">
                                                    @foreach ($shippers as $shipper)
                                                        <option value="{{ $shipper->id }}"
                                                            {{ in_array($shipper->id, $rv_shipper_priorities) ? 'selected' : '' }}>
                                                            {{ $shipper->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
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

@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var $select2 = $('#shippers_select').select2({
                templateSelection: template,
                width: '100%',
                placeholder: 'Select Shippers',
            });

            // Initialize with default values
            var defaultValues = {!! json_encode($rv_shipper_priorities) !!};
            $select2.val(defaultValues).trigger('change');

            // Cache the original order of selected values
            var selectedOrder = defaultValues.slice();

            $select2.on('select2:select', function(e) {
                // When a new option is selected, add it to the selectedOrder
                selectedOrder.push(e.params.data.id);

                $('.unsorted_zones').val(selectedOrder.join(', '));

                // Update the Select2 value to reflect the new order
                $select2.val(selectedOrder).trigger('change');
            });

            $select2.on('select2:unselect', function(e) {
                // When an option is unselected, remove it from the selectedOrder
                var index = selectedOrder.indexOf(e.params.data.id);
                if (index !== -1) {
                    selectedOrder.splice(index, 1);
                    $('.unsorted_zones').val(selectedOrder.join(', '));

                }
            });

            $select2.on('select2:clear', function() {
                // When all options are cleared, reset the selectedOrder
                selectedOrder = [];
            });


            /**
             * Customize the display of each option in the dropdown.
             * @param data
             * @param container
             */
            function template(data, container) {
                return data.text;
            }

            function customOrderSelections() {
                var $selectedOptions = $select2.select2('data');
                var $selectionContainer = $select2.next('.select2-container').find('.select2-selection__rendered');
                var selectedIds = [];
                var selectedOrder = [];

                // Clear the existing selections
                $selectionContainer.empty();

                // Append selections in the desired order
                defaultValues.forEach(function(value) {
                    var selectedOption = $selectedOptions.find(function(option) {
                        return option.id == value;
                    });
                    if (selectedOption) {
                        var $option = $('<span class="select2-selection__choice"></span>');
                        var $removeButton = $(
                            '<span class="select2-selection__choice__remove" tabindex="-1">×</span>');

                        $option.text(selectedOption.text);
                        $option.attr('title', selectedOption.text);
                        $option.append($removeButton);
                        $selectionContainer.append($option);

                        // Add the selected value to the selectedIds array
                        selectedIds.push(selectedOption.id);
                        // Add the selected value to the selectedOrder array to maintain order
                        selectedOrder.push(selectedOption.id);

                        // Add a click event handler to the remove button
                        $removeButton.on('click', function() {
                            // Find the index of the selectedOption to be removed
                            var selectedIndex = selectedOptions.findIndex(function(option) {
                                return option.id === selectedOption.id;
                            });

                            if (selectedIndex !== -1) {
                                // Remove the option from the selectedOptions array
                                selectedOptions.splice(selectedIndex, 1);

                                // Remove the ID from the selectedIds array
                                selectedIds = selectedOptions.map(function(option) {
                                    return option.id;
                                });

                                // Remove the ID from the selectedOrder array
                                selectedOrder = selectedOrder.filter(function(id) {
                                    return id !== selectedOption.id;
                                });

                                // Update the input field value
                                $('.unsorted_zones').val(selectedOrder.join(', '));
                            }

                            // Remove the option from the selection container
                            $option.remove();
                        });
                    }
                });

                // Set the selectedIds as the value of the input field
                $('.unsorted_zones').val(selectedIds.join(', '));

                // Restore the original order of selected options in the dropdown
                $select2.val(selectedOrder).trigger('change');
            }

            // Call the function to display selections in the desired order and add remove buttons
            customOrderSelections();





        });
    </script>
@endsection
