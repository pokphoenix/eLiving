
var RouteUrl = "/purchase/quotation/" ;

$(document).on("click",".link-attachment-file",function () {
    var boxId = $("#current_card_id").val() ;
    var companyId = $(this).closest('td').find('.file-company-id').val();
    var route = RouteUrl+boxId+"/attach/"+companyId+"?api_token="+api_token ;
    ajaxPromise('GET',route,null).done(function (data) {
        var file = data.attachment ;
        var element = $("#modal-attachment").find('.modal-body') ;
        var appendData = "<ul>";
        for (var i =0; i<file.length; i++) {
            appendData += "<li><a href=\" "+file[i].img+" \" target=\"_blank\" download=\""+file[i].file_name+"\" >"+file[i].file_name+" ("+convertByte(file[i].file_size)+") "+"</a></li>" ;
        }
        appendData += "</ul>";
        element.html(appendData);
        $("#modal-attachment").modal('show');
    })
});
$(document).on("click",".all-attachment-file",function () {
    var boxId = $("#current_card_id").val() ;
    // console.log(boxId);
    var route = RouteUrl+boxId+"/attach-all?api_token="+api_token ;
    ajaxPromise('GET',route,null).done(function (data) {
        var file = data.attachment ;
        var element = $("#modal-attachment").find('.modal-body') ;
        var appendData = "<ul>";
        for (var i =0; i<file.length; i++) {
            appendData += "<li><a href=\" "+file[i].img+" \" target=\"_blank\" download=\""+file[i].file_name+"\" >"+file[i].file_name+" ("+convertByte(file[i].file_size)+") "+"</a></li>" ;
        }
        appendData += "</ul>";
        element.html(appendData);
        $("#modal-attachment").modal('show');
    })
});


$(document).on("click","#modal-card-content .link-voting-list",function () {
    var newVoting = $(this).closest('td').find('.voting-list-ul').clone();
    newVoting.css({"display":"block"});
    var element = $("#modal-user-voting").find('.modal-body') ;
    element.html(newVoting);
    $("#modal-user-voting").modal('show');
});




$(document).on("click",".show-content",function (event) {
    var boxId = $(this).find(".box-id").val() ;
    // console.log("click",boxId);
    var title = $(this).find(".box-title").text() ;
    var modal = $('#modal-card-content') ;
    modal.find('.modal-title span').text(title);

    openCard(title,boxId);
    // if($("#table-quatation").length){
    //  $("#section-company").show();
    // }
});

function openCard(title,boxId)
{
    window.history.pushState("object or string", title , $("#baseUrl").val()+'/purchase/quotation/'+boxId);
    clearQuotationData();
    $("#current_card_id").val(boxId);     ;
    $("#company_id").val('');

    var route = RouteUrl+"data/"+boxId+"?api_token="+api_token;
    ajaxPromise('GET',route,null).done(function (data) {
        createCard(data)
        $("#modal-card-content").modal('show');
    })
}

function createCard(data)
{
    createQuatationTable(data);
    if (data.quotation_items!=null) {
        createQuatationTableItem(data.quotation_items);
    }
    if (data.quotation_historys!=null) {
        createQuatationTableHistory(data.quotation_historys);
    }
    if (data.quotation!=null) {
        createQuatationCard(data.quotation);
    }
    if (data.status!=null) {
        createQuatationSetInit(data.status);
    }
    if (data.quotation_comments!=null) {
        createQuotationComment(data.quotation_comments);
    }
    if (data.quotation_user_can_vote!=null) {
        createQuotationUserCanVote(data.quotation_user_can_vote);
    }
    if (data.quotation_votes_instead!=null) {
        createQuotationUserInsteadVote(data.quotation_votes_instead,data.quotation_total_user_can_vote);
    }
}

$(".btn-quotation-new").on("click",function () {
    $("#modal-card-content").modal("toggle");
    $("#modal-quotation-item").modal("toggle");
});

$(".btn-quotation-company").on('click',function () {

    clearQuotationCompany();

    getDataQuatationItem().done(function (data) {
    
        addQuotationItem();
        $("#modal-quotation-company").modal('show');
    }).fail(function (txt) {
        var error = JSON.stringify(txt);
                       swal(
                           'Error...',
                           error,
                           'error'
                       )
    });
});

