var RouteUrl = "/resolution/" ;

$(document).on("click",".show-content",function (event) {
    var boxId = $(this).find(".box-id").val() ;
    // console.log("click",boxId);
    var title = $(this).find(".box-title").text() ;
    var modal = $('#modal-card-content') ;
    modal.find('.modal-title span').text(title);

    openCard(title,boxId);
    // if($("#table-resolution").length){
    //  $("#section-company").show();
    // }
});

function openCard(title,boxId)
{
    window.history.pushState("object or string", title , $("#baseUrl").val()+RouteUrl+boxId);
    clearResolutionData();
    $("#current_card_id").val(boxId);     ;
    $("#item_id").val('');
    var route = RouteUrl+"data/"+boxId+"?api_token="+api_token ;
    ajaxPromise('GET',route,null).done(function (data) {
        createCard(data)
        $("#modal-card-content").modal('show');
    }).fail(function (txt) {
        var error = JSON.stringify(txt);
                       swal(
                           'Error...',
                           error,
                           'error'
                       )
    });
}

function createCard(data)
{
    createResolutionTable(data);
    createTableItem(data.resolution_items);
    createHistory(data.resolution_historys);
    createContentCard(data.resolution);
    createResolutionSetInit(data.status);
    createComment(data.resolution_comments);
    createUserCanVote(data.resolution_user_can_vote);
}

$(".btn-resolution-new").on("click",function () {
    $("#modal-card-content").modal("toggle");
    $("#modal-resolution-item").modal("toggle");
});



$(document).on("click",".btn-set-company-winner",function (event) {
    var boxId = $("#current_card_id").val() ;
    var voteId = $(this).closest('td').find(".resolution-item-id").val() ;
    var route = RouteUrl+boxId+"/winner/"+voteId+"?api_token="+api_token;
    ajaxPromise('GET',route,null).then(function (data) {
        createCard(data);
    }).fail(function (txt) {
        var error = JSON.stringify(txt);
                       swal(
                           'Error...',
                           error,
                           'error'
                       )
    });
});


