var is_member =false;

var baseSystem = "/officer/work/" ;


$('#task-edit-description-btn').click(function () {
    $('#task-edit-description-body').show();
});
$('#task-edit-description-clost-btn').click(function () {
    $('#task-description').val('');
    $('#task-edit-description-body').hide();
    $('#task-edit-description-btn').show();
});
$('#task-edit-description-add-btn').click(function () {
    var text = $('#task-description').val();
    var boxId = $('#current_card_id').val();

    var route = baseSystem+boxId+"?api_token="+api_token ;
    var data = {description:text,'_method':'PUT'} ;
    ajaxPromise('POST',route,data).done(function (data) {
         socket.emit('work',data);
        $('#description-body').text(text);
        $('.task-edit-description').hide();
        $('#task-edit-description-btn').hide();
        $('#description').show();
    })

    
});

$('#description-body,#btn_edit_description_body').click(function () {
    var ele = $('#description-body') ;
// console.log(ele.outerHeight(),ele.height(),ele.innerHeight());

    var height = $('#description-body').outerHeight()+40;
    var width = $('#description-body').outerWidth();
    var text = $('#description-body').text();
    $('textarea#description-edit-body-text').val(text);
    $('textarea#description-edit-body-text').innerHeight(height);
    $('textarea#description-edit-body-text').innerWidth(width);
    
    $('#description').hide();
    $('#description-edit').show();
});

$('#description-edit-body-close-btn').click(function () {
    // var text = $('#description-edit-body-text').val();
    // $('#description-body').text(text);
    $('#description-edit').hide();
    $('#description').show();
});

$('#description-edit-body-add-btn').click(function () {
    var text = $('#description-edit-body-text').val();
    
    var boxId = $('#current_card_id').val();
    // console.log(text);
    var route = baseSystem+boxId+"?api_token="+api_token ;
    var data = {description:text,'_method':'PUT'} ;
    ajaxPromise('POST',route,data).done(function (data) {
         socket.emit('work',data);
        $('#description-body').text(text);
        $('#description-edit').hide();
        $('#description').show();
    })

});



$(document).on("click","#task-edit-description",function (event) {
    var add_description = "<div class=\"addcard-box\"><div class=\"box box-solid\">"+
                "<div class=\"box-header\">"+
                "<textarea class=\"txt-area-card-title\" rows=\"2\" style=\"border: 0;\">"+
                "</textarea>"+"</div></div>"+
                "<button class=\"btn bg-olive margin btn-add-card\" >"+(($("#app_local").val()=='th') ? ' เพิ่ม' : ' Add ' )+"</button>"+
                "<button class=\"btn btn-close-card\" ><i class=\"fa fa-close\"></i></button></div>";
    var rows = $(this).parent(".box-solid") ;
    rows.find(".append-card").append(add_card);
    rows.find(".box-footer").hide();
});


function createTask(data)
{
    var cardId = data.work.id;
    // createTaskTableHistory(data.task_historys);
    createTaskCard(data.work);
    createTaskSetInit(data.status);
    createTaskAttachment(data.work_attachs);
    createTaskCategory(data.task_category,cardId);
    // createTaskChecklist(data.task_checklists);
    // createTaskMember(data.task_members,cardId);
    createTaskComment(data.work_comments);
    $("#modal-card-content").modal('show');
}

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
}

function appendCard(rows,boxId,title,color,categoryName)
{
    var canAppendCard =true ;
    $('#card_new .box-id').each(function (index, el) {
        if ($(this).val()==boxId) {
            canAppendCard = false;
        }
    });
    if (canAppendCard&&rows.length > 0) {
         var card = "<div class=\"box box-solid card show-content\" data-toggle=\"modal\" data-target=\"#modal-card-content\">"+
        "<div class=\"box-header\">"+
        "<h3 class=\"box-title\">"+title+
        "</h3>"+
        "<input type=\"hidden\" class=\"box-id\" value=\""+boxId+"\" >"+
        "</div>"+
        "<div class=\"box-body\">"+
         "<small class=\"label category-label\" style=\"color:#FFF; background:"+color+";\" >"+
                    categoryName+"</small>"+
        "</div></div>";
        // console.log('[appendCard]',card);
        $(rows).find(".append-card").append(card);
    }
}