$(document).on("click",".btn-edit-quotation-company",function (event) {
    clearQuotationCompany();
    var boxId = $("#current_card_id").val() ;
    var companyId = $(this).closest('th').find(".company-id").val() ;
    $("#company_id").val(companyId);
    // console.log("[ btn-edit-quotation-company ] click",companyId);
    // addQuotationItem();
    var route = RouteUrl+boxId+"/company/"+companyId+"?api_token="+api_token ;
    ajaxPromise('GET',route,null).then(function (data) {
        setCompanyData(data);
        // createQuatationTable(data);
        // createQuatationTableItem(data.quotation_items);
        // $("#modal-card-content").modal('show');
    })
});

$(document).on("click",".btn-set-company-winner",function (event) {
    var boxId = $("#current_card_id").val() ;
    var companyId = $(this).closest('th').find(".company-id").val() ;
    var route = RouteUrl+boxId+"/winner/"+companyId+"?api_token="+api_token;
    ajaxPromise('GET',route,null).then(function (data) {
        createCard(data);
    });
});


function setCompanyData(data)
{
    $("#table-quatation-company .item-price-total").text('0');
    $("#table-quatation-company .total-price-before-vat").text('0');
    $("#table-quatation-company .price-discount").text('0');
    $("#table-quatation-company .price-vat").text('0');
    $("#table-quatation-company .total-price-net").text('0');
    $("#table-quatation-company .discount").text('0');
    $("#table-quatation-company #cal_vat").attr('checked',true);
    $("#table-quatation-company .payment_term").val('');
    $("#table-quatation-company .guarantee").val('');
    $("#table-quatation-company .company-remark").val('');

    var company = data.company;
    var item = data.quotation_items;
    var companyItem = data.quotation_company_items;
    var quotationCompany = data.quotation_companys;
    var attachment = data.quotation_company_attach;
    // console.log(attachment);
    // console.log(company);
    // console.log(item);
    // console.log(companyItem);
    // console.log(quotationCompany);

    $("#supplier_name").val(company.name);
    $("#supplier_address").val(company.address);
    $("#contact_name").val(company.contact_name);
    $("#contact_tel").val(company.contact_tel);
    $("#contact_email").val(company.contact_email);

    if (quotationCompany !== null) {
        $(".total-price-before-vat").text(quotationCompany.price_b4_vat);
        if (quotationCompany.discount>0) {
            $(".price-discount").text(-quotationCompany.discount);
        }
        $("#cal_vat").attr({checked: true});
        if (quotationCompany.vat==0) {
            $("#cal_vat").attr({checked: false});
        }

        $(".price-vat").text(quotationCompany.vat);
        $(".total-price-net").text(quotationCompany.price_net);
        $(".company-remark").text(quotationCompany.remark);
        $(".discount").val(quotationCompany.discount);
        $(".payment_term").val(quotationCompany.payment_term);
        $(".guarantee").val(quotationCompany.guarantee);
    }

    
    var table = '';
    for (var i =0; i< item.length; i++) {
        // console.log(item[i]);
        table += "<tr>"+
                "<td>"+(i+1)+"<input type=\"hidden\" value=\""+(item[i].id)+"\" class=\"quotation-item-id\"></td>"+
                "<td><span >"+(item[i].name)+"</span></td>"+
                "<td><span >"+(item[i].amount)+"</span></td>";
        // console.log(typeof company);
        hasCompanyItem = false;
        for (var k =0; k < companyItem.length; k++) {
            // console.log(companyItem[k].quotation_item_id,item[i].id);
            if (companyItem[k].quotation_item_id==item[i].id) {
                table += "<td><input type=\"text\" class=\"item-price-per-unit\" value=\""+companyItem[k].price_per_unit+"\"></td>"+
                "<td><span class=\"item-price-total\">"+companyItem[k].price+"</span></td>" ;
                hasCompanyItem = true;
            }
        }

        if (!hasCompanyItem) {
            table += "<td><input type=\"text\" class=\"item-price-per-unit\" value=\"\"></td>"+
            "<td><span class=\"item-price-total\"></span></td>" ;
        }

                                            
        table += "</tr>";
    }

    if (attachment!=null) {
        var htmlAttach = '';
        if (attachment.length>0) {
            for (var a=0; a<attachment.length; a++) {
                htmlAttach += "<tr>"+
                              "<td><button type=\"button\" "+
                              " class=\"btn btn-danger del-attachment\" data-file-code=\""+attachment[a].file_code+"\" > "+
                              " <i class=\"fa fa-close\"></i>"+
                              "</button></td>"+
                              "<td><a href=\" "+attachment[a].img+" \" "+
                              " target=\"_blank\" "+
                              " download=\""+attachment[a].file_name+"\" >"+
                              attachment[a].file_name+"</a></td>"+
                              "</tr>";
            }
        }
    }

    // console.log(table);

    $("#table-quatation-company tbody").html(table);
    $("#table-attachmented tbody").html(htmlAttach);
    $("#modal-card-content").modal("hide");
    $("#modal-quotation-company").modal("show");
}