function createResolutionTable(data)
{
    $(".btn-resolution-new").text((($("#app_local").val()=='th') ? 'เพิ่มรายการโหวต' : 'Add Vote List' ));
   
    var item = data.resolution_items;
 
    var resolution = data.resolution ;
    var voting = data.resolution_votes ;
    var userCanVote = data.resolution_user_can_vote ;
    var voteColor = ["#D7C6E6","#99D6EA","#7CE0D3","#C1A7E2","#5BC2E7","#2CD5C4","#9063CD","#00A9E0","#00C7B1"];

    var appLocal = $("#app_local").val();

    var statusTool = data.status;


    cardUpdateStatus(resolution)

 
    //---Title
    $("#modal-card-content .show-title").find('span').text(data.resolution.title);
   
    $("#modal-card-content .btn-show-set-company-winner").hide();
    if (resolution.vote_winner==null&&resolution.status==3) {
        $("#modal-card-content .btn-show-set-company-winner").show();
    }


    // console.log(data.resolution_items);
    // console.log(item, typeof item);

    if (item.length <= 0) {
        // console.log("not data found");
        $("#data-summary-resolution-table").html('');
        $("#table-resolution tbody").html('');
        return false;
    }

    var table = "<table id=\"table-resolution-summary\" "+
                " class=\"table table-bordered table-striped\">"+
                "<thead>"+
                "<tr>"+
                "<th rowspan=\"2\" class=\"vm-ct\" width=\"50\">"+((appLocal=='th') ? 'อันดับ' : 'No' )+ "</th>"+
                "<th rowspan=\"2\" class=\"vm-ct\">"+((appLocal=='th') ? 'รายการ' : 'Description' )+ "</th>"+
                "<th rowspan=\"2\" class=\"vm-ct\">"+((appLocal=='th') ? 'จำนวนโหวต' : 'Amount' )+ "</th>";

        table += "</tr>";

    



        table += "</thead><tbody>"  ;

    // console.log('item',item);
    // console.log('company',company);
    // console.log('companyItem',companyItem);

    // console.log('item.length',item.length);
    // console.log('company.length',company.length);
    // console.log('companyItem.length',companyItem.length);

    for (var i =0; i< item.length; i++) {
        table += "<tr>"+
                "<td>";
        if (resolution.vote_winner==null&&resolution.status==3&&statusTool.btn_set_company_winner&&item[i].amount>0) {
            table +="<button class=\"btn btn-success btn-flat btn-xs btn-set-company-winner\" >"+((appLocal=='th') ? 'เลือก' : 'Choose' )+"</button>";
        } else if (statusTool.voting) {
            table +="<button class=\"btn btn-info btn-flat btn-xs btn-voting-item\" >"+((appLocal=='th') ? 'โหวต' : 'Vote' )+"</button>";
        } else {
            table +=(i+1);
        }
                
        table +="<input type=\"hidden\" class=\"resolution-item-id\" value=\""+(item[i].id)+"\"></td>"+
                "<td><span >"+(item[i].name)+"</span></td>"+
                "<td class=\"text-center\"><span >"+(item[i].amount)+"</span></td>";
        table += "</tr>";

        if (item[i].id==data.voted_item_id) {
            $("#btn_voted").html(((appLocal=='th') ? 'คุณโหวต' : 'You choose' )+'<BR>'+item[i].name);
        }
        if (item[i].id==resolution.vote_winner) {
            $("#success_vote").html(((appLocal=='th') ? 'สรุปผลโหวต <BR> เลือก ' : 'Voted Result <BR> choose' )+'<BR>'+item[i].name);
        }
    }

    table += "</tbody><tfoot>";


    

    table += "<tr>"+
             "<td colspan=\"2\">"+((appLocal=='th') ? 'จำนวนคนที่ไม่ออกเสียง' : 'No Vote' )+"</td>";
             
             novote = 0 ;
    for (var h =0; h < voting.length; h++) {
        if (voting[h].item_id==0) {
            novote++ ;
        }
    }
    table += "<td class=\"text-center\" colspan=\""+(item.length*2)+"\">"+novote+"</td>";

    table += "</tr>";

    // for(var i =0;i< item.length ; i++){
    

    // }
    table += "</tfoot><table>";


    var percent = ((voting.length/userCanVote.length)*100).toFixed(0);



    table +="<h4 class=\"title\"><i class=\"fa fa-gavel\" ></i> "+((appLocal=='th') ? 'ผลโหวต' : 'Vote Result' )+"</h4>"+
                "<div class=\"row\">"+
                      "<div class=\"text-right\" style=\"width:5%; display:inline-block;float:left;\"><span>"+percent+"% </span></div>"+
                      "<div class=\"\" style=\"width:95%;margin-top:7px;\">"+
                        "<div class=\"progress progress-xs\">"+
                          "<div class=\"checklist-progress-bar progress-bar progress-bar-success progress-bar-striped\" "+
                            "role=\"progressbar\" aria-valuenow=\""+percent+"\" aria-valuemin=\"0\" "+
                            "aria-valuemax=\"100\" style=\"width: "+percent+"%\">"+
                              
                          "</div>"+
                        "</div>"+
                      "</div>"+
                    "</div>";

    $(".btn-resolution-new").text(((appLocal=='th') ? 'แก้ไขมติ' : 'Edit resolution' ));
    $("#data-summary-resolution-table").html(table);

    
    if (data.voted_item_id==0) {
        $("#btn_voted").html(((appLocal=='th') ? 'คุณเลือก<BR>ไม่ออกเสียง' : 'You Choose<BR>No vote' ));
    }

    // console.log("createresolutionTable("+voting.length+" / "+userCanVote.length+")");
    
}

