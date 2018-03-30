$(document).on("click",".hide-history",function (event) {
    $(".history h6.text-muted").hide();
    $(".history div.item").hide();
    $(this).text((($("#app_local").val()=='th') ? 'แสดง' : 'show' ));
    $(this).addClass('show-history').removeClass('hide-history');

});
$(document).on("click",".show-history",function (event) {
    $(".history h6.text-muted").show();
    $(".history div.item").show();
    $(this).text('hide');
    $(this).addClass('hide-history').removeClass('show-history');
});


function createTaskTableHistory(data)
{
    // console.log('createTaskTableHistory',data);
    moment.locale('th');

    if (data.length>0) {
        var table = "<span class=\"hide-history pull-right\">"+
                    (($("#app_local").val()=='th') ? 'ซ่อน' : 'hide' )+"</span>";
        table += "<h4 class=\"title\"><i class=\"fa fa-history\"></i> "+(($("#app_local").val()=='th') ? 'ประวัติ' : 'History' )+"</h4>";
        var attachment ='';
        for (var i = 0; i<data.length; i++) {
            if (data[i].status==7&&data[i].comment_id!=null) {
                table += "<div class=\"item\">"+
                "<p class=\"header\">"+
                "<a href=\"#\" class=\"name\">"+
                data[i].first_name+" "+data[i].last_name+
                "<small class=\"text-muted\"> "+
                "<i class=\"fa fa-clock-o\"></i> "+
                moment.unix(data[i].history_created_at).format("D/MM/YYYY HH:mm")+
                "</small>"+
                "</a>";
                if (data[i].created_by_id==user_id) {
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
                
                table +="<input class=\"comment-id\" type=\"hidden\" value=\""+data[i].comment_id+"\" > </div>" ;
            } else if (data[i].status==10) {
                table += "<h6 class=\"text-muted\">"+data[i].first_name+
                " "+data[i].last_name+
                " "+data[i].history_status+
                " at "+moment.unix(data[i].history_duedate_to).format("D/MM/YYYY HH:mm")+
                " , "+moment.unix(data[i].history_created_at).format("D/MM/YYYY HH:mm")+
                "</h6>";
            } else if (data[i].status==20) {
                table += "<h6 class=\"text-muted\">"+data[i].first_name+
                " "+data[i].last_name+
                " "+data[i].history_status+
                " "+data[i].file_name+
                " , "+moment.unix(data[i].history_created_at).format("D/MM/YYYY HH:mm")+
                "</h6>";
            } else if (data[i].status==22||data[i].status==23) {
                table += "<h6 class=\"text-muted\">"+data[i].first_name+
                " "+data[i].last_name+
                " "+data[i].history_status+
                " "+data[i].category_name+
                " , "+moment.unix(data[i].history_created_at).format("D/MM/YYYY HH:mm")+
                "</h6>";
            } else if (data[i].status==3||data[i].status==4) {
                table += "<h6 class=\"text-muted\">"+data[i].first_name+
                " "+data[i].last_name+
                " "+data[i].history_status+
                " "+data[i].assign_name+
                " , "+moment.unix(data[i].history_created_at).format("D/MM/YYYY HH:mm")+
                "</h6>";
            } else {
                    table += "<h6 class=\"text-muted\">"+data[i].first_name+
                " "+data[i].last_name+
                " "+data[i].history_status+
                " , "+moment.unix(data[i].history_created_at).format("D/MM/YYYY HH:mm")+"</h6>";
            }
        }

        $("#modal-card-content").find('.history').html(table);
    }

    
}