function updateListCard(data)
{

    var cardId = data.work_id ;
    // console.log("updateListCard ",cardId,data);
    if (data.work!=null) {
        var task = data.work ;
        //---title
        $('.box-id').each(function (index, el) {
            if ($(this).val()==task.id) {
                $(this).closest('.show-content').find('.box-title').text(task.title);
            }
        });
        //---duedate
        if (task.due_dated_at!=null) {
            $('.box-id').each(function (index, el) {
                if ($(this).val()==cardId) {
                    var ele = $(this).closest('.show-content').find('.due-date-label');
                    if (ele.length > 0 ) {
                        ele.html('<i class="fa fa-clock-o"></i> '+moment.utc(task.due_dated_at).format("D/MM/YYYY HH:mm"));
                        if (task.due_dated_complete==1) {
                            ele.find('i').addClass('fa-check-square-o').removeClass('fa-clock-o');
                            ele.addClass('label-success').removeClass('label-danger');
                        } else {
                            ele.find('i').addClass('fa-clock-o').removeClass('fa-check-square-o');
                            ele.addClass('label-danger').removeClass('label-success');
                        }
                    } else {
                        var due =  "<small class=\"due-date-label label label-success\">"+
                         "<i class=\"fa fa-clock-o\"></i> "+moment.utc(task.due_dated_at).format("D/MM/YYYY HH:mm")+
                         "</small>";
                         $(this).closest('.show-content').find('.box-body').append(due);
                    }
                }
            });
        }
        

        //--- Done at
        if (task.doned_at!=null) {
            $('.box-id').each(function (index, el) {
                if ($(this).val()==cardId) {
                    var ele = $(this).closest('.show-content').find('.done-at-label');
                    var html = 'Done at '+moment.utc(task.doned_at).format('DD MMM YYYY');
                    if (ele.length > 0 ) {
                        ele.html(html);
                    } else {
                        var due =  "<BR><small class=\"label label-success done-at-label\">"+
                         html+"</small>";
                         $(this).closest('.show-content').find('.box-body').append(due);
                    }
                }
            });
        } else {
            $('.box-id').each(function (index, el) {
                if ($(this).val()==cardId) {
                    var ele = $(this).closest('.show-content').find('.done-at-label').remove();
                }
            });
        }


        if (typeof roomId == "undefined") {
            cardStatusMove(task);
        }
    }

    //---category
    if (data.work_category!=null) {
        var taskCategory = data.work_category ;
        if (taskCategory.id != null) {
            $('.box-id').each(function (index, el) {
                if ($(this).val()==cardId) {
                    var ele = $(this).closest('.show-content').find('.box-body');
                    if (ele.length >0) {
                         // console.log("back-end");
                        var html = "<small class=\"label category-label\" data-id=\""+taskCategory.id+"\" style=\"color:#FFF; background:"+taskCategory.color+";\" >"+
                        (($("#app_local").val()=='th') ? taskCategory.name_th : taskCategory.name_en )+
                        "</small>";
                         // console.log('show-content box-id',$(this).val());
                         // console.log('find category label',ele.find('.category-label').length);
                        if (ele.find('.category-label').length <= 0 ) {
                            // console.log(ele,html);
                            ele.append(html);
                        } else {
                            ele.find('.category-label').replaceWith(html);
                            // ele.append(html);
                        }
                    }

                    if ($("#room_id").length > 0) {
                        var userEle = $(this).closest('.show-content').find('.box-header');
                        if (userEle.length >0 ) {
                            // console.log("front-end");
                            // console.log('find category label',userEle.find('.category-label').length);
                            if (userEle.find('.category-label').length <= 0 ) {
                                var html = "<small class=\"category-label\" style=\"color:"+taskCategory.color+";\" >"+
                                (($("#app_local").val()=='th') ? taskCategory.name_th : taskCategory.name_en )+"</small>";
                                // console.log(userEle,html);
                                userEle.append(html);
                            } else {
                                userEle.find('.category-label').css({color:taskCategory.color}).text((($("#app_local").val()=='th') ? taskCategory.name_th : taskCategory.name_en ));
                                userEle.append(html);
                            }
                        }
                    }
                }
            });
        } else {
            $('.box-id').each(function (index, el) {
                if ($(this).val()==cardId) {
                    var ele = $(this).closest('.show-content').find('.category-label');
                    ele.hide();
                }
            });
        }
    }

    

}


