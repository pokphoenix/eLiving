

$(".addcard-hover").on("click",function () {

    $(".addcard-box").show();
    $(".txt-area-card-title").val("");
    $("#pioritized_desc").val("");
    $("#prioritize_id").val("");
    $("#category").val("").focus();
})
$(document).on("click",".btn-close-card",function (event) {
    $(".addcard-box").hide();
});

$(document).on("input","textarea.txt-area-card-title",function (e) {
    $("textarea.txt-area-card-title").css({'border':'1px solid #d2d6de'});
});
$(document).on("change","#category",function (e) {
    $("#category").css({'border':'1px solid #d2d6de'});
});

$("textarea.txt-area-card-title").on("input",function () {
    $(this).next("span").remove();
})
$("#category").on("change",function () {
    $(this).next("span").remove();
})

$(document).on("click",".btn-add-card",function (event) {
    var txt = $("textarea.txt-area-card-title").val();
    var categoryId = $("#category").val();
    var prioritizeId = $("#prioritize_id").val();
    var pioritizedDesc = $("#pioritized_desc").val();
    if (categoryId==""||categoryId==" ") {
        $("#category").focus().css({'border':'1px solid #F00'}).after("<span style=\"color:#F00;\">Select category</span>");
        return false;
    }
    if (txt==""||txt==" ") {
        $("textarea.txt-area-card-title").focus().css({'border':'1px solid #F00'}).after("<span style=\"color:#F00;\">Insert Title</span>");
        return false;
    }
  


    var route = "/work/"+$("#room_id").val()+"/user?api_token="+api_token ;
    var data = { title:txt , category_id:categoryId ,pioritized :prioritizeId ,pioritized_desc:pioritizedDesc }
    ajaxPromise('POST',route,data).done(function (data) {
      // console.log(data);
      // socket.emit('task',data);
        $('.addcard-box').hide();
        $("#first_open_card").val('true');
        var boxId = data.work.id ;
        var res = data.work ;

        var card = "<div class=\"box box-solid card show-content\" "+
          " style=\"border-left: 5px solid "+res.status_color+";\">"+
                  "<div class=\"box-header\">"+
                    "<small class=\"category-label\" data-id=\""+data.task_category.id+"\" style=\"color:"+data.task_category.color+
              ";font-weight: bold;\" >&nbsp;"+
                        data.task_category.name+"</small>"+
                      "<h3 class=\"box-title\">"+res.title+"</h3>"+
                      "<br>"+
                      "<small class=\"label \"  style=\"background:"+res.status_color+" ;\" >"+
                        res.status_txt+"</small>"+
                  "</div>"+
                  "<input type=\"hidden\" class=\"box-id\" value=\""+boxId+"\" >"+
              "</div>";
    
        var newTime = true ;
        $(".append-card h4.title").each(function (i) {
          // console.log($(this).text(),(moment().format("D MMM YYYY")+" : "));
            if ($(this).text()==(moment().format("D MMM YYYY")+" : ")) {
                newTime = false;
            }
        });
        if (newTime) {
            var head = "<h4 class=\"title\">"+moment().format("D MMM YYYY")+" : </h4>";
            $(".append-card").prepend(head);
        }
        $(".append-card h4.title:first").after(card);

    
        OpenCard(txt,boxId,data);
    })
});

