
var baseSystem = "/suggest/system/" ;


$("#task-comment-btn").on("click",function () {
    var boxId = $("#current_card_id").val() ;
    var description = $("#task-comment-description").val();
    var route = baseSystem+boxId+"/comment?api_token="+api_token ;
    var data = { description:description } ;
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('suggest',data);
        $("#task-comment-description").val("");
    });

});






// socket.on('channel_chat', function(r){
//   console.log('[channel_chat][show] ',r);
//   if (typeof r==='string'){
//     r = JSON.parse(r);
//   }
//   var channelId = $("#channel_id").val();

//   $(".content-wrapper").find('.chat-box-id').each(function(){
//       console.log('.chat-box-id',$(this).val(),channelId);
//       if(parseInt($(this).val())==channelId){
//           $(this).closest('.chat-box-info').remove() ;
//       }
//   }) ;

  
//   console.log(channelId,r.channel.id,r.chat);
//   if(channelId!=r.channel.id || r.init ==1 ){
//     return false;
//   }

//   setChat(r.chat);
//   $("#message_text").val('');

//   setTimeout(function(){
//    $("#direct-chat").hide();
//   }, 200);
  
// })


$(document).on("click",".btn-comment-delete",function (event) {

    swal({
        title: 'Are you sure?',
        text: "คุณต้องการลบคอมเมนท์นี้ทิ้งใช่หรือไม่!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
        buttonsStyling: false,
        reverseButtons: true
    }).then((result) => {
        if (result.value) {
            var boxId = $("#current_card_id").val() ;
            var parent = $(this).closest('.item') ;
            var comment_id = parent.find('.comment-id').val();

            var route = baseSystem+boxId+"/comment/"+comment_id+"?api_token="+api_token ;

            ajaxPromise('POST',route,{'_method':'DELETE'}).done(function (data) {
                socket.emit('suggest',data);
                parent.remove();
            })
        } else if (result.dismiss === 'cancel') {
        }
    })


    
});
$(document).on("click",".btn-comment-delete-real",function (event) {

    swal({
        title: 'Are you sure?',
        text: "คุณต้องการลบคอมเมนท์นี้ทิ้งใช่หรือไม่!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-default',
        buttonsStyling: false,
        reverseButtons: true
    }).then((result) => {
        if (result.value) {
            var boxId = $("#current_card_id").val() ;
            var parent = $(this).closest('.item') ;
            var comment_id = parent.find('.comment-id').val();
            var box = $(this).closest('.box') ;

            var route = baseSystem+boxId+"/comment/"+comment_id+"?api_token="+api_token ;
    
            ajaxPromise('POST',route,{'_method':'DELETE'}).done(function (data) {
                socket.emit('suggest',data);
                box.remove();
            })
        } else if (result.dismiss === 'cancel') {
        }
    })


    
});
$(document).on("click",".btn-edit-message-save",function (event) {
    var boxId = $("#current_card_id").val() ;
    var parent = $(this).closest('.item') ;
    var comment_id = parent.find('.comment-id').val();
    var text = parent.find('.edit-comment').val();

    var route = baseSystem+boxId+"/comment/"+comment_id+"?api_token="+api_token ;
    var data = { description:text,'_method':'PUT' } ;
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('suggest',data);
        parent.find('.edit-message').html(text).removeClass('edit-message').addClass('message');
        parent.find('.btn-edit-message-save').remove();
    });
});

$(document).on("click",".history .message.can-edit",function (event) {
    var text = $(this).text() ;
    var message = "<textarea class=\"form-control edit-comment\" >"+text+"</textarea>" ;
    // console.log($(this).text()) ;
    // console.log($(this).closest('.item').find('.header') );
    $(this).closest('.item').find('.header').append(" <button class=\"btn btn-xs btn-info btn-flat btn-edit-message-save\" ><i class=\"fa fa-save\"></i></button>");
    $(this).html(message);
    $(this).removeClass('message').addClass('edit-message');

});

function createTaskComment(data)
{
    console.log(data);
    if (data.length>0) {
        html = "" ;

        for (var i = 0; i< data.length; i++) {
            html+= "<div class=\"box\">"+
                        "<div class=\"box-body chat\">"+
                            "<div class=\"item\">"+
                                "<img src=\""+data[i].img+"\" alt=\"user image\" >"+
                                "<input type=\"hidden\" class=\"comment-id\" value=\""+data[i].comment_id+"\" >"+
                                "<div class=\"message\">"+
                                  "<a href=\"javascript:void(0)\" class=\"name\">"+
                                    "<small class=\"text-muted pull-right\">"+
            "<i class=\"fa fa-clock-o\"></i> "+moment.unix(data[i].ts_created_at).format("D/MM/YYYY HH:mm") ;
            if (data[i].user_id==user_id) {
                    html +=  "<button type=\"button\" class=\"btn btn-default btn-comment-delete-real btn-xs\" title=\"Remove\"><i class=\"fa fa-close\"></i></button>";
            }
            html +=  "</small>"+
                                    data[i].user_name+
                                  "</a>"+
                                  data[i].comment_description+
                                "</div>"+
                              "</div>"+
                        "</div>"+
                    "</div>";
        }
        $(".task-comment .comment-list").html(html);
    } else {
        $(".task-comment .comment-list").html('');
    }
}

