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
        message: '<div class="ft-loader icon-spin font-medium-2"></div>',
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
// var data_table_loader = '<div class="blockUI blockOverlay" style="z-index: 1000; border: none; margin: 0px; padding: 0px; width: 5%; height: 5%; background-color: transparent; opacity: 0; cursor: wait; position: fixed;"></div><div class="blockUI blockMsg blockPage" style="z-index: 1011; position: fixed; padding: 10px; margin: 0px; width: 30%; top: 40%; left: 35%; height:10%; text-align: center; color: rgb(0, 0, 0); border: 0px; background-color: red; cursor: wait;"><div class="ft-refresh-cw icon-spin font-medium-2" style=""></div></div>';
var data_table_loader = '<div class="blockUI blockOverlay" style="z-index: 1000; border: none; margin: 0px; padding: 0px; width: 5%; height: 5%; background-color: transparent; opacity: 0; cursor: wait; position: fixed;"></div><div class="blockUI blockMsg blockPage" style="z-index: 1011; position: fixed; padding: 10px; margin: 0px; width: 20%; top: 40%; left: 40%; text-align: center; border-radius: 5px; background-color: white; cursor: wait; -webkit-box-shadow: 0px 0px 10px -5px rgba(0,0,0,0.75); -moz-box-shadow: 0px 0px 10px -5px rgba(0,0,0,0.75); box-shadow: 0px 0px 10px -5px rgba(0,0,0,0.75); "><div class="ft-loader icon-spin font-medium-2 align-middle"></div><div class="d-inline-block ml-1 align-middle">Data Loading</div></div>';