$(".task-prioritize-show").on("click",function () {
    $(".task-prioritize-show").hide();
    $(".task-prioritize-edit").show();
})
$(".btn-save-edit-pioritize").on("click",function () {

    var pioritized = $('#task_prioritize_id').val();
    var pioritizedName = $('#task_prioritize_id option:selected').text();
    var pioritizedDesc = $('#task_pioritized_desc').val();
    var boxId = $('#current_card_id').val();

    if (pioritized!=3) {
        pioritizedDesc ='';
    }

    var route = baseSystem+boxId+"?api_token="+api_token ;
    var data = {pioritized:pioritized,pioritized_desc:pioritizedDesc,'_method':'PUT'} ;
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('work',data);
        $('.task-prioritize-show').text(pioritizedName+" "+pioritizedDesc).show();
        $(".task-prioritize-edit").hide();
    })

    
})

$(".btn-close-edit-pioritize").on("click",function () {
    $(".task-prioritize-show").show();
    $(".task-prioritize-edit").hide();
})

$("#task_prioritize_id").on("change",function () {
    $(".row-task-pioritized-desc,#task_pioritized_desc").hide();
    console.log($(this).val());

    if ($(this).val()==3) {
        console.log("desc");
        $(".row-task-pioritized-desc,#task_pioritized_desc").show();
    }
    
})
$("#result").on("change",function () {
    var result = 0 ;
    if ($(this).is(':checked')) {
        result =1 ;
    }
    var boxId = $('#current_card_id').val();
    var route = baseSystem+boxId+"?api_token="+api_token ;
    var data = {result:result,'_method':'PUT'} ;
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('work',data);
    });
    
})


function createTaskCard(data)
{

    if ($("#room_id").length>0) {
        $(".row-action-taken").hide();
        $(".row-incomplete-because").hide();
        $(".row-recommendation").hide();
        $("#task_area_type").hide();
        $("#task_job_type").hide();
    }


    if (data.description!=null) {
        $('#description-body').text(data.description);
        $('#description-body-readonly').text(data.description);
        $('.task-edit-description').hide();
        $('#task-edit-description-btn').hide();
        $('#description').show();
    } else {
        $('#description-body').text("");
        $('#description-body-readonly').text("");
        $('.task-edit-description').show();
        $('#task-edit-description-btn').show();
        $('#description').hide();
    }
    
    
    if (data.cover_img!=null) {
        $(".cotent-cover").find('img').attr('src',data.cover_img);
        $(".cotent-cover").show();
    } else {
        $(".cotent-cover").hide();
    }

    if (data.created_by == user_id) {
        $("#btn-task-viewer").attr("disabled",true);
    }

    $("#task_created_by").val(data.created_by);

    $(".show-title").find('span').text(data.title);
    $(".show-edit-title").hide();
    $(".show-title").show();


    if (data.pioritized!=null) {
        console.log('pioritized_name :',data.pioritized_name);
        if (data.pioritized_desc==null) {
            data.pioritized_desc = '';
        }
        $(".task-prioritize-show").show().text(data.pioritized_name+" "+data.pioritized_desc);
        $(".task-prioritize-edit").hide();

        $("#task_pioritized_desc").hide();
        $("#task_prioritize_id").val(data.pioritized);
        if (data.pioritized==3) {
            $("#task_pioritized_desc").val(data.pioritized_desc).show();
            $(".row-task-pioritized-desc").show();
        }
    }




    
    if (data.job_type!=null&&data.job_type!=0) {
        console.log('jobtype_name :',data.job_type_name);
        $(".task-job-type-show").show().text(data.job_type_name);
        $(".task-job-type-edit").hide();
        $("#task_job_type_id").val(data.job_type);
        $("#task_job_type").show();
    }

    
    if (data.area_type!=null&&data.area_type!=0) {
        console.log('areatype_name :',data.area_type_name);
        $(".task-area-type-show").show().text(data.area_type_name);
        $(".task-area-type-edit").hide();
        $("#task_area_type_id").val(data.area_type);
        $("#task_area_type").show();
    }

    $("#result").prop("checked",false);
    if (data.result!=0) {
        $("#result").prop("checked",true);
    }
    

    
    $(".row-action-taken-show").html('&nbsp;');
    if (data.action_taken!=null) {
        $(".row-action-taken-show").text(data.action_taken);
        $("#action_taken").val(data.action_taken);
        $(".row-action-taken").show();
    }

    
    $(".row-incomplete-because-show").html('&nbsp;');
    if (data.incomplete_because!=null) {
        $(".row-incomplete-because-show").text(data.incomplete_because);
        $("#incomplete_because").val(data.incomplete_because);
        $(".row-incomplete-because").hide();
    }

    
    $(".row-recommendation-show").html('&nbsp;');
    if (data.recommendation!=null) {
        $(".row-recommendation-show").text(data.recommendation);
        $("#recommendation").val(data.recommendation);
        $(".row-recommendation").show();
    }

    $(".row-tower-show").html('&nbsp;');
    if (data.tower!=null) {
        $(".row-tower-show").text(data.tower);
        $("#tower").val(data.tower);
        $(".row-tower").show();
    }

    $(".row-floor-show").html('&nbsp;');
    if (data.floor!=null) {
        $(".row-floor-show").text(data.floor);
        $("#floor").val(data.floor);
        $(".row-floor").show();
    }


    
    if (data.technician_by!=null) {
        var table = '';
        table += "<span class=\"assign-member\"><img src=\" "+data.technician_img+" \" data-toggle=\"tooltip\" title=\""+
                data.technician_name+"\" class=\"img-circle assign-member-img\" height=\"25\" alt=\"User Image\">"+
                "<input type=\"hidden\" class=\"assign-member-id\" value=\""+data.technician_by+"\">"+
                "</span>";
       
        $("#modal-card-content #task_member").find('div.form-group div.list').html(table);
        $("#modal-card-content #task_member").show();
    } else {
        $("#modal-card-content #task_member").find('div.form-group div.list').html('');
        $("#modal-card-content #task_member").hide();
    }


    if (data.requested_by!=null) {
        var table = '';
        table += "<span class=\"assign-member\"><img src=\" "+data.requestor_img+" \" data-toggle=\"tooltip\" title=\""+
                data.requestor_name+"\" class=\"img-circle assign-member-img\" height=\"25\" alt=\"User Image\">"+
                "<input type=\"hidden\" class=\"assign-member-id\" value=\""+data.requested_by+"\">"+
                "</span>";
        $("#modal-card-content #work_accept_by").find('div.form-group div.list').html(table);
        $("#modal-card-content #work_accept_by").show();
    } else {
        $("#modal-card-content #work_accept_by").find('div.form-group div.list').html('');
        $("#modal-card-content #work_accept_by").hide();
    }


    // $(".btn_duedate").data('DateTimePicker') .setLocalDate(duedate);
    
    cardUpdateStatus(data);

    
}

