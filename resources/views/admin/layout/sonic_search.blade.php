<!-- Global Search Sonic (Admin Side) -->
<div class="modal fade text-left" id="GlobalSearchSonicModal" tabindex="-1" role="dialog" aria-labelledby="GlobalSearchSonicModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <select name="search_admin_sonic" id="search_admin_sonic" class="form-control select2">
                            @foreach($search_sonic as $search)
                                <option value="{{ $search['url'] }}">{{ $search['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>


<script type="text/javascript">

        $('#search_admin_sonic').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder:'Search Sonic',
            dropdownParent:$('#GlobalSearchSonicModal')
        }).bind('select2:select',function () {
            var url = $(this).val();
            window.location.href = url;
        });


</script>
