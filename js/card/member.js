$(document).on("input","#search_member",function (e) {
    ajaxSearchMember($(this)).then(function ( res ) {
        // console.log("search_member",res);
        var tool = { thumbnail:true } ;
        autoData(res,tool).done(function (data) {
            $("#modal_task_member #member_list").html(data);
        }).fail(function () {
            $("#modal_task_member #member_list").html('');
        });
    }).fail(function () {
        $("#modal_task_member #member_list").html('');
    });
});


$(document).on("click",".my-autocomplete-li",function (e) {
    var cardId = $("#current_card_id").val() ;
    var id = $.trim($(this).find('.search-id').val()) ;
    var route = "/task/"+cardId+"/member/"+id+"?api_token="+api_token
    ajaxPromise('POST',route,null).done(function (data) {
        socket.emit('task',data);
        createTaskMember(data.task_members,data.task.id);
        createTaskTableHistory(data.task_historys);
        createTaskCard(data.task);
        createTaskSetInit(data.status);
        $("#modal_task_member").modal('toggle');
    });
  // $(this).parent().prev('input').val($(this).find('h5').text()) ;
  // $(this).parent().remove();
});


$(document).on("click",".assign-member-close",function (e) {
    $(this).closest('.col-sm-4').remove();
});
$(document).on("click",".btn_task_member,.btn-task-member-add",function (e) {
    $("#modal_task_member #search_member").val('');
    ajaxSearchMember($("#search_member")).then(function ( res ) {
        var tool = { thumbnail:true } ;
        autoData(res,tool).done(function (data) {
            $("#modal_task_member #member_list").html(data);
        }).fail(function () {
            $("#modal_task_member #member_list").html('');
        })

    }).fail(function () {
        $("#modal_task_member #member_list").html('');
    })
    $("#modal_task_member").modal('show');
    
});
$(document).on("click",".assign-member-remove",function (e) {
    var id = $(this).closest('.box-solid').find('.assign-member-remove-id').val();
    var cardId = $("#current_card_id").val() ;
    var route = "/task/"+cardId+"/member/"+id+"?api_token="+api_token
    ajaxPromise('POST',route,null).done(function (data) {
        socket.emit('task',data);
        createTaskMember(data.task_members,data.task.id);
        createTaskTableHistory(data.task_historys);
        createTaskCard(data.task);
        createTaskSetInit(data.status);
    });
    $(this).closest('.col-sm-4').remove();
});

$(document).on("click",".assign-member-img",function (e) {
    var cardId = $("#current_card_id").val() ;
    var id = $.trim($(this).closest('.assign-member').find('.assign-member-id').val()) ;
    var img = $(this).attr('src');
    var name = $(this).attr('title');

    var html = "<div class=\"row\">"+
                "<div class=\"col-sm-4\">"+
                    "<div class=\"box box-solid\">"+
                        "<div class=\"box-body\">"+
                            "<button type=\"button\" class=\"close assign-member-close\" data-dismiss=\"alert\" "+
                            "aria-hidden=\"true\">×</button>"+
                            "<div class=\"pull-left\" >"+
                                "<img src=\" "+img+" \" class=\"img-circle\" height=\"50\" alt=\"User Image\">"+
                            "</div>"+
                            "<h5>&nbsp;"+name+"</h5>"+
                            "<input type=\"hidden\" class=\"assign-member-remove-id\" value=\""+id+"\">"+
                            "<BR><a href=\"javascript:void(0)\" class=\"assign-member-remove\" >"+(($("#app_local").val()=='th') ? 'นำออกจากงานนี้' : 'remove from this card' )+"</a>"+
                        "</div>"+
                    "</div>"+
                "</div>"+
            "</div>";

    $(this).closest('.form-group').find('.member-detail').html(html);
  // console.log('member',id,img,name);
  // console.log('html',html);
});

function ajaxSearchMember(ele)
{
    var data =  ele.val();
    var url = $("#apiUrl").val() ;
    var dfd = $.Deferred();
    var cardId = $("#current_card_id").val();
    $.ajax({
        url:  url+'/search/member/task/'+cardId+'?api_token='+api_token  ,
        type: 'POST',
        dataType: 'json',
        data: {name:data} ,
    })
    .done(function (res) {
        data = { ele : ele,data : res } ;
        dfd.resolve(data);
    })
    .fail(function () {
        dfd.reject("error");
    })
    return dfd.promise();
}

function createTaskMember(data,cardId)
{
    // console.log('[createTaskMember]',cardId);
    if (data.length>0) {
        var table = '';
        for (var i = 0; i<data.length; i++) {
            table += "<span class=\"assign-member\"><img src=\" "+data[i].img+" \" data-toggle=\"tooltip\" title=\""+
                data[i].text+"\" class=\"img-circle assign-member-img\" height=\"25\" alt=\"User Image\">"+
                "<input type=\"hidden\" class=\"assign-member-id\" value=\""+data[i].id+"\">"+
                "</span>";
        }
       
        $("#modal-card-content #task_member").find('div.form-group div.list').html(table);
        $("#modal-card-content #task_member").show();
    } else {
        $("#modal-card-content #task_member").find('div.form-group div.list').html('');
        $("#modal-card-content #task_member").hide();
    }
}