function cardUpdateStatus(data)
{
    var html = ''
    switch (Number(data.status)) {
        case 1:
            html = "<div>"+
                "<img src=\""+$("#asset_url").val()+"/icon_new_2.png\" class=\"icon-task-menu\">"+
                "</div>"+
                data.status_txt ;
            break;
        case 2:
            html = "<div>"+
                "<img src=\""+$("#asset_url").val()+"/icon_todo.png\" class=\"icon-task-menu\">"+
                "</div>"+
                data.status_txt ;
            break;
        case 3:
            html = "<div>"+
                "<img src=\""+$("#asset_url").val()+"/icon_accept.png\" class=\"icon-task-menu\">"+
                "</div>"+
                data.status_txt ;
            break;
        case 4:
            html = "<i class=\"fa fa-window-close-o\"></i>"+
                data.status_txt ;
            break;
        case 5:
            html = "<i class=\"fa fa-clock-o\"></i>"+
                data.status_txt ;
            break;
        case 6:
            html = "<i class=\"fa fa-hourglass\"></i>"+
                data.status_txt ;
            break;
        case 7:
            html = "<i class=\"fa fa-check-square-o\"></i>"+
                data.status_txt ;
            break;
    }


    $(".btn-status").html(html).css({'background':data.status_color,'color':'#FFF'});
}


