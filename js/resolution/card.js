
$(".addcard-hover").on("click",function () {
    var add_card = "<div class=\"addcard-box\"><div class=\"box box-solid\" >"+
                "<div class=\"box-header\">"+
                "<textarea class=\"txt-area-card-title form-control\" rows=\"2\" style=\"border: 0;\">"+
                "</textarea>"+"</div></div>"+
                "<button class=\"btn bg-olive margin btn-add-card\" >"+(($("#app_local").val()=='th') ? ' เพิ่ม' : ' Add ' )+"</button>"+
                "<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
    var rows = $(this).parent(".box-solid") ;
    rows.find(".append-card").append(add_card);
    rows.find(".box-footer").hide();
    
    
    var parentHeight = rows.find(".append-card")[0].scrollHeight -  $(".addcard-box")[0].scrollHeight ;
    
    rows.find(".box-body").animate({
        scrollTop: parentHeight
      },'fast');
    $(".txt-area-card-title").focus();
})



$(document).on("click",".btn-close-card",function (event) {
    // console.log("click");
    var rows = $(this).closest(".box-parent") ;
    rows.find(".append-card").find(".addcard-box").remove();
    rows.find(".box-footer").show();
});

$(document).on("click",".btn-add-card",function (event) {
    var rows = $(this).closest(".box-parent") ;
    var txt = $("textarea.txt-area-card-title").val();
    var route = '/resolution?api_token='+api_token ;
    var data = { 'title': txt}
    ajaxPromise('POST',route,data).done(function (data) {
        var boxId = (typeof data.resolution_id =="undefined") ? data.task_id : data.resolution_id ;

        var card = "<div class=\"box box-solid card show-content\" data-toggle=\"modal\" data-target=\"#modal-card-content\">"+
                "<div class=\"box-header\">"+
                "<h3 class=\"box-title\">"+txt+
                "</h3>"+
                "<input type=\"hidden\" class=\"box-id\" value=\""+boxId+"\" >"+
                "</div>"+
                "<div class=\"box-body\">"+
                "<span class=\"vote-label\"> <i class=\"fa fa-gavel\"></i> (0 / 3) </span>"+
                "</div>"+
                "</div>";
            rows.find(".append-card").find(".addcard-box").remove();
            rows.find(".append-card").append(card);
            rows.find(".box-footer").show();

            openCard(txt,boxId);

            

        // location.reload();
    }).fail(function (txt) {
        var error = JSON.stringify(txt);
                       swal(
                           'Error...',
                           error,
                           'error'
                       )
    });
});


$("#edit-title").on("click",function () {
    var title =  $(this).parent().find('span').text();
    $("#card_title").val(title);
    $(".show-title").hide();
    $(".show-edit-title").show();
});



$("#save-edit-title").on("click",function () {
    var cardId = $("#current_card_id").val();
    var title =  $("#card_title").val();
    var route = "/resolution/"+cardId+"?api_token="+api_token;
    var data = { title:title,'_method':'PUT' };
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('resolution',data);
        $(".show-title").find('span').text(title);
        $(".show-edit-title").hide();
        $(".show-title").show();
    }).fail(function (txt) {
        var error = JSON.stringify(txt);
           swal(
               'Error...',
               error,
               'error'
           )
    });
});
