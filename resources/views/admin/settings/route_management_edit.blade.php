<form action="{{route('admin.settings.route_management.update',['id'=>$route_management->id])}}" method="post" class="mt-2" id="editRouteManagementForm" novalidate="novalidate">

    @csrf
@method('put')
<div class="row align-items-center justify-content-center">
    <div class="col-md-12">
        <div class="row justify-content-center">
            <div class="col-6 form-group">
                <label for="route_code">Route Code</label>
                <input type="text" name="route_code" value="{{$route_management->route_code}}" id="route_code" class="form-control route_code" placeholder="Route Code*" data-rule-required="true" data-msg-required="Route Code is required" >
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-6 form-group">
                <label for="route_title">Route Title</label>
                <input type="text" name="route_title" id="route_title" value="{{$route_management->route_title}}" class="form-control route_title" placeholder="Route Title*" data-rule-required="true" data-msg-required="Route Title is required" >
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-6 form-group">
                <label for="starting_point_id">Starting Point</label>

                <select class="form-control starting_point_id_edit" name="starting_point_id" id="starting_point_id_edit" data-rule-required="true" data-msg-required="Starting Point is required">
                    @foreach($cities as $city)
                        <option value="{{$city->id}}">{{$city->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row justify-content-center">

            <div class="col-6 form-group">
                <label for="end_point_id">End Point</label>

                <select class="form-control end_point_id_edit" name="end_point_id" id="end_point_id_edit" data-rule-required="true" data-msg-required="End Point_id is required">
                    @foreach($cities as $city)
                        <option value="{{$city->id}}">{{$city->name}}</option>
                    @endforeach
                </select>
            </div>
            
        </div>
        <div class="row justify-content-center">
            <div class="col-6 form-group">
                <label for="end_point_id">Junctions</label>
            @php
                $i=1;
            @endphp
                @foreach ($route_management->junctions as $item)

                    <select class="form-control junctions_select" name="junction[{{$item->id}}]" id="junction_{{$item->id}}" data-rule-required="true" data-msg-required="Junction 1 is required">
                        @foreach($cities as $city)
                            @if ($item->junction_id == $city->id)
                            <option value="{{$city->id}}" selected>{{$city->name}}</option>
                            @else
                            <option value="{{$city->id}}">{{$city->name}}</option>
                            @endif
                        @endforeach
                    </select>
                    @php
                        $i++
                    @endphp
                @endforeach
                    
            </div>
            
        </div>
        <div id="edit_junctions">

        </div>
        <div class="row justify-content-center">
            <button type="button" class="btn btn-outline-success mr-1" title="Add more junctions" id="edit_add_junction"><i class="la la-plus"></i>Add Junction</button>
        </div>
        <div class="row justify-content-center">
            <p class="danger">Note: please add junctions in sequence!</p>
        </div>
        
    </div>
    
    <div class="col-auto">
        <div class="form-group text-right my-1">
            <button type="submit" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-dark btn-min-width mr-1" data-dismiss="modal">Cancel</button>

        </div>
    </div>
</div>

    


</form>

<script type="text/javascript">
    $(document).ready(function () {

        $(".starting_point_id_edit").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Starting Point",
                width:'100%',
                dropdownParent:$('#editRouteManagementForm')
            });
            $(".end_point_id_edit").prepend('<option value="" selected></option>').select2({
                placeholder: "Select End Point",
                width:'100%',
                dropdownParent:$('#editRouteManagementForm')
            });
            
            $(".junctions_select").select2({
                        placeholder: "Select Junction 1",
                        width:'100%',
                        dropdownParent:$('#editRouteManagementForm')
            });
            var row = 1;
            $('#editRouteManagementDiv #editRouteManagementForm #edit_add_junction').on('click', function(){

               var html = '<div class="row justify-content-center">\n' +
                   '                            <div class="col-6 form-group">\n' +
                   '                                <select class="form-control edit_junctions" name="new_junction[' + row + ']" id="new_junction_' + row + '" data-rule-required="true" data-msg-required="Junction ' + row + ' is required">\n' +
                   '                                    @foreach($cities as $city)\n' +
                   '                                        <option value="{{$city->id}}">{{$city->name}}</option>\n' +
                   '                                    @endforeach\n' +
                   '                                </select>\n' +
                   '                            </div>\n' +
                   '                        </div>';

                $('#edit_junctions').append(html);
                
                $("#new_junction_" + row).prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Junction " + row,
                    width:'100%',
                    dropdownParent:$('#editRouteManagementForm')
                });
                console.log(html);
                row++;
            });


                var starting_point_id = @json($route_management->starting_point_id);
                $('.starting_point_id_edit').val(starting_point_id).trigger('change');

                var end_point_id = @json($route_management->end_point_id);
                $('.end_point_id_edit').val(end_point_id).trigger('change');


        var errors = 0;
        
        $( "#editRouteManagementForm" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            normalizer: function(value) {
                    return $.trim(value);
                },
            submitHandler: function(form) {
                // $(form).find('button[type=submit]').attr('disabled', 'disabled');
                if(errors === 1){
                    return false;
                }else{
                    form.submit();
                }
            }
        });


    });
</script>