function cardStatusMove(data)
{
    console.log('cardStatusMove');
    if (data.status==1) {
        elementStatus = "#card_new";
        if ($("#card_new_2").length>0) {
            elementStatus = "#card_new_2";

            // console.log(data.title,data.start_task_at);
            if (data.start_task_at!=null) {
                startDate = new Date(data.start_task_at);
                
                var curr = new Date; // get current date
                // console.log(curr.getDate() ,curr.getDay());
                var first = (curr.getDate() - curr.getDay())+1; // First day is the day of the month - the day of the week
                var last = first + 6; // last day is the first day + 6

                var firstday = getMonday(data.start_task_at) ;
                var lastday = getSunday(data.start_task_at);
                //console.log(startDate,firstday,lastday);
                //console.log(( ((firstday.getTime()/1000)  <= (startDate.getTime()/1000)) &&  ((startDate.getTime()/1000) <= (lastday.getTime()/1000))  ));
                
                if ( ((firstday.getTime()/1000)  <= (startDate.getTime()/1000)) &&  ((startDate.getTime()/1000) <= (lastday.getTime()/1000))  ) {
                    elementStatus = "#card_new";
                }
                //console.log(elementStatus);
            }
        }
    } else if (data.status==2||data.status==3) {
        elementStatus = "#card_accept";
    } else if (data.status==4) {
        elementStatus = "#card_reject";
    } else if (data.status==5) {
        elementStatus = "#card_in_progress";
    } else if (data.status==6) {
        elementStatus = "#card_pending";
    } else if (data.status==7) {
        elementStatus = "#card_done";
    }
    var clone ;

    // $.each($(".show-content").find('.box-id'), function (index, value) {
    //   console.log('.box-id',$(this).val());
 //        if($(this).val()==data.id){
 //            var ele = $(this).closest('.show-content');
 //            console.log('ele',ele);
 //            clone = ele.clone();
 //            ele.remove();
 //        }
    // });

    
    var ele;
    var canMoveCard =false;
    var hasCard =false;
    $('.box-id').each(function (index, el) {
        if ($(this).val()==data.id) {
            console.log('canMoveCard',$(this).closest('.box-parent').find(elementStatus).length,elementStatus);
            if ($(this).closest('.box-parent').find(elementStatus).length <= 0) {
                canMoveCard = true;
                ele = $(this).closest('.show-content');
                clone = ele.clone();
                ele.remove();
            } else {
                hasCard =true;
            }
        }
    });


    console.log(canMoveCard);
    var html = '';
    if (canMoveCard) {
        html = clone ;
    } else if (!hasCard) {
        //--- no card move create card
        html = "<div class=\"box box-solid card show-content\">";
        if (data.file_path!=null) {
            html += "<div><img src=\""+data.file_path+"\" class=\"img-responsive\"></div>" ;
        }
          
            html += "<div class=\"box-header\">"+
                      "<h3 class=\"box-title\">"+data.title+"</h3>"+
                      "<input type=\"hidden\" class=\"box-id\" value=\""+data.id+"\">"+
                      "<div class=\"box-tools pull-right card-btn-edit\">"+
                          "<button type=\"button\" class=\"btn btn-box-tool\" >"+
                            "<i class=\"fa fa-edit\"></i>"+
                          "</button>"+
                      "</div>"+
                  "</div>"+
                  "<div class=\"box-body\">";
        if (data.category_id!=null&&data.category_id!=0) {
            html += " <small class=\"label category-label\" data-id=\""+data.category_id+"\" style=\"color:#FFF; background:"+data.category_color+"\" >"+
                    data.category_name+"</small>";
        }
        if (data.members!=null) {
            html += " <div class=\"card-member pull-right\">";
            for (var i=0; i<data.members.length; i++) {
                html += "<img src=\""+data.members[i].member_img+"\" "+
                " class=\"img-circle\" height=\"25\" data-id=\""+data.members[i].member_id+"\" title=\""+data.members[i].member_name+"\" >" ;
            }
            html += "</div>";
        }
        if (data.doned_at!=null) {
            html +=" <small class=\"label label-success done-at-label\">"+
                    (($("#app_local").val()=='th') ? 'เสร็จ ณ ' : 'Done at ' )+moment.utc(data.doned_at).format("D/MM/YYYY HH:mm")+"</small>";
        }
        if (data.due_dated_at!=null) {
            html +="<BR>"+
                  " <small class=\"due-date-label label {{ labelClass($task) }}\">"+
                  "<i class=\"fa "+((data.due_dated_complete==1) ? 'fa-check-square-o':'fa-clock-o')+"></i>"+
                  moment.utc(data.doned_at).format("D/MM/YYYY HH:mm")+"</small>";
        }
        if (data.checklist_total>0) {
            html +=" <small class=\"card-checklist\">"+
                  "<i class=\"fa fa-check-square-o\"></i>"+data.checklist_success+" / "+data.checklist_total+
                  "</small>";
        }
        html +="</div></div>";
        // $(elementStatus).find('.append-card').append(html);
    }
    // console.log(elementStatus);
    // console.log(html);
    if (data.status==7) {
        $(elementStatus).find('.append-card').prepend(html);
    } else {
        $(elementStatus).find('.append-card').append(html);
    }
    
    
     // ele.remove();
    countCardMenu();

}