function cardUpdateStatus(data)
{
    var html = ''
    switch (Number(data.status)) {
        case 1:
            html = "<div>"+
                "<img src=\""+$("#asset_url").val()+"/icon_new.png\" class=\"icon-task-menu\">"+
                "</div>"+
                data.status_txt ;
            break;
        case 2:
            html = "<div>"+
                "<img src=\""+$("#asset_url").val()+"/icon_voting.png\" class=\"icon-task-menu\">"+
                "</div>"+
                data.status_txt ;
            break;
        case 3:
            html = "<div>"+
                "<img src=\""+$("#asset_url").val()+"/icon_voted.png\" class=\"icon-task-menu\">"+
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

function createTableItem(data)
{
    // console.log(data);
    // console.log('[createTableItem]',$("#table-resolution tbody tr").length);

    if (data.length>0) {
        $("#insert_new_item").val("false");
    }

    if ($("#table-resolution tbody tr").length==0) {
        for (var i =0; i< data.length; i++) {
            // console.log(data[i]);
            table = "<tr>"+
                    "<td><button type=\"button\" "+
                    " class=\"btn btn-danger btn-xs btn-resolution-del-item\" >"+
                    "<i class=\"fa fa-close\"></i></button>"+
                    "<input type=\"hidden\" class=\"resolution-item-id\" value=\""+(data[i].id)+"\">"+
                    "</td>"+
                    "<td>"+(i+1)+"</td>"+
                    "<td><span >"+(data[i].name)+"</span></td>"+
                    "</tr>";
            $("#table-resolution tbody").append(table)
        }
    } else {
        table = '';
        for (var i =0; i< data.length; i++) {
            table += "<tr>"+
                    "<td><button type=\"button\" "+
                    " class=\"btn btn-danger btn-xs btn-resolution-del-item\" >"+
                    "<i class=\"fa fa-close\"></i></button>"+
                    "<input type=\"hidden\" class=\"resolution-item-id\" value=\" "+(data[i].id)+" \">"+
                    "</td>"+
                    "<td>"+(i+1)+"</td>"+
                    "<td><span >"+(data[i].name)+"</span></td>"+
                    "</tr>";
        }
        $("#table-resolution tbody").html(table);
    }

    
}


function dateFormat(unix_timestamp)
{
    var m_names = new Array(
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec"
    );

    var date = new Date(unix_timestamp*1000);
    var years = date.getFullYear();
    var months = date.getMonth();
    var Days = date.getDate();

    // Hours part from the timestamp
    var hours = date.getHours();
    // Minutes part from the timestamp
    var minutes = "0" + date.getMinutes();
    // Seconds part from the timestamp
    var seconds = "0" + date.getSeconds();

    // Will display time in 10:30:23 format
    var formattedTime = hours + ':' + minutes.substr(-2) ;

    var  DateTxt = Days+" "+m_names[months]+" "+years+" at "+formattedTime ;
    return DateTxt ;
}

function createHistory(data)
{
    // console.log(data);
    moment.locale('th');

    var userId = $("#user_id").val();

    if (data.length>0) {
        var table = '';
        for (var i = 0; i<data.length; i++) {
            if (data[i].status!=7) {
                table += "<h6 class=\"text-muted\">"+data[i].first_name+
                " "+data[i].last_name+
                " "+data[i].history_status+
                " , "+moment.unix(data[i].history_created_at).format("D/MM/YYYY HH:mm")+"</h6>";
            } else if (data[i].status==7&&data[i].comment_id!=null) {
                table += "<div class=\"item\">"+
                "<p class=\"header\">"+
                "<a href=\"#\" class=\"name\">"+
                data[i].first_name+" "+data[i].last_name+
                "<small class=\"text-muted\"> "+
                "<i class=\"fa fa-clock-o\"></i> "+
                moment.unix(data[i].history_created_at).format("D/MM/YYYY HH:mm")+
                "</small>"+
                "</a>";
                if (data[i].created_by_id==userId) {
                    table += "<span class=\"pull-right\">"+
                    "<button type=\"button\" class=\"btn btn-default btn-comment-delete btn-xs\" title=\"Remove\">"+
                    "<i class=\"fa fa-times\"></i></button>"+
                    "</span>";
                    table += "</p><p class=\"message can-edit\">"+
                    data[i].comment_description+
                    "</p>";
                } else {
                    table += "</p><p class=\"message\">"+
                    data[i].comment_description+
                    "</p>";
                }
                
                table +="<input class=\"comment-id\" type=\"hidden\" value=\""+data[i].comment_id+"\" > "
                "</div>" ;
            }
        }
        // console.log(table);
        $("#modal-card-content").find('.history').html(table);
    }

    
}

function createContentCard(data)
{
    $("#start_vote").hide();
    $("#edit-title").hide();
    $(".btn-edit-resolution-company").hide();
    
    if (data.status==1) {
        $("#modal-card-content .menu-flow").show();
        $("#start_vote").show();
        $("#edit-title").show();
        $(".btn-edit-resolution-company").show();
    }
    
    

    if (data.vote_winner!=null) {
        $("#start_vote,#edit-title").hide();
        $(".section-resolution-button-new,.btn-edit-resolution-company").hide();
    }

    

    
}

function cardStatusMove(data)
{
    // console.log('cardStatusMove');
    if (data.status==1) {
        elementStatus = "#card_new";
    } else if (data.status==2) {
        elementStatus = "#card_voting";
    } else if (data.status==3) {
        elementStatus = "#card_voted";
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
    var ele;
    var canMoveCard =false;
    $('.box-id').each(function (index, el) {
        if ($(this).val()==data.id) {
            if ($(this).closest('.box-parent').find(elementStatus).length <= 0) {
                canMoveCard = true;
                ele = $(this).closest('.show-content');
                clone = ele.clone();
                ele.remove();
            }
        }
    });
    if (canMoveCard) {
        $(elementStatus).find('.append-card').append(clone);
    }
}

function clearResolutionData()
{

    $("#modal-user-voting .modal-body").html('');

    $(".show-edit-title").hide();
    $(".show-title").show();

    $("#card_title").val("");
    $("#insert_new_item").val("true");
    $("#modal-card-content").find('.history').html('');
    $('#modal-card-content input,#modal-card-content textarea').val('');
    $('#modal-resolution-item input,#modal-resolution-item textarea').val('');
    $('#modal-resolution-company input,#modal-resolution-company textarea').val('');
    $("#supplier_name").next('ul').remove();

    $("#modal-resolution-company #append_upload").html('');

    

}

function createResolutionSetInit(data)
{
    // console.log('createresolutionSetInit',data);
    $("#modal-card-content .menu-flow").hide();
    if (data.btn_resubmit||data.in_progress||data.pending||data.done||data.cancel_vote) {
        $("#modal-card-content .menu-flow").show();
    }

    $("#modal-card-content #btn_resubmit").hide();
    if (data.btn_resubmit) {
        $("#modal-card-content #btn_resubmit").show();
    }

    $("#modal-card-content #btn_manual_voted").hide();
    if (data.btn_manual_voted) {
        $("#modal-card-content #btn_manual_voted").show();
    }

    $("#modal-card-content .btn-set-company-winner").hide();
    
    if (data.btn_set_company_winner) {
        $("#modal-card-content .btn-set-company-winner").show();
    }

    $("#modal-card-content #btn_in_progress").hide();
    if (data.in_progress) {
        $("#modal-card-content #btn_in_progress").show();
    }
    
    $("#modal-card-content #btn_pending").hide();
    if (data.pending) {
        $("#modal-card-content #btn_pending").show();
    }
    
    $("#modal-card-content #btn_done").hide();
    if (data.done) {
        $("#modal-card-content #btn_done").show();
    }

    $("#modal-card-content #cancel_vote").hide();
    if (data.cancel_vote) {
        $("#modal-card-content #cancel_vote").show();
    }

    $("#modal-card-content .menu-action").hide();
    if (data.voting||data.voted||data.change_voted||data.winner) {
        $("#modal-card-content .menu-action").show();
    }


    $("#modal-card-content #voting").hide();
    $("#modal-card-content #no_vote").hide();

    $(".btn-voting-item").hide();
    

    if (data.voting) {
        $("#modal-card-content #voting").show();
        $("#modal-card-content #no_vote").show();

        $(".btn-voting-item").show();
    }

    $("#modal-card-content #btn_voted").hide();
    if (data.voted) {
        $("#modal-card-content #btn_voted").show();
    }

    $("#modal-card-content #btn_change_voted").hide();
    if (data.change_voted) {
        $("#modal-card-content #btn_change_voted").show();
    }

    $(".section-resolution-button-new").hide();
    if (data.add_item) {
        $(".section-resolution-button-new").show();
    }

    
    
    
    $("#modal-card-content #success_vote").hide();
    $("#modal-card-content #voted").show();
    if (data.winner) {
        $("#modal-card-content #voted").hide();
        $("#modal-card-content #success_vote").show();
    }

    
    

}

function createUserCanVote(data)
{
    // console.log('createUserCanVote',data);
    $(".user-can-vote .user-can-vote-list").html('');
    if (data.length>0) {
        var html = "";
        for (var i = 0; i< data.length; i++) {
            html += "<div class=\"user-can-vote-li text-left\" style=\"float:left;\"> <img src=\" "+data[i].img+" \" class=\"img-circle assign-member-img\" height=\"25\" alt=\"User Image\"> "+data[i].user_name ;
            if (data[i].voted) {
                html += " <small> ";
                if (data[i].vote_name!=null) {
                    html += (($("#app_local").val()=='th') ? 'โหวต' : 'Voted' )+" "+
                    data[i].vote_name ;
                } else {
                    html += (($("#app_local").val()=='th') ? 'ไม่ออกเสียง' : 'No voted' ) ;
                }
                html +=" ("+ moment.utc(data[i].created_at).format("D/MM/YYYY HH:mm") +")";
                html +="</small>" ;
            }
            html += " </div>" ;
        }
        
        $(".user-can-vote .user-can-vote-list").html(html);
    }
}
