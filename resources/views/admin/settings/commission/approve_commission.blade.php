@extends('admin.layout.master')

@section('title', 'Approve Commission')

@section('content')
    <h1>Approve Commission</h1>
    @foreach($users as $user_id)
    @csrf
    <input type="hidden" value="{{$user_id->id}}" id="user_id"/>
       <div class="row approve_div">
            <div class="col-12">
                <div class="card">
                <div class="row shipperhead mt-2">
                    <h5>{{$user_id->name}}({{$user_id->id}})</h5>
                    </div>
                    <div class="col-12">
                                    <table class="table table-bordered datatable" id="datatable_{{$user_id->id}}" style="z-index: 3;">
                                        <thead>
                                        <tr role="row" class="bg-primary white">
                                            <th class="border-primary border-darken-1">S. No.</th>
                                            <th class="border-primary border-darken-1">User Name</th>
                                            <th class="border-primary border-darken-1">Tier</th>
                                            <th class="border-primary border-darken-1">Commission Percentage</th>
                                            <th class="border-primary border-darken-1"></th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <input type="hidden" value="0" name="total_commission" id="total_commission">
                                        <tr><th colspan="3" style="text-align:right" rowspan="1">Total Commission:</th><th rowspan="1" colspan="2"><span id="total_commission_value">0</span>%</th></tr>
                                        <tr><td colspan="5" style="text-align:center">
                                        <input type="radio" checked id="pending" name="gender" value="pending">
                                        <label for="pending">Pending</label><br>
                                        <input type="radio" id="approve" name="gender" value="approve">
                                        <label for="approve">Approve</label><br>
                                        <input type="radio" id="reject" name="gender" value="reject">
                                        <label for="reject">Reject</label>
                                        </td></tr>
                                           
                                        </tfoot>
                                        
                                    </table>
                       </div>
                </div>
            </div>
        </div> 
        @endforeach

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style type="text/css">
        .hide{
            display:none;
        }
        .shipperhead{
            padding-left:30px;
        }
    </style>


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">

@foreach($users as $user_id)
var table = $('#datatable_'.user_id).DataTable({
            dom: 'ltipr',
            paging: false,
            ordering:false,
            sorting:false,
            bInfo:false,
            columns: [
                {
                    orderable: false,
                    searchable: false,
                    name: 'serial_number',
                    class: 'align-middle serial_number',
                    targets: 1,
                    render: function (data, type, row) {
                        return '';
                    }
                },
                { name: 'user_name', class: 'align-middle user_name'},
                { name: 'tier', class: 'align-middle tier'},
                { name: 'commission_percentage', class: 'align-middle commission_percentage'},
                { name: 'action', class: 'align-middle action'}

            ],
            rowCallback: function (row, data, index) {
                var info = table.page.info();

                $('td:eq(0)', row).html(index + 1 + info.page * info.length);

            },
        });

        @endforeach

        function add_commission_row(tier_id, tier_name, tier_type, user_id, user_name, commission){
            var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger remove"><i class="la la-close"></i></a>';
            var tier = '<div><input type="hidden" name="tier_id['+ row +']"  value="'+ tier_id +'">'+ tier_name +'</div>';
            if(tier_type == 1){
                var name = '<div><input type="hidden" name="user_id['+ row +']"  value="'+ user_id +'">'+ user_name +'</div>';
            }else{
                var name = '<div><input type="hidden" name="user_id['+ row +']"  value="'+ user_name +'">'+ user_name +'</div>';
            }
            var commission_percentage = '<div><input type="hidden" name="commission_percentage['+ row +']"  value="'+ commission +'">'+ commission +'%</div>';
            table.row.add([row,name,tier,commission_percentage,remove]).node().id = row;
            table.draw(false);
            if(tier_type == 1){
                selected_users.push(user_id.toString());
            }
            $('#commission_add_button').attr('disabled', false);
            $('#total_commission_value').html(selected_commission);
            $('#total_commission').val(selected_commission);
            row++;
        }

        var existing_commissions = @json($existing_commission_array);
        existing_commissions.forEach(function(existing_commission){
            selected_commission = roundToTwo(selected_commission + existing_commission['commission']);
            add_commission_row(existing_commission['tier_id'], existing_commission['tier_name'], existing_commission['tier_type_id'], existing_commission['user_id'], existing_commission['user_name'], existing_commission['commission']);
        });

        

</script>
@endsection            