function countCardMenu()
{
    if ($(".tab-2-title").length>0) {
        $(".tab-2-title").text("("+$("#card_new_2").find('.show-content').length+")");
    }
    if ($(".title-new").length>0) {
        $(".title-new").text("("+$("#card_new").find('.show-content').length+")");
    }
    if ($(".title-cancel").length>0) {
        $(".title-cancel").text("("+$("#card_reject").find('.show-content').length+")");
    }
    if ($(".title-inprocess").length>0) {
        $(".title-inprocess").text("("+$("#card_in_progress").find('.show-content').length+")");
    }
    if ($(".title-pending").length>0) {
        $(".title-pending").text("("+$("#card_pending").find('.show-content').length+")");
    }
    if ($(".title-done").length>0) {
        $(".title-done").text("("+$("#card_done").find('.show-content').length+")");
    }
    if ($(".title-accept").length>0) {
        $(".title-accept").text("("+$("#card_accept").find('.show-content').length+")");
    }
}

$(document).on("click",".show-content",function (event) {
    var boxId = $(this).find(".box-id").val() ;
    // console.log("click",boxId);
    var title = $(this).find(".box-title").text() ;
    var modal = $('#modal-card-content') ;
    modal.find('.modal-title span').text(title);
    $("#first_open_card").val('false');
    window.history.pushState("object or string", title , $("#baseUrl").val()+baseRoute+boxId);
    clearTaskData();
    $("#current_card_id").val(boxId);
    var route = baseRoute+boxId+"?api_token="+api_token ;
    var data = "" ;
    ajaxPromise('GET',route,data).done(function (data) {
        createTask(data);
    })
    // if($("#table-quatation").length){
    //  $("#section-company").show();
    // }
});


$(function () {

    $('#modal_task_checklist').on('shown.bs.modal', function () {
        $('#checklist_title').focus();
    })

    $(".btn_task_checklist").on("click",function () {
        $('#checklist_title').val('checklist');
        $("#modal_task_checklist").modal('toggle');
        
    });

    $("#checklist_title").on('focus', function () {
        $(this).select(); });


    $("#btn_cancel").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var status = 4 ;
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

    $("#btn-task-viewer").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var route = baseSystem+cardId+"/viewer?api_token="+api_token ;
        ajaxPromise('POST',route,null).done(function (data) {
            socket.emit('work',data);
             // console.log('view',data);
            if (data.viewer=="add") {
                $("#btn-task-viewer").find('i').addClass('fa-eye-slash').removeClass('fa-eye')
            } else {
                $("#btn-task-viewer").find('i').addClass('fa-eye').removeClass('fa-eye-slash')
            }
        }).fail(function (txt) {
            var error = JSON.stringify(txt);
                       swal(
                           'Error...',
                           error,
                           'error'
                       )
        });
    });

    
    
    

    $("#btn-remove-task").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var route = baseSystem+cardId+"?api_token="+api_token ;

        swal({
            title: 'Are you sure?',
            text: "คุณต้องการลบงานนี้ทิ้งใช่หรือไม่!",
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
                ajaxPromise('POST',route,{'_method':'DELETE'}).done(function (data) {
                    socket.emit('work',data);

                    if ($("#is_user").length>0) {
                         window.location.href = $("#baseUrl").val()+baseRoute ;
                    } else {
                         window.location.href = $("#baseUrl").val()+baseSystem ;
                    }

                   
                }).fail(function (txt) {
                    var error = JSON.stringify(txt);
                     swal(
                         'Error...',
                         error,
                         'error'
                     )
                });
            } else if (result.dismiss === 'cancel') {
            }
        })



        
    });
    $("#btn_re_submit").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var status = 1 ;
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
    $("#btn_pending").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var status = 6;
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
    $("#btn_in_progress").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var status = 5 ;
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
    $("#btn_done").on("click", function (event) {
        var cardId = $("#current_card_id").val();
        var status = 7 ;
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
    $("#edit-title").on("click",function () {
        var title =  $(this).parent().find('span').text();
        $("#card_title").val(title)
        $(".show-title").hide();
        $(".show-edit-title").show(0,function () {
                 $("#card_title").focus().select();
        });
    });
    $("#save-edit-title").on("click",function () {
            var title = $("#card_title").val();
        var data = {title:title} ;
        UpdateTask(data).done(function (res) {
            $(".show-title").find('span').text(title);
            $(".show-edit-title").hide();
            $(".show-title").show();
        });
    });
    $("#close-edit-title").on("click",function () {
        $(".show-edit-title").hide();
        $(".show-title").show();
    });
});

