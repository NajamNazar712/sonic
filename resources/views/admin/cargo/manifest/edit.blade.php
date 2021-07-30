@extends('admin.layout.master')

@section('title', 'Edit Mapping')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Edit Mapping
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <form id="edit_mapping" class="form-horizontal" method="POST" action="{{ route('admin.cargo.mapping.manifest.update',$mapping->id) }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <select name="origin" class="select2 origin" data-rule-required="true" data-msg-required="Origin Hub is required">
                                                        @foreach($cities as $city)
                                                            <option value="{{$city->id}}" {{$city->id == $mapping->origin_id ? 'selected' : ''}}>{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group">
                                                    <select name="destination" class="select2 destination" data-rule-required="true" data-msg-required="Destination Hub is required">
                                                        @foreach($cities as $city)
                                                            <option value="{{$city->id}}" {{$city->id == $mapping->destination_id ? 'selected' : ''}}>{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-1" id="add_junctions_container">
                                            @if($mapping->junctions->count() > 0)
                                            @foreach($mapping->junctions as $key => $j)
                                                <div class="junction_container row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <select name="junctions[{{$key+1}}]" id="junction_{{$key+1}}" class="select2 junctions" @if(!$loop->first) data-rule-required="true" data-msg-required="Junction is Required" @endif>
                                                                @foreach($cities as $junction)
                                                                    <option value="{{$junction->id}}" {{$junction->id == $j->junction_id ? 'selected' : ''}}>{{$junction->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    @if($loop->first)
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <button type="button" id="add_junction" class="btn btn-primary">Add Junction</button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <button type="button" class="btn btn-danger remove_junction">Remove Junction</button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @else
                                                <div class="junction_container row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <select name="junctions[1]" id="junction_1" class="select2 junctions" data-msg-required="Junction is Required">
                                                                @foreach($cities as $junction)
                                                                    <option value="{{$junction->id}}">{{$junction->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <button type="button" id="add_junction" class="btn btn-primary">Add Junction</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <table class="table table-bordered" id="junction_table">
                                                    <thead>
                                                    <tr role="row" class="bg-primary white">
                                                        <th class="border-primary border-darken-1">S.No</th>
                                                        <th class="border-primary border-darken-1">Starting</th>
                                                        <th class="border-primary border-darken-1">Ending</th>
                                                        <th class="border-primary border-darken-1">Vehicle</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($mapping->routes as $key => $routes)
                                                    <tr>
                                                        <td>{{$key + 1}}</td>
                                                        <td>{{$routes->starting->name}}</td>
                                                        <td>{{$routes->ending->name}}<input type='hidden' value='{{$routes->ending_hub_id}}' name='route_junctions[{{$key + 1}}]'></td>
                                                        <td>
                                                            @php
                                                                $v_array = [];
                                                                foreach ($routes->vehicles as $v)
                                                                {
                                                                    array_push($v_array,$v->vehicle_id);
                                                                }
                                                            @endphp
                                                            <div class="form-group">
                                                                <select multiple="multiple" name="vehicles[{{$key + 1}}][]" id="vehicles_{{$key + 1}}" class="vehicles_select" data-msg-required="Vehicle is Required" data-rule-required="true">
                                                                    @foreach($vehicles as $vehicle)
                                                                        <option value="{{$vehicle->id}}" {{in_array($vehicle->id,$v_array) ? 'selected' : ''}}>{{$vehicle->reg_number}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="w-100 text-center">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var junction_table = $("#edit_mapping #junction_table").DataTable({
                dom: 'ltipr',
                paging:false,
                autoWidth: false,
                columns: [
                    {orderable: false, searchable: false, name: 'piece_serial_number', class: 'align-middle serial_number'},
                    {name: 'starting', class: 'align-middle starting', orderable: false, searchable: false},
                    {name: 'ending', class: 'align-middle ending', orderable: false, searchable: false},
                    {name: 'vehicle', class: 'align-middle vehicle', sortable: false, orderable: false, searchable: false}
                ],
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#edit_mapping .origin').prepend('<option value=""></option>').select2({
                width: '100%',
                placeholder: 'Origin Hub*'
            }).bind('change', function() {
                $(this).valid();
                junctions_display()
            });

            $('#edit_mapping .destination').prepend('<option value=""></option>').select2({
                width: '100%',
                placeholder: 'Destination Hub*'
            }).bind('change', function() {
                $(this).valid();
                junctions_display()
            });

            $('#edit_mapping .junctions').prepend('<option value="" {{($mapping->junctions->count() == 0) ? 'selected' : ''}}></option>').select2({
                width: '100%',
                placeholder: 'Junction*',
                allowClear: true
            }).bind('change', function() {
                $(this).valid();
                junctions_display()
            });


            $('#edit_mapping #add_junction').on('click',function (){
                count = document.getElementById("add_junctions_container").children.length + 1;
                html = `
                <div class="junction_container row">
                    <div class="col">
                        <div class="form-group">
                            <select name="junctions[${count}]" class="select2 junctions" id="junction_${count}" data-rule-required="true" data-msg-required="Junction is Required">
                                @foreach($cities as $junction)
                                    <option value="{{$junction->id}}">{{$junction->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <button type="button" class="btn btn-danger remove_junction">Remove Junction</button>
                        </div>
                    </div>
                </div>`;
                $("#add_junctions_container").append(html);

                $('#add_junctions_container #junction_'+count+'').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Junction*'
                }).bind('change', function() {
                    $(this).valid();
                    junctions_display();
                });
            });

            $(document).on('click',"#edit_mapping .remove_junction",function (){
                $(this).closest('.junction_container').remove();
                junctions_display()
            });

            $('#edit_mapping').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    blockPagePermanently();
                    swal({
                        title: 'Please Wait!',
                        text: 'Mapping is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }

            });

            function make_vehicle_select(index)
            {
                vehicles = `<div class="form-group">
                                    <select multiple="multiple" name="vehicles[${index}][]" id="vehicles_${index}" class="vehicles_select" data-msg-required="Vehicle is Required" data-rule-required="true">
                                        @foreach($vehicles as $vehicle)
                <option value="{{$vehicle->id}}">{{$vehicle->reg_number}}</option>
                                        @endforeach
                </select>
             </div>`;

                return vehicles;
            }

            function make_junction_input(text,id,index)
            {
                var junction_html = `${text}<input type='hidden' value='${id}' name='route_junctions[${index}]'>`;

                return junction_html;
            }

            function junctions_display()
            {
                origin = $("#edit_mapping .origin").find(":selected").text();
                destination = $("#edit_mapping .destination").find(":selected").text();
                destination_id = $("#edit_mapping .destination").find(":selected").val();
                junction_table.rows().remove();

                if($("#edit_mapping .junction_container .junctions").length > 1)
                {
                    var lastIndex = $("#edit_mapping .junction_container .junctions").length - 1;
                    $("#edit_mapping .junction_container .junctions").each(function (index){
                        if(index == 0)
                        {
                            junction_table.row.add([index+1,origin,make_junction_input($(this).find(":selected").text(),$(this).find(":selected").val(),index+1),make_vehicle_select(index + 1)]);
                            id_index = index + 1;
                            previous_junction = $(this).find(":selected").text();
                        }
                        else{
                            junction_table.row.add([index+1,previous_junction,make_junction_input($(this).find(":selected").text(),$(this).find(":selected").val(),index+1),make_vehicle_select(index + 1)]);
                            previous_junction = $(this).find(":selected").text();
                        }
                    });
                    junction_table.row.add([lastIndex + 2,previous_junction,make_junction_input(destination,destination_id,lastIndex+2),make_vehicle_select(lastIndex + 2)]);
                }
                else{
                    if($("#edit_mapping .junction_container .junctions").first().val() != '')
                    {
                        junction_table.row.add([1,origin,make_junction_input($("#edit_mapping .junction_container .junctions").first().find(":selected").text(),$("#edit_mapping .junction_container .junctions").first().find(":selected").val(),1),make_vehicle_select(1)]);
                        junction_table.row.add([2,$("#edit_mapping .junction_container .junctions").first().find(":selected").text(),make_junction_input(destination,destination_id,2),make_vehicle_select(2)]);
                    }
                    else{
                        junction_table.row.add([1,origin,make_junction_input(destination,destination_id,1),make_vehicle_select(1)]);
                    }
                }
                junction_table.draw(false);
                junction_table.columns.adjust().draw();

                $("#edit_mapping .vehicles_select").select2({
                    width: '100%',
                    placeholder: 'Vehicles*'
                }).bind('select2:select', function(e){
                    var current_val = e.params.data.id;
                    var select = $(this);
                    $("#edit_mapping .vehicles_select").each(function (){
                        if($.inArray(current_val,$(this).val()) != -1 && $(this).attr('id') != select.attr('id'))
                        {
                            toastr.error("Vehicle Can Not Be Repeated", 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            var arr = $(select).val();
                            arr.splice(arr.indexOf(current_val),1);
                            $(select).val(arr).trigger('change');
                        }
                    });
                });
            }

            $("#edit_mapping .vehicles_select").select2({
                width: '100%',
                placeholder: 'Vehicles*'
            }).bind('select2:select', function(e){
                var current_val = e.params.data.id;
                var select = $(this);
                $("#edit_mapping .vehicles_select").each(function (){
                    if($.inArray(current_val,$(this).val()) != -1 && $(this).attr('id') != select.attr('id'))
                    {
                        toastr.error("Vehicle Can Not Be Repeated", 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        var arr = $(select).val();
                        arr.splice(arr.indexOf(current_val),1);
                        $(select).val(arr).trigger('change');
                    }
                });
            });
        });
    </script>
@endsection