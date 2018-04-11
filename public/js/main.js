$(document).ready(function () {
    $("#BankInfoModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('target-id');

        $.get( "/admin/accounts/pending/"+id+"/bank", function( data ) {
            $(".modal-body").html(data);
            // console.log(data);
        });

    });
});