function deleteTask(data)
{
    $(".show-content").find('.box-id').each(function (index, el) {
        if ($(this).val()==data.delete_task_id) {
            var ele = $(this).closest('.show-content');
            ele.remove();
        }
    });
    var cardId = $("#current_card_id").val();
    if (data.delete_task_id==cardId) {
        $("#modal-card-content").modal('hide');
    }
    console.log('deleteTask',cardId);
    if (data.delete_task_id!=cardId) {
        $("#modal-card-delete").modal('show');
    }

    
}
        

function UpdateTask(data)
{
    var dfd = $.Deferred();
    var cardId = $("#current_card_id").val();
    var route = baseSystem+cardId+"?api_token="+api_token ;
    data._method='PUT';
    ajaxPromise('POST',route,data).done(function (res) {
        dfd.resolve(res);
        socket.emit('work',res);
        // createTaskTableHistory(res.task_historys);
    }).fail(function () {
        dfd.reject("error");
    })
    return dfd.promise();
}
function ajaxUpdateStatus(boxId,status)
{
    var dfd = $.Deferred();
    var url = $("#apiUrl").val() ;
    $.ajax({
        url: url+baseSystem+boxId+"/status?api_token="+api_token,
        type: 'POST',
        dataType: 'json',
        data : {status:status,'_method':'PUT'}
    })
    .done(function (res) {

        if (res.result=="true") {
            socket.emit('work',res.response);
            dfd.resolve(res.response);
        } else {
            dfd.reject(res.errors);
        }
    })
    .fail(function () {
        dfd.reject("error");
    })
    return dfd.promise();
}


function hideInputTitle()
{
    var title =  $("#modal-card-content .modal-title").find('input').val();

    if (typeof title != "undefined") {
        var text = "<span>"+title+"</span>";
        $("#modal-card-content .modal-title").html(text);
    }
    $("#modal-card-content .modal-title").removeClass('active-edit-title');
    
}



function ajaxCreateQuatation(txt,start)
{
    var dfd = $.Deferred();
    var url = $("#apiUrl").val() ;
    $.ajax({
        url: $("#apiUrl").val()+baseSystem+'?api_token='+api_token,
        type: 'POST',
        dataType: 'json',
        data: { 'title': txt,'start':start},
    })
    .done(function (res) {

        if (res.result=="true") {
            dfd.resolve(res.response);
        } else {
            dfd.reject(res.errors);
        }
    })
    .fail(function () {
        dfd.reject("error");
    })
    return dfd.promise();
}

$(".row-action-taken-show").on("click",function () {
    if ($("#room_id").length>0) {
        return false;
    }
    $(".row-action-taken-edit").show();
    $(".row-action-taken-show").hide();
});
$(".btn-save-action-taken").on("click",function () {
    var actionTaken = $('#action_taken').val();
    var boxId = $('#current_card_id').val();
    var route = baseSystem+boxId+"?api_token="+api_token ;
    var data = {action_taken:actionTaken,'_method':'PUT'} ;
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('work',data);
        $('.row-action-taken-show').text(actionTaken).show();
        $(".row-action-taken-edit").hide();
    })
});
$(".btn-close-edit-action-taken").on("click",function () {
    $(".row-action-taken-edit").hide();
    $(".row-action-taken-show").show();
});


