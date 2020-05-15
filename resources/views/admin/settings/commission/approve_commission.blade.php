@extends('admin.layout.master')

@section('title', 'Approve Commission')

@section('content')
    <h1>Approve Commission</h1>

    <form id="approveForms" class="card-body card-dashboard" action="{{route('admin.settings.commission.approve_commission.submit')}}" method="post" novalidate="novalidate">
    @csrf

    @foreach($users as $user_id)
    @if(array_key_exists($user_id->id, $existing_commission_array))
   
    <input type="hidden" name="user_ids" value="{{$ids}}" id="user_ids"/>
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
                                        <!-- <input type="hidden" value="0" name="total_commission_{{$user_id->id}}" id="total_commission_{{$user_id->id}}"> -->
                                        <tr><th colspan="3" style="text-align:right" rowspan="1">Total Commission:</th><th rowspan="1" colspan="2"><span id="total_commission_value_{{$user_id->id}}">0</span>%</th></tr>
                                        <tr><td colspan="5" style="text-align:center">
                                        <input type="radio" checked id="pending_{{$user_id->id}}" name="rates_status[{{$user_id->id}}]" value="1">
                                        <label for="pending">Pending</label><br>
                                        <input type="radio" id="approve_{{$user_id->id}}" name="rates_status[{{$user_id->id}}]" value="2">
                                        <label for="approve">Approve</label><br>
                                        <input type="radio" id="reject_{{$user_id->id}}" name="rates_status[{{$user_id->id}}]" value="3">
                                        <label for="reject">Reject</label>
                                        </td></tr>
                                           
                                        </tfoot>
                                        
                                    </table>
                       </div>
                </div>
            </div>
        </div> 
        @endif
        @endforeach
                    <div class="text-center mt-2">
                        <div class="form-group">
                            <button id="addRatesSubmit" type="submit" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Submit</button>
                       </div>  
               </div>
        </form>
        

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
var table_{{$user_id->id}} = $('#datatable_{{$user_id->id}}').DataTable({
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
                var info = table_{{$user_id->id}}.page.info();

                $('td:eq(0)', row).html(index + 1 + info.page * info.length);

            },
        });
        var selected_commision_{{$user_id->id}} = 0;
        var existing_commissions = @json($existing_commission_array[$user_id->id]);
        var row = 1;
        var commision_{{$user_id->id}} =[];
        existing_commissions.forEach(function(existing_commission){
            selected_commision_{{$user_id->id}} = roundToTwo(selected_commision_{{$user_id->id}} + existing_commission['commission']);
            add_commission_row_{{$user_id->id}}(existing_commission['sales_commission_id'], existing_commission['id'], existing_commission['tier_id'], existing_commission['tier_name'], existing_commission['tier_type_id'], existing_commission['user_id'], existing_commission['user_name'], existing_commission['commission'],existing_commission['sales_status'], row);
            commision_{{$user_id->id}}[row] = existing_commission['commission'];
            row++;
        });
        $('#datatable_{{$user_id->id}} tbody').on('click', 'tr td.action a.remove', function() {
            var id = $(this).parents('tr').attr('id');

          //  var commission =  parseFloat($('input[name="commission_percentage['+ id +']"]').val());
            //commission_max = roundToTwo(commission_max + selected_commision_{{$user_id->id}});
            console.log(commision_{{$user_id->id}});
            console.log(id);
            selected_commision_{{$user_id->id}} = roundToTwo(selected_commision_{{$user_id->id}} - commision_{{$user_id->id}}[id]);
            $('#total_commission_value_{{$user_id->id}}').html(selected_commision_{{$user_id->id}});
            // $('#total_commission_{{$user_id->id}}').val(selected_commision_{{$user_id->id}});
            table_{{$user_id->id}}.row( $(this).parents('tr') ).remove().draw();
        });
        var row_{{$user_id->id}} = 1;
            function add_commission_row_{{$user_id->id}}(sales_commission_id, id, tier_id, tier_name, tier_type, user_id, user_name, commission, sales_status, row){
                var table = table_{{$user_id->id}};
                if(sales_status == 0){
                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger remove"><i class="la la-close"></i></a>';
                }
                else{
                    var remove = '';
                }
                var tier = '<div><input type="hidden" name="sale_commission['+ id +']"  value="'+ sales_commission_id +'">'+ tier_name +'</div>';

                var commission_percentage = '<div><input type="hidden" name="commission_percentage[{{$user_id->id}}]"  value="'+ commission +'">'+ commission +'%</div>';
                table.row.add([row_{{$user_id->id}},user_name,tier,commission+'%',remove]).node().id = row;
                table.draw(false);
                // if(tier_type == 1){
                //     selected_users.push(user_id.toString());
                // }
                $('#total_commission_value_{{$user_id->id}}').html(selected_commision_{{$user_id->id}});
                // $('#total_commission_{{$user_id->id}}').val(selected_commision_{{$user_id->id}});
                row_{{$user_id->id}}++;
        }
        @endforeach

        function roundToTwo(num) {
            return +(Math.round(num + "e+2")  + "e-2");
        }

// console.log();

       

        

</script>
@endsection            