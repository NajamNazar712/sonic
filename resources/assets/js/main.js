$(document).ready(function () {
    //let axios = require('axios');
    $("#BankInfoModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('target-id');

        $.get( "/admin/accounts/pending/"+id+"/bank", function( data ) {
            $(".modal-body").html(data);
            // console.log(data);
        });

    });

    $("#ShippingInfoModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('target-id');
        // console.log(id);
        $.get( "/admin/accounts/pending/"+id+"/shipping", function( data ) {
            $(".modal-body").html(data);
            // console.log(data);
        });

    });
    $("#RatesModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('target-id');
        console.log(id);
        // $.get( "/admin/accounts/pending/"+id+"/rates", function( data ) {
        //     $(".modal-body").html(data);
        //     // console.log(data);
        // });

    });
    $("#ConfirmModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('target-id');
        var rel = $(e.relatedTarget).attr('rel');

        $('#shid').val(id);
        $('#shstatus').val(rel);
        // axios.get('/accounts/block/active', {
        //     params: {
        //         id: id
        //     }
        // })
        //     .then(function (response) {
        //         $(".modal-body.confirmation").html(response);
        //     })
        //     .catch(function (error) {
        //         console.log(error);
        //     });

    });



    $('body').on('click','#confirmAction',function () {
       var uid = $('#shid').val();
       var status = $('#shstatus').val();
       axios.post('/account/status',{
           params:{
               uid: uid,
               status: status
           }
       })
           .then(function (response) {
                console.log(response);
           })
           .catch(function(error){

           });
    });
//     $(".touchspin-color").TouchSpin();


});
