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

                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.rv_shipper_priority.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <div class="row mb-2 justify-content-center">
                                            <div class="col-12 form-group">
                                                <select name="shippers[]" class="unsorted_zones" id="shippers_select" class="form-control select2" multiple="multiple" data-msg-required="Atleast one shipper is required" data-rule-required="true" required="required">
                                                    @foreach($shippers as $shipper)
                                                        <option value="{{$shipper->id}}" {{ in_array($shipper->id, $rv_shipper_priorities) ? 'selected' : '' }}>{{$shipper->name}}</option>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var $select2 = $('#shippers_select').select2({
                templateSelection: template,
                width: '100%',
                placeholder: 'Select Zones',
            });
            
            var defaultValues = {!! json_encode($rv_shipper_priorities) !!};

                 // Initialize with default values
            $select2.val(defaultValues).trigger('change');

            // Cache order of initial values
            var preservedOrder = defaultValues.slice();

            $select2.on('select2:select select2:unselect', selectionHandler);

            function selectionHandler(e) {
                var val = e.params.data.id;

                switch (e.type) {
                    case 'select2:select':
                        preservedOrder.push(val);
                        break;
                    case 'select2:unselect':
                        var foundIndex = preservedOrder.indexOf(val);
                        if (foundIndex >= 0) {
                            preservedOrder.splice(foundIndex, 1);
                        }
                        break;
                }

                // Store the updated order
                $select2.data('preserved-order', preservedOrder);

                // Render selections in the preserved order
                select2_renderSelections($select2);
            }

            function select2_renderSelections($select2) {
                var order = $select2.data('preserved-order') || [];
                var $container = $select2.next('.select2-container');
                var $tags = $container.find('li.select2-selection__choice');
                var $input = $tags.last().next();


                var stringList = order.filter(function(item) {
                    return typeof item === 'string';
                });


                // Apply tag order
                order.forEach(function(val) {
                    var $el = $tags.filter(function(i, tag) {
                        return $(tag).data('data').id === val;
                    });
                    $input.before($el);
                });

                var selectedIds = $select2.val() || []; 
        
                var unsortedZonesValue = $('.unsorted_zones').val();

                if (typeof unsortedZonesValue === 'string') {
                    var idArray = unsortedZonesValue.split(',');
                } else {
                    return;
                }

                selectedIds = selectedIds.filter(function(item) {
                    return idArray.indexOf(item) === -1;
                });

                selectedIds = selectedIds.concat(idArray);

                $('.unsorted_zones').val(selectedIds)

            }

            /**
             * Customize the display of each option in the dropdown.
             * @param data
             * @param container
             */
            function template(data, container) {
                return data.text;
            }

        });
     
    </script>
@endsection