function createQuatationTable(data)
{
    $(".btn-quotation-new").text((($("#app_local").val()=='th') ? 'เพิ่มรายการ' : 'Create Item' ));
    var company = data.quotation_companys;
    var item = data.quotation_items;
    var companyItem = data.quotation_company_items;
    var quotation = data.quotation ;
    var voting = data.quotation_votes ;
    var votingInstead = data.quotation_votes_instead ;
    var userCanVote = data.quotation_user_can_vote ;

    var totalUserCanVote = data.quotation_total_user_can_vote;

    var voteColor = ["#D7C6E6","#99D6EA","#7CE0D3","#C1A7E2","#5BC2E7","#2CD5C4","#9063CD","#00A9E0","#00C7B1"];

    var appLocal = $("#app_local").val();


    cardUpdateStatus(quotation)

 
    //---Title
    $("#modal-card-content .show-title").find('span').text(data.quotation.title);
   
    $("#modal-card-content .btn-show-set-company-winner").hide();
    if (quotation.vote_winner==null&&quotation.status==3) {
        $("#modal-card-content .btn-show-set-company-winner").show();
    }


    // console.log(data.quotation_items);
    // console.log(item, typeof item);

    if (item.length <= 0) {
        // console.log("not data found");
        $("#data-summary-quotation-table").html('');
        $("#table-quatation tbody").html('');
        return false;
    }

    var table = "<table id=\"table-quatation-summary\" "+
                " class=\"table table-bordered table-striped\">"+
                "<thead>"+
                "<tr>"+
                "<th rowspan=\"2\" class=\"vm-ct\" width=\"50\">"+((appLocal=='th') ? 'อันดับ' : 'No' )+ "</th>"+
                "<th rowspan=\"2\" class=\"vm-ct\">"+((appLocal=='th') ? 'รายการ' : 'Description' )+ "</th>"+
                "<th rowspan=\"2\" class=\"vm-ct\">"+((appLocal=='th') ? 'จำนวน' : 'Amount' )+ "</th>";

    if (typeof company != "undefined"&&company.length>0) {
        for (var j =0; j < company.length; j++) {
            if (company[j].company_id==data.voted_company_id) {
                $("#btn_voted").html(((appLocal=='th') ? 'คุณโหวตบริษัท' : 'You choose' )+'<BR>'+company[j].name);
            }
            if (company[j].company_id==quotation.vote_winner) {
                $("#success_vote").html(((appLocal=='th') ? 'สรุปผลโหวต <BR> เลือก ' : 'Voted Result <BR> choose' )+'<BR>'+company[j].name);
            }
            

            table += "<th colspan=\"2\" class=\"vm-ct\" >"+
                     
                     "<span class=\"company-name\"> "+
                     
                     company[j].name+"</span>"+
                     "<input type=\"hidden\" class=\"company-id\" value=\""+company[j].company_id+"\">";
                     

            table += "<button class=\"btn btn-default btn-xs btn-edit-quotation-company pull-right\" >"+
                     "<i class=\"fa fa-pencil\"></i></button>";

            if (quotation.vote_winner==null&&quotation.status==3) {
                table += "<button class=\"btn btn-success btn-xs btn-set-company-winner pull-right\" >"+
                     ((appLocal=='th') ? 'เลือก' : 'Choose' )+"</button>";
            }

            table += "</th>" ;
        }
    }
        table += "</tr>";

    if (typeof company != "undefined"&&company.length>0) {
        table += "<tr>" ;
        for (var j =0; j < company.length; j++) {
            table += "<th>"+((appLocal=='th') ? ' ราคา / หน่วย' : 'Price / Unit ' )+"</th>"+
                     "<th>"+((appLocal=='th') ? ' จำนวน / บาท' : 'Amount / Bath ' )+"</th>" ;
        }
        table += "</tr>"    ;
    }



        table += "</thead><tbody>"  ;

    // console.log('item',item);
    // console.log('company',company);
    // console.log('companyItem',companyItem);

    // console.log('item.length',item.length);
    // console.log('company.length',company.length);
    // console.log('companyItem.length',companyItem.length);

    for (var i =0; i< item.length; i++) {
        // console.log(item[i]);
        table += "<tr>"+
                "<td>"+(i+1)+"<input type=\"hidden\" value=\""+(item[i].id)+"\"></td>"+
                "<td><span >"+(item[i].name)+"</span></td>"+
                "<td class=\"text-center\"><span >"+(item[i].amount)+"</span></td>";
        // console.log(typeof company);

        if (typeof company != "undefined") {
            for (var j =0; j < company.length; j++) {
                hasCompanyItem = false;
                for (var k =0; k < companyItem.length; k++) {
                    // console.log(companyItem[k].company_id,company[j].company_id,
                        // companyItem[k].quotation_item_id,item[i].id);
                    if (companyItem[k].company_id==company[j].company_id &&
                        companyItem[k].quotation_item_id==item[i].id) {
                        table += "<td class=\"text-center\">"+( (companyItem[k].price_per_unit==0) ? '-' : ReplaceNumberWithCommas(parseFloat(companyItem[k].price_per_unit).toFixed(2))  )+"</td>"+
                        "<td class=\"text-center\"><span class=\"item-price-total\">"+( (companyItem[k].price==0) ? '-' :  ReplaceNumberWithCommas(parseFloat(companyItem[k].price).toFixed(2))  )+"</span></td>"   ;
                        hasCompanyItem = true;
                    }
                }
                if (!hasCompanyItem) {
                    table += "<td class=\"text-center\" >-</td>"+
                                "<td class=\"text-center\"><span class=\"item-price-total\">-</span></td>"  ;
                }
            }
        }
                                            
        table += "</tr>";
    }

    table += "</tbody><tfoot>";

    table += "<tr>"+
                 "<td colspan=\"3\">"+((appLocal=='th') ? 'ราคารวมก่อนภาษี' : 'Price before Vat' )+"</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td></td><td class=\"total-price-before-vat text-center\">"+ ReplaceNumberWithCommas(parseFloat(company[k].price_b4_vat).toFixed(2))+"</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">"+((appLocal=='th') ? 'ส่วนลด' : 'Discount' )+"</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td></td><td class=\"price-discount text-center text-danger\">"+((company[k].discount>0) ?  ReplaceNumberWithCommas(parseFloat(company[k].discount).toFixed(2)) : '-' )+"</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">"+((appLocal=='th') ? 'ภาษี' : 'Vat ' )+" 7% </td>";
    for (var k =0; k < company.length; k++) {
        table += "<td></td><td class=\"price-vat text-center\">"+ ReplaceNumberWithCommas(parseFloat(company[k].vat).toFixed(2))+"</td>";
    }
    table += "</tr>";

    

    table += "<tr>"+
             "<td colspan=\"3\"><b>"+((appLocal=='th') ? 'ราคาสุทธิ' : 'Net price' )+"</b></td>";
    for (var k =0; k < company.length; k++) {
        table += "<td></td><td class=\"total-price-net text-center\"><b>"+ ReplaceNumberWithCommas(parseFloat(company[k].price_net).toFixed(2))+"</b></td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">"+((appLocal=='th') ? 'การชำระเงิน' : 'Term of payment' )+"</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td colspan=\"2\">"+company[k].payment_term+"</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">"+((appLocal=='th') ? 'การรับประกัน' : 'Warranty' )+"</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td colspan=\"2\">"+company[k].guarantee+"</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">"+((appLocal=='th') ? 'ไฟล์แนบ' : 'Attachment' )+"</td>";
    var all_attachment =false;
    for (var k =0; k < company.length; k++) {
        table += "<td colspan=\"2\">";

        // attachmentData = [];
        // hasAttachment = false;
        // for (var l =0 ; l < attachment.length;l++){
        //  if (company[k].company_id==attachment[l].company_id){
        //      attachmentData.push(attachment[l]);
        //      hasAttachment = true;
        //  }
        // }
        if (company[k].has_attachment==1) {
            all_attachment =true;
            table += "<a href=\"javascript:void(0)\" class=\"link-attachment-file\" >"+
                    "See attach file</a>"+
                    "<input type=\"hidden\" class=\"file-company-id\" "+
                    " value=\""+company[k].company_id+"\">" ;
        } else {
            table += "-";
        }
        table +="</td>";
    }
            //todo  //<td colspan="2"> <input type="file"> </td>
    table += "</tr>";

    if (all_attachment) {
        table += "<tr>"+
             "<td class=\"text-center\" colspan=\""+((company.length*2)+3)+"\">"+
             "<a href=\"javascript:void(0)\" class=\"all-attachment-file\" >"+
             ((appLocal=='th') ? 'ไฟล์แนบทั้งหมด' : 'All Attachment' )+"</a></td>"+
             "</tr>";
    }

    table += "<tr>"+
             "<td colspan=\"3\">"+((appLocal=='th') ? 'หมายเหตุ' : 'Remark' )+"</td>";
    for (var k =0; k < company.length; k++) {
        table += "<td colspan=\"2\">"+company[k].remark+"</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">"+((appLocal=='th') ? 'จำนวนคนที่โหวตแล้ว' : 'Voted' )+"</td>";
    for (var k =0; k < company.length; k++) {
        var bgColor  = ( k <= voteColor[k].length) ? voteColor[k] : "#ECC7CD" ;

        table += "<td colspan=\"2\" class=\"text-center\" style=\"background:"+bgColor+"\">";
        if (company[k].vote_count > 0 ) {
            table += "<a href=\"javascript:void(0)\" class=\"link-voting-list\" >"+
                    company[k].vote_count+"</a>"+
                    "<input type=\"hidden\" class=\"file-company-id\" "+
                    " value=\""+ company[k].company_id +"\">";
            if (voting.length >0 ) {
                table += "<ul class=\"voting-list-ul\" style=\"display:none;\">";
                for (var h =0; h < voting.length; h++) {
                    if (voting[h].company_id==company[k].company_id) {
                        table += "<li class=\"voting-list-li text-left\"> <img src=\" "+voting[h].img+" \" data-toggle=\"tooltip\" title=\""+
                        voting[h].user_name+"\" class=\"img-circle assign-member-img\" height=\"25\" alt=\"User Image\"> "+voting[h].user_name+" </li>" ;
                    }
                }
                if (votingInstead.length >0 ) {
                    for (var vi =0; vi < votingInstead.length; vi++) {
                        if (votingInstead[vi].company_id==company[k].company_id) {
                            table += "<li class=\"voting-list-li text-left\"> <img src=\" "+votingInstead[vi].img+" \" data-toggle=\"tooltip\" title=\""+
                            votingInstead[vi].user_name+"\" class=\"img-circle assign-member-img\" height=\"25\" alt=\"User Image\"> "+votingInstead[vi].user_name+" </li>" ;
                        }
                    }
                }
                
                table += "</ul>";
            }
        } else {
            table += company[k].vote_count
        }

        table += "</td>";
    }
    table += "</tr>";

    table += "<tr>"+
             "<td colspan=\"3\">"+((appLocal=='th') ? 'จำนวนคนที่ไม่ออกเสียง' : 'No Vote' )+"</td>";
             
             novote = 0 ;
            
    for (var h =0; h < voting.length; h++) {
        if (voting[h].company_id==0) {
            novote++ ;
        }
    }
    for (var h =0; h < votingInstead.length; h++) {
        if (votingInstead[h].company_id==0) {
            novote++ ;
        }
    }
    table += "<td class=\"text-center\" colspan=\""+(company.length*2)+"\">"+novote+"</td>";

    table += "</tr>";

    // for(var i =0;i< item.length ; i++){
    

    // }
    table += "</tfoot><table>";


    var percent = (((voting.length+votingInstead.length)/totalUserCanVote)*100).toFixed(0);



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

    $(".btn-quotation-new").text(((appLocal=='th') ? 'แก้ไขรายการ' : 'Edit item' ));
    $("#data-summary-quotation-table").html(table);

    
    if (data.voted_company_id==0) {
        $("#btn_voted").html(((appLocal=='th') ? 'คุณเลือก<BR>ไม่ออกเสียง' : 'You Choose<BR>No vote' ));
    }

    // console.log("createQuatationTable("+voting.length+" / "+userCanVote.length+")");
    
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

function createQuatationTableItem(data)
{
    // console.log(data);
    // console.log('[createQuatationTableItem]',$("#table-quatation tbody tr").length);

    if (data.length>0) {
        $("#insert_new_item").val("false");
    }

    if ($("#table-quatation tbody tr").length==0) {
        for (var i =0; i< data.length; i++) {
            // console.log(data[i]);
            table = "<tr>"+
                    "<td><button type=\"button\" "+
                    " class=\"btn btn-danger btn-xs btn-quotation-del-item\" >"+
                    "<i class=\"fa fa-close\"></i></button>"+
                    "<input type=\"hidden\" class=\"quotation-item-id\" value=\""+(data[i].id)+"\">"+
                    "</td>"+
                    "<td>"+(i+1)+"</td>"+
                    "<td><span >"+(data[i].name)+"</span></td>"+
                    "<td><span >"+(data[i].amount)+"</span></td>"+
                    "</tr>";
            $("#table-quatation tbody").append(table)
        }
    } else {
        table = '';
        for (var i =0; i< data.length; i++) {
            table += "<tr>"+
                    "<td><button type=\"button\" "+
                    " class=\"btn btn-danger btn-xs btn-quotation-del-item\" >"+
                    "<i class=\"fa fa-close\"></i></button>"+
                    "<input type=\"hidden\" class=\"quotation-item-id\" value=\" "+(data[i].id)+" \">"+
                    "</td>"+
                    "<td>"+(i+1)+"</td>"+
                    "<td><span >"+(data[i].name)+"</span></td>"+
                    "<td><span >"+(data[i].amount)+"</span></td>"+
                    "</tr>";
        }
        $("#table-quatation tbody").html(table);
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

function createQuatationTableHistory(data)
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

function createQuatationCard(data)
{
    $("#start_vote").hide();
    $("#edit-title").hide();
    $(".btn-edit-quotation-company").hide();
    
    if (data.status==1) {
        $("#modal-card-content .menu-flow").show();
        $("#start_vote").show();
        $("#edit-title").show();
        $(".btn-edit-quotation-company").show();
    }
    
    $("#modal-card-content .btn-print").show();
    // $("#modal-card-content .btn-print").hide();
    // if(data.status>3){
    //  $("#modal-card-content .btn-print").show();
    // }
    

    if (data.vote_winner!=null) {
        $("#start_vote,#edit-title").hide();
        $(".section-quotation-button-new,.btn-edit-quotation-company").hide();
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
    countCardMenu();

}

function countCardMenu()
{
    
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
    if ($(".title-voting").length>0) {
        $(".title-voting").text("("+$("#card_voting").find('.show-content').length+")");
    }
    if ($(".title-voted").length>0) {
        $(".title-voted").text("("+$("#card_voted").find('.show-content').length+")");
    }
}




function addQuotationItem()
{
    hideInputItem();
    $("#table-quatation-company tbody").html('');
    var modalItem = $('#modal-quotation-item') ;
    var modalCompany = $('#modal-quotation-company') ;
    modalItem.find("#table-quatation tbody tr").each(function () {
        content = "<tr>"+$(this).html()+"<td><input type=\"text\" class=\"item-price-per-unit\"  ></td><td><span class=\"item-price-total\">0</span></td></tr>" ;
        // console.log('company_item : ',content);
        modalCompany.find("#table-quatation-company tbody").append(content);
    });

    modalCompany.find("#table-quatation-company tbody tr td:first-child").each(function () {
        getItemId = $(this).find('.quotation-item-id').val();
        // console.log('[addQuotationItem] getItemId',getItemId);
        $(this).next('td').append("<input type=\"hidden\" value=\""+getItemId+"\" class=\"quotation-item-id\" >");
        $(this).remove() ;
    });

    $("#modal-quotation-item").modal("hide");
    $("#modal-quotation-company").modal("show");

}


function clearQuotationData()
{

    $("#modal-user-voting .modal-body").html('');

    $(".show-edit-title").hide();
    $(".show-title").show();

    $("#card_title").val("");
    $("#instead_vote").val(0);
    $("#insert_new_item").val("true");
    $("#modal-card-content").find('.history').html('');
    $('#modal-card-content input,#modal-card-content textarea').val('');
    $('#modal-quotation-item input,#modal-quotation-item textarea').val('');
    $('#modal-quotation-company input,#modal-quotation-company textarea').val('');
    $("#supplier_name").next('ul').remove();

    $("#modal-quotation-company #append_upload").html('');

    

}

function createQuatationSetInit(data)
{
    // console.log('createQuatationSetInit',data);
    $("#modal-card-content .menu-flow").hide();
    if (data.btn_resubmit||data.in_progress||data.pending||data.done||data.cancel_vote) {
        $("#modal-card-content .menu-flow").show();
    }

    $("#modal-card-content #btn_resubmit").hide();
    if (data.btn_resubmit) {
        $("#modal-card-content #btn_resubmit").show();
    }

    $(".task-menu-delete").hide();
    if (data.btn_delete) {
        $(".task-menu-delete").show();
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
    if (data.voting) {
        $("#modal-card-content #voting").show();
        $("#modal-card-content #no_vote").show();
    }

    $("#modal-card-content #btn_voted").hide();
    if (data.voted) {
        $("#modal-card-content #btn_voted").show();
    }

    $("#modal-card-content #btn_change_voted").hide();
    if (data.change_voted) {
        $("#modal-card-content #btn_change_voted").show();
    }

    $(".section-quotation-button-new").hide();
    if (data.add_item) {
        $(".section-quotation-button-new").show();
    }

    
    
    
    $("#modal-card-content #success_vote").hide();
    $("#modal-card-content #voted").show();
    if (data.winner) {
        $("#modal-card-content #voted").hide();
        $("#modal-card-content #success_vote").show();
    }

    
    

}

function createQuotationUserCanVote(data)
{
    // console.log('createQuotationUserCanVote',data);
    $(".user-can-vote .user-can-vote-list").html('');
    if (data.length>0) {
        // var html = "<ul class=\"user-can-vote-ul\" >";
        // for (var i = 0 ; i< data.length ;i++){
        //  html += "<li class=\"user-can-vote-li text-left\"> <img src=\" "+data[i].img+" \" class=\"img-circle assign-member-img\" height=\"25\" alt=\"User Image\"> "+data[i].user_name ;
        //  if(data[i].voted){
        //      html += " <small> (voted) </small>" ;
        //  }
        //  html += " </li>" ;
        // }
        // html += "</ul>";
        var html = "";
        for (var i = 0; i< data.length; i++) {
            html += "<div class=\"user-can-vote-li text-left\" style=\"float:left;\"> <img src=\" "+data[i].img+" \" class=\"img-circle assign-member-img\" height=\"25\" alt=\"User Image\"> "+data[i].user_name ;
            if (data[i].voted=="1") {
                html += " <small> ";
                if (data[i].company_name!=null) {
                    html += (($("#app_local").val()=='th') ? 'โหวต' : 'Voted' )+
                    data[i].company_name ;
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


function createQuotationUserInsteadVote(data,countInstead)
{
    // console.log('createQuotationUserCanVote',data);
    if (countInstead>0) {
        $(".total_instead_vote").html(" ( จากทั้งหมด "+countInstead+" คน )");
    }

    

    $(".user-instead-vote .user-instead-vote-list").html('');
    if (data.length>0) {
        var html = "";
        for (var i = 0; i< data.length; i++) {
            html += "<div class=\"user-instead-vote-li text-left\" style=\"float:left;\"> <img src=\" "+data[i].img+" \" class=\"img-circle assign-member-img\" height=\"25\" alt=\"User Image\"> "+data[i].user_name ;
            html += "<span class=\"pull-right\">"+
                    "<button type=\"button\" data-id=\""+data[i].id+"\" "+
                    "class=\"btn btn-danger btn-instead-vote-delete btn-xs\" "+
                    " title=\"Remove\"><i class=\"fa fa-times\"></i></button></span>" ;
            html += " <small> ";
            if (data[i].company_name!=null) {
                html += (($("#app_local").val()=='th') ? 'โหวต' : 'Voted' )+
                data[i].company_name ;
            } else {
                html += (($("#app_local").val()=='th') ? 'ไม่ออกเสียง' : 'No voted' ) ;
            }
                html +=" (สร้าง" ;
            if (data[i].created_by_name!=null) {
                html += "โดย "+data[i].created_by_name ;
            }
                html += " "+ moment.utc(data[i].created_at).format("D/MM/YYYY HH:mm") +")";
                html +="</small>" ;
            html += " </div> " ;
        }
        
        $(".user-instead-vote .user-instead-vote-list").html(html);
    }
}
