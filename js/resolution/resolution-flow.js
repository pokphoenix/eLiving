$(function () {
    $("#start_vote").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var status = 2 ;
        ajaxUpdateStatus(cardId,status);
    });
    $("#btn_resubmit").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var status = 1 ;
        ajaxUpdateStatus(cardId,status);
    });

    $("#btn_done").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var status = 7 ;
        ajaxUpdateStatus(cardId,status);
    });

    $("#close-edit-title").on("click",function () {
        $(".show-edit-title").hide();
        $(".show-title").show();
    });
        
    $("#no_vote").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var route = "/resolution/"+cardId+"/novote?api_token="+api_token ;
        var data = "" ;
        ajaxPromise('GET',route,data).done(function (data) {
            socket.emit('resolution',data);
            createCard(data);
        })
    });
     
    $("#table-resolution tbody").sortable({
        placeholder: "list-group-item-info",
        stop: function (event, ui) {
            $("#table-resolution tbody tr td:nth-child(2)").each(function (i) {
                var j = ++i;
                $(this).text(j);
            });
        }
    });

});