$(".row-incomplete-because-show").on("click",function () {
    if ($("#room_id").length>0) {
        return false;
    }
    $(".row-incomplete-because-edit").show();
    $(".row-incomplete-because-show").hide();
});
$(".btn-save-incomplete-because").on("click",function () {
    var incompleteBecause = $('#incomplete_because').val();
    var boxId = $('#current_card_id').val();
    var route = baseSystem+boxId+"?api_token="+api_token ;
    var data = {incomplete_because:incompleteBecause,'_method':'PUT'} ;
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('work',data);
        $('.row-incomplete-because-show').text(incompleteBecause).show();
        $(".row-incomplete-because-edit").hide();
    })
});
$(".btn-close-edit-incomplete-because").on("click",function () {
    $(".row-incomplete-because-edit").hide();
    $(".row-incomplete-because-show").show();
});


$(".row-recommendation-show").on("click",function () {
    if ($("#room_id").length>0) {
        return false;
    }
    $(".row-recommendation-edit").show();
    $(".row-recommendation-show").hide();
});
$(".btn-save-recommendation").on("click",function () {
    var recommendation = $('#recommendation').val();
    var boxId = $('#current_card_id').val();
    var route = baseSystem+boxId+"?api_token="+api_token ;
    var data = {recommendation:recommendation,'_method':'PUT'} ;
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('work',data);
        $('.row-recommendation-show').text(recommendation).show();
        $(".row-recommendation-edit").hide();
    })
});
$(".btn-close-edit-recommendation").on("click",function () {
    $(".row-recommendation-edit").hide();
    $(".row-recommendation-show").show();
});





$(".task-area-type-show").on("click",function () {
    if ($("#room_id").length>0) {
        return false;
    }
    $(".task-area-type-show").hide();
    $(".task-area-type-edit").show();
})
$(".btn-save-edit-area-type").on("click",function () {
    var areaType = $('#task_area_type_id').val();
    var areaTypeName = $('#task_area_type_id option:selected').text();
    
    var boxId = $('#current_card_id').val();
    var route = baseSystem+boxId+"?api_token="+api_token ;
    var data = {area_type:areaType,'_method':'PUT'} ;
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('work',data);
        $('.task-area-type-show').text(areaTypeName).show();
        $(".task-area-type-edit").hide();
    })
})
$(".btn-close-edit-area-type").on("click",function () {
    $(".task-area-type-show").show();
    $(".task-area-type-edit").hide();
})



$(".task-job-type-show").on("click",function () {
    if ($("#room_id").length>0) {
        return false;
    }
    $(".task-job-type-show").hide();
    $(".task-job-type-edit").show();
})
$(".btn-save-edit-job-type").on("click",function () {
    var jobType = $('#task_job_type_id').val();
    var jobTypeName = $('#task_job_type_id option:selected').text();
    var boxId = $('#current_card_id').val();
    var route = baseSystem+boxId+"?api_token="+api_token ;
    var data = {job_type:jobType,'_method':'PUT'} ;
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('work',data);
        $('.task-job-type-show').text(jobTypeName).show();
        $(".task-job-type-edit").hide();
    })
})
$(".btn-close-edit-job-type").on("click",function () {
    $(".task-job-type-show").show();
    $(".task-job-type-edit").hide();
})



$(".row-tower-show").on("click",function () {
    
    $(".row-tower-edit").show();
    $(".row-tower-show").hide();
});
$(".btn-save-tower").on("click",function () {
    var tower = $('#tower').val();
    var boxId = $('#current_card_id').val();
    var route = baseSystem+boxId+"?api_token="+api_token ;
    var data = {tower:tower,'_method':'PUT'} ;
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('work',data);
        $('.row-tower-show').text(tower).show();
        $(".row-tower-edit").hide();
    })
});
$(".btn-close-edit-tower").on("click",function () {
    $(".row-tower-edit").hide();
    $(".row-tower-show").show();
});


$(".row-floor-show").on("click",function () {
    
    $(".row-floor-edit").show();
    $(".row-floor-show").hide();
});
$(".btn-save-floor").on("click",function () {
    var floor = $('#floor').val();
    var boxId = $('#current_card_id').val();
    var route = baseSystem+boxId+"?api_token="+api_token ;
    var data = {floor:floor,'_method':'PUT'} ;
    ajaxPromise('POST',route,data).done(function (data) {
        socket.emit('work',data);
        $('.row-floor-show').text(floor).show();
        $(".row-floor-edit").hide();
    })
});
$(".btn-close-edit-floor").on("click",function () {
    $(".row-floor-edit").hide();
    $(".row-floor-show").show();
});