function clearTaskData()
{
    $("#current_card_id").val("");
    $("#card_title").val("");
    $("#insert_new_item").val("true");
    $("#modal-card-content").find('.history').html('');
    $('#modal-card-content input,#modal-card-content textarea').val('');
    $("#supplier_name").next('ul').remove();
    $("#checklist_title").val('');
    $("#task_created_by").val('');

  // $(".no-list").show();
  // if((".append-card .show-content").length>0){
  //   $(".no-list").hide();
  // }

}
function OpenCard(title,boxId,data)
{
    var modal = $('#modal-card-content') ;
    var roomId = $("#room_id").val();
    modal.find('.modal-title span').text(title);
    window.history.pushState("object or string", title , $("#baseUrl").val()+'/work/'+roomId+'/user/'+boxId);
    clearTaskData();
    $("#current_card_id").val(boxId);     ;
    createTask(data);
}
function createTaskSetInit(data)
{
    if (!data.start_task) {
        $("#modal-card-content .btn_start_task").hide();
    } else {
        $("#modal-card-content .btn_start_task").show();
    }

    if (!data.todo) {
        $("#modal-card-content .btn_todo").hide();
    } else {
        $("#modal-card-content .btn_todo").html('<i class="fa fa-signal"></i> Accept');
        $("#modal-card-content .btn_todo").show();
    }

    if (!data.accept) {
        $("#modal-card-content .btn_accept").hide();
    } else {
        $("#modal-card-content .btn_accept").show();
    }


    if ($("#first_open_card").val()=="true") {
        $("#modal-card-content .task-menu-action .btn_todo").show();
        $("#modal-card-content .task-menu-flow").hide();
        $("#modal-card-content .task-comment").hide();
    } else {
        $("#modal-card-content .task-menu-action .btn_todo").hide();
        $("#modal-card-content .task-menu-flow").show();
        $("#modal-card-content .task-comment").show();
    }

  
    if (!data.cancel_task) {
        $("#modal-card-content #btn_cancel").hide();
    } else {
        $("#modal-card-content #btn_cancel").show();
    }
  

    if (!data.in_progress) {
        $("#modal-card-content #btn_in_progress").hide();
    } else {
        $("#modal-card-content #btn_in_progress").show();
    }
    if (!data.pending) {
        $("#modal-card-content #btn_pending").hide();
    } else {
        $("#modal-card-content #btn_pending").show();
    }
    if (!data.done) {
        $("#modal-card-content #btn_done").hide();
    } else {
        $("#modal-card-content #btn_done").show();
    }

    if (!data.re_submit) {
        $("#modal-card-content #btn_re_submit").hide();
    } else {
        $("#modal-card-content #btn_re_submit").show();
    }

    if (!data.duedate) {
        $("#modal-card-content #task_due_date").hide();
    } else {
        $("#modal-card-content #task_due_date").show();
    }

    if (data.viewer) {
        $("#btn-task-viewer").find('i').addClass('fa-eye-slash').removeClass('fa-eye');
    } else {
        $("#btn-task-viewer").find('i').addClass('fa-eye').removeClass('fa-eye-slash');
    }

    $(".history").hide();
    if (data.history) {
        $(".history").show();
    }

    $(".title-flow").hide();
    if (data.menu_flow) {
        $(".title-flow").show();
    }
    if ((!data.todo||!data.accept)&&!data.cancel_task&&!data.in_progress&&!data.pending&&!data.done&&!data.re_submit) {
        $(".task-menu-flow .title-flow").hide();
    }


    $(".task-menu-add").hide();
    if (data.menu_add) {
        $(".task-menu-add").show();
    }

    $(".task-menu-action").hide();
    if (data.menu_action) {
        $(".task-menu-action").show();
    }

    $(".task-menu-delete").hide();
    if (data.menu_delete) {
        $(".task-menu-delete").show();
    }

 
    $("#task_attach_ment").hide();

    if (data.attachment||data.btn_attachment) {
        $("#task_attach_ment").show();
    }

  


    $(".description").show();
    $("#btn_edit_description_body").hide();

    $('#description-body').hide();
    $('#description-body-readonly').show();
    $("#task-edit-description-body").hide();
    if (data.edit_description) {
        $(".description").hide();
        $("#btn_edit_description_body").show();
        $("#task-edit-description-body").show();

        $('#description-body').show();
        $('#description-body-readonly').hide();
    }

    $('#edit-title').hide();
    if (data.edit_description) {
        $('#edit-title').show();
    }

    is_member = data.is_member ;
  

}


$(function () {
    $(".btn_accept").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var status = 3 ;
        ajaxUpdateStatus(cardId,status).done(function (data) {
             createTask(data);
        }).fail(function (txt) {
            var error = JSON.stringify(txt);
                       swal(
                           'Error...',
                           error,
                           'error'
                       )
        });
    });


});


function hideInputItem()
{
    if ($("#table-quatation tbody tr.active-edit").length>0) {
        var ele = $("#table-quatation tbody tr.active-edit");
        $(ele).find('td:gt(1)').each(function () {
            var val = $(this).find('input').val() ;
            $(this).find('input').attr("type","hidden");
            $(this).find('span').text(val).show();
        });

        ele.removeClass('active-edit');
    }
}


