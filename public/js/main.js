// let axios = require('axios');
// console.log('custom')
//
//     $("#BankInfoModal").on("show.bs.modal", function(e) {
//         var id = $(e.relatedTarget).data('target-id');
//
//         $.get( "/admin/accounts/pending/"+id+"/bank", function( data ) {
//             $(".modal-body").html(data);
//             // console.log(data);
//         });
//
//     });
//     $("#ShippingInfoModal").on("show.bs.modal", function(e) {
//         var id = $(e.relatedTarget).data('target-id');
//         // console.log(id);
//         $.get( "/admin/accounts/pending/"+id+"/shipping", function( data ) {
//             $(".modal-body").html(data);
//             // console.log(data);
//         });
//
//     });
//     $("#RatesModal").on("show.bs.modal", function(e) {
//         var id = $(e.relatedTarget).data('target-id');
//         console.log(id);
//         // $.get( "/admin/accounts/pending/"+id+"/rates", function( data ) {
//         //     $(".modal-body").html(data);
//         //     // console.log(data);
//         // });
//
//     });
//     $("#ConfirmModal").on("show.bs.modal", function(e) {
//         var id = $(e.relatedTarget).data('target-id');
//         console.log(id);
//         axios.get('/accounts/block/active', {
//             params: {
//                 id: id
//             }
//         })
//             .then(function (response) {
//                 $(".modal-body.confirmation").html(response);
//             })
//             .catch(function (error) {
//                 console.log(error);
//             });
//
//     });
function scan_sound(type) {
    if(type === 1){
        var sound = document.getElementById("audio_success");
        sound.play();
    }else{
        var sound = document.getElementById("audio_error");
        sound.play();
    }
}