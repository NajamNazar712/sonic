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
// Block page
function blockPagePermanently() {

    $.blockUI({
        message: '<div class="ft-refresh-cw icon-spin font-medium-2"></div>',
        timeout: 0, //unblock after 2 seconds
        overlayCSS: {
            backgroundColor: '#FFF',
            opacity: 0.8,
            cursor: 'wait'
        },
        css: {
            border: 0,
            padding: 0,
            backgroundColor: 'transparent'
        }
    });
}
function UnblockPagePermanently() {

    $.unblockUI({ fadeOut: 200 });
}
var data_table_loader = '<div class="blockUI blockOverlay" style="z-index: 1000; border: none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background-color: rgb(255, 255, 255); opacity: 0.8; cursor: wait; position: fixed;"></div><div class="blockUI blockMsg blockPage" style="z-index: 1011; position: fixed; padding: 0px; margin: 0px; width: 30%; top: 40%; left: 35%; text-align: center; color: rgb(0, 0, 0); border: 0px; background-color: transparent; cursor: wait;"><div class="ft-refresh-cw icon-spin font-medium-2"></div></div>';
