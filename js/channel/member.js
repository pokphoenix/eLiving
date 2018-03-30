$(document).on("input","#search_room",function (e) {
    ajaxSearchAutoComplete($(this)).then(function ( res ) {
        roomData(res).done(function (data) {
            $(".search-append").html(data);
        })
    })
});
$(document).on("click",".my-autocomplete-li",function (e) {
    var id = $.trim($(this).find('.search-id').val()) ;
    var text = $.trim($(this).find('.search-text').val()) ;
 
    var html = "<tr>"+
            "<td><button type=\"button\" "+
            " class=\"btn btn-danger btn-xs btn-user-in-room-del\" >"+
            "<i class=\"fa fa-close\"></i></button>"+
            "<input type=\"hidden\" class=\"room-id\" value=\""+(id)+"\">"+
            "</td>"+
            "<td></td><td>"+text+"</td></tr>";
    $("#user-in-room-table tbody").append(html);
  // $(this).parent().parent().parent().find("#search_room").val($(this).find('h5').text()) ;
    $(this).parent().remove();
    $("#user-in-room-table tbody tr td:nth-child(2)").each(function (i) {
        var j = ++i;
        $(this).text(j);
    });
    $("#search_room").val('');
});

$(document).on("click",".btn-user-in-room-del",function (event) {
    var rows = $(this).closest("tr") ;
    var itemID = $.trim(rows.find('.id-card').val());
    rows.remove();
    $("#user-in-room-table tbody tr td:nth-child(2)").each(function (i) {
        var j = ++i;
        $(this).text(j);
    });
});

function roomData(res)
{
    var dfd = $.Deferred();
    elewidth = res.ele.innerWidth();
    $(res.ele).next('ul').remove();
    var data = res.data.response.data ;
    if (data.length>0) {
        var autocomplete = "<ul class=\"my-autocomplete-ul\" style=\"width:"+elewidth+"px; \">";
        for (var i=0; i<data.length; i++) {
            autocomplete+= "<li class=\"my-autocomplete-li \"> " ;
       
            autocomplete+= "<h5>&nbsp;"+data[i].text+"</h5>";

            if (typeof data[i].id !="undefind") {
                autocomplete+= "<input type=\"hidden\" class=\"search-id\" value=\""+$.trim(data[i].id)+"\">";
                autocomplete+= "<input type=\"hidden\" class=\"search-text\" value=\""+$.trim(data[i].text)+"\">";
            }

       
            autocomplete+= "</li>";
        }

        autocomplete+='</ul>';

      // res.ele.after().html(autocomplete);

      // $(res.ele).after(autocomplete);
        dfd.resolve(autocomplete);
    } else {
        dfd.reject("");
    }
    return dfd.promise();
}
