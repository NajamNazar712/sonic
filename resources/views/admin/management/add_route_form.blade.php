{{--/**--}}
 {{--* Created by PhpStorm.--}}
 {{--* User: WaqasTrax--}}
 {{--* Date: 5/30/2018--}}
 {{--* Time: 12:00 PM--}}
 {{--*/--}}
<style>
    textarea#junction {
        resize: none;
    }
</style>
{{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMo9kqvMhqVAe_GCXZXOfzfAZ_oeBapkQ&callback=initMap" type="text/javascript"></script>--}}
<form action="{{route('admin.management.route.add')}}" method="post" class="mt-2" id="addRouteForm" novalidate="novalidate">
{{csrf_field()}}
    {{--<div class="row">--}}
        {{--<div class="col-md-6">--}}
    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <select name="city_id" id="city_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                    <option value="" selected>Select a City</option>
                    @foreach($cities as $city)
                        <option value="{{$city->id}}">{{$city->name}}</option>
                    @endforeach
                </select>
            </fieldset>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="route_code" placeholder="Route Code" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>

        </div>
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="start"  id="startSearchTextField" placeholder="Start Point" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
        </div>
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="end"  placeholder="End Point" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
          <fieldset class="form-group">
              <textarea name="junction" class="form-control" placeholder="Add Junctions (comma seperated)" id="junction" cols="30" rows="5" required data-rule-required="true" data-msg-required="This field is required"></textarea>
          </fieldset>
        </div>
    </div>
            {{--<div class="col-md-6">--}}
                {{--<div id="map_canvas"></div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Add Route</button>
        <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>

    </div>
</form>

<script type="text/javascript">
    $(document).ready(function () {


        // $('#addRoute').on('shown.bs.modal', function () {
        //     //map start
        //     $(function () {
        //         var lat = -33.8688,
        //             lng = 151.2195,
        //             latlng = new google.maps.LatLng(lat, lng),
        //             image = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png';
        //
        //         var mapOptions = {
        //                 center: new google.maps.LatLng(lat, lng),
        //                 zoom: 13,
        //                 mapTypeId: google.maps.MapTypeId.ROADMAP
        //             },
        //             map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions),
        //             marker = new google.maps.Marker({
        //                 position: latlng,
        //                 map: map,
        //                 icon: image
        //             });
        //
        //         var input = document.getElementById('startSearchTextField');
        //         var autocomplete = new google.maps.places.Autocomplete(input, {
        //             types: ["geocode"]
        //         });
        //
        //         autocomplete.bindTo('bounds', map);
        //         var infowindow = new google.maps.InfoWindow();
        //
        //         google.maps.event.addListener(autocomplete, 'place_changed', function () {
        //             infowindow.close();
        //             var place = autocomplete.getPlace();
        //             if (place.geometry.viewport) {
        //                 map.fitBounds(place.geometry.viewport);
        //             } else {
        //                 map.setCenter(place.geometry.location);
        //                 map.setZoom(17);
        //             }
        //
        //             moveMarker(place.name, place.geometry.location);
        //         });
        //
        //         $("input").focusin(function () {
        //             $(document).keypress(function (e) {
        //                 if (e.which == 13) {
        //                     infowindow.close();
        //                     var firstResult = $(".pac-container .pac-item:first").text();
        //
        //                     var geocoder = new google.maps.Geocoder();
        //                     geocoder.geocode({"address": firstResult}, function (results, status) {
        //                         if (status == google.maps.GeocoderStatus.OK) {
        //                             var lat = results[0].geometry.location.lat(),
        //                                 lng = results[0].geometry.location.lng(),
        //                                 placeName = results[0].address_components[0].long_name,
        //                                 latlng = new google.maps.LatLng(lat, lng);
        //
        //                             moveMarker(placeName, latlng);
        //                             $("input").val(firstResult);
        //                         }
        //                     });
        //                 }
        //             });
        //         });
        //
        //         function moveMarker(placeName, latlng) {
        //             marker.setIcon(image);
        //             marker.setPosition(latlng);
        //             infowindow.setContent(placeName);
        //             infowindow.open(map, marker);
        //         }
        //     });
        //
        //
        //     //map end
        // });

        $('.select2').select2({
            dropdownParent: $("#addRoute")
        });
//AIzaSyBMo9kqvMhqVAe_GCXZXOfzfAZ_oeBapkQ

        $("#addRouteForm").validate({

            errorClass: "danger",
            errorPlacement: function (error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function (form) {

                $(form).find('button[type=submit]').attr('disabled', 'disabled');
                swal({
                    title: 'Please Wait!',
                    text: 'Route is being added!',
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