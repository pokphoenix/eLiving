var RouteUrl = "/resolution/" ;

$(document).on("click",".btn-voting-item",function () {
    var cardId = $("#current_card_id").val();
    var itemId = $(this).closest('td').find('.resolution-item-id').val();
    var route = RouteUrl+cardId+"/voting/"+itemId+"?api_token="+api_token;
    ajaxPromise('GET',route,null).done(function (data) {
        socket.emit('resolution',data);
        createCard(data);
        // location.reload();
        // $("#voting").hide();
        // $("#modal-voting").modal("hide");
    }).fail(function (txt) {
        var error = JSON.stringify(txt);
                       swal(
                           'Error...',
                           error,
                           'error'
                       )
    });
});

$("#cancel_vote").on("click", function (event) {
    var cardId = $("#current_card_id").val();
    var status = 4 ;
    ajaxUpdateStatus(cardId,status);
});
$("#btn_manual_voted").on("click", function (event) {
    var cardId = $("#current_card_id").val();
    var status = 3 ;
    ajaxUpdateStatus(cardId,status).done(function (data) {
         socket.emit('resolution',data);
        window.location.reload() ;
    }).fail(function (txt) {
        var error = JSON.stringify(txt);
                       swal(
                           'Error...',
                           error,
                           'error'
                       )
    });
});


$("#btn_change_voted").on("click", function (event) {
    var cardId = $("#current_card_id").val();
    var route = RouteUrl+cardId+"/change_voted?api_token="+api_token;
    ajaxPromise('POST',route,{'_method':'DELETE'}).done(function (data) {
        socket.emit('resolution',data);
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


function createVoteList(data)
{
    var companies = data.resolution_companys ;
    
    var table = "";
    if (companies.length >0) {
        for (var i=0; i<companies.length; i++) {
            table += "<tr>"+
                    "<td><button class=\"btn btn-info btn-flat btn-xs btn-voting-company\" >vote</button>"+
                    "<input type=\"hidden\" class=\"company-id\" value=\""+companies[i].id+"\" >"+
                    "</td>"+
                    "<td>"+(i+1)+"</td>"+
                    "<td>"+companies[i].name+"</td>"+
                    "<td>"+companies[i].price_net+"</td>"+
                    "<td>"+companies[i].vote_count;
            if (companies[i].user.length>0) {
                table += " (";
                for (var k=0; k<companies[i].user.length; k++) {
                    console.log(k,companies[i].user[k].name);
                    table +=   (k==0) ? companies[i].user[k].name : ","+companies[i].user[k].name ;
                }
                table += ")";
            }

            table += "</td>"+
                    "</tr>";
                // console.log(table);
        }
        $("#modal-voting #voting-table tbody").html(table);
    }
    // console.log(companies.length);
    
    $("#modal-voting").modal("show");
}





function ajaxUpdateStatus(boxId,status)
{
    var dfd = $.Deferred();
    var url = $("#apiUrl").val() ;
    $.ajax({
        url: url+RouteUrl+boxId+"/status?api_token="+api_token,
        type: 'POST',
        dataType: 'json',
        data : {status:status,'_method':'PUT'}
    })
    .done(function (res) {
        // console.log(res);
        if (res.result=="true") {
            dfd.resolve(res.response);
            console.log('emit',res.response);
            socket.emit('resolution',res.response);
            var cardId = $("#current_card_id").val();
            createCard(res.response);
            // window.location.href = $("#baseUrl").val()+RouteUrl+cardId ;
        } else {
            dfd.reject(res.errors);
            var error = JSON.stringify(res.errors);
                       swal(
                           'Error...',
                           error,
                           'error'
                       )
        }
    })
    .fail(function () {
        dfd.reject("error");
        swal(
            'Error...',
            'Cannot connect server.Please try again',
            'error'
        )
    })
    return dfd.promise();
}
