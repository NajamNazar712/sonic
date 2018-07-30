<form id="update_dispute_form" action="" method="post">

    <input type="hidden" name="dispute_id" id="dispute_id" value="{{$dispute->id}}">
    <div class="row mb-2">
        <div class="col form-group">
            <select name="city_select" id="update_city_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                <option></option>
                @foreach($cities as $city)
                    <option value="{{$city->id}}">{{$city->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col form-group">
            <select name="dispute_type_select" id="update_dispute_type_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                <option></option>
                @foreach($dispute_types as $type)
                    <option value="{{$type->id}}">{{$type->type}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row mb-2 justify-content-center">
        <div class="col-6 form-group">
            <input name="update_tracking_number" id="update_tracking_number" class="tracking_numbers" data-tags-input-name="tracking_number">

        </div>
    </div>
    <div class="row mb-2 ">
        <div class="col-12 mb-1">
            @foreach($shipments as $cn)
                <span class="pb-1"><u>{{$cn->tracking_number}}</u></span>&emsp;
            @endforeach
        </div>
        <div class="col-12 form-group description-div ">
            <div class="p-1 border">
                <h4><u>Description :</u></h4>
                <p class="">{{$dispute->description}}</p>

            </div>
        </div>

    </div>
    {{--<hr>--}}
    <h2 class="text-left"><u>Comments</u></h2>
    <div class="comments dispute_comments_section">
        <div class="row ">
            <div class="col-12 ">
            @foreach($comments as $comment)

                <div class="comment-row border">
                    <p class="comment">{{ucfirst($comment->comment)}}</p>

                    <span class="">by <b>{{ucfirst($comment->admin->name)}}</b> at {{\Carbon\Carbon::parse($comment->created_at)->format('d/m/Y h:i:s A')}}</span>
                </div>

            @endforeach
            </div>
        </div>
    </div>
    <div class="comment-post">
        <div class="form-group">
            <input type="text" class="form-control block" id="commentbox" placeholder="Write a comment" name="dispute_comment" data-rule-required="true" data-msg-required="This field is required">
        </div>

    </div>

    <hr>
    <div class="row justify-content-center">
        <div class="col-3">
            <button id="DisputeUpdate" type="submit" class="btn btn-primary btn-block">Update Dispute</button>
        </div>
    </div>

</form>
<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


<script type="text/javascript">
    $(document).ready(function () {
        var select = $('#update_tracking_number').selectize({
            placeholder: 'Tracking Number(s)',
            delimiter: ',',
            createOnBlur: true,
            persist: false,
            plugins: ['remove_button'],
            onDropdownOpen: function(dropdown) {
                dropdown.remove();
            },
            onType: function(str) {
                var regex = /^[0-9,]+$/;

                if (!regex.test(str)) {
                    select[0].selectize.setTextboxValue('');
                }
            },
            create: function(input) {
                if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
                    return {
                        value: input,
                        text: input
                    }
                }
                else {
                    return false;
                }
            }
        });
        var max_char = 250;
        $('#commentbox').keypress(function (e) {
            // var comment = $(this).val();
           // console.log(comment)
            if ($(this).val().length == max_char) {
                e.preventDefault();
            } else if ($(this).val().length > max_char) {
                // Maximum exceeded
                this.value = this.value.substring(0, max_char);
            }
        });
        //$('.dispute_comments_section').scrollable();

        // $('#update_tracking_number').select2({
        //     placeholder:'Enter Tracking Number',
        //     dropdownParent:$('#update_dispute_form'),
        //     tags: true,
        //     tokenSeparators: ['/',',',';'," "]
        // }).inputmask({
        //     'alias': 'integer',
        //     'allowMinus': false,
        //     'allowPlus': false,
        //     'rightAlign': false,
        //     'min': 12,
        //     'max': 12
        // });
        $('#update_city_select').select2({
            placeholder:'Select a city',
            dropdownParent:$('#update_dispute_form')
        });
        $('#update_city_select').val('{{$dispute->city->id}}').trigger('change');
        $('#update_dispute_type_select').select2({
            placeholder:'Select a Dispute type',
            dropdownParent:$('#update_dispute_form')
        });
        $('#update_dispute_type_select').val({{$dispute->dispute_types->id}}).trigger('change');
        $('#update_dispute_form').on('submit',function(e){
           e.preventDefault();
        });
        $( "#update_dispute_form" ).validate({
            //ignore: [],
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {

                $(form).find('button[type=submit]').attr('disabled', 'disabled');
                var dispute_id = $('#dispute_id').val();
                var update_city_select = $('#update_city_select').val();
                var update_dispute_type_select = $('#update_dispute_type_select').val();
                var update_tracking_number = $('#update_tracking_number').val();
                var dispute_comment = $('#commentbox').val();
                $.ajax({
                    url: '{!! route('admin.dispute.update.submit') !!}',
                    method: 'PUT',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'dispute_id': dispute_id,
                        'city_select': update_city_select,
                        'dispute_type_select':update_dispute_type_select,
                        'update_tracking_number':update_tracking_number,
                        'dispute_comment':dispute_comment
                    }
                }).done(function(data){
                    // console.log(data[0].success);
                    $('#DisputeUpdateModal').modal('hide');
                    if (data.invalid !== undefined) {

                        var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');

                        toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                    if (data.duplicate !== undefined) {
                        var message = 'Following Tracking Number(s) already exists: ' + data.duplicate.join(', ');

                        toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                    if(data.success !== undefined){

                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                    }
                    if(data.status == 0){
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                    }
                });


            }


        });
    });
</script>